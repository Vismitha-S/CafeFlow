<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\CafeRepository;

// Handles authenticated customer dashboard, cafe exploration, details, and reservation UI flows
class CustomerController extends Controller
{
    // Render personalized customer home
    public function home()
    {
        $user = Auth::user();
        $allCafes = CafeRepository::all();

        // Calculate time of day greeting
        $hour = (int) date('H');
        if ($hour < 12) {
            $greeting = 'Good morning';
        } elseif ($hour < 17) {
            $greeting = 'Good afternoon';
        } else {
            $greeting = 'Good evening';
        }

        // Segment cafes into categories
        $recommendedCafes = array_slice($allCafes, 0, 3);
        $nearbyCafes = array_slice($allCafes, 1, 4);
        $popularCafes = array_slice($allCafes, 0, 4);
        $recentlyViewed = [
            $allCafes[0],
            $allCafes[2],
            $allCafes[3],
        ];

        return view('dashboards.customer', compact(
            'user',
            'greeting',
            'allCafes',
            'recommendedCafes',
            'nearbyCafes',
            'popularCafes',
            'recentlyViewed'
        ));
    }

    // Render Explore Cafes discovery page
    public function explore(Request $request)
    {
        $allCafes = CafeRepository::all();
        $query = strtolower(trim((string) $request->input('q', '')));
        $category = $request->input('category', 'all');
        $location = $request->input('location', 'all');
        $type = $request->input('type', 'all');

        // Apply filters
        $filteredCafes = array_filter($allCafes, function ($cafe) use ($query, $category, $location, $type) {
            if ($query !== '') {
                $searchable = strtolower($cafe['name'] . ' ' . $cafe['location'] . ' ' . $cafe['cuisine'] . ' ' . implode(' ', $cafe['tags']));
                if (!str_contains($searchable, $query)) {
                    return false;
                }
            }

            if ($category !== 'all') {
                $categorySlug = str_replace('-', ' ', strtolower($category));
                $foundTag = false;
                foreach ($cafe['tags'] as $tag) {
                    if (str_contains(strtolower($tag), $categorySlug)) {
                        $foundTag = true;
                        break;
                    }
                }
                if (!$foundTag && !str_contains(strtolower($cafe['cafe_type']), $categorySlug)) {
                    return false;
                }
            }

            if ($location !== 'all' && !str_contains(strtolower($cafe['location']), strtolower($location))) {
                return false;
            }

            if ($type !== 'all' && !str_contains(strtolower($cafe['cafe_type']), strtolower($type))) {
                return false;
            }

            return true;
        });

        // Re-index array
        $cafes = array_values($filteredCafes);

        return view('customer.explore', compact('cafes', 'query', 'category', 'location', 'type'));
    }

    // Render cafe details page with 5 tabs
    public function showCafe(string $slug)
    {
        $cafe = CafeRepository::findBySlug($slug);
        if (!$cafe) {
            abort(404, 'Cafe not found');
        }

        $tables = CafeRepository::getTablesForCafe($slug);
        $menu = CafeRepository::getMenuForCafe($slug);
        $allCafes = CafeRepository::all();

        return view('customer.cafe-details', compact('cafe', 'tables', 'menu', 'allCafes'));
    }

    // Render reservation checkout screen
    public function checkout(Request $request)
    {
        $slug = $request->input('cafe', 'the-velvet-bean');
        $tableId = (int) $request->input('table', 2);
        $date = $request->input('date', 'Fri, Oct 4, 2026');
        $time = $request->input('time', '10:30 AM');
        $guests = $request->input('guests', '2 Guests');

        $cafe = CafeRepository::findBySlug($slug);
        $tables = CafeRepository::getTablesForCafe($slug);

        // Find selected table
        $selectedTable = null;
        foreach ($tables as $t) {
            if ($t['id'] === $tableId) {
                $selectedTable = $t;
                break;
            }
        }

        if (!$selectedTable) {
            $selectedTable = $tables[1] ?? $tables[0];
        }

        // Dynamic fee from cafe model
        $reservationFee = $cafe['reservation_fee'] ?? 500;
        $platformFee = 0;
        $totalAmount = $reservationFee + $platformFee;

        return view('customer.reservation-checkout', compact(
            'cafe',
            'selectedTable',
            'date',
            'time',
            'guests',
            'reservationFee',
            'platformFee',
            'totalAmount'
        ));
    }

    // Render customer's reservations page
    public function reservations()
    {
        $reservations = CafeRepository::getCustomerReservations();
        $favourites = array_filter(CafeRepository::all(), fn($c) => $c['is_favourite']);

        return view('customer.reservations', compact('reservations', 'favourites'));
    }

    // Render customer's favourites page
    public function favourites()
    {
        $allCafes = CafeRepository::all();
        $favourites = array_values(array_filter($allCafes, fn($c) => $c['is_favourite']));

        return view('customer.favourites', compact('favourites', 'allCafes'));
    }
}
