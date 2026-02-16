<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'registration_number' => fake()->unique()->numerify('MAT####'),
            'date_of_birth' => fake()->dateTimeBetween('-15 years', '-5 years'),
            'grade_level' => fake()->randomElement(['1º ano', '2º ano', '3º ano', '4º ano', '5º ano', '6º ano', '7º ano', '8º ano', '9º ano']),
            'class_name' => fake()->randomElement(['Turma A', 'Turma B', 'Turma C']),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the student is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the student has high grades (8.0-10.0).
     */
    public function withHighGrades(): static
    {
        return $this->afterCreating(function (\App\Models\Student $student) {
            \App\Models\Grade::factory()
                ->count(3)
                ->avancado()
                ->sequence(
                    ['evaluation_period' => 'trimestre_1'],
                    ['evaluation_period' => 'trimestre_2'],
                    ['evaluation_period' => 'trimestre_3'],
                )
                ->create(['student_id' => $student->id]);
        });
    }

    /**
     * Indicate that the student has low grades (0.0-5.9).
     */
    public function withLowGrades(): static
    {
        return $this->afterCreating(function (\App\Models\Student $student) {
            \App\Models\Grade::factory()
                ->count(3)
                ->basico()
                ->sequence(
                    ['evaluation_period' => 'trimestre_1'],
                    ['evaluation_period' => 'trimestre_2'],
                    ['evaluation_period' => 'trimestre_3'],
                )
                ->create(['student_id' => $student->id]);
        });
    }
}
