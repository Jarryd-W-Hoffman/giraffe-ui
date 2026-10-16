<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Vite;

// Apps with a nonce-based Content Security Policy block inline scripts without the nonce, so every script GiraffeUI
// writes (in components and in the published layout) has to carry it.

it('gives every inline script the CSP nonce', function () {
    $files = collect(File::allFiles(__DIR__.'/../../resources/views'))
        ->push(new SplFileInfo(__DIR__.'/../../stubs/layout.blade.php'))
        ->filter(fn (SplFileInfo $file) => str_ends_with($file->getFilename(), '.blade.php'));

    $missing = $files->flatMap(function (SplFileInfo $file) {
        preg_match_all('/<script\b[^>]*>/', File::get($file->getPathname()), $matches);

        return collect($matches[0])
            // JSON data islands never run, so CSP doesn't apply to them.
            ->reject(fn (string $tag) => str_contains($tag, 'type="application/json"'))
            ->reject(fn (string $tag) => str_contains($tag, 'Vite::cspNonce()'))
            ->map(fn () => $file->getFilename());
    });

    expect($missing->all())->toBe([]);
});

it('renders the nonce on the scripts that run before paint', function (string $blade) {
    Vite::useCspNonce('gui-test-nonce');

    expect((string) $this->blade($blade))->toMatch('/<script\s+nonce="gui-test-nonce"/');
})->with([
    'banner' => '<x-gui::banner dismissible remember="trial">Trial ends soon.</x-gui::banner>',
    'sidebar layout' => '<x-gui::sidebar-layout collapsible><x-slot:sidebar>Nav</x-slot:sidebar> Page</x-gui::sidebar-layout>',
    'modal' => '<x-gui::modal id="welcome" open>Hi</x-gui::modal>',
    'toasts' => '<x-gui::toasts />',
]);

it('leaves the nonce out when the app has none', function () {
    expect((string) $this->blade('<x-gui::banner dismissible remember="trial">Hi</x-gui::banner>'))->not->toContain('nonce=');
});
