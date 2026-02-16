<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentClassification>
 */
class StudentClassificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $average = fake()->randomFloat(2, 0, 10);
        $level = $this->getClassificationLevel($average);

        return [
            'student_id' => Student::factory(),
            'classification_level' => $level,
            'overall_average' => $average,
            'evaluation_period' => 'current',
            'classification_date' => now(),
            'metadata' => [
                'trimestre_1' => fake()->randomFloat(2, 0, 10),
                'trimestre_2' => fake()->randomFloat(2, 0, 10),
                'trimestre_3' => fake()->randomFloat(2, 0, 10),
            ],
        ];
    }

    /**
     * Get classification level based on average.
     */
    protected function getClassificationLevel(float $average): string
    {
        if ($average < 6.0) {
            return 'basico';
        }

        if ($average < 8.0) {
            return 'intermediario';
        }

        return 'avancado';
    }
}
