<?php

namespace App\Filament\Resources\QueueTickets\Schemas;

use App\Enums\QueueTicketPriority;
use App\Enums\QueueTicketStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class QueueTicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('appointment_id')
                    ->label(__('Patient Appointment'))
                    ->relationship('appointment', 'booking_code')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('doctor_id')
                    ->label(__('Doctor'))
                    ->relationship('doctor', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('schedule_id')
                    ->label(__('Doctor Schedule'))
                    ->relationship('schedule', 'id')
                    ->required(),

                DatePicker::make('queue_date')
                    ->label(__('Queue Date'))
                    ->default(now())
                    ->required(),

                TextInput::make('display_number')
                    ->label(__('Display Number'))
                    ->required()
                    ->placeholder('contoh: A-001'),

                TextInput::make('prefix')
                    ->label(__('Prefix'))
                    ->default('A')
                    ->maxLength(5)
                    ->required(),

                TextInput::make('queue_number')
                    ->label(__('Queue Number'))
                    ->numeric()
                    ->required(),

                Select::make('priority')
                    ->label(__('Queue Priority'))
                    ->options(QueueTicketPriority::class)
                    ->default(QueueTicketPriority::Normal)
                    ->required(),

                Select::make('status')
                    ->label(__('Ticket Status'))
                    ->options(QueueTicketStatus::class)
                    ->default(QueueTicketStatus::Waiting)
                    ->required(),

                TextInput::make('counter')
                    ->label(__('Counter / Clinic'))
                    ->placeholder('contoh: Poli Anak 1'),

                TextInput::make('call_count')
                    ->label(__('Call Count'))
                    ->numeric()
                    ->default(0),

                Textarea::make('notes')
                    ->label(__('Queue Notes'))
                    ->rows(2),
            ]);
    }
}
