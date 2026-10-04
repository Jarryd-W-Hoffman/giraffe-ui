@props([
    'orientation' => 'horizontal',
    'label' => null,
])

@if ($orientation === 'vertical')
    <div role="separator" aria-orientation="vertical" {{ $attributes->class('w-px shrink-0 self-stretch bg-zinc-200 dark:bg-zinc-800') }}></div>
@elseif ($label)
    <div {{ $attributes->class('flex items-center gap-3 text-sm text-zinc-600 dark:text-zinc-400') }}>
        <span class="h-px flex-1 bg-zinc-200 dark:bg-zinc-800" aria-hidden="true"></span>
        {{ $label }}
        <span class="h-px flex-1 bg-zinc-200 dark:bg-zinc-800" aria-hidden="true"></span>
    </div>
@else
    <hr {{ $attributes->class('border-0 border-t border-zinc-200 dark:border-zinc-800') }}>
@endif
