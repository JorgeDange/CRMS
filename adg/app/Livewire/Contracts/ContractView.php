<?php

namespace App\Livewire\Contracts;

use App\Enums\ContractStatus;
use App\Models\Contract;
use App\Services\ContractService;
use Livewire\Component;

class ContractView extends Component
{
    public int $contractId;

    public string $activeTab = 'overview';

    public function mount(Contract $contract): void
    {
        $this->contractId = $contract->id;
    }

    public function setStatus(string $estado): void
    {
        $contract = Contract::find($this->contractId);
        $to = ContractStatus::tryFrom($estado);

        if (! $contract || ! $to || $contract->estado === $to) {
            return;
        }

        app(ContractService::class)->setStatus($contract, $to);

        $this->redirect(route('contracts.show', $contract), navigate: true);
    }

    public function cancelar(): void
    {
        $contract = Contract::find($this->contractId);

        if (! $contract) {
            return;
        }

        app(ContractService::class)->cancelar($contract);

        $this->redirect(route('contracts.show', $contract), navigate: true);
    }

    public function renovar(): void
    {
        $contract = Contract::find($this->contractId);

        if (! $contract) {
            return;
        }

        app(ContractService::class)->renovar($contract);

        $this->redirect(route('contracts.show', $contract), navigate: true);
    }

    public function render()
    {
        $contract = Contract::with(['opportunity', 'commercial', 'product'])->findOrFail($this->contractId);

        return view('livewire.contracts.contract-view', [
            'contract' => $contract,
        ]);
    }
}
