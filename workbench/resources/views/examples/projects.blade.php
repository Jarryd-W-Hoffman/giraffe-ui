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
                ['title' => 'Write the launch email', 'start' => 12, 'tag' => 'Copy', 'due' => '18 Oct', 'people' => [1], 'tasks' => [0, 3]],
                ['title' => 'Pick photos for the home page', 'start' => 10, 'tag' => 'Design', 'due' => '21 Oct', 'people' => [0, 3], 'tasks' => [1, 5]],
                ['title' => 'Redirects from the old site', 'start' => 16, 'tag' => 'Build', 'due' => '24 Oct', 'people' => [2], 'tasks' => [0, 2]],
            ],
            'In progress' => [
                ['title' => 'Pricing page', 'start' => 6, 'tag' => 'Design', 'due' => '15 Oct', 'people' => [0, 1, 2], 'tasks' => [4, 6]],
                ['title' => 'Checkout flow', 'start' => 3, 'tag' => 'Build', 'due' => '17 Oct', 'people' => [2, 3], 'tasks' => [7, 9]],
            ],
            'In review' => [
                ['title' => 'Product descriptions', 'start' => 7, 'tag' => 'Copy', 'due' => '14 Oct', 'people' => [1, 3], 'tasks' => [12, 12]],
            ],
            'Done' => [
                ['title' => 'New logo', 'start' => 1, 'tag' => 'Design', 'due' => '2 Oct', 'people' => [0], 'tasks' => [3, 3]],
                ['title' => 'Analytics set up', 'start' => 2, 'tag' => 'Build', 'due' => '6 Oct', 'people' => [2], 'tasks' => [2, 2]],
                ['title' => 'Press list', 'start' => 4, 'tag' => 'Launch', 'due' => '9 Oct', 'people' => [3, 1], 'tasks' => [5, 5]],
            ],
        ];
        // Every task with its stage, for the list and the timeline.
        $tasks = collect($columns)->flatMap(fn ($cards, $stage) => collect($cards)->map(fn ($card) => $card + ['stage' => $stage]))->values();
        $stageColors = ['To do' => 'secondary', 'In progress' => 'info', 'In review' => 'warning', 'Done' => 'success'];
        $today = 13;
        $total = collect($columns)->flatten(1)->sum(fn ($card) => $card['tasks'][1]);
        $done = collect($columns)->flatten(1)->sum(fn ($card) => $card['tasks'][0]);
    @endphp

    <x-example::app search="Search projects">
        <div class="space-y-8" x-data="{ view: 'board' }">
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
                    <x-gui::segmented-control name="view" label="View" size="sm" x-model="view" :options="['board' => 'Board', 'list' => 'List', 'timeline' => 'Timeline']" value="board" />
                </div>
            </div>

            {{-- The board: a column per stage, scrolling sideways on small screens. --}}
            <div class="-mx-4 overflow-x-auto px-4 pb-2 sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8" x-show="view === 'board'">
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

            {{-- The list: every task in a table, the same data as the board. --}}
            <div x-show="view === 'list'" x-cloak class="rounded-3xl bg-zinc-100 p-4 sm:p-6 dark:bg-zinc-900">
                <x-gui::table label="Tasks">
                    <x-slot:head>
                        <x-gui::table.heading>Task</x-gui::table.heading>
                        <x-gui::table.heading>Stage</x-gui::table.heading>
                        <x-gui::table.heading class="hidden md:table-cell">Due</x-gui::table.heading>
                        <x-gui::table.heading class="hidden sm:table-cell">Progress</x-gui::table.heading>
                        <x-gui::table.heading class="hidden lg:table-cell">People</x-gui::table.heading>
                    </x-slot:head>
                    @foreach ($tasks as $task)
                        <x-gui::table.row>
                            <x-gui::table.cell>
                                <span class="block font-medium">{{ $task['title'] }}</span>
                                <x-gui::badge size="sm" shape="pill" :dot="$tags[$task['tag']]" class="mt-1.5">{{ $task['tag'] }}</x-gui::badge>
                            </x-gui::table.cell>
                            <x-gui::table.cell><x-gui::badge size="sm" shape="pill" :color="$stageColors[$task['stage']]">{{ $task['stage'] }}</x-gui::badge></x-gui::table.cell>
                            <x-gui::table.cell class="hidden whitespace-nowrap md:table-cell">{{ $task['due'] }}</x-gui::table.cell>
                            <x-gui::table.cell class="hidden w-40 sm:table-cell">
                                <x-gui::progress :value="$task['tasks'][0]" :max="$task['tasks'][1]" :label="$task['tasks'][0].' of '.$task['tasks'][1].' done'" />
                            </x-gui::table.cell>
                            <x-gui::table.cell class="hidden lg:table-cell">
                                <x-gui::avatar.group :avatars="collect($task['people'])->map(fn ($i) => ['name' => $people[$i]])->all()" :max="3" size="sm" label="Assigned to" />
                            </x-gui::table.cell>
                        </x-gui::table.row>
                    @endforeach
                </x-gui::table>
            </div>

            {{-- The timeline: each task as a bar across October, from its start to its due date. --}}
            <section x-show="view === 'timeline'" x-cloak aria-label="Timeline for October" class="rounded-3xl bg-zinc-100 p-4 sm:p-6 dark:bg-zinc-900">
                <div class="overflow-x-auto">
                    <div class="min-w-[44rem]">
                        <div class="grid grid-cols-[12rem_minmax(0,1fr)] gap-4 pb-3 text-xs text-zinc-600 dark:text-zinc-400" aria-hidden="true">
                            <span>Task</span>
                            <div class="relative h-4">
                                @foreach ([1, 8, 15, 22, 29] as $day)
                                    <span class="absolute -translate-x-1/2 whitespace-nowrap" style="left: {{ ($day - 1) / 30 * 100 }}%">{{ $day }} Oct</span>
                                @endforeach
                            </div>
                        </div>
                        <ol class="space-y-2">
                            @foreach ($tasks->sortBy('start') as $task)
                                @php($due = (int) $task['due'])
                                <li class="grid grid-cols-[12rem_minmax(0,1fr)] items-center gap-4">
                                    <span class="truncate text-sm font-medium">{{ $task['title'] }}</span>
                                    <div class="relative h-9 rounded-xl bg-white dark:bg-black">
                                        {{-- Today. --}}
                                        <span class="absolute inset-y-0 w-px bg-gui-danger" style="left: {{ ($today - 1) / 30 * 100 }}%" aria-hidden="true"></span>
                                        <span
                                            class="absolute inset-y-1.5 flex items-center overflow-hidden rounded-lg px-2 text-xs font-medium whitespace-nowrap text-zinc-950"
                                            style="left: {{ ($task['start'] - 1) / 30 * 100 }}%; width: {{ max(3, ($due - $task['start'] + 1) / 30 * 100) }}%; background: {{ $tags[$task['tag']] }}"
                                        >
                                            <span class="sr-only">{{ $task['tag'] }}, {{ $task['stage'] }}, from {{ $task['start'] }} to </span>{{ $task['due'] }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                        <p class="mt-4 flex items-center gap-2 text-xs text-zinc-600 dark:text-zinc-400"><span class="h-3 w-px bg-gui-danger" aria-hidden="true"></span> Today, {{ $today }} October</p>
                    </div>
                </div>
            </section>
        </div>
    </x-example::app>
@endsection
