<?php

declare(strict_types=1);

namespace Awtechs\LaravelDomainKit\Support;

final class DomainPathResolver
{
    public function resolve(string $domain, string $type): string
    {
        return app_path("Domains/{$domain}/{$type}");
    }

    public function namespace(string $domain, string $type): string
    {
        return "App\\Domains\\{$domain}\\{$type}";
    }
}
