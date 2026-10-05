<?php

namespace Tests\Feature;

use App\Models\Cafe;
use App\Models\CafeTable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CafeTableManagementTest extends TestCase
{
    use RefreshDatabase;

    private function validTableData($overrides = [])
    {
        return array_merge([
            'table_number' => 'T1',
            'name' => 'Window Table',
            'capacity' => 2,
            'location' => 'indoor',
            'status' => 'active',
        ], $overrides);
    }

    public function test_admin_can_create_a_table()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cafe = Cafe::factory()->create();

        $response = $this->actingAs($admin)->postJson("/cafes/{$cafe->id}/tables", $this->validTableData());

        $response->assertStatus(201);
        $this->assertDatabaseHas('cafe_tables', ['cafe_id' => $cafe->id, 'table_number' => 'T1']);
    }

    public function test_owner_can_create_a_table_for_their_cafe()
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);

        $response = $this->actingAs($owner)->postJson("/cafes/{$cafe->id}/tables", $this->validTableData());

        $response->assertStatus(201);
        $this->assertDatabaseHas('cafe_tables', ['cafe_id' => $cafe->id, 'table_number' => 'T1']);
    }

    public function test_customer_cannot_create_a_table()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = Cafe::factory()->create();

        $response = $this->actingAs($customer)->postJson("/cafes/{$cafe->id}/tables", $this->validTableData());

        $response->assertStatus(403);
    }

    public function test_owner_can_view_their_cafe_tables()
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);
        CafeTable::factory()->create(['cafe_id' => $cafe->id]);

        $response = $this->actingAs($owner)->getJson("/cafes/{$cafe->id}/tables");

        $response->assertStatus(200);
        $this->assertCount(1, $response->json());
    }

    public function test_owner_can_update_their_own_cafe_table()
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id]);

        $response = $this->actingAs($owner)->putJson("/cafe-tables/{$table->id}", $this->validTableData([
            'table_number' => 'Updated-T1',
        ]));

        $response->assertStatus(200);
        $this->assertDatabaseHas('cafe_tables', ['id' => $table->id, 'table_number' => 'Updated-T1']);
    }

    public function test_owner_can_delete_their_own_cafe_table()
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id]);

        $response = $this->actingAs($owner)->deleteJson("/cafe-tables/{$table->id}");

        $response->assertStatus(204);
        $this->assertSoftDeleted($table);
    }

    public function test_owner_cannot_view_another_owners_management_table()
    {
        $owner1 = User::factory()->create(['role' => 'owner']);
        $owner2 = User::factory()->create(['role' => 'owner']);
        $cafe2 = Cafe::factory()->create(['owner_id' => $owner2->id]);
        $table2 = CafeTable::factory()->create(['cafe_id' => $cafe2->id]);

        $response = $this->actingAs($owner1)->getJson("/cafe-tables/{$table2->id}");

        $response->assertStatus(403);
    }

    public function test_owner_cannot_update_another_owners_table()
    {
        $owner1 = User::factory()->create(['role' => 'owner']);
        $owner2 = User::factory()->create(['role' => 'owner']);
        $cafe2 = Cafe::factory()->create(['owner_id' => $owner2->id]);
        $table2 = CafeTable::factory()->create(['cafe_id' => $cafe2->id]);

        $response = $this->actingAs($owner1)->putJson("/cafe-tables/{$table2->id}", $this->validTableData());

        $response->assertStatus(403);
    }

    public function test_owner_cannot_delete_another_owners_table()
    {
        $owner1 = User::factory()->create(['role' => 'owner']);
        $owner2 = User::factory()->create(['role' => 'owner']);
        $cafe2 = Cafe::factory()->create(['owner_id' => $owner2->id]);
        $table2 = CafeTable::factory()->create(['cafe_id' => $cafe2->id]);

        $response = $this->actingAs($owner1)->deleteJson("/cafe-tables/{$table2->id}");

        $response->assertStatus(403);
    }

    public function test_admin_can_manage_tables_across_cafes()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cafe = Cafe::factory()->create();
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id]);

        // Update
        $response = $this->actingAs($admin)->putJson("/cafe-tables/{$table->id}", $this->validTableData([
            'table_number' => 'Admin-T1',
        ]));
        $response->assertStatus(200);

        // Delete
        $response = $this->actingAs($admin)->deleteJson("/cafe-tables/{$table->id}");
        $response->assertStatus(204);
    }

    public function test_customer_can_view_active_tables()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = Cafe::factory()->create(['status' => 'active']);
        CafeTable::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active']);

        $response = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/tables");

        $response->assertStatus(200);
        $this->assertCount(1, $response->json());
    }

    public function test_inactive_tables_are_not_shown_to_customers()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = Cafe::factory()->create(['status' => 'active']);
        CafeTable::factory()->create(['cafe_id' => $cafe->id, 'status' => 'inactive']);

        $response = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/tables");

        $response->assertStatus(200);
        $this->assertCount(0, $response->json());
    }

    public function test_inactive_cafes_tables_are_not_shown_to_customers()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = Cafe::factory()->create(['status' => 'inactive']);
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active']);

        $response = $this->actingAs($customer)->getJson("/cafe-tables/{$table->id}");

        $response->assertStatus(403);
    }

    public function test_duplicate_table_number_within_same_cafe_is_rejected()
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);
        CafeTable::factory()->create(['cafe_id' => $cafe->id, 'table_number' => 'T1']);

        $response = $this->actingAs($owner)->postJson("/cafes/{$cafe->id}/tables", $this->validTableData([
            'table_number' => 'T1',
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('table_number');
    }

    public function test_same_table_number_in_different_cafes_is_allowed()
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $cafe1 = Cafe::factory()->create(['owner_id' => $owner->id]);
        CafeTable::factory()->create(['cafe_id' => $cafe1->id, 'table_number' => 'T1']);

        $cafe2 = Cafe::factory()->create(['owner_id' => $owner->id]);
        $response = $this->actingAs($owner)->postJson("/cafes/{$cafe2->id}/tables", $this->validTableData([
            'table_number' => 'T1',
        ]));

        $response->assertStatus(201);
    }

    public function test_capacity_below_1_is_rejected()
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);

        $response = $this->actingAs($owner)->postJson("/cafes/{$cafe->id}/tables", $this->validTableData([
            'capacity' => 0,
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('capacity');
    }

    public function test_invalid_capacity_is_rejected()
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);

        $response = $this->actingAs($owner)->postJson("/cafes/{$cafe->id}/tables", $this->validTableData([
            'capacity' => 1000, // Above reasonable max 50
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('capacity');
    }

    public function test_invalid_location_is_rejected()
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);

        $response = $this->actingAs($owner)->postJson("/cafes/{$cafe->id}/tables", $this->validTableData([
            'location' => 'roof',
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('location');
    }

    public function test_invalid_status_is_rejected()
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);

        $response = $this->actingAs($owner)->postJson("/cafes/{$cafe->id}/tables", $this->validTableData([
            'status' => 'broken',
        ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('status');
    }

    public function test_owner_cannot_spoof_cafe_id()
    {
        $owner1 = User::factory()->create(['role' => 'owner']);
        $owner2 = User::factory()->create(['role' => 'owner']);

        $cafe2 = Cafe::factory()->create(['owner_id' => $owner2->id]);

        // Owner 1 attempts to add table to Cafe 2
        $response = $this->actingAs($owner1)->postJson("/cafes/{$cafe2->id}/tables", $this->validTableData());

        $response->assertStatus(403);
    }

    public function test_unauthenticated_users_cannot_access_protected_management_routes()
    {
        $cafe = Cafe::factory()->create();
        $response = $this->postJson("/cafes/{$cafe->id}/tables", $this->validTableData());
        $response->assertStatus(401);
    }

    public function test_soft_deleted_tables_are_excluded_from_normal_queries()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cafe = Cafe::factory()->create();
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id]);

        $table->delete();

        $response = $this->actingAs($admin)->getJson("/cafe-tables/{$table->id}");
        $response->assertStatus(404);
    }
}
