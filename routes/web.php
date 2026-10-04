<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\CafeController;
use App\Http\Controllers\CafeTableController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MenuCategoryController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\ReservationAvailabilityController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

// Public landing page
Route::get('/', function () {
    return view('landing');
})->name('home');

// Authenticated routes protected by Sanctum and session verification
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Default dashboard redirects based on role
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isOwner()) {
            return redirect()->route('owner.dashboard');
        }

        return redirect()->route('customer.dashboard');
    })->name('dashboard');

    // Role-specific dashboard routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', function () {
            return view('dashboards.admin');
        })->name('admin.dashboard');
    });

    Route::middleware('role:owner')->group(function () {
        Route::get('/owner/dashboard', function () {
            return view('dashboards.owner');
        })->name('owner.dashboard');
    });

    Route::get('/customer/dashboard', [CustomerController::class, 'home'])->name('customer.dashboard');

    // Cafe Management Routes
    Route::get('/cafes', [CafeController::class, 'index'])->name('cafes.index');
    Route::post('/cafes', [CafeController::class, 'store'])->name('cafes.store');
    Route::get('/cafes/{cafe}', [CafeController::class, 'show'])->name('cafes.show')->where('cafe', '[0-9]+');
    Route::put('/cafes/{cafe}', [CafeController::class, 'update'])->name('cafes.update')->where('cafe', '[0-9]+');
    Route::patch('/cafes/{cafe}', [CafeController::class, 'update'])->where('cafe', '[0-9]+');
    Route::delete('/cafes/{cafe}', [CafeController::class, 'destroy'])->name('cafes.destroy')->where('cafe', '[0-9]+');

    // Cafe Table Management Routes
    Route::get('/cafes/{cafe}/tables', [CafeTableController::class, 'index'])->name('cafe-tables.index')->where('cafe', '[0-9]+');
    Route::post('/cafes/{cafe}/tables', [CafeTableController::class, 'store'])->name('cafe-tables.store')->where('cafe', '[0-9]+');
    Route::get('/cafe-tables/{cafeTable}', [CafeTableController::class, 'show'])->name('cafe-tables.show');
    Route::put('/cafe-tables/{cafeTable}', [CafeTableController::class, 'update'])->name('cafe-tables.update');
    Route::patch('/cafe-tables/{cafeTable}', [CafeTableController::class, 'update']);
    Route::delete('/cafe-tables/{cafeTable}', [CafeTableController::class, 'destroy'])->name('cafe-tables.destroy');

    // Reservation Routes
    Route::get('/cafes/{cafe}/availability', [ReservationAvailabilityController::class, 'index'])->name('cafes.availability')->where('cafe', '[0-9]+');
    Route::post('/cafes/{cafe}/reservations', [ReservationController::class, 'store'])->name('cafes.reservations.store')->where('cafe', '[0-9]+');
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::post('/reservations/{reservation}/deposit', [ReservationController::class, 'startDepositPayment'])->name('reservations.deposit')->where('reservation', '[0-9]+');
    Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel')->where('reservation', '[0-9]+');
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show')->where('reservation', '[0-9]+');

    // Menu Management Routes
    Route::get('/cafes/{cafe}/menu/categories', [MenuCategoryController::class, 'index'])->name('menu-categories.index')->where('cafe', '[0-9]+');
    Route::post('/cafes/{cafe}/menu/categories', [MenuCategoryController::class, 'store'])->name('menu-categories.store')->where('cafe', '[0-9]+');
    Route::get('/menu-categories/{menuCategory}', [MenuCategoryController::class, 'show'])->name('menu-categories.show');
    Route::put('/menu-categories/{menuCategory}', [MenuCategoryController::class, 'update'])->name('menu-categories.update');
    Route::patch('/menu-categories/{menuCategory}', [MenuCategoryController::class, 'update']);
    Route::delete('/menu-categories/{menuCategory}', [MenuCategoryController::class, 'destroy'])->name('menu-categories.destroy');

    Route::get('/cafes/{cafe}/menu/items', [MenuItemController::class, 'index'])->name('menu-items.index')->where('cafe', '[0-9]+');
    Route::post('/cafes/{cafe}/menu/items', [MenuItemController::class, 'store'])->name('menu-items.store')->where('cafe', '[0-9]+');
    Route::get('/menu-items/{menuItem}', [MenuItemController::class, 'show'])->name('menu-items.show');
    Route::put('/menu-items/{menuItem}', [MenuItemController::class, 'update'])->name('menu-items.update');
    Route::patch('/menu-items/{menuItem}', [MenuItemController::class, 'update']);
    Route::delete('/menu-items/{menuItem}', [MenuItemController::class, 'destroy'])->name('menu-items.destroy');

    // Customer Discovery and Cafe Details
    Route::get('/explore', [CustomerController::class, 'explore'])->name('customer.explore');
    Route::get('/customer/explore', [CustomerController::class, 'explore']);

    Route::get('/cafes/{slug}', [CustomerController::class, 'showCafe'])->name('customer.cafe.show');
    Route::get('/customer/cafes/{slug}', [CustomerController::class, 'showCafe']);

    // Reservation Checkout Flow
    Route::get('/reservations/checkout', [CustomerController::class, 'checkout'])->name('customer.reservation.checkout');
    Route::get('/customer/reservations/checkout', [CustomerController::class, 'checkout']);

    // My Reservations
    Route::get('/customer/reservations', [CustomerController::class, 'reservations'])->name('customer.reservations');

    // Favourites
    Route::get('/favourites', [CustomerController::class, 'favourites'])->name('customer.favourites');
    Route::get('/customer/favourites', [CustomerController::class, 'favourites']);

    // Settings redirect
    Route::get('/settings', function () {
        return redirect()->route('profile.show');
    })->name('customer.settings');
});

// Google OAuth authentication routes
Route::get('/auth/google', [GoogleController::class, 'redirect'])
    ->name('google.redirect');

Route::get('/auth/google/callback', [GoogleController::class, 'callback'])
    ->name('google.callback');
