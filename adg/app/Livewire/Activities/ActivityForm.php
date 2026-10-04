<?php

namespace App\Livewire\Activities;

use App\Enums\ActivityStatus;
use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Opportunity;
use App\Services\ActivityService;
use Livewire\Component;

class ActivityForm extends Component
{
    public ?int $activityId = null;

    public string $tipo = 'visita';

    public ?int $opportunity_id = null;

    public string $descricao = '';

    public string $resultado = '';

    public string $proxima_accao = '';

    public string $proxima_accao_prazo = '';

    public string $status = 'pendente';

    public string $data = '';

    public function mount(): void
    {
        $this->data = now()->format('Y-m-d');
        $this->proxima_accao_prazo = now()->addWeekday()->format('Y-m-d');

        $uuid = request()->query('atividade');

        if (! is_string($uuid) || $uuid === '') {
            return;
        }

        $activity = Activity::query()->where('uuid', $uuid)->first();

        if (! $activity) {
            return;
        }

        $this->activityId = $activity->id;
        $this->tipo = $activity->tipo->value;
        $this->opportunity_id = $activity->opportunity_id;
        $this->descricao = (string) $activity->descricao;
        $this->resultado = (string) $activity->resultado;
        $this->proxima_accao = (string) $activity->proxima_accao;
        $this->proxima_accao_prazo = $activity->proxima_accao_prazo?->format('Y-m-d') ?? '';
        $this->status = $activity->status->value;
        $this->data = $activity->data->format('Y-m-d');
    }

    /**
     * Regras do formulario de apps-notes.html (campo *, obrigatorios
     * descritos no cartao "Regras" da propria pagina do prototipo).
     */
    protected function rules(): array
    {
        return [
            'tipo' => ['required', 'in:'.implode(',', array_column(ActivityType::cases(), 'value'))],
            'opportunity_id' => ['required', 'integer', 'exists:opportunities,id'],
            'descricao' => ['required', 'string', 'max:4000'],
            'resultado' => ['nullable', 'string', 'max:8000'],
            'proxima_accao' => ['required', 'string', 'max:4000'],
            'proxima_accao_prazo' => ['required', 'date'],
            'status' => ['required', 'in:'.implode(',', array_column(ActivityStatus::cases(), 'value'))],
            'data' => ['required', 'date'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate();

        $data['resultado'] = $data['resultado'] !== '' ? $data['resultado'] : null;

        if ($this->activityId !== null) {
            app(ActivityService::class)->update(
                Activity::query()->findOrFail($this->activityId),
                $data,
            );
        } else {
            app(ActivityService::class)->create($data);
        }

        $this->redirect(route('agenda'), navigate: true);
    }

    public function render()
    {
        return view('livewire.activities.activity-form', [
            'tipos' => ActivityType::cases(),
            'estados' => ActivityStatus::cases(),
            'oportunidades' => Opportunity::query()
                ->orderBy('nome')
                ->get(['id', 'nome']),
            'editing' => $this->activityId !== null,
        ]);
    }
}
