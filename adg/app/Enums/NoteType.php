<?php

namespace App\Enums;

enum NoteType: string
{
    case INFORMACAO = 'informacao';
    case DECISAO = 'decisao';
    case LEMBRETE = 'lembrete';
    case REUNIAO = 'reuniao';

    public function label(): string
    {
        return match ($this) {
            self::INFORMACAO => 'Informação',
            self::DECISAO => 'Decisão',
            self::LEMBRETE => 'Lembrete',
            self::REUNIAO => 'Reunião',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::INFORMACAO => 'bg-soft-sky text-info',
            self::DECISAO => 'bg-soft-indigo text-primary',
            self::LEMBRETE => 'bg-soft-purple text-purple',
            self::REUNIAO => 'bg-soft-teal text-teal',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::INFORMACAO => 'feather-info',
            self::DECISAO => 'feather-check-circle',
            self::LEMBRETE => 'feather-bell',
            self::REUNIAO => 'feather-users',
        };
    }
}
