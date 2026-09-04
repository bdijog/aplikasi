<?php

namespace App\Filament\Resources\QueueTickets\Pages;

use App\Filament\Resources\QueueTickets\QueueTicketResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditQueueTicket extends EditRecord
{
    protected static string $resource = QueueTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
