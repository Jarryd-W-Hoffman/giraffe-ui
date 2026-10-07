@props([
    'name',
    'options' => [],
    'value' => null,
    'label' => null,
    'size' => 'md',
])

@php
    // Binding and state go on every radio; class and the like on the group.
    $perInput = fn ($v, $key) => str_starts_with($key, 'wire:model') || str_starts_with($key, 'x-model') || in_array($key, ['disabled', 'required', 'form'], true);
    $inputAttributes = $attributes->filter($perInput);
    $groupAttributes = $attributes->filter(fn ($v, $key) => ! $perInput($v, $key));
    $sizes = ['sm' => 'size-7', 'md' => 'size-9', 'lg' => 'size-11'];
@endphp

{{--
    Pick a colour from a set of swatches: native radios, so one tab stop, arrow keys and forms all work. Each swatch is
    named for screen readers. options are value => ['label' => …, 'color' => any CSS colour], or value => colour.
--}}
<fieldset {{ $groupAttributes }}>
    @if ($label)
        <legend class="text-sm font-medium">{{ $label }}</legend>
    @endif
    <div @class(['flex flex-wrap gap-2', 'mt-3' => $label])>
        @foreach ($options as $optionValue => $option)
            @php($option = is_array($option) ? $option : ['label' => ucfirst((string) $optionValue), 'color' => $option])
            <label class="relative cursor-pointer">
                <input type="radio" name="{{ $name }}" value="{{ $optionValue }}" @checked((string) $value === (string) $optionValue) {{ $inputAttributes }} class="peer sr-only">
                <span class="sr-only">{{ $option['label'] }}</span>
                <span class="block {{ $sizes[$size] ?? $sizes['md'] }} rounded-full outline-2 outline-offset-2 outline-transparent transition peer-checked:outline-zinc-900 peer-focus-visible:outline-gui-primary-text peer-focus-visible:outline-offset-4 dark:peer-checked:outline-white" style="background: {{ $option['color'] }}" aria-hidden="true"></span>
            </label>
        @endforeach
    </div>
</fieldset>
