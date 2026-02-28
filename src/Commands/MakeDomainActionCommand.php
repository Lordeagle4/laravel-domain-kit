<?php

declare(strict_types=1);

namespace Awtechs\LaravelDomainKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
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
        $name = (string) $this->argument('name');
        $style = (string) config('domain-kit.controller_actions.style', 'flat');

        $target = $this->resolveTarget($domain, $name, $style, $paths);
        File::ensureDirectoryExists($target['path']);
        $file = "{$target['path']}/{$target['class']}.php";

        if (File::exists($file)) {
            $this->error('Action already exists.');
            return self::FAILURE;
        }

        $model = $this->ensureModel($domain, $target['entity']);
        $modelImport = $model === null ? '' : "use App\\Domains\\{$domain}\\Models\\{$model};";

        $stub = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ model_import }}'],
            [$target['namespace'], $target['class'], $modelImport],
            $stubs->resolve('action')
        );

        File::put($file, $stub);

        $this->wireControllerAction(
            $domain,
            $target['controller'],
            $target['verb'],
            $target['fqcn'],
            $target['class']
        );

        $this->info("Action [{$target['class']}] created.");
        return self::SUCCESS;
    }

    /**
     * @return array{
     *   path:string,
     *   namespace:string,
     *   class:string,
     *   fqcn:string,
     *   controller:?string,
     *   verb:?string,
     *   entity:?string
     * }
     */
    private function resolveTarget(
        string $domain,
        string $name,
        string $style,
        DomainPathResolver $paths
    ): array {
        $basePath = $paths->resolve($domain, 'Actions');
        $baseNamespace = $paths->namespace($domain, 'Actions');
        $normalized = trim(str_replace('\\', '/', $name), '/');

        $segments = array_values(array_filter(explode('/', $normalized), static fn (string $part): bool => $part !== ''));
        $segments = array_map(static fn (string $part): string => Str::studly($part), $segments);

        if ($segments === []) {
            $segments = ['Action'];
        }

        $class = array_pop($segments);
        $subdirs = $segments;
        $verb = null;
        $controller = null;
        $entity = null;

        if ($style === 'nested') {
            if ($subdirs === []) {
                if (preg_match('/^(Create|Update|Destroy)(.+)$/', $class, $matches) === 1) {
                    $verb = Str::lower($matches[1]);
                    $entity = Str::studly($matches[2]);
                    $controller = $entity;
                    $subdirs = [$controller];
                    $class = Str::studly($matches[1]);
                }
            } else {
                $controller = Str::studly((string) end($subdirs));
                $entity = $controller;
                if (preg_match('/^(Create|Update|Destroy)$/', $class, $matches) === 1) {
                    $verb = Str::lower($matches[1]);
                }
            }
        } else {
            if (preg_match('/^(Create|Update|Destroy)(.+)$/', $class, $matches) === 1) {
                $verb = Str::lower($matches[1]);
                $entity = Str::studly($matches[2]);
                $controller = $entity;
                $class = Str::studly($matches[1]) . $entity;
            }
        }

        $path = $subdirs === [] ? $basePath : $basePath . '/' . implode('/', $subdirs);
        $namespace = $subdirs === [] ? $baseNamespace : $baseNamespace . '\\' . implode('\\', $subdirs);

        return [
            'path' => $path,
            'namespace' => $namespace,
            'class' => $class,
            'fqcn' => "{$namespace}\\{$class}",
            'controller' => $controller,
            'verb' => $verb,
            'entity' => $entity,
        ];
    }

    private function ensureModel(string $domain, ?string $entity): ?string
    {
        if ($entity === null || $entity === '') {
            return null;
        }

        $model = Str::studly($entity);
        $modelFile = app_path("Domains/{$domain}/Models/{$model}.php");

        if (File::exists($modelFile)) {
            return $model;
        }

        if ($this->confirm("Model [{$model}] not found. Create it?", false) !== true) {
            return null;
        }

        $result = $this->call('make:domain:model', [
            'domain' => $domain,
            'name' => $model,
        ]);

        return $result === self::SUCCESS && File::exists($modelFile) ? $model : null;
    }

    private function wireControllerAction(
        string $domain,
        ?string $controller,
        ?string $verb,
        string $actionFqcn,
        string $actionClass
    ): void {
        if ($controller === null || $verb === null) {
            return;
        }

        $controllerFile = app_path("Domains/{$domain}/Controllers/{$controller}Controller.php");
        if (!File::exists($controllerFile)) {
            return;
        }

        $method = match ($verb) {
            'create' => 'store',
            'update' => 'update',
            'destroy' => 'destroy',
            default => null,
        };

        if ($method === null) {
            return;
        }

        $contents = File::get($controllerFile);
        $contents = $this->ensureUseImport($contents, $actionFqcn);
        $contents = $this->ensureMethodTypeHint(
            $contents,
            $method,
            $actionClass,
            '$' . Str::camel($actionClass) . 'Action'
        );

        File::put($controllerFile, $contents);
    }

    private function ensureUseImport(string $contents, string $fqcn): string
    {
        $useLine = "use {$fqcn};";
        if (str_contains($contents, $useLine)) {
            return $contents;
        }

        if (preg_match_all('/^use\s+[^\n]+;$/m', $contents, $matches, PREG_OFFSET_CAPTURE) > 0) {
            $last = end($matches[0]);
            $pos = $last[1] + strlen($last[0]);
            return substr($contents, 0, $pos) . "\n{$useLine}" . substr($contents, $pos);
        }

        if (preg_match('/^namespace[^\n]+;\n/m', $contents, $match, PREG_OFFSET_CAPTURE) === 1) {
            $pos = $match[0][1] + strlen($match[0][0]);
            return substr($contents, 0, $pos) . "\n{$useLine}\n" . substr($contents, $pos);
        }

        return $contents;
    }

    private function ensureMethodTypeHint(
        string $contents,
        string $method,
        string $actionClass,
        string $variable
    ): string {
        $pattern = '/public function ' . preg_quote($method, '/') . '\(([^)]*)\)/';

        return (string) preg_replace_callback(
            $pattern,
            static function (array $matches) use ($actionClass, $variable): string {
                $args = trim($matches[1]);
                $inject = "{$actionClass} {$variable}";

                if ($args !== '' && str_contains($args, $actionClass . ' ')) {
                    return $matches[0];
                }

                if ($args === '') {
                    return str_replace($matches[1], $inject, $matches[0]);
                }

                return str_replace($matches[1], "{$inject}, {$args}", $matches[0]);
            },
            $contents,
            1
        );
    }
}
