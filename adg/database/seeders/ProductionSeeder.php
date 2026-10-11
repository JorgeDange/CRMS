<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder de PRODUÇÃO — NÃO cria dados de demonstração.
 *
 * Uso no VPS:
 *   php artisan migrate --force
 *   php artisan db:seed --class=ProductionSeeder --force
 *
 * A password do utilizador vem de ADMIN_PASSWORD no .env (obrigatória).
 */
class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('ADMIN_PASSWORD');

        if (empty($password)) {
            $this->command->error('ADMIN_PASSWORD não está definido no .env — abortado.');

            return;
        }

        User::query()->updateOrCreate(
            ['email' => 'adailson@otimize.ao'],
            [
                'name' => 'Adailson Nogueira',
                'username' => 'adailson',
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ],
        );

        // Definições por omissão (alertas, agenda, email de destino).
        $this->call(SettingSeeder::class);

        // Email real de destino para alertas (o SettingSeeder traz placeholder).
        if ($email = env('ALERTS_EMAIL')) {
            Setting::set('alertas.email_destino', $email);
        }

        $this->command->info('Produção semeada: 1 utilizador + definições. Sem dados demo.');
    }
}
