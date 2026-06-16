<?php

namespace Database\Seeders;

use App\Models\CustomerType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CustomerTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['type' => 'Regular', 'discount' => 0],
            ['type' => 'Member', 'discount' => 10],
        ];

        foreach ($types as $type) {
            CustomerType::firstOrCreate(
                ['type' => $type['type'], 'company_id' => 1],
                [
                    'uuid' => (string) Str::uuid(),
                    'discount' => $type['discount'],
                    'created_by' => 1,
                ]
            );
        }
    }
}
