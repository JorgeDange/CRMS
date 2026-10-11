<?php

namespace Database\Factories;

use App\Enums\NoteType;
use App\Models\Note;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Note>
 */
class NoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'opportunity_id' => Opportunity::factory(),
            'user_id' => User::factory(),
            'titulo' => $this->faker->sentence(4),
            'conteudo' => $this->faker->paragraph(),
            'tipo' => NoteType::INFORMACAO,
            'fixada' => false,
        ];
    }

    public function fixada(): static
    {
        return $this->state(fn () => ['fixada' => true]);
    }
}
