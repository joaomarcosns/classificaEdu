<?php

namespace Database\Factories;

use App\Enums\ObservationCategory;
use App\Enums\ObservationSentiment;
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
            'category' => fake()->randomElement(
                array_map(fn (ObservationCategory $category) => $category->value, ObservationCategory::cases())
            ),
            'sentiment' => fake()->randomElement(
                array_map(fn (ObservationSentiment $sentiment) => $sentiment->value, ObservationSentiment::cases())
            ),
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
            'sentiment' => ObservationSentiment::Positive->value,
        ]);
    }

    /**
     * Indicate that the observation is concerning.
     */
    public function concerning(): static
    {
        return $this->state(fn (array $attributes) => [
            'sentiment' => ObservationSentiment::Concerning->value,
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
