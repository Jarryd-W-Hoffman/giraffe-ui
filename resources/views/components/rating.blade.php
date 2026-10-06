@aware(['guiField' => null])

@props([
    'max' => 5,
    'value' => null,
    'size' => 'md',
    'readonly' => false,
    'invalid' => null,
])

@php
    $max = max(1, (int) $max);

    // Every class is written out in full so Tailwind can find it when scanning this file.
    $sizes = ['sm' => 'size-5', 'md' => 'size-6', 'lg' => 'size-8'];
    $star = $sizes[$size] ?? $sizes['md'];
    $path = \JayAitch\GiraffeUI\Support\Icons::path('star');

    if (! $readonly) {
        $control = \JayAitch\GiraffeUI\Support\Form::control($guiField, $attributes, $errors ?? null, $invalid);
        $name = $control['name'] ?? \JayAitch\GiraffeUI\Support\Id::make('gui-rating');
        $id = $control['attributes']['id'] ?? \JayAitch\GiraffeUI\Support\Id::make('gui-rating');
        $selected = $value ?? ($control['name'] && $attributes->whereStartsWith('wire:model')->isEmpty() ? old($control['errorKey']) : null);

        // Binding and state go on every radio; the rest (class, aria-*) goes on the group.
        $perRadio = fn ($v, $key) => str_starts_with($key, 'wire:model') || str_starts_with($key, 'x-model') || in_array($key, ['disabled', 'required', 'form'], true);
        $radioAttributes = $attributes->filter($perRadio)->merge(isset($control['attributes']['required']) ? ['required' => true] : []);
        $groupAttributes = $attributes->filter(fn ($v, $key) => ! $perRadio($v, $key) && ! in_array($key, ['name', 'id', 'aria-describedby'], true));
    }
@endphp

@if ($readonly)
    {{-- A rating to show, not pick: partial stars for fractions, read out as "Rated 4.5 out of 5". --}}
    @php($rated = max(0, min($max, (float) $value)))
    <span role="img" aria-label="{{ __('giraffeui::ui.rated', ['value' => rtrim(rtrim(number_format($rated, 1), '0'), '.'), 'max' => $max]) }}" {{ $attributes->class('inline-flex items-center') }}>
        @for ($i = 1; $i <= $max; $i++)
            @php($fill = max(0, min(1, $rated - ($i - 1))))
            <span class="relative inline-flex">
                <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="{{ $star }} text-zinc-400 dark:text-zinc-600"><path fill-rule="evenodd" clip-rule="evenodd" d="{{ $path }}" /></svg>
                @if ($fill > 0)
                    <span class="absolute inset-y-0 start-0 overflow-hidden" style="width: {{ round($fill * 100) }}%">
                        <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="{{ $star }} max-w-none text-gui-warning-text"><path fill-rule="evenodd" clip-rule="evenodd" d="{{ $path }}" /></svg>
                    </span>
                @endif
            </span>
        @endfor
    </span>
@else
    {{--
        A native radio group: one tab stop, arrow keys to change it, required and form submission all come from the
        browser, and each star is read out as "3 stars". The stars fill with CSS (see .gui-rating in giraffeui.css).
    --}}
    <div
        role="radiogroup"
        id="{{ $id }}"
        @if ($guiField && ($guiField['label'] ?? false)) aria-labelledby="{{ $id }}-label" @endif
        @if ($control['attributes']['aria-describedby'] ?? null) aria-describedby="{{ $control['attributes']['aria-describedby'] }}" @endif
        @if ($control['invalid']) aria-invalid="true" @endif
        {{ $groupAttributes->class('gui-rating group/rating inline-flex items-center') }}
    >
        @for ($i = 1; $i <= $max; $i++)
            <label class="gui-rating-star relative cursor-pointer rounded-sm transition-colors has-focus-visible:outline-2 has-focus-visible:outline-offset-1 has-focus-visible:outline-gui-primary-text has-disabled:cursor-not-allowed has-disabled:opacity-50">
                <input
                    type="radio"
                    name="{{ $name }}"
                    value="{{ $i }}"
                    aria-label="{{ __($i === 1 ? 'giraffeui::ui.star' : 'giraffeui::ui.stars', ['count' => $i]) }}"
                    @checked((string) $selected === (string) $i)
                    {{ $radioAttributes->class('sr-only') }}
                >
                <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="{{ $star }}"><path fill-rule="evenodd" clip-rule="evenodd" d="{{ $path }}" /></svg>
            </label>
        @endfor
    </div>
@endif
