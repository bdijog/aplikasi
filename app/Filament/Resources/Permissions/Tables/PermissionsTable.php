<?php

namespace App\Filament\Resources\Permissions\Tables;

use App\Enums\PermissionType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PermissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Permission Key'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => PermissionType::tryFrom($record->name)?->label() ?? '-'),

                TextColumn::make('group')
                    ->label(__('Category / Group'))
                    ->state(fn ($record) => PermissionType::tryFrom($record->name)?->group() ?? 'Lainnya')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Jadwal & Dokter' => 'info',
                        'Janji Temu & Pasien' => 'primary',
                        'Antrean Layanan' => 'warning',
                        'Master Informasi' => 'gray',
                        'Laporan & Statistik' => 'success',
                        'Manajemen Sistem' => 'danger',
                        default => 'secondary',
                    }),

                TextColumn::make('guard_name')
                    ->label(__('Guard'))
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('roles_count')
                    ->counts('roles')
                    ->label(__('Assigned Roles'))
                    ->badge()
                    ->color('success')
                    ->sortable(),

                TextColumn::make('roles.name')
                    ->label(__('Roles'))
                    ->badge()
                    ->color('info')
                    ->limitList(3)
                    ->expandableLimitedList(),

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
