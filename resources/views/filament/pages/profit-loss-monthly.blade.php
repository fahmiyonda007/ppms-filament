<x-filament::page>
    <x-filament::card>
        {{ $this->form }}

        <x-filament::button wire:click="$set('showRpt', true)" target="_blank" type="button"
            class="filament-page-button-action w-auto" color='primary' icon='heroicon-o-document-text'>
            Preview Report
        </x-filament::button>
        {{-- <div>
            <x-filament::button type="button" class="filament-page-button-action w-auto" color='success'
                icon='heroicon-o-document-report'>
                Excel
            </x-filament::button>
        </div> --}}
        @if ($showRpt)
        @php
        $asd = env('APP_URL') . "/profitlossmonthly/pdf/0";
        @endphp
            <iframe id="framepdf" frameborder="0" src="{{ $frameSrc ?? $asd  }}" style="width: 100%; height: 100vh;">
            </iframe>
        @endif
    </x-filament::card>
</x-filament::page>
