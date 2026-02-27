<?php

declare(strict_types=1);

namespace Awtechs\LaravelDomainKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Awtech\LaravelDomainKit\Support\{DomainPathResolver, StubResolver};

final class MakeDomainListenerCommand extends Command
{
    protected $signature = 'make:domain:listener {domain} {name}';
    protected $description = 'Create a domain listener';

    public function handle(
        DomainPathResolver $paths,
        StubResolver $stubs
    ): int {
        $domain = ucfirst($this->argument('domain'));
        $name = ucfirst($this->argument('name'));

        $path = $paths->resolve($domain, 'Listeners');
        File::ensureDirectoryExists($path);

        $file = "{$path}/{$name}.php";

        if (File::exists($file)) {
            $this->error('Listener already exists.');
            return self::FAILURE;
        }

        $stub = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$paths->namespace($domain, 'Listeners'), $name],
            $stubs->resolve('listener')
        );

        File::put($file, $stub);

        $this->info("Listener [{$name}] created.");
        return self::SUCCESS;
    }
}
