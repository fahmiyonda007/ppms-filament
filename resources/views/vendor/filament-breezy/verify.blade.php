<x-filament-breezy::auth-card action="logout">
    <div class="w-full flex justify-center">
        <x-filament::brand />
    </div>

    <div class="space-y-8">
        <h2 class="font-bold tracking-tight text-center text-2xl">
            {{-- {{ __('filament-breezy::default.verification.heading') }} --}}
            Verification Required
        </h2>
        <div>
            {{-- {{ __('filament-breezy::default.verification.before_proceeding') }} --}}

            {{-- @unless($hasBeenSent)
                {{ __('filament-breezy::default.verification.not_receive') }}

                <a class="text-primary-600" href="#" wire:click="resend">
                    {{ __('filament-breezy::default.verification.request_another') }}
                </a>

            @else
                <span class="block text-success-600 font-semibold">{{ __('filament-breezy::default.verification.notification_success') }}</span>
            @endunless --}}

            Before proceeding, please check your account for a verification. If you did not receive please contact your administrator.
        </div>
    </div>

    {{ $this->form }}

    <x-filament::button type="submit" class="w-full" form="logout">
        {{ __('filament-breezy::default.verification.submit.label') }}
    </x-filament::button>
</x-filament-breezy::auth-card>
