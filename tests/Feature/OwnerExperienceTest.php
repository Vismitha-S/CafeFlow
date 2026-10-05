<?php

namespace Tests\Feature;

use App\Models\Cafe;
use App\Models\CafeHour;
use App\Models\CafeTable;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_without_cafe_is_redirected_to_onboarding_page(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_OWNER]);

        $response = $this->actingAs($owner)->get(route('owner.dashboard'));

        $response->assertRedirect(route('owner.cafe.create'));
    }

    public function test_owner_can_view_onboarding_page(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_OWNER]);

        $response = $this->actingAs($owner)->get(route('owner.cafe.create'));

        $response->assertStatus(200);
        $response->assertSee('Create Your Cafe Profile');
    }

    public function test_owner_can_create_cafe_and_is_redirected_to_dashboard(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_OWNER]);

        $response = $this->actingAs($owner)->post(route('owner.cafe.store'), [
            'name' => 'Artisan Roastery',
            'slug' => 'artisan-roastery',
            'city' => 'Colombo 07',
            'address' => '15 Flower Road',
            'phone' => '+94 11 234 5678',
            'email' => 'artisan@roastery.lk',
            'reservation_fee' => 500,
            'cancellation_penalty_percentage' => 50,
            'status' => 'active',
            'hours' => [
                1 => ['day_of_week' => 1, 'opens_at' => '08:00', 'closes_at' => '21:00', 'is_closed' => false],
                2 => ['day_of_week' => 2, 'opens_at' => '08:00', 'closes_at' => '21:00', 'is_closed' => false],
            ],
        ]);

        $response->assertRedirect(route('owner.dashboard'));
        $this->assertDatabaseHas('cafes', [
            'name' => 'Artisan Roastery',
            'owner_id' => $owner->id,
            'status' => 'active',
        ]);
    }

    public function test_owner_with_existing_cafe_cannot_create_second_cafe(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_OWNER]);
        Cafe::factory()->create(['owner_id' => $owner->id]);

        $response = $this->actingAs($owner)->get(route('owner.cafe.create'));
        $response->assertRedirect(route('owner.dashboard'));

        $storeResponse = $this->actingAs($owner)->post(route('owner.cafe.store'), [
            'name' => 'Second Cafe',
            'slug' => 'second-cafe',
            'city' => 'Kandy',
            'address' => '10 Lake Road',
            'reservation_fee' => 500,
            'cancellation_penalty_percentage' => 50,
            'status' => 'active',
        ]);

        $storeResponse->assertRedirect(route('owner.dashboard'));
        $this->assertDatabaseMissing('cafes', ['name' => 'Second Cafe']);
    }

    public function test_owner_can_manage_tables(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_OWNER]);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);

        // 1. Add Table
        $response = $this->actingAs($owner)->post(route('owner.tables.store'), [
            'table_number' => '1',
            'name' => 'Window Corner',
            'capacity' => 4,
            'location' => 'indoor',
            'status' => 'active',
        ]);
        $response->assertRedirect(route('owner.tables.index'));

        $table = CafeTable::where('cafe_id', $cafe->id)->first();
        $this->assertNotNull($table);
        $this->assertEquals('Window Corner', $table->name);

        // 2. Toggle Status
        $this->actingAs($owner)->patch(route('owner.tables.toggle', $table->id));
        $this->assertEquals('inactive', $table->fresh()->status);

        // 3. Delete Table
        $this->actingAs($owner)->delete(route('owner.tables.destroy', $table->id));
        $this->assertSoftDeleted($table);
    }

    public function test_owner_cannot_modify_another_owners_table(): void
    {
        $owner1 = User::factory()->create(['role' => User::ROLE_OWNER]);
        $cafe1 = Cafe::factory()->create(['owner_id' => $owner1->id]);

        $owner2 = User::factory()->create(['role' => User::ROLE_OWNER]);
        $cafe2 = Cafe::factory()->create(['owner_id' => $owner2->id]);
        $table2 = CafeTable::factory()->create(['cafe_id' => $cafe2->id]);

        // Attempt delete
        $response = $this->actingAs($owner1)->delete(route('owner.tables.destroy', $table2->id));
        $response->assertStatus(403);
    }

    public function test_owner_can_manage_menu_categories_and_items(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_OWNER]);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);

        // 1. Add Category
        $catResponse = $this->actingAs($owner)->post(route('owner.menu.categories.store'), [
            'name' => 'Signature Coffees',
            'status' => 'active',
        ]);
        $catResponse->assertRedirect(route('owner.menu.index'));

        $category = MenuCategory::where('cafe_id', $cafe->id)->first();
        $this->assertNotNull($category);

        // 2. Add Menu Item
        $itemResponse = $this->actingAs($owner)->post(route('owner.menu.items.store'), [
            'name' => 'Caramel Cortado',
            'description' => 'Espresso with caramel and micro-foam',
            'price' => 750,
            'menu_category_id' => $category->id,
            'status' => 'active',
            'is_available' => true,
        ]);
        $itemResponse->assertRedirect();

        $item = MenuItem::where('cafe_id', $cafe->id)->first();
        $this->assertNotNull($item);
        $this->assertEquals('Caramel Cortado', $item->name);

        // 3. Toggle availability
        $this->actingAs($owner)->patch(route('owner.menu.items.toggle', $item->id));
        $this->assertFalse($item->fresh()->is_available);
    }

    public function test_reservation_creation_notifies_cafe_owner(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_OWNER]);
        $cafe = Cafe::factory()->create([
            'owner_id' => $owner->id,
            'status' => 'active',
            'reservation_fee' => 500,
        ]);

        for ($day = 1; $day <= 7; $day++) {
            CafeHour::create([
                'cafe_id' => $cafe->id,
                'day_of_week' => $day,
                'opens_at' => '08:00',
                'closes_at' => '22:00',
                'is_closed' => false,
            ]);
        }

        $table = CafeTable::factory()->create([
            'cafe_id' => $cafe->id,
            'status' => 'active',
            'capacity' => 4,
        ]);

        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);

        $response = $this->actingAs($customer)->post(route('cafes.reservations.store', $cafe->id), [
            'cafe_table_id' => $table->id,
            'reservation_date' => now()->addDays(2)->format('Y-m-d'),
            'start_time' => '14:00',
            'guest_count' => 2,
        ]);

        // Check owner received database notification
        $this->assertCount(1, $owner->fresh()->notifications);
        $notification = $owner->fresh()->notifications->first();
        $this->assertEquals('New Reservation Received', $notification->data['title']);
        $this->assertEquals($customer->name, $notification->data['customer_name']);

        // Owner marks as read
        $readResponse = $this->actingAs($owner)->post(route('owner.notifications.read', $notification->id));
        $this->assertTrue($notification->fresh()->read());
    }

    public function test_owner_can_update_cafe_details(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_OWNER]);
        $cafe = Cafe::factory()->create([
            'owner_id' => $owner->id,
            'slug' => 'original-slug',
            'name' => 'Original Cafe Name',
            'city' => 'Colombo',
            'address' => '123 Main St',
            'reservation_fee' => 300,
            'cancellation_penalty_percentage' => 50,
            'status' => 'active',
        ]);

        $response = $this->actingAs($owner)->put(route('owner.cafe.update'), [
            'name' => 'Updated Cafe Name',
            'slug' => 'original-slug', // Keep same slug, verifying unique ignore works
            'city' => 'Galle',
            'address' => '456 Beach Road',
            'phone' => '+94 77 123 4567',
            'email' => 'contact@gallecafe.lk',
            'reservation_fee' => 450,
            'cancellation_penalty_percentage' => 35,
            'status' => 'active',
            'hours' => [
                1 => ['day_of_week' => 1, 'opens_at' => '09:00', 'closes_at' => '22:00', 'is_closed' => false],
            ],
        ]);

        $response->assertRedirect(route('owner.dashboard'));
        $response->assertSessionHas('success');

        $cafe->refresh();
        $this->assertSame('Updated Cafe Name', $cafe->name);
        $this->assertSame('Galle', $cafe->city);
        $this->assertSame('450.00', (string) $cafe->reservation_fee);
        $this->assertSame('35.00', (string) $cafe->cancellation_penalty_percentage);
    }

    public function test_owner_created_cafe_appears_on_customer_dashboard_explore_and_details(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_OWNER]);
        $cafe = Cafe::factory()->create([
            'owner_id' => $owner->id,
            'slug' => 'custom-artisan-cafe',
            'name' => 'Custom Artisan Cafe',
            'city' => 'Negombo',
            'address' => '77 Beach Way',
            'status' => 'active',
            'reservation_fee' => 350,
        ]);

        $table = CafeTable::factory()->create([
            'cafe_id' => $cafe->id,
            'table_number' => '1',
            'name' => 'Sea Breeze Table',
            'capacity' => 2,
            'location' => 'outdoor',
            'status' => 'active',
        ]);

        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);

        // 1. Appears on Customer Dashboard
        $dashboardResponse = $this->actingAs($customer)->get(route('customer.dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Custom Artisan Cafe');

        // 2. Appears on Explore page
        $exploreResponse = $this->actingAs($customer)->get(route('customer.explore'));
        $exploreResponse->assertStatus(200);
        $exploreResponse->assertSee('Custom Artisan Cafe');

        // 3. Cafe details page renders successfully without 404
        $detailsResponse = $this->actingAs($customer)->get(route('customer.cafe.show', 'custom-artisan-cafe'));
        $detailsResponse->assertStatus(200);
        $detailsResponse->assertSee('Custom Artisan Cafe');
        $detailsResponse->assertSee('Sea Breeze Table');
    }
}
