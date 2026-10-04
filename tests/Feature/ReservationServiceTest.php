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
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ReservationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ReservationService $reservationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reservationService = app(ReservationService::class);
    }

    // Helper to setup a cafe with standard opening hours (08:00 to 20:00 every day)
    private function createCafeWithHours(array $cafeAttributes = []): Cafe
    {
        $cafe = Cafe::factory()->create(array_merge([
            'status' => 'active',
            'reservation_fee' => 25.50,
            'cancellation_penalty_percentage' => 30.00,
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

    // X. Reservation fee is read dynamically from the cafe
    public function test_reservation_fee_is_read_dynamically_from_the_cafe(): void
    {
        $cafe = $this->createCafeWithHours(['reservation_fee' => 45.75]);
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'capacity' => 4, 'status' => 'active']);
        $user = User::factory()->create(['role' => 'customer']);

        $date = Carbon::parse('next monday')->toDateString();

        $reservation = $this->reservationService->createReservation([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'user_id' => $user->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'guest_count' => 2,
        ]);

        $this->assertEquals('45.75', $reservation->reservation_fee);
        $this->assertEquals(45.75, $this->reservationService->getCafeReservationFee($cafe));
    }

    // Y. Cancellation percentage is read dynamically from the cafe
    public function test_cancellation_percentage_is_read_dynamically_from_the_cafe(): void
    {
        $cafe = $this->createCafeWithHours(['cancellation_penalty_percentage' => 40.00]);
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'capacity' => 4, 'status' => 'active']);
        $user = User::factory()->create(['role' => 'customer']);

        $date = Carbon::parse('next monday')->toDateString();

        $reservation = $this->reservationService->createReservation([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'user_id' => $user->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'guest_count' => 2,
        ]);

        $this->assertEquals('40.00', $reservation->cancellation_penalty_percentage);
        $this->assertEquals(40.00, $this->reservationService->getCafeCancellationPenaltyPercentage($cafe));
    }

    // Z. Reservation duration is configurable and not scattered as magic numbers
    public function test_reservation_duration_is_configurable_and_not_scattered_as_magic_numbers(): void
    {
        Config::set('reservations.default_duration_minutes', 120);

        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'capacity' => 4, 'status' => 'active']);
        $user = User::factory()->create(['role' => 'customer']);

        $date = Carbon::parse('next monday')->toDateString();

        $reservation = $this->reservationService->createReservation([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'user_id' => $user->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'guest_count' => 2,
        ]);

        // With 120 minutes configured, 10:00 start should calculate end time to 12:00
        $this->assertEquals('12:00', $reservation->end_time);
    }

    // Double-booking concurrency prevention in createReservation
    public function test_service_prevents_double_booking_on_same_table(): void
    {
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'capacity' => 4, 'status' => 'active']);
        $user1 = User::factory()->create(['role' => 'customer']);
        $user2 = User::factory()->create(['role' => 'customer']);

        $date = Carbon::parse('next monday')->toDateString();

        // First reservation created successfully: 10:00 to 11:30
        $this->reservationService->createReservation([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'user_id' => $user1->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'guest_count' => 2,
            'status' => 'confirmed',
        ]);

        // Second reservation overlapping: 10:30 to 12:00
        $this->expectException(ValidationException::class);

        $this->reservationService->createReservation([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'user_id' => $user2->id,
            'reservation_date' => $date,
            'start_time' => '10:30',
            'guest_count' => 2,
        ]);
    }

    // Rejection when table belongs to another cafe
    public function test_service_rejects_table_from_another_cafe(): void
    {
        $cafe1 = $this->createCafeWithHours();
        $cafe2 = $this->createCafeWithHours();
        $tableOfCafe2 = CafeTable::factory()->create(['cafe_id' => $cafe2->id, 'capacity' => 4, 'status' => 'active']);
        $user = User::factory()->create(['role' => 'customer']);

        $date = Carbon::parse('next monday')->toDateString();

        $this->expectException(ValidationException::class);

        $this->reservationService->createReservation([
            'cafe_id' => $cafe1->id,
            'cafe_table_id' => $tableOfCafe2->id,
            'user_id' => $user->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'guest_count' => 2,
        ]);
    }

    // Rejection when table capacity is insufficient
    public function test_service_rejects_insufficient_table_capacity(): void
    {
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'capacity' => 2, 'status' => 'active']);
        $user = User::factory()->create(['role' => 'customer']);

        $date = Carbon::parse('next monday')->toDateString();

        $this->expectException(ValidationException::class);

        $this->reservationService->createReservation([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'user_id' => $user->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'guest_count' => 6,
        ]);
    }

    // Rejection when cafe is inactive
    public function test_service_rejects_inactive_cafe(): void
    {
        $cafe = $this->createCafeWithHours(['status' => 'inactive']);
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'capacity' => 4, 'status' => 'active']);
        $user = User::factory()->create(['role' => 'customer']);

        $date = Carbon::parse('next monday')->toDateString();

        $this->expectException(ValidationException::class);

        $this->reservationService->createReservation([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'user_id' => $user->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'guest_count' => 2,
        ]);
    }

    // Rejection when table is inactive
    public function test_service_rejects_inactive_table(): void
    {
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'capacity' => 4, 'status' => 'inactive']);
        $user = User::factory()->create(['role' => 'customer']);

        $date = Carbon::parse('next monday')->toDateString();

        $this->expectException(ValidationException::class);

        $this->reservationService->createReservation([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'user_id' => $user->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'guest_count' => 2,
        ]);
    }

    // Back-to-back reservation creation allowed
    public function test_service_allows_back_to_back_reservations(): void
    {
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'capacity' => 4, 'status' => 'active']);
        $user1 = User::factory()->create(['role' => 'customer']);
        $user2 = User::factory()->create(['role' => 'customer']);

        $date = Carbon::parse('next monday')->toDateString();

        // 10:00 to 11:30
        $res1 = $this->reservationService->createReservation([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'user_id' => $user1->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'guest_count' => 2,
            'status' => 'confirmed',
        ]);

        // 11:30 to 13:00 (back-to-back)
        $res2 = $this->reservationService->createReservation([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'user_id' => $user2->id,
            'reservation_date' => $date,
            'start_time' => '11:30',
            'guest_count' => 2,
            'status' => 'confirmed',
        ]);

        $this->assertEquals('10:00', $res1->start_time);
        $this->assertEquals('11:30', $res1->end_time);
        $this->assertEquals('11:30', $res2->start_time);
        $this->assertEquals('13:00', $res2->end_time);
    }

    // U. Customer cannot access another customer's reservation
    public function test_customer_cannot_access_another_customers_reservation(): void
    {
        $customerA = User::factory()->create(['role' => 'customer']);
        $customerB = User::factory()->create(['role' => 'customer']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id]);

        $reservation = Reservation::factory()->create([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'user_id' => $customerA->id,
        ]);

        $this->assertTrue(Gate::forUser($customerA)->allows('view', $reservation));
        $this->assertFalse(Gate::forUser($customerB)->allows('view', $reservation));
    }

    // V. Owner cannot access another owner's reservation
    public function test_owner_cannot_access_another_owners_reservation(): void
    {
        $owner1 = User::factory()->create(['role' => 'owner']);
        $owner2 = User::factory()->create(['role' => 'owner']);

        $cafe1 = $this->createCafeWithHours(['owner_id' => $owner1->id]);
        $table1 = CafeTable::factory()->create(['cafe_id' => $cafe1->id]);

        $customer = User::factory()->create(['role' => 'customer']);

        $reservation = Reservation::factory()->create([
            'cafe_id' => $cafe1->id,
            'cafe_table_id' => $table1->id,
            'user_id' => $customer->id,
        ]);

        $this->assertTrue(Gate::forUser($owner1)->allows('view', $reservation));
        $this->assertFalse(Gate::forUser($owner2)->allows('view', $reservation));
    }

    // W. Admin can access reservations
    public function test_admin_can_access_reservations(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id]);
        $customer = User::factory()->create(['role' => 'customer']);

        $reservation = Reservation::factory()->create([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'user_id' => $customer->id,
        ]);

        $this->assertTrue(Gate::forUser($admin)->allows('view', $reservation));
        $this->assertTrue(Gate::forUser($admin)->allows('update', $reservation));
        $this->assertTrue(Gate::forUser($admin)->allows('delete', $reservation));
    }

    // Model casts and relationships verification
    public function test_reservation_model_relationships_and_casts(): void
    {
        $cafe = $this->createCafeWithHours();
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id]);
        $user = User::factory()->create(['role' => 'customer']);

        $reservation = Reservation::factory()->create([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'user_id' => $user->id,
            'reservation_date' => '2026-10-15',
            'start_time' => '14:00',
            'end_time' => '15:30',
            'reservation_fee' => 20.00,
            'cancellation_penalty_percentage' => 50.00,
            'cancellation_penalty_amount' => 10.00,
            'cancelled_at' => now(),
        ]);

        // Relationships
        $this->assertInstanceOf(User::class, $reservation->user);
        $this->assertInstanceOf(Cafe::class, $reservation->cafe);
        $this->assertInstanceOf(CafeTable::class, $reservation->cafeTable);

        $this->assertTrue($user->reservations->contains($reservation));
        $this->assertTrue($cafe->reservations->contains($reservation));
        $this->assertTrue($table->reservations->contains($reservation));

        // Casts
        $this->assertInstanceOf(Carbon::class, $reservation->reservation_date);
        $this->assertInstanceOf(Carbon::class, $reservation->cancelled_at);
        $this->assertEquals('20.00', $reservation->reservation_fee);
        $this->assertEquals('50.00', $reservation->cancellation_penalty_percentage);
        $this->assertEquals('10.00', $reservation->cancellation_penalty_amount);
        $this->assertEquals('14:00', $reservation->start_time);
        $this->assertEquals('15:30', $reservation->end_time);
    }
}
