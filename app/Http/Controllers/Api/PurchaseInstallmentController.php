<?php

namespace App\Http\Controllers\Api;

use App\Enums\InstallmentStatus;
use App\Enums\TransactionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\InstallmentPaymentRequest;
use App\Http\Resources\PurchaseInstallmentPlanResource;
use App\Models\PurchaseInstallmentPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseInstallmentController extends Controller
{
    public function index(Request $request)
    {
        $plans = PurchaseInstallmentPlan::with(['supplier', 'purchaseTransaction'])
            ->when($request->status, fn($q, $status) =>
                $q->where('status', $status)
            )
            ->when($request->search, fn($q, $search) =>
                $q->whereHas('supplier', fn($s) =>
                    $s->where('name', 'like', "%{$search}%")
                )
            )
            ->orderBy('created_at', 'DESC')
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => __('installments.list'),
            'data'    => PurchaseInstallmentPlanResource::collection($plans),
        ]);
    }

    public function show(PurchaseInstallmentPlan $purchaseInstallmentPlan)
    {
        return response()->json([
            'success' => true,
            'message' => __('installments.detail'),
            'data'    => new PurchaseInstallmentPlanResource(
                $purchaseInstallmentPlan->load(['supplier', 'purchaseTransaction', 'payments'])
            ),
        ]);
    }

    public function pay(InstallmentPaymentRequest $request, PurchaseInstallmentPlan $purchaseInstallmentPlan)
    {
        if ($purchaseInstallmentPlan->status === InstallmentStatus::COMPLETED) {
            return response()->json([
                'success' => false,
                'message' => __('installments.already_completed'),
                'code'    => 422,
            ], 422);
        }

        $remaining  = $purchaseInstallmentPlan->remainingAmount();
        $paidAmount = (float) $request->paid_amount;

        if ($paidAmount > $remaining) {
            return response()->json([
                'success' => false,
                'message' => __('installments.overpaid', ['remaining' => $remaining]),
                'code'    => 422,
            ], 422);
        }

        DB::beginTransaction();

        try {
            $nextNumber = $purchaseInstallmentPlan->payments()->count() + 1;

            $purchaseInstallmentPlan->payments()->create([
                'ulid'                         => Str::ulid(),
                'purchase_installment_plan_id' => $purchaseInstallmentPlan->id,
                'installment_number'           => $nextNumber,
                'paid_amount'                  => $paidAmount,
                'paid_date'                    => now()->toDateString(),
                'notes'                        => $request->notes,
                'created_by'                   => $request->user()->id,
                'company_id'                   => $request->user()->company_id,
            ]);

            $newPaidAmount = $purchaseInstallmentPlan->paid_amount + $paidAmount;
            $isCompleted   = $newPaidAmount >= $purchaseInstallmentPlan->total_amount;

            $newStatus = match(true) {
                $isCompleted => InstallmentStatus::COMPLETED,
                default => InstallmentStatus::ACTIVE,
            };

            $purchaseInstallmentPlan->update([
                'paid_amount' => $newPaidAmount,
                'status'      => $newStatus,
            ]);

            if ($isCompleted) {
                $purchaseInstallmentPlan->purchaseTransaction->update([
                    'transaction_status' => TransactionStatus::PAID,
                    'paid'               => $purchaseInstallmentPlan->total_amount,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $isCompleted
                    ? __('installments.completed')
                    : __('installments.payment_recorded'),
                'data'    => new PurchaseInstallmentPlanResource(
                    $purchaseInstallmentPlan->fresh()->load(['supplier', 'purchaseTransaction', 'payments'])
                ),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}