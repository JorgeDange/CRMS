<?php

namespace Database\Seeders;

use App\Models\Commercial;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Produtos de demonstração.
     */
    public function run(): void
    {
        $comercial = Commercial::query()->first();

        $produtos = [
            ['Sistema Solar Fotovoltaico 10kW', 'SOL-10KW-001', 'SOL-10KW', 1200000],
            ['Sistema Solar Fotovoltaico 20kW', 'SOL-20KW-001', 'SOL-20KW', 2200000],
            ['Bateria de Lítio 10kWh', 'BAT-LIT-10KWH', 'BAT-10KWH', 450000],
            ['Bateria de Lítio 20kWh', 'BAT-LIT-20KWH', 'BAT-20KWH', 850000],
            ['Inversor Híbrido 5kW', 'INV-HIB-5KW', 'INV-5KW', 320000],
            ['Inversor Híbrido 10kW', 'INV-HIB-10KW', 'INV-10KW', 580000],
            ['Estrutura de Fixação Telhado', 'EST-TEL-001', 'EST-TEL', 85000],
            ['Estrutura de Fixação Solo', 'EST-SOL-001', 'EST-SOL', 120000],
            ['Cabo Solar 4mm² (100m)', 'CAB-SOL-4MM', 'CAB-4MM', 25000],
            ['Conector MC4 Par', 'CON-MC4-PAR', 'CON-MC4', 1500],
        ];

        foreach ($produtos as [$nome, $numeroSerie, $codigo, $valor]) {
            Product::updateOrCreate(
                ['codigo' => $codigo],
                [
                    'nome' => $nome,
                    'numero_serie' => $numeroSerie,
                    'valor' => $valor,
                    'commercial_id' => $comercial?->id,
                    'activo' => true,
                ],
            );
        }
    }
}
