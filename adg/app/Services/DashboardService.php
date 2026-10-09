<?php

namespace App\Services;

use App\Enums\ContractStatus;
use App\Enums\OpportunityStage;
use App\Models\Activity;
use App\Models\Contract;
use App\Models\Opportunity;

/**
 * Dados para Dashboard (index.html) e Painel Executivo (analytics.html)
 * baseados exactamente nos prototipos.
 */
class DashboardService
{
    /**
     * Cores do prototipo (analytics.html).
     */
    private const PALETTE = ['#a2acc7', '#5485e4', '#3dc7be', '#e49e3d', '#10b981', '#f43f5e', '#3454d1', '#64748b'];

    private const SALES_COLOR = '#3454d1';

    /**
     * Fases em curso = pipeline aberto (9 fases, 2 finais).
     */
    public static function openStages(): array
    {
        return [
            OpportunityStage::LEAD->value,
            OpportunityStage::QUALIFICACAO->value,
            OpportunityStage::VISITA->value,
            OpportunityStage::PROPOSTA_NEGOCIACAO->value,
            OpportunityStage::APROVACAO->value,
            OpportunityStage::BILHETE->value,
            OpportunityStage::RESULTADO->value,
        ];
    }

    /**
     * Dados para o Dashboard principal (index.html).
     *
     * @return array{
     *     kpis: array{
     *         clientesActivos: array{total: int, activos: int, percent: int},
     *         oportunidadesAbertas: array{total: int, abertas: int, valor: float, percent: int},
     *         encomendasCurso: array{entregues: int, total: int, percent: int},
     *         pendenciasHoje: array{vencidas: int, hoje: int, percent: int}
     *     },
     *     faturacaoMensal: array{labels: list<string>, series: list<array{name: string, data: list<float>}>},
     *     statsCards: list<array{titulo: string, valor: string, progress: int, cor: string}>,
     *     taskCompleted: array{labels: list<string>, series: list<array{name: string, data: list<float>}>},
     *     newTasks: array{labels: list<string>, series: list<array{name: string, data: list<float>}>},
     *     projectDone: array{labels: list<string>, series: list<array{name: string, data: list<float>}>},
     *     minis: array{taskCompleted: string, newTasks: string, projectDone: string},
     * }
     */
    public function dashboardData(): array
    {
        $open = self::openStages();
        $now = now();
        $year = $now->year;

        // KPIs baseados no prototype index.html
        $totalClientes = Opportunity::where('fase_atual', OpportunityStage::CLIENTE_APROVADO)->count();
        $clientesActivos = Opportunity::where('fase_atual', OpportunityStage::CLIENTE_APROVADO)->count(); // simplified
        $totalOportunidades = Opportunity::count();
        $oportunidadesAbertas = Opportunity::whereIn('fase_atual', $open)->count();
        $valorPipeline = Opportunity::whereIn('fase_atual', $open)->sum('valor_estimado');
        $totalContratos = Contract::count();
        $entregues = Contract::where('estado', ContractStatus::ACTIVO)->count();

        // Activities usa coluna 'proxima_accao_prazo' (date) e status 'pendente', 'concluida', 'cancelada'
        $vencidas = Activity::where('proxima_accao_prazo', '<', $now->toDateString())
            ->where('status', '!=', 'concluida')->count();
        $hoje = Activity::whereDate('proxima_accao_prazo', $now)->count();

        $kpis = [
            'clientesActivos' => [
                'total' => $totalClientes,
                'activos' => $clientesActivos,
                'percent' => $totalClientes > 0 ? round($clientesActivos / $totalClientes * 100) : 0,
            ],
            'oportunidadesAbertas' => [
                'total' => $totalOportunidades,
                'abertas' => $oportunidadesAbertas,
                'valor' => (float) $valorPipeline,
                'percent' => $totalOportunidades > 0 ? round($oportunidadesAbertas / $totalOportunidades * 100) : 0,
            ],
            'encomendasCurso' => [
                'entregues' => $entregues,
                'total' => $totalContratos,
                'percent' => $totalContratos > 0 ? round($entregues / $totalContratos * 100) : 0,
            ],
            'pendenciasHoje' => [
                'vencidas' => $vencidas,
                'hoje' => $hoje,
                'percent' => ($vencidas + $hoje) > 0 ? round($vencidas / ($vencidas + $hoje) * 100) : 0,
            ],
        ];

        // Facturação Mensal (12 meses) - area chart
        $faturacaoMensal = [];
        $seriesData = [];
        for ($i = 11; $i >= 0; $i--) {
            $mes = $now->copy()->subMonths($i);
            $faturacaoMensal[] = $mes->format('M/Y');
            $seriesData[] = (float) Opportunity::where('fase_atual', OpportunityStage::CLIENTE_APROVADO)
                ->whereYear('data_aprovacao', $mes->year)
                ->whereMonth('data_aprovacao', $mes->month)
                ->sum('valor_estimado');
        }

        $faturacaoMensalPayload = [
            'type' => 'area',
            'labels' => $faturacaoMensal,
            'series' => [['name' => 'Facturação', 'data' => $seriesData]],
            'color' => self::SALES_COLOR,
        ];

        // Stats cards abaixo do gráfico principal
        $pendente = Contract::where('estado', ContractStatus::ACTIVO)
            ->where('data_fim', '>=', $now->toDateString())->sum('valor');
        $recebido = Contract::where('estado', ContractStatus::ACTIVO)
            ->where('data_fim', '<', $now->toDateString())->sum('valor');
        $cancelado = Contract::where('estado', ContractStatus::CANCELADO)->sum('valor');
        $totalFaturacao = $pendente + $recebido + $cancelado;

        $statsCards = [
            ['titulo' => 'Pendente', 'valor' => self::formatKz($pendente), 'progress' => $totalFaturacao > 0 ? round($pendente / $totalFaturacao * 100) : 0, 'cor' => 'bg-primary'],
            ['titulo' => 'Recebido', 'valor' => self::formatKz($recebido), 'progress' => $totalFaturacao > 0 ? round($recebido / $totalFaturacao * 100) : 0, 'cor' => 'bg-success'],
            ['titulo' => 'Cancelado', 'valor' => self::formatKz($cancelado), 'progress' => $totalFaturacao > 0 ? round($cancelado / $totalFaturacao * 100) : 0, 'cor' => 'bg-danger'],
            ['titulo' => 'Total', 'valor' => self::formatKz($totalFaturacao), 'progress' => 100, 'cor' => 'bg-dark'],
        ];

        // Task Completed area chart (6 meses) - activities com status 'concluida'
        $taskLabels = [];
        $taskCompletedData = [];
        for ($i = 5; $i >= 0; $i--) {
            $mes = $now->copy()->subMonths($i);
            $taskLabels[] = $mes->format('M/Y');
            $taskCompletedData[] = Activity::where('status', 'concluida')
                ->whereYear('updated_at', $mes->year)
                ->whereMonth('updated_at', $mes->month)->count();
        }

        // New Tasks area chart
        $newTasksData = [];
        for ($i = 5; $i >= 0; $i--) {
            $mes = $now->copy()->subMonths($i);
            $newTasksData[] = Activity::whereYear('created_at', $mes->year)
                ->whereMonth('created_at', $mes->month)->count();
        }

        // Project Done area chart (encomendas entregues: data_fim no mês, não canceladas)
        $projectLabels = [];
        $projectDoneData = [];
        for ($i = 5; $i >= 0; $i--) {
            $mes = $now->copy()->subMonths($i);
            $projectLabels[] = $mes->format('M/Y');
            $projectDoneData[] = Contract::where('estado', '!=', ContractStatus::CANCELADO->value)
                ->whereYear('data_fim', $mes->year)
                ->whereMonth('data_fim', $mes->month)->count();
        }

        // Contadores X/Y dos 3 mini-cards (protótipo: "22/35 concluídas" etc.)
        $totalActividades = Activity::count();
        $concluidas = Activity::where('status', 'concluida')->count();
        $novas30 = Activity::where('created_at', '>=', $now->copy()->subDays(30))->count();
        $entreguesTotal = Contract::where('estado', '!=', ContractStatus::CANCELADO->value)
            ->where('data_fim', '<', $now->toDateString())->count();

        $minis = [
            'taskCompleted' => $concluidas.'/'.$totalActividades,
            'newTasks' => $novas30.'/'.$totalActividades,
            'projectDone' => $entreguesTotal.'/'.$totalContratos,
        ];

        return [
            'kpis' => $kpis,
            'faturacaoMensal' => $faturacaoMensalPayload,
            'statsCards' => $statsCards,
            'minis' => $minis,
            'taskCompleted' => [
                'labels' => $taskLabels,
                'series' => [['name' => 'Concluídas', 'data' => $taskCompletedData]],
            ],
            'newTasks' => [
                'labels' => $taskLabels,
                'series' => [['name' => 'Novas', 'data' => $newTasksData]],
            ],
            'projectDone' => [
                'labels' => $projectLabels,
                'series' => [['name' => 'Concluídos', 'data' => $projectDoneData]],
            ],
        ];
    }

