<?php

declare(strict_types=1);

namespace Awtech\LaravelDomainKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Awtech\LaravelDomainKit\Support\{DomainPathResolver, StubResolver};

class MakeDomainJobCommand extends Command
{
    protected $signature = 'make:domain:job {domain} {name}';
    protected $description = 'Create a domain job';

    public function handle(
        DomainPathResolver $paths,
        StubResolver $stubs
    ): int {
        $domain = ucfirst($this->argument('domain'));
        $name = ucfirst($this->argument('name'));

        $path = $paths->resolve($domain, 'Jobs');
        File::ensureDirectoryExists($path);

        $file = "{$path}/{$name}.php";

        if (File::exists($file)) {
            $this->error('Job already exists.');
            return self::FAILURE;
        }

        $stub = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$paths->namespace($domain, 'Jobs'), $name],
            $stubs->resolve('job')
        );

        File::put($file, $stub);

        $this->info("Job [{$name}] created.");
        return self::SUCCESS;
    }
}