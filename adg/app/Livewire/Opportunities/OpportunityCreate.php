<?php

namespace App\Livewire\Opportunities;

use App\Models\Opportunity;
use App\Services\OpportunityService;
use Livewire\Component;

class OpportunityCreate extends Component
{
    public string $cliente = '';

    public string $titulo = '';

    public string $valor = '';

    public string $fase_atual = 'lead';

    public int $probabilidade = 30;

    public string $data_fecho = '';

    public string $descricao = '';

    protected function rules(): array
    {
        return [
            'cliente' => ['required', 'string', 'max:255'],
            'titulo' => ['required', 'string', 'max:255'],
            'valor' => ['required', 'numeric', 'min:0'],
            'fase_atual' => ['required', 'in:lead,visita,aprovacao,aprovado,nao_aprovado'],
            'probabilidade' => ['required', 'integer', 'min:0', 'max:100'],
            'data_fecho' => ['nullable', 'date'],
            'descricao' => ['nullable', 'string', 'max:4000'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate();

        app(OpportunityService::class)->create([
            'nome' => $data['cliente'],
            'empresa' => $data['cliente'],
            'titulo' => $data['titulo'],
            'valor_estimado' => $data['valor'],
            'fase_atual' => $data['fase_atual'],
            'probabilidade' => $data['probabilidade'],
            'data_fecho' => $data['data_fecho'] !== '' ? $data['data_fecho'] : null,
            'interesse' => $data['descricao'] !== '' ? $data['descricao'] : null,
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

        return view('livewire.opportunities.create', [
            'clientes' => $clientes,
        ]);
    }
}
