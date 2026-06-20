<?php

namespace App\Http\Controllers\Api\Absence;

use App\Http\Controllers\Controller;
use App\Http\Resources\Absence\AbsAttendanceResource;
use App\Http\Resources\Absence\AbsPayrollPeriodResource;
use App\Models\AbsAttendance;
use App\Models\AbsPayrollPeriod;
use Illuminate\Http\Request;

class AbsReportController extends Controller
{
    public function attendance(Request $request)
    {
        $records = AbsAttendance::with(['user', 'subCompany', 'shift'])
            ->when($request->date_from, fn ($q, $date) => $q->whereDate('date', '>=', $date))
            ->when($request->date_to, fn ($q, $date) => $q->whereDate('date', '<=', $date))
            ->when($request->sub_company_uuid, fn ($q, $uuid) =>
                $q->whereHas('subCompany', fn ($sc) => $sc->where('uuid', $uuid))
            )
            ->when($request->employee_uuid, fn ($q, $uuid) =>
                $q->whereHas('user', fn ($u) => $u->where('uuid', $uuid))
            )
            ->orderByDesc('date')
            ->paginate($request->input('per_page', 50));

        return response()->json([
            'success' => true,
            'message' => __('absence.reports.attendance'),
            'data' => AbsAttendanceResource::collection($records),
        ]);
    }

    public function payroll(Request $request)
    {
        $month = (int) $request->input('month', now(config('absence.timezone'))->month);
        $year = (int) $request->input('year', now(config('absence.timezone'))->year);

        $records = AbsPayrollPeriod::with(['user', 'deductions'])
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->orderBy('user_id')
            ->paginate($request->input('per_page', 50));

        return response()->json([
            'success' => true,
            'message' => __('absence.reports.payroll'),
            'data' => AbsPayrollPeriodResource::collection($records),
        ]);
    }
}
