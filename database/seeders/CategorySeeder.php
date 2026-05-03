<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Makanan',
                'created_by' => 1,
                'company_id' => 1,
            ],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Minuman',
                'created_by' => 1,
                'company_id' => 1,
            ],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Snack',
                'created_by' => 1,
                'company_id' => 1,
            ],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Rokok',
                'created_by' => 1,
                'company_id' => 1,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}