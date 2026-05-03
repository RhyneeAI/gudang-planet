<?php

namespace Database\Factories;

use App\Enums\PaymentType;
use App\Enums\TransactionStatus;
use App\Models\Company;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SalesTransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'transaction_code'   => 'SO-' . fake()->unique()->numerify('####'),
            'transaction_date'   => fake()->dateTime(),
            'discount'           => fake()->randomFloat(2, 0, 10000),
            'total'              => fake()->randomFloat(2, 10000, 100000),
            'paid'               => fake()->randomFloat(2, 0, 100000),
            'payment_type'       => fake()->randomElement([PaymentType::CASH->value, PaymentType::TRANSFER->value, PaymentType::QRIS->value]),
            'transaction_status' => fake()->randomElement(TransactionStatus::cases())->value,
            'customer_id'        => Customer::factory(),
            'created_by'         => User::factory(),
            'company_id'         => Company::factory(),
        ];
    }

    public function paid(): static
    {
        return $this->state(['transaction_status' => TransactionStatus::PAID->value]);
    }

    public function unpaid(): static
    {
        return $this->state(['transaction_status' => TransactionStatus::UNPAID->value]);
    }

    public function pending(): static
    {
        return $this->state(['transaction_status' => TransactionStatus::PENDING->value]);
    }

    public function process(): static
    {
        return $this->state(['transaction_status' => TransactionStatus::PROCESS->value]);
    }

    public function cancel(): static
    {
        return $this->state(['transaction_status' => TransactionStatus::CANCEL->value]);
    }

    public function cash(): static
    {
        return $this->state(['payment_type' => PaymentType::CASH->value]);
    }

    public function transfer(): static
    {
        return $this->state(['payment_type' => PaymentType::TRANSFER->value]);
    }

    public function qris(): static
    {
        return $this->state(['payment_type' => PaymentType::QRIS->value]);
    }
}
