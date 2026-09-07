<?php

namespace Database\Seeders;

use App\Enums\PermissionType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create all permissions from PermissionType enum
        foreach (PermissionType::cases() as $permission) {
            Permission::firstOrCreate([
                'name' => $permission->value,
                'guard_name' => 'web',
            ]);
        }

        // 2. Create default roles and assign permissions
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdminRole->syncPermissions(Permission::all());

        $petugasLoketRole = Role::firstOrCreate(['name' => 'Petugas Loket', 'guard_name' => 'web']);
        $petugasLoketRole->syncPermissions([
            PermissionType::KELOLA_PASIEN->value,
            PermissionType::KELOLA_APPOINTMENT->value,
            PermissionType::PANGGIL_ANTRIAN->value,
            PermissionType::KELOLA_COUNTER->value,
        ]);

        $direkturRole = Role::firstOrCreate(['name' => 'Direktur', 'guard_name' => 'web']);
        $direkturRole->syncPermissions([
            PermissionType::LIHAT_LAPORAN->value,
            PermissionType::KELOLA_PENGUMUMAN->value,
        ]);

        // 3. Assign roles to seeded users if they exist
        $adminUser = User::where('email', 'admin@klinik.test')->first();
        if ($adminUser) {
            $adminUser->assignRole($superAdminRole);
        }

        $loketUser = User::where('email', 'loket1@klinik.test')->first();
        if ($loketUser) {
            $loketUser->assignRole($petugasLoketRole);
        }

        $direkturUser = User::where('email', 'direktur@klinik.test')->first();
        if ($direkturUser) {
            $direkturUser->assignRole($direkturRole);
        }
    }
}
