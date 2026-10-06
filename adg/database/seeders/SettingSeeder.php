<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Definições por omissão (PROMPT_MASTER §8.10/§10 + resposta.md §10/§11).
     */
    public function run(): void
    {
        Setting::set('alertas.oportunidades_dias', '7');
        Setting::set('alertas.clientes_dias', '60');
        Setting::set('alertas.contratos_dias', '90');
        Setting::set('alertas.leads_inativos_dias', '7');

        // Agenda lembrete diário (resposta.md §11)
        Setting::set('agenda.lembrete_hora', '20:00');
        // Email para alertas/agenda — substituir pelo email real do Adailson
        Setting::set('alertas.email_destino', 'adailson@otimize.co.ao');
    }
}
