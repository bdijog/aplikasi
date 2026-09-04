<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum QueueTicketPriority: string implements HasLabel, HasColor
{
    case Normal = 'normal';
    case Priority = 'priority';
    case Emergency = 'emergency';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Normal => 'Reguler / Normal',
            self::Priority => 'Prioritas (Lansia / Disabilitas / Hamil)',
            self::Emergency => 'Gawat Darurat (Emergency)',
        };
    }

    public function label(): string
    {
        return $this->getLabel() ?? '';
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Normal => 'gray',
            self::Priority => 'warning',
            self::Emergency => 'danger',
        };
    }
}
