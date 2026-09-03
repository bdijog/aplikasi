<?php

namespace App\Enums;

enum QueueTicketPriority: string
{
    case Normal = 'normal';
    case Priority = 'priority';
    case Emergency = 'emergency';

    public function label(): string
    {
        return match ($this) {
            self::Normal => 'Reguler / Normal',
            self::Priority => 'Prioritas (Lansia / Disabilitas / Hamil)',
            self::Emergency => 'Gawat Darurat (Emergency)',
        };
    }
}
