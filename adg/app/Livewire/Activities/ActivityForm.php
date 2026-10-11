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

    // BLOCO 3: texto personalizado quando tipo = 'outros'.
    public string $tipo_outros = '';

    public ?int $opportunity_id = null;

    public string $descricao = '';

    public string $resultado = '';

    public string $proxima_accao = '';

    public string $proxima_accao_prazo = '';

    public string $status = 'pendente';

    public string $data = '';

    /**
     * BLOCO 4: true quando o form foi aberto a partir da aba Actividades
     * da oportunidade (/agenda/nova?oportunidade={id}) — usado no redirect
     * pos-save para voltar ao detalhe da oportunidade.
     */
    public bool $fromOpportunity = false;

    public function mount(): void
    {
        $this->data = now()->format('Y-m-d');
        $this->proxima_accao_prazo = now()->addWeekday()->format('Y-m-d');

        // BLOCO 4: pré-seleccionar cliente quando vindo da oportunidade.
        $opportunityParam = request()->query('oportunidade');

        if (is_string($opportunityParam) && ctype_digit($opportunityParam)) {
            $opportunity = Opportunity::query()->find((int) $opportunityParam);

            if ($opportunity) {
                $this->opportunity_id = $opportunity->id;
                $this->fromOpportunity = true;
            }
        }

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
        $this->tipo_outros = (string) $activity->tipo_outros;
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
            // BLOCO 3: obrigatorio apenas quando o tipo e 'outros'.
            'tipo_outros' => ['nullable', 'string', 'max:100', 'required_if:tipo,outros'],
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

        // BLOCO 3: guarda o texto apenas quando o tipo e 'outros'.
        if ($data['tipo'] !== ActivityType::OUTROS->value) {
            $data['tipo_outros'] = null;
        } elseif (trim($data['tipo_outros']) === '') {
            $data['tipo_outros'] = null;
        } else {
            $data['tipo_outros'] = trim($data['tipo_outros']);
        }

        if ($this->activityId !== null) {
            app(ActivityService::class)->update(
                Activity::query()->findOrFail($this->activityId),
                $data,
            );
        } else {
            app(ActivityService::class)->create($data);
        }

        session()->flash('actividade_criada', true);

        // BLOCO 4: quando aberto a partir da oportunidade, volta ao detalhe.
        if ($this->fromOpportunity && $this->opportunity_id !== null) {
            $opportunity = Opportunity::find($this->opportunity_id);

            if ($opportunity) {
                $this->redirect(route('opportunities.show', $opportunity), navigate: true);

                return;
            }
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
