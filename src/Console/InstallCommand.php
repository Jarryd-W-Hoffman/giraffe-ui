<?php

namespace JayAitch\GiraffeUI\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gui:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install GiraffeUI into your application.';

    /**
     * The line added to the app's CSS to pull in GiraffeUI.
     */
    public const CSS_IMPORT = '@import "../../vendor/jayaitch/giraffeui/resources/css/giraffeui.css";';

    /**
     * The line added to the app's CSS so the layout's .dark class switches themes.
     */
    public const DARK_VARIANT = '@custom-variant dark (&:where(.dark, .dark *));';

    /**
     * Create a new command instance.
     */
    public function __construct(protected Filesystem $files)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * Every step checks what's already there first, so the command is safe to run again.
     */
    public function handle(): int
    {
        $this->components->info('Installing GiraffeUI.');

        $this->installCss();
        $this->installLivewire();
        $this->publishLayout();

        $this->callSilently('view:clear');

        $this->components->info('GiraffeUI is ready. Try <x-gui::button>Hello</x-gui::button> in any Blade view.');

        return self::SUCCESS;
    }

    /**
     * Import GiraffeUI's CSS straight after Tailwind in resources/css/app.css.
     */
    protected function installCss(): void
    {
        $path = $this->cssPath();

        if (! $this->files->exists($path)) {
            $this->components->warn("Couldn't find resources/css/app.css. Add this line to your main CSS file, after @import \"tailwindcss\":");
            $this->line('  '.self::CSS_IMPORT);

            return;
        }

        $css = $this->files->get($path);

        if (Str::contains($css, 'giraffeui.css')) {
            $this->components->twoColumnDetail('GiraffeUI CSS', '<fg=yellow;options=bold>ALREADY IMPORTED</>');

            return;
        }

        if (! preg_match('/^@import\s+[\'"]tailwindcss[\'"].*;[ \t]*$/m', $css, $matches, PREG_OFFSET_CAPTURE)) {
            $this->components->warn('GiraffeUI needs Tailwind CSS v4, but resources/css/app.css has no @import "tailwindcss". See https://tailwindcss.com/docs/installation/framework-guides/laravel');
            $this->line('  Once Tailwind is set up, add this line after @import "tailwindcss":');
            $this->line('  '.self::CSS_IMPORT);

            return;
        }

        // Insert on the line after Tailwind's import.
        $offset = $matches[0][1] + strlen($matches[0][0]);
        $this->files->put($path, substr_replace($css, "\n".self::CSS_IMPORT, $offset, 0));

        $this->components->twoColumnDetail('GiraffeUI CSS', '<fg=green;options=bold>IMPORTED</>');
    }

    /**
     * Offer to install Livewire. GiraffeUI works without it, so the default is no.
     */
    protected function installLivewire(): void
    {
        if (class_exists('Livewire\Livewire')) {
            $this->components->twoColumnDetail('Livewire', '<fg=yellow;options=bold>ALREADY INSTALLED</>');

            return;
        }

        if (! $this->confirm('Would you like to install Livewire? GiraffeUI works with or without it.', false)) {
            return;
        }

        $result = Process::path(base_path())
            ->timeout(300)
            ->run(['composer', 'require', 'livewire/livewire'], function (string $type, string $output) {
                $this->output->write($output);
            });

        if ($result->failed()) {
            $this->components->error('Livewire could not be installed. Run "composer require livewire/livewire" yourself to see why.');

            return;
        }

        $this->components->twoColumnDetail('Livewire', '<fg=green;options=bold>INSTALLED</>');
    }

    /**
     * Publish the app layout as an anonymous component, e.g. <x-layouts.app>.
     */
    protected function publishLayout(): void
    {
        if (! $this->confirm('Would you like to publish the GiraffeUI app layout (with dark mode)?', true)) {
            return;
        }

        $name = 'app';

        if ($this->files->exists($this->layoutPath($name))) {
            $choice = $this->choice(
                'A layout already exists at resources/views/components/layouts/app.blade.php. What would you like to do?',
                ['Install under a different name', 'Overwrite', 'Skip'],
                0
            );

            if ($choice === 'Skip') {
                return;
            }

            if ($choice === 'Install under a different name') {
                $name = Str::slug($this->ask('What should the layout be called?', 'gui'));

                if ($name === '' || $this->files->exists($this->layoutPath($name))) {
                    $this->components->error('That layout name is empty or already taken. Skipping the layout.');

                    return;
                }
            }
        }

        $this->files->ensureDirectoryExists(dirname($this->layoutPath($name)));
        $this->files->copy(__DIR__.'/../../stubs/layout.blade.php', $this->layoutPath($name));

        $this->components->twoColumnDetail("Layout <x-layouts.{$name}>", '<fg=green;options=bold>PUBLISHED</>');

        $this->ensureDarkVariant();
    }

    /**
     * Make Tailwind's dark: variant follow the .dark class the layout sets.
     */
    protected function ensureDarkVariant(): void
    {
        $path = $this->cssPath();

        if (! $this->files->exists($path)) {
            return;
        }

        $css = $this->files->get($path);

        if (Str::contains($css, '@custom-variant dark')) {
            return;
        }

        $this->files->put($path, rtrim($css)."\n\n".self::DARK_VARIANT."\n");

        $this->components->twoColumnDetail('Class-based dark mode', '<fg=green;options=bold>ENABLED</>');
    }

    /**
     * Get the path to the app's main CSS file.
     */
    protected function cssPath(): string
    {
        return base_path('resources/css/app.css');
    }

    /**
     * Get the path to a published layout.
     */
    protected function layoutPath(string $name): string
    {
        return base_path("resources/views/components/layouts/{$name}.blade.php");
    }
}
