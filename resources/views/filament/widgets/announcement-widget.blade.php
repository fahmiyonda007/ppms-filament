{{-- @stack('modals') --}}
{{-- <link rel="stylesheet" href="{{ asset('vendor/filament-tiptap-editor/filament-tiptap-editor.css') }}"> --}}

<x-filament::widget class="col-span-full">
    @if ($data != null)
        <x-filament::card>
            <div>
                <h2 class="text-lg sm:text-xl font-bold tracking-tight text-success-400">
                    Announcement
                </h2>
                <br>
                {!! $data->announcement !!}
            </div>
        </x-filament::card>
    @endif
</x-filament::widget>

{{-- <script src="{{ asset('vendor/filament-tiptap-editor/filament-tiptap-editor.js') }}"></script> --}}
