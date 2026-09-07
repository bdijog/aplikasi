<?php

namespace App\Filament\Resources\ServiceCounters\Pages;

use App\Filament\Resources\ServiceCounters\ServiceCounterResource;
use Filament\Resources\Pages\CreateRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;

class CreateServiceCounter extends CreateRecord
{
    use Translatable;

    protected static string $resource = ServiceCounterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }
}
