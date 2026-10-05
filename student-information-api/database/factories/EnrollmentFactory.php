<?php

namespace Database\Factories;

use App\Models\CourseOffering;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Enrollment> */
class EnrollmentFactory extends Factory
{
    protected $model = Enrollment::class;

    public function definition(): array
    {
        return ['student_id' => Student::factory(), 'course_offering_id' => CourseOffering::factory(), 'enrollment_date' => now()->toDateString(), 'status' => 'ENROLLED'];
    }
}