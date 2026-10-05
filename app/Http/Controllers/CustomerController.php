<?php

namespace App\Http\Controllers;

use App\Models\Cafe;
use App\Models\CafeTable;
use App\Models\MenuItem;
use App\Models\Payment;
use App\Models\Reservation;
use App\Services\CafeRepository;
use App\Services\DecimalMoney;
use App\Services\ReservationAvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

// Handles authenticated customer dashboard, cafe exploration, details, and reservation UI flows
class CustomerController extends Controller
{
    // Render personalized customer home
    public function home()
    {
        $user = Auth::user();
        $allCafes = $this->getDisplayCafes();

        // Calculate time of day greeting
        $hour = (int) date('H');
        if ($hour < 12) {
            $greeting = 'Good morning';
        } elseif ($hour < 17) {
            $greeting = 'Good afternoon';
        } else {
            $greeting = 'Good evening';
        }

        // Segment cafes into categories, ensuring newest cafes (at start of array) are visible
        $totalCount = count($allCafes);
        $recommendedCafes = array_slice($allCafes, 0, min(3, $totalCount));
        $nearbyCafes = array_slice($allCafes, 0, min(4, $totalCount));
        $popularCafes = array_slice($allCafes, 0, min(4, $totalCount));
        $recentlyViewed = array_slice($allCafes, 0, min(3, $totalCount));

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
        $allCafes = $this->getDisplayCafes();
        $query = strtolower(trim((string) $request->input('q', '')));
        $category = $request->input('category', 'all');
        $location = $request->input('location', 'all');
        $type = $request->input('type', 'all');

        // Apply filters
        $filteredCafes = array_filter($allCafes, function ($cafe) use ($query, $category, $location, $type) {
            if ($query !== '') {
                $searchable = strtolower($cafe['name'].' '.$cafe['location'].' '.$cafe['cuisine'].' '.implode(' ', $cafe['tags']));
                if (! str_contains($searchable, $query)) {
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
                if (! $foundTag && ! str_contains(strtolower($cafe['cafe_type']), $categorySlug)) {
                    return false;
                }
            }

            if ($location !== 'all' && ! str_contains(strtolower($cafe['location']), strtolower($location))) {
                return false;
            }

            if ($type !== 'all' && ! str_contains(strtolower($cafe['cafe_type']), strtolower($type))) {
                return false;
            }

            return true;
        });

        // Re-index array
        $cafes = array_values($filteredCafes);

        return view('customer.explore', compact('cafes', 'query', 'category', 'location', 'type'));
    }

    // Render cafe details page with 5 tabs
    public function showCafe(string $slug, Request $request, ReservationAvailabilityService $availabilityService)
    {
        $allCafes = $this->getDisplayCafes();
        $cafe = collect($allCafes)->firstWhere('slug', $slug);
        if (! $cafe) {
            abort(404, 'Cafe not found');
        }

        $validated = $request->validate([
            'date' => ['sometimes', 'date_format:Y-m-d'],
            'time' => ['sometimes', 'date_format:g:i A'],
            'guests' => ['sometimes', 'regex:/^\d+\s+Guests?$/i'],
        ]);

        $selectedDate = $validated['date'] ?? now()->toDateString();
        $selectedTimeLabel = $validated['time'] ?? '10:30 AM';
        $selectedTime = Carbon::parse($selectedTimeLabel)->format('H:i');
        $selectedGuestsLabel = $validated['guests'] ?? '2 Guests';
        $guestCount = (int) preg_replace('/\D+/', '', $selectedGuestsLabel);

        $storedCafe = Cafe::query()
            ->where('slug', $slug)
            ->where('status', 'active')
            ->first();

        $tables = [];
        $availableTableIds = [];
        $availableTimeSlots = [];
        $availabilityMessage = null;
        $availabilityUrl = null;
        $canReserve = false;

        if ($storedCafe) {
            $cafe = array_merge($cafe, [
                'id' => $storedCafe->id,
                'slug' => $storedCafe->slug,
                'name' => $storedCafe->name,
                'location' => $storedCafe->city,
                'address' => $storedCafe->address,
                'reservation_fee' => (string) $storedCafe->reservation_fee,
                'cancellation_penalty_percentage' => (string) $storedCafe->cancellation_penalty_percentage,
                'image' => $storedCafe->image_path ?: ($cafe['image'] ?? 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=1200&q=80'),
            ]);

            $activeTables = $storedCafe->tables()
                ->where('status', 'active')
                ->get()
                ->values();

            if ($activeTables->isNotEmpty()) {
                try {
                    $availableTimeSlots = $availabilityService->getAvailableTimeSlots(
                        $storedCafe,
                        $selectedDate,
                        $guestCount,
                    );

                    if ($availableTimeSlots !== []) {
                        $availableTimeValues = array_column($availableTimeSlots, 'value');
                        if (! in_array($selectedTime, $availableTimeValues, true)) {
                            $selectedTime = $availableTimeSlots[0]['value'];
                            $selectedTimeLabel = $availableTimeSlots[0]['label'];
                        }

                        $availableTableIds = $availabilityService
                            ->getAvailableTables($storedCafe, $selectedDate, $selectedTime, $guestCount)
                            ->modelKeys();
                    } else {
                        $availabilityMessage = 'No tables are available for the selected date, time, and guest count.';
                    }
                } catch (ValidationException $exception) {
                    $availabilityMessage = collect($exception->errors())->flatten()->first();
                }
            }

            $tableFixtures = CafeRepository::getTablesForCafe($slug);
            $tables = $activeTables->map(function (CafeTable $table, int $index) use ($tableFixtures, $availableTableIds): array {
                $isAvailable = in_array($table->id, $availableTableIds, true);
                $fixture = $tableFixtures[$index] ?? [];

                return array_merge($fixture, [
                    'id' => $table->id,
                    'name' => $table->name ?: ($table->table_number ? 'Table '.$table->table_number : 'Table '.($index + 1)),
                    'capacity' => $table->capacity,
                    'location' => ucfirst($table->location),
                    'category' => $table->location,
                    'status' => $isAvailable ? 'available' : 'unavailable',
                    'status_label' => $isAvailable ? 'Available' : 'Unavailable',
                    'is_available' => $isAvailable,
                    'type' => $fixture['type'] ?? (ucfirst($table->location).' Table'),
                    'image' => $fixture['image'] ?? 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=600&q=80',
                ]);
            })
                ->all();

            $canReserve = $activeTables->isNotEmpty();
            $availabilityUrl = route('cafes.availability', $storedCafe);
        }

        $initialSelectedTable = collect($tables)->firstWhere('is_available');

        if ($storedCafe) {
            $menuFixtures = collect(CafeRepository::getMenuForCafe($slug))->keyBy('name');
            $menu = $storedCafe->menuItems()
                ->where('status', 'active')
                ->where('is_available', true)
                ->with('category')
                ->orderBy('sort_order')
                ->get()
                ->map(function (MenuItem $item) use ($menuFixtures, $cafe): array {
                    $fixture = $menuFixtures->get($item->name, []);

                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'category' => $item->category?->name ?? ($fixture['category'] ?? 'General'),
                        'price' => (float) $item->price,
                        'formatted_price' => 'LKR '.number_format((float) $item->price, 2),
                        'description' => $item->description,
                        'image' => $item->image_path ?: ($fixture['image'] ?? ($cafe['image'] ?? 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=500&q=80')),
                    ];
                })
                ->all();
        } else {
            $menu = CafeRepository::getMenuForCafe($slug);
        }

        return view('customer.cafe-details', compact(
            'cafe',
            'tables',
            'menu',
            'allCafes',
            'canReserve',
            'selectedDate',
            'selectedTimeLabel',
            'selectedTime',
            'selectedGuestsLabel',
            'availableTableIds',
            'availableTimeSlots',
            'initialSelectedTable',
            'availabilityMessage',
            'availabilityUrl',
        ));
    }

    // Render reservation checkout screen
    public function checkout(Request $request)
    {
        $reservation = null;

        if ($request->filled('reservation')) {
            $reservation = $request->user()
                ->reservations()
                ->with(['cafe', 'cafeTable', 'payments'])
                ->findOrFail($request->integer('reservation'));

            $storedCafe = $reservation->cafe;
            $table = $reservation->cafeTable;
            $date = $reservation->reservation_date->toDateString();
            $time = $reservation->start_time;
            $guestCount = (int) $reservation->guest_count;
        } else {
            $validated = $request->validate([
                'cafe' => ['required', 'string'],
                'table' => ['required', 'integer'],
                'date' => ['required', 'date_format:Y-m-d'],
                'time' => ['required', 'string', 'max:20'],
                'guests' => ['required', 'regex:/^\d+\s+Guests?$/i'],
            ]);

            $storedCafe = Cafe::query()
                ->where('slug', $validated['cafe'])
                ->where('status', 'active')
                ->firstOrFail();
            $table = $storedCafe->tables()
                ->where('status', 'active')
                ->findOrFail((int) $validated['table']);
            $date = $validated['date'];
            $time = Carbon::parse($validated['time'])->format('H:i');
            $guestCount = (int) preg_replace('/\D+/', '', $validated['guests']);
        }

        $cafeFixture = CafeRepository::findBySlug($storedCafe->slug) ?? [];
        $cafe = array_merge($cafeFixture, [
            'id' => $storedCafe->id,
            'slug' => $storedCafe->slug,
            'name' => $storedCafe->name,
            'location' => $storedCafe->city,
            'address' => $storedCafe->address,
            'reservation_fee' => (string) ($reservation?->reservation_fee ?? $storedCafe->reservation_fee),
            'cancellation_penalty_percentage' => (string) ($reservation?->cancellation_penalty_percentage ?? $storedCafe->cancellation_penalty_percentage),
            'gallery' => $cafeFixture['gallery'] ?? [],
        ]);

        $tableFixture = collect(CafeRepository::getTablesForCafe($storedCafe->slug))
            ->firstWhere('id', $table->id) ?? [];
        $selectedTable = array_merge($tableFixture, [
            'id' => $table->id,
            'name' => $table->name ?: $table->table_number,
            'capacity' => $table->capacity,
            'location' => ucfirst($table->location),
            'type' => $tableFixture['type'] ?? ucfirst($table->location),
        ]);

        $reservationFee = (string) ($reservation?->reservation_fee ?? $storedCafe->reservation_fee);
        $cancellationPenaltyPercentage = (string) ($reservation?->cancellation_penalty_percentage ?? $storedCafe->cancellation_penalty_percentage);
        $cancellationPenaltyDisplay = rtrim(rtrim($cancellationPenaltyPercentage, '0'), '.');
        $date = Carbon::parse($date)->format('D, M j, Y');
        $time = Carbon::parse($time)->format('g:i A');
        $guests = $guestCount.($guestCount === 1 ? ' Guest' : ' Guests');
        $reservationCreated = $reservation !== null && (bool) session('reservation_created');
        $depositPayment = $reservation?->payments->first(fn (Payment $payment): bool => $payment->type === Payment::TYPE_DEPOSIT
        );
        $paymentStatus = $depositPayment?->status;
        $showPaymentForm = $reservation !== null
            && $paymentStatus === Payment::STATUS_PENDING
            && (bool) session('show_payment_form');
        $paymentConfirmed = $reservation !== null
            && $paymentStatus === Payment::STATUS_PAID
            && (bool) session('payment_confirmed');
        $paymentStatusLabel = match (true) {
            $paymentStatus === Payment::STATUS_PAID => 'Paid',
            $paymentStatus === Payment::STATUS_PENDING => 'Pending',
            $paymentStatus === Payment::STATUS_FAILED => 'Failed',
            DecimalMoney::toMinorUnits($reservationFee) === 0 => 'Not required',
            default => 'Not started',
        };
        $reservationTableName = $reservation?->cafeTable?->name ?: $reservation?->cafeTable?->table_number;
        $reservationReference = $reservation ? '#'.$reservation->id : null;
        $amountPaidNow = (string) ($depositPayment?->status === Payment::STATUS_PAID ? $depositPayment->amount : '0.00');
        $remainingBalance = DecimalMoney::fromMinorUnits(max(
            0,
            DecimalMoney::toMinorUnits($reservationFee) - DecimalMoney::toMinorUnits($amountPaidNow),
        ));
        $totalToPay = $reservationFee;
        $requiresDeposit = DecimalMoney::toMinorUnits($reservationFee) > 0;

        return view('customer.reservation-checkout', compact(
            'cafe',
            'selectedTable',
            'date',
            'time',
            'guests',
            'reservationFee',
            'cancellationPenaltyDisplay',
            'reservation',
            'reservationCreated',
            'showPaymentForm',
            'paymentConfirmed',
            'depositPayment',
            'paymentStatus',
            'paymentStatusLabel',
            'reservationTableName',
            'reservationReference',
            'amountPaidNow',
            'remainingBalance',
            'totalToPay',
            'requiresDeposit',
        ));
    }

    // Render customer's reservations page
    public function reservations()
    {
        $user = Auth::user();
        $dbReservations = $user ? Reservation::query()
            ->where('user_id', $user->id)
            ->with(['cafe', 'cafeTable', 'payments'])
            ->orderBy('reservation_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get() : collect();

        if ($dbReservations->isNotEmpty()) {
            $reservations = $dbReservations->map(function (Reservation $res): array {
                $cafeSlug = $res->cafe?->slug ?? 'cafe';
                $cafeFixture = CafeRepository::findBySlug($cafeSlug) ?? [];
                $cancellationPenalty = (float) ($res->cancellation_penalty_percentage ?? 0);
                $penaltyDisplay = rtrim(rtrim((string) $cancellationPenalty, '0'), '.');
                $paidPayment = $res->payments->firstWhere('status', Payment::STATUS_PAID);
                $isCancelled = $res->status === config('reservations.statuses.cancelled', 'cancelled');
                $isCompleted = $res->status === config('reservations.statuses.completed', 'completed');

                if ($isCancelled) {
                    $policyText = 'Reservation cancelled. The table slot has been released back.';
                } elseif ($isCompleted) {
                    $policyText = 'Reservation completed successfully.';
                } else {
                    $policyText = 'A 50% cancellation fee will be deducted upon cancellation. The remaining balance will be refunded.';
                }

                $tableName = $res->cafeTable?->name ?: ($res->cafeTable?->table_number ? 'Table '.$res->cafeTable->table_number : 'Table');
                $tableType = $res->cafeTable?->location ? ucfirst($res->cafeTable->location) : 'Standard';

                return [
                    'id' => (string) $res->id,
                    'reference' => '#RES-'.$res->id,
                    'cafe_slug' => $cafeSlug,
                    'cafe_name' => $res->cafe?->name ?? 'Cafe',
                    'cafe_location' => $res->cafe?->city ?? $res->cafe?->address ?? '',
                    'cafe_image' => $res->cafe?->featured_image_path ?: ($cafeFixture['image'] ?? 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=400&q=80'),
                    'date' => $res->reservation_date ? Carbon::parse($res->reservation_date)->format('D, M j, Y') : '',
                    'time' => $res->start_time ? Carbon::parse($res->start_time)->format('g:i A') : '',
                    'guests' => (int) $res->guest_count,
                    'table_name' => $tableName,
                    'table_type' => $tableType,
                    'table_location' => $tableType,
                    'status' => $res->status,
                    'status_label' => ucfirst($res->status),
                    'payment_status' => $paidPayment ? 'Paid' : ucfirst($res->payments->first()?->status ?? 'Pending'),
                    'reservation_fee' => (float) $res->reservation_fee,
                    'paid_amount' => (float) ($paidPayment?->amount ?? 0),
                    'remaining_balance' => max(0, (float) $res->reservation_fee - (float) ($paidPayment?->amount ?? 0)),
                    'cancellation_policy' => $policyText,
                    'cancellation_allowed' => in_array($res->status, ['pending', 'confirmed'], true),
                    'cancel_url' => route('reservations.cancel', $res->id),
                ];
            })->all();
        } else {
            $reservations = CafeRepository::getCustomerReservations();
        }

        $allCafes = $this->getDisplayCafes();
        $favourites = array_values(array_filter($allCafes, fn ($c) => $c['is_favourite'] ?? false));
        if (empty($favourites)) {
            $favourites = array_slice($allCafes, 0, min(2, count($allCafes)));
        }

        return view('customer.reservations', compact('reservations', 'favourites'));
    }

    // Render customer's favourites page
    public function favourites()
    {
        $allCafes = $this->getDisplayCafes();
        $favourites = array_values(array_filter($allCafes, fn ($c) => $c['is_favourite'] ?? false));
        if (empty($favourites)) {
            $favourites = array_slice($allCafes, 0, min(2, count($allCafes)));
        }

        return view('customer.favourites', compact('favourites', 'allCafes'));
    }

    /**
     * Retrieve all cafes for customer views, prioritizing active database records
     * and enriching them with fixtures or sensible defaults.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getDisplayCafes(): array
    {
        $dbCafes = Cafe::query()
            ->where('status', 'active')
            ->with(['tables' => function ($query) {
                $query->where('status', 'active');
            }, 'hours'])
            ->orderBy('id', 'desc')
            ->get();

        $displayCafes = [];
        $handledSlugs = [];

        foreach ($dbCafes as $cafe) {
            $handledSlugs[] = $cafe->slug;
            $fixture = CafeRepository::findBySlug($cafe->slug);
            $activeTablesCount = $cafe->tables->count();

            if ($fixture && ($fixture['slug'] === $cafe->slug)) {
                $displayCafes[] = array_merge($fixture, [
                    'id' => $cafe->id,
                    'slug' => $cafe->slug,
                    'name' => $cafe->name,
                    'location' => $cafe->city,
                    'address' => $cafe->address,
                    'reservation_fee' => (string) $cafe->reservation_fee,
                    'cancellation_penalty_percentage' => (string) $cafe->cancellation_penalty_percentage,
                    'image' => $cafe->image_path ?: ($fixture['image'] ?? 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=1200&q=80'),
                    'tables_left' => $activeTablesCount ?: ($fixture['tables_left'] ?? 3),
                ]);
            } else {
                // Real cafe entered by an owner
                $dayNames = [
                    1 => 'Monday',
                    2 => 'Tuesday',
                    3 => 'Wednesday',
                    4 => 'Thursday',
                    5 => 'Friday',
                    6 => 'Saturday',
                    7 => 'Sunday',
                ];

                $formattedHours = [];
                foreach ($cafe->hours as $hour) {
                    $dayName = $dayNames[$hour->day_of_week] ?? "Day {$hour->day_of_week}";
                    if ($hour->is_closed) {
                        $formattedHours[$dayName] = 'Closed';
                    } elseif ($hour->opens_at && $hour->closes_at) {
                        $opens = Carbon::parse($hour->opens_at)->format('g:i A');
                        $closes = Carbon::parse($hour->closes_at)->format('g:i A');
                        $formattedHours[$dayName] = "{$opens} - {$closes}";
                    }
                }

                $image = $cafe->image_path ?: 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=1200&q=80';

                $displayCafes[] = [
                    'id' => $cafe->id,
                    'slug' => $cafe->slug,
                    'name' => $cafe->name,
                    'tagline' => $cafe->description ?: 'Artisanal roastery & tranquil sanctuary',
                    'location' => $cafe->city,
                    'address' => $cafe->address,
                    'phone' => $cafe->phone,
                    'email' => $cafe->email,
                    'rating' => 4.9,
                    'reviews_count' => 12,
                    'verified' => true,
                    'price_level' => '$$',
                    'average_price' => 'LKR 900 - 2,200',
                    'distance' => '1.5 km',
                    'availability_status' => 'Available today',
                    'availability_badge' => 'Available',
                    'tables_left' => $activeTablesCount ?: 3,
                    'is_favourite' => false,
                    'cafe_type' => 'Specialty Coffee',
                    'tags' => ['Specialty Coffee', 'Artisanal', 'Brunch', 'Cozy'],
                    'short_description' => $cafe->description ?: "Freshly roasted single origin beans and comforting cafe fare in {$cafe->city}.",
                    'about' => $cafe->description ?: "Welcome to {$cafe->name}. Handcrafted coffee, warm hospitality, and cozy spaces in the heart of {$cafe->city}.",
                    'image' => $image,
                    'gallery' => [
                        $image,
                        'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&w=800&q=80',
                        'https://images.unsplash.com/photo-1442512595331-e89e73853f31?auto=format&fit=crop&w=800&q=80',
                    ],
                    'opening_hours' => $formattedHours ?: [
                        'Daily' => '8:00 AM - 10:00 PM',
                    ],
                    'amenities' => [
                        'High-speed Wi-Fi',
                        'Indoor Seating',
                        'Outdoor Area',
                        'Power Outlets',
                        'Takeaway & Dine-in',
                    ],
                    'cuisine' => 'Specialty Coffee & European Bistro',
                    'reservation_fee' => (float) $cafe->reservation_fee,
                    'cancellation_window_hours' => 2,
                    'cancellation_penalty_percentage' => (float) $cafe->cancellation_penalty_percentage,
                    'cancellation_policy' => "Cancellations up to 2 hours before your booking receive a full refund minus the {$cafe->cancellation_penalty_percentage}% cancellation fee.",
                ];
            }
        }

        // Also append any mock fixture cafes that are not yet in the DB (for development/demo continuity)
        foreach (CafeRepository::all() as $fixture) {
            if (! in_array($fixture['slug'], $handledSlugs, true)) {
                $displayCafes[] = $fixture;
            }
        }

        return $displayCafes;
    }
}
