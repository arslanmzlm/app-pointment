<?php

namespace App\Enums;

enum PaymentMethod: int
{
    case CASH = 1;
    case CARD = 2;
    case TRANSFER = 3;

    public static function getAll(): array
    {
        return array_map(function ($case) {
            return [
                'name' => $case->name,
                'label' => trans("enums.payment.method.{$case->name}"),
                'value' => $case->value,
            ];
        }, self::cases());
    }
}
