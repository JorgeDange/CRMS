<?php

namespace Database\Factories;

use App\Enums\ConciliacaoEstado;
use App\Enums\ContratoEstado;
use App\Enums\OpportunityStage;
use Illuminate\Database\Eloquent\Factories\Factory;

class OpportunityFactory extends Factory
{
    public function definition(): array
    {
        $stage = $this->faker->randomElement(OpportunityStage::cases());

        return [
            'titulo' => $this->faker->sentence(3),
            'nome' => $this->faker->company(),
            'empresa' => $this->faker->company(),
            'telefone' => $this->faker->numerify('+244 9## ### ###'),
            'email' => $this->faker->safeEmail(),
            'nif' => $this->faker->numerify('54#########'),
            'codigo' => $this->faker->boolean(70)
                ? 'CLI'.str_pad((string) $this->faker->numberBetween(1, 999), 3, '0', STR_PAD_LEFT)
                : null,
            'tem_compras' => $this->faker->boolean(60),
            'assinatura_director' => $this->faker->boolean(40),
            'enviado_entregue' => $this->faker->boolean(50),
            'conciliacao_estado' => $this->faker->randomElement(ConciliacaoEstado::cases()),
            'contrato_estado' => $this->faker->randomElement(ContratoEstado::cases()),
            'sector' => $this->faker->randomElement(['Distribuição', 'Indústria', 'Construção', 'Agricultura', 'Serviços']),
            'localizacao' => $this->faker->randomElement(['Luanda', 'Benguela', 'Huíla', 'Namibe', 'Huambo']),
            'origem' => $this->faker->randomElement(['Recomendação', 'Feira', 'Website', 'Chamada fria']),
            'interesse' => $this->faker->sentence(8),
            'valor_estimado' => $this->faker->numberBetween(500_000, 50_000_000),
            'probabilidade' => $this->defaultProbability($stage),
            'data_fecho' => $this->faker->dateTimeBetween('+7 days', '+180 days'),
            'fase_atual' => $stage,
            'motivo_nao_aprovacao' => $stage === OpportunityStage::CLIENTE_NAO_APROVADO ? $this->faker->sentence(5) : null,
            'data_aprovacao' => $stage === OpportunityStage::CLIENTE_APROVADO ? $this->faker->dateTimeBetween('-20 days') : null,
            'ultima_actividade_em' => $this->faker->dateTimeBetween('-14 days'),
            'ultimo_contacto_em' => $this->faker->dateTimeBetween('-14 days'),
        ];
    }

    private function defaultProbability(OpportunityStage $stage): int
    {
        return match ($stage) {
            OpportunityStage::LEAD => 20,
            OpportunityStage::QUALIFICACAO => 30,
            OpportunityStage::VISITA => 40,
            OpportunityStage::PROPOSTA_NEGOCIACAO => 50,
            OpportunityStage::APROVACAO => 60,
            OpportunityStage::BILHETE => 70,
            OpportunityStage::RESULTADO => 80,
            OpportunityStage::CLIENTE_APROVADO => 100,
            OpportunityStage::CLIENTE_NAO_APROVADO => 0,
        };
    }
}
