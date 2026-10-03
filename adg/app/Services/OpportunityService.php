<?php

namespace App\Services;

use App\Enums\OpportunityStage;
use App\Models\Opportunity;
use App\Models\OpportunityStageHistory;
use App\Models\User;

class OpportunityService
{
    /**
     * Move a oportunidade de fase, regista o historico (8.3) e aplica as
     * regras dos estados finais: Aprovado -> probabilidade 100 +
     * data_aprovacao; Nao Aprovado -> probabilidade 0.
     */
    public function changeStage(
        Opportunity $opportunity,
        OpportunityStage $to,
        ?string $observacao = null,
        ?int $userId = null,
    ): Opportunity {
        $from = $opportunity->fase_atual;

        if ($from === $to) {
            return $opportunity;
        }

        $opportunity->fase_atual = $to;

        if ($to === OpportunityStage::APROVADO) {
            $opportunity->probabilidade = 100;
            $opportunity->data_aprovacao ??= now();
        }

        if ($to === OpportunityStage::NAO_APROVADO) {
            $opportunity->probabilidade = 0;
            $opportunity->data_aprovacao = null;
            $opportunity->motivo_nao_aprovacao = $observacao ?? $opportunity->motivo_nao_aprovacao;
        }

        $opportunity->save();

        OpportunityStageHistory::create([
            'opportunity_id' => $opportunity->id,
            'fase_anterior' => $from?->value,
            'fase_nova' => $to->value,
            'user_id' => $userId ?? auth()->id() ?? User::query()->value('id'),
            'observacao' => $observacao,
        ]);

        return $opportunity;
    }

    /**
     * Formato monetario do prototipo: Kz 110.400.000
     */
    public static function formatKz(float|int|string|null $value): string
    {
        return 'Kz '.number_format((float) $value, 0, '.', '.');
    }
}
