<?php

namespace App\Filament\Resources\QueueTickets\Pages;

use App\Filament\Resources\QueueTickets\QueueTicketResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQueueTicket extends CreateRecord
{
    protected static string $resource = QueueTicketResource::class;
}
