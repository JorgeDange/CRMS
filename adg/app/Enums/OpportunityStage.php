<?php

namespace App\Enums;

enum OpportunityStage: string
{
    case LEAD = 'lead';
    case VISITA = 'visita';
    case APROVACAO = 'aprovacao';
    case APROVADO = 'aprovado';
    case NAO_APROVADO = 'nao_aprovado';

    /**
     * Ordem do pipeline (PROMPT_MASTER §7/§8.2).
     */
    public static function pipeline(): array
    {
        return [
            self::LEAD,
            self::VISITA,
            self::APROVACAO,
            self::APROVADO,
            self::NAO_APROVADO,
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::LEAD => 'Lead',
            self::VISITA => 'Visita',
            self::APROVACAO => 'Aprovação',
            self::APROVADO => 'Cliente Aprovado',
            self::NAO_APROVADO => 'Cliente Não Aprovado',
        };
    }

    public function next(): ?self
    {
        $pipeline = self::pipeline();
        $index = array_search($this, $pipeline, true);

        if ($index === false || ! isset($pipeline[$index + 1])) {
            return null;
        }

        return $pipeline[$index + 1];
    }

    public function previous(): ?self
    {
        $pipeline = self::pipeline();
        $index = array_search($this, $pipeline, true);

        if ($index === false || $index === 0) {
            return null;
        }

        return $pipeline[$index - 1];
    }

    public function isFinal(): bool
    {
        return $this === self::APROVADO || $this === self::NAO_APROVADO;
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::LEAD => 'secondary',
            self::VISITA => 'info',
            self::APROVACAO => 'warning',
            self::APROVADO => 'success',
            self::NAO_APROVADO => 'danger',
        };
    }
}
