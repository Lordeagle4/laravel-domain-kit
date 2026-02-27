<?php

declare(strict_types=1);

namespace Awtechs\LaravelDomainKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Awtech\LaravelDomainKit\Support\{DomainPathResolver, StubResolver};

final class MakeDomainEventCommand extends Command
{
    protected $signature = 'make:domain:event {domain} {name}';
    protected $description = 'Create a domain event';

    public function handle(
        DomainPathResolver $paths,
        StubResolver $stubs
    ): int {
        $domain = ucfirst($this->argument('domain'));
        $name = ucfirst($this->argument('name'));

        $path = $paths->resolve($domain, 'Events');
        File::ensureDirectoryExists($path);

        $file = "{$path}/{$name}.php";

        if (File::exists($file)) {
            $this->error('Event already exists.');
            return self::FAILURE;
        }

        $stub = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$paths->namespace($domain, 'Events'), $name],
            $stubs->resolve('event')
        );

        File::put($file, $stub);

        $this->info("Event [{$name}] created.");
        return self::SUCCESS;
    }
}
