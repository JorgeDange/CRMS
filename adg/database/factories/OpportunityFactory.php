<?php

namespace Database\Factories;

use App\Enums\OpportunityStage;
use App\Models\Opportunity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Opportunity>
 */
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
            'segmento' => $this->faker->randomElement(['Distribuição', 'Indústria', 'Construção', 'Agricultura', 'Serviços']),
            'localizacao' => $this->faker->randomElement(['Luanda', 'Benguela', 'Huíla', 'Namibe', 'Huambo']),
            'origem' => $this->faker->randomElement(['Recomendação', 'Feira', 'Website', 'Chamada fria']),
            'interesse' => $this->faker->sentence(8),
            'valor_estimado' => $this->faker->numberBetween(500_000, 50_000_000),
            'probabilidade' => $this->defaultProbability($stage),
            'data_fecho' => $this->faker->dateTimeBetween('+7 days', '+180 days'),
            'fase_atual' => $stage,
            'motivo_nao_aprovacao' => $stage === OpportunityStage::NAO_APROVADO ? $this->faker->sentence(5) : null,
            'data_aprovacao' => $stage === OpportunityStage::APROVADO ? $this->faker->dateTimeBetween('-20 days') : null,
            'ultima_actividade_em' => $this->faker->dateTimeBetween('-14 days'),
            'ultimo_contacto_em' => $this->faker->dateTimeBetween('-14 days'),
        ];
    }

    private function defaultProbability(OpportunityStage $stage): int
    {
        return match ($stage) {
            OpportunityStage::LEAD => 20,
            OpportunityStage::VISITA => 40,
            OpportunityStage::APROVACAO => 60,
            OpportunityStage::APROVADO => 100,
            OpportunityStage::NAO_APROVADO => 0,
        };
    }
}
