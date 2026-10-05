<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Reservation;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator as ValidatorInstance;

class PaymentController extends Controller
{
    use AuthorizesRequests;

    public function confirmDemo(
        Request $request,
        Reservation $reservation,
        Payment $payment,
        PaymentService $paymentService,
    ): RedirectResponse {
        $this->authorize('pay', $reservation);

        $deposit = $reservation->payments()
            ->whereKey($payment->getKey())
            ->where('type', Payment::TYPE_DEPOSIT)
            ->firstOrFail();

        if ($deposit->status === Payment::STATUS_PAID) {
            $paymentService->markDepositAsPaid($deposit);

            return $this->paymentConfirmedRedirect($reservation);
        }

        if ($deposit->status !== Payment::STATUS_PENDING || $reservation->status !== 'pending') {
            return redirect()
                ->route('customer.reservation.checkout', ['reservation' => $reservation->id])
                ->withErrors(['payment' => 'This payment is no longer pending.']);
        }

        $cardholderName = trim((string) $request->input('cardholder_name'));
        $cardNumber = preg_replace('/\s+/', '', (string) $request->input('card_number'));
        $expiryDate = trim((string) $request->input('expiry_date'));
        $cvv = trim((string) $request->input('cvv'));

        $request->replace([]);
        $request->session()->forget('_old_input');

        $validator = Validator::make([
            'cardholder_name' => $cardholderName,
            'card_number' => $cardNumber,
            'expiry_date' => $expiryDate,
            'cvv' => $cvv,
        ], [
            'cardholder_name' => ['required', 'string', 'min:2', 'max:120'],
            'card_number' => ['required', 'regex:/\A[0-9]{13,19}\z/'],
            'expiry_date' => ['required', 'regex:/\A(0[1-9]|1[0-2])\/[0-9]{2}\z/'],
            'cvv' => ['required', 'regex:/\A[0-9]{3,4}\z/'],
        ], [
            'card_number.regex' => 'Enter a valid card number.',
            'expiry_date.regex' => 'Enter an expiry date in MM/YY format.',
            'cvv.regex' => 'Enter a valid card security code.',
        ], [
            'cardholder_name' => 'cardholder name',
            'card_number' => 'card number',
            'expiry_date' => 'expiry date',
            'cvv' => 'card security code',
        ]);

        $validator->after(function (ValidatorInstance $validator) use ($cardNumber, $expiryDate): void {
            if (preg_match('/\A[0-9]{13,19}\z/', $cardNumber) === 1 && ! $this->passesLuhnCheck($cardNumber)) {
                $validator->errors()->add('card_number', 'Enter a valid card number.');
            }

            if (preg_match('/\A(0[1-9]|1[0-2])\/[0-9]{2}\z/', $expiryDate) === 1) {
                try {
                    $expiresAt = Carbon::createFromFormat('!m/y', $expiryDate)->endOfMonth()->endOfDay();
                    if ($expiresAt->isPast()) {
                        $validator->errors()->add('expiry_date', 'The card has expired.');
                    }
                } catch (\Throwable) {
                    $validator->errors()->add('expiry_date', 'Enter a valid expiry date.');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()
                ->route('customer.reservation.checkout', ['reservation' => $reservation->id])
                ->withErrors($validator)
                ->withInput(['cardholder_name' => $cardholderName])
                ->with('show_payment_form', true);
        }

        $paymentService->markDepositAsPaid(
            $deposit,
            'demo',
            'DEMO-'.Str::upper(Str::random(16)),
        );

        return $this->paymentConfirmedRedirect($reservation);
    }

    private function paymentConfirmedRedirect(Reservation $reservation): RedirectResponse
    {
        return redirect()
            ->route('customer.reservation.checkout', ['reservation' => $reservation->id])
            ->with('payment_confirmed', true);
    }

    private function passesLuhnCheck(string $cardNumber): bool
    {
        $sum = 0;
        $shouldDouble = false;

        foreach (array_reverse(str_split($cardNumber)) as $digit) {
            $value = (int) $digit;

            if ($shouldDouble) {
                $value *= 2;
                if ($value > 9) {
                    $value -= 9;
                }
            }

            $sum += $value;
            $shouldDouble = ! $shouldDouble;
        }

        return $sum % 10 === 0;
    }
}
