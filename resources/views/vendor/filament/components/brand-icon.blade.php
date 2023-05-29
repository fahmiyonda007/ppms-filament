@if (filled($brand = config('filament.brand')))
    {{-- <div @class([
        'filament-brand text-xl font-bold tracking-tight',
        'dark:text-white' => config('filament.dark_mode'),
    ])>
        {{
            \Illuminate\Support\Str::of($brand)
                ->snake()
                ->upper()
                ->explode('_')
                ->map(fn (string $string) => \Illuminate\Support\Str::substr($string, 0, 1))
                ->take(2)
                ->implode('')
        }}
    </div> --}}
    <div>
        <img src="{{ asset('/logo.png') }}" alt="Icon" class="h-full w-full object-contain dark:hidden" />
        <img src="{{ asset('/logo-dark.png') }}" alt="Icon"
            class="hidden h-full w-full object-contain dark:block" />
    </div>
@endif
