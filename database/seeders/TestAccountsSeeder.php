<?php

namespace Database\Seeders;

use App\Models\Cafe;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestAccountsSeeder extends Seeder
{
    /**
     * Provision clean, verified test accounts for all user roles:
     * - Admin:     admin@cafeflow.com    / password
     * - Owner:     owner@cafeflow.com    / password (owns 1 cafe: The Velvet Bean)
     * - New Owner: newowner@cafeflow.com / password (owns 0 cafes -> triggers onboarding)
     * - Customer:  customer@cafeflow.com / password
     */
    public function run(): void
    {
        // 1. Admin Account
        User::updateOrCreate(
            ['email' => 'admin@cafeflow.com'],
            [
                'name' => 'CafeFlow Admin',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
            ]
        );

        // 2. Primary Owner Account (1 Owner = 1 Cafe)
        $owner = User::updateOrCreate(
            ['email' => 'owner@cafeflow.com'],
            [
                'name' => 'CafeFlow Owner',
                'password' => Hash::make('password'),
                'role' => User::ROLE_OWNER,
                'email_verified_at' => now(),
            ]
        );

        // 3. New Registered Owner with NO Cafe (For testing onboarding)
        User::updateOrCreate(
            ['email' => 'newowner@cafeflow.com'],
            [
                'name' => 'New Cafe Owner',
                'password' => Hash::make('password'),
                'role' => User::ROLE_OWNER,
                'email_verified_at' => now(),
            ]
        );

        // 4. Customer Account
        User::updateOrCreate(
            ['email' => 'customer@cafeflow.com'],
            [
                'name' => 'CafeFlow Customer',
                'password' => Hash::make('password'),
                'role' => User::ROLE_CUSTOMER,
                'email_verified_at' => now(),
            ]
        );

        // 5. Ensure 1 Owner = 1 Cafe mapping across all cafes in database
        $allCafes = Cafe::all();
        if ($allCafes->isNotEmpty()) {
            // Assign first cafe to primary test owner
            $firstCafe = $allCafes->first();
            $firstCafe->update(['owner_id' => $owner->id]);

            // Assign remaining cafes to distinct owner accounts
            foreach ($allCafes->slice(1) as $idx => $otherCafe) {
                $otherOwner = User::updateOrCreate(
                    ['email' => 'owner'.($idx + 2).'@cafeflow.com'],
                    [
                        'name' => "Owner of {$otherCafe->name}",
                        'password' => Hash::make('password'),
                        'role' => User::ROLE_OWNER,
                        'email_verified_at' => now(),
                    ]
                );
                $otherCafe->update(['owner_id' => $otherOwner->id]);
            }
        }
    }
}
