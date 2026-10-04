@props([
    'steps' => null,
    'current' => null,
    'complete' => false,
    'invalid' => [],
    'orientation' => 'horizontal',
])

@php
    // Steps are key => label, or key => ['label' => …, 'description' => …, 'href' => …].
    // Steps before the current one are complete and can link back with href; complete marks every step done.
    // Keys compare as strings, so current="2" and :current="2" both work.
    $steps = is_iterable($steps) ? collect($steps)->all() : [];
    $keys = array_map('strval', array_keys($steps));
    $currentIndex = $complete ? count($keys) : array_search((string) ($current ?? ($keys[0] ?? '')), $keys, true);
    $invalid = array_map('strval', (array) $invalid);
    $vertical = $orientation === 'vertical';
@endphp

{{-- A list of steps showing progress through a multi-step flow. Steps read their layout from data-orientation. --}}
<nav {{ $attributes->merge(['aria-label' => 'Progress'])->class('w-full') }}>
    <ol data-orientation="{{ $vertical ? 'vertical' : 'horizontal' }}" @class([
        'group/stepper [counter-reset:gui-step]',
        'flex flex-col' => $vertical,
        'flex items-start' => ! $vertical,
    ])>
        @if ($keys)
            @foreach ($steps as $key => $step)
                @php
                    $step = is_array($step) ? $step : ['label' => $step];
                    $index = $loop->index;
                    $past = $currentIndex !== false && $index < $currentIndex;
                    $status = match (true) {
                        in_array((string) $key, $invalid, true) => 'error',
                        $past => 'complete',
                        $index === $currentIndex => 'current',
                        default => 'upcoming',
                    };
                @endphp
                <x-gui::stepper.step :status="$status" :description="$step['description'] ?? null" :current="$index === $currentIndex" :href="$past ? ($step['href'] ?? null) : null">{{ $step['label'] }}</x-gui::stepper.step>
            @endforeach
        @else
            {{ $slot }}
        @endif
    </ol>
</nav>
