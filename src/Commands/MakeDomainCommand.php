<?php

declare(strict_types=1);

namespace Awtechs\LaravelDomainKit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

final class MakeDomainCommand extends Command
{
    protected $signature = 'make:domain {name}';
    protected $description = 'Create a new domain structure';

    public function handle(): int
    {
        $domain = ucfirst($this->argument('name'));
        $basePath = app_path("Domains/{$domain}");

        if (File::exists($basePath)) {
            $this->error("Domain [{$domain}] already exists.");
            return self::FAILURE;
        }

        File::makeDirectory($basePath, 0755, true);

        foreach (config('domain-kit.generate') as $folder => $enabled) {
            if ($enabled === true) {
                File::makeDirectory(
                    "{$basePath}/" . ucfirst($folder),
                    0755,
                    true
                );
            }
        }

        $this->info("Domain [{$domain}] created.");
        return self::SUCCESS;
    }
}
