<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OpsProductionSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::firstOrCreate(
            ['code' => 'GP001'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Gudang Planet',
                'address' => 'Jl. Raya No. 1, Jakarta',
                'code' => 'GP001',
            ]
        );

        $users = [
            [
                'name' => 'Owner',
                'phone' => '081234567890',
                'email' => 'owner@gudangplanet.com',
                'password' => 'owner123',
                'role' => Role::OWNER,
            ],
            [
                'name' => 'Admin',
                'phone' => '081234567891',
                'email' => 'admin@gudangplanet.com',
                'password' => 'admin123',
                'role' => Role::ADMIN,
            ],
        ];

        foreach ($users as $user) {
            $attributes = [
                'name' => $user['name'],
                'email' => $user['email'],
                'address' => 'Jl. Raya No. 1, Jakarta',
                'password' => Hash::make($user['password']),
                'role' => $user['role'],
                'company_id' => $company->id,
                'is_active' => true,
            ];

            $existing = User::where('phone', $user['phone'])->first();

            User::updateOrCreate(
                ['phone' => $user['phone']],
                $existing ? $attributes : array_merge(['uuid' => (string) Str::uuid()], $attributes)
            );
        }
    }
}
