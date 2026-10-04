<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    private PaymentService $paymentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentService = app(PaymentService::class);
    }

    public function test_deposit_uses_reservation_fee_snapshot_and_reuses_pending_payment(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $reservation = Reservation::factory()->pending()->create([
            'user_id' => $customer->id,
            'reservation_fee' => '45.75',
        ]);
        $reservation->cafe->update(['reservation_fee' => '900.00']);

        $payment = $this->paymentService->createDepositPayment($reservation);
        $reusedPayment = $this->paymentService->createDepositPayment($reservation->fresh());

        $this->assertSame($payment->id, $reusedPayment->id);
        $this->assertDatabaseCount('payments', 1);
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'reservation_id' => $reservation->id,
            'user_id' => $customer->id,
            'type' => Payment::TYPE_DEPOSIT,
            'status' => Payment::STATUS_PENDING,
            'amount' => '45.75',
        ]);
    }

    public function test_successful_deposit_confirms_reservation_once(): void
    {
        $reservation = Reservation::factory()->pending()->create(['reservation_fee' => '32.40']);
        $payment = $this->paymentService->createDepositPayment($reservation);

        $paidPayment = $this->paymentService->markDepositAsPaid($payment);
        $repeatedPayment = $this->paymentService->markDepositAsPaid($payment);

        $this->assertSame(Payment::STATUS_PAID, $paidPayment->status);
        $this->assertSame($paidPayment->id, $repeatedPayment->id);
        $this->assertNotNull($paidPayment->paid_at);
        $this->assertSame('confirmed', $reservation->fresh()->status);
        $this->assertDatabaseCount('payments', 1);
    }

    public function test_failed_deposit_leaves_reservation_pending_and_allows_retry(): void
    {
        $reservation = Reservation::factory()->pending()->create();
        $failedPayment = $this->paymentService->createDepositPayment($reservation);

        $failedPayment = $this->paymentService->markPaymentAsFailed($failedPayment, 'Declined');
        $retryPayment = $this->paymentService->createDepositPayment($reservation->fresh());

        $this->assertSame(Payment::STATUS_FAILED, $failedPayment->status);
        $this->assertNotNull($failedPayment->failed_at);
        $this->assertSame('Declined', $failedPayment->failure_reason);
        $this->assertSame('pending', $reservation->fresh()->status);
        $this->assertNotSame($failedPayment->id, $retryPayment->id);
        $this->assertSame(Payment::STATUS_PENDING, $retryPayment->status);
    }

    public function test_failed_deposit_cannot_be_confirmed(): void
    {
        $reservation = Reservation::factory()->pending()->create();
        $payment = $this->paymentService->createDepositPayment($reservation);
        $this->paymentService->markPaymentAsFailed($payment);

        $this->expectException(ValidationException::class);
        $this->paymentService->markDepositAsPaid($payment);
    }

    public function test_only_one_deposit_attempt_can_be_paid_for_a_reservation(): void
    {
        $reservation = Reservation::factory()->pending()->create();
        $firstPayment = $this->paymentService->createDepositPayment($reservation);
        $secondPayment = Payment::query()->create([
            'reservation_id' => $reservation->id,
            'user_id' => $reservation->user_id,
            'type' => Payment::TYPE_DEPOSIT,
            'status' => Payment::STATUS_PENDING,
            'amount' => (string) $reservation->reservation_fee,
        ]);

        $this->paymentService->markDepositAsPaid($firstPayment);

        $this->expectException(ValidationException::class);
        $this->paymentService->markDepositAsPaid($secondPayment);
    }

    public function test_refund_is_pending_and_idempotent_for_a_paid_deposit(): void
    {
        $reservation = Reservation::factory()->pending()->create(['reservation_fee' => '100.00']);
        $deposit = $this->paymentService->createDepositPayment($reservation);
        $deposit = $this->paymentService->markDepositAsPaid($deposit);

        $refund = $this->paymentService->createRefund($reservation, $deposit, '62.50');
        $reusedRefund = $this->paymentService->createRefund($reservation, $deposit, '62.50');

        $this->assertSame($refund->id, $reusedRefund->id);
        $this->assertDatabaseCount('payments', 2);
        $this->assertDatabaseHas('payments', [
            'id' => $refund->id,
            'reservation_id' => $reservation->id,
            'user_id' => $reservation->user_id,
            'refund_of_payment_id' => $deposit->id,
            'type' => Payment::TYPE_REFUND,
            'status' => Payment::STATUS_PENDING,
            'amount' => '62.50',
        ]);
        $this->assertNull($refund->paid_at);
    }

    public function test_refund_requires_a_paid_deposit_and_cannot_exceed_it(): void
    {
        $reservation = Reservation::factory()->pending()->create(['reservation_fee' => '10.00']);
        $deposit = $this->paymentService->createDepositPayment($reservation);

        try {
            $this->paymentService->createRefund($reservation, $deposit, '5.00');
            $this->fail('An unpaid deposit must not be refundable.');
        } catch (ValidationException) {
            $this->assertDatabaseCount('payments', 1);
        }

        $deposit = $this->paymentService->markDepositAsPaid($deposit);

        $this->expectException(ValidationException::class);
        $this->paymentService->createRefund($reservation, $deposit, '10.01');
    }

    public function test_payment_factory_creates_a_deposit_linked_to_its_reservation_owner(): void
    {
        $payment = Payment::factory()->create();

        $this->assertSame(Payment::TYPE_DEPOSIT, $payment->type);
        $this->assertSame($payment->reservation->user_id, $payment->user_id);
        $this->assertSame('10.00', $payment->amount);
        $this->assertSame($payment->id, $payment->reservation->payments->first()->id);
        $this->assertSame($payment->id, $payment->user->payments->first()->id);

        $this->assertNull($payment->refundOf);
    }

    public function test_legacy_currency_provider_refund_and_metadata_fields_are_cast(): void
    {
        $payment = new Payment;
        $payment->setRawAttributes([
            'amount' => '80.00',
            'currency' => 'LKR',
            'provider' => 'legacy-provider',
            'provider_reference' => 'legacy-reference',
            'refunded_at' => '2026-10-04 12:00:00',
            'refund_amount' => '25.50',
            'metadata' => '{"source":"legacy"}',
        ]);

        $this->assertSame('LKR', $payment->currency);
        $this->assertSame('legacy-provider', $payment->provider);
        $this->assertSame('legacy-reference', $payment->provider_reference);
        $this->assertInstanceOf(\Carbon\Carbon::class, $payment->refunded_at);
        $this->assertSame('25.50', $payment->refund_amount);
        $this->assertSame(['source' => 'legacy'], $payment->metadata);
    }
}
