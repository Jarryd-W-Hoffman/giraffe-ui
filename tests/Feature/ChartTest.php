<?php

use JayAitch\GiraffeUI\Support\Chart;

it('rounds the axis to friendly ticks that hug the data', function () {
    expect(Chart::ticks([18200, 42600], 0.0))->toBe(['min' => 0.0, 'max' => 50000.0, 'ticks' => [0.0, 10000.0, 20000.0, 30000.0, 40000.0, 50000.0]])
        ->and(Chart::ticks([99.91, 99.99])['ticks'])->toBe([99.9, 99.92, 99.94, 99.96, 99.98, 100.0])
        ->and(Chart::ticks([-6900, 15300], 0.0)['min'])->toBeLessThan(-6900.0)
        ->and(Chart::ticks([5, 5])['max'])->toBeGreaterThan(5.0)
        ->and(Chart::ticks([])['ticks'])->toBe([0.0, 1.0]);
});

it('gives short charts fewer ticks, and the ticks matching decimals', function () {
    expect(Chart::tickCount(80))->toBe(3)
        ->and(Chart::tickCount(240))->toBe(5)
        ->and(Chart::tickCount(800))->toBe(6)
        ->and(Chart::decimals([99.9, 99.92, 100]))->toBe(2)
        ->and(Chart::decimals([0, 10000, 20000]))->toBe(0);
});

it('formats numbers for people', function () {
    expect(Chart::format(1234.5))->toBe('1,234.5')
        ->and(Chart::format(12500, '$', '', null, true))->toBe('$12.5K')
        ->and(Chart::format(310000, compact: true))->toBe('310K')
        ->and(Chart::format(1200000, compact: true))->toBe('1.2M')
        ->and(Chart::format(-3200, '$', '', null, true))->toBe('-$3.2K')
        ->and(Chart::format(99.95, suffix: '%'))->toBe('99.95%')
        ->and(Chart::format(null))->toBe('–');
});

it('breaks lines at missing values, and closes areas and bands', function () {
    expect(Chart::line([1, 2, null, 3, 4], 0, 4))->toBe('M0,750L250,500M750,250L1000,0')
        ->and(Chart::area([1, 2], 0, 4))->toBe('M0,750L1000,500L1000,1000L0,1000Z')
        ->and(Chart::band([3, 4], [1, 2], 0, 4))->toBe('M0,250L1000,0L1000,500L0,750Z');
});

it('curves through every point without overshooting', function () {
    $path = Chart::line([1, 3, 2, 4], 0, 4, true);

    // Flat tangents at the peak (y 250) and the valley (y 500): the curve's control points stay level with them.
    expect($path)->toStartWith('M0,750C')
        ->toContain(' 333.33,250C444.44,250 ', ' 666.67,500C777.78,500 ')
        ->toEndWith(' 1000,0');
});

it('draws a line chart with real text, a keyboard crosshair and a data table', function () {
    $html = (string) $this->blade('<x-gui::chart.line :labels="[\'Jan\', \'Feb\', \'Mar\']" :series="[\'Online\' => [12000, 14000, 13000], \'In store\' => [5800, null, 6600]]" prefix="$" compact />');

    expect($html)
        ->toContain('role="group"', 'aria-roledescription="chart"', 'tabindex="0"', 'aria-label="Online &amp; In store from Jan to Mar"')
        ->toContain('aria-live="polite"', '<caption class="sr-only">')->not->toContain('Show the data')
        // The legend for two series, the colours in order, and exact numbers in the table.
        ->toContain('var(--color-gui-chart-1)', 'var(--color-gui-chart-2)', '$12,000', '$15K')
        ->and(substr_count($html, '<tr '))->toBe(4)
        // The gap breaks the second line into two moves.
        ->and($html)->toMatch('/d="M0,[\d.]+M1000,[\d.]+"/');
});

