<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OpsNotificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => fake()->randomElement(['EXPENSE_INSUFFICIENT_BALANCE', 'INCOME_PENDING', 'EXPENSE_CREATED']),
            'title' => fake()->sentence(),
            'message' => fake()->paragraph(),
            'notifiable_type' => null,
            'notifiable_id' => null,
            'is_read' => false,
            'read_at' => null,
            'company_id' => Company::factory(),
            'created_at' => now(),
        ];
    }
}
