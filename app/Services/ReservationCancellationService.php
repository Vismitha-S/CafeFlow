<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReservationCancellationService
{
    public function __construct(private PaymentService $paymentService) {}

    public function validateEligibility(Reservation $reservation): void
    {
        $eligibleStatuses = [
            config('reservations.statuses.pending', 'pending'),
            config('reservations.statuses.confirmed', 'confirmed'),
        ];

        if (! in_array($reservation->status, $eligibleStatuses, true)) {
            throw ValidationException::withMessages([
                'reservation' => ['Only pending or confirmed reservations can be cancelled.'],
            ]);
        }
    }

    public function cancelReservation(Reservation $reservation, ?string $reason = null): Reservation
    {
        return DB::transaction(function () use ($reservation, $reason): Reservation {
            $lockedReservation = Reservation::query()
                ->whereKey($reservation->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $this->validateEligibility($lockedReservation);

            $reservationFee = (string) $lockedReservation->reservation_fee;
            $penaltyAmount = DecimalMoney::percentageOf(
                $reservationFee,
                (string) $lockedReservation->cancellation_penalty_percentage,
            );
            $refundAmountInMinorUnits = DecimalMoney::toMinorUnits($reservationFee)
                - DecimalMoney::toMinorUnits($penaltyAmount);

            $lockedReservation->forceFill([
                'status' => config('reservations.statuses.cancelled', 'cancelled'),
                'cancellation_penalty_amount' => $penaltyAmount,
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
            ])->save();

            $pendingDeposits = Payment::query()
                ->where('reservation_id', $lockedReservation->id)
                ->where('type', Payment::TYPE_DEPOSIT)
                ->where('status', Payment::STATUS_PENDING)
                ->lockForUpdate()
                ->get();

            foreach ($pendingDeposits as $pendingDeposit) {
                $this->paymentService->markPaymentAsFailed(
                    $pendingDeposit,
                    'Reservation cancelled before the deposit was paid.',
                );
            }

            $paidDeposit = Payment::query()
                ->where('reservation_id', $lockedReservation->id)
                ->where('type', Payment::TYPE_DEPOSIT)
                ->where('status', Payment::STATUS_PAID)
                ->lockForUpdate()
                ->first();

            if ($paidDeposit && $refundAmountInMinorUnits > 0) {
                $this->paymentService->createRefund(
                    $lockedReservation,
                    $paidDeposit,
                    DecimalMoney::fromMinorUnits($refundAmountInMinorUnits),
                );
            }

            return $lockedReservation->refresh();
        });
    }
}
