<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class AbsJabatanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->jobTitle(),
            'daily_rate' => fake()->randomFloat(0, 100000, 500000),
            'company_id' => Company::factory(),
        ];
    }
}
