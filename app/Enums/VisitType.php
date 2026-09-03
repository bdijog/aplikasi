<?php

namespace App\Enums;

enum VisitType: string
{
    case NewVisit = 'new_visit';
    case FollowUp = 'follow_up';

    public function label(): string
    {
        return match ($this) {
            self::NewVisit => 'Kunjungan Baru',
            self::FollowUp => 'Kontrol / Kunjungan Ulang',
        };
    }
}
