<?php

namespace App\Enums;

enum ScheduleType: string
{
    case Recurring = 'recurring';
    case OneTime = 'one_time';

    public function label(): string
    {
        return match ($this) {
            self::Recurring => 'Berulang (Mingguan)',
            self::OneTime => 'Khusus (Satu Kali)',
        };
    }
}
