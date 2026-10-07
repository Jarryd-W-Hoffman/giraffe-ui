@extends('catalog::examples.layout')

@section('content')
    @php
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
            ['name' => 'Fastly', 'category' => 'Hosting', 'dot' => 'var(--color-lime-500)', 'amount' => '-$40.00'],
            ['name' => 'Grace Hopper', 'category' => 'Transfer', 'dot' => 'var(--color-violet-500)', 'amount' => '-$86.50'],
            ['name' => 'Figma', 'category' => 'Software', 'dot' => 'var(--color-sky-500)', 'amount' => '-$248.80'],
            ['name' => 'Linear', 'category' => 'Software', 'dot' => 'var(--color-sky-500)', 'amount' => '-$96.00'],
            ['name' => 'Alan Turing', 'category' => 'Transfer', 'dot' => 'var(--color-violet-500)', 'amount' => '+$500.00'],
        ];


        $stats = [
            ['label' => 'Customers', 'value' => '2,840', 'change' => '8%', 'trend' => 'up', 'data' => [2210, 2290, 2350, 2400, 2460, 2530, 2590, 2650, 2700, 2760, 2800, 2840]],
            ['label' => 'Orders', 'value' => '1,204', 'change' => '4%', 'trend' => 'up', 'data' => [980, 1010, 995, 1060, 1040, 1100, 1090, 1120, 1150, 1130, 1160, 1204]],
            ['label' => 'Average order', 'value' => '$40.03', 'change' => '2%', 'trend' => 'down', 'data' => [42.1, 41.8, 42.4, 41.2, 40.9, 41.5, 40.6, 40.8, 40.2, 40.5, 40.1, 40.03]],
            ['label' => 'Refund rate', 'value' => '1.8%', 'change' => '0.3 pts', 'trend' => 'down', 'invert' => true, 'data' => [2.6, 2.4, 2.5, 2.2, 2.3, 2.1, 2.2, 1.9, 2.0, 1.9, 2.1, 1.8]],
        ];
        $months = ['May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'];
        $goals = [
            ['label' => 'Monthly revenue', 'value' => 48200, 'max' => 60000, 'text' => '$48,200 of $60,000'],
            ['label' => 'New customers', 'value' => 312, 'max' => 400, 'text' => '312 of 400'],
            ['label' => 'Emergency fund', 'value' => 18000, 'max' => 20000, 'text' => '$18,000 of $20,000'],
        ];
        $upcoming = [
            ['title' => 'Payroll', 'time' => '15 Oct', 'icon' => 'banknotes', 'detail' => '$21,400 to 12 people'],
            ['title' => 'Office rent', 'time' => '20 Oct', 'icon' => 'building-office', 'detail' => '$4,800'],
            ['title' => 'Quarterly tax', 'time' => '31 Oct', 'icon' => 'receipt-percent', 'detail' => 'Estimated $6,120'],
        ];
    @endphp

    <x-example::app>
        <div class="space-y-10">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h1 class="text-4xl font-normal tracking-tight sm:text-5xl">Welcome back, <span class="text-zinc-500 dark:text-zinc-400">Ada</span></h1>
                <div style="--radius-gui: 9999px">
                    <x-gui::segmented-control name="period" label="Period" size="sm" :options="['week' => 'Week', 'month' => 'Month', 'year' => 'Year']" value="month" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-10 2xl:grid-cols-[minmax(0,20rem)_minmax(0,1fr)] 2xl:gap-6">
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
                        <x-gui::button color="ink" shape="pill" size="sm" x-on:click="GiraffeUI.toast('Transfer started', { color: 'success' })">
                            Transfer <x-gui::icon name="arrow-up" size="sm" />
                        </x-gui::button>
                        <x-gui::button color="ink" shape="pill" size="sm" x-on:click="GiraffeUI.toast('Request sent', { color: 'success' })">
                            Request <x-gui::icon name="arrow-down" size="sm" />
                        </x-gui::button>
                        <x-gui::dropdown id="revenue-menu">
                            <x-slot:trigger>
                                <x-gui::button variant="outline" color="ink" shape="pill" icon size="sm" commandfor="revenue-menu" command="toggle-popover" aria-haspopup="menu" aria-label="More actions"><x-gui::icon name="ellipsis-vertical" size="sm" /></x-gui::button>
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
                                {{-- Equal columns on phones, so every amount fits; sized by share from md up. --}}
                                <div class="flex min-w-0 flex-1 flex-col border-s border-zinc-200 ps-3 md:flex-(--share) md:ps-4 dark:border-zinc-800" style="--share: {{ round($part['value'] / $breakdownTotal * 100, 1) }} 1 0%">
                                    <dd class="text-base tabular-nums md:text-xl" style="margin-top: {{ $rise }}px">${{ number_format($part['value'], 2) }}</dd>
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

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 2xl:grid-cols-3">
                <x-gui::card variant="filled" title="Cash flow" icon="presentation-chart-line">
                    <x-slot:actions>
                        <x-gui::dropdown id="cash-flow-menu" position="bottom-end">
                            <x-slot:trigger>
                                <x-gui::button variant="ghost" color="ink" shape="pill" icon size="sm" aria-label="Cash flow options" commandfor="cash-flow-menu" command="toggle-popover" aria-haspopup="menu"><x-gui::icon name="ellipsis-vertical" size="sm" /></x-gui::button>
                            </x-slot:trigger>
                            <x-gui::dropdown.item href="#">Last 6 months</x-gui::dropdown.item>
                            <x-gui::dropdown.item href="#">Last 12 months</x-gui::dropdown.item>
                        </x-gui::dropdown>
                    </x-slot:actions>
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

                <x-gui::card variant="filled" title="Activity by time" icon="globe-alt">
                    <x-slot:actions>
                        <x-gui::button variant="ghost" color="ink" shape="pill" icon size="sm" aria-label="Open the activity report" href="#"><x-gui::icon name="arrow-up-right" size="sm" /></x-gui::button>
                    </x-slot:actions>
                    <x-gui::chart.heatmap
                        label="Orders by day and time"
                        color="chart-7"
                        :rows="$hours"
                        :columns="$days"
                        :data="$activity"
                    />
                </x-gui::card>

                <x-gui::card variant="filled" title="Recent transactions" icon="arrows-right-left" class="lg:col-span-2 2xl:col-span-1">
                    <x-slot:actions>
                        <x-gui::button variant="ghost" color="ink" shape="pill" icon size="sm" aria-label="Search transactions" href="#"><x-gui::icon name="magnifying-glass" size="sm" /></x-gui::button>
                    </x-slot:actions>
                    <ul class="divide-y divide-zinc-200/70 dark:divide-zinc-800">
                        @foreach ($transactions as $i => $transaction)
                            <li class="flex items-center gap-3 py-3">
                                <span class="min-w-0 flex-1 truncate text-sm font-medium">{{ $transaction['name'] }}</span>
                                <x-gui::badge shape="pill" size="sm" :dot="$transaction['dot']">{{ $transaction['category'] }}</x-gui::badge>
                                <span class="w-20 shrink-0 text-end text-sm font-medium tabular-nums">{{ $transaction['amount'] }}</span>
                                <x-gui::dropdown id="transaction-{{ $i }}" position="bottom-end">
                                    <x-slot:trigger>
                                        <x-gui::button variant="ghost" color="ink" shape="pill" icon size="sm" class="-me-1.5 size-7! bg-transparent! hover:bg-zinc-200! dark:hover:bg-zinc-800!" commandfor="transaction-{{ $i }}" command="toggle-popover" aria-haspopup="menu" aria-label="Options for {{ $transaction['name'] }}"><x-gui::icon name="ellipsis-vertical" size="sm" /></x-gui::button>
                                    </x-slot:trigger>
                                    <x-gui::dropdown.item href="#">View receipt</x-gui::dropdown.item>
                                    <x-gui::dropdown.item href="#">Change category</x-gui::dropdown.item>
                                </x-gui::dropdown>
                            </li>
                        @endforeach
                    </ul>
                </x-gui::card>
            </div>

            {{-- The month at a glance: four stats, each with its trend. --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @foreach ($stats as $stat)
                    <x-gui::card variant="filled" class="flex flex-col justify-between gap-6">
                        <x-gui::stat :label="$stat['label']" :value="$stat['value']" :change="$stat['change']" :trend="$stat['trend']" :invert="$stat['invert'] ?? false" description="vs last month" />
                        <x-gui::sparkline :data="$stat['data']" color="chart-1" area class="h-12 w-full" />
                    </x-gui::card>
                @endforeach
            </div>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                <x-gui::card variant="filled" title="Revenue by channel" icon="chart-bar" class="lg:col-span-2">
                    <x-slot:actions>
                        <x-gui::button variant="ghost" color="ink" shape="pill" icon size="sm" aria-label="Open the revenue report" href="#"><x-gui::icon name="arrow-up-right" size="sm" /></x-gui::button>
                    </x-slot:actions>
                    <x-gui::chart.bar
                        :labels="$months"
                        :series="['Online' => [21300, 24100, 27300, 28800, 30100, 33600], 'In store' => [9300, 9600, 10400, 10900, 11800, 14600]]"
                        :colors="['Online' => 'chart-1', 'In store' => 'chart-7']"
                        :height="220"
                        prefix="$"
                        compact
                    />
                </x-gui::card>

                <x-gui::card variant="filled" title="Goals" icon="trophy">
                    <div class="space-y-6">
                        @foreach ($goals as $goal)
                            <div>
                                <x-gui::progress :value="$goal['value']" :max="$goal['max']" :label="$goal['label']" show-value />
                                <p class="mt-1.5 text-xs text-zinc-600 tabular-nums dark:text-zinc-400">{{ $goal['text'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </x-gui::card>
            </div>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                <x-gui::card variant="filled" title="Top products" icon="shopping-bag">
                    <x-gui::chart.bar-list
                        label="Top products by revenue"
                        color="chart-7"
                        :data="['Pro plan' => 18420, 'Team plan' => 12960, 'Add-on seats' => 6230, 'Support' => 3140, 'Training' => 1850]"
                        prefix="$"
                        compact
                    />
                </x-gui::card>

                <x-gui::card variant="filled" title="Payment methods" icon="credit-card">
                    <x-gui::chart.donut
                        :size="128"
                        :thickness="12"
                        :data="['Card' => 31200, 'PayPal' => 11400, 'Bank transfer' => 4100, 'Gift card' => 1500]"
                        :colors="['Card' => 'chart-7', 'PayPal' => 'chart-1', 'Bank transfer' => 'chart-4', 'Gift card' => 'chart-5']"
                        prefix="$"
                        compact
                    />
                </x-gui::card>

                <x-gui::card variant="filled" title="Coming up" icon="calendar">
                    <x-gui::timeline label="Payments coming up">
                        @foreach ($upcoming as $item)
                            <x-gui::timeline.item :title="$item['title']" :time="$item['time']" :icon="$item['icon']" color="secondary">{{ $item['detail'] }}</x-gui::timeline.item>
                        @endforeach
                    </x-gui::timeline>
                </x-gui::card>
            </div>
        </div>
    </x-example::app>
@endsection
