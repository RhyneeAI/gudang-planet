<?php

namespace Database\Seeders;

use App\Models\AbsShift;
use App\Models\Company;
use Illuminate\Database\Seeder;

class AbsShiftSeeder extends Seeder
{
    public function run(): void
    {
        Company::withoutGlobalScopes()->each(function (Company $company) {
            AbsShift::firstOrCreate(
                [
                    'company_id' => $company->id,
                    'name' => 'Shift Reguler',
                ],
                [
                    'start_time' => '08:00:00',
                    'end_time' => '17:00:00',
                ]
            );
        });
    }
}
