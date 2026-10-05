<?php

namespace App\Policies;

use App\Models\MenuItem;
use App\Models\User;

class MenuItemPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MenuItem $menuItem): bool
    {
        if ($user->isOwner()) {
            return $user->id === $menuItem->cafe->owner_id;
        }

        if ($user->isCustomer()) {
            $isCategoryActive = true;
            if ($menuItem->category) {
                $isCategoryActive = $menuItem->category->status === 'active';
            }

            return $menuItem->status === 'active' && $menuItem->is_available && $menuItem->cafe->status === 'active' && $isCategoryActive;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isOwner(); // Admin handled by before
    }

    public function update(User $user, MenuItem $menuItem): bool
    {
        if ($user->isOwner()) {
            return $user->id === $menuItem->cafe->owner_id;
        }

        return false;
    }

    public function delete(User $user, MenuItem $menuItem): bool
    {
        if ($user->isOwner()) {
            return $user->id === $menuItem->cafe->owner_id;
        }

        return false;
    }

    public function restore(User $user, MenuItem $menuItem): bool
    {
        return false;
    }

    public function forceDelete(User $user, MenuItem $menuItem): bool
    {
        return false;
    }
}
