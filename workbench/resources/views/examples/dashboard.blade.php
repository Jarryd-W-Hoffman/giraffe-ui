@extends('catalog::examples.layout')

@section('content')
    @php
        // Mock data for each period the Week / Month / Year switch can show.
        $periods = [
            'week' => [
                'name' => 'this week', 'compare' => 'last week',
                'revenue' => '$11,240', 'change' => '+6.1%', 'available' => '$7,320.00',
                'range' => ['6 October', '12 October'],
                'breakdown' => [['Invest', 3210, '+9%'], ['Products', 5980.4, '+4%'], ['Other', 1240.6, '-2%']],
                'flow' => [
                    'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    'income' => [1420, 1680, 1590, 1890, 2140, 1360, 1160],
                    'expenses' => [980, 1120, 1040, 1250, 1180, 760, 640],
                ],
                'stats' => [
                    ['Customers', '2,840', '1%', 'up', false, [2790, 2798, 2806, 2815, 2822, 2831, 2840]],
                    ['Orders', '286', '6%', 'up', false, [34, 41, 39, 46, 52, 40, 34]],
                    ['Average order', '$39.30', '1%', 'down', false, [40.1, 39.8, 39.6, 39.9, 39.2, 39.4, 39.3]],
                    ['Refund rate', '1.6%', '0.2 pts', 'down', true, [1.9, 1.8, 1.8, 1.7, 1.7, 1.6, 1.6]],
                ],
            ],
            'month' => [
                'name' => 'this month', 'compare' => 'last month',
                'revenue' => '$48,200', 'change' => '+12.4%', 'available' => '$31,450.00',
                'range' => ['1 October', '31 October'],
                'breakdown' => [['Invest', 14465, '+23%'], ['Products', 22458.7, '+12%'], ['Other', 6034.3, '+1.2%']],
                'flow' => [
                    'labels' => ['1 Mar', '15 Mar', '1 Apr', '15 Apr', '1 May', '15 May', '1 Jun', '15 Jun', '1 Jul', '15 Jul', '1 Aug', '15 Aug'],
                    'income' => [5200, 5900, 7400, 7968, 6100, 4800, 5600, 5957, 4700, 5300, 6800, 6200],
                    'expenses' => [3900, 4300, 3600, 4800, 4200, 3700, 4500, 4100, 3800, 4600, 4000, 4400],
                ],
                'stats' => [
                    ['Customers', '2,840', '8%', 'up', false, [2210, 2290, 2350, 2400, 2460, 2530, 2590, 2650, 2700, 2760, 2800, 2840]],
                    ['Orders', '1,204', '4%', 'up', false, [980, 1010, 995, 1060, 1040, 1100, 1090, 1120, 1150, 1130, 1160, 1204]],
                    ['Average order', '$40.03', '2%', 'down', false, [42.1, 41.8, 42.4, 41.2, 40.9, 41.5, 40.6, 40.8, 40.2, 40.5, 40.1, 40.03]],
                    ['Refund rate', '1.8%', '0.3 pts', 'down', true, [2.6, 2.4, 2.5, 2.2, 2.3, 2.1, 2.2, 1.9, 2.0, 1.9, 2.1, 1.8]],
                ],
            ],
            'year' => [
                'name' => 'this year', 'compare' => 'last year',
                'revenue' => '$512,900', 'change' => '+31.2%', 'available' => '$214,800.00',
                'range' => ['January', 'December'],
                'breakdown' => [['Invest', 148200, '+41%'], ['Products', 236900, '+27%'], ['Other', 61400, '+8%']],
                'flow' => [
                    'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    'income' => [31200, 33800, 36400, 35900, 39800, 41200, 40100, 43900, 45600, 48200, 52400, 64400],
                    'expenses' => [24100, 25200, 26800, 27400, 28100, 29900, 30400, 31800, 32100, 33600, 35200, 41800],
                ],
                'stats' => [
                    ['Customers', '2,840', '64%', 'up', false, [1730, 1840, 1960, 2050, 2140, 2230, 2350, 2470, 2580, 2690, 2770, 2840]],
                    ['Orders', '12,980', '38%', 'up', false, [820, 880, 940, 920, 1010, 1060, 1040, 1120, 1150, 1204, 1290, 1540]],
                    ['Average order', '$39.52', '5%', 'up', false, [37.2, 37.6, 38.1, 38.4, 38.9, 39.0, 39.3, 39.2, 39.6, 40.03, 39.8, 39.52]],
                    ['Refund rate', '2.1%', '0.6 pts', 'down', true, [2.9, 2.8, 2.7, 2.6, 2.5, 2.4, 2.3, 2.2, 2.0, 1.8, 1.9, 2.1]],
                ],
            ],
        ];
        $styles = ['solid', 'stripes', 'faint'];

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
        {{-- Week, Month or Year: every period's numbers are on the page; the switch shows one. --}}
        <div class="space-y-8" x-data="{ period: 'month' }">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <x-gui::breadcrumbs :items="['Workspace' => '/catalog/examples/dashboard', 'Overview' => null]" />
                    <h1 class="mt-3 text-4xl font-normal tracking-tight sm:text-5xl">Welcome back, <span class="text-zinc-500 dark:text-zinc-400">Ada</span></h1>
                    <p class="mt-3 text-zinc-600 dark:text-zinc-400">Here's how Acme is doing.</p>
                </div>
                <div style="--radius-gui: 9999px">
                    <x-gui::segmented-control name="period" label="Period" size="sm" x-model="period" :options="['week' => 'Week', 'month' => 'Month', 'year' => 'Year']" value="month" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-10 rounded-3xl bg-zinc-100 p-6 sm:p-8 2xl:grid-cols-[minmax(0,20rem)_minmax(0,1fr)] 2xl:gap-8 dark:bg-zinc-900">
                {{-- The headline: total revenue, with the cents quieter than the dollars. --}}
                <section aria-labelledby="revenue-heading" class="space-y-6">
                    @foreach ($periods as $key => $period)
                        <div class="space-y-6" x-show="period === '{{ $key }}'" @if ($key !== 'month') x-cloak @endif>
                            <div>
                                <h2 @if ($key === 'month') id="revenue-heading" @endif class="text-base text-zinc-600 dark:text-zinc-400">Total revenue {{ $period['name'] }}</h2>
                                <p class="mt-2 flex flex-wrap items-baseline gap-x-3">
                                    <span class="text-5xl font-semibold tracking-tight tabular-nums">{{ $period['revenue'] }}<span class="text-zinc-500">.00</span></span>
                                    <span class="text-sm font-medium text-gui-success-text"><span class="sr-only">Up </span>{{ $period['change'] }}<span class="sr-only"> on {{ $period['compare'] }}</span></span>
                                </p>
                            </div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Available to spend: <span class="font-semibold text-zinc-900 tabular-nums dark:text-zinc-100">{{ $period['available'] }}</span></p>
                        </div>
                    @endforeach
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
                    <h2 id="breakdown-heading" class="sr-only">Spending by category</h2>
                    @foreach ($periods as $key => $period)
                        @php
                            $values = array_column($period['breakdown'], 1);
                            $max = max($values);
                            $total = array_sum($values);
                        @endphp
                        <div class="relative" x-show="period === '{{ $key }}'" @if ($key !== 'month') x-cloak @endif>
                            {{-- The average, dashed across every part. --}}
                            <div class="pointer-events-none absolute inset-x-0 top-[42%] border-t border-dashed border-zinc-300 dark:border-zinc-700" aria-hidden="true">
                                <span class="absolute end-[12%] -translate-y-1/2 rounded-full bg-zinc-200 px-2.5 py-0.5 text-xs text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">Average</span>
                            </div>
                            <dl class="flex h-52 gap-4">
                                @foreach ($period['breakdown'] as $i => [$label, $value, $change])
                                    @php($style = $styles[$i])
                                    {{-- Equal columns on phones, so every amount fits; sized by share from md up. --}}
                                    <div class="flex min-w-0 flex-1 flex-col border-s border-zinc-300 ps-3 md:flex-(--share) md:ps-4 dark:border-zinc-800" style="--share: {{ round($value / $total * 100, 1) }} 1 0%">
                                        <dd class="text-base tabular-nums md:text-xl" style="margin-top: {{ round(48 - $value / $max * 44) }}px">${{ number_format($value, 2) }}</dd>
                                        <dt class="mt-auto mb-2 truncate text-sm">
                                            <span class="font-medium">{{ $change }}</span>
                                            <span class="text-zinc-600 dark:text-zinc-400">{{ $label }}</span>
                                        </dt>
                                        <div class="h-12" aria-hidden="true">
                                            @if ($style === 'solid')
                                                <div class="h-full rounded-xl bg-violet-600"></div>
                                            @else
                                                {{-- Thin rounded stripes, fainter for the smaller part. --}}
                                                <div class="flex h-full justify-between gap-1 overflow-hidden">
                                                    @foreach (range(1, $style === 'stripes' ? 22 : 8) as $stripe)
                                                        <span @class(['w-1.5 shrink-0 rounded-full', 'bg-violet-500' => $style === 'stripes', 'bg-violet-500/35' => $style === 'faint'])></span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </dl>
                            <div class="mt-3 flex justify-between text-sm text-zinc-600 dark:text-zinc-400">
                                <span>{{ $period['range'][0] }}</span>
                                <span>{{ $period['range'][1] }}</span>
                            </div>
                        </div>
                    @endforeach
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
                    @foreach ($periods as $key => $period)
                        <div x-show="period === '{{ $key }}'" @if ($key !== 'month') x-cloak @endif>
                            <x-gui::chart.line
                                :labels="$period['flow']['labels']"
                                :series="['Income' => $period['flow']['income'], 'Expenses' => $period['flow']['expenses']]"
                                :colors="['Income' => 'chart-1', 'Expenses' => 'muted']"
                                :dashed="['Expenses']"
                                :axis="false"
                                :height="180"
                                :label="'Cash flow '.$period['name']"
                                prefix="$"
                                compact
                                curve
                                area
                            />
                        </div>
                    @endforeach
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

            {{-- The period at a glance: four stats, each with its trend. --}}
            @foreach ($periods as $key => $period)
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4" x-show="period === '{{ $key }}'" @if ($key !== 'month') x-cloak @endif>
                    @foreach ($period['stats'] as [$label, $value, $change, $trend, $invert, $data])
                        <x-gui::card variant="filled" class="flex flex-col justify-between gap-6">
                            <x-gui::stat :label="$label" :value="$value" :change="$change" :trend="$trend" :invert="$invert" :description="'vs '.$period['compare']" />
                            <x-gui::sparkline :data="$data" color="chart-1" area class="h-12 w-full" />
                        </x-gui::card>
                    @endforeach
                </div>
            @endforeach

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
