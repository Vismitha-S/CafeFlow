{{-- CafeFlow Customer Authentication - Register --}}
<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        {{-- Header Heading --}}
        <div class="text-center mb-6">
            <h1 class="font-serif text-2xl sm:text-3xl font-bold text-coffee-950 tracking-tight">Create Account</h1>
            <p class="text-xs sm:text-sm text-coffee-500 mt-1">Join CafeFlow and explore cozy artisanal cafes</p>
        </div>

        <x-validation-errors class="mb-4" />

        <div x-data="{ role: '{{ old('role', request('role', 'customer')) }}' }">
            {{-- Prominent "Continue with Google" OAuth Button --}}
            <div class="space-y-4">
                <a x-bind:href="`{{ route('google.redirect') }}?role=${role}`"
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
                        or register with email
                    </div>
                </div>
            </div>

            {{-- Standard Registration Form --}}
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                {{-- Hidden radio inputs ensuring selected role is always sent reliably --}}
                <input type="radio" name="role" value="customer" class="hidden" x-model="role">
                <input type="radio" name="role" value="owner" class="hidden" x-model="role">

                {{-- Role Selector Cards --}}
                <div>
                    <x-label value="{{ __('Register As') }}" class="text-xs font-semibold text-coffee-700 mb-2" />
                    <div class="grid grid-cols-2 gap-2.5">
                        <button type="button"
                                @click="role = 'customer'"
                                :class="role === 'customer' ? 'border-accent-500 bg-accent-50/50 ring-2 ring-accent-400/30' : 'border-cream-200 bg-white hover:border-cream-300'"
                                class="relative flex flex-col items-center gap-1.5 p-3.5 rounded-2xl border-2 cursor-pointer transition-all duration-200 text-left">
                            <div :class="role === 'customer' ? 'bg-accent-500 text-white' : 'bg-cream-100 text-coffee-500'"
                                 class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <span class="text-xs font-bold text-coffee-900">Explore & Book</span>
                            <span class="text-[10px] text-coffee-500 leading-tight text-center">Discover cafes & reserve tables</span>
                        </button>

                        <button type="button"
                                @click="role = 'owner'"
                                :class="role === 'owner' ? 'border-accent-500 bg-accent-50/50 ring-2 ring-accent-400/30' : 'border-cream-200 bg-white hover:border-cream-300'"
                                class="relative flex flex-col items-center gap-1.5 p-3.5 rounded-2xl border-2 cursor-pointer transition-all duration-200 text-left">
                            <div :class="role === 'owner' ? 'bg-accent-500 text-white' : 'bg-cream-100 text-coffee-500'"
                                 class="w-9 h-9 rounded-xl flex items-center justify-center transition-colors">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <span class="text-xs font-bold text-coffee-900">Manage My Cafe</span>
                            <span class="text-[10px] text-coffee-500 leading-tight text-center">List your cafe & accept bookings</span>
                        </button>
                    </div>
                </div>

            <div>
                <x-label for="name" value="{{ __('Full Name') }}" class="text-xs font-semibold text-coffee-700" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Vismitha Susikumar" />
            </div>

            <div>
                <x-label for="email" value="{{ __('Email Address') }}" class="text-xs font-semibold text-coffee-700" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="you@example.com" />
            </div>

            <div>
                <x-label for="password" value="{{ __('Password') }}" class="text-xs font-semibold text-coffee-700" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
            </div>

            <div>
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" class="text-xs font-semibold text-coffee-700" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div>
                    <label for="terms" class="flex items-start text-xs text-coffee-600 cursor-pointer">
                        <x-checkbox name="terms" id="terms" required class="rounded text-accent-500 focus:ring-accent-400 border-cream-300 mt-0.5" />
                        <span class="ms-2">
                            {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                    'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-coffee-800 hover:text-accent-600 font-medium">'.__('Terms of Service').'</a>',
                                    'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-coffee-800 hover:text-accent-600 font-medium">'.__('Privacy Policy').'</a>',
                            ]) !!}
                        </span>
                    </label>
                </div>
            @endif

            <div class="pt-2">
                <button type="submit" class="btn-primary w-full py-3 text-sm font-semibold tracking-tight shadow-card transition-all">
                    {{ __('Create Account') }}
                </button>
            </div>
        </form>
    </div>

        {{-- Already registered footer --}}
        <div class="mt-6 pt-5 border-t border-cream-100 text-center text-xs text-coffee-600">
            <span>Already have an account?</span>
            <a href="{{ route('login') }}" class="font-semibold text-accent-600 hover:text-accent-700 ml-1">
                Sign in
            </a>
        </div>
    </x-authentication-card>
</x-guest-layout>
