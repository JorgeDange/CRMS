<?php

namespace App\Enums;

enum ConciliactionClassificacao: string
{
    case CONCORDANTE = 'concordante';
    case DISCORDANTE = 'discordante';
    case PENDENTE = 'pendente';

    public function label(): string
    {
        return match ($this) {
            self::CONCORDANTE => 'Concordante',
            self::DISCORDANTE => 'Discordante',
            self::PENDENTE => 'Pendente',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::CONCORDANTE => 'success',
            self::DISCORDANTE => 'danger',
            self::PENDENTE => 'warning',
        };
    }

    public function badgeClass(): string
    {
        return 'bg-soft-'.$this->badgeVariant().' text-'.$this->badgeVariant();
    }

    public function icon(): string
    {
        return match ($this) {
            self::CONCORDANTE => 'feather-check-circle',
            self::DISCORDANTE => 'feather-x-circle',
            self::PENDENTE => 'feather-alert-triangle',
        };
    }
}
