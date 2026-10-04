@props([
    'required' => false,
])

<label {{ $attributes->class('block text-sm font-medium text-zinc-900 dark:text-zinc-100') }}>
    {{ $slot }}@if ($required)<span class="text-gui-danger-text" aria-hidden="true"> *</span>@endif
</label>
