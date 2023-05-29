@if (filled($brand = config('filament.brand')))
    <div @class([
        'filament-brand text-xl font-bold tracking-tight',
        'dark:text-white' => config('filament.dark_mode'),
    ])>
        {{-- <img src="{{ asset('/logo-inline.png') }}" alt="Icon"
            class="relative z-20 block h-10 object-contain dark:hidden" />
        <img src="{{ asset('/logo-inline-dark.png') }}" alt="Icon"
            class="relative z-20 hidden h-10 object-contain dark:block" /> --}}
        {{ $brand }}
    </div>
@endif
