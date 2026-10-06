<?php

namespace App\Services;

use App\Enums\AlertType;
use App\Models\Alert;
use App\Models\Conciliaction;
use App\Models\Contract;
use App\Models\Opportunity;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\NewAlert;
use Illuminate\Support\Facades\DB;

class AlertService
{
    /**
     * Gera todos os alertas (Sprint 10 / resposta.md §10).
     * O sistema NÃO move leads automaticamente — apenas notifica.
     */
    public function generate(): array
    {
        $report = [
            'oportunidade' => 0,
            'cliente' => 0,
            'contrato' => 0,
            'lead_inativo' => 0,
            'conciliacao' => 0,
        ];

        DB::transaction(function () use (&$report): void {
            $report['oportunidade'] += $this->checkOportunidadesSemActividade();
            $report['cliente'] += $this->checkClientesSemContacto();
            $report['contrato'] += $this->checkContratosPorExpirar();
            $report['lead_inativo'] += $this->checkLeadsInativos();
            $report['conciliacao'] += $this->checkConciliaçõesDiscordantes();
        });

        return $report;
    }

    /**
     * Oportunidades sem actividade há X dias (default 7, configurável em settings).
     */
    private function checkOportunidadesSemActividade(): int
    {
        $dias = (int) Setting::get('alertas.oportunidades_dias', 7);
        $limite = now()->subDays($dias)->toDateString();

        $oportunidades = Opportunity::whereIn('fase_atual', [
            'lead', 'qualificacao', 'visita', 'proposta_negociacao',
            'aprovacao', 'bilhete', 'resultado',
        ])
            ->where('ultima_actividade_em', '<=', $limite)
            ->whereNotNull('ultima_actividade_em')
            ->get();

        $count = 0;
        foreach ($oportunidades as $opp) {
            if ($this->createIfNotExists(
                AlertType::OPORTUNIDADE,
                'opportunities',
                $opp->id,
                "Oportunidade \"{$opp->titulo}\" está parada há {$dias} dias na fase {$opp->fase_atual->label()}.",
                $opp->user_id ?? User::query()->value('id'),
            )) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Clientes sem contacto há X dias (default 60, configurável em settings).
     */
    private function checkClientesSemContacto(): int
    {
        $dias = (int) Setting::get('alertas.clientes_dias', 60);
        $limite = now()->subDays($dias)->toDateString();

        $clientes = Opportunity::where('fase_atual', 'cliente_aprovado')
            ->where('ultimo_contacto_em', '<=', $limite)
            ->whereNotNull('ultimo_contacto_em')
            ->get();

        $count = 0;
        foreach ($clientes as $cliente) {
            if ($this->createIfNotExists(
                AlertType::CLIENTE,
                'opportunities',
                $cliente->id,
                "Cliente \"{$cliente->nome}\" sem contacto há {$dias} dias.",
                $cliente->user_id ?? User::query()->value('id'),
            )) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Contratos a expirar nos próximos X dias (default 90, configurável em settings).
     */
    private function checkContratosPorExpirar(): int
    {
        $dias = (int) Setting::get('alertas.contratos_dias', 90);
        $limite = now()->addDays($dias)->toDateString();

        $contratos = Contract::where('estado', 'activo')
            ->where('data_fim', '<=', $limite)
            ->whereNotNull('data_fim')
            ->get();

        $count = 0;
        foreach ($contratos as $contrato) {
            $diasRestantes = now()->diffInDays($contrato->data_fim, false);
            if ($this->createIfNotExists(
                AlertType::CONTRATO,
                'contracts',
                $contrato->id,
                "Contrato {$contrato->numero_contrato} vence em {$diasRestantes} dias ({$contrato->data_fim->format('d/m/Y')}).",
                $contrato->commercial_id ?? User::query()->value('id'),
            )) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Leads inativos (parados em Leads há X dias, default 7).
     */
    private function checkLeadsInativos(): int
    {
        $dias = (int) Setting::get('alertas.leads_inativos_dias', 7);
        $limite = now()->subDays($dias)->toDateString();

        $leads = Opportunity::where('fase_atual', 'lead')
            ->where('updated_at', '<=', $limite)
            ->get();

        $count = 0;
        foreach ($leads as $lead) {
            if ($this->createIfNotExists(
                AlertType::LEAD_INATIVO,
                'opportunities',
                $lead->id,
                "Lead \"{$lead->titulo}\" parado há {$dias} dias em Leads.",
                $lead->user_id ?? User::query()->value('id'),
            )) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Conciliações discordantes pendentes.
     */
    private function checkConciliaçõesDiscordantes(): int
    {
        $conciliações = Conciliaction::where('classificacao', 'discordante')
            ->where('resolvido', false)
            ->get();

        $count = 0;
        foreach ($conciliações as $conciliaction) {
            if ($this->createIfNotExists(
                AlertType::CONCILIACAO,
                'conciliactions',
                $conciliaction->id,
                "Conciliação de \"{$conciliaction->opportunity?->nome}\" discordante — {$conciliaction->observacoes}.",
                $conciliaction->commercial_id ?? User::query()->value('id'),
            )) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Cria alerta se não existir (evita duplicados).
     * Envia notificação por email.
     */
    private function createIfNotExists(
        AlertType $tipo,
        string $entidade,
        int $entidadeId,
        string $mensagem,
        ?int $userId = null
    ): bool {
        $exists = Alert::where('tipo', $tipo->value)
            ->where('entidade', $entidade)
            ->where('entidade_id', $entidadeId)
            ->where('resolvido', false)
            ->exists();

        if ($exists) {
            return false;
        }

        $alert = Alert::create([
            'tipo' => $tipo,
            'entidade' => $entidade,
            'entidade_id' => $entidadeId,
            'mensagem' => $mensagem,
            'user_id' => $userId ?? User::query()->value('id'),
            'prazo' => now()->addDays(7),
        ]);

        // Enviar notificação por email (queue)
        $user = User::find($alert->user_id);
        if ($user) {
            $user->notify(new NewAlert($alert));
        }

        return true;
    }
}
