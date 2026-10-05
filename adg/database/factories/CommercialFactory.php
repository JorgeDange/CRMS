<?php

namespace Database\Factories;

use App\Models\Commercial;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommercialFactory extends Factory
{
    protected $model = Commercial::class;

    public function definition(): array
    {
        return [
            'nome' => $this->faker->unique()->company(),
            'descricao' => $this->faker->optional(0.7)->sentence(8),
            'activo' => true,
        ];
    }
}
