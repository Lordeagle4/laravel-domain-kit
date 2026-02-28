<?php

declare(strict_types=1);

namespace Awtechs\LaravelDomainKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Awtechs\LaravelDomainKit\Support\{DomainPathResolver, StubResolver};

final class MakeDomainControllerCommand extends Command
{
    protected $signature = 'make:domain:controller {domain} {name} {--r|resource} {--api} {--a|action}';
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

        if ($this->option('action') && !$this->option('resource')) {
            $this->error('The --action option requires --resource.');
            return self::FAILURE;
        }

        $contents = $this->option('resource')
            ? str_replace(
                ['{{ namespace }}', '{{ class }}', '{{ response_import }}', '{{ return_type }}', '{{ index_body }}', '{{ show_body }}', '{{ create_body }}', '{{ update_body }}', '{{ destroy_body }}'],
                [
                    $paths->namespace($domain, 'Controllers'),
                    $name,
                    $this->option('api') ? 'use Illuminate\Http\JsonResponse;' : 'use Illuminate\Http\Response;',
                    $this->option('api') ? 'JsonResponse' : 'Response',
                    $this->option('api') ? 'return response()->json([]);' : 'return response()->noContent();',
                    $this->option('api') ? "return response()->json(['id' => \$id]);" : 'return response()->noContent();',
                    $this->option('api') ? 'return response()->json([], 201);' : 'return response()->noContent();',
                    $this->option('api') ? 'return response()->json([]);' : 'return response()->noContent();',
                    $this->option('api') ? 'return response()->json(null, 204);' : 'return response()->noContent();',
                ],
                $stubs->resolve('controller-resource')
            )
            : str_replace(
                ['{{ namespace }}', '{{ class }}'],
                [$paths->namespace($domain, 'Controllers'), $name],
                $stubs->resolve('controller')
            );

        File::put($file, $contents);

        if ($this->option('resource') && $this->option('action')) {
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
