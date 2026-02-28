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

it('respects nested action style when generating actions directly', function () {
    config()->set('domain-kit.controller_actions.style', 'nested');

    $this->artisan('make:domain:action Users DestroyUser')
        ->assertExitCode(0);

    $file = app_path('Domains/Users/Actions/User/Destroy.php');
    expect(file_exists($file))->toBeTrue();

    $contents = file_get_contents($file);
    expect($contents)->toContain('namespace App\Domains\Users\Actions\User;');
    expect($contents)->toContain('final class Destroy');
});

it('auto-wires action import and typehint into existing controller', function () {
    $this->artisan('make:domain:controller Users UserController --resource')
        ->assertExitCode(0);

    $this->artisan('make:domain:action Users CreateUser')
        ->assertExitCode(0);

    $controller = file_get_contents(app_path('Domains/Users/Controllers/UserController.php'));
    expect($controller)->toContain('use App\Domains\Users\Actions\CreateUser;');
    expect($controller)->toContain('public function store(CreateUser $createUserAction, Request $request): Response');
});

it('imports existing model into generated action', function () {
    $this->artisan('make:domain:model Users User')
        ->assertExitCode(0);

    $this->artisan('make:domain:action Users UpdateUser')
        ->assertExitCode(0);

    $action = file_get_contents(app_path('Domains/Users/Actions/UpdateUser.php'));
    expect($action)->toContain('use App\Domains\Users\Models\User;');
});
