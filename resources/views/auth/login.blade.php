{{-- CafeFlow Customer Authentication - Login --}}
<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        {{-- Header Heading --}}
        <div class="text-center mb-6">
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-coffee-950 tracking-tight">Welcome Back</h1>
            <p class="text-xs sm:text-sm text-coffee-500 mt-1">Sign in to your personal cafe reservation space</p>
        </div>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-xs text-sage-700 bg-sage-50 border border-sage-200/80 p-3 rounded-xl">
                {{ $value }}
            </div>
        @endsession

        {{-- Prominent "Continue with Google" OAuth Button --}}
        <div class="space-y-4">
            <a href="{{ route('google.redirect') }}"
               class="w-full flex items-center justify-center gap-3 px-4 py-3 rounded-2xl bg-white hover:bg-cream-50/80 text-coffee-800 font-semibold text-sm border border-cream-300 hover:border-coffee-400 shadow-xs hover:shadow-subtle transition-all duration-200 active:scale-[0.99] group">
                <x-google-icon class="w-5 h-5 transition-transform duration-200 group-hover:scale-105" />
                <span class="tracking-tight">Continue with Google</span>
            </a>

            {{-- Subtle "OR" Divider --}}
            <div class="relative my-6 flex items-center justify-center">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-cream-200"></div>
                </div>
                <div class="relative bg-white px-3 text-[11px] uppercase tracking-wider font-semibold text-coffee-400">
                    or continue with email
                </div>
            </div>
        </div>

        {{-- Standard Email & Password Form --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Email Address') }}" class="text-xs font-semibold text-coffee-700" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@example.com" />
            </div>

            <div>
                <x-label for="password" value="{{ __('Password') }}" class="text-xs font-semibold text-coffee-700" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            </div>

            <div class="flex items-center justify-between text-xs">
                <label for="remember_me" class="flex items-center cursor-pointer">
                    <x-checkbox id="remember_me" name="remember" class="rounded text-accent-500 focus:ring-accent-400 border-cream-300" />
                    <span class="ms-2 text-coffee-600 font-medium">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-xs font-medium text-coffee-500 hover:text-accent-600 transition-colors" href="{{ route('password.request') }}">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>

            <div class="pt-2">
                <button type="submit" class="btn-primary w-full py-3 text-sm font-semibold tracking-tight shadow-card transition-all">
                    {{ __('Sign In') }}
                </button>
            </div>
        </form>

        {{-- Sign up link footer --}}
        <div class="mt-6 pt-5 border-t border-cream-100 text-center text-xs text-coffee-600">
            <span>Don't have an account yet?</span>
            <a href="{{ route('register') }}" class="font-semibold text-accent-600 hover:text-accent-700 ml-1">
                Create an account
            </a>
        </div>
    </x-authentication-card>
</x-guest-layout>
