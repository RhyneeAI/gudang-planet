<?php

namespace App\Services\Absence;

use App\Enums\AbsAttendanceStatus;
use App\Models\AbsAttendance;
use App\Models\AbsEmployeeProfile;
use App\Models\AbsShift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

class AbsAttendanceService
{
    public function __construct(
        protected AbsGpsService $gpsService,
        protected AbsFileService $fileService,
    ) {}

    public function todayFor(User $user): ?AbsAttendance
    {
        return AbsAttendance::where('user_id', $user->id)
            ->whereDate('date', $this->today())
            ->first();
    }

    public function checkIn(
        User $user,
        AbsEmployeeProfile $profile,
        UploadedFile $photo,
        float $latitude,
        float $longitude,
        ?string $lateReason = null
    ): AbsAttendance {
        if ($this->todayFor($user)) {
            throw new \RuntimeException(__('absence.attendance.already_checked_in'));
        }

        $branch = $profile->branch;
        $shift = $profile->shift;

        if (!$this->gpsService->isWithinBranchRadius($branch, $latitude, $longitude)) {
            throw new \RuntimeException(__('absence.attendance.location_out_of_range'));
        }

        $now = $this->now();
        $isLate = $this->isLate($shift, $now);

        if ($isLate && blank($lateReason)) {
            throw new \RuntimeException(__('absence.attendance.late_reason_required'));
        }

        return AbsAttendance::create([
            'user_id' => $user->id,
            'abs_branch_id' => $branch->id,
            'abs_shift_id' => $shift->id,
            'date' => $this->today(),
            'check_in_time' => $now->format('H:i:s'),
            'check_in_photo' => $this->fileService->storePhoto($photo, 'check-in'),
            'check_in_lat' => $latitude,
            'check_in_lng' => $longitude,
            'status' => $isLate ? AbsAttendanceStatus::TERLAMBAT : AbsAttendanceStatus::HADIR,
            'late_reason' => $isLate ? $lateReason : null,
            'company_id' => $user->company_id,
        ]);
    }

    public function checkOut(
        User $user,
        AbsEmployeeProfile $profile,
        UploadedFile $photo,
        float $latitude,
        float $longitude,
        ?string $earlyReason = null
    ): AbsAttendance {
        $attendance = $this->todayFor($user);

        if (!$attendance || !$attendance->hasCheckedIn()) {
            throw new \RuntimeException(__('absence.attendance.check_in_first'));
        }

        if ($attendance->hasCheckedOut()) {
            throw new \RuntimeException(__('absence.attendance.already_checked_out'));
        }

        $branch = $profile->branch;
        $shift = $profile->shift;

        if (!$this->gpsService->isWithinBranchRadius($branch, $latitude, $longitude)) {
            throw new \RuntimeException(__('absence.attendance.location_out_of_range'));
        }

        $now = $this->now();
        $isEarly = $this->isEarlyLeave($shift, $now);

        if ($isEarly && blank($earlyReason)) {
            throw new \RuntimeException(__('absence.attendance.early_reason_required'));
        }

        $wasLate = in_array($attendance->status, [
            AbsAttendanceStatus::TERLAMBAT,
            AbsAttendanceStatus::TERLAMBAT_PULANG_AWAL,
        ], true);

        $status = match (true) {
            $wasLate && $isEarly => AbsAttendanceStatus::TERLAMBAT_PULANG_AWAL,
            $wasLate => AbsAttendanceStatus::TERLAMBAT,
            $isEarly => AbsAttendanceStatus::PULANG_AWAL,
            default => AbsAttendanceStatus::HADIR,
        };

        $attendance->update([
            'check_out_time' => $now->format('H:i:s'),
            'check_out_photo' => $this->fileService->storePhoto($photo, 'check-out'),
            'check_out_lat' => $latitude,
            'check_out_lng' => $longitude,
            'early_reason' => $isEarly ? $earlyReason : null,
            'status' => $status,
        ]);

        return $attendance->fresh();
    }

    public function monthSummary(User $user, ?int $month = null, ?int $year = null): array
    {
        $month = $month ?? (int) $this->now()->month;
        $year = $year ?? (int) $this->now()->year;

        $presentDays = AbsAttendance::where('user_id', $user->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->whereIn('status', config('absence.attended_statuses'))
            ->count();

        $workingDays = (int) $this->now()->copy()->year($year)->month($month)->daysInMonth;

        return [
            'month' => $month,
            'year' => $year,
            'present_days' => $presentDays,
            'working_days' => $workingDays,
        ];
    }

    protected function isLate(AbsShift $shift, Carbon $time): bool
    {
        $start = Carbon::parse($shift->start_time)->setDateFrom($time);

        return $time->greaterThan($start);
    }

    protected function isEarlyLeave(AbsShift $shift, Carbon $time): bool
    {
        $end = Carbon::parse($shift->end_time)->setDateFrom($time);

        return $time->lessThan($end);
    }

    protected function now(): Carbon
    {
        return Carbon::now(config('absence.timezone'));
    }

    protected function today(): string
    {
        return $this->now()->toDateString();
    }
}
