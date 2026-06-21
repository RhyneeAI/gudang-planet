<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OpsExpenseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'amount' => fake()->randomFloat(2, 50000, 500000),
            'date' => fake()->dateTimeBetween('-1 days', 'now'),
            'payment_method' => fake()->randomElement(['CASH', 'TRANSFER']),
            'proof_files' => ['proofs/test.jpg'],
            'note' => fake()->optional()->sentence(),
            'expense_type' => 'INTERNAL',
            'mandor_id' => null,
            'sub_company_id' => null,
            'transfer_income_id' => null,
            'created_by' => User::factory()->admin(),
            'company_id' => Company::factory(),
        ];
    }
}
