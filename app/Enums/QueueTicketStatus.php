<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum QueueTicketStatus: string implements HasLabel, HasColor
{
    case Waiting = 'waiting';
    case Serving = 'serving';
    case Completed = 'completed';
    case Skipped = 'skipped';
    case Cancelled = 'cancelled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Waiting => 'Menunggu',
            self::Serving => 'Sedang Dilayani',
            self::Completed => 'Selesai',
            self::Skipped => 'Terlewat (Skipped)',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function label(): string
    {
        return $this->getLabel() ?? '';
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Waiting => 'warning',
            self::Serving => 'info',
            self::Completed => 'success',
            self::Skipped => 'danger',
            self::Cancelled => 'gray',
        };
    }
}
