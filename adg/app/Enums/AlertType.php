<?php

namespace App\Enums;

enum AlertType: string
{
    case OPORTUNIDADE = 'oportunidade';
    case CLIENTE = 'cliente';
    case CONTRATO = 'contrato';
    case LEAD_INATIVO = 'lead_inativo';
    case CONCILIACAO = 'conciliacao';
    case AGENDA = 'agenda';

    public function label(): string
    {
        return match ($this) {
            self::AGENDA => 'Agenda',
            self::OPORTUNIDADE => 'Oportunidade',
            self::CLIENTE => 'Cliente',
            self::CONTRATO => 'Contrato',
            self::LEAD_INATIVO => 'Lead Inativo',
            self::CONCILIACAO => 'Conciliação',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::AGENDA => 'feather-calendar',
            self::OPORTUNIDADE => 'feather-alert-triangle',
            self::CLIENTE => 'feather-users',
            self::CONTRATO => 'feather-file-text',
            self::LEAD_INATIVO => 'feather-clock',
            self::CONCILIACAO => 'feather-dollar-sign',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::AGENDA => 'success',
            self::OPORTUNIDADE => 'warning',
            self::CLIENTE => 'info',
            self::CONTRATO => 'primary',
            self::LEAD_INATIVO => 'danger',
            self::CONCILIACAO => 'secondary',
        };
    }

    public function badgeClass(): string
    {
        return 'bg-soft-'.$this->badgeVariant().' text-'.$this->badgeVariant();
    }
}
