@props([
    'term',
])

<div {{ $attributes->class('py-3 first:pt-0 last:pb-0 sm:grid sm:grid-cols-3 sm:gap-4') }}>
    <dt class="font-medium text-zinc-600 dark:text-zinc-400">{{ $term }}</dt>
    <dd class="mt-1 text-zinc-900 sm:col-span-2 sm:mt-0 dark:text-zinc-100">{{ $slot }}</dd>
</div>
