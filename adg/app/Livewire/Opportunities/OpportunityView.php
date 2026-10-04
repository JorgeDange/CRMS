<?php

namespace App\Livewire\Opportunities;

use App\Enums\OpportunityStage;
use App\Models\Opportunity;
use App\Services\OpportunityService;
use Livewire\Attributes\On;
use Livewire\Component;

class OpportunityView extends Component
{
    public int $opportunityId;

    public string $activeTab = 'profile';

    public function mount(Opportunity $opportunity): void
    {
        $this->opportunityId = $opportunity->id;
    }

    /**
     * Marcar como Aprovado / Não Aprovado (dropdown do header).
     * Redireciona para recarregar o badge do page-header.
     */
    public function moveTo(string $stage): void
    {
        $opportunity = Opportunity::find($this->opportunityId);
        $to = OpportunityStage::tryFrom($stage);

        if (! $opportunity || ! $to || $opportunity->fase_atual === $to) {
            return;
        }

        app(OpportunityService::class)->changeStage($opportunity, $to);

        $this->redirect(route('opportunities.show', $opportunity), navigate: true);
    }

    #[On('visit-report-added')]
    public function refreshReports(): void
    {
        // Apenas para forçar re-render (lista de relatorios).
    }

    public function render()
    {
        $opportunity = Opportunity::findOrFail($this->opportunityId);

        return view('livewire.opportunities.view', [
            'opportunity' => $opportunity,
            'reports' => $opportunity->visitReports()->with('user')->latest('data_visita')->latest()->get(),
        ]);
    }
}
