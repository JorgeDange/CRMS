<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Definições por omissão (PROMPT_MASTER §8.10/§10).
     */
    public function run(): void
    {
        Setting::set('alertas.oportunidades_dias', '7');
        Setting::set('alertas.clientes_dias', '30');
        Setting::set('alertas.contratos_dias', '90');
        // alertas.leads_inativos_dias — a definir pelo cliente (§19)
    }
}
