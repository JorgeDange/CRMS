<?php

namespace App\Services;

use App\Enums\ConciliactionClassificacao;
use App\Models\Conciliaction;

class ConciliactionService
{
    /**
     * Cria uma conciliação (Sprint 6 / resposta.md §8.9).
     */
    public function create(array $data): Conciliaction
    {
        return Conciliaction::create($data);
    }

    /**
     * Actualiza uma conciliação existente.
     */
    public function update(Conciliaction $conciliaction, array $data): Conciliaction
    {
        $conciliaction->fill($data);
        $conciliaction->save();

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

        return $conciliaction;
    }

    /**
     * Elimina definitivamente (sem soft deletes).
     */
    public function remove(Conciliaction $conciliaction): void
    {
        $conciliaction->delete();
    }
}
