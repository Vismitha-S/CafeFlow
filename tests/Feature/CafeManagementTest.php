<?php

namespace Tests\Feature;

use App\Models\Cafe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CafeManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    private function validCafeData($overrides = [])
    {
        return array_merge([
            'name' => 'Test Cafe',
            'slug' => 'test-cafe',
            'address' => '123 Test St',
            'city' => 'Test City',
            'reservation_fee' => 10,
            'cancellation_penalty_percentage' => 50,
            'status' => 'active',
        ], $overrides);
    }

    public function test_admin_can_create_a_cafe()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['role' => 'owner']);

        $response = $this->actingAs($admin)->postJson('/cafes', $this->validCafeData([
            'owner_id' => $owner->id,
        ]));

        $response->assertStatus(201);
        $this->assertDatabaseHas('cafes', ['slug' => 'test-cafe', 'owner_id' => $owner->id]);
    }

    public function test_owner_can_create_a_cafe()
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $response = $this->actingAs($owner)->postJson('/cafes', $this->validCafeData());

        $response->assertStatus(201);
        $this->assertDatabaseHas('cafes', ['slug' => 'test-cafe', 'owner_id' => $owner->id]);
    }

    public function test_customer_cannot_create_a_cafe()
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->postJson('/cafes', $this->validCafeData());

        $response->assertStatus(403);
    }

    public function test_owner_can_view_their_cafe()
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);

        $response = $this->actingAs($owner)->getJson("/cafes/{$cafe->id}");

        $response->assertStatus(200);
    }

    public function test_owner_can_update_their_cafe()
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);

        $response = $this->actingAs($owner)->putJson("/cafes/{$cafe->id}", $this->validCafeData([
            'name' => 'Updated Name',
            'slug' => $cafe->slug,
        ]));

        $response->assertStatus(200);
        $this->assertDatabaseHas('cafes', ['id' => $cafe->id, 'name' => 'Updated Name']);
    }

    public function test_owner_cannot_update_another_owners_cafe()
    {
        $owner1 = User::factory()->create(['role' => 'owner']);
        $owner2 = User::factory()->create(['role' => 'owner']);
        $cafe = Cafe::factory()->create(['owner_id' => $owner2->id]);

        $response = $this->actingAs($owner1)->putJson("/cafes/{$cafe->id}", $this->validCafeData());

        $response->assertStatus(403);
    }

    public function test_owner_cannot_delete_another_owners_cafe()
    {
        $owner1 = User::factory()->create(['role' => 'owner']);
        $owner2 = User::factory()->create(['role' => 'owner']);
        $cafe = Cafe::factory()->create(['owner_id' => $owner2->id]);

        $response = $this->actingAs($owner1)->deleteJson("/cafes/{$cafe->id}");

        $response->assertStatus(403);
    }

    public function test_owner_can_delete_their_own_cafe()
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);

        $response = $this->actingAs($owner)->deleteJson("/cafes/{$cafe->id}");

        $response->assertStatus(204);
        $this->assertSoftDeleted($cafe);
    }

    public function test_admin_can_update_any_cafe()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cafe = Cafe::factory()->create();

        $response = $this->actingAs($admin)->putJson("/cafes/{$cafe->id}", $this->validCafeData([
            'name' => 'Admin Updated',
            'slug' => $cafe->slug,
        ]));

        $response->assertStatus(200);
        $this->assertDatabaseHas('cafes', ['id' => $cafe->id, 'name' => 'Admin Updated']);
    }

    public function test_admin_can_delete_any_cafe()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cafe = Cafe::factory()->create();

        $response = $this->actingAs($admin)->deleteJson("/cafes/{$cafe->id}");

        $response->assertStatus(204);
        $this->assertSoftDeleted($cafe);
    }

    public function test_customer_can_view_active_cafes()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        Cafe::factory()->create(['status' => 'active']);

        $response = $this->actingAs($customer)->getJson('/cafes');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json());
    }

    public function test_inactive_cafes_are_excluded_from_customer_listings()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        Cafe::factory()->create(['status' => 'active']);
        Cafe::factory()->create(['status' => 'inactive']);

        $response = $this->actingAs($customer)->getJson('/cafes');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json());
    }

    public function test_invalid_reservation_fee_is_rejected()
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $response = $this->actingAs($owner)->postJson('/cafes', $this->validCafeData([
            'reservation_fee' => -10,
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('reservation_fee');
    }

    public function test_cancellation_percentage_below_0_is_rejected()
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $response = $this->actingAs($owner)->postJson('/cafes', $this->validCafeData([
            'cancellation_penalty_percentage' => -1,
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('cancellation_penalty_percentage');
    }

    public function test_cancellation_percentage_above_100_is_rejected()
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $response = $this->actingAs($owner)->postJson('/cafes', $this->validCafeData([
            'cancellation_penalty_percentage' => 101,
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('cancellation_penalty_percentage');
    }

    public function test_duplicate_slug_is_rejected()
    {
        Cafe::factory()->create(['slug' => 'duplicate-slug']);
        $owner = User::factory()->create(['role' => 'owner']);

        $response = $this->actingAs($owner)->postJson('/cafes', $this->validCafeData([
            'slug' => 'duplicate-slug',
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('slug');
    }

    public function test_invalid_latitude_longitude_is_rejected()
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $response = $this->actingAs($owner)->postJson('/cafes', $this->validCafeData([
            'latitude' => 100, // Invalid, max 90
            'longitude' => 200, // Invalid, max 180
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['latitude', 'longitude']);
    }

    public function test_opening_hours_are_stored_correctly()
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $response = $this->actingAs($owner)->postJson('/cafes', $this->validCafeData([
            'hours' => [
                [
                    'day_of_week' => 1,
                    'opens_at' => '08:00',
                    'closes_at' => '17:00',
                    'is_closed' => false,
                ],
            ],
        ]));

        $response->assertStatus(201);
        $this->assertDatabaseHas('cafe_hours', ['day_of_week' => 1, 'opens_at' => '08:00']);
    }

    public function test_closed_day_can_exist_without_opening_closing_times()
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $response = $this->actingAs($owner)->postJson('/cafes', $this->validCafeData([
            'hours' => [
                [
                    'day_of_week' => 7,
                    'is_closed' => true,
                ],
            ],
        ]));

        $response->assertStatus(201);
        $this->assertDatabaseHas('cafe_hours', ['day_of_week' => 7, 'is_closed' => true]);
    }

    public function test_invalid_opening_closing_times_are_rejected()
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $response = $this->actingAs($owner)->postJson('/cafes', $this->validCafeData([
            'hours' => [
                [
                    'day_of_week' => 1,
                    'opens_at' => '17:00',
                    'closes_at' => '08:00', // Closes before opens
                    'is_closed' => false,
                ],
            ],
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('hours.0.closes_at');
    }

    public function test_owner_id_cannot_be_spoofed()
    {
        $owner1 = User::factory()->create(['role' => 'owner']);
        $owner2 = User::factory()->create(['role' => 'owner']);

        $response = $this->actingAs($owner1)->postJson('/cafes', $this->validCafeData([
            'owner_id' => $owner2->id,
        ]));

        $response->assertStatus(201);
        // Should ignore owner_id in request and use authenticated user's ID
        $this->assertDatabaseHas('cafes', ['owner_id' => $owner1->id]);
    }

    public function test_unauthenticated_users_cannot_access_protected_management_actions()
    {
        $response = $this->postJson('/cafes', $this->validCafeData());

        $response->assertStatus(401);
    }

    public function test_soft_deleted_cafes_are_excluded_from_normal_queries()
    {
        $cafe = Cafe::factory()->create();
        $cafe->delete();

        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->getJson("/cafes/{$cafe->id}");

        $response->assertStatus(404);
    }
}
