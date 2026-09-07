<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\QueueTicket;
use App\Models\User;

class QueueTicketPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionType::KELOLA_ANTRIAN->value)
            || $user->can(PermissionType::PANGGIL_ANTRIAN->value);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, QueueTicket $ticket): bool
    {
        return $user->can(PermissionType::KELOLA_ANTRIAN->value)
            || $user->can(PermissionType::PANGGIL_ANTRIAN->value);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can(PermissionType::KELOLA_ANTRIAN->value)
            || $user->can(PermissionType::PANGGIL_ANTRIAN->value);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, QueueTicket $ticket): bool
    {
        return $user->can(PermissionType::KELOLA_ANTRIAN->value)
            || $user->can(PermissionType::PANGGIL_ANTRIAN->value);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, QueueTicket $ticket): bool
    {
        return $user->can(PermissionType::KELOLA_ANTRIAN->value);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, QueueTicket $ticket): bool
    {
        return $user->can(PermissionType::KELOLA_ANTRIAN->value);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, QueueTicket $ticket): bool
    {
        return $user->can(PermissionType::KELOLA_ANTRIAN->value);
    }

    /**
     * Determine whether the user can call or operate queue tickets.
     */
    public function call(User $user, QueueTicket $ticket): bool
    {
        return $user->can(PermissionType::PANGGIL_ANTRIAN->value)
            || $user->can(PermissionType::KELOLA_ANTRIAN->value);
    }
}
