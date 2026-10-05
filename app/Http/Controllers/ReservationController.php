<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\ReservationResource;
use App\Models\Cafe;
use App\Models\Reservation;
use App\Notifications\NewReservationNotification;
use App\Services\CafeRepository;
use App\Services\DecimalMoney;
use App\Services\PaymentService;
use App\Services\ReservationCancellationService;
use App\Services\ReservationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected ReservationService $reservationService,
        protected PaymentService $paymentService,
        protected ReservationCancellationService $cancellationService,
    ) {}

    // List reservations for the authenticated user based on role
    public function index(Request $request)
    {
        $user = $request->user();

        if ($request->wantsJson()) {
            $data = $this->reservationService->getReservationsForUser($user);

            if ($user->isCustomer()) {
                return response()->json([
                    'upcoming' => ReservationResource::collection($data['upcoming']),
                    'past' => ReservationResource::collection($data['past']),
                    'data' => ReservationResource::collection($data['all']),
                ]);
            }

            return response()->json([
                'reservations' => ReservationResource::collection($data),
                'data' => ReservationResource::collection($data),
            ]);
        }

        // Return Blade view for browser requests if customer
        if ($user->isCustomer()) {
            $reservations = CafeRepository::getCustomerReservations();
            $favourites = array_values(array_filter(CafeRepository::all(), fn ($c) => $c['is_favourite']));

            return view('customer.reservations', compact('reservations', 'favourites'));
        }

        return redirect()->route('dashboard');
    }

    // Display a specific reservation
    public function show(Request $request, Reservation $reservation): JsonResponse
    {
        $this->authorize('view', $reservation);

        $reservation->load(['cafe', 'cafeTable', 'user', 'payments']);

        return (new ReservationResource($reservation))->response();
    }

    // Store a new reservation for the cafe
    public function store(StoreReservationRequest $request, Cafe $cafe)
    {
        $this->authorize('create', Reservation::class);

        $validated = $request->validated();
        $checkoutFlow = $request->input('_checkout') === '1';

        $reservation = DB::transaction(function () use ($request, $cafe, $validated, $checkoutFlow): Reservation {
            $reservation = $this->reservationService->createReservation(
                $request->user(),
                $cafe,
                $validated,
            );

            if (
                $checkoutFlow
                && DecimalMoney::toMinorUnits((string) $reservation->reservation_fee) > 0
            ) {
                $this->paymentService->createDepositPayment($reservation);
            }

            return $reservation;
        });

        $reservation->load(['cafe.owner', 'cafeTable', 'user', 'payments']);

        if ($reservation->cafe && $reservation->cafe->owner) {
            $reservation->cafe->owner->notify(new NewReservationNotification($reservation));
        }

        if ($request->wantsJson()) {
            return (new ReservationResource($reservation))
                ->response()
                ->setStatusCode(201);
        }

        if ($checkoutFlow) {
            return redirect()
                ->route('customer.reservation.checkout', ['reservation' => $reservation->id])
                ->with('reservation_created', true)
                ->with('show_payment_form', DecimalMoney::toMinorUnits((string) $reservation->reservation_fee) > 0);
        }

        return redirect()->route('reservations.show', $reservation->id)
            ->with('success', 'Reservation booked successfully.');
    }

    public function startDepositPayment(Reservation $reservation): JsonResponse
    {
        $this->authorize('pay', $reservation);

        $payment = $this->paymentService->createDepositPayment($reservation);

        return (new PaymentResource($payment))->response();
    }

    public function cancel(Request $request, Reservation $reservation): JsonResponse
    {
        $this->authorize('cancel', $reservation);

        $validated = $request->validate([
            'cancellation_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $reservation = $this->cancellationService->cancelReservation(
            $reservation,
            $validated['cancellation_reason'] ?? null,
        );
        $reservation->load(['cafe', 'cafeTable', 'user', 'payments']);

        return (new ReservationResource($reservation))->response();
    }
}
