<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller for the Cafe Owner Dashboard.
 *
 * Scopes all analytics, cafe details, reservations, tables,
 * and menu data exclusively to the authenticated owner's single cafe.
 */
class OwnerDashboardController extends Controller
{
    /**
     * Render the owner dashboard view with live cafe data.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $owner = $request->user();

        // 1 Owner = 1 Cafe rule: retrieve the owner's single cafe
        $cafe = $owner->cafe()
            ->with([
                'tables',
                'menuCategories.menuItems',
                'menuItems.category',
                'hours',
            ])
            ->first();

        // If the owner does not have a cafe yet, redirect to the onboarding setup page
        if (! $cafe) {
            return redirect()->route('owner.cafe.create')
                ->with('info', 'Welcome to CafeFlow! Please set up your cafe profile to begin managing bookings and tables.');
        }

        $today = Carbon::today()->toDateString();
        $nowTime = Carbon::now()->format('H:i:s');
        $monthStart = Carbon::now()->startOfMonth();

        // 1. Today's Reservations
        $todayReservations = Reservation::query()
            ->where('cafe_id', $cafe->id)
            ->whereDate('reservation_date', $today)
            ->with(['user', 'cafeTable', 'payments'])
            ->orderBy('start_time', 'asc')
            ->get();

        $todayCount = $todayReservations->count();
        $todayConfirmedCount = $todayReservations->where('status', 'confirmed')->count();
        $todayPendingCount = $todayReservations->where('status', 'pending')->count();

        // 2. Upcoming Reservations
        $upcomingReservations = Reservation::query()
            ->where('cafe_id', $cafe->id)
            ->where(function ($query) use ($today, $nowTime) {
                $query->whereDate('reservation_date', '>', $today)
                    ->orWhere(function ($sub) use ($today, $nowTime) {
                        $sub->whereDate('reservation_date', $today)
                            ->where('end_time', '>=', $nowTime);
                    });
            })
            ->whereIn('status', ['pending', 'confirmed'])
            ->with(['user', 'cafeTable', 'payments'])
            ->orderBy('reservation_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->take(6)
            ->get();

        $upcomingCount = Reservation::query()
            ->where('cafe_id', $cafe->id)
            ->where('reservation_date', '>=', $today)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        // 3. Recent Reservations
        $recentReservations = Reservation::query()
            ->where('cafe_id', $cafe->id)
            ->with(['user', 'cafeTable', 'payments'])
            ->orderBy('reservation_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->take(8)
            ->get();

        // 4. Tables and Seating Capacity Summary
        $tables = $cafe->tables()->orderBy('table_number', 'asc')->get();
        $totalTablesCount = $tables->count();
        $activeTables = $tables->where('status', 'active');
        $activeTablesCount = $activeTables->count();
        $totalCapacity = (int) $activeTables->sum('capacity');

        $indoorCount = $activeTables->where('location', 'indoor')->count();
        $outdoorCount = $activeTables->where('location', 'outdoor')->count();
        $rooftopCount = $activeTables->where('location', 'rooftop')->count();
        $verandahCount = $activeTables->where('location', 'verandah')->count();

        // 5. Menu Categories and Items Summary
        $menuCategories = $cafe->menuCategories()
            ->withCount('menuItems')
            ->orderBy('sort_order', 'asc')
            ->get();

        $menuItems = $cafe->menuItems()
            ->with('category')
            ->orderBy('sort_order', 'asc')
            ->get();

        $categoriesCount = $menuCategories->count();
        $menuItemsCount = $menuItems->count();
        $availableMenuItemsCount = $menuItems->where('status', 'active')->where('is_available', true)->count();

        // 6. Monthly Net Revenue:
        // - For confirmed/completed reservations with a paid deposit: the full deposit fee.
        // - For cancelled reservations with a paid deposit: ONLY the retained cancellation penalty deduction amount.
        $paidReservations = Reservation::query()
            ->where('cafe_id', $cafe->id)
            ->whereHas('payments', function ($query) use ($monthStart) {
                $query->where('type', Payment::TYPE_DEPOSIT)
                    ->where('status', Payment::STATUS_PAID)
                    ->where('created_at', '>=', $monthStart);
            })
            ->with(['payments' => function ($query) {
                $query->where('type', Payment::TYPE_DEPOSIT)
                    ->where('status', Payment::STATUS_PAID);
            }])
            ->get();

        $monthlyRevenue = (float) $paidReservations->sum(function (Reservation $res): float {
            $deposit = $res->payments->first();
            if (! $deposit) {
                return 0.0;
            }

            if ($res->status === 'cancelled') {
                return (float) ($res->cancellation_penalty_amount ?? 0.0);
            }

            return (float) $deposit->amount;
        });

        $hours = $cafe->hours()->orderBy('day_of_week', 'asc')->get();

        return view('dashboards.owner', compact(
            'owner',
            'cafe',
            'todayReservations',
            'todayCount',
            'todayConfirmedCount',
            'todayPendingCount',
            'upcomingReservations',
            'upcomingCount',
            'recentReservations',
            'tables',
            'totalTablesCount',
            'activeTablesCount',
            'totalCapacity',
            'indoorCount',
            'outdoorCount',
            'rooftopCount',
            'verandahCount',
            'menuCategories',
            'categoriesCount',
            'menuItems',
            'menuItemsCount',
            'availableMenuItemsCount',
            'monthlyRevenue',
            'hours',
        ));
    }
}
