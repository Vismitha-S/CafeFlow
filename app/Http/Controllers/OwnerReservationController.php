<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Services\ReservationCancellationService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerReservationController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected ReservationCancellationService $cancellationService
    ) {}

    /**
     * Display reservations for the owner's cafe.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $owner = $request->user();
        $cafe = $owner->cafe;

        if (! $cafe) {
            return redirect()->route('owner.cafe.create');
        }

        $query = Reservation::query()
            ->where('cafe_id', $cafe->id)
            ->with(['user', 'cafeTable', 'payments']);

        // Filter by Date Range / Tab
        $timeFilter = $request->input('time_filter', 'all');
        $today = Carbon::today()->toDateString();
        $nowTime = Carbon::now()->format('H:i:s');

        if ($timeFilter === 'today') {
            $query->whereDate('reservation_date', $today);
        } elseif ($timeFilter === 'upcoming') {
            $query->where(function ($q) use ($today, $nowTime) {
                $q->whereDate('reservation_date', '>', $today)
                    ->orWhere(function ($sub) use ($today, $nowTime) {
                        $sub->whereDate('reservation_date', $today)
                            ->where('end_time', '>=', $nowTime);
                    });
            })->whereIn('status', ['pending', 'confirmed']);
        } elseif ($timeFilter === 'past') {
            $query->where(function ($q) use ($today, $nowTime) {
                $q->whereDate('reservation_date', '<', $today)
                    ->orWhere(function ($sub) use ($today, $nowTime) {
                        $sub->whereDate('reservation_date', $today)
                            ->where('end_time', '<', $nowTime);
                    });
            });
        }

        // Filter by Status
        $statusFilter = $request->input('status', 'all');
        if ($statusFilter !== 'all' && in_array($statusFilter, ['pending', 'confirmed', 'completed', 'cancelled'])) {
            $query->where('status', $statusFilter);
        }

        // Search Query
        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'ilike', "%{$search}%")
                            ->orWhere('email', 'ilike', "%{$search}%");
                    })
                    ->orWhereHas('cafeTable', function ($tq) use ($search) {
                        $tq->where('name', 'ilike', "%{$search}%")
                            ->orWhere('table_number', 'like', "%{$search}%");
                    });
            });
        }

        $reservations = $query->orderBy('reservation_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Summary counts for tabs
        $todayCount = Reservation::where('cafe_id', $cafe->id)->whereDate('reservation_date', $today)->count();
        $upcomingCount = Reservation::where('cafe_id', $cafe->id)
            ->whereDate('reservation_date', '>=', $today)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();
        $totalCount = Reservation::where('cafe_id', $cafe->id)->count();

        $highlightId = $request->integer('highlight');

        return view('owner.reservations.index', compact(
            'owner',
            'cafe',
            'reservations',
            'timeFilter',
            'statusFilter',
            'search',
            'todayCount',
            'upcomingCount',
            'totalCount',
            'highlightId',
        ));
    }

    /**
     * Show detailed reservation information.
     */
    public function show(Request $request, Reservation $reservation): JsonResponse|View
    {
        $this->authorize('view', $reservation);

        $reservation->load(['cafe', 'cafeTable', 'user', 'payments']);

        if ($request->wantsJson()) {
            return response()->json($reservation);
        }

        return view('owner.reservations.show', compact('reservation'));
    }

    /**
     * Mark a reservation as completed.
     */
    public function complete(Request $request, Reservation $reservation): RedirectResponse
    {
        $this->authorize('update', $reservation);

        if ($reservation->status === 'cancelled') {
            return redirect()->back()->with('error', 'Cannot complete a cancelled reservation.');
        }

        $reservation->update(['status' => 'completed']);

        return redirect()->back()
            ->with('success', "Reservation #RES-{$reservation->id} marked as completed.");
    }

    /**
     * Cancel a reservation as owner.
     */
    public function cancel(Request $request, Reservation $reservation): RedirectResponse
    {
        $this->authorize('cancel', $reservation);

        $validated = $request->validate([
            'cancellation_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->cancellationService->cancelReservation(
            $reservation,
            $validated['cancellation_reason'] ?? 'Cancelled by cafe owner.'
        );

        return redirect()->back()
            ->with('success', "Reservation #RES-{$reservation->id} has been cancelled.");
    }
}
