<?php

namespace Database\Factories;

use App\Models\AcademicTerm;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CourseOffering> */
class CourseOfferingFactory extends Factory
{
    protected $model = CourseOffering::class;

    public function definition(): array
    {
        return ['course_id' => Course::factory(), 'academic_term_id' => AcademicTerm::factory(), 'instructor_id' => User::factory(), 'section' => fake()->unique()->bothify('SEC-##'), 'schedule' => 'MWF 09:00-10:00', 'room' => fake()->bothify('ROOM-##'), 'capacity' => fake()->numberBetween(20, 40), 'status' => 'ACTIVE'];
    }
}