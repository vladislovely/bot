<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case REFUNDED = 'refunded';
    case FAILED = 'failed';

    public static function values(): array
    {
        $callback = static fn(object $v): string => $v->value;

        return array_map($callback, self::cases());
    }
}
