@aware(['guiTable' => null])

@props([
    'sortable' => null,
])

@php
    $sort = $guiTable['sort'] ?? null;
    $direction = $guiTable['direction'] ?? 'asc';
    $sorted = $sortable !== null && $sortable === $sort;

    // Behaviour attributes (wire:click, x-on:click, …) go on the sort button; everything else on the <th>.
    $action = $attributes->filter(fn ($value, $key) => str_starts_with($key, 'wire:') || str_starts_with($key, 'x-on:') || str_starts_with($key, '@'));
    $cell = $attributes->except(array_keys($action->getAttributes()));

    $next = $sorted && $direction === 'asc' ? 'desc' : 'asc';
@endphp

<th
    scope="col"
    @if ($sorted) aria-sort="{{ $direction === 'asc' ? 'ascending' : 'descending' }}" @endif
    {{ $cell->class('px-4 py-3 text-left text-xs font-medium tracking-wider whitespace-nowrap text-zinc-600 uppercase dark:text-zinc-400') }}
>
    @if ($sortable !== null)
        @php
            $classes = 'group/sort -mx-1 inline-flex cursor-pointer items-center gap-1 rounded-gui px-1 uppercase hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-gui-primary-text dark:hover:text-zinc-100';
            $icon = $sorted ? ($direction === 'asc' ? 'arrow-up' : 'arrow-down') : 'sort';
        @endphp

        @if ($action->isNotEmpty())
            <button type="button" {{ $action->class($classes) }}>
                {{ $slot }}
                <x-gui::icon :name="$icon" size="xs" @class(['opacity-40 group-hover/sort:opacity-100' => ! $sorted]) />
            </button>
        @else
            <a href="{{ request()->fullUrlWithQuery(['sort' => $sortable, 'direction' => $next, 'page' => null]) }}" class="{{ $classes }}">
                {{ $slot }}
                <x-gui::icon :name="$icon" size="xs" @class(['opacity-40 group-hover/sort:opacity-100' => ! $sorted]) />
            </a>
        @endif
    @else
        {{ $slot }}
    @endif
</th>
