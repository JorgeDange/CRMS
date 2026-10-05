<?php

namespace App\Services;

use App\Enums\ContractStatus;
use App\Models\Contract;

class ContractService
{
    /**
     * Cria um contrato (Sprint 7 / resposta.md §8.10).
     */
    public function create(array $data): Contract
    {
        return Contract::create($data);
    }

    /**
     * Actualiza um contrato existente.
     */
    public function update(Contract $contract, array $data): Contract
    {
        $contract->fill($data);
        $contract->save();

        return $contract;
    }

    /**
     * Define o estado do contrato.
     */
    public function setStatus(Contract $contract, ContractStatus $status): Contract
    {
        $contract->estado = $status;
        $contract->save();

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

        return $contract;
    }

    /**
     * Cancela o contrato.
     */
    public function cancelar(Contract $contract): Contract
    {
        $contract->estado = ContractStatus::CANCELADO;
        $contract->save();

        return $contract;
    }

    /**
     * Elimina definitivamente (soft delete).
     */
    public function remove(Contract $contract): void
    {
        $contract->delete();
    }
}
