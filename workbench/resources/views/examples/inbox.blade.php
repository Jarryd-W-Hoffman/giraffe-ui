@extends('catalog::examples.layout')

@section('content')
    @php
        $photo = \Workbench\App\Catalog::data()['photo'];
        $labels = [
            'Billing' => 'var(--color-lime-500)',
            'Team' => 'var(--color-violet-500)',
            'Support' => 'var(--color-sky-500)',
            'Launch' => 'var(--color-orange-500)',
        ];
        $conversations = [
            ['id' => 1, 'from' => 'Grace Hopper', 'email' => 'grace@example.com', 'subject' => 'Launch copy is ready for review', 'label' => 'Launch', 'time' => '9:42 AM', 'unread' => true, 'starred' => true, 'messages' => [
                ['from' => 'Grace Hopper', 'time' => '9:42 AM', 'body' => ["Morning Ada,", "The launch copy is in the doc: home page, pricing and the announcement email. I kept the headline short and moved the numbers into the subheading.", "Could you have a look before lunch? I'd like to hand it to design this afternoon."]],
            ]],
            ['id' => 2, 'from' => 'Stripe', 'email' => 'receipts@example.com', 'subject' => 'Your payout of $12,480.00 is on its way', 'label' => 'Billing', 'time' => '8:15 AM', 'unread' => true, 'starred' => false, 'messages' => [
                ['from' => 'Stripe', 'time' => '8:15 AM', 'body' => ['A payout of $12,480.00 is on its way to your bank account ending 4242.', 'It should arrive within two working days.']],
            ]],
            ['id' => 3, 'from' => 'Alan Turing', 'email' => 'alan@example.com', 'subject' => 'Re: Checkout flow', 'label' => 'Team', 'time' => 'Yesterday', 'unread' => false, 'starred' => false, 'messages' => [
                ['from' => 'Ada Lovelace', 'time' => 'Yesterday, 4:10 PM', 'body' => ['Where are we with the checkout flow? Anything blocking you?']],
                ['from' => 'Alan Turing', 'time' => 'Yesterday, 5:02 PM', 'body' => ['Nearly there. Card payments work end to end; I am finishing the error states now.', 'One question: should a declined card keep the rest of the form filled in? I think yes.']],
            ]],
            ['id' => 4, 'from' => 'Katherine Johnson', 'email' => 'katherine@example.com', 'subject' => 'Numbers for the board deck', 'label' => 'Team', 'time' => 'Yesterday', 'unread' => true, 'starred' => false, 'messages' => [
                ['from' => 'Katherine Johnson', 'time' => 'Yesterday, 2:31 PM', 'body' => ["I've pulled revenue, churn and cash flow for the quarter. Churn is down to 1.8%, the lowest it has been.", 'Want me to add the cohort chart as well?']],
            ]],
            ['id' => 5, 'from' => 'Help desk', 'email' => 'support@example.com', 'subject' => 'Ticket #4821: Export is missing a column', 'label' => 'Support', 'time' => 'Mon', 'unread' => false, 'starred' => false, 'messages' => [
                ['from' => 'Help desk', 'time' => 'Mon, 11:20 AM', 'body' => ['A customer reports that the CSV export is missing the "Joined" column since Friday.', 'Priority: normal. Assigned to you.']],
            ]],
            ['id' => 6, 'from' => 'Margaret Hamilton', 'email' => 'margaret@example.com', 'subject' => 'Offsite: dates that work', 'label' => 'Team', 'time' => 'Mon', 'unread' => false, 'starred' => true, 'messages' => [
                ['from' => 'Margaret Hamilton', 'time' => 'Mon, 9:05 AM', 'body' => ['The venue has the 14th to the 16th or the 21st to the 23rd. The first is cheaper; the second gives us more time to prepare.', 'Let me know which by Friday and I will book it.']],
            ]],
            ['id' => 7, 'from' => 'Fastly', 'email' => 'billing@example.com', 'subject' => 'Your September invoice', 'label' => 'Billing', 'time' => '2 Oct', 'unread' => false, 'starred' => false, 'messages' => [
                ['from' => 'Fastly', 'time' => '2 Oct, 7:00 AM', 'body' => ['Your invoice for September is ready: $40.00, paid by card.', 'Thank you for your business.']],
            ]],
        ];
        $initials = fn ($name) => collect(explode(' ', $name))->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->join('');
    @endphp

    <x-example::app search="Search mail">
        <div
            class="space-y-6"
            x-data="{
                selected: 1,
                // On small screens the list and the conversation take turns; open is the conversation.
                open: false,
                filter: 'all',
                query: '',
                unread: @js(collect($conversations)->mapWithKeys(fn ($c) => [$c['id'] => $c['unread']])),
                starred: @js(collect($conversations)->mapWithKeys(fn ($c) => [$c['id'] => $c['starred']])),
                archived: {},
                replies: {},
                draft: '',
                search: @js(collect($conversations)->mapWithKeys(fn ($c) => [$c['id'] => mb_strtolower($c['from'].' '.$c['subject'].' '.$c['label'].' '.implode(' ', collect($c['messages'])->flatMap(fn ($m) => $m['body'])->all()))])),
                shows(id) {
                    if (this.archived[id]) return false;
                    if (this.filter === 'unread' && ! this.unread[id]) return false;
                    return this.search[id].includes(this.query.trim().toLowerCase());
                },
                get count() {
                    return Object.keys(this.search).filter((id) => this.shows(id)).length;
                },
                get unreadCount() {
                    return Object.keys(this.unread).filter((id) => this.unread[id] && ! this.archived[id]).length;
                },
                choose(id) {
                    this.selected = id;
                    this.unread[id] = false;
                    this.open = true;
                    this.$nextTick(() => document.getElementById('thread-' + id + '-heading')?.focus());
                },
                archive(id) {
                    this.archived[id] = true;
                    GiraffeUI.toast('Conversation archived');
                    const next = Object.keys(this.search).map(Number).find((other) => this.shows(other));
                    this.selected = next ?? null;
                    this.open = false;
                },
                send(id) {
                    if (! this.draft.trim()) return;
                    (this.replies[id] ??= []).push(this.draft.trim());
                    this.draft = '';
                    GiraffeUI.toast('Reply sent', { color: 'success' });
                },
            }"
        >
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <x-gui::breadcrumbs :items="['Workspace' => '/catalog/examples/dashboard', 'Inbox' => null]" />
                    <h1 class="mt-3 text-4xl font-normal tracking-tight sm:text-5xl">Your <span class="text-zinc-500 dark:text-zinc-400">inbox</span></h1>
                    <p class="mt-3 text-zinc-600 dark:text-zinc-400" aria-live="polite"><span x-text="unreadCount">3</span> unread</p>
                </div>
                <x-gui::button color="ink" shape="pill" commandfor="compose" command="show-modal">
                    <x-gui::icon name="pencil-square" size="sm" /> Compose
                </x-gui::button>
            </div>

            <div class="grid grid-cols-1 gap-4 lg:h-[calc(100dvh-15rem)] lg:min-h-[36rem] lg:grid-cols-[minmax(0,24rem)_minmax(0,1fr)]">
                {{-- The list: every conversation, filtered by the search and the All/Unread switch. --}}
                <section aria-label="Conversations" class="flex min-h-0 flex-col rounded-3xl bg-zinc-100 p-3 dark:bg-zinc-900" x-bind:class="open && 'max-lg:hidden'">
                    <div class="flex flex-wrap items-center gap-2 p-1 pb-3">
                        <x-gui::input type="search" x-model="query" placeholder="Search conversations" aria-label="Search conversations" size="sm" class="min-w-0 flex-1 rounded-full!">
                            <x-slot:leading><x-gui::icon name="magnifying-glass" size="sm" /></x-slot:leading>
                        </x-gui::input>
                        <div style="--radius-gui: 9999px">
                            <x-gui::segmented-control name="inbox_filter" label="Show" size="sm" x-model="filter" :options="['all' => 'All', 'unread' => 'Unread']" value="all" />
                        </div>
                    </div>

                    <ul class="min-h-0 flex-1 space-y-1 overflow-y-auto">
                        @foreach ($conversations as $conversation)
                            <li x-show="shows({{ $conversation['id'] }})">
                                <button
                                    type="button"
                                    x-on:click="choose({{ $conversation['id'] }})"
                                    x-bind:aria-current="selected === {{ $conversation['id'] }} ? 'true' : null"
                                    x-bind:class="selected === {{ $conversation['id'] }} ? 'bg-white shadow-xs dark:bg-black' : 'hover:bg-zinc-200/70 dark:hover:bg-zinc-800/70'"
                                    class="flex w-full cursor-pointer gap-3 rounded-2xl p-3 text-start transition-colors focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-gui-primary-text"
                                >
                                    <x-gui::avatar :name="$conversation['from']" size="sm" />
                                    <span class="min-w-0 flex-1">
                                        <span class="flex items-center gap-2">
                                            <span class="min-w-0 flex-1 truncate text-sm" x-bind:class="unread[{{ $conversation['id'] }}] ? 'font-semibold' : 'font-medium'">{{ $conversation['from'] }}</span>
                                            <span class="shrink-0 text-xs text-zinc-600 dark:text-zinc-400">{{ $conversation['time'] }}</span>
                                        </span>
                                        <span class="mt-0.5 block truncate text-sm" x-bind:class="unread[{{ $conversation['id'] }}] ? 'text-zinc-900 dark:text-zinc-100' : 'text-zinc-600 dark:text-zinc-400'">{{ $conversation['subject'] }}</span>
                                        <span class="mt-2 flex items-center gap-2">
                                            <x-gui::badge size="sm" shape="pill" :dot="$labels[$conversation['label']]">{{ $conversation['label'] }}</x-gui::badge>
                                            <x-gui::icon name="star" size="xs" class="text-amber-500" x-show="starred[{{ $conversation['id'] }}]" />
                                            <span class="ms-auto size-2 rounded-full bg-gui-primary" x-show="unread[{{ $conversation['id'] }}]"></span>
                                            {{-- What the dot and star mean, for screen readers. --}}
                                            <span class="sr-only" x-text="[unread[{{ $conversation['id'] }}] && 'Unread', starred[{{ $conversation['id'] }}] && 'Starred'].filter(Boolean).join(', ')"></span>
                                        </span>
                                    </span>
                                </button>
                            </li>
                        @endforeach
                    </ul>

                    <div x-show="count === 0" x-cloak class="flex-1">
                        <x-gui::empty-state icon="inbox" title="Nothing here" description="No conversations match. Try another search, or show all." />
                    </div>
                </section>

                {{-- The conversation that's open. --}}
                <div class="flex min-h-[32rem] min-w-0 flex-col rounded-3xl bg-zinc-100 dark:bg-zinc-900 lg:min-h-0" x-bind:class="! open && 'max-lg:hidden'">
                    @foreach ($conversations as $conversation)
                        @php($id = $conversation['id'])
                        <article aria-labelledby="thread-{{ $id }}-heading" class="flex min-h-0 flex-1 flex-col" x-show="selected === {{ $id }} && ! archived[{{ $id }}]" @if (! $loop->first) x-cloak @endif>
                            <header class="flex flex-wrap items-start gap-3 border-b border-zinc-200 p-5 dark:border-zinc-800">
                                <x-gui::button variant="ghost" color="ink" shape="pill" icon size="sm" class="lg:hidden" aria-label="Back to conversations" x-on:click="open = false"><x-gui::icon name="arrow-left" size="sm" /></x-gui::button>
                                <div class="min-w-0 flex-1">
                                    <h2 id="thread-{{ $id }}-heading" tabindex="-1" class="text-xl font-medium tracking-tight focus:outline-none">{{ $conversation['subject'] }}</h2>
                                    <x-gui::badge size="sm" shape="pill" :dot="$labels[$conversation['label']]" class="mt-2">{{ $conversation['label'] }}</x-gui::badge>
                                </div>
                                <div class="flex items-center gap-1">
                                    <x-gui::button variant="ghost" color="ink" shape="pill" icon size="sm" aria-label="Star" aria-pressed="{{ $conversation['starred'] ? 'true' : 'false' }}" x-bind:aria-pressed="starred[{{ $id }}].toString()" x-on:click="starred[{{ $id }}] = ! starred[{{ $id }}]">
                                        <x-gui::icon name="star" size="sm" x-bind:class="starred[{{ $id }}] && 'text-amber-500'" />
                                    </x-gui::button>
                                    <x-gui::button variant="ghost" color="ink" shape="pill" icon size="sm" aria-label="Mark as unread" x-on:click="unread[{{ $id }}] = true; open = false; GiraffeUI.toast('Marked as unread')">
                                        <x-gui::icon name="envelope" size="sm" />
                                    </x-gui::button>
                                    <x-gui::button variant="ghost" color="ink" shape="pill" icon size="sm" aria-label="Archive" x-on:click="archive({{ $id }})">
                                        <x-gui::icon name="archive-box" size="sm" />
                                    </x-gui::button>
                                </div>
                            </header>

                            <ol class="min-h-0 flex-1 space-y-4 overflow-y-auto p-5">
                                @foreach ($conversation['messages'] as $message)
                                    @php($mine = $message['from'] === 'Ada Lovelace')
                                    <li class="rounded-2xl bg-white p-5 dark:bg-black">
                                        <div class="flex items-center gap-3">
                                            <x-gui::avatar :name="$message['from']" :src="$mine ? $photo : null" size="sm" />
                                            <div class="min-w-0 flex-1">
                                                <p class="truncate text-sm font-medium">{{ $mine ? 'You' : $message['from'] }}</p>
                                                <p class="truncate text-xs text-zinc-600 dark:text-zinc-400">{{ $mine ? 'ada@example.com' : $conversation['email'] }}</p>
                                            </div>
                                            <time class="shrink-0 text-xs text-zinc-600 dark:text-zinc-400">{{ $message['time'] }}</time>
                                        </div>
                                        <div class="mt-4 space-y-3 text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">
                                            @foreach ($message['body'] as $paragraph)
                                                <p>{{ $paragraph }}</p>
                                            @endforeach
                                        </div>
                                    </li>
                                @endforeach
                                {{-- Your replies, as you send them. --}}
                                <template x-for="(reply, index) in replies[{{ $id }}] ?? []" x-bind:key="index">
                                    <li class="rounded-2xl bg-white p-5 dark:bg-black">
                                        <div class="flex items-center gap-3">
                                            <x-gui::avatar :src="$photo" name="Ada Lovelace" size="sm" />
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-medium">You</p>
                                                <p class="text-xs text-zinc-600 dark:text-zinc-400">ada@example.com</p>
                                            </div>
                                            <span class="shrink-0 text-xs text-zinc-600 dark:text-zinc-400">Just now</span>
                                        </div>
                                        <p class="mt-4 text-sm leading-relaxed whitespace-pre-line text-zinc-700 dark:text-zinc-300" x-text="reply"></p>
                                    </li>
                                </template>
                            </ol>

                            <form class="border-t border-zinc-200 p-4 dark:border-zinc-800" x-on:submit.prevent="send({{ $id }})">
                                <x-gui::field label="Reply to {{ $conversation['from'] }}" name="reply_{{ $id }}" class="[&_label]:sr-only">
                                    <x-gui::textarea rows="2" autosize x-model="draft" placeholder="Write a reply…" />
                                </x-gui::field>
                                <div class="mt-3 flex items-center justify-between gap-3">
                                    <x-gui::button variant="ghost" color="ink" shape="pill" icon size="sm" aria-label="Attach a file" x-on:click="GiraffeUI.toast('Attachments are coming soon')"><x-gui::icon name="paper-clip" size="sm" /></x-gui::button>
                                    <x-gui::button type="submit" color="ink" shape="pill" size="sm">Send <x-gui::icon name="paper-airplane" size="sm" /></x-gui::button>
                                </div>
                            </form>
                        </article>
                    @endforeach

                    <div x-show="selected === null || archived[selected]" x-cloak class="flex flex-1 items-center justify-center p-6">
                        <x-gui::empty-state icon="inbox" title="Inbox zero" description="Nothing open. Pick a conversation, or enjoy the quiet." />
                    </div>
                </div>
            </div>
        </div>
    </x-example::app>

    <x-gui::modal id="compose" title="New message" size="lg">
        <form id="compose-form" class="space-y-4" x-on:submit.prevent="$el.closest('dialog').close(); $el.reset(); GiraffeUI.toast('Message sent', { color: 'success' })">
            <x-gui::field label="To" name="compose_to" required>
                <x-gui::input type="email" placeholder="grace@example.com" />
            </x-gui::field>
            <x-gui::field label="Subject" name="compose_subject">
                <x-gui::input />
            </x-gui::field>
            <x-gui::field label="Message" name="compose_body">
                <x-gui::textarea rows="5" autosize />
            </x-gui::field>
        </form>
        <x-slot:footer>
            <x-gui::modal.close>Discard</x-gui::modal.close>
            <x-gui::button type="submit" form="compose-form" color="ink" shape="pill">Send</x-gui::button>
        </x-slot:footer>
    </x-gui::modal>
@endsection
