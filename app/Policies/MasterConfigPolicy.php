<?php

namespace App\Policies;

use App\Models\MasterConfig;
use App\Models\User;

class MasterConfigPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_master_configs');
    }

    public function view(User $user, MasterConfig $masterConfig): bool
    {
        return $user->hasPermission('view_master_configs');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create_master_configs');
    }

    public function update(User $user, MasterConfig $masterConfig): bool
    {
        return $user->hasPermission('edit_master_configs');
    }

    public function delete(User $user, MasterConfig $masterConfig): bool
    {
        return $user->hasPermission('delete_master_configs');
    }
}
