<?php

namespace App\Services\Absence;

use App\Enums\AbsAttendanceStatus;
use App\Models\AbsAttendance;
use App\Models\AbsEmployeeProfile;
use App\Models\AbsShift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

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

        $subCompany = $profile->subCompany;
        $shift = $profile->shift;

        $this->assertAttendanceLocation($profile, $latitude, $longitude);

        $now = $this->now();
        $isLate = $this->isLate($shift, $now);

        if ($isLate && blank($lateReason)) {
            throw new \RuntimeException(__('absence.attendance.late_reason_required'));
        }

        return AbsAttendance::create([
            'user_id' => $user->id,
            'sub_company_id' => $subCompany->id,
            'abs_shift_id' => $shift->id,
            'date' => $this->today(),
            'check_in_time' => $now->format('H:i:s'),
            'check_in_photo' => $this->fileService->storePhoto($photo, 'check-in'),
            'check_in_lat' => $latitude,
            'check_in_lng' => $longitude,
            'status' => $isLate ? AbsAttendanceStatus::LATE : AbsAttendanceStatus::PRESENT,
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

        $this->assertAttendanceLocation($profile, $latitude, $longitude);

        $shift = $profile->shift;
        $now = $this->now();
        $isEarly = $this->isEarlyLeave($shift, $now);

        if ($isEarly && blank($earlyReason)) {
            throw new \RuntimeException(__('absence.attendance.early_reason_required'));
        }

        $wasLate = in_array($attendance->status, [
            AbsAttendanceStatus::LATE,
            AbsAttendanceStatus::LATE_AND_EARLY_OUT,
        ], true);

        $status = match (true) {
            $wasLate && $isEarly => AbsAttendanceStatus::LATE_AND_EARLY_OUT,
            $wasLate => AbsAttendanceStatus::LATE,
            $isEarly => AbsAttendanceStatus::EARLY_OUT,
            default => AbsAttendanceStatus::PRESENT,
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

    protected function assertAttendanceLocation(AbsEmployeeProfile $profile, float $latitude, float $longitude): void
    {
        $subCompany = $profile->subCompany;

        if (!$subCompany) {
            throw new \RuntimeException(__('absence.attendance.sub_company_not_assigned'));
        }

        if (!$profile->shift) {
            throw new \RuntimeException(__('absence.attendance.shift_not_assigned'));
        }

        if (!$this->gpsService->isWithinSubCompanyRadius($subCompany, $latitude, $longitude)) {
            throw new \RuntimeException(__('absence.attendance.location_out_of_range'));
        }
    }

    protected function getShiftRange(AbsShift $shift, Carbon $time): array
    {
        $start = Carbon::parse(
            $time->toDateString() . ' ' . $shift->start_time,
            $time->timezone
        );

        $end = Carbon::parse(
            $time->toDateString() . ' ' . $shift->end_time,
            $time->timezone
        );

        if ($end->lt($start)) {
            $end->addDay();
        }

        return [$start, $end];
    }

    protected function isLate(AbsShift $shift, Carbon $time): bool
    {
        [$start, $end] = $this->getShiftRange($shift, $time);

        return $time->gt($start);
    }

    protected function isEarlyLeave(AbsShift $shift, Carbon $time): bool
    {
        [$start, $end] = $this->getShiftRange($shift, $time);

        return $time->lt($end);
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