    /**
     * Dados para o Painel Executivo (analytics.html).
     *
     * @return array{
     *     kpis: array{vendasYTD: float, pipelinePonderado: float, encomendasCurso: int, dividaVencida: float},
     *     charts: array{sales: array, orders: array, pipeline: array, segment: array}
     * }
     */
    public function analyticsData(): array
    {
        $open = self::openStages();

        $vendasYTD = (float) Opportunity::where('fase_atual', OpportunityStage::CLIENTE_APROVADO)
            ->whereYear('data_aprovacao', now()->year)
            ->sum('valor_estimado');

        $pipelinePonderado = (float) Opportunity::whereIn('fase_atual', $open)->get()
            ->sum(fn (Opportunity $opp) => $opp->valor_estimado * $opp->probabilidade / 100);

        $encomendasCurso = Contract::where('estado', ContractStatus::ACTIVO)->count();

        $dividaVencida = (float) Contract::where('estado', ContractStatus::ACTIVO)
            ->where('data_fim', '<', now()->toDateString())
            ->sum('valor');

        // Vendas mensais (12 meses) - area chart
        $vendasMensais = [];
        for ($i = 11; $i >= 0; $i--) {
            $mes = now()->subMonths($i);
            $vendasMensais[] = [
                'mes' => $mes->format('M/Y'),
                'valor' => (float) Opportunity::where('fase_atual', OpportunityStage::CLIENTE_APROVADO)
                    ->whereYear('data_aprovacao', $mes->year)
                    ->whereMonth('data_aprovacao', $mes->month)
                    ->sum('valor_estimado'),
            ];
        }

        $pipelinePorEstado = Opportunity::whereIn('fase_atual', $open)
            ->selectRaw('fase_atual, count(*) as total')
            ->groupBy('fase_atual')
            ->pluck('total', 'fase_atual')
            ->toArray();

        $encomendasPorEstado = Contract::selectRaw('estado, count(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->toArray();

        $segmentos = Opportunity::where('fase_atual', OpportunityStage::CLIENTE_APROVADO)
            ->selectRaw('sector, count(*) as total')
            ->groupBy('sector')
            ->orderBy('sector')
            ->pluck('total', 'sector')
            ->toArray();

        $stageOrder = $this->enumOrder(
            array_map(fn (OpportunityStage $s) => $s->value, OpportunityStage::pipeline()),
        );
        $statusOrder = $this->enumOrder(
            array_map(fn (ContractStatus $s) => $s->value, ContractStatus::cases()),
        );

        return [
            'kpis' => [
                'vendasYTD' => $vendasYTD,
                'pipelinePonderado' => $pipelinePonderado,
                'encomendasCurso' => $encomendasCurso,
                'dividaVencida' => $dividaVencida,
            ],
            'charts' => [
                'sales' => [
                    'type' => 'area',
                    'labels' => array_column($vendasMensais, 'mes'),
                    'series' => array_column($vendasMensais, 'valor'),
                    'color' => self::SALES_COLOR,
                ],
                'orders' => $this->donut(
                    $encomendasPorEstado,
                    fn (string $key) => ContractStatus::tryFrom($key)?->label() ?? $key,
                    fn (string $a, string $b) => ($statusOrder[$a] ?? PHP_INT_MAX) <=> ($statusOrder[$b] ?? PHP_INT_MAX),
                ),
                'pipeline' => $this->donut(
                    $pipelinePorEstado,
                    fn (string $key) => OpportunityStage::tryFrom($key)?->label() ?? $key,
                    fn (string $a, string $b) => ($stageOrder[$a] ?? PHP_INT_MAX) <=> ($stageOrder[$b] ?? PHP_INT_MAX),
                ),
                'segment' => $this->donut($segmentos),
            ],
        ];
    }

    /**
     * Formata valor em Kz (ex: 1200000 -> "1.200.000").
     */
    public static function formatKz(float|int $valor): string
    {
        return number_format($valor, 0, ',', '.');
    }

    /**
     * @param  list<string>  $values
     * @return array<string, int>
     */
    private function enumOrder(array $values): array
    {
        return array_flip($values);
    }

    /**
     * @param  array<array-key, int|float|string|null>  $counts
     * @return array<string, mixed>
     */
    private function donut(array $counts, ?callable $label = null, ?callable $sort = null): array
    {
        if ($sort !== null) {
            uksort($counts, $sort);
        }

        $labels = [];
        $values = [];
        $colors = [];
        $position = 0;

        foreach ($counts as $key => $total) {
            $text = $label !== null ? $label((string) $key) : (string) $key;
            $labels[] = $text === '' ? '—' : $text;
            $values[] = (float) $total;
            $colors[] = self::PALETTE[$position % count(self::PALETTE)];
            $position++;
        }

        return [
            'type' => 'donut',
            'labels' => $labels,
            'series' => $values,
            'colors' => $colors,
        ];
    }

    /**
     * Tabelas "Performance por Gestor" e "Top Clientes por Facturacao".
     *
     * @return array{gestores: list<array<string, mixed>>, topClientes: list<array<string, mixed>>}
     */
    public function tables(): array
    {
        $gestores = Opportunity::where('fase_atual', OpportunityStage::CLIENTE_APROVADO)
            ->selectRaw('user_id, count(*) as total, sum(valor_estimado) as valor, sum(case when fase_atual = ? then 1 else 0 end) as ganhas', [OpportunityStage::CLIENTE_APROVADO->value])
            ->with('user:id,name')
            ->groupBy('user_id')
            ->get()
            ->map(fn ($item) => [
                'nome' => $item->user->name ?? '—',
                'total' => $item->total,
                'ganhas' => $item->ganhas,
                'valor' => $item->valor,
                'taxa' => $item->total > 0 ? round($item->ganhas / $item->total * 100) : 0,
            ])
            ->toArray();

        $topClientes = Opportunity::where('fase_atual', OpportunityStage::CLIENTE_APROVADO)
            ->selectRaw('nome, count(*) as encomendas, sum(valor_estimado) as facturacao, sector')
            ->groupBy('nome', 'sector')
            ->orderByDesc('facturacao')
            ->limit(10)
            ->get()
            ->map(fn ($item) => [
                'nome' => $item->nome,
                'encomendas' => $item->encomendas,
                'facturacao' => $item->facturacao,
                'estado' => 'Activo',
                'estado_badge' => 'bg-soft-success text-success',
                'opportunity_id' => Opportunity::where('nome', $item->nome)->value('id'),
            ])
            ->toArray();

        return [
            'gestores' => $gestores,
            'topClientes' => $topClientes,
        ];
    }
}
