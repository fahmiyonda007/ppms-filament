<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-action="$getHintAction()"
    :hint-color="$getHintColor()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div x-data="{ state: $wire.entangle('{{ $getStatePath() }}').defer }">
        <!-- Interact with the `state` property in Alpine.js -->
        <iframe class="" frameborder="0" src="{{ $getStatePath() }}" style="width: 100%; height: 100vh;"></iframe>
        {{-- <iframe class="" frameborder="0" src="{{ env('APP_URL') . '/profitloss/pdf/1/2023-04-29/2023-05-30' }}" style="width: 100%; height: 100vh;"></iframe> --}}

    </div>
</x-dynamic-component>
