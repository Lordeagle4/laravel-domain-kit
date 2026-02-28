<?php

declare(strict_types=1);

namespace Awtechs\LaravelDomainKit;

use Illuminate\Support\ServiceProvider;
use Awtechs\LaravelDomainKit\Commands\{
    MakeDomainActionCommand,
    MakeDomainCommand,
    MakeDomainControllerCommand,
    MakeDomainEventCommand,
    MakeDomainListenerCommand,
    MakeDomainJobCommand,
    MakeDomainModelCommand
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
            MakeDomainActionCommand::class,
            MakeDomainCommand::class,
            MakeDomainEventCommand::class,
            MakeDomainListenerCommand::class,
            MakeDomainJobCommand::class,
            MakeDomainModelCommand::class,
            MakeDomainControllerCommand::class,
        ]);

        $this->publishes([
            __DIR__ . '/../stubs' => base_path('stubs/domain-kit'),
        ], 'domain-kit-stubs');

        $this->publishes([
            __DIR__ . '/../config/domain-kit.php' => config_path('domain-kit.php'),
        ], 'domain-kit-config');
    }
}
