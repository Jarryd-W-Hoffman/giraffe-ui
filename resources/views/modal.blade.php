@php
    $sizes = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-lg',
        'lg' => 'max-w-2xl',
        'xl' => 'max-w-4xl',
    ];
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
        'm-auto w-[calc(100%-2rem)] rounded-gui border border-zinc-200 bg-white p-0 text-zinc-900 shadow-xl dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-100',
        'backdrop:bg-zinc-950/50 backdrop:backdrop-blur-[2px]',
        'opacity-0 scale-95 transition-all transition-discrete duration-150 open:opacity-100 open:scale-100 starting:open:opacity-0 starting:open:scale-95',
        'backdrop:opacity-0 backdrop:transition-all backdrop:transition-discrete backdrop:duration-150 open:backdrop:opacity-100 starting:open:backdrop:opacity-0',
        'motion-reduce:transition-none motion-reduce:backdrop:transition-none',
        $sizes[$size] ?? $sizes['md'],
    ]) }}
>
    <div class="flex max-h-[calc(100dvh-2rem)] flex-col">
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
                        <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" /></svg>
                    </button>
                @endif
            </div>
        @endif

        @if ($slot->isNotEmpty())
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
