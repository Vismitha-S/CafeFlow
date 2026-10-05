<?php

namespace Database\Seeders;

use App\Models\Cafe;
use App\Models\CafeHour;
use App\Models\CafeTable;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\User;
use App\Services\CafeRepository;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class CafeFlowDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $demoOwner = $this->demoOwner();

        foreach (CafeRepository::all() as $fixture) {
            $cafe = $this->persistCafe($fixture, $demoOwner);

            if (! $cafe) {
                continue;
            }

            $this->persistHours($cafe, $fixture);
            $this->persistTables($cafe, $fixture['slug']);
            $this->persistMenu($cafe, $fixture['slug']);
        }
    }

    private function demoOwner(): User
    {
        $email = 'cafeflow-demo-owner@example.test';
        $owner = User::query()->where('email', $email)->first();

        if ($owner) {
            if (! $owner->isOwner()) {
                throw new RuntimeException('The reserved CafeFlow demo owner email belongs to a non-owner account.');
            }

            return $owner;
        }

        $owner = new User;
        $owner->forceFill([
            'name' => 'CafeFlow Demo Owner',
            'email' => $email,
            'password' => Hash::make(Str::random(64)),
            'role' => User::ROLE_OWNER,
            'email_verified_at' => now(),
        ])->save();

        return $owner;
    }

    private function persistCafe(array $fixture, User $demoOwner): ?Cafe
    {
        $cafe = Cafe::withTrashed()->where('slug', $fixture['slug'])->first();

        if ($cafe && ($cafe->trashed() || (int) $cafe->owner_id !== (int) $demoOwner->id)) {
            $this->command?->warn("Skipped demo cafe {$fixture['slug']} because that slug is already owned by another account.");

            return null;
        }

        $attributes = [
            'name' => $fixture['name'],
            'slug' => $fixture['slug'],
            'description' => $fixture['short_description'],
            'address' => $fixture['address'],
            'city' => $fixture['location'],
            'phone' => $fixture['phone'] ?? null,
            'email' => $fixture['email'] ?? null,
            'latitude' => $fixture['latitude'] ?? null,
            'longitude' => $fixture['longitude'] ?? null,
            'image_path' => $fixture['image'] ?? null,
            'reservation_fee' => $fixture['reservation_fee'],
            'cancellation_penalty_percentage' => $fixture['cancellation_penalty_percentage'],
            'status' => 'active',
        ];

        $cafe ??= new Cafe;
        $cafe->forceFill($attributes + ['owner_id' => $demoOwner->id])->save();

        return $cafe;
    }

    private function persistHours(Cafe $cafe, array $fixture): void
    {
        foreach ($fixture['opening_hours'] as $dayRange => $hours) {
            if (! preg_match('/^(.+?)\s+-\s+(.+)$/', $hours, $timeParts)) {
                continue;
            }

            $opensAt = now()->parse($timeParts[1])->format('H:i:s');
            $closesAt = now()->parse($timeParts[2])->format('H:i:s');

            foreach ($this->daysInRange($dayRange) as $dayOfWeek) {
                CafeHour::query()->updateOrCreate(
                    [
                        'cafe_id' => $cafe->id,
                        'day_of_week' => $dayOfWeek,
                    ],
                    [
                        'opens_at' => $opensAt,
                        'closes_at' => $closesAt,
                        'is_closed' => false,
                    ],
                );
            }
        }
    }

    private function daysInRange(string $dayRange): array
    {
        $days = [
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6,
            'sunday' => 7,
        ];

        if (strtolower(trim($dayRange)) === 'daily') {
            return range(1, 7);
        }

        $range = preg_split('/\s+-\s+/', strtolower($dayRange));
        if (count($range) !== 2 || ! isset($days[$range[0]], $days[$range[1]])) {
            return [];
        }

        return range($days[$range[0]], $days[$range[1]]);
    }

    private function persistTables(Cafe $cafe, string $slug): void
    {
        foreach (CafeRepository::getTablesForCafe($slug) as $fixture) {
            preg_match_all('/\d+/', $fixture['capacity'], $capacityMatches);
            $capacities = array_map('intval', $capacityMatches[0]);
            $capacity = $capacities ? max($capacities) : 2;

            CafeTable::query()->updateOrCreate(
                [
                    'cafe_id' => $cafe->id,
                    'table_number' => 'T'.$fixture['id'],
                ],
                [
                    'name' => $fixture['name'],
                    'capacity' => $capacity,
                    'location' => strtolower($fixture['location']) === 'indoor' ? 'indoor' : 'outdoor',
                    'status' => 'active',
                ],
            );
        }
    }

    private function persistMenu(Cafe $cafe, string $slug): void
    {
        $items = CafeRepository::getMenuForCafe($slug);
        $categories = [];

        foreach ($items as $sortOrder => $item) {
            $categoryName = $item['category'];
            if (! isset($categories[$categoryName])) {
                $categories[$categoryName] = MenuCategory::query()->updateOrCreate(
                    [
                        'cafe_id' => $cafe->id,
                        'name' => $categoryName,
                    ],
                    [
                        'description' => null,
                        'sort_order' => count($categories),
                        'status' => 'active',
                    ],
                );
            }

            MenuItem::query()->updateOrCreate(
                [
                    'cafe_id' => $cafe->id,
                    'name' => $item['name'],
                ],
                [
                    'menu_category_id' => $categories[$categoryName]->id,
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'image_path' => $item['image'],
                    'is_available' => true,
                    'sort_order' => $sortOrder,
                    'status' => 'active',
                ],
            );
        }
    }
}
