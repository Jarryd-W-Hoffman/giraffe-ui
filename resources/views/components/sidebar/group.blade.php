@props([
    'heading' => null,
])

{{-- A heading names the group without an id, so the same sidebar can render twice (column and drawer). --}}
<div @if ($heading) role="group" aria-label="{{ $heading }}" @endif {{ $attributes }}>
    @if ($heading)
        <p class="mb-1 px-2 text-xs font-semibold tracking-wide text-zinc-500 uppercase dark:text-zinc-400" aria-hidden="true">{{ $heading }}</p>
    @endif
    <ul class="space-y-0.5">
        {{ $slot }}
    </ul>
</div>
