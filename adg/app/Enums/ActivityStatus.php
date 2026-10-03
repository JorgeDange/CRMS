<?php

namespace App\Enums;

enum ActivityStatus: string
{
    case PENDENTE = 'pendente';
    case CONCLUIDA = 'concluida';
    case CANCELADA = 'cancelada';

    public function label(): string
    {
        return match ($this) {
            self::PENDENTE => 'Pendente',
            self::CONCLUIDA => 'Concluída',
            self::CANCELADA => 'Cancelada',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::PENDENTE => 'warning',
            self::CONCLUIDA => 'success',
            self::CANCELADA => 'secondary',
        };
    }
}
