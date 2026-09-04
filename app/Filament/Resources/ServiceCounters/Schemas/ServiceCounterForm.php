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
                    ->label(__('Room / Counter Name'))
                    ->required()
                    ->maxLength(255)
                    ->placeholder('contoh: Loket Pendaftaran 1, Poli Anak'),

                TextInput::make('code')
                    ->label(__('Room / Counter Code'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(50)
                    ->placeholder('contoh: LOK-01, POLI-01'),

                TextInput::make('location')
                    ->label(__('Location / Floor'))
                    ->maxLength(255)
                    ->placeholder('contoh: Gedung A Lantai 1'),

                Toggle::make('is_active')
                    ->label(__('Active Status'))
                    ->default(true),
            ]);
    }
}
