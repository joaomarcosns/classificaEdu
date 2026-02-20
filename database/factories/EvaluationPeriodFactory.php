<?php

namespace Database\Factories;

use App\Enums\EvaluationPeriodName;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EvaluationPeriod>
 */
class EvaluationPeriodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $order = 1;
        $names = EvaluationPeriodName::cases();
        $name = $names[($order - 1) % count($names)];

        return [
            'academic_year' => (string) fake()->year(),
            'name' => $name->value,
            'order' => $order++,
            'is_active' => true,
        ];
    }
}
