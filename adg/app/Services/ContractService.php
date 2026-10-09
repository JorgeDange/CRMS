<?php

namespace App\Services;

use App\Enums\ContractStatus;
use App\Enums\ContratoEstado;
use App\Models\Contract;
use App\Models\Opportunity;

class ContractService
{
    /**
     * Cria um contrato (Sprint 7 / resposta.md §8.10).
     */
    public function create(array $data): Contract
    {
        $contract = Contract::create($data);

        $this->sincronizarOpportunity($contract);

        return $contract;
    }

    /**
     * Actualiza um contrato existente.
     */
    public function update(Contract $contract, array $data): Contract
    {
        $contract->fill($data);
        $contract->save();

        $this->sincronizarOpportunity($contract);

        return $contract;
    }

    /**
     * Define o estado do contrato.
     */
    public function setStatus(Contract $contract, ContractStatus $status): Contract
    {
        $contract->estado = $status;
        $contract->save();

        $this->sincronizarOpportunity($contract);

        return $contract;
    }

    /**
     * Marca como renovado.
     */
    public function renovar(Contract $contract, array $novosDados = []): Contract
    {
        $contract->estado = ContractStatus::RENOVADO;

        if (isset($novosDados['data_fim'])) {
            $contract->data_fim = $novosDados['data_fim'];
        }

        $contract->save();

        $this->sincronizarOpportunity($contract);

        return $contract;
    }

    /**
     * Cancela o contrato.
     */
    public function cancelar(Contract $contract): Contract
    {
        $contract->estado = ContractStatus::CANCELADO;
        $contract->save();

        $this->sincronizarOpportunity($contract);

        return $contract;
    }

    /**
     * Elimina definitivamente (soft delete).
     */
    public function remove(Contract $contract): void
    {
        $contract->delete();
    }

    /**
     * Actualiza opportunities.contrato_estado (Tabela de Clientes,
     * seguir.md §14.1): ACTIVO/RENOVADO → SIM, EXPIRADO → CADUCIDADE,
     * CANCELADO → NAO.
     */
    private function sincronizarOpportunity(Contract $contract): void
    {
        if (! $contract->opportunity_id) {
            return;
        }

        $estado = $contract->estado instanceof ContractStatus
            ? $contract->estado
            : ContractStatus::tryFrom((string) $contract->estado) ?? ContractStatus::ACTIVO;

        $contratoEstado = match ($estado) {
            ContractStatus::ACTIVO, ContractStatus::RENOVADO => ContratoEstado::SIM,
            ContractStatus::EXPIRADO => ContratoEstado::CADUCIDADE,
            ContractStatus::CANCELADO => ContratoEstado::NAO,
        };

        Opportunity::whereKey($contract->opportunity_id)
            ->update(['contrato_estado' => $contratoEstado->value]);
    }
}
