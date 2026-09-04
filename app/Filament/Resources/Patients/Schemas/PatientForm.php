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
                    ->label(__('Medical Record No. (RM)'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50)
                    ->placeholder('contoh: RM-20260901-0001'),

                TextInput::make('name')
                    ->label(__('Patient Full Name'))
                    ->required()
                    ->maxLength(255),

                TextInput::make('national_id')
                    ->label(__('NIK (ID Card No.)'))
                    ->unique(ignoreRecord: true)
                    ->maxLength(30)
                    ->placeholder('16 digit NIK'),

                TextInput::make('phone')
                    ->label(__('Phone / WhatsApp Number'))
                    ->tel()
                    ->required()
                    ->maxLength(50),

                TextInput::make('email')
                    ->label(__('Patient Email'))
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                DatePicker::make('date_of_birth')
                    ->label(__('Date of Birth'))
                    ->required()
                    ->maxDate(now()),

                Select::make('gender')
                    ->label(__('Gender'))
                    ->options(Gender::class)
                    ->required(),

                Select::make('blood_type')
                    ->label(__('Blood Type'))
                    ->options([
                        'A' => 'A',
                        'B' => 'B',
                        'AB' => 'AB',
                        'O' => 'O',
                    ])
                    ->placeholder(__('Select Blood Type')),

                TagsInput::make('allergies')
                    ->label(__('Allergy History'))
                    ->placeholder('Ketik alergi lalu tekan enter (contoh: Amoksisilin, Seafood)'),

                FileUpload::make('photo')
                    ->label(__('Patient Photo'))
                    ->image()
                    ->disk('public')
                    ->directory('patients'),

                Textarea::make('address')
                    ->label(__('Home Address'))
                    ->rows(3),
            ]);
    }
}
