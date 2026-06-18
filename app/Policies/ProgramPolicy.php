<?php

namespace App\Policies;

use App\Models\Program;
use App\Models\User;

class ProgramPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_program');
    }

    public function view(User $user, Program $program): bool
    {
        return $user->hasPermission('view_program');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create_program');
    }

    public function update(User $user, Program $program): bool
    {
        return $user->hasPermission('edit_program');
    }

    public function delete(User $user, Program $program): bool
    {
        return $user->hasPermission('delete_program');
    }
}
