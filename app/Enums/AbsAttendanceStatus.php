<?php

namespace App\Enums;

enum AbsAttendanceStatus: string
{
    case PRESENT = 'hadir';
    case LATE = 'terlambat';
    case EARLY_OUT = 'pulang_awal';
    case LATE_AND_EARLY_OUT = 'terlambat_pulang_awal';
    case ABSENT = 'absen';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function countedForPayroll(): array
    {
        return [
            self::PRESENT->value,
            self::LATE->value,
            self::EARLY_OUT->value,
            self::LATE_AND_EARLY_OUT->value,
        ];
    }

    public function countsForPayroll(): bool
    {
        return in_array($this->value, self::countedForPayroll(), true);
    }
}
