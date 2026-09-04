<?php

namespace App\Filament\Resources\Patients\Schemas;

use App\Enums\Gender;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PatientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('medical_record_number')
                    ->label('No. Rekam Medis (RM)')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50)
                    ->placeholder('contoh: RM-20260901-0001'),

                TextInput::make('name')
                    ->label('Nama Lengkap Pasien')
                    ->required()
                    ->maxLength(255),

                TextInput::make('national_id')
                    ->label('NIK (No. KTP)')
                    ->unique(ignoreRecord: true)
                    ->maxLength(30)
                    ->placeholder('16 digit NIK'),

                TextInput::make('phone')
                    ->label('Nomor Telepon / WhatsApp')
                    ->tel()
                    ->required()
                    ->maxLength(50),

                TextInput::make('email')
                    ->label('Email Pasien')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                DatePicker::make('date_of_birth')
                    ->label('Tanggal Lahir')
                    ->required()
                    ->maxDate(now()),

                Select::make('gender')
                    ->label('Jenis Kelamin')
                    ->options(Gender::class)
                    ->required(),

                Select::make('blood_type')
                    ->label('Golongan Darah')
                    ->options([
                        'A' => 'A',
                        'B' => 'B',
                        'AB' => 'AB',
                        'O' => 'O',
                    ])
                    ->placeholder('Pilih Golongan Darah'),

                TagsInput::make('allergies')
                    ->label('Riwayat Alergi')
                    ->placeholder('Ketik alergi lalu tekan enter (contoh: Amoksisilin, Seafood)'),

                FileUpload::make('photo')
                    ->label('Foto Pasien')
                    ->image()
                    ->disk('public')
                    ->directory('patients'),

                Textarea::make('address')
                    ->label('Alamat Domisili')
                    ->rows(3),
            ]);
    }
}
