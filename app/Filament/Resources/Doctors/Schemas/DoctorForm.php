<?php

namespace App\Filament\Resources\Doctors\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DoctorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255),

                TextInput::make('license_number')
                    ->label('Nomor STR')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(100)
                    ->placeholder('contoh: STR-12345678'),

                TextInput::make('specialty')
                    ->label('Spesialisasi')
                    ->maxLength(255)
                    ->placeholder('contoh: Spesialis Anak, Umum, Penyakit Dalam'),

                TextInput::make('email')
                    ->label('Email Login')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label('Nomor Telepon')
                    ->tel()
                    ->maxLength(50),

                FileUpload::make('photo')
                    ->label('Foto Dokter')
                    ->image()
                    ->imageEditor()
                    ->disk('public')
                    ->directory('doctors'),

                Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->default(true),

                Textarea::make('bio')
                    ->label('Bio / Profil Singkat')
                    ->rows(3),
            ]);
    }
}
