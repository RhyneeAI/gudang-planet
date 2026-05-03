<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\CustomerType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'             => fake()->name(),
            'address'          => fake()->address(),
            'phone'            => fake()->phoneNumber(),
            'customer_type_id' => CustomerType::factory(),
            'created_by'       => User::factory(),
            'company_id'       => Company::factory(),
        ];
    }
}
