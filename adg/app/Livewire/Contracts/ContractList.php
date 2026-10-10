<?php

namespace App\Livewire\Contracts;

use App\Enums\ContractStatus;
use App\Models\Contract;
use App\Services\ContractService;
use Livewire\Component;

class ContractList extends Component
{
    public string $search = '';

    public string $statusFilter = '';

    public string $sort = 'desc';

    public function mount(): void
    {
        $estado = request()->query('estado');

        if (is_string($estado) && in_array($estado, array_column(ContractStatus::cases(), 'value'), true)) {
            $this->statusFilter = $estado;
        }
    }

    public function filterStatus(string $estado): void
    {
        $this->statusFilter = $this->statusFilter === $estado ? '' : $estado;
    }

    public function clearFilters(): void
    {
        $this->statusFilter = '';
        $this->search = '';
    }

    public function updateStatus(int $contractId, string $estado): void
    {
        $contract = Contract::find($contractId);

        if (! $contract) {
            return;
        }

        $enum = ContractStatus::tryFrom($estado);

        if (! $enum) {
            return;
        }

        app(ContractService::class)->setStatus($contract, $enum);
    }

    public function cancelar(int $contractId): void
    {
        $contract = Contract::find($contractId);

        if (! $contract) {
            return;
        }

        app(ContractService::class)->cancelar($contract);
    }

    public function remove(int $contractId): void
    {
        $contract = Contract::find($contractId);

        if (! $contract) {
            return;
        }

        $contract->delete();

        $this->dispatch('contract-removed');
    }

    public function render()
    {
        $contracts = Contract::with(['opportunity', 'commercial', 'product'])
            ->when($this->search !== '', function ($query): void {
                $like = '%'.$this->search.'%';
                $query->where(fn ($q) => $q
                    ->where('numero_contrato', 'like', $like)
                    ->orWhereHas('opportunity', fn ($q2) => $q2->where('nome', 'like', $like))
                    ->orWhereHas('product', fn ($q2) => $q2->where('nome', 'like', $like)));
            })
            ->when($this->statusFilter !== '', fn ($query) => $query->where('estado', $this->statusFilter))
            ->orderBy('data_inicio', $this->sort === 'asc' ? 'asc' : 'desc')
            ->orderBy('id')
            ->get();

        return view('livewire.contracts.contract-list', [
            'contracts' => $contracts,
            'estados' => ContractStatus::cases(),
        ]);
    }
}
