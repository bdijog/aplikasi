<?php

namespace App\Filament\Resources\Appointments\Schemas;

use App\Enums\AppointmentSource;
use App\Enums\AppointmentStatus;
use App\Enums\VisitType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class AppointmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('booking_code')
                    ->label(__('Booking Code'))
                    ->default(fn () => 'APT-' . date('Ymd') . '-' . strtoupper(Str::random(4)))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->disabled(fn (string $operation): bool => $operation === 'edit')
                    ->dehydrated(),

                Select::make('patient_id')
                    ->label(__('Patient'))
                    ->relationship('patient', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('doctor_id')
                    ->label(__('Doctor'))
                    ->relationship('doctor', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live(),

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
                        $dayOrDate = ($record->type?->value === 'one_time' || $record->type === 'one_time')
                            ? ($record->specific_date ? Carbon::parse($record->specific_date)->format('d/m/Y') : '-')
                            : ([
                                0 => 'Minggu',
                                1 => 'Senin',
                                2 => 'Selasa',
                                3 => 'Rabu',
                                4 => 'Kamis',
                                5 => 'Jumat',
                                6 => 'Sabtu',
                            ][$record->day_of_week] ?? '-');

                        $start = $record->start_time ? Carbon::parse($record->start_time)->format('H:i') : '--:--';
                        $end = $record->end_time ? Carbon::parse($record->end_time)->format('H:i') : '--:--';

                        return "{$dayOrDate} ({$start} - {$end}) - Kuota: {$record->max_patients}";
                    })
                    ->searchable()
                    ->preload()
                    ->required(),

                DatePicker::make('appointment_date')
                    ->label(__('Appointment Date'))
                    ->default(now())
                    ->required(),

                TimePicker::make('estimated_time')
                    ->label(__('Estimated Service Time'))
                    ->seconds(false),

                Select::make('visit_type')
                    ->label(__('Visit Type'))
                    ->options(VisitType::class)
                    ->default(VisitType::NewVisit)
                    ->required(),

                Select::make('source')
                    ->label(__('Booking Source'))
                    ->options(AppointmentSource::class)
                    ->default(AppointmentSource::Online)
                    ->required(),

                Select::make('status')
                    ->label(__('Appointment Status'))
                    ->options(AppointmentStatus::class)
                    ->default(AppointmentStatus::Pending)
                    ->live()
                    ->required(),

                Textarea::make('chief_complaint')
                    ->label(__('Chief Complaint'))
                    ->rows(3),

                Textarea::make('patient_notes')
                    ->label(__('Patient Notes'))
                    ->rows(2),

                Textarea::make('cancellation_reason')
                    ->label(__('Cancellation Reason'))
                    ->rows(2)
                    ->visible(fn (Get $get): bool => in_array($get('status'), [AppointmentStatus::Cancelled->value, 'cancelled'], true)),
            ]);
    }
}
