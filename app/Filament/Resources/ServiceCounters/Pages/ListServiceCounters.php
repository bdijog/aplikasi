<?php

namespace App\Filament\Resources\ServiceCounters\Pages;

use App\Filament\Resources\ServiceCounters\ServiceCounterResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\ListRecords\Concerns\Translatable;

class ListServiceCounters extends ListRecords
{
    use Translatable;

    protected static string $resource = ServiceCounterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            LocaleSwitcher::make(),
        ];
    }
}
