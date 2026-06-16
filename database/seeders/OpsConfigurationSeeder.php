<?php

namespace Database\Seeders;

use App\Models\OpsConfiguration;
use App\Services\Operational\OpsSubCompanyService;
use Illuminate\Database\Seeder;

class OpsConfigurationSeeder extends Seeder
{
    public function run(): void
    {
        OpsConfiguration::withoutGlobalScopes()->updateOrCreate(
            [
                'company_id' => 1,
                'key' => OpsSubCompanyService::KEY_MAX_SUB_COMPANIES_PER_MANDOR,
            ],
            [
                'value' => '10',
            ]
        );
    }
}
