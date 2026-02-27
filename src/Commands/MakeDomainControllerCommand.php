<?php

declare(strict_types=1);

namespace Awtechs\LaravelDomainKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use LaravelDomainKit\Support\DomainPathResolver;
use LaravelDomainKit\Support\StubResolver;

final class MakeDomainControllerCommand extends Command
{
    protected $signature = 'make:domain:controller {domain} {name}';
    protected $description = 'Create a domain controller';

    public function handle(
        DomainPathResolver $paths,
        StubResolver $stubs
    ): int {
        $domain = ucfirst($this->argument('domain'));
        $name = ucfirst($this->argument('name'));

        $path = $paths->resolve($domain, 'Controllers');
        File::ensureDirectoryExists($path);

        $file = "{$path}/{$name}.php";

        if (File::exists($file)) {
            $this->error('Controller already exists.');
            return self::FAILURE;
        }

        File::put(
            $file,
            str_replace(
                ['{{ namespace }}', '{{ class }}'],
                [$paths->namespace($domain, 'Controllers'), $name],
                $stubs->resolve('controller')
            )
        );

        $this->info("Controller [{$name}] created.");
        return self::SUCCESS;
    }
}
