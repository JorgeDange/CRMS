<?php

namespace Database\Seeders;

use App\Enums\AlertType;
use App\Models\Alert;
use App\Models\Conciliaction;
use App\Models\Contract;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Database\Seeder;

class AlertSeeder extends Seeder
{
    /**
     * Dados de demonstração de Alertas (Sprint 10).
     */
    public function run(): void
    {
        $user = User::query()->first();

        if (! $user) {
            return;
        }

        $rows = [
            [
                'tipo' => AlertType::OPORTUNIDADE->value,
                'entidade' => 'opportunities',
                'entidade_id' => Opportunity::query()->first()?->id,
                'mensagem' => 'Oportunidade "Renovação contrato 2026" está parada há 7 dias na fase Qualificação.',
                'prazo' => now()->addDays(3),
                'lido' => false,
                'resolvido' => false,
            ],
            [
                'tipo' => AlertType::CLIENTE->value,
                'entidade' => 'opportunities',
                'entidade_id' => Opportunity::query()->skip(1)->first()?->id,
                'mensagem' => 'Cliente "SIL – Sociedade Importadora" sem contacto há 60 dias.',
                'prazo' => now()->addDays(7),
                'lido' => false,
                'resolvido' => false,
            ],
            [
                'tipo' => AlertType::CONTRATO->value,
                'entidade' => 'contracts',
                'entidade_id' => Contract::query()->first()?->id,
                'mensagem' => 'Contrato CT-2026-014 vence em 30 dias (2026-11-15).',
                'prazo' => now()->addDays(30),
                'lido' => false,
                'resolvido' => false,
            ],
            [
                'tipo' => AlertType::LEAD_INATIVO->value,
                'entidade' => 'opportunities',
                'entidade_id' => Opportunity::query()->skip(2)->first()?->id,
                'mensagem' => 'Lead "Expansão armazém" parado há 7 dias em Qualificação.',
                'prazo' => now()->addDays(2),
                'lido' => true,
                'resolvido' => false,
            ],
            [
                'tipo' => AlertType::CONCILIACAO->value,
                'entidade' => 'conciliactions',
                'entidade_id' => Conciliaction::query()->first()?->id,
                'mensagem' => 'Conciliação de "Metalúrgica Kilamba Lda" discordante — diferença de Kz 150.000.',
                'prazo' => now()->addDays(5),
                'lido' => false,
                'resolvido' => true,
            ],
        ];

        foreach ($rows as $row) {
            if (! $row['entidade_id']) {
                continue;
            }

            Alert::query()->updateOrCreate(
                ['entidade' => $row['entidade'], 'entidade_id' => $row['entidade_id'], 'tipo' => $row['tipo']],
                [
                    'user_id' => User::query()->first()?->id,
                    'mensagem' => $row['mensagem'],
                    'prazo' => $row['prazo'],
                    'lido' => $row['lido'],
                    'resolvido' => $row['resolvido'],
                    'resolvido_em' => $row['resolvido'] ? now()->subDays(rand(1, 5)) : null,
                ],
            );
        }
    }
}
