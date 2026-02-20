<?php

namespace Database\Factories;

use App\Enums\AssessmentType;
use App\Models\EvaluationPeriod;
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
            'period_id' => EvaluationPeriod::factory(),
            'assessment_type' => fake()->optional()->randomElement(
                array_map(fn (AssessmentType $type) => $type->value, AssessmentType::cases())
            ),
            'value' => fake()->randomFloat(2, 0, 10),
            'evaluation_date' => fake()->dateTimeThisYear(),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the grade is in the basic range (0.0-5.9).
     */
    public function basic(): static
    {
        return $this->state(fn (array $attributes) => [
            'value' => fake()->randomFloat(2, 0, 5.9),
        ]);
    }

    /**
     * Indicate that the grade is in the intermediate range (6.0-7.9).
     */
    public function intermediate(): static
    {
        return $this->state(fn (array $attributes) => [
            'value' => fake()->randomFloat(2, 6.0, 7.9),
        ]);
    }

    /**
     * Indicate that the grade is in the advanced range (8.0-10.0).
     */
    public function advanced(): static
    {
        return $this->state(fn (array $attributes) => [
            'value' => fake()->randomFloat(2, 8.0, 10.0),
        ]);
    }

    /**
     * Set the grade for a specific term order within the current year.
     */
    public function term(int $number): static
    {
        return $this->state(fn (array $attributes) => [
            'period_id' => EvaluationPeriod::query()
                ->where('academic_year', now()->year)
                ->where('order', $number)
                ->value('id')
                ?? EvaluationPeriod::factory()->create(['order' => $number, 'academic_year' => now()->year])->id,
        ]);
    }
}
