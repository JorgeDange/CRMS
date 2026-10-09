<?php

namespace App\Livewire\Clients;

use App\Enums\ConciliacaoEstado;
use App\Enums\ContratoEstado;
use App\Models\Commercial;
use App\Models\Opportunity;
use Livewire\Component;

/**
 * Tabela de Clientes (Sprint 13 — seguir.md §5): clientes aprovados
 * (fase_atual = cliente_aprovado) com os campos denormalizados do espelho
 * Excel do cliente.
 */
class ClientTable extends Component
{
    public string $search = '';

    public string $kamFilter = '';

    public string $comprasFilter = ''; // '', 'sim', 'nao'

    public string $conciliacaoFilter = ''; // '', valor de ConciliacaoEstado

    public string $contratoFilter = ''; // '', valor de ContratoEstado

    public string $sortField = 'nome';

    public string $sortDirection = 'asc';

    public int $perPage = 20;

    public function resetFilters(): void
    {
        $this->reset([
            'search',
            'kamFilter',
            'comprasFilter',
            'conciliacaoFilter',
            'contratoFilter',
        ]);
    }

    public function sortBy(string $field): void
    {
        if (! in_array($field, ['nome', 'codigo'], true)) {
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';

            return;
        }

        $this->sortField = $field;
        $this->sortDirection = 'asc';
    }

    public function render()
    {
        $clients = Opportunity::query()
            ->where('fase_atual', 'cliente_aprovado')
            ->with('commercial')
            ->when($this->search !== '', function ($query): void {
                $like = '%'.$this->search.'%';
                $query->where(fn ($q) => $q
                    ->where('nome', 'like', $like)
                    ->orWhere('codigo', 'like', $like));
            })
            ->when($this->kamFilter !== '', fn ($query) => $query->where('comercial_id', $this->kamFilter))
            ->when($this->comprasFilter !== '', fn ($query) => $query->where('tem_compras', $this->comprasFilter === 'sim'))
            ->when($this->conciliacaoFilter !== '', fn ($query) => $query->where('conciliacao_estado', $this->conciliacaoFilter))
            ->when($this->contratoFilter !== '', fn ($query) => $query->where('contrato_estado', $this->contratoFilter))
            ->orderBy($this->sortField, $this->sortDirection === 'desc' ? 'desc' : 'asc')
            ->paginate($this->perPage);

        return view('livewire.clients.client-table', [
            'clients' => $clients,
            'kams' => Commercial::where('activo', true)->orderBy('nome')->get(),
            'conciliacaoEstados' => ConciliacaoEstado::cases(),
            'contratoEstados' => ContratoEstado::cases(),
        ]);
    }
}
