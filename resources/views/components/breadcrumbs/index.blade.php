@props([
    'items' => null,
])

{{-- WAI-ARIA breadcrumb pattern. Pass items (label => url, with null for the current page) or compose breadcrumbs.item. --}}
<nav {{ $attributes->merge(['aria-label' => __('giraffeui::ui.breadcrumb')]) }}>
    <ol class="flex flex-wrap items-center gap-1.5 text-sm text-zinc-600 dark:text-zinc-400">
        @if (is_iterable($items))
            @foreach ($items as $label => $url)
                <x-gui::breadcrumbs.item :href="$url">{{ $label }}</x-gui::breadcrumbs.item>
            @endforeach
        @else
            {{ $slot }}
        @endif
    </ol>
</nav>
