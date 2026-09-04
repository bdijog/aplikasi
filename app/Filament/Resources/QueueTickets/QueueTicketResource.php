<?php

namespace App\Filament\Resources\QueueTickets;

use App\Filament\Resources\QueueTickets\Pages\CreateQueueTicket;
use App\Filament\Resources\QueueTickets\Pages\EditQueueTicket;
use App\Filament\Resources\QueueTickets\Pages\ListQueueTickets;
use App\Filament\Resources\QueueTickets\Schemas\QueueTicketForm;
use App\Filament\Resources\QueueTickets\Tables\QueueTicketsTable;
use App\Models\QueueTicket;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class QueueTicketResource extends Resource
{
    protected static ?string $model = QueueTicket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('Patient Services & Queue');
    }

    public static function getModelLabel(): string
    {
        return __('Queue Ticket');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Queue Tickets');
    }

    public static function form(Schema $schema): Schema
    {
        return QueueTicketForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QueueTicketsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQueueTickets::route('/'),
            'create' => CreateQueueTicket::route('/create'),
            'edit' => EditQueueTicket::route('/{record}/edit'),
        ];
    }
}
