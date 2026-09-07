<?php

namespace App\Filament\Resources\ServiceCounters\Pages;

use App\Filament\Resources\ServiceCounters\ServiceCounterResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

class EditServiceCounter extends EditRecord
{
    use Translatable;

    protected static string $resource = ServiceCounterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            LocaleSwitcher::make(),
        ];
    }
}
