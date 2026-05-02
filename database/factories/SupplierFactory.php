<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'       => fake()->company(),
            'address'    => fake()->address(),
            'phone'      => fake()->phoneNumber(),
            'company_id' => Company::factory(),
        ];
    }
}