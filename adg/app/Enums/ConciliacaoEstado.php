<?php

namespace App\Enums;

/**
 * Estado da conciliacao denormalizado em opportunities.conciliacao_estado
 * (Sprint 13 — tabela de clientes). Valores iguais a ConciliactionClassificacao.
 */
enum ConciliacaoEstado: string
{
    case CONCORDANTE = 'concordante';
    case DISCORDANTE = 'discordante';
    case PENDENTE = 'pendente';

    public function label(): string
    {
        return match ($this) {
            self::CONCORDANTE => 'Concordante',
            self::DISCORDANTE => 'Discordante',
            self::PENDENTE => 'Pendente',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::CONCORDANTE => 'success',
            self::DISCORDANTE => 'danger',
            self::PENDENTE => 'warning',
        };
    }

    public function badgeClass(): string
    {
        return 'bg-soft-'.$this->badgeVariant().' text-'.$this->badgeVariant();
    }
}
