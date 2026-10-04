<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use App\Services\PaymentService;
use App\Services\ReservationCancellationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ReservationCancellationTest extends TestCase
{
    use RefreshDatabase;

    public function test_cancellation_calculates_snapshot_penalty_and_pending_refund(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $reservation = Reservation::factory()->pending()->create([
            'user_id' => $customer->id,
            'reservation_fee' => '80.05',
            'cancellation_penalty_percentage' => '10.00',
        ]);
        $reservation->cafe->update(['reservation_fee' => '900.00']);

        $paymentService = app(PaymentService::class);
        $deposit = $paymentService->createDepositPayment($reservation);
        $paymentService->markDepositAsPaid($deposit);

        $cancelledReservation = app(ReservationCancellationService::class)
            ->cancelReservation($reservation->fresh(), 'Plans changed');

        $refund = Payment::query()
            ->where('type', Payment::TYPE_REFUND)
            ->where('reservation_id', $reservation->id)
            ->firstOrFail();

        $this->assertSame('cancelled', $cancelledReservation->status);
        $this->assertSame('8.01', $cancelledReservation->cancellation_penalty_amount);
        $this->assertSame('Plans changed', $cancelledReservation->cancellation_reason);
        $this->assertNotNull($cancelledReservation->cancelled_at);
        $this->assertSame('pending', $refund->status);
        $this->assertSame('72.04', $refund->amount);
        $this->assertSame($deposit->id, $refund->refund_of_payment_id);
        $this->assertNull($refund->paid_at);
    }

    public function test_cancellation_without_paid_deposit_fails_pending_attempt_without_refund(): void
    {
        $reservation = Reservation::factory()->pending()->create([
            'reservation_fee' => '50.00',
            'cancellation_penalty_percentage' => '25.00',
        ]);
        $payment = app(PaymentService::class)->createDepositPayment($reservation);

        $cancelledReservation = app(ReservationCancellationService::class)
            ->cancelReservation($reservation, 'Customer cancelled before payment');

        $this->assertSame('cancelled', $cancelledReservation->status);
        $this->assertSame('12.50', $cancelledReservation->cancellation_penalty_amount);
        $this->assertSame(Payment::STATUS_FAILED, $payment->fresh()->status);
        $this->assertSame('Reservation cancelled before the deposit was paid.', $payment->fresh()->failure_reason);
        $this->assertDatabaseCount('payments', 1);
    }

    public function test_full_penalty_does_not_create_a_zero_value_refund(): void
    {
        $reservation = Reservation::factory()->pending()->create([
            'reservation_fee' => '25.00',
            'cancellation_penalty_percentage' => '100.00',
        ]);
        $paymentService = app(PaymentService::class);
        $deposit = $paymentService->createDepositPayment($reservation);
        $paymentService->markDepositAsPaid($deposit);

        $cancelledReservation = app(ReservationCancellationService::class)->cancelReservation($reservation);

        $this->assertSame('25.00', $cancelledReservation->cancellation_penalty_amount);
        $this->assertSame(1, Payment::query()->where('type', Payment::TYPE_DEPOSIT)->count());
        $this->assertSame(0, Payment::query()->where('type', Payment::TYPE_REFUND)->count());
    }

    public function test_completed_reservation_is_not_eligible_for_cancellation(): void
    {
        $reservation = Reservation::factory()->completed()->create();

        $this->expectException(ValidationException::class);
        app(ReservationCancellationService::class)->cancelReservation($reservation);
    }

    public function test_http_flow_ignores_client_financial_state_and_identity_fields(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $otherCustomer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $reservation = Reservation::factory()->pending()->create([
            'user_id' => $customer->id,
            'reservation_fee' => '80.05',
            'cancellation_penalty_percentage' => '10.00',
        ]);

        $depositResponse = $this->actingAs($customer)->postJson(route('reservations.deposit', $reservation), [
            'amount' => '0.01',
            'status' => Payment::STATUS_PAID,
            'reservation_fee' => '0.01',
            'user_id' => $otherCustomer->id,
        ]);

        $depositResponse->assertCreated()
            ->assertJsonPath('type', Payment::TYPE_DEPOSIT)
            ->assertJsonPath('status', Payment::STATUS_PENDING)
            ->assertJsonPath('amount', '80.05');

        $deposit = Payment::query()->findOrFail($depositResponse->json('id'));
        app(PaymentService::class)->markDepositAsPaid($deposit);

        $cancellationResponse = $this->postJson(route('reservations.cancel', $reservation), [
            'cancellation_reason' => 'Client cancellation',
            'amount' => '0.01',
            'status' => 'confirmed',
            'refund_amount' => '999999.99',
            'reservation_fee' => '0.01',
            'penalty_amount' => '0.00',
            'user_id' => $otherCustomer->id,
        ]);

        $cancellationResponse->assertOk()
            ->assertJsonPath('status', 'cancelled')
            ->assertJsonPath('reservation_fee', '80.05')
            ->assertJsonPath('cancellation_penalty_percentage', '10.00')
            ->assertJsonPath('cancellation_penalty_amount', '8.01')
            ->assertJsonPath('cancellation_reason', 'Client cancellation');

        $refund = Payment::query()->where('type', Payment::TYPE_REFUND)->firstOrFail();
        $this->assertSame('72.04', $refund->amount);
        $this->assertSame(Payment::STATUS_PENDING, $refund->status);
        $this->assertSame($customer->id, $refund->user_id);
    }

    public function test_customer_cannot_start_or_cancel_another_customers_reservation(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $reservationOwner = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $reservation = Reservation::factory()->pending()->create(['user_id' => $reservationOwner->id]);

        $this->actingAs($customer)
            ->postJson(route('reservations.deposit', $reservation))
            ->assertForbidden();

        $this->postJson(route('reservations.cancel', $reservation))
            ->assertForbidden();

        $this->assertDatabaseCount('payments', 0);
        $this->assertSame('pending', $reservation->fresh()->status);
    }

    public function test_cancellation_rejects_an_invalid_reason_without_changing_reservation(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $reservation = Reservation::factory()->pending()->create(['user_id' => $customer->id]);

        $this->actingAs($customer)
            ->postJson(route('reservations.cancel', $reservation), [
                'cancellation_reason' => str_repeat('x', 1001),
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['cancellation_reason']);

        $this->assertSame('pending', $reservation->fresh()->status);

    }
}
