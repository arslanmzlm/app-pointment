<?php

namespace App\Enums;

enum MessageType: int
{
    case APPOINT = 1;
    case REMIND = 2;

    public static function getAll(): array
    {
        return array_map(function ($case) {
            return [
                'name' => $case->name,
                'label' => trans("enums.message.type.{$case->name}"),
                'value' => $case->value,
            ];
        }, self::cases());
    }
}
