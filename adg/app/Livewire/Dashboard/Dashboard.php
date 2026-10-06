<?php

namespace App\Livewire\Dashboard;

use App\Models\Contract;
use App\Models\Opportunity;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        // KPIs do Dashboard (baseados em index.html do protótipo)
        $totalOportunidades = Opportunity::count();
        $valorPipeline = Opportunity::whereIn('fase_atual', [
            'lead', 'qualificacao', 'visita', 'proposta_negociacao',
            'aprovacao', 'bilhete', 'resultado',
        ])->sum('valor_estimado');

        $ponderado = Opportunity::whereIn('fase_atual', [
            'lead', 'qualificacao', 'visita', 'proposta_negociacao',
            'aprovacao', 'bilhete', 'resultado',
        ])->get()->sum(fn ($opp) => $opp->valor_estimado * $opp->probabilidade / 100);

        $ganhoMes = Opportunity::where('fase_atual', 'cliente_aprovado')
            ->where('data_aprovacao', '>=', now()->startOfMonth())
            ->sum('valor_estimado');

        $encomendasEmCurso = Contract::where('estado', 'activo')->count();
        $encomendasAtraso = Contract::where('estado', 'activo')
            ->where('data_fim', '<', now()->toDateString())
            ->count();

        $dividaVencida = Contract::where('estado', 'activo')
            ->where('data_fim', '<', now()->toDateString())
            ->sum('valor');

        $clientesBloqueados = Contract::where('estado', 'activo')
            ->where('data_fim', '<', now()->toDateString())
            ->distinct('opportunity_id')
            ->count();

        // Vendas por mês (últimos 6 meses)
        $vendasMensais = [];
        for ($i = 5; $i >= 0; $i--) {
            $mes = now()->subMonths($i);
            $valor = Opportunity::where('fase_atual', 'cliente_aprovado')
                ->whereYear('data_aprovacao', $mes->year)
                ->whereMonth('data_aprovacao', $mes->month)
                ->sum('valor_estimado');
            $vendasMensais[] = [
                'mes' => $mes->format('M/Y'),
                'valor' => $valor,
            ];
        }

        // Pipeline por estado
        $pipelinePorEstado = Opportunity::whereIn('fase_atual', [
            'lead', 'qualificacao', 'visita', 'proposta_negociacao',
            'aprovacao', 'bilhete', 'resultado',
        ])->selectRaw('fase_atual, count(*) as total')
            ->groupBy('fase_atual')
            ->pluck('total', 'fase_atual')
            ->toArray();

        // Encomendas por estado
        $encomendasPorEstado = Contract::selectRaw('estado, count(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->toArray();

        // Performance por gestor
        $gestores = Opportunity::where('fase_atual', 'cliente_aprovado')
            ->selectRaw('user_id, count(*) as total, sum(valor_estimado) as valor, sum(case when fase_atual = ? then 1 else 0 end) as ganhas', ['cliente_aprovado'])
            ->with('user:id,name')
            ->groupBy('user_id')
            ->get()
            ->map(function ($item) {
                return [
                    'nome' => $item->user->name ?? '—',
                    'total' => $item->total,
                    'ganhas' => $item->ganhas,
                    'valor' => $item->valor,
                    'taxa' => $item->total > 0 ? round($item->ganhas / $item->total * 100) : 0,
                ];
            })
            ->toArray();

        // Top clientes por facturação
        $topClientes = Opportunity::where('fase_atual', 'cliente_aprovado')
            ->selectRaw('nome, count(*) as encomendas, sum(valor_estimado) as facturacao, sector')
            ->groupBy('nome', 'sector')
            ->orderByDesc('facturacao')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'nome' => $item->nome,
                    'encomendas' => $item->encomendas,
                    'facturacao' => $item->facturacao,
                    'estado' => 'Activo',
                    'estado_badge' => 'bg-soft-success text-success',
                    'opportunity_id' => Opportunity::where('nome', $item->nome)->value('id'),
                ];
            })
            ->toArray();

        return view('livewire.dashboard.dashboard', [
            'totalOportunidades' => $totalOportunidades,
            'valorPipeline' => $valorPipeline,
            'ponderado' => $ponderado,
            'ganhoMes' => $ganhoMes,
            'encomendasEmCurso' => $encomendasEmCurso,
            'encomendasAtraso' => $encomendasAtraso,
            'dividaVencida' => $dividaVencida,
            'clientesBloqueados' => $clientesBloqueados,
            'vendasMensais' => $vendasMensais,
            'pipelinePorEstado' => $pipelinePorEstado,
            'encomendasPorEstado' => $encomendasPorEstado,
            'gestores' => $gestores,
            'topClientes' => $topClientes,
        ]);
    }
}
