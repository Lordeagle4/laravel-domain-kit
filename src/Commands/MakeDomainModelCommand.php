<?php

declare(strict_types=1);

namespace Awtech\LaravelDomainKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Awtech\LaravelDomainKit\Support\{DomainPathResolver, StubResolver};

final class MakeDomainModelCommand extends Command
{
    protected $signature = 'make:domain:model {domain} {name}';
    protected $description = 'Create a domain model';

    public function handle(
        DomainPathResolver $paths,
        StubResolver $stubs
    ): int {
        $domain = ucfirst($this->argument('domain'));
        $name = ucfirst($this->argument('name'));

        $path = $paths->resolve($domain, 'Models');
        File::ensureDirectoryExists($path);

        $file = "{$path}/{$name}.php";

        if (File::exists($file)) {
            $this->error('Model already exists.');
            return self::FAILURE;
        }

        File::put(
            $file,
            str_replace(
                ['{{ namespace }}', '{{ class }}'],
                [$paths->namespace($domain, 'Models'), $name],
                $stubs->resolve('model')
            )
        );

        $this->info("Model [{$name}] created.");
        return self::SUCCESS;
    }
}
