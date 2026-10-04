<?php

namespace App\Livewire\Activities;

use App\Enums\ActivityStatus;
use App\Enums\ActivityType;
use App\Models\Activity;
use App\Services\ActivityService;
use Illuminate\Support\Carbon;
use Livewire\Component;

class ActivityList extends Component
{
    public string $search = '';

    public string $statusFilter = '';

    public string $typeFilter = '';

    public string $sort = 'desc';

    public function mount(): void
    {
        $status = request()->query('status');

        if (is_string($status) && in_array($status, array_column(ActivityStatus::cases(), 'value'), true)) {
            $this->statusFilter = $status;
        }

        $tipo = request()->query('tipo');

        if (is_string($tipo) && in_array($tipo, array_column(ActivityType::cases(), 'value'), true)) {
            $this->typeFilter = $tipo;
        }
    }

    public function filterStatus(string $status): void
    {
        $this->statusFilter = $this->statusFilter === $status ? '' : $status;
    }

    public function filterType(string $tipo): void
    {
        $this->typeFilter = $this->typeFilter === $tipo ? '' : $tipo;
    }

    public function clearFilters(): void
    {
        $this->statusFilter = '';
        $this->typeFilter = '';
        $this->search = '';
    }

    public function toggle(int $activityId): void
    {
        $activity = Activity::query()->find($activityId);

        if (! $activity) {
            return;
        }

        app(ActivityService::class)->toggleStatus($activity);
    }

    public function remove(int $activityId): void
    {
        $activity = Activity::query()->find($activityId);

        if (! $activity) {
            return;
        }

        app(ActivityService::class)->remove($activity);

        $this->dispatch('activity-removed');
    }

    public function render()
    {
        $activities = Activity::with('opportunity')
            ->when($this->search !== '', function ($query): void {
                $like = '%'.$this->search.'%';
                $query->where(fn ($q) => $q
                    ->where('descricao', 'like', $like)
                    ->orWhere('proxima_accao', 'like', $like)
                    ->orWhereHas('opportunity', fn ($q2) => $q2->where('nome', 'like', $like)));
            })
            ->when($this->statusFilter !== '', fn ($query) => $query->where('status', $this->statusFilter))
            ->when($this->typeFilter !== '', fn ($query) => $query->where('tipo', $this->typeFilter))
            ->orderBy('data', $this->sort === 'asc' ? 'asc' : 'desc')
            ->orderBy('hora')
            ->orderBy('id')
            ->get();

        $groups = $activities
            ->groupBy(fn (Activity $activity): string => $activity->data->toDateString())
            ->map(fn ($items, string $date) => [
                'label' => $this->groupLabel($date),
                'items' => $items,
            ])
            ->values();

        return view('livewire.activities.activity-list', [
            'groups' => $groups,
            'tipos' => ActivityType::cases(),
            'estados' => ActivityStatus::cases(),
        ]);
    }

    /**
     * Rotulo dos cartoes agrupados do apps-tasks.html.
     */
    private function groupLabel(string $date): string
    {
        $carbon = Carbon::parse($date);

        if ($carbon->isToday()) {
            return 'Hoje';
        }

        if ($carbon->isYesterday()) {
            return 'Ontem';
        }

        return $carbon->format('d M, Y');
    }
}
