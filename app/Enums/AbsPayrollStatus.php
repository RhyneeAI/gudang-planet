<?php

namespace App\Enums;

enum AbsPayrollStatus: string
{
    case DRAFT = 'draft';
    case FINAL = 'final';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
