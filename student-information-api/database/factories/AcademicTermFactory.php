<?php

namespace Database\Factories;

use App\Models\AcademicTerm;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AcademicTerm> */
class AcademicTermFactory extends Factory
{
    protected $model = AcademicTerm::class;

    public function definition(): array
    {
        $year = fake()->numberBetween(2024, 2026);
        return ['academic_year' => "$year-" . ($year + 1), 'term' => fake()->randomElement(['FIRST', 'SECOND', 'SUMMER']), 'start_date' => now()->subMonths(3)->toDateString(), 'end_date' => now()->addMonths(3)->toDateString(), 'status' => 'ACTIVE'];
    }
}