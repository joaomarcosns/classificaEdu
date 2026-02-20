<?php

namespace Database\Factories;

use App\Enums\EvaluationPeriodName;
use App\Enums\GradeLevel;
use App\Models\EvaluationPeriod;
use App\Models\Grade;
use App\Models\Student;
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
            'grade_level' => fake()->randomElement(array_map(fn (GradeLevel $level) => $level->value, GradeLevel::cases())),
            'class_name' => fake()->randomElement(['Class A', 'Class B', 'Class C']),
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
        return $this->afterCreating(function (Student $student) {
            $periods = $this->getOrCreateDefaultPeriods();

            foreach ($periods as $period) {
                Grade::factory()->advanced()->create([
                    'student_id' => $student->id,
                    'period_id' => $period->id,
                ]);
            }
        });
    }

    /**
     * Indicate that the student has low grades (0.0-5.9).
     */
    public function withLowGrades(): static
    {
        return $this->afterCreating(function (Student $student) {
            $periods = $this->getOrCreateDefaultPeriods();

            foreach ($periods as $period) {
                Grade::factory()->basic()->create([
                    'student_id' => $student->id,
                    'period_id' => $period->id,
                ]);
            }
        });
    }

    /**
     * Get or create the default 3 periods for the current year.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, EvaluationPeriod>
     */
    protected function getOrCreateDefaultPeriods(): \Illuminate\Database\Eloquent\Collection
    {
        $year = (string) now()->year;

        $existing = EvaluationPeriod::query()
            ->where('academic_year', $year)
            ->orderBy('order')
            ->limit(3)
            ->get();

        if ($existing->count() >= 3) {
            return $existing;
        }

        $created = collect();
        $names = EvaluationPeriodName::cases();
        for ($i = 1; $i <= 3; $i++) {
            $name = $names[$i - 1] ?? EvaluationPeriodName::FirstTerm;

            $created->push(EvaluationPeriod::firstOrCreate(
                ['academic_year' => $year, 'order' => $i],
                ['name' => $name->value, 'is_active' => true]
            ));
        }

        return $created;
    }
}
