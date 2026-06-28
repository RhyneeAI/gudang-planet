<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'OWNER',
                'phone' => '12345678',
                'email' => 'owner_ps@ps.com',
                'password' => 'owner_ps',
                'role' => Role::OWNER,
            ],
            [
                'name' => 'SuperAdmin ps',
                'phone' => '081572438561',
                'email' => 'superadmin_ps@ps.com',
                'password' => 'superadmin_ps123',
                'role' => Role::SUPERADMIN,
            ],
            [
                'name' => 'Admin ps',
                'phone' => '0812345678',
                'email' => 'admin_ps@ps.com',
                'password' => 'admin_ps',
                'role' => Role::ADMIN,
            ],
            [
                'name' => 'Kepala Mandor ps',
                'phone' => '08123456789',
                'email' => 'kepala_mandor_ps@ps.com',
                'password' => 'kepala_mandor_ps',
                'role' => Role::GUDANG,
            ],
            [
                'name' => 'Marketing ps',
                'phone' => '081234567890',
                'email' => 'marketing_ps@ps.com',
                'password' => 'marketing_ps',
                'role' => Role::MARKETING,
            ],
            [
                'name' => 'GUDANG ps',
                'phone' => '0812345678901',
                'email' => 'gudang_ps@ps.com',
                'password' => 'gudang_ps',
                'role' => Role::GUDANG,
            ],
            [
                'name' => 'HRD ps',
                'phone' => '081976567086',
                'email' => 'hrd_ps@ps.com',
                'password' => 'hrd_ps',
                'role' => Role::HRD,
            ],
        ];

        foreach ($users as $user) {
            $attributes = [
                'name' => $user['name'],
                'email' => $user['email'],
                'address' => 'Jl. Demo No. 1, Jakarta',
                'password' => Hash::make($user['password']),
                'role' => $user['role'],
                'company_id' => 1,
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
