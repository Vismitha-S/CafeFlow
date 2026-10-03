<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;

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
    Route::get('/dashboard', function () {
        return view('dashboards.customer');
    })->name('dashboard');

    // Role-specific dashboard routes
    Route::get('/admin/dashboard', function () {
        return view('dashboards.admin');
    })->name('admin.dashboard');

    Route::get('/owner/dashboard', function () {
        return view('dashboards.owner');
    })->name('owner.dashboard');

    Route::get('/customer/dashboard', function () {
        return view('dashboards.customer');
    })->name('customer.dashboard');
});

// Google OAuth authentication routes.
Route::get('/auth/google', [GoogleController::class, 'redirect'])
    ->name('google.redirect');

Route::get('/auth/google/callback', [GoogleController::class, 'callback'])
    ->name('google.callback');
