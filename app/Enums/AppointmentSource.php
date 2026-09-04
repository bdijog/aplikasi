<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AppointmentSource: string implements HasLabel, HasColor
{
    case Online = 'online';
    case WalkIn = 'walk_in';
    case Phone = 'phone';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Online => 'Online Portal',
            self::WalkIn => 'Langsung di Loket (Walk-in)',
            self::Phone => 'Telepon / WhatsApp',
        };
    }

    public function label(): string
    {
        return $this->getLabel() ?? '';
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Online => 'success',
            self::WalkIn => 'warning',
            self::Phone => 'info',
        };
    }
}