it('stacks, highlights, and draws a reference line', function () {
    $stacked = (string) $this->blade('<x-gui::chart.area :labels="[\'A\', \'B\']" :series="[\'Free\' => [1, 2], \'Pro\' => [1, 1]]" />');
    $highlighted = (string) $this->blade('<x-gui::chart.line highlight="Acme" :labels="[\'A\', \'B\']" :series="[\'Acme\' => [1, 2], \'Globex\' => [2, 1]]" :reference="3" reference-label="Target" />');

    expect($stacked)->toContain('fill-opacity="0.25"')->toMatch('/<path d="M[^"]+Z" fill="currentColor"/')
        ->and($highlighted)->toContain('var(--gui-chart-muted)', 'stroke-dasharray="4 4"', 'Target · 3')
        // The grey line is drawn first, so the highlighted one sits on top.
        ->and(strpos($highlighted, 'color: var(--gui-chart-muted)'))->toBeLessThan(strpos($highlighted, 'color: var(--color-gui-chart-1)'));
});

it('pins a series to its colour', function () {
    expect((string) $this->blade('<x-gui::chart.line :labels="[\'A\', \'B\']" :series="[\'Costs\' => [1, 2]]" :colors="[\'Costs\' => \'chart-4\']" />'))
        ->toContain('var(--color-gui-chart-4)')->not->toContain('var(--color-gui-chart-1)');
});

it('says when there is no data', function () {
    foreach (['<x-gui::chart.line :labels="[]" :data="[]" />', '<x-gui::chart.bar :labels="[\'A\']" :data="[null]" />', '<x-gui::chart.donut :data="[]" />'] as $chart) {
        expect((string) $this->blade($chart))->toContain('No data yet.')->not->toContain('role="group"');
    }
});

it('draws bars grouped, stacked, horizontal and negative', function () {
    $grouped = (string) $this->blade('<x-gui::chart.bar :labels="[\'Q1\', \'Q2\']" :series="[\'2025\' => [4, 5], \'2026\' => [6, 7]]" />');
    $stacked = (string) $this->blade('<x-gui::chart.bar stacked :values="true" :labels="[\'Jan\']" :series="[\'Free\' => [3], \'Pro\' => [2]]" />');
    $horizontal = (string) $this->blade('<x-gui::chart.bar horizontal name="Visitors" :labels="[\'Search\', \'Direct\']" :data="[48200, 31900]" compact />');
    $negative = (string) $this->blade('<x-gui::chart.bar name="Cash" :labels="[\'Jan\', \'Feb\']" :data="[10, -5]" />');

    expect($grouped)->toContain('size-2.5 rounded-[2px]', 'var(--color-gui-chart-2)')->not->toContain('left-1/2 -translate-x-1/2 font-medium')
        // Stacked segments are separated by the surface colour, and only the outermost is rounded; the total sits on top.
        ->and($stacked)->toContain('border-y border-(--gui-chart-surface)', '>5</span>')
        ->and(substr_count($stacked, 'rounded-t'))->toBe(1)
        ->and($horizontal)->toContain('48.2K', '31.9K', 'Search')
        ->and($negative)->toContain('rounded-b', '-5');
});

it('draws a donut whose list is its legend and its numbers', function () {
    $html = (string) $this->blade('<x-gui::chart.donut :data="[\'Card\' => 75, \'PayPal\' => 24.5, \'Cash\' => 0.5, \'Refunds\' => -3]" prefix="$" />');

    expect($html)
        ->toContain('aria-label="Card 75%, PayPal 25%, Cash &lt;1%"', '$75, 75% of the total', 'pathLength="100"', 'Total', '$100')
        ->not->toContain('Refunds');
});

it('folds a donut past eight parts into Other', function () {
    $html = (string) $this->blade('<x-gui::chart.donut :data="array_combine(range(\'a\', \'j\'), range(10, 1))" />');

    expect(substr_count($html, '<circle'))->toBe(8)
        ->and($html)->toContain('Other')->not->toContain('var(--color-gui-chart-9)');
});

it('draws a sparkline with a spoken summary', function () {
    $html = (string) $this->blade('<x-gui::sparkline :data="[2.6, 2.4, 1.7, 1.8]" suffix="%" />');

    expect($html)->toContain('role="img"', 'aria-label="Trend from 2.6% to 1.8%, high 2.6%, low 1.7%"', 'h-8 w-24')
        ->and((string) $this->blade('<x-gui::sparkline :data="[1, 2]" class="h-10 w-full" />'))->not->toContain('h-8', 'w-24');
});

