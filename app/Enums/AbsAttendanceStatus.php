<?php

namespace App\Enums;

enum AbsAttendanceStatus: string
{
    case HADIR = 'hadir';
    case TERLAMBAT = 'terlambat';
    case PULANG_AWAL = 'pulang_awal';
    case TERLAMBAT_PULANG_AWAL = 'terlambat_pulang_awal';
    case ABSEN = 'absen';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function countedForPayroll(): array
    {
        return [
            self::HADIR->value,
            self::TERLAMBAT->value,
            self::PULANG_AWAL->value,
            self::TERLAMBAT_PULANG_AWAL->value,
        ];
    }

    public function countsForPayroll(): bool
    {
        return in_array($this->value, self::countedForPayroll(), true);
    }
}
