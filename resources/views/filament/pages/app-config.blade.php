<x-filament::page>
    <x-filament::card>
        <x-filament::form wire:submit.prevent="save">
            {{ $this->form }}

            <x-filament::button type="submit" class="filament-page-button-action w-auto" color='primary'
                icon='heroicon-o-document-text'>
                Save
            </x-filament::button>
        </x-filament::form>
    </x-filament::card>
</x-filament::page>
