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
            ['cliente_aprovado', 'Renovação material escritório', 'Importadora Kinaxixi', 7_200_000, 100, '2026-09-25'],
            ['cliente_aprovado', 'Fornecimento ração animal', 'Agropecuária do Planalto', 6_800_000, 100, '2026-09-28'],
            ['cliente_aprovado', 'Cabos eléctricos contrato anual', 'Energias do Namibe', 8_200_000, 100, '2026-10-02'],
            ['cliente_aprovado', 'Mobiliário escolar lote 3', 'Construções Alicia Lda', 3_400_000, 100, '2026-10-04'],
            ['cliente_aprovado', 'Cimento obra habitacional', 'Grupo Comercial Benguela', 35_000_000, 100, '2026-10-06'],
            ['cliente_aprovado', 'Painéis solares parque', 'Maona Energy Lda', 22_000_000, 100, '2026-10-07'],
            ['cliente_aprovado', 'Perfis aço torres', 'Metalúrgica Kilamba Lda', 18_500_000, 100, '2026-10-08'],
            ['cliente_aprovado', 'Arroz importado Q4', 'SIL – Sociedade Importadora', 12_000_000, 100, '2026-10-09'],
            // Cliente Não Aprovado
            ['cliente_nao_aprovado', 'Fornecimento geradores', 'Energias do Namibe', 15_000_000, 0, '2026-09-01'],
        ];

        // Campos da Tabela de Clientes (seguir.md §10.2) — so para clientes aprovados:
        // 60% compras, 40% assinatura do director, 50% enviado/entregue,
        // conciliacao 30% concordante / 20% discordante / 50% pendente,
        // contrato 40% SIM / 20% CADUCIDADE / 40% NAO.
        $camposClientes = [
            // [codigo, tem_compras, assinatura_director, enviado_entregue, conciliacao_estado, contrato_estado]
            ['CLI001', true, true, true, 'concordante', 'sim'],
            ['CLI002', true, false, true, 'concordante', 'sim'],
            ['CLI003', false, false, false, 'discordante', 'sim'],
            ['CLI004', false, true, true, 'pendente', 'caducidade'],
            ['CLI005', true, false, false, 'pendente', 'nao'],
            ['CLI006', true, true, false, 'pendente', 'sim'],
            ['CLI007', true, false, true, 'discordante', 'caducidade'],
            ['CLI008', true, true, false, 'concordante', 'nao'],
            ['CLI009', false, false, true, 'pendente', 'nao'],
            ['CLI010', false, false, false, 'pendente', 'nao'],
        ];

        $indiceCliente = 0;

        foreach ($rows as $i => [$stage, $titulo, $empresa, $valor, $probabilidade, $fecho]) {
            $isAprovado = $stage === OpportunityStage::CLIENTE_APROVADO->value;
            $isNaoAprovado = $stage === OpportunityStage::CLIENTE_NAO_APROVADO->value;
            $campos = $isAprovado
                ? ($camposClientes[$indiceCliente++] ?? [null, false, false, false, null, null])
                : [null, false, false, false, null, null];

            Opportunity::updateOrCreate(
                ['titulo' => $titulo],
                [
                    'nome' => $empresa,
                    'empresa' => $empresa,
                    'valor_estimado' => $valor,
                    'probabilidade' => $probabilidade,
                    'data_fecho' => $fecho,
                    'fase_atual' => $stage,
                    // Sectores variados → donut "Segmento" do Painel Executivo
                    'sector' => $i % 2 === 0 ? 'Distribuição' : 'Indústria',
                    'localizacao' => 'Luanda',
                    'data_aprovacao' => $isAprovado ? '2026-10-01 10:00:00' : null,
                    'motivo_nao_aprovacao' => $isNaoAprovado ? 'preço concorrência' : null,
                    'ultima_actividade_em' => now()->subDays(3),
                    'ultimo_contacto_em' => now()->subDays(5),
                    // Tabela de Clientes (Sprint 13) — nulos/defaults nas outras fases
                    'codigo' => $campos[0],
                    'tem_compras' => $campos[1] ?? false,
                    'assinatura_director' => $campos[2] ?? false,
                    'enviado_entregue' => $campos[3] ?? false,
                    'conciliacao_estado' => $campos[4],
                    'contrato_estado' => $campos[5],
                ],
            );
        }
    }
}
