<?php

namespace Tests\Feature;

use App\Models\Cafe;
use App\Models\CafeHour;
use App\Models\CafeTable;
use App\Models\Reservation;
use App\Models\User;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ReservationCreationTest extends TestCase
{
    use RefreshDatabase;

    // Helper to setup a cafe with standard opening hours (08:00 to 20:00 every day)
    private function createCafeWithHours(array $cafeAttributes = []): Cafe
    {
        $cafe = Cafe::factory()->create(array_merge([
            'status' => 'active',
            'reservation_fee' => 15.00,
            'cancellation_penalty_percentage' => 25.00,
        ], $cafeAttributes));

        for ($day = 1; $day <= 7; $day++) {
            CafeHour::create([
                'cafe_id' => $cafe->id,
                'day_of_week' => $day,
                'opens_at' => '08:00:00',
                'closes_at' => '20:00:00',
                'is_closed' => false,
            ]);
        }

        return $cafe;
    }

    // A. Customer can create valid reservation
    public function test_customer_can_create_valid_reservation(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create([
            'cafe_id' => $cafe->id,
            'status' => 'active',
            'capacity' => 4,
            'table_number' => 'T1',
        ]);

        $date = Carbon::parse('next monday')->toDateString();

        $response = $this->actingAs($customer)->postJson("/cafes/{$cafe->id}/reservations", [
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'guest_count' => 2,
            'notes' => 'Quiet table please',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'pending')
            ->assertJsonPath('reservation_date', $date)
            ->assertJsonPath('start_time', '10:00')
            ->assertJsonPath('end_time', '11:30')
            ->assertJsonPath('guest_count', 2)
            ->assertJsonPath('notes', 'Quiet table please');

        $this->assertDatabaseHas('reservations', [
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'user_id' => $customer->id,
            'guest_count' => 2,
        ]);
        $reservation = Reservation::first();
        $this->assertEquals($date, $reservation->reservation_date->format('Y-m-d'));
    }

    // B. Reservation is created with pending status
    public function test_reservation_is_created_with_pending_status(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active', 'capacity' => 4]);

        $date = Carbon::parse('next monday')->toDateString();

        $response = $this->actingAs($customer)->postJson("/cafes/{$cafe->id}/reservations", [
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '12:00',
            'guest_count' => 2,
        ]);

        $response->assertStatus(201);
        $this->assertEquals('pending', $response->json('status'));
        $this->assertDatabaseHas('reservations', ['user_id' => $customer->id, 'status' => 'pending']);
    }

    // C. Reservation fee is copied from cafe
    public function test_reservation_fee_is_copied_from_cafe(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours(['reservation_fee' => 75.00]);
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active', 'capacity' => 4]);

        $date = Carbon::parse('next monday')->toDateString();

        $response = $this->actingAs($customer)->postJson("/cafes/{$cafe->id}/reservations", [
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '12:00',
            'guest_count' => 2,
        ]);

        $response->assertStatus(201);
        $this->assertEquals('75.00', $response->json('reservation_fee'));

        // Changing cafe fee afterwards must not alter existing reservation
        $cafe->update(['reservation_fee' => 100.00]);

        $this->assertDatabaseHas('reservations', [
            'id' => $response->json('id'),
            'reservation_fee' => 75.00,
        ]);
    }

    // D. Cancellation percentage is copied from cafe
    public function test_cancellation_percentage_is_copied_from_cafe(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours(['cancellation_penalty_percentage' => 35.00]);
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active', 'capacity' => 4]);

        $date = Carbon::parse('next monday')->toDateString();

        $response = $this->actingAs($customer)->postJson("/cafes/{$cafe->id}/reservations", [
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '12:00',
            'guest_count' => 2,
        ]);

        $response->assertStatus(201);
        $this->assertEquals('35.00', $response->json('cancellation_penalty_percentage'));

        // Changing cafe cancellation percentage afterwards must not alter existing reservation
        $cafe->update(['cancellation_penalty_percentage' => 50.00]);

        $this->assertDatabaseHas('reservations', [
            'id' => $response->json('id'),
            'cancellation_penalty_percentage' => 35.00,
        ]);
    }

    // E. End time is calculated from configured duration
    public function test_end_time_is_calculated_from_configured_duration(): void
    {
        Config::set('reservations.default_duration_minutes', 60);

        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active', 'capacity' => 4]);

        $date = Carbon::parse('next monday')->toDateString();

        $response = $this->actingAs($customer)->postJson("/cafes/{$cafe->id}/reservations", [
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'guest_count' => 2,
        ]);

        $response->assertStatus(201);
        $this->assertEquals('11:00', $response->json('end_time'));
    }

    // F. Reservation cannot be created for inactive cafe
    public function test_reservation_cannot_be_created_for_inactive_cafe(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours(['status' => 'inactive']);
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active', 'capacity' => 4]);

        $date = Carbon::parse('next monday')->toDateString();

        $response = $this->actingAs($customer)->postJson("/cafes/{$cafe->id}/reservations", [
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'guest_count' => 2,
        ]);

        $response->assertStatus(403);
    }

    // G. Reservation cannot be created for inactive table
    public function test_reservation_cannot_be_created_for_inactive_table(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'status' => 'inactive', 'capacity' => 4]);

        $date = Carbon::parse('next monday')->toDateString();

        $response = $this->actingAs($customer)->postJson("/cafes/{$cafe->id}/reservations", [
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'guest_count' => 2,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cafe_table_id']);
    }

    // H. Reservation cannot be created for soft-deleted table
    public function test_reservation_cannot_be_created_for_soft_deleted_table(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active', 'capacity' => 4]);
        $tableId = $table->id;
        $table->delete();

        $date = Carbon::parse('next monday')->toDateString();

        $response = $this->actingAs($customer)->postJson("/cafes/{$cafe->id}/reservations", [
            'cafe_table_id' => $tableId,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'guest_count' => 2,
        ]);

        $response->assertStatus(422);
    }

    // I. Reservation cannot be created when capacity is insufficient
    public function test_reservation_cannot_be_created_when_capacity_is_insufficient(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active', 'capacity' => 2]);

        $date = Carbon::parse('next monday')->toDateString();

        $response = $this->actingAs($customer)->postJson("/cafes/{$cafe->id}/reservations", [
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'guest_count' => 4,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['guest_count']);
    }

    // J. Reservation cannot be created outside cafe opening hours
    public function test_reservation_cannot_be_created_outside_cafe_opening_hours(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active', 'capacity' => 4]);

        $date = Carbon::parse('next monday')->toDateString();

        // Cafe opens at 08:00, requesting 07:00
        $response = $this->actingAs($customer)->postJson("/cafes/{$cafe->id}/reservations", [
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '07:00',
            'guest_count' => 2,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['time']);
    }

    // K. Reservation cannot be created when it extends beyond closing time
    public function test_reservation_cannot_be_created_when_it_extends_beyond_closing_time(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active', 'capacity' => 4]);

        $date = Carbon::parse('next monday')->toDateString();

        // Cafe closes at 20:00, requesting 19:30 with 90 min duration (ends 21:00)
        $response = $this->actingAs($customer)->postJson("/cafes/{$cafe->id}/reservations", [
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '19:30',
            'guest_count' => 2,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['time']);
    }

    // L. Reservation cannot be created for a past date
    public function test_reservation_cannot_be_created_for_a_past_date(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active', 'capacity' => 4]);

        $pastDate = Carbon::yesterday()->toDateString();

        $response = $this->actingAs($customer)->postJson("/cafes/{$cafe->id}/reservations", [
            'cafe_table_id' => $table->id,
            'reservation_date' => $pastDate,
            'start_time' => '12:00',
            'guest_count' => 2,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['reservation_date']);
    }

    // M. Reservation cannot use another cafe's table
    public function test_reservation_cannot_use_another_cafes_table(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe1 = $this->createCafeWithHours();
        $cafe2 = $this->createCafeWithHours();
        $tableOfCafe2 = CafeTable::factory()->create(['cafe_id' => $cafe2->id, 'status' => 'active', 'capacity' => 4]);

        $date = Carbon::parse('next monday')->toDateString();

        $response = $this->actingAs($customer)->postJson("/cafes/{$cafe1->id}/reservations", [
            'cafe_table_id' => $tableOfCafe2->id,
            'reservation_date' => $date,
            'start_time' => '12:00',
            'guest_count' => 2,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cafe_table_id']);
    }

    // N. Existing pending reservation blocks booking
    public function test_existing_pending_reservation_blocks_booking(): void
    {
        $customer1 = User::factory()->create(['role' => 'customer']);
        $customer2 = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active', 'capacity' => 4]);

        $date = Carbon::parse('next monday')->toDateString();

        Reservation::factory()->create([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'user_id' => $customer1->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'end_time' => '11:30',
            'status' => 'pending',
        ]);

        // Customer 2 requests overlapping slot 10:30 to 12:00
        $response = $this->actingAs($customer2)->postJson("/cafes/{$cafe->id}/reservations", [
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '10:30',
            'guest_count' => 2,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['table']);
    }

    // O. Existing confirmed reservation blocks booking
    public function test_existing_confirmed_reservation_blocks_booking(): void
    {
        $customer1 = User::factory()->create(['role' => 'customer']);
        $customer2 = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active', 'capacity' => 4]);

        $date = Carbon::parse('next monday')->toDateString();

        Reservation::factory()->create([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'user_id' => $customer1->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'end_time' => '11:30',
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($customer2)->postJson("/cafes/{$cafe->id}/reservations", [
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '10:30',
            'guest_count' => 2,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['table']);
    }

    // P. Cancelled reservation does not block booking
    public function test_cancelled_reservation_does_not_block_booking(): void
    {
        $customer1 = User::factory()->create(['role' => 'customer']);
        $customer2 = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active', 'capacity' => 4]);

        $date = Carbon::parse('next monday')->toDateString();

        Reservation::factory()->create([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'user_id' => $customer1->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'end_time' => '11:30',
            'status' => 'cancelled',
        ]);

        $response = $this->actingAs($customer2)->postJson("/cafes/{$cafe->id}/reservations", [
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'guest_count' => 2,
        ]);

        $response->assertStatus(201);
    }

    // Q. Completed reservation does not block booking
    public function test_completed_reservation_does_not_block_booking(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active', 'capacity' => 4]);

        $date = Carbon::parse('next monday')->toDateString();

        Reservation::factory()->create([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'end_time' => '11:30',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($customer)->postJson("/cafes/{$cafe->id}/reservations", [
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'guest_count' => 2,
        ]);

        $response->assertStatus(201);
    }

    // R. No-show reservation does not block booking
    public function test_no_show_reservation_does_not_block_booking(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active', 'capacity' => 4]);

        $date = Carbon::parse('next monday')->toDateString();

        Reservation::factory()->create([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'end_time' => '11:30',
            'status' => 'no_show',
        ]);

        $response = $this->actingAs($customer)->postJson("/cafes/{$cafe->id}/reservations", [
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'guest_count' => 2,
        ]);

        $response->assertStatus(201);
    }

    // S. Back-to-back reservations are allowed
    public function test_back_to_back_reservations_are_allowed(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active', 'capacity' => 4]);

        $date = Carbon::parse('next monday')->toDateString();

        Reservation::factory()->create([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'end_time' => '11:30',
            'status' => 'confirmed',
        ]);

        // Exactly back-to-back at 11:30
        $response = $this->actingAs($customer)->postJson("/cafes/{$cafe->id}/reservations", [
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '11:30',
            'guest_count' => 2,
        ]);

        $response->assertStatus(201);
    }

    // T. Overlapping reservation is rejected
    public function test_overlapping_reservation_is_rejected(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active', 'capacity' => 4]);

        $date = Carbon::parse('next monday')->toDateString();

        Reservation::factory()->create([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'end_time' => '11:30',
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($customer)->postJson("/cafes/{$cafe->id}/reservations", [
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '11:00',
            'guest_count' => 2,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['table']);
    }

    // U. Customer cannot set another user_id
    public function test_customer_cannot_set_another_user_id(): void
    {
        $customerA = User::factory()->create(['role' => 'customer']);
        $customerB = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active', 'capacity' => 4]);

        $date = Carbon::parse('next monday')->toDateString();

        $response = $this->actingAs($customerA)->postJson("/cafes/{$cafe->id}/reservations", [
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '12:00',
            'guest_count' => 2,
            'user_id' => $customerB->id,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('reservations', [
            'id' => $response->json('id'),
            'user_id' => $customerA->id,
        ]);
        $this->assertDatabaseMissing('reservations', [
            'id' => $response->json('id'),
            'user_id' => $customerB->id,
        ]);
    }

    // V. Customer cannot force status=confirmed
    public function test_customer_cannot_force_status_confirmed(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active', 'capacity' => 4]);

        $date = Carbon::parse('next monday')->toDateString();

        $response = $this->actingAs($customer)->postJson("/cafes/{$cafe->id}/reservations", [
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '12:00',
            'guest_count' => 2,
            'status' => 'confirmed',
        ]);

        $response->assertStatus(201);
        $this->assertEquals('pending', $response->json('status'));
        $this->assertDatabaseHas('reservations', [
            'id' => $response->json('id'),
            'status' => 'pending',
        ]);
    }

    // W. Customer cannot override reservation_fee
    public function test_customer_cannot_override_reservation_fee(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours(['reservation_fee' => 50.00]);
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active', 'capacity' => 4]);

        $date = Carbon::parse('next monday')->toDateString();

        $response = $this->actingAs($customer)->postJson("/cafes/{$cafe->id}/reservations", [
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '12:00',
            'guest_count' => 2,
            'reservation_fee' => 0.00,
        ]);

        $response->assertStatus(201);
        $this->assertEquals('50.00', $response->json('reservation_fee'));
    }

    // X. Customer cannot override cancellation percentage
    public function test_customer_cannot_override_cancellation_percentage(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours(['cancellation_penalty_percentage' => 40.00]);
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active', 'capacity' => 4]);

        $date = Carbon::parse('next monday')->toDateString();

        $response = $this->actingAs($customer)->postJson("/cafes/{$cafe->id}/reservations", [
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '12:00',
            'guest_count' => 2,
            'cancellation_penalty_percentage' => 0.00,
        ]);

        $response->assertStatus(201);
        $this->assertEquals('40.00', $response->json('cancellation_penalty_percentage'));
    }

    // Y. Customer can only view own reservations
    public function test_customer_can_only_view_own_reservations(): void
    {
        $customerA = User::factory()->create(['role' => 'customer']);
        $customerB = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id]);

        $date = Carbon::parse('next monday')->toDateString();

        $resA = Reservation::factory()->create([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'user_id' => $customerA->id,
            'reservation_date' => $date,
        ]);

        $resB = Reservation::factory()->create([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'user_id' => $customerB->id,
            'reservation_date' => $date,
        ]);

        // Customer A can view their own reservation
        $resAView = $this->actingAs($customerA)->getJson("/reservations/{$resA->id}");
        $resAView->assertStatus(200);

        // Customer A cannot view Customer B's reservation
        $resBView = $this->actingAs($customerA)->getJson("/reservations/{$resB->id}");
        $resBView->assertStatus(403);

        // Customer A index includes only their own reservation
        $index = $this->actingAs($customerA)->getJson('/reservations');
        $index->assertStatus(200);
        $this->assertCount(1, $index->json('data'));
        $this->assertEquals($resA->id, $index->json('data.0.id'));
    }

    // Z. Owner can view reservations for own cafe
    public function test_owner_can_view_reservations_for_own_cafe(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $cafe = $this->createCafeWithHours(['owner_id' => $owner->id]);
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id]);
        $customer = User::factory()->create(['role' => 'customer']);

        $date = Carbon::parse('next monday')->toDateString();

        $reservation = Reservation::factory()->create([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'user_id' => $customer->id,
            'reservation_date' => $date,
        ]);

        $response = $this->actingAs($owner)->getJson("/reservations/{$reservation->id}");
        $response->assertStatus(200);

        $index = $this->actingAs($owner)->getJson('/reservations');
        $index->assertStatus(200);
        $this->assertCount(1, $index->json('reservations'));
    }

    // AA. Owner cannot view another owner's reservations
    public function test_owner_cannot_view_another_owners_reservations(): void
    {
        $owner1 = User::factory()->create(['role' => 'owner']);
        $owner2 = User::factory()->create(['role' => 'owner']);
        $cafe1 = $this->createCafeWithHours(['owner_id' => $owner1->id]);
        $table1 = CafeTable::factory()->create(['cafe_id' => $cafe1->id]);
        $customer = User::factory()->create(['role' => 'customer']);

        $date = Carbon::parse('next monday')->toDateString();

        $reservation = Reservation::factory()->create([
            'cafe_id' => $cafe1->id,
            'cafe_table_id' => $table1->id,
            'user_id' => $customer->id,
            'reservation_date' => $date,
        ]);

        $response = $this->actingAs($owner2)->getJson("/reservations/{$reservation->id}");
        $response->assertStatus(403);
    }

    // AB. Admin can view all reservations
    public function test_admin_can_view_all_reservations(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id]);
        $customer = User::factory()->create(['role' => 'customer']);

        $date = Carbon::parse('next monday')->toDateString();

        $reservation = Reservation::factory()->create([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'user_id' => $customer->id,
            'reservation_date' => $date,
        ]);

        $showResponse = $this->actingAs($admin)->getJson("/reservations/{$reservation->id}");
        $showResponse->assertStatus(200);

        $indexResponse = $this->actingAs($admin)->getJson('/reservations');
        $indexResponse->assertStatus(200);
        $this->assertCount(1, $indexResponse->json('reservations'));
    }

    // AC. Reservation creation occurs transactionally
    public function test_reservation_creation_occurs_transactionally(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active', 'capacity' => 4]);

        $service = app(ReservationService::class);
        $date = Carbon::parse('next monday')->toDateString();

        // Simulate a failure inside the transaction by passing capacity violation
        try {
            $service->createReservation($customer, $cafe, [
                'cafe_table_id' => $table->id,
                'reservation_date' => $date,
                'start_time' => '12:00',
                'guest_count' => 10, // Exceeds table capacity
            ]);
        } catch (\Exception $e) {
            // Expected validation failure
        }

        $this->assertDatabaseCount('reservations', 0);
    }

    // AD. Concurrent booking conflict is safely rejected
    public function test_concurrent_booking_conflict_is_safely_rejected(): void
    {
        $customer1 = User::factory()->create(['role' => 'customer']);
        $customer2 = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active', 'capacity' => 4]);

        $date = Carbon::parse('next monday')->toDateString();

        // First customer secures the booking
        $res1 = $this->actingAs($customer1)->postJson("/cafes/{$cafe->id}/reservations", [
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '14:00',
            'guest_count' => 2,
        ]);
        $res1->assertStatus(201);

        // Second customer attempts to book the same table with overlapping time
        $res2 = $this->actingAs($customer2)->postJson("/cafes/{$cafe->id}/reservations", [
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '14:30',
            'guest_count' => 2,
        ]);

        $res2->assertStatus(422)
            ->assertJsonValidationErrors(['table'])
            ->assertJsonPath('errors.table.0', 'This table is no longer available for the selected time.');
    }

    // AE. Reservation listing avoids obvious N+1 queries where practical
    public function test_reservation_listing_avoids_obvious_n_plus_one_queries_where_practical(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();

        for ($i = 1; $i <= 5; $i++) {
            $table = CafeTable::factory()->create(['cafe_id' => $cafe->id]);
            Reservation::factory()->create([
                'cafe_id' => $cafe->id,
                'cafe_table_id' => $table->id,
                'user_id' => $customer->id,
                'reservation_date' => Carbon::parse("next monday +{$i} days")->toDateString(),
            ]);
        }

        DB::enableQueryLog();

        $response = $this->actingAs($customer)->getJson('/reservations');

        $response->assertStatus(200);

        $queries = DB::getQueryLog();

        // Should be around 9 queries total: user lookup, upcoming, past, all (with eager loading 'cafe', 'cafeTable', 'user' using WHERE IN)
        // Without eager loading, 5 reservations would perform 45+ individual relation queries across upcoming/past/all!
        $this->assertLessThanOrEqual(12, count($queries));
    }
}
