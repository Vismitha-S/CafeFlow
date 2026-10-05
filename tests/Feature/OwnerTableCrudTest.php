<?php

namespace Tests\Feature;

use App\Models\Cafe;
use App\Models\CafeTable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerTableCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_create_table_with_indoor(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_OWNER]);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);

        $response = $this->actingAs($owner)->post(route('owner.tables.store'), [
            'table_number' => '1',
            'name' => 'Main Window',
            'capacity' => 4,
            'location' => 'indoor',
            'status' => 'active',
        ]);

        $response->assertRedirect(route('owner.tables.index'));
        $this->assertDatabaseHas('cafe_tables', [
            'cafe_id' => $cafe->id,
            'location' => 'indoor',
        ]);
    }

    public function test_owner_can_create_table_with_outdoor(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_OWNER]);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);

        $response = $this->actingAs($owner)->post(route('owner.tables.store'), [
            'table_number' => '2',
            'capacity' => 2,
            'location' => 'outdoor',
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('cafe_tables', [
            'cafe_id' => $cafe->id,
            'location' => 'outdoor',
        ]);
    }

    public function test_owner_can_view_table(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_OWNER]);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'name' => 'View Table']);

        $response = $this->actingAs($owner)->get(route('owner.tables.index'));

        $response->assertStatus(200);
        $response->assertSee('View Table');
    }

    public function test_owner_can_update_table_name_and_capacity(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_OWNER]);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'name' => 'Old Name', 'capacity' => 2, 'location' => 'indoor', 'status' => 'active']);

        $response = $this->actingAs($owner)->put(route('owner.tables.update', $table->id), [
            'table_number' => $table->table_number,
            'name' => 'New Name',
            'capacity' => 6,
            'location' => 'indoor',
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('cafe_tables', [
            'id' => $table->id,
            'name' => 'New Name',
            'capacity' => 6,
        ]);
    }

    public function test_owner_can_change_indoor_to_outdoor_and_vice_versa(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_OWNER]);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'location' => 'indoor', 'status' => 'active']);

        // Indoor -> Outdoor
        $this->actingAs($owner)->put(route('owner.tables.update', $table->id), [
            'table_number' => $table->table_number,
            'capacity' => $table->capacity,
            'location' => 'outdoor',
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('cafe_tables', ['id' => $table->id, 'location' => 'outdoor']);

        // Outdoor -> Indoor
        $this->actingAs($owner)->put(route('owner.tables.update', $table->id), [
            'table_number' => $table->table_number,
            'capacity' => $table->capacity,
            'location' => 'indoor',
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('cafe_tables', ['id' => $table->id, 'location' => 'indoor']);
    }

    public function test_invalid_location_is_rejected_with_validation_not_500(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_OWNER]);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);

        $response = $this->actingAs($owner)->post(route('owner.tables.store'), [
            'table_number' => '1',
            'capacity' => 2,
            'location' => 'rooftop', // Invalid
            'status' => 'active',
        ]);

        $response->assertSessionHasErrors('location');
        $this->assertDatabaseEmpty('cafe_tables');
    }

    public function test_owner_can_toggle_status(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_OWNER]);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active']);

        // Active -> Inactive
        $this->actingAs($owner)->patch(route('owner.tables.toggle', $table->id));
        $this->assertEquals('inactive', $table->fresh()->status);

        // Inactive -> Active
        $this->actingAs($owner)->patch(route('owner.tables.toggle', $table->id));
        $this->assertEquals('active', $table->fresh()->status);
    }

    public function test_owner_can_soft_delete_own_table_and_not_in_list(): void
    {
        $owner = User::factory()->create(['role' => User::ROLE_OWNER]);
        $cafe = Cafe::factory()->create(['owner_id' => $owner->id]);
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'name' => 'Unique Table To Delete']);

        $response = $this->actingAs($owner)->delete(route('owner.tables.destroy', $table->id));
        $response->assertRedirect();

        $this->assertSoftDeleted($table);

        $viewResponse = $this->actingAs($owner)->get(route('owner.tables.index'));
        $viewResponse->assertDontSee('Unique Table To Delete');
    }

    public function test_owner_cannot_update_toggle_delete_another_owners_table(): void
    {
        $owner1 = User::factory()->create(['role' => User::ROLE_OWNER]);
        $cafe1 = Cafe::factory()->create(['owner_id' => $owner1->id]);
        $table1 = CafeTable::factory()->create(['cafe_id' => $cafe1->id]);

        $owner2 = User::factory()->create(['role' => User::ROLE_OWNER]);

        // Update
        $this->actingAs($owner2)->put(route('owner.tables.update', $table1->id), [
            'table_number' => '1', 'capacity' => 2, 'location' => 'indoor', 'status' => 'active',
        ])->assertStatus(403);

        // Toggle
        $this->actingAs($owner2)->patch(route('owner.tables.toggle', $table1->id))->assertStatus(403);

        // Delete
        $this->actingAs($owner2)->delete(route('owner.tables.destroy', $table1->id))->assertStatus(403);
    }
}
