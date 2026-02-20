<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StudentResourceLivewireTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->actingAs($user);
    }

    public function test_can_render_the_students_list_page(): void
    {
        $student = Student::factory()->create();

        Livewire::test(\App\Filament\Resources\StudentResource\Pages\ListStudents::class)
            ->assertStatus(200)
            ->assertSee($student->name);
    }

    public function test_can_render_the_create_student_page(): void
    {
        Livewire::test(\App\Filament\Resources\StudentResource\Pages\CreateStudent::class)
            ->assertStatus(200)
            ->assertSee(trans('students.sections.basic_info'));
    }
}
