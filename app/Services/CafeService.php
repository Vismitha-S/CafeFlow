<?php

namespace App\Services;

use App\Models\Cafe;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class CafeService
{
    /**
     * Create a new cafe.
     */
    public function createCafe(User $user, array $data): Cafe
    {
        return DB::transaction(function () use ($user, $data) {
            $cafeData = Arr::except($data, ['hours']);

            // Set the owner securely
            if ($user->isAdmin() && isset($data['owner_id'])) {
                $cafeData['owner_id'] = $data['owner_id'];
            } else {
                $cafeData['owner_id'] = $user->id;
            }

            $cafe = Cafe::create($cafeData);

            if (isset($data['hours']) && is_array($data['hours'])) {
                $this->syncHours($cafe, $data['hours']);
            }

            return $cafe;
        });
    }

    /**
     * Update an existing cafe.
     */
    public function updateCafe(User $user, Cafe $cafe, array $data): Cafe
    {
        return DB::transaction(function () use ($user, $cafe, $data) {
            $cafeData = Arr::except($data, ['hours']);

            // Only admin can change the owner
            if ($user->isAdmin() && isset($data['owner_id'])) {
                $cafeData['owner_id'] = $data['owner_id'];
            } else {
                unset($cafeData['owner_id']);
            }

            $cafe->update($cafeData);

            if (isset($data['hours']) && is_array($data['hours'])) {
                $this->syncHours($cafe, $data['hours']);
            }

            return $cafe->fresh();
        });
    }

    /**
     * Sync cafe hours.
     */
    protected function syncHours(Cafe $cafe, array $hours): void
    {
        // We delete existing hours for simplicity, or we can use updateOrCreate
        // However, a simple approach is to iterate and use updateOrCreate for each day
        foreach ($hours as $hourData) {
            $cafe->hours()->updateOrCreate(
                ['day_of_week' => $hourData['day_of_week']],
                [
                    'opens_at' => $hourData['opens_at'] ?? null,
                    'closes_at' => $hourData['closes_at'] ?? null,
                    'is_closed' => $hourData['is_closed'] ?? false,
                ]
            );
        }
    }
}
