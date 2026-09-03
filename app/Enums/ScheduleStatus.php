<?php

namespace App\Enums;

enum ScheduleStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Aktif',
            self::Inactive => 'Tidak Aktif',
            self::Cancelled => 'Dibatalkan',
        };
    }
}
