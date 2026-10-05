<?php

namespace Tests\Feature;

use App\Models\Cafe;
use App\Models\CafeHour;
use App\Models\CafeTable;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationAvailabilityTest extends TestCase
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

        // Create opening hours for all 7 days of the week (1=Monday to 7=Sunday)
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

    // A. Active cafe availability
    public function test_active_cafe_returns_availability(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create([
            'cafe_id' => $cafe->id,
            'capacity' => 4,
            'status' => 'active',
            'table_number' => 'T1',
        ]);

        $date = Carbon::parse('next monday')->toDateString();

        $response = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/availability?date={$date}&time=10:00&guests=2");

        $response->assertStatus(200)
            ->assertJsonPath('cafe.id', $cafe->id)
            ->assertJsonPath('requested_date', $date)
            ->assertJsonPath('requested_start_time', '10:00')
            ->assertJsonPath('requested_end_time', '11:30')
            ->assertJsonPath('guest_count', 2)
            ->assertJsonPath('reservation_duration', 90)
            ->assertJsonCount(1, 'available_tables')
            ->assertJsonPath('available_tables.0.id', $table->id);
    }

    // B. Inactive cafe rejected
    public function test_inactive_cafe_rejected(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours(['status' => 'inactive']);

        $date = Carbon::parse('next monday')->toDateString();

        $response = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/availability?date={$date}&time=10:00&guests=2");

        $response->assertStatus(403);
    }

    // C. Cafe closed on requested day
    public function test_cafe_closed_on_requested_day_rejected(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = Cafe::factory()->create(['status' => 'active']);

        // Set day 1 (Monday) as closed
        CafeHour::create([
            'cafe_id' => $cafe->id,
            'day_of_week' => 1,
            'opens_at' => '08:00:00',
            'closes_at' => '20:00:00',
            'is_closed' => true,
        ]);

        $date = Carbon::parse('next monday')->toDateString();

        $response = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/availability?date={$date}&time=10:00&guests=2");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['date']);
    }

    // D. Request before opening time rejected
    public function test_request_before_opening_time_rejected(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();

        $date = Carbon::parse('next monday')->toDateString();

        // Cafe opens at 08:00, requesting 07:00
        $response = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/availability?date={$date}&time=07:00&guests=2");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['time']);
    }

    // E. Request after closing time rejected
    public function test_request_after_closing_time_rejected(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();

        $date = Carbon::parse('next monday')->toDateString();

        // Cafe closes at 20:00, requesting 19:30 for 90 minutes (ends 21:00)
        $response = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/availability?date={$date}&time=19:30&guests=2");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['time']);
    }

    // F. Active table returned
    public function test_active_table_returned(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create([
            'cafe_id' => $cafe->id,
            'status' => 'active',
            'capacity' => 4,
        ]);

        $date = Carbon::parse('next monday')->toDateString();

        $response = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/availability?date={$date}&time=12:00&guests=2");

        $response->assertStatus(200);
        $this->assertEquals($table->id, $response->json('available_tables.0.id'));
    }

    // G. Inactive table excluded
    public function test_inactive_table_excluded(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        CafeTable::factory()->create([
            'cafe_id' => $cafe->id,
            'status' => 'inactive',
            'capacity' => 4,
        ]);

        $date = Carbon::parse('next monday')->toDateString();

        $response = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/availability?date={$date}&time=12:00&guests=2");

        $response->assertStatus(200)
            ->assertJsonCount(0, 'available_tables');
    }

    // H. Soft-deleted table excluded
    public function test_soft_deleted_table_excluded(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create([
            'cafe_id' => $cafe->id,
            'status' => 'active',
            'capacity' => 4,
        ]);
        $table->delete();

        $date = Carbon::parse('next monday')->toDateString();

        $response = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/availability?date={$date}&time=12:00&guests=2");

        $response->assertStatus(200)
            ->assertJsonCount(0, 'available_tables');
    }

    // I. Table capacity too small excluded
    public function test_table_capacity_too_small_excluded(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        CafeTable::factory()->create([
            'cafe_id' => $cafe->id,
            'status' => 'active',
            'capacity' => 2,
        ]);

        $date = Carbon::parse('next monday')->toDateString();

        // Requesting 4 guests on a table of 2
        $response = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/availability?date={$date}&time=12:00&guests=4");

        $response->assertStatus(200)
            ->assertJsonCount(0, 'available_tables');
    }

    // J. Existing confirmed reservation blocks table
    public function test_existing_confirmed_reservation_blocks_table(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create([
            'cafe_id' => $cafe->id,
            'status' => 'active',
            'capacity' => 4,
        ]);

        $date = Carbon::parse('next monday')->toDateString();

        Reservation::factory()->create([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'end_time' => '11:30',
            'status' => 'confirmed',
        ]);

        // Requesting overlapping time 10:30 to 12:00
        $response = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/availability?date={$date}&time=10:30&guests=2");

        $response->assertStatus(200)
            ->assertJsonCount(0, 'available_tables');
    }

    // K. Existing pending reservation blocks table
    public function test_existing_pending_reservation_blocks_table(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create([
            'cafe_id' => $cafe->id,
            'status' => 'active',
            'capacity' => 4,
        ]);

        $date = Carbon::parse('next monday')->toDateString();

        Reservation::factory()->create([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'end_time' => '11:30',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/availability?date={$date}&time=10:30&guests=2");

        $response->assertStatus(200)
            ->assertJsonCount(0, 'available_tables');
    }

    // L. Cancelled reservation does not block table
    public function test_cancelled_reservation_does_not_block_table(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create([
            'cafe_id' => $cafe->id,
            'status' => 'active',
            'capacity' => 4,
        ]);

        $date = Carbon::parse('next monday')->toDateString();

        Reservation::factory()->create([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'end_time' => '11:30',
            'status' => 'cancelled',
        ]);

        $response = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/availability?date={$date}&time=10:00&guests=2");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'available_tables');
    }

    // M. Completed reservation does not block table
    public function test_completed_reservation_does_not_block_table(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create([
            'cafe_id' => $cafe->id,
            'status' => 'active',
            'capacity' => 4,
        ]);

        $date = Carbon::parse('next monday')->toDateString();

        Reservation::factory()->create([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'end_time' => '11:30',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/availability?date={$date}&time=10:00&guests=2");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'available_tables');
    }

    // N. No-show reservation does not block table
    public function test_no_show_reservation_does_not_block_table(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create([
            'cafe_id' => $cafe->id,
            'status' => 'active',
            'capacity' => 4,
        ]);

        $date = Carbon::parse('next monday')->toDateString();

        Reservation::factory()->create([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'end_time' => '11:30',
            'status' => 'no_show',
        ]);

        $response = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/availability?date={$date}&time=10:00&guests=2");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'available_tables');
    }

    // O. Back-to-back reservations are allowed (10:00-11:30 and 11:30-13:00)
    public function test_back_to_back_reservations_are_allowed(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create([
            'cafe_id' => $cafe->id,
            'status' => 'active',
            'capacity' => 4,
        ]);

        $date = Carbon::parse('next monday')->toDateString();

        // Existing reservation from 10:00 to 11:30
        Reservation::factory()->create([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'end_time' => '11:30',
            'status' => 'confirmed',
        ]);

        // Request back-to-back at exactly 11:30
        $response = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/availability?date={$date}&time=11:30&guests=2");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'available_tables')
            ->assertJsonPath('available_tables.0.id', $table->id);
    }

    // P. Overlapping reservations are rejected
    public function test_overlapping_reservations_are_rejected(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create([
            'cafe_id' => $cafe->id,
            'status' => 'active',
            'capacity' => 4,
        ]);

        $date = Carbon::parse('next monday')->toDateString();

        Reservation::factory()->create([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'end_time' => '11:30',
            'status' => 'confirmed',
        ]);

        // Request 11:00 to 12:30 (overlaps by 30 mins)
        $response = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/availability?date={$date}&time=11:00&guests=2");

        $response->assertStatus(200)
            ->assertJsonCount(0, 'available_tables');
    }

    // Q. Multiple tables returned when available
    public function test_multiple_tables_returned_when_available(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        CafeTable::factory()->create([
            'cafe_id' => $cafe->id,
            'status' => 'active',
            'capacity' => 2,
            'table_number' => 'T1',
        ]);
        CafeTable::factory()->create([
            'cafe_id' => $cafe->id,
            'status' => 'active',
            'capacity' => 4,
            'table_number' => 'T2',
        ]);

        $date = Carbon::parse('next monday')->toDateString();

        $response = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/availability?date={$date}&time=12:00&guests=2");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'available_tables');
    }

    // R. Guest count validation
    public function test_guest_count_validation(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();

        $date = Carbon::parse('next monday')->toDateString();

        // 0 guests is invalid
        $resZero = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/availability?date={$date}&time=12:00&guests=0");
        $resZero->assertStatus(422)->assertJsonValidationErrors(['guests']);

        // Exceeding maximum guests (e.g. 50) is invalid
        $resExcess = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/availability?date={$date}&time=12:00&guests=50");
        $resExcess->assertStatus(422)->assertJsonValidationErrors(['guests']);
    }

    // S. Invalid date validation
    public function test_invalid_date_validation(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();

        $response = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/availability?date=not-a-date&time=12:00&guests=2");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['date']);
    }

    // T. Invalid time validation
    public function test_invalid_time_validation(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();

        $date = Carbon::parse('next monday')->toDateString();

        $response = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/availability?date={$date}&time=25:99&guests=2");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['start_time']);
    }

    // Owner can access availability for their own cafe even if inactive
    public function test_owner_can_access_availability_for_their_own_cafe(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $cafe = $this->createCafeWithHours([
            'owner_id' => $owner->id,
            'status' => 'inactive',
        ]);
        CafeTable::factory()->create([
            'cafe_id' => $cafe->id,
            'status' => 'active',
            'capacity' => 2,
        ]);

        $date = Carbon::parse('next monday')->toDateString();

        // Service rejects inactive cafe when checking tables, but owner passes controller authorization
        $response = $this->actingAs($owner)->getJson("/cafes/{$cafe->id}/availability?date={$date}&time=12:00&guests=2");

        // Service validation fails with 422 because cafe is inactive
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cafe']);
    }

    // Admin can access availability
    public function test_admin_can_access_availability(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cafe = $this->createCafeWithHours();
        CafeTable::factory()->create([
            'cafe_id' => $cafe->id,
            'status' => 'active',
            'capacity' => 2,
        ]);

        $date = Carbon::parse('next monday')->toDateString();

        $response = $this->actingAs($admin)->getJson("/cafes/{$cafe->id}/availability?date={$date}&time=12:00&guests=2");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'available_tables');
    }

    public function test_generated_time_slots_follow_opening_hours_and_reservation_duration(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        CafeTable::factory()->create([
            'cafe_id' => $cafe->id,
            'status' => 'active',
            'capacity' => 4,
        ]);
        $date = Carbon::parse('next monday')->toDateString();

        $response = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/availability?date={$date}&guests=2&slots=1");

        $response->assertOk()
            ->assertJsonPath('requested_date', $date)
            ->assertJsonPath('guest_count', 2)
            ->assertJsonPath('time_slots.0.value', '08:00')
            ->assertJsonPath('time_slots.0.label', '8:00 AM')
            ->assertJsonPath('time_slots.21.value', '18:30')
            ->assertJsonCount(22, 'time_slots');
    }

    public function test_generated_time_slots_exclude_conflicts_and_respect_table_capacity(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $reservationOwner = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create([
            'cafe_id' => $cafe->id,
            'table_number' => 'T-SMALL',
            'status' => 'active',
            'capacity' => 2,
        ]);
        $date = Carbon::parse('next monday')->toDateString();

        Reservation::factory()->pending()->create([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'user_id' => $reservationOwner->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'end_time' => '11:30',
        ]);

        $response = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/availability?date={$date}&guests=2&slots=1");
        $slotValues = array_column($response->json('time_slots'), 'value');

        $response->assertOk();
        $this->assertNotContains('10:00', $slotValues);
        $this->assertNotContains('10:30', $slotValues);
        $this->assertContains('11:30', $slotValues);

        $capacityResponse = $this->actingAs($customer)->getJson("/cafes/{$cafe->id}/availability?date={$date}&guests=3&slots=1");
        $capacityResponse->assertOk()->assertJsonCount(0, 'time_slots');
    }
}
