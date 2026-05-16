<?php

namespace App\Policies;

use App\Models\Tagihan;
use App\Models\User;

class TagihanPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_tagihan');
    }

    public function view(User $user, Tagihan $tagihan): bool
    {
        return $user->hasPermission('view_tagihan');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create_tagihan');
    }

    public function update(User $user, Tagihan $tagihan): bool
    {
        return $user->hasPermission('edit_tagihan');
    }

    public function delete(User $user, Tagihan $tagihan): bool
    {
        return $user->hasPermission('delete_tagihan');
    }

    public function reset(User $user): bool
    {
        return $user->hasPermission('delete_tagihan');
    }
}
