<?php

namespace App\Enums;

/**
 * Estado do contrato denormalizado em opportunities.contrato_estado
 * (Sprint 13 — tabela de clientes): SIM / CADUCIDADE / NAO.
 */
enum ContratoEstado: string
{
    case SIM = 'sim';
    case CADUCIDADE = 'caducidade';
    case NAO = 'nao';

    public function label(): string
    {
        return match ($this) {
            self::SIM => 'SIM',
            self::CADUCIDADE => 'CADUCIDADE',
            self::NAO => 'NÃO',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::SIM => 'success',
            self::CADUCIDADE => 'warning',
            self::NAO => 'secondary',
        };
    }

    public function badgeClass(): string
    {
        return 'bg-soft-'.$this->badgeVariant().' text-'.$this->badgeVariant();
    }
}
