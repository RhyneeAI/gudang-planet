<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Services\Absence\AbsEmployeeProfileService;
use Illuminate\Database\Seeder;

class AbsEmployeeProfileSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(AbsEmployeeProfileService::class);

        Company::withoutGlobalScopes()->each(function (Company $company) use ($service) {
            $service->ensureAllUsersHaveProfiles($company->id);
        });
    }
}
