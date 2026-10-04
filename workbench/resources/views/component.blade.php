@extends('catalog::layout')

@section('toc')
    <li><a href="#usage" class="hover:text-zinc-900 dark:hover:text-zinc-100">Usage</a></li>
    @if ($playground)
        <li><a href="#playground" class="hover:text-zinc-900 dark:hover:text-zinc-100">Playground</a></li>
    @endif
    <li><a href="#examples" class="hover:text-zinc-900 dark:hover:text-zinc-100">Examples</a></li>
    @foreach ($examples as $example)
        <li class="pl-3"><a href="#{{ $example['id'] }}" class="hover:text-zinc-900 dark:hover:text-zinc-100">{{ $example['title'] }}</a></li>
    @endforeach
    <li><a href="#api" class="hover:text-zinc-900 dark:hover:text-zinc-100">API reference</a></li>
@endsection

@section('content')
    @php
        $preview = $component['preview'] ?? 'inline';
        $previewClasses = match ($preview) {
            'block' => 'p-4 sm:p-8',
            'narrow' => 'px-4 py-8 sm:px-8 sm:py-10',
            default => 'flex flex-wrap items-center justify-center gap-3 p-8',
        };
    @endphp

    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $component['group'] ?? 'Components' }}</p>
    <h1 class="mt-1 text-3xl font-bold tracking-tight">{{ $component['name'] }}</h1>
    <p class="mt-3 text-lg text-zinc-600 dark:text-zinc-400">{{ $component['description'] }}</p>

    <h2 id="usage" class="mt-12 scroll-mt-20 text-xl font-semibold tracking-tight">Usage</h2>
    <div class="mt-4">
        @include('catalog::partials.code', ['code' => $component['usage']])
    </div>

    @if ($playground)
        <h2 id="playground" class="mt-12 scroll-mt-20 text-xl font-semibold tracking-tight">Playground</h2>
        <p class="mt-2 text-zinc-600 dark:text-zinc-400">Change the props and the real component re-renders on the server.</p>

        <div
            class="mt-4 overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-800"
            x-data="playground(@js(['slug' => $slug, 'controls' => $playground['controls'], 'selfClosing' => $playground['selfClosing'] ?? false]))"
        >
            <div class="grid md:grid-cols-[1fr_15rem]">
                <div x-ref="preview" class="flex min-h-56 items-center justify-center p-8" aria-live="polite">
                    {!! $playground['html'] !!}
                </div>

                <form class="space-y-4 border-t border-zinc-200 bg-zinc-50 p-4 md:border-t-0 md:border-l dark:border-zinc-800 dark:bg-zinc-900/50" x-on:submit.prevent>
                    @foreach ($playground['controls'] as $control)
                        @php $id = 'control-'.$control['name']; @endphp

                        @if ($control['type'] === 'boolean')
                            <label for="{{ $id }}" class="flex items-center justify-between gap-3 text-sm font-medium">
                                {{ $control['label'] ?? $control['name'] }}
                                <input id="{{ $id }}" type="checkbox" x-model="state.{{ $control['name'] }}" class="size-4 rounded border-zinc-300 accent-gui-primary">
                            </label>
                        @else
                            <div>
                                <label for="{{ $id }}" class="mb-1 block text-sm font-medium">{{ $control['name'] }}</label>
                                @if ($control['type'] === 'select')
                                    <select id="{{ $id }}" x-model="state.{{ $control['name'] }}" class="w-full rounded-md border border-zinc-300 bg-white px-2 py-1.5 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                                        @foreach ($control['options'] as $option)
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <input id="{{ $id }}" type="text" x-model.debounce.250ms="state.{{ $control['name'] }}" placeholder="{{ $control['name'] === 'href' ? 'e.g. /dashboard' : '' }}" class="w-full rounded-md border border-zinc-300 bg-white px-2 py-1.5 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                                @endif
                            </div>
                        @endif
                    @endforeach

                    <x-gui::button variant="ghost" color="secondary" size="sm" class="w-full" x-on:click="reset()">Reset</x-gui::button>
                </form>
            </div>

            <div class="border-t border-zinc-200 p-3 dark:border-zinc-800">
                @include('catalog::partials.code', ['model' => 'snippet'])
            </div>
        </div>
    @endif

    <h2 id="examples" class="mt-12 scroll-mt-20 text-xl font-semibold tracking-tight">Examples</h2>

    @foreach ($examples as $example)
        <section class="mt-8" x-data="{ tab: 'preview' }">
            <h3 id="{{ $example['id'] }}" class="scroll-mt-20 text-lg font-semibold">{{ $example['title'] }}</h3>
            @isset($example['description'])
                <p class="mt-1 text-zinc-600 dark:text-zinc-400">{{ $example['description'] }}</p>
            @endisset

            <div class="mt-3 flex gap-4 border-b border-zinc-200 text-sm dark:border-zinc-800" role="tablist">
                @foreach (['preview' => 'Preview', 'code' => 'Code'] as $tab => $label)
                    <button
                        type="button"
                        role="tab"
                        class="-mb-px border-b-2 px-1 pb-2 font-medium transition-colors"
                        x-bind:class="tab === '{{ $tab }}' ? 'border-zinc-900 text-zinc-900 dark:border-zinc-100 dark:text-zinc-100' : 'border-transparent text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100'"
                        x-bind:aria-selected="tab === '{{ $tab }}'"
                        x-on:click="tab = '{{ $tab }}'"
                    >{{ $label }}</button>
                @endforeach
            </div>

            <div x-show="tab === 'preview'" role="tabpanel" class="mt-4 min-h-40 rounded-xl border border-zinc-200 dark:border-zinc-800 {{ $previewClasses }}">
                @if ($preview === 'narrow')
                    <div class="mx-auto w-full max-w-sm space-y-4">{!! $example['html'] !!}</div>
                @else
                    {!! $example['html'] !!}
                @endif
            </div>

            <div x-show="tab === 'code'" x-cloak role="tabpanel" class="mt-4">
                @include('catalog::partials.code', ['code' => $example['code']])
            </div>
        </section>
    @endforeach

    <h2 id="api" class="mt-14 scroll-mt-20 text-xl font-semibold tracking-tight">API reference</h2>

    <h3 class="mt-6 font-semibold">Props</h3>
    <div class="mt-3 overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-800">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-zinc-50 text-zinc-500 dark:bg-zinc-900 dark:text-zinc-400">
                <tr>
                    <th scope="col" class="px-4 py-2.5 font-medium">Prop</th>
                    <th scope="col" class="px-4 py-2.5 font-medium">Type</th>
                    <th scope="col" class="px-4 py-2.5 font-medium">Default</th>
                    <th scope="col" class="px-4 py-2.5 font-medium">Description</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @foreach ($component['props'] as [$name, $type, $default, $description])
                    <tr class="align-top">
                        <td class="px-4 py-3 font-mono text-[13px] font-medium whitespace-nowrap">{{ $name }}</td>
                        <td class="px-4 py-3 font-mono text-[13px] text-zinc-600 dark:text-zinc-400">{{ $type }}</td>
                        <td class="px-4 py-3 font-mono text-[13px] whitespace-nowrap text-zinc-600 dark:text-zinc-400">{{ $default }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $description }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @foreach ($component['notes'] ?? [] as $note)
        <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $note }}</p>
    @endforeach
    <p class="mt-3 text-sm text-zinc-600 dark:text-zinc-400">Any other attribute, such as <code class="font-mono text-[13px]">class</code>, <code class="font-mono text-[13px]">wire:click</code> or <code class="font-mono text-[13px]">x-on:click</code>, is passed straight through to the element.</p>

    <h3 class="mt-8 font-semibold">Slots</h3>
    <div class="mt-3 overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-800">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-zinc-50 text-zinc-500 dark:bg-zinc-900 dark:text-zinc-400">
                <tr>
                    <th scope="col" class="px-4 py-2.5 font-medium">Slot</th>
                    <th scope="col" class="px-4 py-2.5 font-medium">Description</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @foreach ($component['slots'] as [$name, $description])
                    <tr>
                        <td class="px-4 py-3 font-mono text-[13px] font-medium whitespace-nowrap">{{ $name }}</td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $description }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
