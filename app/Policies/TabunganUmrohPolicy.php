<?php

namespace App\Policies;

use App\Models\TabunganUmroh;
use App\Models\User;

class TabunganUmrohPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_tabungan_umroh');
    }

    public function view(User $user, TabunganUmroh $tabunganUmroh): bool
    {
        return $user->hasPermission('view_tabungan_umroh');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create_tabungan_umroh');
    }

    public function update(User $user, TabunganUmroh $tabunganUmroh): bool
    {
        return $user->hasPermission('edit_tabungan_umroh');
    }

    public function delete(User $user, TabunganUmroh $tabunganUmroh): bool
    {
        return $user->hasPermission('delete_tabungan_umroh');
    }
}
