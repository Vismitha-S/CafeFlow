<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class GoogleController extends Controller
{
    /**
     * Redirect the user to Google's OAuth authentication page.
     */
    public function redirect(Request $request)
    {
        if ($request->filled('role')) {
            session(['google_auth_role' => $request->input('role')]);
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google's OAuth callback.
     *
     * Authenticates the user and creates or updates their account.
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (InvalidStateException) {
            // The OAuth state parameter did not match — this happens when the
            // session is lost between the redirect and the callback (e.g. the
            // user opened a new tab or the session expired). Restart the flow.
            return redirect()->route('google.redirect')
                ->withErrors(['email' => 'Google sign-in failed. Please try again.']);
        }

        // Find or create the user by their Google email.
        // Use firstOrNew so we can set non-fillable attributes safely.
        $user = User::firstOrNew(['email' => $googleUser->getEmail()]);

        $isNewUser = ! $user->exists;

        $user->name = $googleUser->getName() ?? ($user->name ?: 'Google User');

        // Mark email as verified — Google has already confirmed ownership.
        // We use forceFill to bypass the $fillable guard on email_verified_at.
        $user->forceFill(['email_verified_at' => now()]);

        if ($isNewUser) {
            // Generate a secure random password so the account cannot be
            // accessed via standard password login.
            $user->password = Hash::make(Str::random(40));

            $desiredRole = session()->pull('google_auth_role');
            if (in_array($desiredRole, [User::ROLE_CUSTOMER, User::ROLE_OWNER], true)) {
                $user->role = $desiredRole;
            }
        }

        $user->save();

        // Log the user into the Laravel application with a persistent session.
        Auth::login($user, true);

        return redirect()->intended('/dashboard');
    }
}
