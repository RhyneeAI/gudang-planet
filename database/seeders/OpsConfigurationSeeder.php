<?php

namespace Database\Seeders;

use App\Models\OpsConfiguration;
use App\Services\Operational\OpsOperationalConfigService;
use App\Services\SubCompanyService;
use Illuminate\Database\Seeder;

class OpsConfigurationSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            SubCompanyService::KEY_MAX_SUB_COMPANIES_PER_MANDOR => '10',
            OpsOperationalConfigService::KEY_INCOME_STORE_BACKDATE_DAYS => '3',
            OpsOperationalConfigService::KEY_INCOME_EDIT_DAYS_AFTER_CREATE => '3',
            OpsOperationalConfigService::KEY_EXPENSE_STORE_BACKDATE_DAYS => '1',
            OpsOperationalConfigService::KEY_EXPENSE_EDIT_DAYS_AFTER_CREATE => '1',
        ];

        foreach ($defaults as $key => $value) {
            OpsConfiguration::withoutGlobalScopes()->updateOrCreate(
                [
                    'company_id' => 1,
                    'key' => $key,
                ],
                [
                    'value' => $value,
                ]
            );
        }
    }
}
