<?php

namespace App\Enums;

enum TipoRelacao: string
{
    case NOVO_CONTACTO = 'novo_contacto';
    case POTENCIAL_CLIENTE = 'potencial_cliente';
    case CLIENTE_RECORRENTE = 'cliente_recorrente';
    case PARCEIRO = 'parceiro';
    case FORNECEDOR = 'fornecedor';

    public function label(): string
    {
        return match ($this) {
            self::NOVO_CONTACTO => 'Novo Contacto',
            self::POTENCIAL_CLIENTE => 'Potencial Cliente',
            self::CLIENTE_RECORRENTE => 'Cliente Recorrente',
            self::PARCEIRO => 'Parceiro',
            self::FORNECEDOR => 'Fornecedor',
        };
    }
}
