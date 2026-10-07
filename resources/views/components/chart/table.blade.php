@props([
    'label' => '',
    'labels' => [],
    'columns' => [],
    'visible' => false,
])

{{--
    The chart's numbers as a table: its text equivalent, exact rather than abbreviated. visible puts it behind a
    "Show the data" disclosure (no JavaScript to open); otherwise it's there for screen readers only.
--}}
@if ($visible)
<details {{ $attributes->class('mt-3') }}>
    <summary class="w-fit cursor-pointer rounded-sm font-medium text-zinc-700 hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gui-primary-text dark:text-zinc-300 dark:hover:text-zinc-100">{{ __('giraffeui::ui.show_data') }}</summary>
@else
<div class="sr-only">
@endif
    <div @class(['mt-2 overflow-x-auto' => $visible])>
        <table class="w-full text-left text-xs">
            <caption class="sr-only">{{ $label }}</caption>
            <thead class="text-zinc-600 dark:text-zinc-400">
                <tr class="border-b border-zinc-200 dark:border-zinc-800">
                    <th scope="col" class="py-1.5 pr-4 font-medium"><span class="sr-only">{{ __('giraffeui::ui.point') }}</span></th>
                    @foreach ($columns as $column)
                        <th scope="col" class="py-1.5 pr-4 text-right font-medium">{{ $column['name'] ?: $label }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="text-zinc-900 tabular-nums dark:text-zinc-100">
                @foreach ($labels as $index => $text)
                    <tr class="border-b border-zinc-100 last:border-0 dark:border-zinc-800/60">
                        <th scope="row" class="py-1.5 pr-4 font-normal text-zinc-600 dark:text-zinc-400">{{ $text }}</th>
                        @foreach ($columns as $column)
                            <td class="py-1.5 pr-4 text-right">{{ $column['values'][$index] ?? '–' }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@if ($visible)
</details>
@else
</div>
@endif
