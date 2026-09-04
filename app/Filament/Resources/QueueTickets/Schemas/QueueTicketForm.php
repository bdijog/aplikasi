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
                    ->label('Appointment Pasien')
                    ->relationship('appointment', 'booking_code')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('doctor_id')
                    ->label('Dokter')
                    ->relationship('doctor', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('schedule_id')
                    ->label('Jadwal Dokter')
                    ->relationship('schedule', 'id')
                    ->required(),

                DatePicker::make('queue_date')
                    ->label('Tanggal Antrian')
                    ->default(now())
                    ->required(),

                TextInput::make('display_number')
                    ->label('Nomor Tampilan (Display)')
                    ->required()
                    ->placeholder('contoh: A-001'),

                TextInput::make('prefix')
                    ->label('Prefix')
                    ->default('A')
                    ->maxLength(5)
                    ->required(),

                TextInput::make('queue_number')
                    ->label('Nomor Urut')
                    ->numeric()
                    ->required(),

                Select::make('priority')
                    ->label('Prioritas Antrian')
                    ->options(QueueTicketPriority::class)
                    ->default(QueueTicketPriority::Normal)
                    ->required(),

                Select::make('status')
                    ->label('Status Tiket')
                    ->options(QueueTicketStatus::class)
                    ->default(QueueTicketStatus::Waiting)
                    ->required(),

                TextInput::make('counter')
                    ->label('Loket / Poli Layanan')
                    ->placeholder('contoh: Poli Anak 1'),

                TextInput::make('call_count')
                    ->label('Jumlah Panggilan')
                    ->numeric()
                    ->default(0),

                Textarea::make('notes')
                    ->label('Catatan Antrian')
                    ->rows(2),
            ]);
    }
}
