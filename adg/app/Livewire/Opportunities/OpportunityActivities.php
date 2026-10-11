<?php

namespace App\Livewire\Opportunities;

use App\Models\Activity;
use App\Services\ActivityService;
use Livewire\Component;

/**
 * BLOCO 4 (mudancas.md): aba "Actividades" do detalhe da oportunidade —
 * modo copia da Agenda (ActivityList), filtrada por opportunity_id e sem
 * filtros globais. Botao "Nova Actividade" abre o form com o cliente
 * pre-seleccionado (/agenda/nova?oportunidade={id}).
 */
class OpportunityActivities extends Component
{
    public int $opportunityId;

    public function mount(int $opportunityId): void
    {
        $this->opportunityId = $opportunityId;
    }

    public function toggle(int $activityId): void
    {
        $activity = Activity::query()
            ->where('opportunity_id', $this->opportunityId)
            ->find($activityId);

        if (! $activity) {
            return;
        }

        app(ActivityService::class)->toggleStatus($activity);
    }

    public function remove(int $activityId): void
    {
        $activity = Activity::query()
            ->where('opportunity_id', $this->opportunityId)
            ->find($activityId);

        if (! $activity) {
            return;
        }

        app(ActivityService::class)->remove($activity);

        $this->dispatch('activity-removed');
    }

    public function render()
    {
        $activities = Activity::query()
            ->where('opportunity_id', $this->opportunityId)
            ->orderBy('data', 'desc')
            ->orderBy('hora')
            ->orderBy('id')
            ->get();

        return view('livewire.opportunities.opportunity-activities', [
            'activities' => $activities,
        ]);
    }
}
