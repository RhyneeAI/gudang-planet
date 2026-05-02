<?php

namespace Database\Factories;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'       => fake()->name(),
            'username'   => fake()->unique()->userName(),
            'email'      => fake()->unique()->safeEmail(),
            'password'   => Hash::make('password'),
            'role'       => fake()->randomElement(Role::cases())->value,
            'address'    => fake()->address(),
            'phone'      => fake()->phoneNumber(),
            'company_id' => CompanyFactory::new(),
        ];
    }

    // State methods — mudah buat user dengan role tertentu
    public function owner(): static
    {
        return $this->state(['role' => Role::OWNER]);
    }

    public function superAdmin(): static
    {
        return $this->state(['role' => Role::SUPERADMIN]);
    }

    public function marketing(): static
    {
        return $this->state(['role' => Role::MARKETING]);
    }
}