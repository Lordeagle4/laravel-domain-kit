<?php

declare(strict_types=1);

it('creates a domain controller', function () {
    $this->artisan('make:domain:controller Orders OrderController')
        ->assertExitCode(0);

    expect(file_exists(
        app_path('Domains/Orders/Controllers/OrderController.php')
    ))->toBeTrue();
});
