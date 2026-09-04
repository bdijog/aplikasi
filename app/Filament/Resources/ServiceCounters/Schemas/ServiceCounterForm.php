<?php

namespace App\Filament\Resources\ServiceCounters\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ServiceCounterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Ruangan / Loket')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('contoh: Loket Pendaftaran 1, Poli Anak'),

                TextInput::make('code')
                    ->label('Kode Ruangan / Loket')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50)
                    ->placeholder('contoh: LOK-01, POLI-01'),

                TextInput::make('location')
                    ->label('Lokasi / Lantai')
                    ->maxLength(255)
                    ->placeholder('contoh: Gedung A Lantai 1'),

                Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->default(true),
            ]);
    }
}
