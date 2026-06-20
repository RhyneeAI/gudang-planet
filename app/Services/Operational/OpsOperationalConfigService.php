<?php

namespace App\Services\Operational;

use App\Models\OpsConfiguration;
use Carbon\Carbon;

class OpsOperationalConfigService
{
    public const KEY_INCOME_STORE_BACKDATE_DAYS = 'income_store_backdate_days';
    public const KEY_INCOME_EDIT_DAYS_AFTER_CREATE = 'income_edit_days_after_create';
    public const KEY_EXPENSE_STORE_BACKDATE_DAYS = 'expense_store_backdate_days';
    public const KEY_EXPENSE_EDIT_DAYS_AFTER_CREATE = 'expense_edit_days_after_create';

    public function incomeStoreBackdateDays(int $companyId): int
    {
        return $this->resolveInt(
            $companyId,
            self::KEY_INCOME_STORE_BACKDATE_DAYS,
            'operational.income_store_backdate_days',
            3
        );
    }

    public function incomeEditDaysAfterCreate(int $companyId): int
    {
        return $this->resolveInt(
            $companyId,
            self::KEY_INCOME_EDIT_DAYS_AFTER_CREATE,
            'operational.income_edit_days_after_create',
            3
        );
    }

    public function expenseStoreBackdateDays(int $companyId): int
    {
        return $this->resolveInt(
            $companyId,
            self::KEY_EXPENSE_STORE_BACKDATE_DAYS,
            'operational.expense_store_backdate_days',
            1
        );
    }

    public function expenseEditDaysAfterCreate(int $companyId): int
    {
        return $this->resolveInt(
            $companyId,
            self::KEY_EXPENSE_EDIT_DAYS_AFTER_CREATE,
            'operational.expense_edit_days_after_create',
            1
        );
    }

    public function storeBackdateDays(int $companyId, string $type): int
    {
        return $type === 'income'
            ? $this->incomeStoreBackdateDays($companyId)
            : $this->expenseStoreBackdateDays($companyId);
    }

    public function editDaysAfterCreate(int $companyId, string $type): int
    {
        return $type === 'income'
            ? $this->incomeEditDaysAfterCreate($companyId)
            : $this->expenseEditDaysAfterCreate($companyId);
    }

    public function isStoreDateAllowed(int $companyId, string $type, string $date): bool
    {
        $days = $this->storeBackdateDays($companyId, $type);
        $requestDate = Carbon::parse($date)->startOfDay();
        $minDate = now()->startOfDay()->subDays($days);

        return $requestDate->greaterThanOrEqualTo($minDate);
    }

    public function isEditAllowed(int $companyId, string $type, Carbon $createdAt): bool
    {
        $days = $this->editDaysAfterCreate($companyId, $type);
        $deadline = $createdAt->copy()->startOfDay()->addDays($days);

        return now()->startOfDay()->lessThanOrEqualTo($deadline);
    }

    protected function resolveInt(int $companyId, string $key, string $configKey, int $default): int
    {
        $configured = OpsConfiguration::where('company_id', $companyId)
            ->where('key', $key)
            ->value('value');

        if ($configured !== null && $configured !== '') {
            return max(0, (int) $configured);
        }

        return max(0, (int) config($configKey, $default));
    }
}
