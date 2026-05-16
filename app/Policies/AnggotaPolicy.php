<?php

namespace App\Policies;

use App\Models\Anggota;
use App\Models\User;

class AnggotaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_anggota');
    }

    public function view(User $user, Anggota $anggota): bool
    {
        return $user->hasPermission('view_anggota');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create_anggota');
    }

    public function update(User $user, Anggota $anggota): bool
    {
        return $user->hasPermission('edit_anggota');
    }

    public function delete(User $user, Anggota $anggota): bool
    {
        return $user->hasPermission('delete_anggota');
    }
}
