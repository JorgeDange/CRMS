<?php

namespace App\Services;

use App\Enums\ConciliactionClassificacao;
use App\Models\Conciliaction;
use App\Models\Opportunity;

class ConciliactionService
{
    /**
     * Cria uma conciliação (Sprint 6 / resposta.md §8.9).
     */
    public function create(array $data): Conciliaction
    {
        $conciliaction = Conciliaction::create($data);

        $this->sincronizarOpportunity($conciliaction);

        return $conciliaction;
    }

    /**
     * Actualiza uma conciliação existente.
     */
    public function update(Conciliaction $conciliaction, array $data): Conciliaction
    {
        $conciliaction->fill($data);
        $conciliaction->save();

        $this->sincronizarOpportunity($conciliaction);

        return $conciliaction;
    }

    /**
     * Marca a conciliação como resolvida.
     */
    public function resolver(Conciliaction $conciliaction, ?string $observacao = null): Conciliaction
    {
        $conciliaction->resolvido = true;
        $conciliaction->resolvido_em = now();

        if ($observacao) {
            $conciliaction->observacoes = ($conciliaction->observacoes ? $conciliaction->observacoes.' | ' : '').$observacao;
        }

        $conciliaction->save();

        return $conciliaction;
    }

    /**
     * Define a classificação da conciliação.
     */
    public function classificar(Conciliaction $conciliaction, ConciliactionClassificacao $classificacao): Conciliaction
    {
        $conciliaction->classificacao = $classificacao;
        $conciliaction->save();

        $this->sincronizarOpportunity($conciliaction);

        return $conciliaction;
    }

    /**
     * Elimina definitivamente (sem soft deletes).
     */
    public function remove(Conciliaction $conciliaction): void
    {
        $conciliaction->delete();
    }

    /**
     * Actualiza opportunities.conciliacao_estado (Tabela de Clientes,
     * seguir.md §14.1 — campo denormalizado para leitura rapida).
     */
    private function sincronizarOpportunity(Conciliaction $conciliaction): void
    {
        if (! $conciliaction->opportunity_id) {
            return;
        }

        $classificacao = $conciliaction->classificacao;

        // Sem atributo carregado (create sem classificacao) → default da BD.
        $valor = $classificacao instanceof ConciliactionClassificacao
            ? $classificacao->value
            : ($classificacao ?? ConciliactionClassificacao::PENDENTE->value);

        Opportunity::whereKey($conciliaction->opportunity_id)
            ->update(['conciliacao_estado' => $valor]);
    }
}
