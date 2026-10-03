<?php

namespace App\Enums;

enum ContractStatus: string
{
    case ACTIVO = 'activo';
    case EXPIRADO = 'expirado';
    case RENOVADO = 'renovado';
    case CANCELADO = 'cancelado';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVO => 'Activo',
            self::EXPIRADO => 'Expirado',
            self::RENOVADO => 'Renovado',
            self::CANCELADO => 'Cancelado',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::ACTIVO => 'success',
            self::EXPIRADO => 'danger',
            self::RENOVADO => 'info',
            self::CANCELADO => 'secondary',
        };
    }
}
