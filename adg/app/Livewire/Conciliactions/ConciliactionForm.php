<?php

namespace App\Livewire\Conciliactions;

use App\Enums\ConciliactionClassificacao;
use App\Models\Commercial;
use App\Models\Conciliaction;
use App\Models\Opportunity;
use App\Services\ConciliactionService;
use Livewire\Component;

class ConciliactionForm extends Component
{
    public ?int $conciliactionId = null;

    public ?int $opportunity_id = null;

    public ?int $commercial_id = null;

    public string $data = '';

    public string $valor = '';

    public string $classificacao = 'pendente';

    public string $observacoes = '';

    public bool $resolvido = false;

    public function mount(): void
    {
        $this->data = now()->toDateString();

        $uuid = request()->query('conciliaction');

        if (! is_string($uuid) || $uuid === '') {
            return;
        }

        $conciliaction = Conciliaction::query()->where('uuid', $uuid)->first();

        if (! $conciliaction) {
            return;
        }

        $this->conciliactionId = $conciliaction->id;
        $this->opportunity_id = $conciliaction->opportunity_id;
        $this->commercial_id = $conciliaction->commercial_id;
        $this->data = $conciliaction->data->format('Y-m-d');
        $this->valor = (string) $conciliaction->valor;
        $this->classificacao = $conciliaction->classificacao->value;
        $this->observacoes = (string) $conciliaction->observacoes;
        $this->resolvido = $conciliaction->resolvido;
    }

    protected function rules(): array
    {
        return [
            'opportunity_id' => ['required', 'integer', 'exists:opportunities,id'],
            'commercial_id' => ['nullable', 'integer', 'exists:commercials,id'],
            'data' => ['required', 'date'],
            'valor' => ['required', 'numeric', 'min:0'],
            'classificacao' => ['required', 'in:'.implode(',', array_column(ConciliactionClassificacao::cases(), 'value'))],
            'observacoes' => ['nullable', 'string', 'max:4000'],
            'resolvido' => ['boolean'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate();

        $data['valor'] = (float) $data['valor'];

        if ($this->conciliactionId !== null) {
            app(ConciliactionService::class)->update(
                Conciliaction::findOrFail($this->conciliactionId),
                $data,
            );
        } else {
            app(ConciliactionService::class)->create($data);
        }

        $this->redirect(route('conciliactions'), navigate: true);
    }

    public function resolver(): void
    {
        if ($this->conciliactionId === null) {
            return;
        }

        app(ConciliactionService::class)->resolver(
            Conciliaction::findOrFail($this->conciliactionId)
        );

        $this->redirect(route('conciliactions'), navigate: true);
    }

    public function render()
    {
        return view('livewire.conciliactions.conciliaction-form', [
            'oportunidades' => Opportunity::query()->orderBy('nome')->get(['id', 'nome']),
            'comerciais' => Commercial::query()->where('activo', true)->orderBy('nome')->get(['id', 'nome']),
            'classificacoes' => ConciliactionClassificacao::cases(),
            'editing' => $this->conciliactionId !== null,
        ]);
    }
}
