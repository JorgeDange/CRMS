<?php

namespace Database\Factories;

use App\Models\Opportunity;
use App\Models\User;
use App\Models\VisitReport;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VisitReport>
 */
class VisitReportFactory extends Factory
{
    public function definition(): array
    {
        return [
            'opportunity_id' => Opportunity::factory(),
            'user_id' => User::factory(),
            'data_visita' => $this->faker->dateTimeBetween('-30 days'),
            'objectivo' => $this->faker->sentence(6),
            'pessoas_presentes' => $this->faker->name().', '.$this->faker->name(),
            'assuntos_tratados' => $this->faker->paragraph(),
            'resultado' => $this->faker->sentence(8),
            'proxima_accao' => $this->faker->sentence(5),
        ];
    }
}
