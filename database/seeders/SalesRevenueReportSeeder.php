<?php

namespace Database\Seeders;

use App\Enums\PaymentType;
use App\Enums\Role;
use App\Models\Category;
use App\Models\Company;
use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\Product;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\Concerns\SeedsSalesTransactions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SalesRevenueReportSeeder extends Seeder
{
    use SeedsSalesTransactions;

    public function run(): void
    {
        $company = Company::firstOrCreate(
            ['code' => 'TSJ-001'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Toko Sejahtera',
                'address' => 'Jl. Omzet Demo, Jakarta',
            ]
        );

        $owner = User::updateOrCreate(
            ['phone' => '081990000011'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Owner Sejahtera',
                'email' => 'owner@sejahtera.test',
                'password' => Hash::make('owner_tsj'),
                'role' => Role::OWNER,
                'company_id' => $company->id,
            ]
        );

        $kasir = User::updateOrCreate(
            ['phone' => '081990000012'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Kasir Sejahtera',
                'email' => 'kasir@sejahtera.test',
                'password' => Hash::make('password'),
                'role' => Role::MARKETING,
                'company_id' => $company->id,
            ]
        );

        $category = Category::firstOrCreate(
            ['name' => 'Kebutuhan Rumah', 'company_id' => $company->id],
            ['uuid' => (string) Str::uuid(), 'created_by' => $owner->id]
        );

        $unit = Unit::firstOrCreate(
            ['name' => 'Pcs', 'company_id' => $company->id],
            ['uuid' => (string) Str::uuid(), 'created_by' => $owner->id]
        );

        $customerType = CustomerType::firstOrCreate(
            ['type' => 'Regular', 'company_id' => $company->id],
            ['uuid' => (string) Str::uuid(), 'discount' => 0, 'created_by' => $owner->id]
        );

        $budi = Customer::firstOrCreate(
            ['name' => 'Budi', 'company_id' => $company->id],
            ['uuid' => (string) Str::uuid(), 'customer_type_id' => $customerType->id, 'created_by' => $owner->id]
        );

        $products = collect([
            ['name' => 'Indomie Goreng', 'code' => 'TSJ-001', 'base' => 2500, 'sell' => 4000, 'stock' => 500],
            ['name' => 'Teh Botol Sosro', 'code' => 'TSJ-002', 'base' => 4000, 'sell' => 6000, 'stock' => 300],
            ['name' => 'Sabun Lifebuoy', 'code' => 'TSJ-003', 'base' => 3000, 'sell' => 5000, 'stock' => 200],
        ])->mapWithKeys(function (array $data) use ($company, $owner, $category, $unit) {
            $product = Product::updateOrCreate(
                ['code' => $data['code'], 'company_id' => $company->id],
                [
                    'uuid' => (string) Str::uuid(),
                    'name' => $data['name'],
                    'base_price' => $data['base'],
                    'sales_price' => $data['sell'],
                    'stock' => $data['stock'],
                    'is_active' => true,
                    'category_id' => $category->id,
                    'unit_id' => $unit->id,
                    'created_by' => $owner->id,
                ]
            );

            return [$data['code'] => $product];
        });

        $this->seedSalesTransactions($company, $products->all(), [
            [
                'date' => '2026-01-05',
                'customer_id' => $budi->id,
                'payment' => PaymentType::CASH,
                'discount' => 0,
                'created_by' => $kasir->id,
                'items' => [
                    ['code' => 'TSJ-001', 'qty' => 10, 'price' => 4000],
                    ['code' => 'TSJ-002', 'qty' => 5, 'price' => 6000],
                ],
            ],
            [
                'date' => '2026-02-14',
                'customer_id' => null,
                'payment' => PaymentType::TRANSFER,
                'discount' => 5000,
                'created_by' => $kasir->id,
                'items' => [
                    ['code' => 'TSJ-001', 'qty' => 20, 'price' => 4000],
                    ['code' => 'TSJ-003', 'qty' => 6, 'price' => 5000],
                ],
            ],
            [
                'date' => '2026-03-07',
                'customer_id' => $budi->id,
                'payment' => PaymentType::QRIS,
                'discount' => 0,
                'created_by' => $kasir->id,
                'items' => [
                    ['code' => 'TSJ-002', 'qty' => 8, 'price' => 6000],
                ],
            ],
        ], 'SO-REV');
    }
}
