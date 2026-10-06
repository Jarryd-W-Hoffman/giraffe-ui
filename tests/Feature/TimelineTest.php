<?php

it('is an ordered list of events, each with a time element', function () {
    $html = (string) $this->blade(<<<'BLADE'
        <x-gui::timeline label="Order history">
            <x-gui::timeline.item title="Order placed" time="5 Oct, 09:12" datetime="2026-10-05T09:12" icon="check" color="success" />
            <x-gui::timeline.item title="Out for delivery">Estimated by 6 PM.</x-gui::timeline.item>
        </x-gui::timeline>
        BLADE);

    expect($html)
        ->toMatch('/<ol\s+aria-label="Order history"/')
        ->toMatch('/<time\s+datetime="2026-10-05T09:12"/')
        ->toContain('5 Oct, 09:12</time>', 'Estimated by 6 PM.', 'bg-gui-success')
        ->and(substr_count($html, '<li '))->toBe(2)
        // An icon for the first, a dot for the second; both hidden from screen readers.
        ->and($html)->toContain('size-2.5 rounded-full');
});

it('formats a date object and fills in datetime', function () {
    $html = (string) $this->blade('<x-gui::timeline><x-gui::timeline.item title="Shipped" :time="new DateTimeImmutable(\'2026-10-06 14:40:00+00:00\')" format="j M" /></x-gui::timeline>');

    expect($html)->toContain('datetime="2026-10-06T14:40:00+00:00"', '>6 Oct</time>');
});

it('takes a custom marker and a title with markup', function () {
    $html = (string) $this->blade('<x-gui::timeline><x-gui::timeline.item><x-slot:marker><span class="my-marker"></span></x-slot:marker><x-slot:title><strong>Ada</strong> commented</x-slot:title></x-gui::timeline.item></x-gui::timeline>');

    expect($html)->toContain('my-marker', '<strong>Ada</strong> commented')->not->toContain('size-2.5 rounded-full');
});
