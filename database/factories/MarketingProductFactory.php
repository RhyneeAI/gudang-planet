<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\MarketingProduct;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MarketingProductFactory extends Factory
{
    protected $model = MarketingProduct::class;

    public function definition(): array
    {
        return [
            'marketing_price' => fake()->randomFloat(2, 5000, 100000),
            'product_id'      => Product::factory(),
            'marketing_id'    => User::factory()->marketing(),
            'created_by'      => User::factory(),
            'company_id'      => Company::factory(),
        ];
    }
}
