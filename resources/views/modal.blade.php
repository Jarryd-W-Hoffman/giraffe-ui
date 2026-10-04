@php
    $drawer = isset($side);

    // Every class is written out in full so Tailwind can find it when scanning this file.
    $sizes = $drawer
        ? ['sm' => 'max-w-sm', 'md' => 'max-w-md', 'lg' => 'max-w-xl', 'xl' => 'max-w-3xl']
        : ['sm' => 'max-w-sm', 'md' => 'max-w-lg', 'lg' => 'max-w-2xl', 'xl' => 'max-w-4xl'];

    $layout = match (true) {
        ! $drawer => 'm-auto w-[calc(100%-2rem)] rounded-gui border opacity-0 scale-95 open:opacity-100 open:scale-100 starting:open:opacity-0 starting:open:scale-95',
        $side === 'left' => 'm-0 mr-auto h-dvh max-h-dvh w-[calc(100%-3rem)] border-r -translate-x-full open:translate-x-0 starting:open:-translate-x-full',
        default => 'm-0 ml-auto h-dvh max-h-dvh w-[calc(100%-3rem)] border-l translate-x-full open:translate-x-0 starting:open:translate-x-full',
    };
@endphp

{{--
    A native <dialog>: the browser handles focus trapping, Escape, the backdrop and making the page behind inert.
    Open it with <button commandfor="{id}" command="show-modal">, or document.getElementById('{id}').showModal().
    wire:ignore.self stops Livewire from closing it when the component re-renders.
--}}
<dialog
    id="{{ $id }}"
    @if ($title) aria-labelledby="{{ $id }}-title" @endif
    @if ($description) aria-describedby="{{ $id }}-description" @endif
    closedby="{{ $dismissible ? 'any' : 'none' }}"
    wire:ignore.self
    {{ $attributes->class([
        'border-zinc-200 bg-white p-0 text-zinc-900 shadow-xl dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-100',
        $layout,
        'backdrop:bg-zinc-950/50 backdrop:backdrop-blur-[2px]',
        'transition-all transition-discrete duration-150',
        'backdrop:opacity-0 backdrop:transition-all backdrop:transition-discrete backdrop:duration-150 open:backdrop:opacity-100 starting:open:backdrop:opacity-0',
        'motion-reduce:transition-none motion-reduce:backdrop:transition-none',
        $sizes[$size] ?? $sizes['md'],
    ]) }}
>
    <div @class(['flex flex-col', 'h-full' => $drawer, 'max-h-[calc(100dvh-2rem)]' => ! $drawer])>
        @if ($title || $description || $dismissible)
            <div class="flex items-start gap-4 px-6 pt-6 pb-4">
                <div class="min-w-0 flex-1 space-y-1.5">
                    @if ($title)
                        <h2 id="{{ $id }}-title" class="text-lg leading-6 font-semibold">{{ $title }}</h2>
                    @endif
                    @if ($description)
                        <p id="{{ $id }}-description" class="text-sm text-zinc-600 dark:text-zinc-400">{{ $description }}</p>
                    @endif
                </div>

                @if ($dismissible)
                    <button
                        type="button"
                        commandfor="{{ $id }}"
                        command="close"
                        aria-label="Close"
                        class="-mt-1 -mr-2 shrink-0 cursor-pointer rounded-gui p-1.5 text-zinc-500 transition-colors hover:bg-zinc-100 hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-gui-primary-text dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100"
                    >
                        <x-gui::icon name="x-mark" />
                    </button>
                @endif
            </div>
        @endif

        @if (! \JayAitch\GiraffeUI\Support\Slot::isBlank($slot))
            <div class="min-h-0 flex-1 overflow-y-auto px-6 pb-4 text-sm first:pt-6">{{ $slot }}</div>
        @endif

        @isset($footer)
            <div class="flex flex-col-reverse gap-3 border-t border-zinc-200 px-6 py-4 sm:flex-row sm:justify-end dark:border-zinc-800">{{ $footer }}</div>
        @endisset
    </div>
</dialog>

@if ($open)
    {{-- Open on page load, e.g. :open="$errors->any()" to reopen a form after validation fails. --}}
    <script @if ($nonce = \Illuminate\Support\Facades\Vite::cspNonce()) nonce="{{ $nonce }}" @endif>
        document.getElementById(@js($id))?.showModal();
    </script>
@endif
