<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum VisitType: string implements HasLabel, HasColor
{
    case NewVisit = 'new_visit';
    case FollowUp = 'follow_up';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::NewVisit => 'Kunjungan Baru',
            self::FollowUp => 'Kontrol / Kunjungan Ulang',
        };
    }

    public function label(): string
    {
        return $this->getLabel() ?? '';
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::NewVisit => 'info',
            self::FollowUp => 'primary',
        };
    }
}
