<?php

namespace App\Filament\Resources\QueueTickets\Schemas;

use App\Enums\QueueTicketPriority;
use App\Enums\QueueTicketStatus;
use App\Models\Appointment;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class QueueTicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('appointment_id')
                    ->label(__('Patient Appointment'))
                    ->relationship('appointment', 'booking_code')
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        $patientName = $record->patient?->name ? " ({$record->patient->name})" : '';
                        $date = $record->appointment_date ? ' - '.Carbon::parse($record->appointment_date)->format('d/m/Y') : '';

                        return "{$record->booking_code}{$patientName}{$date}";
                    })
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, Set $set) {
                        if ($state) {
                            $appointment = Appointment::with('schedule')->find($state);
                            if ($appointment) {
                                $set('doctor_id', $appointment->doctor_id);
                                $set('schedule_id', $appointment->schedule_id);
                                if ($appointment->appointment_date) {
                                    $set('queue_date', Carbon::parse($appointment->appointment_date)->format('Y-m-d'));
                                }
                            }
                        }
                    })
                    ->required(),

                Select::make('doctor_id')
                    ->label(__('Doctor'))
                    ->relationship('doctor', 'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required(),

                Select::make('schedule_id')
                    ->label(__('Doctor Schedule'))
                    ->relationship(
                        'schedule',
                        'id',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->when(
                            $get('doctor_id'),
                            fn (Builder $q, $doctorId) => $q->where('doctor_id', $doctorId)
                        )
                    )
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        $doctorPrefix = $record->doctor?->name ? "{$record->doctor->name} • " : '';

                        return "{$doctorPrefix}{$record->full_schedule_label}";
                    })
                    ->searchable()
                    ->preload()
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
