<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function createDepositPayment(Reservation $reservation): Payment
    {
        return DB::transaction(function () use ($reservation): Payment {
            $lockedReservation = Reservation::query()
                ->whereKey($reservation->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $paidDeposit = Payment::query()
                ->where('reservation_id', $lockedReservation->id)
                ->where('type', Payment::TYPE_DEPOSIT)
                ->where('status', Payment::STATUS_PAID)
                ->lockForUpdate()
                ->first();

            if ($paidDeposit) {
                return $paidDeposit;
            }

            if ($lockedReservation->status !== 'pending') {
                throw ValidationException::withMessages([
                    'reservation' => ['A deposit can only be created for a pending reservation.'],
                ]);
            }

            $pendingDeposit = Payment::query()
                ->where('reservation_id', $lockedReservation->id)
                ->where('type', Payment::TYPE_DEPOSIT)
                ->where('status', Payment::STATUS_PENDING)
                ->lockForUpdate()
                ->first();

            if ($pendingDeposit) {
                return $pendingDeposit;
            }

            if (DecimalMoney::toMinorUnits((string) $lockedReservation->reservation_fee) === 0) {
                throw ValidationException::withMessages([
                    'reservation' => ['This reservation does not require a deposit payment.'],
                ]);
            }

            return Payment::query()->create([
                'reservation_id' => $lockedReservation->id,
                'user_id' => $lockedReservation->user_id,
                'type' => Payment::TYPE_DEPOSIT,
                'status' => Payment::STATUS_PENDING,
                'amount' => (string) $lockedReservation->reservation_fee,
            ]);
        });
    }

    public function markDepositAsPaid(
        Payment $payment,
        ?string $provider = null,
        ?string $providerReference = null,
    ): Payment {
        return DB::transaction(function () use ($payment, $provider, $providerReference): Payment {
            $paymentReservationId = Payment::query()->whereKey($payment->getKey())->value('reservation_id');

            if ($paymentReservationId === null) {
                throw (new ModelNotFoundException)->setModel(Payment::class, [$payment->getKey()]);
            }

            $reservation = Reservation::query()
                ->whereKey($paymentReservationId)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedPayment = Payment::query()
                ->whereKey($payment->getKey())
                ->where('reservation_id', $reservation->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedPayment->type !== Payment::TYPE_DEPOSIT) {
                throw ValidationException::withMessages([
                    'payment' => ['Only a deposit payment can be marked as paid.'],
                ]);
            }

            if ($lockedPayment->status === Payment::STATUS_PAID) {
                return $lockedPayment;
            }

            if ($lockedPayment->status !== Payment::STATUS_PENDING || $reservation->status !== 'pending') {
                throw ValidationException::withMessages([
                    'payment' => ['This deposit payment can no longer be confirmed.'],
                ]);
            }

            $anotherPaidDepositExists = Payment::query()
                ->where('reservation_id', $reservation->id)
                ->where('type', Payment::TYPE_DEPOSIT)
                ->where('status', Payment::STATUS_PAID)
                ->where('id', '<>', $lockedPayment->id)
                ->lockForUpdate()
                ->exists();

            if ($anotherPaidDepositExists) {
                throw ValidationException::withMessages([
                    'payment' => ['A deposit has already been paid for this reservation.'],
                ]);
            }

            $paymentAttributes = [
                'status' => Payment::STATUS_PAID,
                'paid_at' => now(),
                'failed_at' => null,
                'failure_reason' => null,
            ];

            if ($provider !== null) {
                $paymentAttributes['provider'] = $provider;
            }

            if ($providerReference !== null) {
                $paymentAttributes['provider_reference'] = $providerReference;
            }

            $lockedPayment->forceFill($paymentAttributes)->save();

            $reservation->forceFill(['status' => 'confirmed'])->save();

            return $lockedPayment->refresh();
        });
    }

    public function markPaymentAsFailed(Payment $payment, ?string $failureReason = null): Payment
    {
        return DB::transaction(function () use ($payment, $failureReason): Payment {
            $paymentReservationId = Payment::query()->whereKey($payment->getKey())->value('reservation_id');

            if ($paymentReservationId === null) {
                throw (new ModelNotFoundException)->setModel(Payment::class, [$payment->getKey()]);
            }

            $reservation = Reservation::query()
                ->whereKey($paymentReservationId)
                ->lockForUpdate()
                ->firstOrFail();

            $lockedPayment = Payment::query()
                ->whereKey($payment->getKey())
                ->where('reservation_id', $reservation->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedPayment->type !== Payment::TYPE_DEPOSIT) {
                throw ValidationException::withMessages([
                    'payment' => ['Only a deposit payment can be marked as failed.'],
                ]);
            }

            if ($lockedPayment->status === Payment::STATUS_FAILED) {
                return $lockedPayment;
            }

            if ($lockedPayment->status !== Payment::STATUS_PENDING) {
                throw ValidationException::withMessages([
                    'payment' => ['Only a pending deposit payment can be marked as failed.'],
                ]);
            }

            $lockedPayment->forceFill([
                'status' => Payment::STATUS_FAILED,
                'failed_at' => now(),
                'failure_reason' => $failureReason,
            ])->save();

            return $lockedPayment->refresh();
        });
    }

    public function createRefund(Reservation $reservation, Payment $depositPayment, string $refundAmount): Payment
    {
        return DB::transaction(function () use ($reservation, $depositPayment, $refundAmount): Payment {
            $lockedReservation = Reservation::query()
                ->whereKey($reservation->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $lockedDeposit = Payment::query()
                ->whereKey($depositPayment->getKey())
                ->where('reservation_id', $lockedReservation->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedDeposit->type !== Payment::TYPE_DEPOSIT || $lockedDeposit->status !== Payment::STATUS_PAID) {
                throw ValidationException::withMessages([
                    'payment' => ['A refund requires a paid deposit payment.'],
                ]);
            }

            $refundInMinorUnits = DecimalMoney::toMinorUnits($refundAmount);
            $depositInMinorUnits = DecimalMoney::toMinorUnits((string) $lockedDeposit->amount);

            if ($refundInMinorUnits === 0 || $refundInMinorUnits > $depositInMinorUnits) {
                throw ValidationException::withMessages([
                    'payment' => ['The refund amount must be greater than zero and cannot exceed the paid deposit.'],
                ]);
            }

            $existingRefund = Payment::query()
                ->where('refund_of_payment_id', $lockedDeposit->id)
                ->lockForUpdate()
                ->first();

            if ($existingRefund) {
                return $existingRefund;
            }

            return Payment::query()->create([
                'reservation_id' => $lockedReservation->id,
                'user_id' => $lockedReservation->user_id,
                'refund_of_payment_id' => $lockedDeposit->id,
                'type' => Payment::TYPE_REFUND,
                'status' => Payment::STATUS_PENDING,
                'amount' => DecimalMoney::fromMinorUnits($refundInMinorUnits),
            ]);
        });
    }
}
