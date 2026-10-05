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
            self::EXPIRADO => 'warning',
            self::RENOVADO => 'info',
            self::CANCELADO => 'danger',
        };
    }

    public function badgeClass(): string
    {
        return 'bg-soft-'.$this->badgeVariant().' text-'.$this->badgeVariant();
    }

    public function icon(): string
    {
        return match ($this) {
            self::ACTIVO => 'feather-check-circle',
            self::EXPIRADO => 'feather-alert-triangle',
            self::RENOVADO => 'feather-refresh-cw',
            self::CANCELADO => 'feather-x-circle',
        };
    }
}
