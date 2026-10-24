@extends('catalog::examples.layout')

@section('content')
    @php
        // October 2026: it starts on a Thursday. Weeks start on Monday.
        $year = 2026;
        $month = 10;
        $today = 13;
        $first = new DateTimeImmutable("{$year}-{$month}-01");
        $offset = (int) $first->format('N') - 1;
        $days = (int) $first->format('t');
        $colors = [
            'Meeting' => 'var(--color-violet-500)',
            'Launch' => 'var(--color-orange-500)',
            'Review' => 'var(--color-sky-500)',
            'Personal' => 'var(--color-lime-500)',
        ];
        $events = [
            2 => [['9:30', 'Design review', 'Review', 'Studio']],
            5 => [['10:00', 'Quarterly planning', 'Meeting', 'Room 2']],
            7 => [['14:00', 'Dentist', 'Personal', 'Harley St']],
            9 => [['11:00', 'Press list sign-off', 'Launch', 'Video call']],
            13 => [['9:00', 'Stand-up', 'Meeting', 'Video call'], ['11:30', 'Pricing page review', 'Review', 'Studio'], ['16:00', 'Coffee with Grace', 'Personal', 'Kiln Café']],
            14 => [['10:00', 'Copy review', 'Review', 'Video call']],
            15 => [['9:00', 'Pricing page due', 'Launch', '']],
            16 => [['13:00', 'Board deck dry run', 'Meeting', 'Room 1']],
            20 => [['9:00', 'Stand-up', 'Meeting', 'Video call'], ['15:00', 'Checkout QA', 'Review', 'Studio']],
            21 => [['18:30', 'Climbing', 'Personal', 'The Arch']],
            22 => [['10:00', 'Launch rehearsal', 'Launch', 'Studio']],
            24 => [['All day', 'Launch day', 'Launch', '']],
            27 => [['9:00', 'Stand-up', 'Meeting', 'Video call']],
            29 => [['14:00', 'Launch retro', 'Meeting', 'Room 2']],
        ];
        $weekdays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $date = fn ($day) => new DateTimeImmutable("{$year}-{$month}-{$day}");
    @endphp

    <x-example::app search="Search events">
        <div class="space-y-8" x-data="{ selected: {{ $today }} }">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <x-gui::breadcrumbs :items="['Workspace' => '/catalog/examples/dashboard', 'Calendar' => null]" />
                    <h1 class="mt-3 text-4xl font-normal tracking-tight sm:text-5xl">October <span class="text-zinc-500 dark:text-zinc-400">2026</span></h1>
                    <p class="mt-3 text-zinc-600 dark:text-zinc-400">{{ collect($events)->flatten(1)->count() }} events this month.</p>
                </div>
                <div class="flex items-center gap-2">
                    <x-gui::button variant="ghost" color="ink" shape="pill" icon size="sm" aria-label="Previous month" x-on:click="GiraffeUI.toast('September is quiet')"><x-gui::icon name="chevron-left" size="sm" /></x-gui::button>
                    <x-gui::button variant="ghost" color="ink" shape="pill" size="sm" x-on:click="selected = {{ $today }}">Today</x-gui::button>
                    <x-gui::button variant="ghost" color="ink" shape="pill" icon size="sm" aria-label="Next month" x-on:click="GiraffeUI.toast('Nothing planned for November yet')"><x-gui::icon name="chevron-right" size="sm" /></x-gui::button>
                    <x-gui::button color="ink" shape="pill" size="sm" commandfor="new-event" command="show-modal"><x-gui::icon name="plus" size="sm" /> New event</x-gui::button>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 xl:grid-cols-[minmax(0,1fr)_22rem]">
                {{-- The month: a button per day; choosing one shows its events in the agenda. --}}
                <section aria-label="October 2026" class="rounded-3xl bg-zinc-100 p-3 sm:p-4 dark:bg-zinc-900">
                    <div class="grid grid-cols-7 gap-1 pb-2 text-center text-xs font-medium text-zinc-600 dark:text-zinc-400" aria-hidden="true">
                        @foreach ($weekdays as $weekday)
                            <span>{{ $weekday }}</span>
                        @endforeach
                    </div>
                    <ol class="grid grid-cols-7 gap-1">
                        @for ($blank = 0; $blank < $offset; $blank++)
                            <li aria-hidden="true"></li>
                        @endfor
                        @for ($day = 1; $day <= $days; $day++)
                            @php($dayEvents = $events[$day] ?? [])
                            <li>
                                <button
                                    type="button"
                                    x-on:click="selected = {{ $day }}"
                                    x-bind:aria-pressed="(selected === {{ $day }}).toString()"
                                    aria-pressed="{{ $day === $today ? 'true' : 'false' }}"
                                    class="flex h-20 w-full cursor-pointer flex-col gap-1 rounded-2xl bg-white p-2 text-start transition-colors hover:bg-zinc-50 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-gui-primary-text aria-pressed:ring-2 aria-pressed:ring-zinc-900 sm:h-28 dark:bg-black dark:hover:bg-zinc-950 dark:aria-pressed:ring-white"
                                >
                                    {{-- The name is the text, not an aria-label, so it contains the visible day number (WCAG 2.5.3). --}}
                                    <span class="sr-only">{{ $date($day)->format('l') }} </span>
                                    <span @class([
                                        'flex size-6 shrink-0 items-center justify-center rounded-full text-xs font-medium tabular-nums',
                                        'bg-gui-primary text-gui-primary-foreground' => $day === $today,
                                    ])>{{ $day }}</span>
                                    <span class="sr-only"> {{ $date($day)->format('F') }}{{ $day === $today ? ', today' : '' }}, {{ count($dayEvents) ? count($dayEvents).' '.(count($dayEvents) === 1 ? 'event' : 'events') : 'nothing on' }}</span>
                                    {{-- The day's events: names from the sm breakpoint, dots below it. --}}
                                    <span class="hidden min-w-0 space-y-0.5 sm:block" aria-hidden="true">
                                        @foreach (array_slice($dayEvents, 0, 2) as [$time, $title, $kind])
                                            <span class="flex items-center gap-1.5 truncate text-[11px] leading-4">
                                                <span class="size-1.5 shrink-0 rounded-full" style="background: {{ $colors[$kind] }}"></span>
                                                <span class="truncate">{{ $title }}</span>
                                            </span>
                                        @endforeach
                                        @if (count($dayEvents) > 2)
                                            <span class="block text-[11px] leading-4 text-zinc-500 dark:text-zinc-400">+{{ count($dayEvents) - 2 }} more</span>
                                        @endif
                                    </span>
                                    <span class="flex gap-0.5 sm:hidden" aria-hidden="true">
                                        @foreach ($dayEvents as [$time, $title, $kind])
                                            <span class="size-1.5 rounded-full" style="background: {{ $colors[$kind] }}"></span>
                                        @endforeach
                                    </span>
                                </button>
                            </li>
                        @endfor
                    </ol>
                </section>

                {{-- The agenda for the chosen day. --}}
                <section aria-label="Agenda" class="space-y-4">
                    @for ($day = 1; $day <= $days; $day++)
                        <x-gui::card variant="filled" :title="$date($day)->format('l j F')" icon="calendar" x-show="selected === {{ $day }}" :x-cloak="$day !== $today">
                            @if (! empty($events[$day]))
                                <x-gui::timeline :label="'Events on '.$date($day)->format('j F')">
                                    @foreach ($events[$day] as [$time, $title, $kind, $where])
                                        <x-gui::timeline.item :title="$title" :time="$time">
                                            <span class="flex flex-wrap items-center gap-2">
                                                <x-gui::badge size="sm" shape="pill" :dot="$colors[$kind]">{{ $kind }}</x-gui::badge>
                                                @if ($where)
                                                    <span class="inline-flex items-center gap-1"><x-gui::icon name="map-pin" size="xs" /> {{ $where }}</span>
                                                @endif
                                            </span>
                                            <x-slot:marker><span class="size-2.5 rounded-full" style="background: {{ $colors[$kind] }}"></span></x-slot:marker>
                                        </x-gui::timeline.item>
                                    @endforeach
                                </x-gui::timeline>
                            @else
                                <x-gui::empty-state icon="calendar" title="Nothing on" description="A free day. Add an event, or enjoy it." />
                            @endif
                        </x-gui::card>
                    @endfor

                    <x-gui::card variant="filled" title="Calendars" icon="squares-2x2">
                        <div class="space-y-3">
                            @foreach ($colors as $kind => $color)
                                <x-gui::checkbox :name="'calendar_'.strtolower($kind)" :label="$kind" checked />
                            @endforeach
                        </div>
                    </x-gui::card>
                </section>
            </div>
        </div>
    </x-example::app>

    <x-gui::modal id="new-event" title="New event" description="It goes on your calendar and the team's." size="md">
        <form id="new-event-form" class="space-y-4" x-on:submit.prevent="$el.closest('dialog').close(); $el.reset(); GiraffeUI.toast('Event added', { color: 'success' })">
            <x-gui::field label="Title" name="event_title" required>
                <x-gui::input placeholder="Launch party" />
            </x-gui::field>
            <x-gui::field label="When" name="event_when" required>
                <x-gui::datetime-picker min="2026-10-01" />
            </x-gui::field>
            <x-gui::field label="Calendar" name="event_kind">
                <x-gui::select :options="array_combine(array_keys($colors), array_keys($colors))" value="Meeting" />
            </x-gui::field>
        </form>
        <x-slot:footer>
            <x-gui::modal.close>Cancel</x-gui::modal.close>
            <x-gui::button type="submit" form="new-event-form" color="ink" shape="pill">Add event</x-gui::button>
        </x-slot:footer>
    </x-gui::modal>
@endsection