it('ranks a bar list largest first, with links and shares', function () {
    $html = (string) $this->blade('<x-gui::chart.bar-list label="Top pages" :data="[\'/a\' => 10, \'/b\' => [\'value\' => 40, \'href\' => \'/b\'], \'/c\' => 50]" :limit="2" percent />');

    expect($html)->toMatch('/<ul\s+aria-label="Top pages"/')
        ->toContain('href="/b"', 'width: max(100%, 2px)', 'width: max(80%, 2px)', '50%', '40%')
        ->not->toContain('>/a<')
        ->and(strpos($html, '/c'))->toBeLessThan(strpos($html, '/b'));
});

it('plots a scatter chart with a shape per series and a table', function () {
    $html = (string) $this->blade('<x-gui::chart.scatter x-label="Spend" y-label="Signups" :series="[\'A\' => [[1, 2], [3, 4]], \'B\' => [[\'x\' => 2, \'y\' => 3, \'label\' => \'Podcast\']]]" />');

    expect($html)
        ->toContain('aria-label="A &amp; B: Signups by Spend"', 'rounded-full', 'rounded-[2px]', 'Podcast', 'Spend', 'Signups', 'Series')
        ->and(substr_count($html, 'ring-(--gui-chart-surface)'))->toBe(3)
        ->and(substr_count($html, '<tr '))->toBe(4);
});

it('shades a heatmap in five steps of one hue, as a table', function () {
    $html = (string) $this->blade('<x-gui::chart.heatmap label="Orders" :rows="[\'Mon\', \'Tue\']" :columns="[\'09\', \'10\']" :data="[[0, 5], [null, 10]]" />');

    expect($html)
        ->toContain('<caption class="sr-only">Orders</caption>', 'scope="row"', 'scope="col"', 'color-mix(in oklab, var(--color-gui-chart-1) 56%, var(--gui-chart-surface))', 'color-mix(in oklab, var(--color-gui-chart-1) 100%')
        // Zero and missing values are empty cells; every cell's value is in the table for screen readers.
        ->and(substr_count($html, 'bg-zinc-100 dark:bg-zinc-800'))->toBe(3)
        ->and($html)->toContain('<span class="sr-only">10</span>', '<span class="sr-only">–</span>');
});

it('draws a dashed, muted comparison behind the main line, and can drop the axis', function () {
    $html = (string) $this->blade('<x-gui::chart.line :labels="[\'A\', \'B\']" :series="[\'Now\' => [1, 2], \'Before\' => [2, 1]]" :colors="[\'Before\' => \'muted\']" :dashed="[\'Before\']" :axis="false" area />');

    expect($html)->toContain('stroke-dasharray="6 5"', 'var(--gui-chart-muted)', 'border-dashed', '<linearGradient', 'fill="url(#gui-chart-fill')
        ->and(strpos($html, 'stroke-dasharray="6 5"'))->toBeLessThan(strpos($html, 'color: var(--color-gui-chart-1)'))
        // No y axis or gridlines, but the data table is still there.
        ->and(substr_count($html, 'class="relative shrink-0 text-right tabular-nums hidden"'))->toBe(1)
        ->and($html)->toContain('<caption class="sr-only">');
});

it('makes navbar links pills', function () {
    expect((string) $this->blade('<x-gui::navbar variant="pills"><x-gui::navbar.item href="/x">X</x-gui::navbar.item></x-gui::navbar>'))
        ->toContain('data-variant="pills"', 'group-data-[variant=pills]/navbar:rounded-full');
});

it('keeps the data table for screen readers, and shows it with table', function () {
    $hidden = (string) $this->blade('<x-gui::chart.bar :labels="[\'A\', \'B\']" :data="[1, 2]" />');
    $shown = (string) $this->blade('<x-gui::chart.bar :labels="[\'A\', \'B\']" :data="[1, 2]" table />');
    $area = (string) $this->blade('<x-gui::chart.area :labels="[\'A\', \'B\']" :data="[1, 2]" table />');

    expect($hidden)->toContain('<div class="sr-only">', '<table')->not->toContain('<details', 'Show the data')
        ->and($shown)->toContain('<details', 'Show the data', '<table')
        ->and($area)->toContain('Show the data');
});
