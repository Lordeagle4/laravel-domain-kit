<?php

declare(strict_types=1);

namespace Awtechs\LaravelDomainKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Awtechs\LaravelDomainKit\Support\{DomainPathResolver, StubResolver};

final class MakeDomainControllerCommand extends Command
{
    protected $signature = 'make:domain:controller {domain} {name} {--r|resource} {--api} {--a|action} {--aa} {--ra}';
    protected $description = 'Create a domain controller';

    public function handle(
        DomainPathResolver $paths,
        StubResolver $stubs
    ): int {
        $domain = ucfirst($this->argument('domain'));
        $name = ucfirst($this->argument('name'));
        $api = (bool) ($this->option('api') || $this->option('aa'));
        $withActions = (bool) ($this->option('action') || $this->option('ra') || $this->option('aa'));
        $resource = (bool) ($this->option('resource') || $this->option('ra') || $this->option('aa') || $api);

        $path = $paths->resolve($domain, 'Controllers');
        File::ensureDirectoryExists($path);

        $file = "{$path}/{$name}.php";

        if (File::exists($file)) {
            $this->error('Controller already exists.');
            return self::FAILURE;
        }

        if ($withActions && !$resource) {
            $this->error('The --action option requires --resource.');
            return self::FAILURE;
        }

        $contents = $resource
            ? str_replace(
                ['{{ namespace }}', '{{ class }}', '{{ store_body }}', '{{ update_body }}', '{{ destroy_body }}'],
                [
                    $paths->namespace($domain, 'Controllers'),
                    $name,
                    $api ? 'return response()->json([], 201);' : 'return response()->noContent();',
                    $api ? 'return response()->json([]);' : 'return response()->noContent();',
                    $api ? 'return response()->json(null, 204);' : 'return response()->noContent();',
                ],
                $stubs->resolve($api ? 'controller-resource-api' : 'controller-resource')
            )
            : str_replace(
                ['{{ namespace }}', '{{ class }}'],
                [$paths->namespace($domain, 'Controllers'), $name],
                $stubs->resolve('controller')
            );

        File::put($file, $contents);

        if ($resource && $withActions) {
            $controllerBase = $this->controllerBase($name);
            foreach (['Create', 'Update', 'Destroy'] as $verb) {
                $result = $this->call('make:domain:action', [
                    'domain' => $domain,
                    'name' => $verb . $controllerBase,
                ]);

                if ($result !== self::SUCCESS) {
                    return self::FAILURE;
                }
            }
        }

        $this->info("Controller [{$name}] created.");
        return self::SUCCESS;
    }

    private function controllerBase(string $controllerName): string
    {
        $base = preg_replace('/Controller$/', '', $controllerName) ?? $controllerName;
        return $base === '' ? $controllerName : $base;
    }
}
