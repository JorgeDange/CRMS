<?php

namespace Database\Seeders;

use App\Enums\ContractStatus;
use App\Models\Commercial;
use App\Models\Contract;
use App\Models\Opportunity;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ContractSeeder extends Seeder
{
    /**
     * Dados de demonstração de Contratos (Sprint 7).
     * Baseado na tabela de projects.html do protótipo.
     * 5 primeiros já terminaram (data_fim no passado = entregues),
     * restantes em curso; estados variados para os gráficos/donuts.
     */
    public function run(): void
    {
        $rows = [
            ['SIL – Sociedade Importadora', 'Cimento CP40 50kg', 'OV-2026-014', 'Fornecimento', 'activo', 'Luanda, Talatona', 800, 12000000],
            ['Metalúrgica Kilamba Lda', 'Perfis de aço 12m', 'OV-2026-011', 'Fornecimento', 'activo', 'Luanda, Viana', 150, 8500000],
            ['Importadora Kinaxixi', 'Rio Tinto 25kg', 'OV-2026-018', 'Fornecimento', 'activo', 'Luanda, Kilamba', 1200, 6000000],
            ['Grupo Comercial Benguela', 'Tijolos 6 furos', 'OV-2026-009', 'Fornecimento', 'activo', 'Benguela', 5000, 2500000],
            ['Distribuidora do Huambo', 'Óleo alimentar 5L', 'OV-2026-007', 'Fornecimento', 'activo', 'Huambo', 300, 4500000],
            ['Maona Energy Lda', 'Painéis solares 450W', 'OV-2026-016', 'Fornecimento', 'activo', 'Luanda', 60, 15000000],
            ['Alimares de Benguela', 'Fertilizante NPK 50kg', 'OV-2026-012', 'Fornecimento', 'activo', 'Benguela', 200, 3000000],
            ['Energias do Namibe', 'Cabos eléctricos 100m', 'OV-2026-015', 'Fornecimento', 'expirado', 'Namibe', 80, 7500000],
            ['Construções Alicia Lda', 'Argamassa 25kg', 'OV-2026-013', 'Fornecimento', 'renovado', 'Luanda', 600, 1800000],
            ['Agropecuária do Planalto', 'Ração animal 40kg', 'OV-2026-012', 'Fornecimento', 'cancelado', 'Huambo', 450, 2200000],
        ];

        foreach ($rows as $i => [$empresa, $produtoNome, $numeroContrato, $tipo, $estado, $destino, $qtd, $valor]) {
            $opportunity = Opportunity::query()->firstWhere('nome', $empresa);
            // Catálogo evoluiu (Sprint solar) — usa o nome esperado se existir, senão um produto qualquer
            $product = Product::query()->firstWhere('nome', $produtoNome) ?? Product::inRandomOrder()->first();
            $commercial = Commercial::inRandomOrder()->first();

            if (! $opportunity || ! $product) {
                continue;
            }

            $terminado = $i < 5 || $estado !== 'activo';

            Contract::query()->updateOrCreate(
                ['numero_contrato' => $numeroContrato],
                [
                    'opportunity_id' => $opportunity->id,
                    'commercial_id' => $commercial?->id,
                    'product_id' => $product->id,
                    'tipo' => $tipo,
                    'data_inicio' => ($terminado ? now()->subDays(rand(150, 320)) : now()->subDays(rand(10, 60)))->toDateString(),
                    'data_fim' => ($terminado ? now()->subDays(rand(10, 140)) : now()->addDays(rand(30, 180)))->toDateString(),
                    'valor' => $valor,
                    'estado' => ContractStatus::from($estado),
                    'documento' => 'DOC-'.strtoupper($numeroContrato),
                    'notas' => "Encomenda {$numeroContrato} — {$qtd} unidades para {$destino}",
                ],
            );
        }
    }
}
