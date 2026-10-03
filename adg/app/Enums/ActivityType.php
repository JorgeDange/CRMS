<?php

namespace App\Enums;

enum ActivityType: string
{
    case VISITA = 'visita';
    case CHAMADA = 'chamada';
    case REUNIAO = 'reuniao';
    case PROPOSTA = 'proposta';
    case COBRANCA = 'cobranca';

    public function label(): string
    {
        return match ($this) {
            self::VISITA => 'Visita',
            self::CHAMADA => 'Chamada',
            self::REUNIAO => 'Reunião',
            self::PROPOSTA => 'Proposta',
            self::COBRANCA => 'Cobrança',
        };
    }
}
