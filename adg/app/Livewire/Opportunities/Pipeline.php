<?php

namespace App\Livewire\Opportunities;

use App\Enums\OpportunityStage;
use App\Models\Opportunity;
use App\Services\OpportunityService;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class Pipeline extends Component
{
    public string $search = '';

    public string $stageFilter = '';

    /**
     * Colunas do kanban — leads.html do prototipo com as fases renomeadas
     * (PROMPT_MASTER Sprint 2: Leads, Visitas, Aprovação, Aprovado, Não Aprovado).
     */
    public const COLUMNS = [
        OpportunityStage::LEAD->value => [
            'label' => 'Leads',
            'dot' => 'secondary',
            'header' => 'bg-soft-secondary',
            'badge' => 'badge bg-secondary text-white',
            'heading' => '',
            'body' => 'bg-light',
            'card' => '',
            'value' => 'text-primary',
            'prob' => 'bg-soft-info text-info',
        ],
        OpportunityStage::VISITA->value => [
            'label' => 'Visitas',
            'dot' => 'primary',
            'header' => 'bg-soft-primary',
            'badge' => 'badge bg-primary text-white',
            'heading' => '',
            'body' => 'bg-light',
            'card' => '',
            'value' => 'text-primary',
            'prob' => 'bg-soft-warning text-warning',
        ],
        OpportunityStage::APROVACAO->value => [
            'label' => 'Aprovação',
            'dot' => 'warning',
            'header' => 'bg-soft-warning',
            'badge' => 'badge bg-warning text-white',
            'heading' => '',
            'body' => 'bg-light',
            'card' => '',
            'value' => 'text-primary',
            'prob' => 'bg-soft-danger text-danger',
        ],
        OpportunityStage::APROVADO->value => [
            'label' => 'Aprovado',
            'dot' => 'success',
            'header' => 'bg-soft-success',
            'badge' => 'badge bg-success text-white',
            'heading' => 'text-success',
            'body' => 'bg-success bg-opacity-10',
            'card' => 'border-success',
            'value' => 'text-success',
            'prob' => 'bg-soft-success text-success',
        ],
        OpportunityStage::NAO_APROVADO->value => [
            'label' => 'Não Aprovado',
            'dot' => 'danger',
            'header' => 'bg-soft-danger',
            'badge' => 'badge bg-danger text-white',
            'heading' => 'text-danger',
            'body' => 'bg-danger bg-opacity-10',
            'card' => 'border-danger',
            'value' => 'text-danger',
            'prob' => 'bg-soft-danger text-danger',
        ],
    ];

    public function clearFilters(): void
    {
        $this->search = '';
        $this->stageFilter = '';
    }

    /**
     * Recebe o drop de um cartao (Alpine) e altera a fase via Service.
     */
    public function move(int $opportunityId, string $stage): void
    {
        $opportunity = Opportunity::find($opportunityId);
        $to = OpportunityStage::tryFrom($stage);

        if (! $opportunity || ! $to) {
            return;
        }

        app(OpportunityService::class)->changeStage($opportunity, $to);

        $this->dispatch('opportunity-moved');
    }

    public function render()
    {
        $opps = Opportunity::query()
            ->when($this->search !== '', function ($query): void {
                $like = '%'.$this->search.'%';
                $query->where(fn ($q) => $q
                    ->where('titulo', 'like', $like)
                    ->orWhere('nome', 'like', $like)
                    ->orWhere('empresa', 'like', $like));
            })
            ->orderBy('data_fecho')
            ->orderBy('id')
            ->get();

        return view('livewire.opportunities.pipeline', [
            'columns' => self::COLUMNS,
            'grouped' => $opps->groupBy(fn (Opportunity $opportunity): string => $opportunity->fase_atual->value),
            'totals' => $opps
                ->groupBy(fn (Opportunity $opportunity): string => $opportunity->fase_atual->value)
                ->map(fn (Collection $stageOpps) => $stageOpps->sum('valor_estimado')),
        ]);
    }
}
