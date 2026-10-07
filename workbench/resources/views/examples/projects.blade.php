@extends('catalog::examples.layout')

@section('content')
    @php
        $people = ['Ada Lovelace', 'Grace Hopper', 'Alan Turing', 'Katherine Johnson'];
        $tags = [
            'Design' => 'var(--color-violet-500)',
            'Copy' => 'var(--color-lime-500)',
            'Build' => 'var(--color-sky-500)',
            'Launch' => 'var(--color-orange-500)',
        ];
        $columns = [
            'To do' => [
                ['title' => 'Write the launch email', 'tag' => 'Copy', 'due' => '18 Oct', 'people' => [1], 'tasks' => [0, 3]],
                ['title' => 'Pick photos for the home page', 'tag' => 'Design', 'due' => '21 Oct', 'people' => [0, 3], 'tasks' => [1, 5]],
                ['title' => 'Redirects from the old site', 'tag' => 'Build', 'due' => '24 Oct', 'people' => [2], 'tasks' => [0, 2]],
            ],
            'In progress' => [
                ['title' => 'Pricing page', 'tag' => 'Design', 'due' => '15 Oct', 'people' => [0, 1, 2], 'tasks' => [4, 6]],
                ['title' => 'Checkout flow', 'tag' => 'Build', 'due' => '17 Oct', 'people' => [2, 3], 'tasks' => [7, 9]],
            ],
            'In review' => [
                ['title' => 'Product descriptions', 'tag' => 'Copy', 'due' => '14 Oct', 'people' => [1, 3], 'tasks' => [12, 12]],
            ],
            'Done' => [
                ['title' => 'New logo', 'tag' => 'Design', 'due' => '2 Oct', 'people' => [0], 'tasks' => [3, 3]],
                ['title' => 'Analytics set up', 'tag' => 'Build', 'due' => '6 Oct', 'people' => [2], 'tasks' => [2, 2]],
                ['title' => 'Press list', 'tag' => 'Launch', 'due' => '9 Oct', 'people' => [3, 1], 'tasks' => [5, 5]],
            ],
        ];
        $total = collect($columns)->flatten(1)->sum(fn ($card) => $card['tasks'][1]);
        $done = collect($columns)->flatten(1)->sum(fn ($card) => $card['tasks'][0]);
    @endphp

    <x-example::app search="Search projects">
        <div class="space-y-8">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <x-gui::breadcrumbs :items="['Projects' => '#', 'Website relaunch' => null]" />
                    <h1 class="mt-3 text-4xl font-normal tracking-tight sm:text-5xl">Website <span class="text-zinc-500 dark:text-zinc-400">relaunch</span></h1>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <x-gui::avatar.group :avatars="collect($people)->map(fn ($name) => ['name' => $name])->all()" :max="3" label="Project members" size="sm" />
                    <x-gui::button color="ink" shape="pill" size="sm" x-on:click="GiraffeUI.toast('New task added', { color: 'success' })">
                        <x-gui::icon name="plus" size="sm" /> New task
                    </x-gui::button>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-x-8 gap-y-3">
                <div class="w-full max-w-xs">
                    <x-gui::progress :value="$done" :max="$total" label="{{ $done }} of {{ $total }} tasks done" show-value />
                </div>
                <div style="--radius-gui: 9999px">
                    <x-gui::segmented-control name="view" label="View" size="sm" :options="['board' => 'Board', 'list' => 'List', 'timeline' => 'Timeline']" value="board" />
                </div>
            </div>

            {{-- The board: a column per stage, scrolling sideways on small screens. --}}
            <div class="-mx-4 overflow-x-auto px-4 pb-2 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                <div class="grid min-w-[56rem] grid-cols-4 gap-4">
                    @foreach ($columns as $stage => $cards)
                        <section aria-labelledby="stage-{{ $loop->index }}" class="rounded-3xl bg-zinc-100 p-3 dark:bg-zinc-900">
                            <div class="flex items-center justify-between px-2 pt-1 pb-3">
                                <h2 id="stage-{{ $loop->index }}" class="flex items-center gap-2 font-medium">
                                    {{ $stage }}
                                    <x-gui::badge size="sm" shape="pill">{{ count($cards) }}</x-gui::badge>
                                </h2>
                                <x-gui::button variant="ghost" color="ink" shape="pill" icon size="sm" aria-label="Add a task to {{ $stage }}"><x-gui::icon name="plus" size="sm" /></x-gui::button>
                            </div>
                            <ul class="space-y-2">
                                @foreach ($cards as $card)
                                    <li class="space-y-3 rounded-2xl bg-white p-4 shadow-xs dark:bg-black">
                                        <x-gui::badge size="sm" shape="pill" :dot="$tags[$card['tag']]">{{ $card['tag'] }}</x-gui::badge>
                                        <p class="font-medium">{{ $card['title'] }}</p>
                                        <div class="flex items-center justify-between gap-2 text-xs text-zinc-600 dark:text-zinc-400">
                                            <span class="flex items-center gap-1.5 whitespace-nowrap"><x-gui::icon name="calendar" size="xs" /> {{ $card['due'] }}</span>
                                            <span class="flex items-center gap-1.5 tabular-nums"><x-gui::icon name="check-circle" size="xs" /> {{ $card['tasks'][0] }}/{{ $card['tasks'][1] }}</span>
                                            <x-gui::avatar.group :avatars="collect($card['people'])->map(fn ($i) => ['name' => $people[$i]])->all()" :max="2" size="sm" label="Assigned to" class="*:ring-white! dark:*:ring-black!" />
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endforeach
                </div>
            </div>
        </div>
    </x-example::app>
@endsection
