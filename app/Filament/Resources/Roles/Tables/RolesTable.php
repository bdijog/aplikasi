<?php

namespace App\Filament\Resources\Roles\Tables;

use App\Enums\PermissionType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Role Name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('guard_name')
                    ->label(__('Guard'))
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('permissions_count')
                    ->counts('permissions')
                    ->label(__('Total Permissions'))
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('permissions.name')
                    ->label(__('Permissions'))
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => PermissionType::tryFrom($state)?->label() ?? $state)
                    ->limitList(3)
                    ->expandableLimitedList(),

                TextColumn::make('users_count')
                    ->counts('users')
                    ->label(__('Users'))
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
