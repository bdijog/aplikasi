<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionType::KELOLA_PENGGUNA->value);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Role $role): bool
    {
        return $user->can(PermissionType::KELOLA_PENGGUNA->value);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can(PermissionType::KELOLA_PENGGUNA->value);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $role): bool
    {
        return $user->can(PermissionType::KELOLA_PENGGUNA->value);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $role): bool
    {
        // Role Super Admin dilindungi dari penghapusan tidak sengaja
        return $user->can(PermissionType::KELOLA_PENGGUNA->value)
            && $role->name !== 'Super Admin';
    }
}
