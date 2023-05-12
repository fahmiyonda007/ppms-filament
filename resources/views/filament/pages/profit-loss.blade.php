<x-filament::page>
    <x-filament::card>
        {{ $this->form }}

        {{-- <x-filament::button wire:click="$set('src', 'Hello')" target="_blank" type="button"
            class="filament-page-button-action w-auto" color='primary' icon='heroicon-o-document-text'>
            Preview PDF
        </x-filament::button> --}}
        {{-- <div>
            <x-filament::button type="button" class="filament-page-button-action w-auto" color='success'
                icon='heroicon-o-document-report'>
                Excel
            </x-filament::button>
        </div> --}}
        @if ($frameSrc != '')
            <iframe id="framepdf" frameborder="0" src="{{ $frameSrc }}" style="width: 100%; height: 100vh;">
            </iframe>
        @endif
    </x-filament::card>
</x-filament::page>
