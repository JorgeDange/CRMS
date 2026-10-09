<?php

namespace Database\Seeders;

use App\Enums\ActivityStatus;
use App\Enums\OpportunityStage;
use App\Models\Activity;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Dados demo HISTORICOS para os gráficos do Dashboard (adiativo, idempotente).
 *
 * Sem isto os mini-cards apareciam vazios: activities só existem desta semana,
 * aprovações só em Out/2026 e contratos nem sempre estão semeados.
 * Linhas marcadas com prefixo "[demo-hist]" são removidas antes de recriar.
 */
class DashboardDemoSeeder extends Seeder
{
    private const MARCA = '[demo-hist]';

    public function run(): void
    {
        $this->actividadesSeisMeses();
        $this->aprovacoesDozeMeses();
    }

    /**
     * Actividades distribuídas pelos últimos 6 meses — alimenta os gráficos
     * "Actividades Concluídas" (updated_at das concluídas) e
     * "Novas Actividades" (created_at).
     * proxima_accao_prazo fica NULL para não poluir "Pendências Hoje".
     */
    private function actividadesSeisMeses(): void
    {
        Activity::query()->where('descricao', 'like', self::MARCA.'%')->delete();

        $userId = User::query()->value('id');
        // Exclui as opps demo-hist: soft-deletadas mais à frente, ligar
        // activities a elas deixaria registos orfaos (agenda 500).
        $opportunityIds = Opportunity::query()
            ->where('titulo', 'not like', self::MARCA.'%')
            ->pluck('id');
        $userId ??= User::factory()->create()->id;

        if ($opportunityIds->isEmpty()) {
            $opportunityIds = collect([Opportunity::factory()->create()->id]);
        }

        // [meses atrás => nº de actividades, rácio de concluídas]
        $plano = [
            5 => [3, 1],
            4 => [4, 2],
            3 => [3, 2],
            2 => [5, 3],
            1 => [4, 3],
            0 => [6, 4],
        ];

        foreach ($plano as $mesesAtras => [$quantidade, $concluidas]) {
            for ($n = 0; $n < $quantidade; $n++) {
                $criada = now()->subMonths($mesesAtras)->subDays(rand(0, 25));
                $concluida = $n < $concluidas;

                Activity::query()->create([
                    'opportunity_id' => $opportunityIds->random(),
                    'user_id' => $userId,
                    'tipo' => 'chamada',
                    'descricao' => self::MARCA." Actividade demo {$criada->format('m/Y')} #".($n + 1),
                    'resultado' => $concluida ? 'Concluído conforme planeado.' : null,
                    'data' => $criada->toDateString(),
                    'hora' => sprintf('%02d:%02d', rand(8, 17), rand(0, 59)),
                    'proxima_accao' => null,
                    'proxima_accao_prazo' => null,
                    'status' => $concluida ? ActivityStatus::CONCLUIDA : ActivityStatus::PENDENTE,
                    'created_at' => $criada,
                    'updated_at' => $concluida ? $criada->copy()->addDays(rand(1, 5)) : $criada,
                ]);
            }
        }
    }

    /**
     * Oportunidades aprovadas com data_aprovacao distribuída pelos últimos
     * 12 meses — alimenta o gráfico "Facturação Mensal".
     */
    private function aprovacoesDozeMeses(): void
    {
        // Soft delete nao dispara o cascade das FKs — apaga primeiro as
        // activities que apontem a estas opps para nao deixar orfas.
        Opportunity::query()->where('titulo', 'like', self::MARCA.'%')->pluck('id')
            ->each(fn ($id) => Activity::query()->where('opportunity_id', $id)->delete());
        Opportunity::query()->where('titulo', 'like', self::MARCA.'%')->delete();

        for ($i = 11; $i >= 0; $i--) {
            $mes = now()->copy()->subMonths($i);

            Opportunity::query()->create([
                'titulo' => self::MARCA." Facturação {$mes->format('m/Y')}",
                'nome' => 'Cliente Demo '.$mes->format('m/Y'),
                'empresa' => 'Empresa Demo '.$mes->format('m/Y'),
                'telefone' => '+244 9'.rand(1000000, 9999999),
                'email' => 'demo'.$mes->format('ym').'@example.com',
                'nif' => '54'.random_int(1000000000, 9999999999),
                // Sectores variados → donut "Segmento" com várias fatias
                'sector' => ['Distribuição', 'Indústria', 'Construção', 'Retalho', 'Agricultura', 'Serviços'][$i % 6],
                'localizacao' => 'Luanda',
                'origem' => 'Website',
                'interesse' => 'Fornecimento anual (demo)',
                'valor_estimado' => random_int(6, 45) * 1_000_000,
                'probabilidade' => 100,
                'data_fecho' => $mes->copy()->addDays(30)->toDateString(),
                'fase_atual' => OpportunityStage::CLIENTE_APROVADO,
                'data_aprovacao' => $mes->toDateString(),
                'ultima_actividade_em' => $mes->toDateString(),
                'ultimo_contacto_em' => $mes->toDateString(),
            ]);
        }
    }
}
