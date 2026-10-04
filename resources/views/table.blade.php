<div {{ $attributes->class('overflow-hidden rounded-gui border border-zinc-200 dark:border-zinc-800') }}>
    {{--
        Wide tables scroll sideways. While they overflow, the scroller joins the tab order so keyboard users can
        scroll it too (Chrome and Firefox do this themselves; Safari doesn't).
    --}}
    <div
        class="overflow-x-auto focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-gui-primary-text"
        @if ($label) role="region" aria-label="{{ $label }}" @endif
        x-data
        x-init="new ResizeObserver(() => $el.scrollWidth > $el.clientWidth ? $el.setAttribute('tabindex', '0') : $el.removeAttribute('tabindex')).observe($el.firstElementChild)"
    >
        <table class="min-w-full divide-y divide-zinc-200 text-sm text-zinc-700 dark:divide-zinc-800 dark:text-zinc-300">
            <thead class="bg-zinc-50 dark:bg-zinc-900">
                <tr>
                    @if ($headings)
                        @foreach ($headings as $key => $label)
                            <x-gui::table.heading :sortable="in_array($key, $sortable, true) ? $key : null">{{ $label }}</x-gui::table.heading>
                        @endforeach
                    @else
                        {{ $head ?? '' }}
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-800 dark:bg-zinc-950">
                @if ($headings)
                    @forelse ($rows as $row)
                        <tr>
                            @foreach ($headings as $key => $label)
                                <x-gui::table.cell>{{ data_get($row, $key) }}</x-gui::table.cell>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ max($headings->count(), 1) }}" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">{{ $empty ?? 'No results.' }}</td>
                        </tr>
                    @endforelse
                @elseif (\JayAitch\GiraffeUI\Support\Slot::isBlank($slot))
                    <tr>
                        {{-- Browsers clamp an oversized colspan to the table's real width. --}}
                        <td colspan="100" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">{{ $empty ?? 'No results.' }}</td>
                    </tr>
                @else
                    {{ $slot }}
                @endif
            </tbody>
        </table>
    </div>

    @isset($footer)
        <div class="border-t border-zinc-200 bg-white px-4 py-3 dark:border-zinc-800 dark:bg-zinc-950">{{ $footer }}</div>
    @elseif ($rows instanceof \Illuminate\Contracts\Pagination\Paginator && $rows->hasPages())
        <div class="border-t border-zinc-200 bg-white px-4 py-3 dark:border-zinc-800 dark:bg-zinc-950">
            <x-gui::pagination :paginator="$rows" />
        </div>
    @endisset
</div>
