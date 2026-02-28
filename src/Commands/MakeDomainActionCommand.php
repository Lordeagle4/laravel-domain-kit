<?php

declare(strict_types=1);

namespace Awtechs\LaravelDomainKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Awtechs\LaravelDomainKit\Support\{DomainPathResolver, StubResolver};

final class MakeDomainActionCommand extends Command
{
    protected $signature = 'make:domain:action {domain} {name}';
    protected $description = 'Create a domain action';

    public function handle(
        DomainPathResolver $paths,
        StubResolver $stubs
    ): int {
        $domain = ucfirst($this->argument('domain'));
        $name = ucfirst($this->argument('name'));

        $path = $paths->resolve($domain, 'Actions');
        File::ensureDirectoryExists($path);

        $file = "{$path}/{$name}.php";

        if (File::exists($file)) {
            $this->error('Action already exists.');
            return self::FAILURE;
        }

        $stub = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$paths->namespace($domain, 'Actions'), $name],
            $stubs->resolve('action')
        );

        File::put($file, $stub);

        $this->info("Action [{$name}] created.");
        return self::SUCCESS;
    }
}
