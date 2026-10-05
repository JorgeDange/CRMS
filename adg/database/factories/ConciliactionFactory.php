<?php

namespace Database\Factories;

use App\Enums\ConciliactionClassificacao;
use App\Models\Commercial;
use App\Models\Conciliaction;
use App\Models\Opportunity;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConciliactionFactory extends Factory
{
    protected $model = Conciliaction::class;

    public function definition(): array
    {
        return [
            'opportunity_id' => Opportunity::inRandomOrder()->first()?->id ?? Opportunity::factory(),
            'commercial_id' => Commercial::inRandomOrder()->first()?->id ?? Commercial::factory(),
            'data' => $this->faker->dateTimeBetween('-30 days', '+30 days'),
            'valor' => $this->faker->randomFloat(2, 10000, 1000000),
            'classificacao' => $this->faker->randomElement(ConciliactionClassificacao::cases()),
            'observacoes' => $this->faker->optional(0.7)->sentence(8),
            'resolvido' => $this->faker->boolean(30),
            'resolvido_em' => null,
        ];
    }

    public function concordante(): static
    {
        return $this->state(fn (array $attributes): array => [
            'classificacao' => ConciliactionClassificacao::CONCORDANTE,
        ]);
    }

    public function discordante(): static
    {
        return $this->state(fn (array $attributes): array => [
            'classificacao' => ConciliactionClassificacao::DISCORDANTE,
        ]);
    }

    public function pendente(): static
    {
        return $this->state(fn (array $attributes): array => [
            'classificacao' => ConciliactionClassificacao::PENDENTE,
        ]);
    }

    public function resolvida(): static
    {
        return $this->state(fn (array $attributes): array => [
            'resolvido' => true,
            'resolvido_em' => $this->faker->dateTimeBetween('-7 days', 'now'),
        ]);
    }
}
