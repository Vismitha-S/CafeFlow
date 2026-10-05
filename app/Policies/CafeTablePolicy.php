<?php

namespace App\Policies;

use App\Models\CafeTable;
use App\Models\User;

class CafeTablePolicy
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
        // Handled in controller/queries
        return true;
    }

    public function view(User $user, CafeTable $cafeTable): bool
    {
        if ($user->isOwner()) {
            return $user->id === $cafeTable->cafe->owner_id;
        }

        if ($user->isCustomer()) {
            return $cafeTable->status === 'active' && $cafeTable->cafe->status === 'active';
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isOwner(); // Admin handled by before
    }

    public function update(User $user, CafeTable $cafeTable): bool
    {
        if ($user->isOwner()) {
            return $user->id === $cafeTable->cafe->owner_id;
        }

        return false;
    }

    public function delete(User $user, CafeTable $cafeTable): bool
    {
        if ($user->isOwner()) {
            return $user->id === $cafeTable->cafe->owner_id;
        }

        return false;
    }

    public function restore(User $user, CafeTable $cafeTable): bool
    {
        return false;
    }

    public function forceDelete(User $user, CafeTable $cafeTable): bool
    {
        return false;
    }
}
