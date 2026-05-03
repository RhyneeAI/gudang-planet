<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $unitsData = [
            ['name' => 'Pcs', 'created_by' => 1, 'company_id' => 1],
            ['name' => 'Kg', 'created_by' => 1, 'company_id' => 1],
            ['name' => 'Liter', 'created_by' => 1, 'company_id' => 1],
            ['name' => 'Dus', 'created_by' => 1, 'company_id' => 1],
            ['name' => 'Pack', 'created_by' => 1, 'company_id' => 1],
        ];

        $units = [];
        foreach ($unitsData as $data) {
            $units[] = [
                'uuid' => (string) Str::uuid(),
                'name' => $data['name'],
                'created_by' => $data['created_by'],
                'company_id' => $data['company_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Unit::insert($units);
    }
}