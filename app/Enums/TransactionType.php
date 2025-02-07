<?php

namespace App\Enums;

enum TransactionType: int
{
    case INCOME = 1;
    case EXPENSE = 2;

    public static function getAll(): array
    {
        return array_map(function ($case) {
            return [
                'name' => $case->name,
                'label' => trans("enums.transaction.type.{$case->name}"),
                'value' => $case->value,
            ];
        }, self::cases());
    }
}
