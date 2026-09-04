<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ScheduleType: string implements HasLabel, HasColor
{
    case Recurring = 'recurring';
    case OneTime = 'one_time';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Recurring => 'Berulang (Mingguan)',
            self::OneTime => 'Khusus (Satu Kali)',
        };
    }

    public function label(): string
    {
        return $this->getLabel() ?? '';
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Recurring => 'info',
            self::OneTime => 'warning',
        };
    }
}
