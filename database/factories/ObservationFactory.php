<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Observation>
 */
class ObservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'user_id' => User::factory(),
            'observation_date' => fake()->dateTimeThisMonth(),
            'category' => fake()->randomElement([
                'comportamento',
                'participacao',
                'cooperacao',
                'responsabilidade',
                'interacao_social',
            ]),
            'sentiment' => fake()->randomElement(['positivo', 'neutro', 'preocupante']),
            'description' => fake()->paragraph(),
            'is_private' => false,
        ];
    }

    /**
     * Indicate that the observation is positive.
     */
    public function positive(): static
    {
        return $this->state(fn (array $attributes) => [
            'sentiment' => 'positivo',
        ]);
    }

    /**
     * Indicate that the observation is concerning.
     */
    public function concerning(): static
    {
        return $this->state(fn (array $attributes) => [
            'sentiment' => 'preocupante',
        ]);
    }

    /**
     * Set the observation category.
     */
    public function category(string $category): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => $category,
        ]);
    }
}
