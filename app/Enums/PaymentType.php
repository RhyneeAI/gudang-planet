<?php

namespace App\Enums;

enum PaymentType: string
{
    case CASH = 'CASH';
    case TRANSFER = 'TRANSFER';
    case QRIS = 'QRIS';

    public static function all(): array
    {
        return [
            self::CASH->value,
            self::TRANSFER->value,
            self::QRIS->value,
        ];
    }

    public function isCash(): bool
    {
        return $this === self::CASH;
    }

    public function isTransfer(): bool
    {
        return $this === self::TRANSFER;
    }

    public function isQris(): bool
    {
        return $this === self::QRIS;
    }
}
