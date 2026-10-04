@props([
    'columns' => [],
    'rows' => [],
])

@php
    // Accept ['name', 'email'] or ['name' => 'Full name', 'email' => 'Email address'].
    $columns = collect($columns)->mapWithKeys(fn ($label, $key) => is_int($key)
        ? [$label => \Illuminate\Support\Str::headline($label)]
        : [$key => $label]
    );
@endphp

<div {{ $attributes->class('overflow-x-auto rounded-gui border border-zinc-200 dark:border-zinc-800') }}>
    <table class="min-w-full divide-y divide-zinc-200 text-sm text-zinc-700 dark:divide-zinc-800 dark:text-zinc-300">
        <thead class="bg-zinc-50 dark:bg-zinc-900">
            <tr>
                @foreach ($columns as $label)
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium tracking-wider text-zinc-500 uppercase dark:text-zinc-400">
                        {{ $label }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-zinc-200 bg-white dark:divide-zinc-800 dark:bg-zinc-950">
            @forelse ($rows as $row)
                <tr>
                    @foreach ($columns as $key => $label)
                        <td class="px-4 py-3 whitespace-nowrap">
                            {{ data_get($row, $key) }}
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ max($columns->count(), 1) }}" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                        {{ $empty ?? 'No results.' }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
