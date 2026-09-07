<?php

namespace App\Filament\Resources\Permissions\Schemas;

use App\Enums\PermissionType;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Permission;

class PermissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Permission Details'))
                    ->schema([
                        Select::make('name')
                            ->label(__('Permission Name / Key'))
                            ->options(function (?Permission $record) {
                                $options = PermissionType::groupedOptions();
                                if ($record && !PermissionType::tryFrom($record->name)) {
                                    $options['Lainnya'][$record->name] = $record->name;
                                }
                                return $options;
                            })
                            ->searchable()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->helperText(__('Pilih hak akses dari daftar PermissionType enum.')),

                        Hidden::make('guard_name')
                            ->default('web'),

                        Select::make('roles')
                            ->label(__('Roles'))
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->helperText(__('Pilih peran yang diberikan hak akses ini.')),
                    ]),
            ]);
    }
}
