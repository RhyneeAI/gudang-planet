<?php

namespace App\Http\Controllers\Api\Absence;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\AbsAttendance;
use App\Models\AbsBranch;
use App\Models\AbsEmployeeProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AbsDashboardController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->user()->company_id;
        $today = Carbon::now(config('absence.timezone'))->toDateString();

        $activeEmployees = User::where('company_id', $companyId)
            ->where('role', Role::KARYAWAN)
            ->where('is_active', true)
            ->count();

        $todayAttendances = AbsAttendance::where('company_id', $companyId)
            ->whereDate('date', $today)
            ->get();

        $presentToday = $todayAttendances
            ->filter(fn ($attendance) => $attendance->status?->countsForPayroll())
            ->count();
        $lateToday = $todayAttendances
            ->filter(fn ($attendance) => in_array($attendance->status?->value, ['terlambat', 'terlambat_pulang_awal'], true))
            ->count();

        $checkedInIds = $todayAttendances->pluck('user_id')->unique();
        $notYetAbsent = User::where('company_id', $companyId)
            ->where('role', Role::KARYAWAN)
            ->where('is_active', true)
            ->whereNotIn('id', $checkedInIds)
            ->with(['absEmployeeProfile.branch'])
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'uuid', 'name']);

        $branches = AbsBranch::where('company_id', $companyId)
            ->withCount([
                'employeeProfiles as employees_count',
            ])
            ->get()
            ->map(function ($branch) use ($today, $companyId) {
                $employeeIds = AbsEmployeeProfile::where('abs_branch_id', $branch->id)->pluck('user_id');
                $present = AbsAttendance::where('company_id', $companyId)
                    ->whereDate('date', $today)
                    ->whereIn('user_id', $employeeIds)
                    ->count();

                return [
                    'uuid' => (string) $branch->uuid,
                    'name' => $branch->name,
                    'employees_count' => (int) $branch->employees_count,
                    'present_today' => $present,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => __('absence.dashboard.summary'),
            'data' => [
                'active_employees' => $activeEmployees,
                'present_today' => $presentToday,
                'late_today' => $lateToday,
                'not_yet_absent_count' => max(0, $activeEmployees - $checkedInIds->count()),
                'not_yet_absent' => $notYetAbsent->map(fn ($u) => [
                    'uuid' => $u->uuid,
                    'name' => $u->name,
                    'branch' => $u->absEmployeeProfile?->branch?->name,
                ]),
                'branches' => $branches,
            ],
        ]);
    }
}
