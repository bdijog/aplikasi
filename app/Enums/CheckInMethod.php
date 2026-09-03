<?php

namespace App\Enums;

enum CheckInMethod: string
{
    case SelfService = 'self_service';
    case Counter = 'counter';
    case QrScan = 'qr_scan';

    public function label(): string
    {
        return match ($this) {
            self::SelfService => 'Mandiri (Kiosk / Portal)',
            self::Counter => 'Petugas Loket',
            self::QrScan => 'Scan QR Code',
        };
    }
}
