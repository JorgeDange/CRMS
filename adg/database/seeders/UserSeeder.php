<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Utilizador único do sistema (PROMPT_MASTER §1).
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'adailson@otimize.ao'],
            [
                'name' => 'Adailson Nogueira',
                'username' => 'adailson',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
    }
}
