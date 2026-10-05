<?php

namespace Database\Seeders;

use App\Enums\ConciliactionClassificacao;
use App\Models\Commercial;
use App\Models\Conciliaction;
use App\Models\Opportunity;
use Illuminate\Database\Seeder;

class ConciliactionSeeder extends Seeder
{
    /**
     * Dados de demonstração de Conciliações (Sprint 6).
     * Baseado na tabela de payment.html do protótipo.
     */
    public function run(): void
    {
        $rows = [
            ['SIL – Sociedade Importadora', 'concordante', 500000, 'Conciliação automática - OK', true],
            ['Metalúrgica Kilamba Lda', 'discordante', 1280000, 'Diferença de Kz 150.000 em cheque devolvido', false],
            ['Importadora Kinaxixi', 'concordante', 300000, 'Conciliação manual confirmada', true],
            ['Grupo Comercial Benguela', 'pendente', 450000, 'Aguardando extrato bancário', false],
            ['Distribuidora do Huambo', 'discordante', 1950000, 'Valor divergente - contacto com cliente', false],
            ['Maona Energy Lda', 'concordante', 800000, 'Conciliação de cartão de crédito OK', true],
            ['Alimares de Benguela', 'concordante', 200000, 'Sem pendências', true],
            ['Energias do Namibe', 'pendente', 250000, 'Aguardando comprovativo de pagamento', false],
            ['Construções Alicia Lda', 'discordante', 2450000, 'Múltiplas facturas em aberto', false],
            ['Agropecuária do Planalto', 'pendente', 320000, 'Verificar duplicação de lançamento', false],
        ];

        foreach ($rows as [$empresa, $classificacao, $valor, $observacoes, $resolvido]) {
            $opportunity = Opportunity::query()->firstWhere('nome', $empresa);
            $commercial = Commercial::inRandomOrder()->first();

            if (! $opportunity) {
                continue;
            }

            Conciliaction::query()->updateOrCreate(
                ['opportunity_id' => $opportunity->id, 'data' => now()->subDays(rand(1, 30))->toDateString()],
                [
                    'opportunity_id' => $opportunity->id,
                    'commercial_id' => $commercial?->id,
                    'data' => now()->subDays(rand(1, 30)),
                    'valor' => $valor,
                    'classificacao' => ConciliactionClassificacao::from($classificacao),
                    'observacoes' => $observacoes,
                    'resolvido' => $resolvido,
                    'resolvido_em' => $resolvido ? now()->subDays(rand(1, 5)) : null,
                ],
            );
        }
    }
}
