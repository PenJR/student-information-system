<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\RoleName;
use App\Models\AcademicTerm;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Program;
use App\Models\Role;
use App\Models\Student;
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
        $roles = collect(RoleName::cases())->mapWithKeys(fn (RoleName $role) => [
            $role->value => Role::updateOrCreate(['name' => $role->value], ['description' => $role->name . ' system role']),
        ]);

        $admin = User::factory()->create(['name' => 'Demo Administrator', 'email' => 'admin@example.test', 'role_id' => $roles[RoleName::ADMINISTRATOR->value]->id]);
        User::factory()->create(['name' => 'Demo Staff', 'email' => 'staff@example.test', 'role_id' => $roles[RoleName::STAFF->value]->id]);
        $instructors = User::factory(2)->create(['role_id' => $roles[RoleName::INSTRUCTOR->value]->id]);
        $studentUsers = User::factory()->create(['name' => 'Demo Student', 'email' => 'student@example.test', 'role_id' => $roles[RoleName::STUDENT->value]->id]);
        $studentUsers = collect([$studentUsers])->merge(
            User::factory(99)->create(['role_id' => $roles[RoleName::STUDENT->value]->id])
        );

        $programs = Program::factory(3)->create();
        $courses = Course::factory(20)->create();
        $terms = collect([
            ['academic_year' => '2025-2026', 'term' => 'FIRST', 'start_date' => '2025-08-01', 'end_date' => '2025-12-20', 'status' => 'ACTIVE'],
            ['academic_year' => '2025-2026', 'term' => 'SECOND', 'start_date' => '2026-01-05', 'end_date' => '2026-05-30', 'status' => 'ACTIVE'],
        ])->map(fn (array $term) => AcademicTerm::updateOrCreate(['academic_year' => $term['academic_year'], 'term' => $term['term']], $term));
        $students = $studentUsers->values()->map(function (User $user, int $index) use ($programs): Student {
            return Student::factory()->create([
                'user_id' => $user->id,
                'first_name' => str($user->name)->before(' ')->toString(),
                'email' => $user->email,
                'student_number' => sprintf('2026-%05d', $index + 1),
                'program_id' => $programs->random()->id,
            ]);
        });

        $offerings = collect();
        foreach ($courses as $course) {
            $term = $terms->random();
            $offerings->push(CourseOffering::factory()->create(['course_id' => $course->id, 'academic_term_id' => $term->id, 'instructor_id' => $instructors->random()->id, 'section' => 'A']));
        }

        $pairs = collect();
        while ($pairs->count() < 200) {
            $student = $students->random();
            $offering = $offerings->random();
            $key = "$student->id-$offering->id";
            if ($pairs->has($key)) continue;
            $pairs->put($key, Enrollment::create(['student_id' => $student->id, 'course_offering_id' => $offering->id, 'enrollment_date' => now()->toDateString(), 'status' => 'ENROLLED']));
        }

        $pairs->take(100)->each(fn (Enrollment $enrollment) => Grade::create(['enrollment_id' => $enrollment->id, 'midterm_grade' => fake()->randomFloat(2, 60, 100), 'final_grade' => fake()->randomFloat(2, 60, 100)]));
    }
}
