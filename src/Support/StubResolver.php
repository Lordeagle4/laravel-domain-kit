<?php

declare(strict_types=1);

namespace Awtech\LaravelDomainKit\Support;

use Illuminate\Support\Facades\File;
use RuntimeException;

final class StubResolver
{
    public function resolve(string $stub): string
    {
        $custom = base_path("stubs/domain-kit/{$stub}.stub");
        $default = __DIR__ . "/../../stubs/{$stub}.stub";

        if (File::exists($custom)) {
            return File::get($custom);
        }

        if (File::exists($default)) {
            return File::get($default);
        }

        throw new RuntimeException("Stub [{$stub}] not found.");
    }
}
