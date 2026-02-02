<?php

declare(strict_types=1);

it('creates a domain with configured folders', function () {
    $this->artisan('make:domain Orders')
        ->assertExitCode(0);

    expect(is_dir(app_path('Domains/Orders/Controllers')))->toBeTrue();
    expect(is_dir(app_path('Domains/Orders/Models')))->toBeTrue();
});
