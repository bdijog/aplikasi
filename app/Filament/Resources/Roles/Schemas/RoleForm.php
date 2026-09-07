<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Enums\PermissionType;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Role Information'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('Role Name'))
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('contoh: Petugas Loket, Dokter, Administrator'),

                        Hidden::make('guard_name')
                            ->default('web'),
                    ]),

                Section::make(__('Permissions Assignment'))
                    ->description(__('Pilih hak akses (permission) yang diberikan untuk peran ini. Hak akses disinkronkan dengan PermissionType.'))
                    ->schema([
                        CheckboxList::make('permissions')
                            ->label(__('Permissions'))
                            ->relationship(
                                name: 'permissions',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn ($query) => $query->orderBy('id')
                            )
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                $enum = PermissionType::tryFrom($record->name);
                                return $enum ? $enum->label() : $record->name;
                            })
                            ->getOptionDescriptionFromRecordUsing(function ($record) {
                                $enum = PermissionType::tryFrom($record->name);
                                return $enum ? "[{$enum->group()}] {$record->name}" : $record->name;
                            })
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(2)
                            ->gridDirection('row'),
                    ]),
            ]);
    }
}
