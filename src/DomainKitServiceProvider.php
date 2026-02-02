<?php

declare(strict_types=1);

namespace Awtech\LaravelDomainKit;

use Illuminate\Support\ServiceProvider;
use Awtech\LaravelDomainKit\Commands\{
    MakeDomainCommand,
    MakeDomainEventCommand,
    MakeDomainListenerCommand,
    MakeDomainJobCommand
};

final class DomainKitServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            MakeDomainCommand::class,
            MakeDomainEventCommand::class,
            MakeDomainListenerCommand::class,
            MakeDomainJobCommand::class,
        ]);

        $this->publishes([
            __DIR__ . '/../stubs' => base_path('stubs/domain-kit'),
        ], 'domain-kit-stubs');
    }
}
