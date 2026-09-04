<?php

namespace App\Filament\Resources\Schedules\Schemas;

use App\Enums\ScheduleStatus;
use App\Enums\ScheduleType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('doctor_id')
                    ->label('Dokter')
                    ->relationship('doctor', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('type')
                    ->label('Tipe Jadwal')
                    ->options(ScheduleType::class)
                    ->default(ScheduleType::Recurring)
                    ->required()
                    ->live(),

                Select::make('day_of_week')
                    ->label('Hari Praktik')
                    ->options([
                        0 => 'Minggu',
                        1 => 'Senin',
                        2 => 'Selasa',
                        3 => 'Rabu',
                        4 => 'Kamis',
                        5 => 'Jumat',
                        6 => 'Sabtu',
                    ])
                    ->required(fn (Get $get) => in_array($get('type'), [ScheduleType::Recurring->value, 'recurring', null], true))
                    ->visible(fn (Get $get) => in_array($get('type'), [ScheduleType::Recurring->value, 'recurring', null], true)),

                DatePicker::make('specific_date')
                    ->label('Tanggal Khusus')
                    ->required(fn (Get $get) => in_array($get('type'), [ScheduleType::OneTime->value, 'one_time'], true))
                    ->visible(fn (Get $get) => in_array($get('type'), [ScheduleType::OneTime->value, 'one_time'], true)),

                TimePicker::make('start_time')
                    ->label('Jam Mulai Praktik')
                    ->seconds(false)
                    ->required(),

                TimePicker::make('end_time')
                    ->label('Jam Selesai Praktik')
                    ->seconds(false)
                    ->required(),

                TextInput::make('max_patients')
                    ->label('Kuota Pasien')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(50)
                    ->default(20)
                    ->required()
                    ->helperText('Batas kuota pasien antara 1 - 50'),

                Select::make('status')
                    ->label('Status Jadwal')
                    ->options(ScheduleStatus::class)
                    ->default(ScheduleStatus::Active)
                    ->required(),

                Textarea::make('notes')
                    ->label('Catatan / Keterangan')
                    ->rows(2),
            ]);
    }
}
