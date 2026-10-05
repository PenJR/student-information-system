<?php

namespace Database\Factories;

use App\Models\Program;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Student> */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return ['student_number' => fake()->unique()->numerify('2026-#####'), 'first_name' => fake()->firstName(), 'middle_name' => fake()->firstName(), 'last_name' => fake()->lastName(), 'birth_date' => fake()->date(), 'email' => fake()->unique()->safeEmail(), 'contact_number' => fake()->phoneNumber(), 'address' => fake()->address(), 'program_id' => Program::factory(), 'year_level' => fake()->numberBetween(1, 4), 'status' => 'ACTIVE'];
    }
}