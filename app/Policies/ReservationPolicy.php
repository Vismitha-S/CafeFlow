<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    // Super-admin authorization bypass
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    // Determine whether user can view a list of reservations
    public function viewAny(User $user): bool
    {
        return true;
    }

    // Determine whether user can view the specific reservation
    public function view(User $user, Reservation $reservation): bool
    {
        if ($user->isOwner()) {
            return (int) $user->id === (int) $reservation->cafe->owner_id;
        }

        if ($user->isCustomer()) {
            return (int) $user->id === (int) $reservation->user_id;
        }

        return false;
    }

    // Determine whether user can create reservations
    public function create(User $user): bool
    {
        return $user->isCustomer() || $user->isOwner();
    }

    // Determine whether user can update the reservation
    public function update(User $user, Reservation $reservation): bool
    {
        if ($user->isOwner()) {
            return (int) $user->id === (int) $reservation->cafe->owner_id;
        }

        if ($user->isCustomer()) {
            return (int) $user->id === (int) $reservation->user_id;
        }

        return false;
    }

    // Determine whether user can delete the reservation
    public function delete(User $user, Reservation $reservation): bool
    {
        if ($user->isOwner()) {
            return (int) $user->id === (int) $reservation->cafe->owner_id;
        }

        return false;
    }

    // Determine whether user can cancel the reservation
    public function cancel(User $user, Reservation $reservation): bool
    {
        if ($user->isOwner()) {
            return (int) $user->id === (int) $reservation->cafe->owner_id;
        }

        if ($user->isCustomer()) {
            return (int) $user->id === (int) $reservation->user_id;
        }

        return false;
    }
}
