<?php

namespace Database\Seeders;

use App\Enums\OpportunityStage;
use App\Models\Opportunity;
use Illuminate\Database\Seeder;

class OpportunitySeeder extends Seeder
{
    /**
     * Dados de demonstracao = 11 cartoes do leads.html do prototipo
     * Mapeados para as 9 fases do pipeline (resposta.md §5):
     * Lead → Qualificação → Visita → Proposta/Negociação → Aprovação → Bilhete → Resultado → Cliente Aprovado/Não Aprovado
     */
    public function run(): void
    {
        $rows = [
            // Leads
            ['lead', 'Renovação contrato 2026', 'SIL – Sociedade Importadora', 12_000_000, 30, '2026-11-30'],
            ['lead', 'Expansão armazém', 'Importadora Kinaxixi', 4_500_000, 25, '2026-12-15'],
            ['lead', 'Fornecimento trimestral ração', 'Agropecuária do Planalto', 6_800_000, 20, '2027-01-10'],
            // Qualificação
            ['qualificacao', 'Qualificação painéis solares', 'Maona Energy Lda', 22_000_000, 40, '2026-10-15'],
            ['qualificacao', 'Qualificação cabos eléctricos', 'Energias do Namibe', 8_200_000, 35, '2026-10-10'],
            // Visita
            ['visita', 'Painéis solares fase 2', 'Maona Energy Lda', 22_000_000, 50, '2026-10-10'],
            ['visita', 'Cabos eléctricos lote 2', 'Energias do Namibe', 8_200_000, 45, '2026-10-05'],
            ['visita', 'Mobiliário escritórios', 'Construções Alicia Lda', 3_400_000, 40, '2026-10-20'],
            // Proposta/Negociação
            ['proposta_negociacao', 'Cimento anual', 'Grupo Comercial Benguela', 35_000_000, 70, '2026-09-30'],
            ['proposta_negociacao', 'Perfis aço projeto habitacional', 'Metalúrgica Kilamba Lda', 18_500_000, 65, '2026-10-15'],
            // Aprovação
            ['aprovacao', 'Aprovação óleo alimentar', 'Distribuidora do Huambo', 9_600_000, 80, '2026-09-25'],
            ['aprovacao', 'Aprovação tijolos', 'Alimares de Benguela', 5_200_000, 75, '2026-09-20'],
            // Bilhete
            ['bilhete', 'Bilhete geradores', 'Energias do Namibe', 15_000_000, 85, '2026-09-15'],
            // Resultado
            ['resultado', 'Resultado final contrato', 'SIL – Sociedade Importadora', 12_000_000, 90, '2026-11-25'],
            // Cliente Aprovado
            ['cliente_aprovado', 'Óleo alimentar Q4', 'Distribuidora do Huambo', 9_600_000, 100, '2026-09-20'],
            ['cliente_aprovado', 'Tijolos obra Benguela', 'Alimares de Benguela', 5_200_000, 100, '2026-09-18'],
            // Cliente Não Aprovado
            ['cliente_nao_aprovado', 'Fornecimento geradores', 'Energias do Namibe', 15_000_000, 0, '2026-09-01'],
        ];

        foreach ($rows as [$stage, $titulo, $empresa, $valor, $probabilidade, $fecho]) {
            $isAprovado = $stage === OpportunityStage::CLIENTE_APROVADO->value;
            $isNaoAprovado = $stage === OpportunityStage::CLIENTE_NAO_APROVADO->value;

            Opportunity::updateOrCreate(
                ['titulo' => $titulo],
                [
                    'nome' => $empresa,
                    'empresa' => $empresa,
                    'valor_estimado' => $valor,
                    'probabilidade' => $probabilidade,
                    'data_fecho' => $fecho,
                    'fase_atual' => $stage,
                    'sector' => 'Distribuição',
                    'localizacao' => 'Luanda',
                    'data_aprovacao' => $isAprovado ? '2026-10-01 10:00:00' : null,
                    'motivo_nao_aprovacao' => $isNaoAprovado ? 'preço concorrência' : null,
                    'ultima_actividade_em' => now()->subDays(3),
                    'ultimo_contacto_em' => now()->subDays(5),
                ],
            );
        }
    }
}
