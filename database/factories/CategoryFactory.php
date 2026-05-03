<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'       => fake()->unique()->word(),
            'created_by' => User::factory(),
            'company_id' => Company::factory(),
        ];
    }
}