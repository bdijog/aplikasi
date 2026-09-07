<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\ServiceCounter;
use App\Models\User;

class ServiceCounterPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->canManage($user);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ServiceCounter $counter): bool
    {
        return $this->canManage($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->canManage($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ServiceCounter $counter): bool
    {
        return $this->canManage($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ServiceCounter $counter): bool
    {
        return $this->canManage($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ServiceCounter $counter): bool
    {
        return $this->canManage($user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ServiceCounter $counter): bool
    {
        return $this->canManage($user);
    }

    /**
     * Determine whether the user has permission to manage service counters.
     */
    protected function canManage(User $user): bool
    {
        return $user->can(PermissionType::KELOLA_COUNTER->value);
    }
}
