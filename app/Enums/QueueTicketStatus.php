<?php

namespace App\Enums;

enum QueueTicketStatus: string
{
    case Waiting = 'waiting';
    case Serving = 'serving';
    case Completed = 'completed';
    case Skipped = 'skipped';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Waiting => 'Menunggu',
            self::Serving => 'Sedang Dilayani',
            self::Completed => 'Selesai',
            self::Skipped => 'Terlewat (Skipped)',
            self::Cancelled => 'Dibatalkan',
        };
    }
}
