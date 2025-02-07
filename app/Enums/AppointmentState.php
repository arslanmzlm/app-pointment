<?php

namespace App\Enums;

enum AppointmentState: int
{
    case PENDING = 1;
    case CONFIRMED = 2;
    case COMPLETED = 3;
    case RESCHEDULED = 4;
    case CANCELED = 5;

    public static function getAll(): array
    {
        return array_map(function ($case) {
            return [
                'name' => $case->name,
                'label' => trans("enums.appointment.state.{$case->name}"),
                'value' => $case->value,
            ];
        }, self::cases());
    }
}
