<?php

namespace App\Enums;

enum AlertType: string
{
    case OPORTUNIDADE = 'oportunidade';
    case CLIENTE = 'cliente';
    case CONTRATO = 'contrato';
    case LEAD_INATIVO = 'lead_inativo';
    case CONCILIACAO = 'conciliacao';

    public function label(): string
    {
        return match ($this) {
            self::OPORTUNIDADE => 'Oportunidade',
            self::CLIENTE => 'Cliente',
            self::CONTRATO => 'Contrato',
            self::LEAD_INATIVO => 'Lead inactivo',
            self::CONCILIACAO => 'Conciliação',
        };
    }

    public function severity(): string
    {
        return match ($this) {
            self::OPORTUNIDADE => 'warning',
            self::CLIENTE => 'info',
            self::CONTRATO => 'danger',
            self::LEAD_INATIVO => 'danger',
            self::CONCILIACAO => 'info',
        };
    }
}
