@extends('catalog::examples.layout')

@section('content')
    @php
        $photo = \Workbench\App\Catalog::data()['photo'];

        // Where the money went this month: each part's share sets its width; the bars are decoration.
        $breakdown = [
            ['label' => 'Invest', 'value' => 14465, 'change' => '+23%', 'style' => 'solid'],
            ['label' => 'Products', 'value' => 22458.7, 'change' => '+12%', 'style' => 'stripes'],
            ['label' => 'Other', 'value' => 6034.3, 'change' => '+1.2%', 'style' => 'faint'],
        ];
        $breakdownMax = max(array_column($breakdown, 'value'));
        $breakdownTotal = array_sum(array_column($breakdown, 'value'));

        $weeks = ['1 Mar', '15 Mar', '1 Apr', '15 Apr', '1 May', '15 May', '1 Jun', '15 Jun', '1 Jul', '15 Jul', '1 Aug', '15 Aug'];
        $income = [5200, 5900, 7400, 7968, 6100, 4800, 5600, 5957, 4700, 5300, 6800, 6200];
        $expenses = [3900, 4300, 3600, 4800, 4200, 3700, 4500, 4100, 3800, 4600, 4000, 4400];

        $hours = ['9 am', '11 am', '1 pm', '3 pm', '5 pm', '7 pm'];
        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $activity = [
            [3, 4, 6, 9, 5, 2, 1],
            [5, 6, 12, 14, 11, 4, 3],
            [4, 13, 15, 16, 14, 12, 3],
            [9, 10, 15, 18, 15, 13, 8],
            [4, 8, 9, 15, 19, 5, 3],
            [2, 3, 3, 7, 4, 2, 2],
        ];

        $transactions = [
            ['name' => 'Fastly', 'category' => 'Hosting', 'dot' => 'bg-lime-500', 'amount' => '-$40.00'],
            ['name' => 'Grace Hopper', 'category' => 'Transfer', 'dot' => 'bg-violet-500', 'amount' => '-$86.50'],
            ['name' => 'Figma', 'category' => 'Software', 'dot' => 'bg-sky-500', 'amount' => '-$248.80'],
            ['name' => 'Linear', 'category' => 'Software', 'dot' => 'bg-sky-500', 'amount' => '-$96.00'],
            ['name' => 'Alan Turing', 'category' => 'Transfer', 'dot' => 'bg-violet-500', 'amount' => '+$500.00'],
        ];

        // Pill buttons: dark on light, light on dark.
        $pill = 'rounded-full! bg-zinc-900! text-white! hover:bg-zinc-700! dark:bg-white! dark:text-zinc-900! dark:hover:bg-zinc-200!';
        $round = 'flex size-9 cursor-pointer items-center justify-center rounded-full bg-zinc-100 text-zinc-700 transition-colors hover:bg-zinc-200 hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gui-primary-text dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 dark:hover:text-zinc-100';
        $small = 'flex size-8 cursor-pointer items-center justify-center rounded-full bg-zinc-200 text-zinc-700 transition-colors hover:bg-zinc-300 hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gui-primary-text dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700 dark:hover:text-zinc-100';
        $cardClass = 'rounded-3xl border-transparent! bg-zinc-100! shadow-none! dark:bg-zinc-900!';
    @endphp

    {{-- The palette for this page: lime for income and violet for activity, over the theme's chart colours. --}}
    <div class="min-h-dvh bg-white [--color-gui-chart-1:var(--color-lime-600)] [--color-gui-chart-7:var(--color-violet-600)] [--gui-chart-surface:var(--color-zinc-100)] dark:bg-black dark:[--color-gui-chart-1:var(--color-lime-300)] dark:[--color-gui-chart-7:var(--color-violet-500)] dark:[--gui-chart-surface:var(--color-zinc-900)]">
        <x-gui::navbar variant="pills" container="max-w-7xl" class="border-transparent! bg-transparent! dark:border-transparent!">
            <x-slot:brand>
                <a href="/catalog/examples/dashboard" class="flex items-center gap-2.5 rounded-full text-lg font-semibold focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-gui-primary-text">
                    <span class="size-8 rounded-full bg-[conic-gradient(var(--color-violet-500)_0_25%,var(--color-violet-200)_0)]" aria-hidden="true"></span>
                    Acme
                </a>
            </x-slot:brand>

            <x-gui::navbar.item href="/catalog/examples/dashboard">Overview</x-gui::navbar.item>
            <x-gui::navbar.item href="#">Analytics</x-gui::navbar.item>
            @if (\Workbench\App\Catalog::livewire())
                <x-gui::navbar.item href="/catalog/examples/customers">Customers</x-gui::navbar.item>
            @endif
            <x-gui::navbar.item href="#">Reports</x-gui::navbar.item>
            <x-gui::navbar.item href="/catalog/examples/settings">Settings</x-gui::navbar.item>

            <x-slot:actions>
                <button type="button" class="{{ $round }} hidden sm:flex" aria-label="Search" x-on:click="GiraffeUI.toast('Search is coming soon')">
                    <x-gui::icon name="magnifying-glass" size="sm" />
                </button>
                <a href="#" class="{{ $round }} relative" aria-label="Messages, 2 unread">
                    <x-gui::icon name="envelope" size="sm" />
                    <span class="absolute top-2 end-2 size-2 rounded-full bg-gui-danger ring-2 ring-zinc-100 dark:ring-zinc-800" aria-hidden="true"></span>
                </a>
                <x-gui::dropdown id="account-menu" position="bottom-end">
                    <x-slot:trigger>
                        <button type="button" commandfor="account-menu" command="toggle-popover" aria-haspopup="menu" aria-label="Account" class="cursor-pointer rounded-full focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gui-primary-text">
                            <x-gui::avatar :src="$photo" name="Ada Lovelace" />
                        </button>
                    </x-slot:trigger>
                    <x-gui::dropdown.item href="#">Profile</x-gui::dropdown.item>
                    <x-gui::dropdown.item href="/catalog/examples/settings">Settings</x-gui::dropdown.item>
                    <x-gui::dropdown.separator />
                    <x-gui::dropdown.item href="/catalog/examples/sign-in">Sign out</x-gui::dropdown.item>
                </x-gui::dropdown>
            </x-slot:actions>
        </x-gui::navbar>

        <main class="mx-auto max-w-7xl space-y-10 px-4 pt-6 pb-16 sm:px-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h1 class="text-4xl font-normal tracking-tight sm:text-5xl">Welcome back, <span class="text-zinc-500 dark:text-zinc-400">Ada</span></h1>
                <div style="--radius-gui: 9999px">
                    <x-gui::segmented-control name="period" label="Period" size="sm" :options="['week' => 'Week', 'month' => 'Month', 'year' => 'Year']" value="month" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-10 lg:grid-cols-[minmax(0,20rem)_minmax(0,1fr)] lg:gap-6">
                {{-- The headline: total revenue, with the cents quieter than the dollars. --}}
                <section aria-labelledby="revenue-heading" class="space-y-6">
                    <div>
                        <h2 id="revenue-heading" class="text-base text-zinc-600 dark:text-zinc-400">Total revenue</h2>
                        <p class="mt-2 flex flex-wrap items-baseline gap-x-3">
                            <span class="text-5xl font-semibold tracking-tight tabular-nums">$48,200<span class="text-zinc-500">.00</span></span>
                            <span class="text-sm font-medium text-gui-success-text"><span class="sr-only">Up </span>+12.4%<span class="sr-only"> on last month</span></span>
                        </p>
                    </div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Available to spend: <span class="font-semibold text-zinc-900 tabular-nums dark:text-zinc-100">$31,450.00</span></p>
                    <div class="flex items-center gap-2">
                        <x-gui::button size="sm" :class="$pill" x-on:click="GiraffeUI.toast('Transfer started', { color: 'success' })">
                            Transfer <x-gui::icon name="arrow-up" size="sm" />
                        </x-gui::button>
                        <x-gui::button size="sm" :class="$pill" x-on:click="GiraffeUI.toast('Request sent', { color: 'success' })">
                            Request <x-gui::icon name="arrow-down" size="sm" />
                        </x-gui::button>
                        <x-gui::dropdown id="revenue-menu">
                            <x-slot:trigger>
                                <button type="button" commandfor="revenue-menu" command="toggle-popover" aria-haspopup="menu" aria-label="More actions" class="flex size-8 cursor-pointer items-center justify-center rounded-full border border-zinc-300 text-zinc-700 hover:bg-zinc-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gui-primary-text dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-900">
                                    <x-gui::icon name="ellipsis-vertical" size="sm" />
                                </button>
                            </x-slot:trigger>
                            <x-gui::dropdown.item x-on:click="GiraffeUI.toast('Report exported', { color: 'success' })">Export report</x-gui::dropdown.item>
                            <x-gui::dropdown.item href="#">View statements</x-gui::dropdown.item>
                        </x-gui::dropdown>
                    </div>
                </section>

                {{-- Where it went: a list of the parts, drawn as one split bar. Each column starts at a divider. --}}
                <section aria-labelledby="breakdown-heading">
                    <h2 id="breakdown-heading" class="sr-only">Spending by category this month</h2>
                    <div class="relative">
                        {{-- The average, dashed across every part. --}}
                        <div class="pointer-events-none absolute inset-x-0 top-[42%] border-t border-dashed border-zinc-300 dark:border-zinc-700" aria-hidden="true">
                            <span class="absolute end-[12%] -translate-y-1/2 rounded-full bg-zinc-200 px-2.5 py-0.5 text-xs text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">Average</span>
                        </div>
                        <dl class="flex h-52 gap-4">
                            @foreach ($breakdown as $part)
                                @php($rise = round(48 - $part['value'] / $breakdownMax * 44))
                                {{-- Equal columns on phones, so every amount fits; sized by share from sm up. --}}
                                <div class="flex min-w-0 flex-1 flex-col border-s border-zinc-200 ps-3 sm:flex-(--share) sm:ps-4 dark:border-zinc-800" style="--share: {{ round($part['value'] / $breakdownTotal * 100, 1) }} 1 0%">
                                    <dd class="text-base tabular-nums sm:text-xl" style="margin-top: {{ $rise }}px">${{ number_format($part['value'], 2) }}</dd>
                                    <dt class="mt-auto mb-2 truncate text-sm">
                                        <span class="font-medium">{{ $part['change'] }}</span>
                                        <span class="text-zinc-600 dark:text-zinc-400">{{ $part['label'] }}</span>
                                    </dt>
                                    <div class="h-12" aria-hidden="true">
                                        @if ($part['style'] === 'solid')
                                            <div class="h-full rounded-xl bg-violet-600"></div>
                                        @else
                                            {{-- Thin rounded stripes, fainter for the smaller part. --}}
                                            <div class="flex h-full justify-between gap-1 overflow-hidden">
                                                @foreach (range(1, $part['style'] === 'stripes' ? 22 : 8) as $stripe)
                                                    <span @class(['w-1.5 shrink-0 rounded-full', 'bg-violet-500' => $part['style'] === 'stripes', 'bg-violet-500/35' => $part['style'] === 'faint'])></span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </dl>
                        <div class="mt-3 flex justify-between text-sm text-zinc-600 dark:text-zinc-400">
                            <span>1 October</span>
                            <span>31 October</span>
                        </div>
                    </div>
                </section>
            </div>

            <div class="grid gap-4 lg:grid-cols-3">
                <x-gui::card :class="$cardClass">
                    <div class="mb-4 flex items-center justify-between gap-2">
                        <h2 class="flex items-center gap-2.5 text-lg">
                            <x-gui::icon name="presentation-chart-line" class="text-zinc-500 dark:text-zinc-400" />
                            Cash flow
                        </h2>
                        <x-gui::dropdown id="cash-flow-menu" position="bottom-end">
                            <x-slot:trigger>
                                <button type="button" commandfor="cash-flow-menu" command="toggle-popover" aria-haspopup="menu" aria-label="Cash flow options" class="{{ $small }}">
                                    <x-gui::icon name="ellipsis-vertical" size="sm" />
                                </button>
                            </x-slot:trigger>
                            <x-gui::dropdown.item href="#">Last 6 months</x-gui::dropdown.item>
                            <x-gui::dropdown.item href="#">Last 12 months</x-gui::dropdown.item>
                        </x-gui::dropdown>
                    </div>
                    <x-gui::chart.line
                        :labels="$weeks"
                        :series="['Income' => $income, 'Expenses' => $expenses]"
                        :colors="['Income' => 'chart-1', 'Expenses' => 'muted']"
                        :dashed="['Expenses']"
                        :axis="false"
                        :height="180"
                        prefix="$"
                        compact
                        curve
                        area
                    />
                </x-gui::card>

                <x-gui::card :class="$cardClass">
                    <div class="mb-4 flex items-center justify-between gap-2">
                        <h2 class="flex items-center gap-2.5 text-lg">
                            <x-gui::icon name="globe-alt" class="text-zinc-500 dark:text-zinc-400" />
                            Activity by time
                        </h2>
                        <a href="#" class="{{ $small }}" aria-label="Open the activity report">
                            <x-gui::icon name="arrow-up-right" size="sm" />
                        </a>
                    </div>
                    <x-gui::chart.heatmap
                        label="Orders by day and time"
                        color="chart-7"
                        :rows="$hours"
                        :columns="$days"
                        :data="$activity"
                    />
                </x-gui::card>

                <x-gui::card :class="$cardClass">
                    <div class="mb-4 flex items-center justify-between gap-2">
                        <h2 class="flex items-center gap-2.5 text-lg">
                            <x-gui::icon name="arrows-right-left" class="text-zinc-500 dark:text-zinc-400" />
                            Recent transactions
                        </h2>
                        <a href="#" class="{{ $small }}" aria-label="Search transactions">
                            <x-gui::icon name="magnifying-glass" size="sm" />
                        </a>
                    </div>
                    <ul class="divide-y divide-zinc-200/70 dark:divide-zinc-800">
                        @foreach ($transactions as $i => $transaction)
                            <li class="flex items-center gap-3 py-3">
                                <span class="min-w-0 flex-1 truncate text-sm font-medium">{{ $transaction['name'] }}</span>
                                <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-zinc-200 px-2.5 py-1 text-xs text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                    <span class="size-1.5 rounded-full {{ $transaction['dot'] }}" aria-hidden="true"></span>
                                    {{ $transaction['category'] }}
                                </span>
                                <span class="w-20 shrink-0 text-end text-sm font-medium tabular-nums">{{ $transaction['amount'] }}</span>
                                <x-gui::dropdown id="transaction-{{ $i }}" position="bottom-end">
                                    <x-slot:trigger>
                                        <button type="button" commandfor="transaction-{{ $i }}" command="toggle-popover" aria-haspopup="menu" aria-label="Options for {{ $transaction['name'] }}" class="-me-1.5 flex size-7 shrink-0 cursor-pointer items-center justify-center rounded-full text-zinc-500 hover:bg-zinc-200 hover:text-zinc-900 focus-visible:outline-2 focus-visible:outline-gui-primary-text dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-100">
                                            <x-gui::icon name="ellipsis-vertical" size="sm" />
                                        </button>
                                    </x-slot:trigger>
                                    <x-gui::dropdown.item href="#">View receipt</x-gui::dropdown.item>
                                    <x-gui::dropdown.item href="#">Change category</x-gui::dropdown.item>
                                </x-gui::dropdown>
                            </li>
                        @endforeach
                    </ul>
                </x-gui::card>
            </div>
        </main>
    </div>
@endsection
