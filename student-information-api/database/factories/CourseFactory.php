<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Course> */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return ['course_code' => fake()->unique()->bothify('CS-###'), 'course_title' => fake()->sentence(3), 'description' => fake()->sentence(), 'units' => fake()->numberBetween(1, 6), 'status' => 'ACTIVE'];
    }
}