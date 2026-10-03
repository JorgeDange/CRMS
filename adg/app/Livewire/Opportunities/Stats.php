<?php

namespace App\Livewire\Opportunities;

use App\Enums\OpportunityStage;
use App\Models\Opportunity;
use App\Services\OpportunityService;
use Livewire\Attributes\On;
use Livewire\Component;

class Stats extends Component
{
    /**
     * Cartoes de estatisticas do collapseOne (leads.html).
     * Recalcula quando o pipeline move uma oportunidade.
     */
    #[On('opportunity-moved')]
    public function render()
    {
        $open = fn () => Opportunity::query()->whereIn('fase_atual', [
            OpportunityStage::LEAD->value,
            OpportunityStage::VISITA->value,
            OpportunityStage::APROVACAO->value,
        ]);

        $pipelineValor = (float) $open()->sum('valor_estimado');
        $pipelineCount = $open()->count();

        $ganhoMes = Opportunity::query()
            ->stage(OpportunityStage::APROVADO)
            ->where('data_aprovacao', '>=', now()->startOfMonth());
        $ganhoValor = (float) $ganhoMes->sum('valor_estimado');
        $ganhoCount = (clone $ganhoMes)->count();

        $ponderado = (float) $open()->get()->sum(
            fn (Opportunity $opportunity): float => (float) $opportunity->valor_estimado * $opportunity->probabilidade / 100
        );

        $finalizadas = Opportunity::query()
            ->whereIn('fase_atual', [OpportunityStage::APROVADO->value, OpportunityStage::NAO_APROVADO->value])
            ->where('updated_at', '>=', now()->subDays(30));
        $finalizadasCount = $finalizadas->count();
        $aprovadasCount = (clone $finalizadas)->stage(OpportunityStage::APROVADO)->count();
        $conversao = $finalizadasCount > 0 ? (int) round($aprovadasCount / $finalizadasCount * 100) : 0;

        return view('livewire.opportunities.stats', [
            'pipelineValor' => OpportunityService::formatKz($pipelineValor),
            'pipelineCount' => $pipelineCount,
            'ganhoValor' => OpportunityService::formatKz($ganhoValor),
            'ganhoCount' => $ganhoCount,
            'ponderado' => OpportunityService::formatKz($ponderado),
            'conversao' => $conversao,
        ]);
    }
}
