<?php

namespace Tests\Feature;

use App\Models\Cafe;
use App\Models\CafeHour;
use App\Models\CafeTable;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function createCafe(array $attributes = []): Cafe
    {
        $owner = User::factory()->create(['role' => User::ROLE_OWNER]);

        return Cafe::factory()->create(array_merge([
            'owner_id' => $owner->id,
            'slug' => 'checkout-cafe',
            'name' => 'Checkout Cafe',
            'city' => 'Colombo',
            'status' => 'active',
            'reservation_fee' => '1234.50',
            'cancellation_penalty_percentage' => '37.50',
        ], $attributes));
    }

    private function createTable(Cafe $cafe): CafeTable
    {
        return CafeTable::factory()->create([
            'cafe_id' => $cafe->id,
            'table_number' => 'T1',
            'name' => 'Garden Table',
            'capacity' => 4,
            'status' => 'active',
        ]);
    }

    private function createOpeningHours(Cafe $cafe): void
    {
        for ($day = 1; $day <= 7; $day++) {
            CafeHour::create([
                'cafe_id' => $cafe->id,
                'day_of_week' => $day,
                'opens_at' => '08:00:00',
                'closes_at' => '20:00:00',
                'is_closed' => false,
            ]);
        }
    }

    public function test_checkout_uses_persisted_fee_and_shows_only_credit_or_debit_card(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $cafe = $this->createCafe();
        $table = $this->createTable($cafe);

        $response = $this->actingAs($customer)->get(route('customer.reservation.checkout', [
            'cafe' => $cafe->slug,
            'table' => $table->id,
            'date' => '2026-10-05',
            'time' => '10:30 AM',
            'guests' => '3 Guests',
        ]));

        $response->assertOk()
            ->assertSee('LKR 1,234.50')
            ->assertSee('37.5%')
            ->assertSee('Credit / Debit Card')
            ->assertDontSee('Pay at Cafe')
            ->assertDontSee('pay_at_cafe')
            ->assertDontSee('value="card"', false)
            ->assertSee('Pay the reservation fee securely to confirm your table.')
            ->assertSee('Amount Due Now')
            ->assertDontSee('LKR 300')
            ->assertDontSee('25%')
            ->assertDontSee('2 hours')
            ->assertDontSee('20 minutes');
    }

    public function test_zero_fee_and_zero_penalty_are_displayed_without_fallback_values(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $cafe = $this->createCafe([
            'slug' => 'zero-fee-cafe',
            'reservation_fee' => '0.00',
            'cancellation_penalty_percentage' => '0.00',
        ]);
        $table = $this->createTable($cafe);

        $this->actingAs($customer)
            ->get(route('customer.reservation.checkout', [
                'cafe' => $cafe->slug,
                'table' => $table->id,
                'date' => '2026-10-05',
                'time' => '10:30 AM',
                'guests' => '2 Guests',
            ]))
            ->assertOk()
            ->assertSee('LKR 0.00')
            ->assertSee('No cancellation penalty applies');
    }

    public function test_proceed_to_payment_creates_pending_reservation_and_deposit_from_server_values(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $otherCustomer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $cafe = $this->createCafe(['reservation_fee' => '67.50', 'cancellation_penalty_percentage' => '32.50']);
        $table = $this->createTable($cafe);
        $this->createOpeningHours($cafe);
        $date = Carbon::parse('next monday')->toDateString();

        $response = $this->actingAs($customer)->post(route('cafes.reservations.store', $cafe), [
            '_checkout' => '1',
            'cafe_table_id' => $table->id,
            'reservation_date' => $date,
            'start_time' => '10:30',
            'guest_count' => 3,
            'reservation_fee' => '0.01',
            'cancellation_penalty_percentage' => '0',
            'user_id' => $otherCustomer->id,
        ]);

        $reservation = Reservation::query()->firstOrFail();
        $payment = Payment::query()->firstOrFail();

        $response->assertRedirect(route('customer.reservation.checkout', ['reservation' => $reservation->id]))
            ->assertSessionHas('reservation_created', true)
            ->assertSessionHas('show_payment_form', true);
        $this->assertSame('pending', $reservation->status);
        $this->assertSame('67.50', $reservation->reservation_fee);
        $this->assertSame('32.50', $reservation->cancellation_penalty_percentage);
        $this->assertSame($customer->id, $reservation->user_id);
        $this->assertSame(Payment::STATUS_PENDING, $payment->status);
        $this->assertSame('67.50', $payment->amount);
        $this->assertSame($customer->id, $payment->user_id);

        $this->get(route('customer.reservation.checkout', ['reservation' => $reservation->id]))
            ->assertOk()
            ->assertSeeText('Reservation: #'.$reservation->id)
            ->assertSeeText('Payment Pending')
            ->assertSeeText('Payment Status')
            ->assertSeeText('Pending')
            ->assertDontSeeText('Payment Successful')
            ->assertSee('Payment Details')
            ->assertSee('name="card_number"', false)
            ->assertSee('Pay LKR 67.50')
            ->assertSeeText('Checkout Cafe')
            ->assertSeeText('Garden Table');
    }

    public function test_cafe_details_offers_persisted_table_ids_for_checkout(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $cafe = $this->createCafe(['slug' => 'persisted-table-cafe']);
        $table = $this->createTable($cafe);
        $this->createOpeningHours($cafe);

        $this->actingAs($customer)
            ->get(route('customer.cafe.show', $cafe->slug))
            ->assertOk()
            ->assertSee('Garden Table')
            ->assertSee('selectedDate')
            ->assertSee('selectedTable.id');
    }

    public function test_available_persisted_table_is_shown_without_unavailable_message(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $cafe = $this->createCafe(['slug' => 'available-persisted-cafe']);
        $this->createTable($cafe);
        $this->createOpeningHours($cafe);

        $this->actingAs($customer)
            ->get(route('customer.cafe.show', [
                'slug' => $cafe->slug,
                'date' => now()->toDateString(),
                'time' => '10:30 AM',
                'guests' => '2 Guests',
            ]))
            ->assertOk()
            ->assertSee('Garden Table')
            ->assertSee('Available')
            ->assertDontSee('Table reservations are not available for this cafe right now.');
    }

    public function test_conflicting_persisted_table_is_unavailable_and_not_preselected(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $reservationOwner = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $cafe = $this->createCafe(['slug' => 'conflicted-persisted-cafe']);
        $table = $this->createTable($cafe);
        $this->createOpeningHours($cafe);
        $date = now()->toDateString();

        Reservation::factory()->pending()->create([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'user_id' => $reservationOwner->id,
            'reservation_date' => $date,
            'start_time' => '10:00',
            'end_time' => '12:00',
        ]);

        $response = $this->actingAs($customer)
            ->get(route('customer.cafe.show', [
                'slug' => $cafe->slug,
                'date' => $date,
                'time' => '10:30 AM',
                'guests' => '2 Guests',
            ]));

        $response->assertOk()
            ->assertSee('Garden Table')
            ->assertSee('Unavailable')
            ->assertSee('No tables are available for the selected date, time, and guest count.');
        $this->assertStringNotContainsString('selectedTable: {', $response->getContent());
    }

    public function test_table_below_selected_guest_capacity_is_unavailable(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $cafe = $this->createCafe(['slug' => 'small-capacity-cafe']);
        $table = $this->createTable($cafe);
        $table->update(['capacity' => 1]);
        $this->createOpeningHours($cafe);

        $this->actingAs($customer)
            ->get(route('customer.cafe.show', [
                'slug' => $cafe->slug,
                'date' => now()->toDateString(),
                'time' => '10:30 AM',
                'guests' => '2 Guests',
            ]))
            ->assertOk()
            ->assertSee('Garden Table')
            ->assertSee('Unavailable')
            ->assertSee('No tables are available for the selected date, time, and guest count.');
    }

    public function test_checkout_loads_successfully_with_persisted_table_without_image(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        // Create persisted cafe and table that will not have 'image' keys injected via mock fixtures
        $cafe = $this->createCafe(['slug' => 'no-image-persisted-cafe', 'name' => 'Persisted Image Free Cafe']);
        $table = $this->createTable($cafe);
        $table->update(['name' => 'Persisted Plain Table']);
        $this->createOpeningHours($cafe);

        $response = $this->actingAs($customer)
            ->get(route('customer.reservation.checkout', [
                'cafe' => $cafe->slug,
                'table' => $table->id,
                'date' => now()->toDateString(),
                'time' => '10:30 AM',
                'guests' => '2 Guests',
            ]));

        // Assert no 'Undefined array key' crash, HTTP 200, and correct names appear
        $response->assertOk()
            ->assertSee('Persisted Image Free Cafe')
            ->assertSee('Persisted Plain Table')
            ->assertSee('LKR 1,234.50') // reservation fee
            ->assertSee('37.5%'); // cancellation penalty
    }

    public function test_mock_fixture_tables_are_not_offered_as_available_without_persisted_cafe(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);

        $this->actingAs($customer)
            ->get(route('customer.cafe.show', 'the-velvet-bean'))
            ->assertOk()
            ->assertDontSee('Table 1')
            ->assertSee('Table reservations are not available for this cafe right now.');
    }

    public function test_payment_success_is_only_shown_after_payment_service_marks_deposit_paid(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $reservation = Reservation::factory()->pending()->create([
            'user_id' => $customer->id,
            'reservation_fee' => '89.50',
        ]);
        $paymentService = app(PaymentService::class);
        $deposit = $paymentService->createDepositPayment($reservation);
        $paymentService->markDepositAsPaid($deposit);
        $cafeName = $reservation->cafe->name;
        $tableName = $reservation->cafeTable->name ?: $reservation->cafeTable->table_number;

        $response = $this->actingAs($customer)
            ->withSession([
                'reservation_created' => true,
                'payment_confirmed' => true,
            ])
            ->get(route('customer.reservation.checkout', ['reservation' => $reservation->id]));

        $response->assertViewHas('paymentConfirmed', true)
            ->assertOk()
            ->assertSeeText('Payment Confirmed')
            ->assertSeeText($cafeName)
            ->assertSeeText($tableName)
            ->assertSeeText('Reservation: #'.$reservation->id)
            ->assertSeeText('Payment Status')
            ->assertSeeText('Paid')
            ->assertSeeText('Amount Paid: LKR 89.50')
            ->assertSee('aria-label="Close payment confirmation"', false)
            ->assertSee('@click="confirmed = false"', false)
            ->assertDontSeeText('Payment Pending');

        $this->assertSame('confirmed', $reservation->fresh()->status);
    }

    public function test_closing_payment_form_does_not_change_pending_payment_or_reservation(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $reservation = Reservation::factory()->pending()->create(['user_id' => $customer->id]);
        $deposit = app(PaymentService::class)->createDepositPayment($reservation);

        $this->actingAs($customer)
            ->withSession(['show_payment_form' => true])
            ->get(route('customer.reservation.checkout', ['reservation' => $reservation->id]))
            ->assertOk()
            ->assertSee('Payment Details')
            ->assertSee('aria-label="Close payment form"', false)
            ->assertSee('@click="paymentFormOpen = false"', false);

        $this->assertSame(Payment::STATUS_PENDING, $deposit->fresh()->status);
        $this->assertSame('pending', $reservation->fresh()->status);
    }
}
