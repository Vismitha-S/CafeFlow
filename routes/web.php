<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\CustomerController;

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
    // Default dashboard redirects to the customer dashboard
    Route::get('/dashboard', [CustomerController::class, 'home'])->name('dashboard');

    // Role-specific dashboard routes
    Route::get('/admin/dashboard', function () {
        return view('dashboards.admin');
    })->name('admin.dashboard');

    Route::get('/owner/dashboard', function () {
        return view('dashboards.owner');
    })->name('owner.dashboard');

    Route::get('/customer/dashboard', [CustomerController::class, 'home'])->name('customer.dashboard');

    // Customer Discovery and Cafe Details
    Route::get('/explore', [CustomerController::class, 'explore'])->name('customer.explore');
    Route::get('/customer/explore', [CustomerController::class, 'explore']);

    Route::get('/cafes/{slug}', [CustomerController::class, 'showCafe'])->name('customer.cafe.show');
    Route::get('/customer/cafes/{slug}', [CustomerController::class, 'showCafe']);

    // Reservation Checkout Flow
    Route::get('/reservations/checkout', [CustomerController::class, 'checkout'])->name('customer.reservation.checkout');
    Route::get('/customer/reservations/checkout', [CustomerController::class, 'checkout']);

    // My Reservations
    Route::get('/reservations', [CustomerController::class, 'reservations'])->name('customer.reservations');
    Route::get('/customer/reservations', [CustomerController::class, 'reservations']);

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
