<?php

namespace Tests\Feature;

use App\Models\Cafe;
use App\Models\CafeHour;
use App\Models\CafeTable;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\User;
use App\Services\CafeRepository;
use App\Services\ReservationAvailabilityService;
use Database\Seeders\CafeFlowDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CafeFlowDemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_persists_all_fixture_cafes_and_is_idempotent(): void
    {
        $existingOwner = User::factory()->create(['role' => User::ROLE_OWNER]);
        $existingCafe = Cafe::factory()->create([
            'owner_id' => $existingOwner->id,
            'name' => 'Existing User Cafe',
            'slug' => 'existing-user-cafe',
            'status' => 'active',
            'reservation_fee' => '88.00',
        ]);
        $existingTable = CafeTable::factory()->create([
            'cafe_id' => $existingCafe->id,
            'table_number' => 'USER-1',
            'name' => 'User Table',
        ]);

        $this->seed();
        $firstCounts = [
            'cafes' => Cafe::query()->count(),
            'hours' => CafeHour::query()->count(),
            'tables' => CafeTable::query()->count(),
            'categories' => MenuCategory::query()->count(),
            'items' => MenuItem::query()->count(),
            'owners' => User::query()->where('role', User::ROLE_OWNER)->count(),
            'test_users' => User::query()->where('email', 'test@example.com')->count(),
        ];

        $this->seed();
        $secondCounts = [
            'cafes' => Cafe::query()->count(),
            'hours' => CafeHour::query()->count(),
            'tables' => CafeTable::query()->count(),
            'categories' => MenuCategory::query()->count(),
            'items' => MenuItem::query()->count(),
            'owners' => User::query()->where('role', User::ROLE_OWNER)->count(),
            'test_users' => User::query()->where('email', 'test@example.com')->count(),
        ];

        $this->assertSame($firstCounts, $secondCounts);
        $this->assertSame(7, $secondCounts['cafes']);
        $this->assertSame(42, $secondCounts['hours']);
        $this->assertSame(31, $secondCounts['tables']);
        $this->assertSame(30, $secondCounts['categories']);
        $this->assertSame(48, $secondCounts['items']);
        $this->assertSame(1, $secondCounts['test_users']);

        $this->assertSame('Existing User Cafe', $existingCafe->fresh()->name);
        $this->assertSame('88.00', $existingCafe->fresh()->reservation_fee);
        $this->assertDatabaseHas('cafe_tables', ['id' => $existingTable->id, 'table_number' => 'USER-1']);

        $demoOwner = User::query()->where('email', 'cafeflow-demo-owner@example.test')->firstOrFail();
        $this->assertTrue($demoOwner->isOwner());

        foreach (CafeRepository::all() as $fixture) {
            $cafe = Cafe::query()->where('slug', $fixture['slug'])->firstOrFail();

            $this->assertSame($fixture['name'], $cafe->name);
            $this->assertSame('active', $cafe->status);
            $this->assertSame($demoOwner->id, $cafe->owner_id);
            $this->assertSame(7, $cafe->hours()->count());
            $this->assertSame(5, $cafe->tables()->count());
            $this->assertSame(5, $cafe->menuCategories()->count());
            $this->assertSame(8, $cafe->menuItems()->count());
        }
    }

    public function test_existing_cafe_with_fixture_slug_owned_by_user_is_not_overwritten(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_OWNER]);
        $existingCafe = Cafe::factory()->create([
            'owner_id' => $owner->id,
            'name' => 'Owner Sunrise Roasters',
            'slug' => 'sunrise-roasters',
            'status' => 'inactive',
            'reservation_fee' => '123.45',
        ]);
        $existingTable = CafeTable::factory()->create([
            'cafe_id' => $existingCafe->id,
            'table_number' => 'OWNER-1',
            'name' => 'Owner Table',
        ]);

        $this->seed(CafeFlowDemoSeeder::class);

        $this->assertSame('Owner Sunrise Roasters', $existingCafe->fresh()->name);
        $this->assertSame('inactive', $existingCafe->fresh()->status);
        $this->assertSame('123.45', $existingCafe->fresh()->reservation_fee);
        $this->assertSame($owner->id, $existingCafe->fresh()->owner_id);
        $this->assertDatabaseHas('cafe_tables', [
            'id' => $existingTable->id,
            'cafe_id' => $existingCafe->id,
            'table_number' => 'OWNER-1',
        ]);
        $this->assertSame(6, Cafe::query()->count());
    }

    public function test_sunrise_roasters_has_real_availability_for_october_fifth(): void
    {
        $this->seed(CafeFlowDemoSeeder::class);
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $sunriseRoasters = Cafe::query()->where('slug', 'sunrise-roasters')->firstOrFail();

        $tables = app(ReservationAvailabilityService::class)->getAvailableTables(
            $sunriseRoasters,
            '2026-10-05',
            '10:30',
            2,
        );

        $this->assertNotEmpty($tables);
        $mondayHours = $sunriseRoasters->hours()->where('day_of_week', 1)->firstOrFail();
        $this->assertFalse($mondayHours->is_closed);
        $this->assertSame('07:00:00', $mondayHours->opens_at);
        $this->assertSame('20:00:00', $mondayHours->closes_at);

        $availabilityResponse = $this->actingAs($customer)->getJson(route('cafes.availability', [
            'cafe' => $sunriseRoasters->id,
            'date' => '2026-10-05',
            'time' => '10:30',
            'guests' => 2,
        ]));

        $availabilityResponse->assertOk()
            ->assertJsonPath('cafe.id', $sunriseRoasters->id)
            ->assertJsonPath('requested_date', '2026-10-05')
            ->assertJsonPath('requested_start_time', '10:30')
            ->assertJsonPath('guest_count', 2)
            ->assertJsonCount(5, 'available_tables');

        $slotsResponse = $this->actingAs($customer)->getJson(route('cafes.availability', [
            'cafe' => $sunriseRoasters->id,
            'date' => '2026-10-05',
            'guests' => 2,
            'slots' => 1,
        ]));
        $slotsResponse->assertOk()
            ->assertJsonPath('time_slots.0.value', '07:00')
            ->assertJsonFragment(['value' => '10:30', 'label' => '10:30 AM']);

        $pageResponse = $this->actingAs($customer)
            ->get(route('customer.cafe.show', [
                'slug' => 'sunrise-roasters',
                'date' => '2026-10-05',
                'time' => '10:30 AM',
                'guests' => '2 Guests',
            ]));
        $pageHtml = $pageResponse->getContent();

        $this->assertSame(200, $pageResponse->status(), 'Cafe Details should render for the persisted demo cafe.');
        $this->assertTrue(str_contains($pageHtml, 'availabilityUrl'), 'Cafe Details should receive an availability endpoint.');
        $pageResponse->assertViewHas('availabilityUrl', route('cafes.availability', $sunriseRoasters));
        $this->assertTrue(str_contains($pageHtml, 'Table 1'), 'Persisted demo tables should render.');
        $this->assertTrue(str_contains($pageHtml, 'Cappuccino'), 'Persisted demo menu rows should render.');
        $this->assertTrue(str_contains($pageHtml, 'LKR 750.00'), 'Menu prices should be rendered from persisted rows.');
        $this->assertFalse(str_contains($pageHtml, 'Table reservations are not available for this cafe right now.'), 'The unavailable message should be absent when tables are available.');
    }

    public function test_brew_and_beyond_uses_its_persisted_hours_tables_and_menu(): void
    {
        $this->seed(CafeFlowDemoSeeder::class);
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $cafe = Cafe::query()->where('slug', 'brew-and-beyond')->firstOrFail();
        $availabilityService = app(ReservationAvailabilityService::class);

        $slots = $availabilityService->getAvailableTimeSlots($cafe, '2026-10-06', 4);
        $tables = $availabilityService->getAvailableTables($cafe, '2026-10-06', '10:00', 4);

        $this->assertNotEmpty($slots);
        $this->assertNotEmpty($tables);
        $this->assertSame(4, $tables->count());

        $this->actingAs($customer)
            ->get(route('customer.cafe.show', [
                'slug' => $cafe->slug,
                'date' => '2026-10-06',
                'time' => '10:00 AM',
                'guests' => '4 Guests',
            ]))
            ->assertOk()
            ->assertSee('Brew & Beyond')
            ->assertSee('Table 1')
            ->assertSee('Cappuccino')
            ->assertDontSee('Table reservations are not available for this cafe right now.');
    }
}
