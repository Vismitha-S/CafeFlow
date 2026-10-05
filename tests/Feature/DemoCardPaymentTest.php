<?php

namespace Tests\Feature;

use App\Models\Cafe;
use App\Models\CafeTable;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class DemoCardPaymentTest extends TestCase
{
    use RefreshDatabase;

    /** @return array{User, Reservation, Payment} */
    private function createPendingDeposit(string $amount = '75.25'): array
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $owner = User::factory()->create(['role' => User::ROLE_OWNER]);
        $cafe = Cafe::factory()->create([
            'owner_id' => $owner->id,
            'status' => 'active',
            'reservation_fee' => $amount,
        ]);
        $table = CafeTable::factory()->create(['cafe_id' => $cafe->id, 'status' => 'active']);
        $reservation = Reservation::factory()->pending()->create([
            'cafe_id' => $cafe->id,
            'cafe_table_id' => $table->id,
            'user_id' => $customer->id,
            'reservation_fee' => $amount,
        ]);
        $deposit = app(PaymentService::class)->createDepositPayment($reservation);

        return [$customer, $reservation, $deposit];
    }

    private function validCardPayload(): array
    {
        return [
            'cardholder_name' => 'CafeFlow Demo',
            'card_number' => '4111 1111 1111 1111',
            'expiry_date' => '12/30',
            'cvv' => '123',
        ];
    }

    private function submitDemoPayment(User $customer, Reservation $reservation, Payment $deposit, array $payload = []): TestResponse
    {
        return $this->actingAs($customer)->post(route('reservations.payments.demo-confirmation', [
            'reservation' => $reservation->id,
            'payment' => $deposit->id,
        ]), array_merge($this->validCardPayload(), $payload));
    }

    public function test_valid_demo_card_marks_server_deposit_paid_and_confirms_reservation(): void
    {
        [$customer, $reservation, $deposit] = $this->createPendingDeposit('67.50');
        $cardNumber = '4111111111111111';

        $response = $this->submitDemoPayment($customer, $reservation, $deposit, [
            'card_number' => '4111 1111 1111 1111',
            'amount' => '0.01',
            'provider' => 'forged',
            'provider_reference' => 'forged-reference',
        ]);

        $response->assertRedirect(route('customer.reservation.checkout', ['reservation' => $reservation->id]))
            ->assertSessionHas('payment_confirmed', true)
            ->assertSessionMissing('_old_input');

        $paidPayment = $deposit->fresh();
        $this->assertSame(Payment::STATUS_PAID, $paidPayment->status);
        $this->assertSame('67.50', $paidPayment->amount);
        $this->assertSame('LKR', $paidPayment->currency);
        $this->assertSame('demo', $paidPayment->provider);
        $this->assertMatchesRegularExpression('/^DEMO-[A-Z0-9]{16}$/', $paidPayment->provider_reference);
        $this->assertNotSame('forged-reference', $paidPayment->provider_reference);
        $this->assertNotNull($paidPayment->paid_at);
        $this->assertSame('confirmed', $reservation->fresh()->status);
        $this->assertDatabaseCount('payments', 1);

        $storedValues = json_encode($paidPayment->getAttributes());
        $this->assertStringNotContainsString($cardNumber, $storedValues);
        $this->assertStringNotContainsString('123', $storedValues);
        $this->assertStringNotContainsString('12/30', $storedValues);
        $response->assertSessionMissing('card_number')
            ->assertSessionMissing('cvv');
    }

    public function test_demo_card_validation_errors_do_not_change_payment_or_reservation_and_do_not_flash_card_data(): void
    {
        [$customer, $reservation, $deposit] = $this->createPendingDeposit();

        $response = $this->submitDemoPayment($customer, $reservation, $deposit, [
            'card_number' => '4111111111111112',
            'expiry_date' => '01/20',
            'cvv' => '12x',
        ]);

        $response->assertRedirect(route('customer.reservation.checkout', ['reservation' => $reservation->id]))
            ->assertSessionHasErrors(['card_number', 'expiry_date', 'cvv'])
            ->assertSessionHas('show_payment_form', true)
            ->assertSessionMissing('_old_input.card_number')
            ->assertSessionMissing('_old_input.expiry_date')
            ->assertSessionMissing('_old_input.cvv');

        $this->assertSame(Payment::STATUS_PENDING, $deposit->fresh()->status);
        $this->assertSame('pending', $reservation->fresh()->status);
    }

    public function test_cardholder_name_is_required_and_bounded(): void
    {
        [$customer, $reservation, $deposit] = $this->createPendingDeposit();

        $this->submitDemoPayment($customer, $reservation, $deposit, ['cardholder_name' => ''])
            ->assertRedirect(route('customer.reservation.checkout', ['reservation' => $reservation->id]))
            ->assertSessionHasErrors(['cardholder_name']);

        $this->assertSame(Payment::STATUS_PENDING, $deposit->fresh()->status);
        $this->assertSame('pending', $reservation->fresh()->status);
    }

    public function test_payment_amount_is_taken_from_database_and_duplicate_submission_is_idempotent(): void
    {
        [$customer, $reservation, $deposit] = $this->createPendingDeposit('91.20');

        $this->submitDemoPayment($customer, $reservation, $deposit, ['amount' => '0.01'])->assertRedirect();
        $firstReference = $deposit->fresh()->provider_reference;
        $this->submitDemoPayment($customer, $reservation, $deposit, ['amount' => '999999.99'])->assertRedirect();

        $this->assertSame(Payment::STATUS_PAID, $deposit->fresh()->status);
        $this->assertSame('91.20', $deposit->fresh()->amount);
        $this->assertSame($firstReference, $deposit->fresh()->provider_reference);
        $this->assertDatabaseCount('payments', 1);
        $this->assertSame('confirmed', $reservation->fresh()->status);
    }

    public function test_customer_cannot_confirm_another_customers_deposit(): void
    {
        [, $reservation, $deposit] = $this->createPendingDeposit();
        $otherCustomer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);

        $this->submitDemoPayment($otherCustomer, $reservation, $deposit)
            ->assertForbidden();

        $this->assertSame(Payment::STATUS_PENDING, $deposit->fresh()->status);
        $this->assertSame('pending', $reservation->fresh()->status);
    }

    public function test_payment_route_rejects_a_deposit_from_another_reservation(): void
    {
        [$customer, $firstReservation, $firstDeposit] = $this->createPendingDeposit();
        [, $secondReservation] = $this->createPendingDeposit();
        $secondReservation->update(['user_id' => $customer->id]);

        $this->actingAs($customer)
            ->post(route('reservations.payments.demo-confirmation', [
                'reservation' => $secondReservation->id,
                'payment' => $firstDeposit->id,
            ]), $this->validCardPayload())
            ->assertNotFound();

        $this->assertSame(Payment::STATUS_PENDING, $firstDeposit->fresh()->status);
        $this->assertSame('pending', $firstReservation->fresh()->status);
    }
}
