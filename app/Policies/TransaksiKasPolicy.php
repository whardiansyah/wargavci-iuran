<?php

namespace App\Policies;

use App\Models\TransaksiKas;
use App\Models\User;

class TransaksiKasPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_transaksi_kas');
    }

    public function view(User $user, TransaksiKas $transaksiKas): bool
    {
        return $user->hasPermission('view_transaksi_kas');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create_transaksi_kas');
    }

    public function update(User $user, TransaksiKas $transaksiKas): bool
    {
        return $user->hasPermission('edit_transaksi_kas');
    }

    public function delete(User $user, TransaksiKas $transaksiKas): bool
    {
        return $user->hasPermission('delete_transaksi_kas');
    }

    public function export(User $user): bool
    {
        return $user->hasPermission('export_transaksi_kas');
    }
}
