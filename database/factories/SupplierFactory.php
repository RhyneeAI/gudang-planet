<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'       => fake()->company(),
            'address'    => fake()->address(),
            'phone'      => fake()->phoneNumber(),
            'created_by' => User::factory(),
            'company_id' => Company::factory(),
        ];
    }
}