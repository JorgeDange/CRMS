<?php

namespace Database\Factories;

use App\Enums\ContractStatus;
use App\Models\Commercial;
use App\Models\Contract;
use App\Models\Opportunity;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContractFactory extends Factory
{
    protected $model = Contract::class;

    public function definition(): array
    {
        return [
            'opportunity_id' => Opportunity::inRandomOrder()->first()?->id ?? Opportunity::factory(),
            'commercial_id' => Commercial::inRandomOrder()->first()?->id ?? Commercial::factory(),
            'product_id' => Product::inRandomOrder()->first()?->id ?? Product::factory(),
            'numero_contrato' => $this->faker->unique()->bothify('CT-####-???'),
            'tipo' => $this->faker->randomElement(['Fornecimento', 'Serviço', 'Manutenção', 'Aluguer']),
            'data_inicio' => $this->faker->dateTimeBetween('-60 days', '+30 days'),
            'data_fim' => $this->faker->dateTimeBetween('+30 days', '+365 days'),
            'valor' => $this->faker->randomFloat(2, 100000, 20000000),
            'estado' => $this->faker->randomElement(ContractStatus::cases()),
            'documento' => $this->faker->optional(0.5)->bothify('DOC-####'),
            'notas' => $this->faker->optional(0.5)->sentence(8),
        ];
    }

    public function activo(): static
    {
        return $this->state(fn (array $attributes): array => [
            'estado' => ContractStatus::ACTIVO,
        ]);
    }

    public function expirado(): static
    {
        return $this->state(fn (array $attributes): array => [
            'estado' => ContractStatus::EXPIRADO,
            'data_fim' => $this->faker->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }

    public function renovado(): static
    {
        return $this->state(fn (array $attributes): array => [
            'estado' => ContractStatus::RENOVADO,
        ]);
    }

    public function cancelado(): static
    {
        return $this->state(fn (array $attributes): array => [
            'estado' => ContractStatus::CANCELADO,
        ]);
    }
}
