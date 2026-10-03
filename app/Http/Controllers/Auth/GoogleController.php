<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    // Redirect the user to Google's OAuth authentication page.
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    // Handle Google's OAuth callback.
    // Authenticates the user and creates or updates their account.
    public function callback()
    {
        // Retrieve the authenticated Google user's information.
        $googleUser = Socialite::driver('google')->user();

        // Find an existing user by email.
        $user = User::where('email', $googleUser->getEmail())->first();

        // Create a new user if the Google account does not exist.
        if (!$user) {
            $user = User::create([
                'name' => $googleUser->getName() ?? 'Google User',
                'email' => $googleUser->getEmail(),
                'email_verified_at' => now(),

                // Generate a secure random password for Google-only accounts.
                'password' => Hash::make(Str::random(40)),
            ]);
        } else {
            // Update the user's name and verification status.
            // The existing password is intentionally preserved.
            $user->update([
                'name' => $googleUser->getName() ?? $user->name,
                'email_verified_at' => now(),
            ]);
        }

        // Log the user into the Laravel application.
        Auth::login($user, true);

        // Redirect the authenticated user to the dashboard.
        return redirect()->intended('/dashboard');
    }
}