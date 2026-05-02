<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'       => fake()->unique()->word(),
            'company_id' => Company::factory(),
        ];
    }
}