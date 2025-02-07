<?php

namespace App\Enums;

enum FieldInput: int
{
    case TEXT = 1;
    case NUMBER = 2;
    case DATE = 3;
    case SELECT = 4;
    case RADIO = 5;
    case CHECKBOX = 6;

    public static function getAll(): array
    {
        return array_map(function ($case) {
            return [
                'name' => $case->name,
                'label' => trans("enums.field.input.{$case->name}"),
                'value' => $case->value,
                'hasValues' => in_array($case, self::hasValues()),
            ];
        }, self::cases());
    }

    public static function hasValues(): array
    {
        return [
            self::SELECT,
            self::RADIO,
            self::CHECKBOX
        ];
    }
}
