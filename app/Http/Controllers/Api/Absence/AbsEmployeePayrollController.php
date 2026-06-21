<?php

namespace App\Http\Controllers\Api\Absence;

use App\Http\Controllers\Controller;
use App\Http\Resources\Absence\AbsPayrollPeriodResource;
use App\Models\AbsPayrollPeriod;
use App\Services\Absence\AbsPayrollService;
use Illuminate\Http\Request;

class AbsEmployeePayrollController extends Controller
{
    public function __construct(
        protected AbsPayrollService $payrollService,
    ) {}

    public function index(Request $request)
    {
        $preview = $this->payrollService->currentPeriodPreview($request->user());

        return response()->json([
            'success' => true,
            'message' => __('absence.payroll.preview'),
            'data' => $preview,
        ]);
    }

    public function show(AbsPayrollPeriod $absPayrollPeriod)
    {
        if ($absPayrollPeriod->user_id !== request()->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You don\'t have permission to access this resource.',
                'code' => 403,
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => __('absence.payroll.detail'),
            'data' => new AbsPayrollPeriodResource(
                $absPayrollPeriod->load(['deductions', 'bonuses', 'user'])
            ),
        ]);
    }

    public function slip(AbsPayrollPeriod $absPayrollPeriod)
    {
        if ($absPayrollPeriod->user_id !== request()->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You don\'t have permission to access this resource.',
                'code' => 403,
            ], 403);
        }

        if (!$absPayrollPeriod->isFinal()) {
            return response()->json([
                'success' => false,
                'message' => __('absence.payroll.slip_not_available'),
                'code' => 422,
            ], 422);
        }

        $pdf = $this->payrollService->generateSlipPdf($absPayrollPeriod);
        $filename = sprintf(
            'slip-gaji-%s-%02d-%d.pdf',
            $absPayrollPeriod->user->uuid,
            $absPayrollPeriod->period_month,
            $absPayrollPeriod->period_year
        );

        return $pdf->download($filename);
    }
}
