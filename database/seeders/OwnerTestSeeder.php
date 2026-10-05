<?php

namespace Database\Seeders;

use App\Models\Cafe;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OwnerTestSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::updateOrCreate(
            ['email' => 'owner@cafeflow.com'],
            [
                'name' => 'CafeFlow Owner Test',
                'password' => Hash::make('password'),
                'role' => User::ROLE_OWNER,
                'email_verified_at' => now(),
            ]
        );

        Cafe::where('owner_id', 8)
            ->orWhereNull('owner_id')
            ->update(['owner_id' => $owner->id]);
    }
}
