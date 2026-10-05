<?php

namespace Tests\Feature;

use App\Models\AcademicTerm;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicApiTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        $role = Role::firstOrCreate(['name' => $role]);
        return User::factory()->create(['role_id' => $role->id]);
    }

    public function test_student_collection_supports_search_filter_sort_and_pagination(): void
    {
        $staff = $this->user('STAFF');
        $program = Program::factory()->create();
        Student::factory(3)->create(['program_id' => $program->id, 'last_name' => 'Dela Cruz']);
        Student::factory(2)->create(['program_id' => $program->id, 'last_name' => 'Santos']);

        $this->actingAs($staff, 'sanctum')->getJson('/api/v1/students?search=dela&program_id='.$program->id.'&sort=last_name&per_page=2')
            ->assertOk()->assertJsonPath('data.current_page', 1)->assertJsonPath('data.per_page', 2)->assertJsonPath('data.total', 3);
    }

    public function test_student_can_only_view_own_student_record(): void
    {
        $role = Role::firstOrCreate(['name' => 'STUDENT']);
        $user = User::factory()->create(['role_id' => $role->id]);
        $own = Student::factory()->create(['user_id' => $user->id]);
        $other = Student::factory()->create();

        $this->actingAs($user, 'sanctum')->getJson('/api/v1/students/'.$own->id)->assertOk();
        $this->actingAs($user, 'sanctum')->getJson('/api/v1/students/'.$other->id)->assertForbidden();
    }

    public function test_duplicate_enrollment_returns_conflict(): void
    {
        $staff = $this->user('STAFF');
        $student = Student::factory()->create();
        $offering = CourseOffering::factory()->create(['instructor_id' => $this->user('INSTRUCTOR')->id]);
        Enrollment::factory()->create(['student_id' => $student->id, 'course_offering_id' => $offering->id]);

        $this->actingAs($staff, 'sanctum')->postJson('/api/v1/enrollments', ['student_id' => $student->id, 'course_offering_id' => $offering->id])
            ->assertStatus(409)->assertJsonPath('success', false);
    }

    public function test_instructor_can_only_modify_assigned_offering_grades(): void
    {
        $instructor = $this->user('INSTRUCTOR');
        $otherInstructor = $this->user('INSTRUCTOR');
        $enrollment = Enrollment::factory()->create(['course_offering_id' => CourseOffering::factory()->create(['instructor_id' => $otherInstructor->id])->id]);

        $this->actingAs($instructor, 'sanctum')->postJson('/api/v1/grades', ['enrollment_id' => $enrollment->id, 'final_grade' => 88])
            ->assertForbidden();
    }
}