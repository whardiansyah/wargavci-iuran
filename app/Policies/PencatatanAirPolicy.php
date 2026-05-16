<?php

namespace App\Policies;

use App\Models\PencatatanAir;
use App\Models\User;

class PencatatanAirPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PencatatanAir $pencatatanAir): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create_pencatatan_air');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PencatatanAir $pencatatanAir): bool
    {
        return $user->hasPermission('edit_pencatatan_air');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PencatatanAir $pencatatanAir): bool
    {
        return $user->hasPermission('delete_pencatatan_air');
    }

    /**
     * Determine whether the user can export models.
     */
    public function export(User $user): bool
    {
        return $user->hasPermission('export_pencatatan_air');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PencatatanAir $pencatatanAir): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PencatatanAir $pencatatanAir): bool
    {
        return true;
    }
}
