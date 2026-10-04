<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reservation_id' => Reservation::factory(),
            'user_id' => null,
            'refund_of_payment_id' => null,
            'type' => Payment::TYPE_DEPOSIT,
            'status' => Payment::STATUS_PENDING,
            'amount' => '10.00',
            'paid_at' => null,
            'failed_at' => null,
            'failure_reason' => null,
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Payment $payment): void {
            if ($payment->reservation_id !== null) {
                $payment->user_id = Reservation::query()->findOrFail($payment->reservation_id)->user_id;
            }
        });
    }
}
