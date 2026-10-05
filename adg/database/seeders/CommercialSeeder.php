<?php

namespace Database\Seeders;

use App\Models\Commercial;
use Illuminate\Database\Seeder;

class CommercialSeeder extends Seeder
{
    /**
     * Seeders iniciais: Comercial 01, Comercial 02, Comercial 03 (resposta.md §8.2).
     */
    public function run(): void
    {
        $comerciais = [
            ['Comercial 01', 'Carteira de clientes da região Norte'],
            ['Comercial 02', 'Carteira de clientes da região Centro'],
            ['Comercial 03', 'Carteira de clientes da região Sul'],
        ];

        foreach ($comerciais as [$nome, $descricao]) {
            Commercial::updateOrCreate(
                ['nome' => $nome],
                ['descricao' => $descricao, 'activo' => true],
            );
        }
    }
}
