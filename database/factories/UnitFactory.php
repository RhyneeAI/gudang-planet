<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnitFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'       => fake()->unique()->randomElement(['Pcs', 'Kg', 'Liter', 'Box', 'Lusin', 'Karton', 'Gram']),
            'company_id' => Company::factory(),
        ];
    }
}