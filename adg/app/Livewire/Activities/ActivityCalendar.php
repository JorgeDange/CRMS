<?php

namespace App\Livewire\Activities;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Services\ActivityService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;

class ActivityCalendar extends Component
{
    /**
     * Views do dropdown de apps-calendar.html (labels estaticos do prototipo).
     */
    public const VIEWS = [
        'day' => 'Diária',
        'week' => 'Semanal',
        'weeks2' => 'Semanas (2)',
        'weeks3' => 'Semanas (3)',
        'month' => 'Mensal',
    ];

    /**
     * Icones de cada view (data-action do apps-calendar.html).
     */
    public const VIEW_ICONS = [
        'day' => 'feather-list',
        'week' => 'feather-umbrella',
        'weeks2' => 'feather-sliders',
        'weeks3' => 'feather-framer',
        'month' => 'feather-grid',
    ];

    public const DAY_NAMES = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

    public string $view = 'month';

    public string $cursor = '';

    public bool $allVisible = true;

    /** @var array<int, string> */
    public array $types = [];

    /** Abre o modal de detalhes do evento clicado na grelha. */
    public ?int $detalheId = null;

    public function mount(): void
    {
        $this->cursor = now()->toDateString();
        $this->types = $this->allTypes();
    }

    public function abrirDetalhe(int $activityId): void
    {
        if (Activity::query()->whereKey($activityId)->exists()) {
            $this->detalheId = $activityId;
            $this->dispatch('modal-actividade', aberto: true);
        }
    }

    public function fecharDetalhe(): void
    {
        $this->detalheId = null;
        $this->dispatch('modal-actividade', aberto: false);
    }

    public function concluirDetalhe(): void
    {
        $activity = $this->detalheId ? Activity::query()->find($this->detalheId) : null;

        if ($activity) {
            app(ActivityService::class)->toggleStatus($activity);
        }
    }

    public function eliminarDetalhe(): void
    {
        $activity = $this->detalheId ? Activity::query()->find($this->detalheId) : null;

        if ($activity) {
            app(ActivityService::class)->remove($activity);
            $this->detalheId = null;
            $this->dispatch('modal-actividade', aberto: false);
        }
    }

    public function setView(string $view): void
    {
        if (array_key_exists($view, self::VIEWS)) {
            $this->view = $view;
        }
    }

    public function prev(): void
    {
        $this->cursor = $this->step(-1)->toDateString();
    }

    public function next(): void
    {
        $this->cursor = $this->step(1)->toDateString();
    }

    public function today(): void
    {
        $this->cursor = now()->toDateString();
    }

    /**
     * Checkbox "Ver todas as actividades" (lnb-calendars do prototipo):
     * marcar activa todos os tipos, desmarcar esconde tudo.
     * So corre em updates vindos do cliente, logo nao cria ciclos.
     */
    public function updatedAllVisible(mixed $value): void
    {
        $this->types = $value ? $this->allTypes() : [];
    }

    /**
     * Caixas de legenda (#calendarList) — mantem a seleccao coerente com
     * a checkbox "Ver todas".
     */
    public function updatedTypes(mixed $value): void
    {
        $this->types = array_values(array_filter(
            is_array($value) ? $value : [],
            fn ($tipo): bool => in_array($tipo, $this->allTypes(), true),
        ));

        $this->allVisible = count($this->types) === count($this->allTypes());
    }

    public function render()
    {
        $range = $this->range();
        [$start, $end] = $range;

        $days = $this->days($start, $end);

        $events = Activity::with('opportunity')
            ->whereHas('opportunity')
            ->whereIn('tipo', $this->types)
            ->whereBetween('data', [$start->toDateString(), $end->toDateString()])
            ->orderBy('data')
            ->orderBy('hora')
            ->orderBy('id')
            ->get();

        $eventsByDate = $events->groupBy(fn (Activity $activity): string => $activity->data->toDateString());

        $upcoming = Activity::with('opportunity')
            ->whereHas('opportunity')
            ->where('data', '>=', now()->toDateString())
            ->orderBy('data')
            ->orderBy('hora')
            ->orderBy('id')
            ->limit(4)
            ->get();

        return view('livewire.activities.activity-calendar', [
            'days' => $days,
            'eventsByDate' => $eventsByDate,
            'upcoming' => $upcoming,
            'tipos' => ActivityType::cases(),
            'detalhe' => $this->detalheId ? Activity::with('opportunity')->find($this->detalheId) : null,
            'dayNames' => $this->view === 'day'
                ? [self::DAY_NAMES[(int) Carbon::parse($this->cursor)->dayOfWeek]]
                : self::DAY_NAMES,
            'cols' => $this->view === 'day' ? 1 : 7,
            'rangeLabel' => $this->rangeLabel($start, $end),
            'viewLabels' => self::VIEWS,
            'viewIcons' => self::VIEW_ICONS,
            'greyOthers' => in_array($this->view, ['month', 'weeks2', 'weeks3'], true),
            'monthKey' => Carbon::parse($this->cursor)->format('Y-m'),
        ]);
    }

    /** @return array{0: Carbon, 1: Carbon} */
    protected function range(): array
    {
        $cursor = Carbon::parse($this->cursor);

        return match ($this->view) {
            'day' => [$cursor->copy()->startOfDay(), $cursor->copy()->startOfDay()],
            'week' => $this->weekRange(1),
            'weeks2' => $this->weekRange(2),
            'weeks3' => $this->weekRange(3),
            // TUI Calendar corre com isAlways6Week (6 linhas fixas no mes).
            default => [
                $cursor->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY),
                $cursor->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY)->addDays(41),
            ],
        };
    }

    /** @return array{0: Carbon, 1: Carbon} */
    protected function weekRange(int $weeks): array
    {
        $start = Carbon::parse($this->cursor)->startOfWeek(Carbon::SUNDAY);

        return [$start, $start->copy()->addDays($weeks * 7 - 1)];
    }

    /**
     * @return Collection<int, Carbon>
     */
    protected function days(Carbon $start, Carbon $end): Collection
    {
        $days = collect();
        $day = $start->copy();

        while ($day->lte($end)) {
            $days->push($day->copy());
            $day->addDay();
        }

        return $days;
    }

    protected function rangeLabel(Carbon $start, Carbon $end): string
    {
        // Formatos exactos de tui-calendar-init.min.js (funcao f()).
        if ($this->view === 'day' || $this->view === 'month') {
            return Carbon::parse($this->cursor)->format('d.m.y');
        }

        return $start->format('d/m/Y').' ~ '.$end->format('d/m/Y');
    }

    protected function step(int $direction): Carbon
    {
        $cursor = Carbon::parse($this->cursor);

        return match ($this->view) {
            'day' => $cursor->addDays($direction),
            'week' => $cursor->addWeeks($direction),
            'weeks2' => $cursor->addWeeks(2 * $direction),
            'weeks3' => $cursor->addWeeks(3 * $direction),
            default => $cursor->startOfMonth()->addMonthsNoOverflow($direction),
        };
    }

    /** @return array<int, string> */
    protected function allTypes(): array
    {
        return array_column(ActivityType::cases(), 'value');
    }
}
