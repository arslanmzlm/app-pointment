<?php

namespace App\Enums;

enum Gender: int
{
    case MALE = 1;
    case FEMALE = 2;

    public static function getAll(): array
    {
        return array_map(function ($case) {
            return [
                'name' => $case->name,
                'label' => trans("enums.gender.{$case->name}"),
                'value' => $case->value,
            ];
        }, self::cases());
    }
}
