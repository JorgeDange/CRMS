<?php

namespace App\Enums;

enum OpportunityStage: string
{
    case LEAD = 'lead';
    case QUALIFICACAO = 'qualificacao';
    case VISITA = 'visita';
    case PROPOSTA_NEGOCIACAO = 'proposta_negociacao';
    case APROVACAO = 'aprovacao';
    case BILHETE = 'bilhete';
    case RESULTADO = 'resultado';
    case CLIENTE_APROVADO = 'cliente_aprovado';
    case CLIENTE_NAO_APROVADO = 'cliente_nao_aprovado';

    /**
     * Ordem do pipeline (resposta.md §5 — 9 fases).
     */
    public static function pipeline(): array
    {
        return [
            self::LEAD,
            self::QUALIFICACAO,
            self::VISITA,
            self::PROPOSTA_NEGOCIACAO,
            self::APROVACAO,
            self::BILHETE,
            self::RESULTADO,
            self::CLIENTE_APROVADO,
            self::CLIENTE_NAO_APROVADO,
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::LEAD => 'Lead',
            self::QUALIFICACAO => 'Qualificação',
            self::VISITA => 'Visita',
            self::PROPOSTA_NEGOCIACAO => 'Proposta/Negociação',
            self::APROVACAO => 'Aprovação',
            self::BILHETE => 'Bilhete',
            self::RESULTADO => 'Resultado',
            self::CLIENTE_APROVADO => 'Cliente Aprovado',
            self::CLIENTE_NAO_APROVADO => 'Cliente Não Aprovado',
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
        return $this === self::CLIENTE_APROVADO || $this === self::CLIENTE_NAO_APROVADO;
    }

    public function isClientState(): bool
    {
        return $this === self::CLIENTE_APROVADO || $this === self::CLIENTE_NAO_APROVADO;
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::LEAD => 'secondary',
            self::QUALIFICACAO => 'info',
            self::VISITA => 'primary',
            self::PROPOSTA_NEGOCIACAO => 'warning',
            self::APROVACAO => 'warning',
            self::BILHETE => 'info',
            self::RESULTADO => 'primary',
            self::CLIENTE_APROVADO => 'success',
            self::CLIENTE_NAO_APROVADO => 'danger',
        };
    }
}
