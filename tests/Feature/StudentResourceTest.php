<?php

namespace Tests\Feature;

use App\Models\EvaluationPeriod;
use App\Models\Grade;
use App\Models\Observation;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StudentResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->actingAs($user);
    }

    public function test_can_list_students(): void
    {
        $student = Student::factory()->create();

        Livewire::test(\App\Filament\Resources\StudentResource\Pages\ListStudents::class)
            ->assertStatus(200)
            ->assertSee($student->name);
    }

    public function test_can_create_a_student(): void
    {
        $data = Student::factory()->make()->toArray();

        Livewire::test(\App\Filament\Resources\StudentResource\Pages\CreateStudent::class)
            ->set('data', $data)
            ->call('create')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('students', ['name' => $data['name']]);
    }

    public function test_can_update_a_student(): void
    {
        $student = Student::factory()->create();
        $newName = 'New Name';

        Livewire::test(\App\Filament\Resources\StudentResource\Pages\EditStudent::class, [
            'record' => $student->getKey(),
        ])
            ->set('data.name', $newName)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => $newName,
        ]);
    }

    public function test_can_delete_a_student(): void
    {
        $student = Student::factory()->create();

        Livewire::test(\App\Filament\Resources\StudentResource\Pages\EditStudent::class, [
            'record' => $student->getKey(),
        ])
            ->callAction('delete')
            ->assertHasNoErrors();

        $this->assertSoftDeleted($student);
    }

    public function test_can_create_a_grade_for_a_student(): void
    {
        $student = Student::factory()->create();
        $period = EvaluationPeriod::factory()->create();
        $gradeData = Grade::factory()->create([
            'student_id' => $student->id,
            'period_id' => $period->id,
        ])->toArray();

        $this->assertDatabaseHas('grades', [
            'student_id' => $student->id,
            'period_id' => $period->id,
            'value' => $gradeData['value'],
        ]);
    }

    public function test_can_create_an_observation_for_a_student(): void
    {
        $student = Student::factory()->create();
        $observationData = Observation::factory()->create([
            'student_id' => $student->id,
        ])->toArray();

        $this->assertDatabaseHas('observations', [
            'student_id' => $student->id,
            'description' => $observationData['description'],
        ]);
    }
}
