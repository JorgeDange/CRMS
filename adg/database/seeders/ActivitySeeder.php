<?php

namespace Database\Seeders;

use App\Enums\ActivityStatus;
use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivitySeeder extends Seeder
{
    /**
     * Dados de demonstracao da Agenda (Sprint 5). Descricoes vindas dos
     * cards de apps-tasks.html; datas distribuidas de forma a aparecerem
     * os grupos "Hoje"/"Ontem" na lista e varios dias no calendario.
     * Os offsets sao assinados e relativos a hoje ('hoje'/'ontem' ou '+3'/'-2').
     */
    public function run(): void
    {
        $userId = User::query()->value('id');

        $rows = [
            // [titulo da oportunidade, tipo, descricao, data, hora, status, proxima_accao, prazo]
            ['Renovação contrato 2026', 'chamada', 'Videoconferência com Importadora Kinaxixi', 'hoje', '09:00', 'pendente', 'Enviar ata da reunião', '-1'],
            ['Expansão armazém', 'reuniao', 'Reunião de objectivos com o cliente', 'hoje', '11:30', 'pendente', 'Apresentar plano de expansão', '+2'],
            ['Painéis solares fase 2', 'reuniao', 'Análise de tendências do mercado-alvo', 'hoje', '15:00', 'concluida', 'Encaminhar relatório ao gestor', '+3'],
            ['Cabos eléctricos lote 2', 'chamada', 'Enviar proposta revista ao cliente', 'ontem', '10:15', 'concluida', 'Confirmar recepção da proposta', '+4'],
            ['Fornecimento trimestral ração', 'chamada', 'Marcar primeira chamada de demonstração', 'ontem', '16:45', 'cancelada', 'Reagendar demonstração', '+5'],
            ['Mobiliário escritórios', 'visita', 'Visita técnica ao armazém de escritórios', '-2', '08:30', 'pendente', 'Levantamento de medidas', '+6'],
            ['Cimento anual', 'proposta', 'Proposta comercial revista (cimento anual)', '-1', '14:00', 'pendente', 'Revisão final do orçamento', '+7'],
            ['Perfis aço projeto habitacional', 'cobranca', 'Cobrança da 2.ª prestação', '+1', '09:45', 'pendente', 'Confirmar liquidação', '-3'],
            ['Óleo alimentar Q4', 'visita', 'Visita de acompanhamento ao cliente', '+3', '10:00', 'pendente', 'Colheita de feedback', '+8'],
            ['Tijolos obra Benguela', 'reuniao', 'Reunião de fecho com obra Benguela', '+5', '15:30', 'pendente', 'Assinar acta final', '+9'],
        ];

        foreach ($rows as [$titulo, $tipo, $descricao, $data, $hora, $status, $proxima, $prazo]) {
            $opportunity = Opportunity::query()->firstWhere('titulo', $titulo);

            if (! $opportunity) {
                continue;
            }

            Activity::query()->updateOrCreate(
                ['descricao' => $descricao],
                [
                    'opportunity_id' => $opportunity->id,
                    'user_id' => $userId,
                    'tipo' => ActivityType::from($tipo),
                    'resultado' => $status === 'concluida' ? 'Concluído conforme planeado.' : null,
                    'data' => $this->date($data),
                    'hora' => $hora,
                    'proxima_accao' => $proxima,
                    'proxima_accao_prazo' => $this->date($prazo),
                    'status' => ActivityStatus::from($status),
                ],
            );
        }
    }

    private function date(string $offset): string
    {
        return match ($offset) {
            'hoje' => now()->toDateString(),
            'ontem' => now()->subDay()->toDateString(),
            default => now()->addDays((int) $offset)->toDateString(),
        };
    }
}
