<?php

namespace App\Policies;

use App\Models\MenuCategory;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MenuCategoryPolicy
{
    public function before(User $user, string $ability): bool|null
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

    public function view(User $user, MenuCategory $menuCategory): bool
    {
        if ($user->isOwner()) {
            return $user->id === $menuCategory->cafe->owner_id;
        }

        if ($user->isCustomer()) {
            return $menuCategory->status === 'active' && $menuCategory->cafe->status === 'active';
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isOwner(); // Admin handled by before
    }

    public function update(User $user, MenuCategory $menuCategory): bool
    {
        if ($user->isOwner()) {
            return $user->id === $menuCategory->cafe->owner_id;
        }

        return false;
    }

    public function delete(User $user, MenuCategory $menuCategory): bool
    {
        if ($user->isOwner()) {
            return $user->id === $menuCategory->cafe->owner_id;
        }

        return false;
    }

    public function restore(User $user, MenuCategory $menuCategory): bool
    {
        return false;
    }

    public function forceDelete(User $user, MenuCategory $menuCategory): bool
    {
        return false;
    }
}
