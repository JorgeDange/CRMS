<?php

namespace Database\Factories;

use App\Enums\AlertType;
use App\Models\Alert;
use App\Models\Conciliaction;
use App\Models\Contract;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlertFactory extends Factory
{
    protected $model = Alert::class;

    public function definition(): array
    {
        // AGENDA é criado apenas pelo comando agenda:reminder (sem entidade).
        $tipo = $this->faker->randomElement(array_values(array_filter(AlertType::cases(), fn (AlertType $c) => $c !== AlertType::AGENDA)));

        return [
            'tipo' => $tipo,
            'entidade' => match ($tipo) {
                AlertType::OPORTUNIDADE => 'opportunities',
                AlertType::CLIENTE => 'opportunities',
                AlertType::CONTRATO => 'contracts',
                AlertType::LEAD_INATIVO => 'opportunities',
                AlertType::CONCILIACAO => 'conciliactions',
            },
            'entidade_id' => match ($tipo) {
                AlertType::OPORTUNIDADE => Opportunity::inRandomOrder()->first()?->id ?? Opportunity::factory(),
                AlertType::CLIENTE => Opportunity::inRandomOrder()->first()?->id ?? Opportunity::factory(),
                AlertType::CONTRATO => Contract::inRandomOrder()->first()?->id ?? Contract::factory(),
                AlertType::LEAD_INATIVO => Opportunity::inRandomOrder()->first()?->id ?? Opportunity::factory(),
                AlertType::CONCILIACAO => Conciliaction::inRandomOrder()->first()?->id ?? Conciliaction::factory(),
            },
            'mensagem' => $this->faker->sentence(8),
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'prazo' => $this->faker->optional(0.7)->dateTimeBetween('now', '+30 days'),
            'lido' => $this->faker->boolean(30),
            'resolvido' => false,
            'resolvido_em' => null,
        ];
    }

    public function lido(): static
    {
        return $this->state(fn (array $attributes): array => [
            'lido' => true,
        ]);
    }

    public function resolvido(): static
    {
        return $this->state(fn (array $attributes): array => [
            'resolvido' => true,
            'resolvido_em' => $this->faker->dateTimeBetween('-7 days', 'now'),
        ]);
    }

    public function vencido(): static
    {
        return $this->state(fn (array $attributes): array => [
            'prazo' => $this->faker->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }
}
