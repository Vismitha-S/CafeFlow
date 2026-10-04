<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Models\Cafe;
use App\Models\Reservation;
use App\Services\CafeRepository;
use App\Services\ReservationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected ReservationService $reservationService
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

        $reservation->load(['cafe', 'cafeTable', 'user']);

        return (new ReservationResource($reservation))->response();
    }

    // Store a new reservation for the cafe
    public function store(StoreReservationRequest $request, Cafe $cafe)
    {
        $this->authorize('create', Reservation::class);

        $reservation = $this->reservationService->createReservation(
            $request->user(),
            $cafe,
            $request->validated()
        );

        $reservation->load(['cafe', 'cafeTable', 'user']);

        if ($request->wantsJson()) {
            return (new ReservationResource($reservation))
                ->response()
                ->setStatusCode(201);
        }

        return redirect()->route('reservations.show', $reservation->id)
            ->with('success', 'Reservation booked successfully.');
    }
}
