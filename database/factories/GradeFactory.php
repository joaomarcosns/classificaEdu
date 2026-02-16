<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Grade>
 */
class GradeFactory extends Factory
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
            'value' => fake()->randomFloat(2, 0, 10),
            'evaluation_date' => fake()->dateTimeThisYear(),
            'evaluation_period' => fake()->randomElement(['trimestre_1', 'trimestre_2', 'trimestre_3', 'final']),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the grade is in the básico range (0.0-5.9).
     */
    public function basico(): static
    {
        return $this->state(fn (array $attributes) => [
            'value' => fake()->randomFloat(2, 0, 5.9),
        ]);
    }

    /**
     * Indicate that the grade is in the intermediário range (6.0-7.9).
     */
    public function intermediario(): static
    {
        return $this->state(fn (array $attributes) => [
            'value' => fake()->randomFloat(2, 6.0, 7.9),
        ]);
    }

    /**
     * Indicate that the grade is in the avançado range (8.0-10.0).
     */
    public function avancado(): static
    {
        return $this->state(fn (array $attributes) => [
            'value' => fake()->randomFloat(2, 8.0, 10.0),
        ]);
    }

    /**
     * Set the grade for a specific trimester.
     */
    public function trimestre(int $number): static
    {
        return $this->state(fn (array $attributes) => [
            'evaluation_period' => "trimestre_{$number}",
        ]);
    }
}
