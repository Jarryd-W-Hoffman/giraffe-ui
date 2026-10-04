@aware(['guiField' => null])

@props([
    'hint' => null,
    'invalid' => null,
])

@php
    $control = \JayAitch\GiraffeUI\Support\Form::control($guiField, $attributes, $errors ?? null, $invalid);
    $id = $control['attributes']['id'] ?? 'gui-file-'.\Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(8));
    $multiple = $attributes->has('multiple') && $attributes->get('multiple') !== false;
@endphp

{{--
    A real <input type="file"> stretched invisibly over the drop zone: clicking anywhere opens the picker, dropping
    files is native, and the field's own label names it. Alpine adds the list of chosen files and Livewire upload progress.
--}}
<div
    x-data="{
        files: [],
        dragging: false,
        progress: null,
        failed: false,

        read(input) {
            this.files = [...input.files].map((file) => ({ name: file.name, size: file.size }));
        },
        size(bytes) {
            return bytes < 1024 ? bytes + ' B' : bytes < 1048576 ? (bytes / 1024).toFixed(0) + ' KB' : (bytes / 1048576).toFixed(1) + ' MB';
        },
    }"
    x-on:livewire-upload-start="progress = 0; failed = false"
    x-on:livewire-upload-progress="progress = $event.detail.progress"
    x-on:livewire-upload-finish="progress = null"
    x-on:livewire-upload-error="progress = null; failed = true"
    x-on:livewire-upload-cancel="progress = null"
    class="space-y-3"
>
    <div
        x-bind:data-dragging="dragging"
        class="relative flex flex-col items-center gap-2 rounded-gui border-2 border-dashed border-gui-input px-6 py-8 text-center transition-colors hover:bg-zinc-50 has-focus-visible:outline-2 has-focus-visible:outline-offset-2 has-focus-visible:outline-gui-primary-text has-aria-invalid:border-gui-danger-text has-disabled:opacity-60 data-[dragging=true]:border-gui-primary-text data-[dragging=true]:bg-gui-primary/5 dark:hover:bg-zinc-900"
    >
        <x-gui::icon name="folder" size="lg" class="text-zinc-500 dark:text-zinc-400" />
        <span class="text-sm text-zinc-700 dark:text-zinc-300" aria-hidden="true">
            <span class="font-medium text-gui-primary-text">Choose {{ $multiple ? 'files' : 'a file' }}</span> or drag {{ $multiple ? 'them' : 'it' }} here
        </span>
        @if ($hint)
            <span id="{{ $id }}-zone-hint" class="text-xs text-zinc-600 dark:text-zinc-400">{{ $hint }}</span>
        @endif

        <input
            type="file"
            x-on:change="read($el)"
            x-on:dragenter="dragging = true"
            x-on:dragleave="dragging = false"
            x-on:drop="dragging = false"
            {{ $attributes
                ->except(['aria-describedby'])
                ->merge(array_merge($control['attributes'], [
                    'id' => $id,
                    'aria-describedby' => \JayAitch\GiraffeUI\Support\Form::describedBy($control['attributes']['aria-describedby'] ?? null, $hint ? "{$id}-zone-hint" : null),
                ]))
                ->class('absolute inset-0 size-full cursor-pointer opacity-0 disabled:cursor-not-allowed') }}
        >
    </div>

    <ul x-show="files.length" x-cloak class="divide-y divide-zinc-200 rounded-gui border border-zinc-200 text-sm dark:divide-zinc-800 dark:border-zinc-800" aria-label="Chosen files">
        <template x-for="file in files" x-bind:key="file.name">
            <li class="flex items-center justify-between gap-3 px-3 py-2">
                <span class="min-w-0 truncate text-zinc-900 dark:text-zinc-100" x-text="file.name"></span>
                <span class="shrink-0 text-zinc-600 tabular-nums dark:text-zinc-400" x-text="size(file.size)"></span>
            </li>
        </template>
    </ul>

    <div x-show="progress !== null" x-cloak>
        <div role="progressbar" aria-label="Uploading" aria-valuemin="0" aria-valuemax="100" x-bind:aria-valuenow="progress" class="h-2 overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-800">
            <div class="h-full rounded-full bg-gui-primary transition-[width] motion-reduce:transition-none" x-bind:style="`width: ${progress}%`"></div>
        </div>
    </div>

    <p x-show="failed" x-cloak role="alert" class="text-sm font-medium text-gui-danger-text">The upload failed. Please try again.</p>
</div>
