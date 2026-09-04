<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CheckInMethod: string implements HasLabel, HasColor
{
    case SelfService = 'self_service';
    case Counter = 'counter';
    case QrScan = 'qr_scan';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SelfService => 'Mandiri (Kiosk / Portal)',
            self::Counter => 'Petugas Loket',
            self::QrScan => 'Scan QR Code',
        };
    }

    public function label(): string
    {
        return $this->getLabel() ?? '';
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::SelfService => 'info',
            self::Counter => 'primary',
            self::QrScan => 'success',
        };
    }
}
