<?php

declare(strict_types=1);

it('creates a final action with a single handle method', function () {
    $this->artisan('make:domain:action Orders CreateOrder')
        ->assertExitCode(0);

    $file = app_path('Domains/Orders/Actions/CreateOrder.php');

    expect(file_exists($file))->toBeTrue();

    $contents = file_get_contents($file);

    expect($contents)->toContain('final class CreateOrder');
    expect(substr_count($contents, 'public function handle('))->toBe(1);
    expect(substr_count($contents, 'public function'))->toBe(1);
});
