<?php

namespace App\Filament\Resources\QueueTickets\Pages;

use App\Filament\Resources\QueueTickets\QueueTicketResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQueueTickets extends ListRecords
{
    protected static string $resource = QueueTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
