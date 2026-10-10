<?php

namespace App\Enums;

enum ActivityType: string
{
    case VISITA = 'visita';
    case CHAMADA = 'chamada';
    case REUNIAO = 'reuniao';
    case PROPOSTA = 'proposta';
    case COBRANCA = 'cobranca';
    // BLOCO 3 (mudancas.md): tipo personalizado com texto em tipo_outros.
    case OUTROS = 'outros';

    /**
     * Legenda de tipos do prototipo: apps-notes.html (select do formulario)
     * e apps-calendar.html (legenda lateral).
     */
    public function label(): string
    {
        return match ($this) {
            self::VISITA => 'Visita',
            self::CHAMADA => 'Chamada',
            self::REUNIAO => 'Reunião',
            self::PROPOSTA => 'Proposta',
            self::COBRANCA => 'Cobrança',
            self::OUTROS => 'Outros',
        };
    }

    /**
     * Rotulo plural usado nos filtros laterais (apps-notes/apps-calendar).
     */
    public function labelPlural(): string
    {
        return match ($this) {
            self::VISITA => 'Visitas',
            self::CHAMADA => 'Chamadas',
            self::REUNIAO => 'Reuniões',
            self::PROPOSTA => 'Propostas',
            self::COBRANCA => 'Cobranças',
            self::OUTROS => 'Outros',
        };
    }

    /**
     * Cor da legenda do calendario (apps-calendar.html, #calendarList).
     */
    public function color(): string
    {
        return match ($this) {
            self::VISITA => '#5485e4',
            self::CHAMADA => '#25b865',
            self::REUNIAO => '#d13b4c',
            self::PROPOSTA => '#17a2b8',
            self::COBRANCA => '#e49e3d',
            self::OUTROS => '#6c757d',
        };
    }

    /**
     * Badge do tipo (legenda "Legenda de tipos" de apps-notes.html).
     */
    public function badge(): string
    {
        return match ($this) {
            self::VISITA => 'bg-soft-sky text-info',
            self::CHAMADA => 'bg-soft-purple text-purple',
            self::REUNIAO => 'bg-soft-indigo text-primary',
            self::PROPOSTA => 'bg-soft-teal text-teal',
            self::COBRANCA => 'bg-soft-danger text-danger',
            self::OUTROS => 'bg-soft-gray text-muted',
        };
    }

    /**
     * Fundo suave usado no bloco de data dos eventos do calendario.
     */
    public function softBadge(): string
    {
        return explode(' ', $this->badge())[0];
    }

    /**
     * Icone do filtro lateral (apps-notes.html, content-sidebar).
     */
    public function icon(): string
    {
        return match ($this) {
            self::VISITA => 'feather-map-pin',
            self::CHAMADA => 'feather-phone',
            self::REUNIAO => 'feather-users',
            self::PROPOSTA => 'feather-file-text',
            self::COBRANCA => 'feather-dollar-sign',
            self::OUTROS => 'feather-more-horizontal',
        };
    }
}
