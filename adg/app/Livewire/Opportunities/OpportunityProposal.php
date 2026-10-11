<?php

namespace App\Livewire\Opportunities;

use App\Models\Opportunity;
use App\Models\Product;
use Livewire\Component;

/**
 * BLOCO 6 (mudancas.md): aba "Proposta" do detalhe da oportunidade —
 * formulario com 9 campos directos em opportunities (uma proposta por
 * oportunidade).
 */
class OpportunityProposal extends Component
{
    public int $opportunityId;

    public ?int $proposta_produto_id = null;

    public ?string $proposta_preco = null;

    public ?string $proposta_volume = null;

    public string $proposta_volume_unidade = 'L';

    public ?string $proposta_periodo_inicio = null;

    public ?string $proposta_periodo_fim = null;

    public ?string $proposta_credito = null;

    public ?string $proposta_facturacao = null;

    public ?string $proposta_non_oil = null;

    public bool $saved = false;

    public function mount(int $opportunityId): void
    {
        $this->opportunityId = $opportunityId;

        $opportunity = Opportunity::findOrFail($opportunityId);

        $this->proposta_produto_id = $opportunity->proposta_produto_id;
        $this->proposta_preco = $opportunity->proposta_preco !== null ? (string) $opportunity->proposta_preco : null;
        $this->proposta_volume = $opportunity->proposta_volume !== null ? (string) $opportunity->proposta_volume : null;
        $this->proposta_volume_unidade = $opportunity->proposta_volume_unidade ?? 'L';
        $this->proposta_periodo_inicio = $opportunity->proposta_periodo_inicio?->format('Y-m-d');
        $this->proposta_periodo_fim = $opportunity->proposta_periodo_fim?->format('Y-m-d');
        $this->proposta_credito = $opportunity->proposta_credito !== null ? (string) $opportunity->proposta_credito : null;
        $this->proposta_facturacao = $opportunity->proposta_facturacao !== null ? (string) $opportunity->proposta_facturacao : null;
        $this->proposta_non_oil = $opportunity->proposta_non_oil !== null ? (string) $opportunity->proposta_non_oil : null;
    }

    protected function rules(): array
    {
        return [
            'proposta_produto_id' => ['nullable', 'integer', 'exists:products,id'],
            'proposta_preco' => ['nullable', 'numeric', 'min:0'],
            'proposta_volume' => ['nullable', 'numeric', 'min:0'],
            'proposta_volume_unidade' => ['required', 'in:L,m³'],
            'proposta_periodo_inicio' => ['nullable', 'date'],
            'proposta_periodo_fim' => ['nullable', 'date', 'after_or_equal:proposta_periodo_inicio'],
            'proposta_credito' => ['nullable', 'numeric', 'min:0'],
            'proposta_facturacao' => ['nullable', 'numeric', 'min:0'],
            'proposta_non_oil' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate();

        // Normaliza: strings vazias -> null.
        foreach (['proposta_produto_id', 'proposta_preco', 'proposta_volume', 'proposta_periodo_inicio', 'proposta_periodo_fim', 'proposta_credito', 'proposta_facturacao', 'proposta_non_oil'] as $campo) {
            if ($data[$campo] === '' || $data[$campo] === null) {
                $data[$campo] = null;
            }
        }

        $opportunity = Opportunity::findOrFail($this->opportunityId);
        $opportunity->fill($data);
        $opportunity->save();

        $this->saved = true;
    }

    public function render()
    {
        return view('livewire.opportunities.opportunity-proposal', [
            'produtos' => Product::where('activo', true)->orderBy('nome')->get(['id', 'nome']),
            'opportunity' => Opportunity::findOrFail($this->opportunityId),
        ]);
    }
}
