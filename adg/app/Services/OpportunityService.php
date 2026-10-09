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
     * regras dos estados finais:
     * - Cliente Aprovado -> probabilidade 100 + data_aprovacao
     * - Cliente Não Aprovado -> probabilidade 0 + motivo_nao_aprovacao
     * Os demais estados não alteram probabilidade automaticamente.
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

        // Regras dos estados finais (resposta.md §5)
        if ($to === OpportunityStage::CLIENTE_APROVADO) {
            $opportunity->probabilidade = 100;
            $opportunity->data_aprovacao ??= now();
        }

        if ($to === OpportunityStage::CLIENTE_NAO_APROVADO) {
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
     * Cria uma oportunidade (Sprint 3) aplicando as regras dos estados
     * finais e registando a fase inicial no historico (8.3).
     */
    public function create(array $data): Opportunity
    {
        $stage = OpportunityStage::tryFrom($data['fase_atual'] ?? '')
            ?? OpportunityStage::LEAD;

        $data['fase_atual'] = $stage->value;

        if ($stage === OpportunityStage::CLIENTE_APROVADO) {
            $data['probabilidade'] = 100;
            $data['data_aprovacao'] = now();
        }

        if ($stage === OpportunityStage::CLIENTE_NAO_APROVADO) {
            $data['probabilidade'] = 0;
        }

        $opportunity = Opportunity::create($data);

        OpportunityStageHistory::create([
            'opportunity_id' => $opportunity->id,
            'fase_anterior' => null,
            'fase_nova' => $stage->value,
            'user_id' => auth()->id() ?? User::query()->value('id'),
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

    /**
     * Formato compacto para os KPI cards do Painel Executivo:
     * valores completos (Kz 305.200.000) vazavam do card em ecras
     * estreitos (col-xxl-3 a ~1400px). Precedente: o dashboard ja
     * usa "15M / 33.5M" no footer de Facturacao.
     *   999.999        -> Kz 999.999
     *   305.200.000    -> Kz 305,2M
     *   1.503.000.000  -> Kz 1.503M
     */
    public static function formatKzCompact(float|int|string|null $value): string
    {
        $v = (float) $value;

        if (abs($v) < 1_000_000) {
            return 'Kz '.number_format($v, 0, '.', '.');
        }

        $milhoes = $v / 1_000_000;

        if (abs($milhoes) < 1000) {
            return 'Kz '.number_format($milhoes, 1, ',', '.').'M';
        }

        return 'Kz '.number_format($milhoes, 0, '.', '.').'M';
    }
}
