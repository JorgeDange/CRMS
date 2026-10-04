<?php

namespace Database\Factories;

use App\Enums\ActivityStatus;
use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Activity>
 */
class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition(): array
    {
        $tipo = $this->faker->randomElement(ActivityType::cases());
        $data = $this->faker->dateTimeBetween('-30 days', '+30 days');

        return [
            'opportunity_id' => Opportunity::inRandomOrder()->first()?->id
                ?? Opportunity::factory(),
            'user_id' => User::inRandomOrder()->first()?->id
                ?? User::factory(),
            'tipo' => $tipo,
            'descricao' => $this->faker->sentence(6),
            'resultado' => $this->faker->optional(0.6)->sentence(8),
            'data' => $data,
            'hora' => $this->faker->optional(0.8)->time('H:i'),
            'proxima_accao' => $this->faker->optional(0.5)->sentence(5),
            'proxima_accao_prazo' => $this->faker->optional(0.5)->dateTimeBetween('now', '+60 days'),
            'status' => $this->faker->randomElement(ActivityStatus::cases()),
        ];
    }

    public function pendente(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ActivityStatus::PENDENTE,
        ]);
    }

    public function concluida(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ActivityStatus::CONCLUIDA,
        ]);
    }

    public function cancelada(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ActivityStatus::CANCELADA,
        ]);
    }

    public function comProximaAcao(): static
    {
        return $this->state(fn (array $attributes): array => [
            'proxima_accao' => $this->faker->sentence(5),
            'proxima_accao_prazo' => $this->faker->dateTimeBetween('now', '+30 days'),
        ]);
    }

    public function vencida(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ActivityStatus::PENDENTE,
            'proxima_accao' => 'Ação vencida para teste',
            'proxima_accao_prazo' => $this->faker->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }

    public function proxima(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ActivityStatus::PENDENTE,
            'proxima_accao' => 'Ação próxima para teste',
            'proxima_accao_prazo' => $this->faker->dateTimeBetween('now', '+7 days'),
        ]);
    }
}
