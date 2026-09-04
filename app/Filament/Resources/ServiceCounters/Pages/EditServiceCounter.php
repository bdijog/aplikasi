<?php

namespace App\Filament\Resources\ServiceCounters\Pages;

use App\Filament\Resources\ServiceCounters\ServiceCounterResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditServiceCounter extends EditRecord
{
    protected static string $resource = ServiceCounterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
