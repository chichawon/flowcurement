<?php

namespace App\Modules\Items\Policies;

use App\Models\User;
use App\Modules\Items\Helpers\ItemPermissions;
use App\Modules\Items\Models\Item;

class ItemPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->can(ItemPermissions::VIEW);
    }

    public function view(User $actor, Item $item): bool
    {
        return $actor->can(ItemPermissions::VIEW);
    }

    public function create(User $actor): bool
    {
        return $actor->can(ItemPermissions::CREATE);
    }

    public function update(User $actor, Item $item): bool
    {
        return $actor->can(ItemPermissions::UPDATE);
    }

    public function delete(User $actor, Item $item): bool
    {
        return $actor->can(ItemPermissions::DELETE);
    }

    public function restore(User $actor, Item $item): bool
    {
        return $actor->can(ItemPermissions::RESTORE);
    }

    public function forceDelete(User $actor, Item $item): bool
    {
        return $actor->can(ItemPermissions::FORCE_DELETE);
    }
}
