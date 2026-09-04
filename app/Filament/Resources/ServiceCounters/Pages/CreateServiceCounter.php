<?php

namespace App\Filament\Resources\ServiceCounters\Pages;

use App\Filament\Resources\ServiceCounters\ServiceCounterResource;
use Filament\Resources\Pages\CreateRecord;

class CreateServiceCounter extends CreateRecord
{
    protected static string $resource = ServiceCounterResource::class;
}
