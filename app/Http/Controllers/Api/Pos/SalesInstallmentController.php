<?php

namespace App\Http\Controllers\Api\Pos;

use App\Enums\PosInstallmentStatus;
use App\Enums\PosTransactionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\InstallmentPaymentRequest;
use App\Http\Resources\Pos\SalesInstallmentPlanResource;
use App\Models\PosSalesInstallmentPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SalesInstallmentController extends Controller
{
    public function index(Request $request)
    {
        $plans = PosSalesInstallmentPlan::with(['customer', 'salesTransaction', 'salesTransaction.createdBy'])
            ->when($request->status, fn($q, $status) =>
                $q->where('status', $status)
            )
            ->when($request->search, fn($q, $search) =>
                $q->whereHas('customer', fn($c) =>
                    $c->where('name', 'like', "%{$search}%")
                )
            )
            ->when($request->created_by_uuid, fn($q, $uuid) =>
                $q->whereHas('salesTransaction.createdBy', fn($u) =>
                    $u->where('uuid', $uuid)
                )
            )
            ->orderBy('created_at', 'DESC')
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => __('installments.list'),
            'data'    => SalesInstallmentPlanResource::collection($plans),
        ]);
    }

    public function show(PosSalesInstallmentPlan $salesInstallmentPlan)
    {
        return response()->json([
            'success' => true,
            'message' => __('installments.detail'),
            'data'    => new SalesInstallmentPlanResource(
                $salesInstallmentPlan->load(['customer', 'salesTransaction', 'salesTransaction.createdBy', 'payments'])
            ),
        ]);
    }

    public function pay(InstallmentPaymentRequest $request, PosSalesInstallmentPlan $salesInstallmentPlan)
    {
        // Cek apakah sudah lunas
        if ($salesInstallmentPlan->status === PosInstallmentStatus::COMPLETED) {
            return response()->json([
                'success' => false,
                'message' => __('installments.already_completed'),
                'code'    => 422,
            ], 422);
        }

        $remaining   = $salesInstallmentPlan->remainingAmount();
        $paidAmount  = (float) $request->paid_amount;
        $isOverdue   = $salesInstallmentPlan->isOverdue();

        // Jika overdue (tenor habis), wajib bayar penuh sisa
        if ($isOverdue && $paidAmount < $remaining) {
            return response()->json([
                'success' => false,
                'message' => __('installments.must_pay_full', ['remaining' => $remaining]),
                'code'    => 422,
            ], 422);
        }

        // Tidak boleh bayar melebihi sisa
        if ($paidAmount > $remaining) {
            return response()->json([
                'success' => false,
                'message' => __('installments.overpaid', ['remaining' => $remaining]),
                'code'    => 422,
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Hitung installment_number berikutnya
            $nextNumber = $salesInstallmentPlan->payments()->count() + 1;

            // Buat payment record
            $salesInstallmentPlan->payments()->create([
                'ulid'                      => Str::ulid(),
                'sales_installment_plan_id' => $salesInstallmentPlan->id,
                'installment_number'        => $nextNumber,
                'paid_amount'               => $paidAmount,
                'paid_date'                 => now()->toDateString(),
                'notes'                     => $request->notes,
                'created_by'                => $request->user()->id,
                'company_id'                => $request->user()->company_id,
            ]);

            // Update paid_amount di plan
            $newPaidAmount = $salesInstallmentPlan->paid_amount + $paidAmount;
            $isCompleted   = $newPaidAmount >= $salesInstallmentPlan->total_amount;

            // Tentukan status baru
            $newStatus = match(true) {
                $isCompleted => PosInstallmentStatus::COMPLETED,
                $salesInstallmentPlan->isOverdue() => PosInstallmentStatus::OVERDUE,
                default => PosInstallmentStatus::ACTIVE,
            };

            $salesInstallmentPlan->update([
                'paid_amount' => $newPaidAmount,
                'status'      => $newStatus,
            ]);

            // Jika lunas, update SalesTransaction status
            if ($isCompleted) {
                $salesInstallmentPlan->salesTransaction->update([
                    'transaction_status' => PosTransactionStatus::PAID,
                    'paid'               => $salesInstallmentPlan->total_amount,
                ]);
            } else {
                $salesInstallmentPlan->salesTransaction->update([
                    'transaction_status' => PosTransactionStatus::PROCESS,
                    'paid'               => $newPaidAmount
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $isCompleted
                    ? __('installments.completed')
                    : __('installments.payment_recorded'),
                'data'    => new SalesInstallmentPlanResource(
                    $salesInstallmentPlan->fresh()->load(['customer', 'salesTransaction', 'salesTransaction.createdBy', 'payments'])
                ),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
