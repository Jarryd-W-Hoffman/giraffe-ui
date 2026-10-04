@props([
    'items' => [],
])

{{-- Terms and details stack on small screens and sit in two columns from the sm breakpoint. --}}
<dl {{ $attributes->class('divide-y divide-zinc-200 text-sm dark:divide-zinc-800') }}>
    @foreach ($items as $term => $detail)
        <x-gui::description-list.item :term="$term">{{ $detail }}</x-gui::description-list.item>
    @endforeach

    {{ $slot }}
</dl>
