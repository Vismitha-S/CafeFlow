{{-- Customer Profile & Account Settings --}}
<x-dashboard-layout dashboard-role="customer">
    <x-slot name="title">Profile & Settings</x-slot>

    <div class="space-y-8">
        {{-- Profile Page Header --}}
        <div class="pb-4 border-b border-cream-200">
            <h1 class="font-serif text-3xl font-bold text-coffee-950 tracking-tight">Profile & Account Settings</h1>
            <p class="text-xs sm:text-sm text-coffee-500 mt-1">Manage your account information, security credentials, and active browser sessions.</p>
        </div>

        <div class="space-y-10">
            @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-cream-200 shadow-subtle">
                    @livewire('profile.update-profile-information-form')
                </div>
            @endif

            @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-cream-200 shadow-subtle">
                    @livewire('profile.update-password-form')
                </div>
            @endif

            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-cream-200 shadow-subtle">
                    @livewire('profile.two-factor-authentication-form')
                </div>
            @endif

            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-cream-200 shadow-subtle">
                @livewire('profile.logout-other-browser-sessions-form')
            </div>

            @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-rose-200 shadow-subtle">
                    @livewire('profile.delete-user-form')
                </div>
            @endif
        </div>
    </div>
</x-dashboard-layout>
