<?php

namespace App\Livewire\Contracts;

use App\Enums\ContractStatus;
use App\Models\Commercial;
use App\Models\Contract;
use App\Models\Opportunity;
use App\Models\Product;
use App\Services\ContractService;
use Livewire\Component;

class ContractForm extends Component
{
    public ?int $contractId = null;

    public ?int $opportunity_id = null;

    public ?int $commercial_id = null;

    public ?int $product_id = null;

    public string $numero_contrato = '';

    public string $tipo = '';

    public string $data_inicio = '';

    public string $data_fim = '';

    public string $valor = '';

    public string $estado = 'activo';

    public string $documento = '';

    public string $notas = '';

    public function mount(?string $contract = null): void
    {
        $this->data_inicio = now()->toDateString();
        $this->data_fim = now()->addYear()->toDateString();

        $uuid = $contract ?? request()->query('contract');

        if (! is_string($uuid) || $uuid === '') {
            return;
        }

        $contract = Contract::query()->where('uuid', $uuid)->first();

        if (! $contract) {
            return;
        }

        $this->contractId = $contract->id;
        $this->opportunity_id = $contract->opportunity_id;
        $this->commercial_id = $contract->commercial_id;
        $this->product_id = $contract->product_id;
        $this->numero_contrato = $contract->numero_contrato;
        $this->tipo = $contract->tipo ?? '';
        $this->data_inicio = $contract->data_inicio->format('Y-m-d');
        $this->data_fim = $contract->data_fim->format('Y-m-d');
        $this->valor = (string) $contract->valor;
        $this->estado = $contract->estado->value;
        $this->documento = $contract->documento ?? '';
        $this->notas = $contract->notas ?? '';
    }

    protected function rules(): array
    {
        return [
            'opportunity_id' => ['required', 'integer', 'exists:opportunities,id'],
            'commercial_id' => ['nullable', 'integer', 'exists:commercials,id'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'numero_contrato' => ['required', 'string', 'max:50', 'unique:contracts,numero_contrato,'.$this->contractId],
            'tipo' => ['nullable', 'string', 'max:50'],
            'data_inicio' => ['required', 'date'],
            'data_fim' => ['required', 'date', 'after_or_equal:data_inicio'],
            'valor' => ['required', 'numeric', 'min:0'],
            'estado' => ['required', 'in:'.implode(',', array_column(ContractStatus::cases(), 'value'))],
            'documento' => ['nullable', 'string', 'max:255'],
            'notas' => ['nullable', 'string', 'max:4000'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate();

        $data['valor'] = (float) $data['valor'];

        if ($this->contractId !== null) {
            app(ContractService::class)->update(
                Contract::findOrFail($this->contractId),
                $data,
            );
        } else {
            app(ContractService::class)->create($data);
        }

        $this->redirect(route('contracts'), navigate: true);
    }

    public function render()
    {
        return view('livewire.contracts.contract-form', [
            'oportunidades' => Opportunity::query()->orderBy('nome')->get(['id', 'nome']),
            'comerciais' => Commercial::query()->where('activo', true)->orderBy('nome')->get(['id', 'nome']),
            'produtos' => Product::query()->where('activo', true)->orderBy('nome')->get(['id', 'nome']),
            'estados' => ContractStatus::cases(),
            'editing' => $this->contractId !== null,
        ]);
    }
}
