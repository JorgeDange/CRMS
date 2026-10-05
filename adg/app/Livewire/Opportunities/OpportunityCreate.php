<?php

namespace App\Livewire\Opportunities;

use App\Enums\OpportunityStage;
use App\Enums\TipoRelacao;
use App\Models\Commercial;
use App\Models\Opportunity;
use App\Models\Product;
use App\Services\OpportunityService;
use Livewire\Component;

class OpportunityCreate extends Component
{
    public string $cliente = '';

    public string $pessoa_contacto = '';

    public string $titulo = '';

    public string $valor = '';

    public string $fase_atual = 'lead';

    public int $probabilidade = 30;

    public string $data_fecho = '';

    public string $descricao = '';

    public string $tipo_relacao = '';

    public ?int $comercial_id = null;

    public ?int $produto_id = null;

    public string $origem = '';

    public string $sector = '';

    public string $localizacao = '';

    public string $telefone = '';

    public string $email = '';

    public string $nif = '';

    public string $codigo_sap = '';

    protected function rules(): array
    {
        return [
            'cliente' => ['required', 'string', 'max:255'],
            'pessoa_contacto' => ['nullable', 'string', 'max:255'],
            'titulo' => ['required', 'string', 'max:255'],
            'valor' => ['required', 'numeric', 'min:0'],
            'fase_atual' => ['required', 'in:'.implode(',', array_column(OpportunityStage::cases(), 'value'))],
            'probabilidade' => ['required', 'integer', 'min:0', 'max:100'],
            'data_fecho' => ['nullable', 'date'],
            'descricao' => ['nullable', 'string', 'max:4000'],
            'tipo_relacao' => ['nullable', 'in:'.implode(',', array_column(TipoRelacao::cases(), 'value'))],
            'comercial_id' => ['nullable', 'integer', 'exists:commercials,id'],
            'produto_id' => ['nullable', 'integer', 'exists:products,id'],
            'origem' => ['nullable', 'string', 'max:100'],
            'sector' => ['nullable', 'string', 'max:100'],
            'localizacao' => ['nullable', 'string', 'max:255'],
            'telefone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'nif' => ['nullable', 'string', 'max:50'],
            'codigo_sap' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate();

        app(OpportunityService::class)->create([
            'nome' => $data['cliente'],
            'empresa' => $data['cliente'],
            'pessoa_contacto' => $data['pessoa_contacto'] !== '' ? $data['pessoa_contacto'] : null,
            'titulo' => $data['titulo'],
            'valor_estimado' => $data['valor'],
            'fase_atual' => $data['fase_atual'],
            'probabilidade' => $data['probabilidade'],
            'data_fecho' => $data['data_fecho'] !== '' ? $data['data_fecho'] : null,
            'interesse' => $data['descricao'] !== '' ? $data['descricao'] : null,
            'tipo_relacao' => $data['tipo_relacao'] !== '' ? $data['tipo_relacao'] : null,
            'comercial_id' => $data['comercial_id'],
            'produto_id' => $data['produto_id'],
            'origem' => $data['origem'] !== '' ? $data['origem'] : null,
            'sector' => $data['sector'] !== '' ? $data['sector'] : null,
            'localizacao' => $data['localizacao'] !== '' ? $data['localizacao'] : null,
            'telefone' => $data['telefone'] !== '' ? $data['telefone'] : null,
            'email' => $data['email'] !== '' ? $data['email'] : null,
            'nif' => $data['nif'] !== '' ? $data['nif'] : null,
            'codigo_sap' => $data['codigo_sap'] !== '' ? $data['codigo_sap'] : null,
        ]);

        $this->redirect(route('pipeline'), navigate: true);
    }

    public function render()
    {
        $clientes = Opportunity::query()
            ->get(['nome', 'empresa'])
            ->flatMap(fn (Opportunity $opportunity): array => [
                $opportunity->nome,
                $opportunity->empresa,
            ])
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $comerciais = Commercial::query()->where('activo', true)->orderBy('nome')->get();
        $produtos = Product::query()->where('activo', true)->orderBy('nome')->get();

        return view('livewire.opportunities.create', [
            'clientes' => $clientes,
            'comerciais' => $comerciais,
            'produtos' => $produtos,
        ]);
    }
}
