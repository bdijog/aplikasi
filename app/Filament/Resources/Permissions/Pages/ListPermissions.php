<?php

namespace App\Filament\Resources\Permissions\Pages;

use App\Enums\PermissionType;
use App\Filament\Resources\Permissions\PermissionResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Spatie\Permission\Models\Permission;

class ListPermissions extends ListRecords
{
    protected static string $resource = PermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('syncFromEnum')
                ->label(__('Sinkronkan dari Enum'))
                ->icon(Heroicon::OutlinedArrowPath)
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading(__('Sinkronisasi Permission dari Enum'))
                ->modalDescription(__('Tindakan ini akan mendaftarkan seluruh hak akses dari PermissionType enum ke database.'))
                ->action(function () {
                    $createdCount = 0;
                    foreach (PermissionType::cases() as $case) {
                        $perm = Permission::firstOrCreate(
                            ['name' => $case->value, 'guard_name' => 'web']
                        );
                        if ($perm->wasRecentlyCreated) {
                            $createdCount++;
                        }
                    }

                    app('cache')
                        ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
                        ->forget(config('permission.cache.key'));

                    Notification::make()
                        ->title(__('Sinkronisasi Berhasil'))
                        ->body($createdCount > 0
                            ? __(':count permission baru berhasil ditambahkan.', ['count' => $createdCount])
                            : __('Semua permission dari enum sudah terdaftar di database.'))
                        ->success()
                        ->send();
                }),
        ];
    }
}
