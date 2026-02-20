<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Observation;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user
        $user = User::factory()->create([
            'name' => 'Test Teacher',
            'email' => 'test@example.com',
        ]);

        // Create students with varied data
        // 3 students with high grades (advanced)
        Student::factory()
            ->count(3)
            ->withHighGrades()
            ->create()
            ->each(function (Student $student) use ($user) {
                // Add positive observations
                Observation::factory()
                    ->count(fake()->numberBetween(2, 5))
                    ->positive()
                    ->create([
                        'student_id' => $student->id,
                        'user_id' => $user->id,
                    ]);
            });

        // 4 students with intermediate grades (intermediate)
        Student::factory()
            ->count(4)
            ->create()
            ->each(function (Student $student) use ($user) {
                // Create intermediate grades for each term
                Grade::factory()->intermediate()->term(1)->create(['student_id' => $student->id]);
                Grade::factory()->intermediate()->term(2)->create(['student_id' => $student->id]);
                Grade::factory()->intermediate()->term(3)->create(['student_id' => $student->id]);

                // Add mixed observations
                Observation::factory()
                    ->count(fake()->numberBetween(1, 3))
                    ->create([
                        'student_id' => $student->id,
                        'user_id' => $user->id,
                    ]);
            });

        // 3 students with low grades (basic)
        Student::factory()
            ->count(3)
            ->withLowGrades()
            ->create()
            ->each(function (Student $student) use ($user) {
                // Add concerning observations
                Observation::factory()
                    ->count(fake()->numberBetween(1, 4))
                    ->concerning()
                    ->create([
                        'student_id' => $student->id,
                        'user_id' => $user->id,
                    ]);
            });

        // Create 1 inactive student
        Student::factory()
            ->inactive()
            ->create();
    }
}
