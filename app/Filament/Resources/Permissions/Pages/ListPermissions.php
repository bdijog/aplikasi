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
                ->label(__('Sync from Enum'))
                ->icon(Heroicon::OutlinedArrowPath)
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading(__('Sync Permissions from Enum'))
                ->modalDescription(__('This action will register all permissions from the PermissionType enum to the database.'))
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
                        ->title(__('Sync Successful'))
                        ->body($createdCount > 0
                            ? __(':count new permissions were successfully added.', ['count' => $createdCount])
                            : __('All permissions from the enum are already registered in the database.'))
                        ->success()
                        ->send();
                }),
        ];
    }
}
