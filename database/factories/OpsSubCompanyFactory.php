<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\OpsSubCompany;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OpsSubCompanyFactory extends Factory
{
    protected $model = OpsSubCompany::class;

    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'name' => fake()->company(),
            'code' => fake()->unique()->regexify('[A-Z]{3}[0-9]{2}'),
            'address' => fake()->address(),
            'is_active' => true,
            'mandor_id' => User::factory()->mandor(),
            'company_id' => Company::factory(),
            'created_by' => User::factory()->admin(),
        ];
    }
}
