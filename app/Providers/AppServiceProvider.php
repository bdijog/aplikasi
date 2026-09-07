<?php

namespace App\Providers;

use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Daftarkan policy untuk model Spatie Permission & Role
        Gate::policy(Role::class, \App\Policies\RolePolicy::class);
        Gate::policy(Permission::class, \App\Policies\PermissionPolicy::class);

        // Otomatis bersihkan cache Spatie saat Role atau Permission disimpan / dihapus
        Role::saved(fn () => app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions());
        Role::deleted(fn () => app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions());
        Permission::saved(fn () => app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions());
        Permission::deleted(fn () => app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions());

        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch->locales(['id', 'en']);
        });
    }
}
