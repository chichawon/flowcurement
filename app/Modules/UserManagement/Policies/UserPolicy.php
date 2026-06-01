<?php

namespace App\Modules\UserManagement\Policies;

use App\Models\User;
use App\Modules\UserManagement\Helpers\UserManagementPermissions;

class UserPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->can(UserManagementPermissions::VIEW);
    }

    public function view(User $actor, User $user): bool
    {
        return $actor->can(UserManagementPermissions::VIEW);
    }

    public function create(User $actor): bool
    {
        return $actor->can(UserManagementPermissions::CREATE);
    }

    public function update(User $actor, User $user): bool
    {
        return $actor->can(UserManagementPermissions::UPDATE);
    }

    public function delete(User $actor, User $user): bool
    {
        return $actor->id !== $user->id && $actor->can(UserManagementPermissions::DELETE);
    }

    public function restore(User $actor, User $user): bool
    {
        return $actor->can(UserManagementPermissions::RESTORE);
    }

    public function forceDelete(User $actor, User $user): bool
    {
        return $actor->id !== $user->id && $actor->can(UserManagementPermissions::FORCE_DELETE);
    }

    public function assignRoles(User $actor, User $user): bool
    {
        return $actor->can(UserManagementPermissions::ASSIGN_ROLES);
    }

    public function assignPermissions(User $actor, User $user): bool
    {
        return $actor->can(UserManagementPermissions::ASSIGN_PERMISSIONS);
    }
}
