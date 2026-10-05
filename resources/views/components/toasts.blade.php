@props([
    'duration' => 5000,
])

@php
    // A toast flashed to the session: session()->flash('toast', 'Saved') or ['message' => 'Saved', 'color' => 'success'].
    $flashed = session('toast');
    $flashed = is_string($flashed) ? ['message' => $flashed] : (is_array($flashed) ? $flashed : null);
@endphp

{{--
    Place once, e.g. in your layout. Show a toast with:
      GiraffeUI.toast('Saved', { color: 'success' })        // JavaScript
      $dispatch('toast', { message: 'Saved' })               // Alpine
      $this->dispatch('toast', message: 'Saved')             // Livewire
      session()->flash('toast', 'Saved')                     // a redirect
    The region is a popover in the top layer, so toasts also appear above open modals.
--}}
<div
    popover="manual"
    role="status"
    aria-live="polite"
    x-data="{
        toasts: [],
        add(detail) {
            const toast = { id: Date.now() + Math.random(), color: 'secondary', title: null, duration: @js($duration), ...(typeof detail === 'string' ? { message: detail } : detail) };
            this.toasts.push(toast);
            // Re-show so the region moves above anything opened since, like a modal.
            this.$el.hidePopover?.();
            this.$el.showPopover?.();
            this.start(toast);
        },
        start(toast) {
            if (toast.duration > 0) {
                toast.timer = setTimeout(() => this.remove(toast.id), toast.duration);
            }
        },
        pause(toast) {
            clearTimeout(toast.timer);
        },
        remove(id) {
            this.toasts = this.toasts.filter((toast) => toast.id !== id);
        },
    }"
    x-init="$el.showPopover?.(); @if ($flashed) add(@js($flashed)); @endif"
    x-on:toast.window="add($event.detail)"
    {{ $attributes->class('pointer-events-none fixed inset-auto right-0 bottom-0 m-0 flex w-full max-w-sm flex-col gap-2 overflow-visible border-0 bg-transparent p-4') }}
>
    <template x-for="toast in toasts" x-bind:key="toast.id">
        <div
            x-on:mouseenter="pause(toast)"
            x-on:mouseleave="start(toast)"
            x-on:focusin="pause(toast)"
            x-on:focusout="start(toast)"
            x-transition:enter="transition duration-200 ease-out motion-reduce:transition-none"
            x-transition:enter-start="translate-y-2 opacity-0"
            x-transition:leave="transition duration-150 ease-in motion-reduce:transition-none"
            x-transition:leave-end="opacity-0"
            class="pointer-events-auto flex items-start gap-3 rounded-gui border border-zinc-200 bg-white p-4 text-sm text-zinc-900 shadow-lg dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-100"
        >
            {{-- Every class is written out in full so Tailwind can find it. --}}
            <span
                class="mt-1.5 size-2 shrink-0 rounded-full"
                x-bind:class="{
                    primary: 'bg-gui-primary',
                    secondary: 'bg-gui-secondary',
                    success: 'bg-gui-success',
                    warning: 'bg-gui-warning',
                    danger: 'bg-gui-danger',
                    info: 'bg-gui-info',
                }[toast.color] ?? 'bg-gui-secondary'"
                aria-hidden="true"
            ></span>
            <div class="min-w-0 flex-1">
                <p class="font-medium" x-show="toast.title" x-text="toast.title"></p>
                <p x-bind:class="toast.title && 'text-zinc-600 dark:text-zinc-400'" x-text="toast.message"></p>
            </div>
            <button
                type="button"
                class="-m-1 shrink-0 cursor-pointer rounded-gui p-1 text-zinc-500 hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-gui-primary-text dark:text-zinc-400 dark:hover:text-zinc-100"
                x-on:click="remove(toast.id)"
                aria-label="{{ __('giraffeui::ui.dismiss') }}"
            >
                <x-gui::icon name="x-mark" size="sm" />
            </button>
        </div>
    </template>
</div>

<script @if ($nonce = \Illuminate\Support\Facades\Vite::cspNonce()) nonce="{{ $nonce }}" @endif>
    window.GiraffeUI = window.GiraffeUI || {};
    window.GiraffeUI.toast = (message, options = {}) => window.dispatchEvent(new CustomEvent('toast', { detail: { message, ...options } }));
</script>
