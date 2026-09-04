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
use UnitEnum;

class QueueTicketResource extends Resource
{
    protected static ?string $model = QueueTicket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static string|UnitEnum|null $navigationGroup = 'Layanan Pasien & Antrian';

    protected static ?string $modelLabel = 'Antrian Layanan';

    protected static ?string $pluralModelLabel = 'Tiket Antrian Layanan';

    protected static ?int $navigationSort = 2;

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
