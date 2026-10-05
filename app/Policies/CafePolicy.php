<?php

namespace App\Policies;

use App\Models\Cafe;
use App\Models\User;

class CafePolicy
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
        return true; // Controller will restrict listing to active cafes for customers
    }

    public function view(User $user, Cafe $cafe): bool
    {
        if ($user->isOwner()) {
            return $user->id === $cafe->owner_id;
        }

        if ($user->isCustomer()) {
            return $cafe->status === 'active';
        }

        return false;
    }

    public function create(User $user): bool
    {
        if (! $user->isOwner()) {
            return false;
        }

        // An owner can only create a cafe if they do not already have one
        return ! $user->hasCafe();
    }

    public function update(User $user, Cafe $cafe): bool
    {
        if ($user->isOwner()) {
            return $user->id === $cafe->owner_id;
        }

        return false;
    }

    public function delete(User $user, Cafe $cafe): bool
    {
        if ($user->isOwner()) {
            return $user->id === $cafe->owner_id;
        }

        return false;
    }

    public function restore(User $user, Cafe $cafe): bool
    {
        return false; // Only admin can restore (handled by before())
    }

    public function forceDelete(User $user, Cafe $cafe): bool
    {
        return false; // Only admin can force delete
    }
}
