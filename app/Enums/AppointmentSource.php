<?php

namespace App\Enums;

enum AppointmentSource: string
{
    case Online = 'online';
    case WalkIn = 'walk_in';
    case Phone = 'phone';

    public function label(): string
    {
        return match ($this) {
            self::Online => 'Online Portal',
            self::WalkIn => 'Langsung di Loket (Walk-in)',
            self::Phone => 'Telepon / WhatsApp',
        };
    }
}
