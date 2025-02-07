<?php

namespace App\Enums;

enum UserType: int
{
    case PATIENT = 1;
    case DOCTOR = 2;
    case ADMIN = 3;

    public static function getAll(): array
    {
        return array_map(function ($case) {
            return [
                'name' => $case->name,
                'label' => trans("enums.user.type.{$case->name}"),
                'value' => $case->value,
            ];
        }, self::cases());
    }
}
