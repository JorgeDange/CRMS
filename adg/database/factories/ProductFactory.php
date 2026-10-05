<?php

namespace Database\Factories;

use App\Models\Commercial;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'nome' => $this->faker->unique()->words(3, true),
            'numero_serie' => $this->faker->optional(0.5)->bothify('SN-####-????'),
            'codigo' => $this->faker->unique()->bothify('PRD-####'),
            'valor' => $this->faker->randomFloat(2, 1000, 500000),
            'commercial_id' => Commercial::inRandomOrder()->first()?->id
                ?? Commercial::factory(),
            'activo' => true,
        ];
    }
}
