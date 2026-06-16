<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customerTypeId = CustomerType::where('company_id', 1)->value('id');

        foreach (['Andi', 'Budi', 'Siti'] as $index => $name) {
            Customer::firstOrCreate(
                ['name' => $name, 'company_id' => 1],
                [
                    'uuid' => (string) Str::uuid(),
                    'phone' => '08188000000' . $index,
                    'address' => 'Jl. Pelanggan, Jakarta',
                    'customer_type_id' => $customerTypeId,
                    'created_by' => 1,
                ]
            );
        }
    }
}
