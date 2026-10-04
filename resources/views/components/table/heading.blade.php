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
            $icon = $sorted ? ($direction === 'asc' ? 'M10 17a.75.75 0 0 1-.75-.75V5.612L5.29 9.77a.75.75 0 0 1-1.08-1.04l5.25-5.5a.75.75 0 0 1 1.08 0l5.25 5.5a.75.75 0 1 1-1.08 1.04l-3.96-4.158V16.25A.75.75 0 0 1 10 17Z' : 'M10 3a.75.75 0 0 1 .75.75v10.638l3.96-4.158a.75.75 0 1 1 1.08 1.04l-5.25 5.5a.75.75 0 0 1-1.08 0l-5.25-5.5a.75.75 0 1 1 1.08-1.04l3.96 4.158V3.75A.75.75 0 0 1 10 3Z') : 'M10 3a.75.75 0 0 1 .55.24l3.25 3.5a.75.75 0 1 1-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 0 1-1.1-1.02l3.25-3.5A.75.75 0 0 1 10 3Zm-3.76 9.2a.75.75 0 0 1 1.06.04l2.7 2.908 2.7-2.908a.75.75 0 1 1 1.1 1.02l-3.25 3.5a.75.75 0 0 1-1.1 0l-3.25-3.5a.75.75 0 0 1 .04-1.06Z';
        @endphp

        @if ($action->isNotEmpty())
            <button type="button" {{ $action->class($classes) }}>
                {{ $slot }}
                <svg @class(['size-3.5 shrink-0', 'opacity-40 group-hover/sort:opacity-100' => ! $sorted]) viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" clip-rule="evenodd" d="{{ $icon }}" /></svg>
            </button>
        @else
            <a href="{{ request()->fullUrlWithQuery(['sort' => $sortable, 'direction' => $next, 'page' => null]) }}" class="{{ $classes }}">
                {{ $slot }}
                <svg @class(['size-3.5 shrink-0', 'opacity-40 group-hover/sort:opacity-100' => ! $sorted]) viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" clip-rule="evenodd" d="{{ $icon }}" /></svg>
            </a>
        @endif
    @else
        {{ $slot }}
    @endif
</th>
