<?php

namespace App\Http\Controllers\Api\Operational;

use App\Models\OpsExpense;
use App\Models\OpsIncome;
use Illuminate\Http\JsonResponse;

trait UsesOperationalTransactionWindow
{
    protected function validateOperationalStoreDate(string $type, string $date): ?JsonResponse
    {
        $companyId = (int) request()->user()->company_id;
        $days = $this->operationalConfig->storeBackdateDays($companyId, $type);

        if ($this->operationalConfig->isStoreDateAllowed($companyId, $type, $date)) {
            return null;
        }

        $messageKey = $type === 'income'
            ? 'operational.incomes.store_window_expired'
            : 'operational.expenses.store_window_expired';

        return response()->json([
            'success' => false,
            'message' => __($messageKey, ['days' => $days]),
            'code' => 422,
        ], 422);
    }

    protected function validateOperationalEditWindow(string $type, OpsIncome|OpsExpense $record): ?JsonResponse
    {
        $companyId = (int) request()->user()->company_id;
        $days = $this->operationalConfig->editDaysAfterCreate($companyId, $type);

        if ($this->operationalConfig->isEditAllowed($companyId, $type, $record->created_at)) {
            return null;
        }

        $messageKey = $type === 'income'
            ? 'operational.incomes.edit_window_expired'
            : 'operational.expenses.edit_window_expired';

        return response()->json([
            'success' => false,
            'message' => __($messageKey, ['days' => $days]),
            'code' => 422,
        ], 422);
    }
}
