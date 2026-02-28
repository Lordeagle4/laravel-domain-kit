<?php

declare(strict_types=1);

it('creates a domain controller', function () {
    $this->artisan('make:domain:controller Orders OrderController')
        ->assertExitCode(0);

    expect(file_exists(
        app_path('Domains/Orders/Controllers/OrderController.php')
    ))->toBeTrue();
});

it('creates a resource controller with the expected methods', function () {
    $this->artisan('make:domain:controller Orders UserController --resource')
        ->assertExitCode(0);

    $file = app_path('Domains/Orders/Controllers/UserController.php');
    $contents = file_get_contents($file);

    expect($contents)->toContain('public function index(): Response');
    expect($contents)->toContain('public function create(): Response');
    expect($contents)->toContain('public function store(Request $request): Response');
    expect($contents)->toContain('public function show(int|string $id): Response');
    expect($contents)->toContain('public function edit(int|string $id): Response');
    expect($contents)->toContain('public function update(Request $request, int|string $id): Response');
    expect($contents)->toContain('public function destroy(int|string $id): Response');
});

it('creates an api resource controller returning json responses', function () {
    $this->artisan('make:domain:controller Orders ApiUserController --api')
        ->assertExitCode(0);

    $file = app_path('Domains/Orders/Controllers/ApiUserController.php');
    $contents = file_get_contents($file);

    expect($contents)->toContain('use Illuminate\Http\JsonResponse;');
    expect($contents)->toContain('public function index(): JsonResponse');
    expect($contents)->toContain('public function store(Request $request): JsonResponse');
    expect($contents)->toContain('public function show(int|string $id): JsonResponse');
    expect($contents)->toContain('public function update(Request $request, int|string $id): JsonResponse');
    expect($contents)->toContain('public function destroy(int|string $id): JsonResponse');
    expect($contents)->not->toContain('public function create(');
    expect($contents)->not->toContain('public function edit(');
    expect($contents)->toContain('return response()->json([], 201);');
    expect($contents)->toContain('return response()->json(null, 204);');
});

it('creates flat action classes and injects them into store/update/destroy', function () {
    config()->set('domain-kit.controller_actions.style', 'flat');

    $this->artisan('make:domain:controller Users UserController --ra')
        ->assertExitCode(0);

    expect(file_exists(app_path('Domains/Users/Actions/CreateUser.php')))->toBeTrue();
    expect(file_exists(app_path('Domains/Users/Actions/UpdateUser.php')))->toBeTrue();
    expect(file_exists(app_path('Domains/Users/Actions/DestroyUser.php')))->toBeTrue();

    $controller = file_get_contents(app_path('Domains/Users/Controllers/UserController.php'));
    expect($controller)->toContain('use App\Domains\Users\Actions\CreateUser;');
    expect($controller)->toContain('use App\Domains\Users\Actions\UpdateUser;');
    expect($controller)->toContain('use App\Domains\Users\Actions\DestroyUser;');
    expect($controller)->toContain('public function store(CreateUser $createUserAction, Request $request): Response');
    expect($controller)->toContain('public function update(UpdateUser $updateUserAction, Request $request, int|string $id): Response');
    expect($controller)->toContain('public function destroy(DestroyUser $destroyUserAction, int|string $id): Response');
    expect($controller)->toContain('public function create(): Response');
    expect($controller)->toContain('public function edit(int|string $id): Response');
});

it('creates nested action classes when configured and injects them', function () {
    config()->set('domain-kit.controller_actions.style', 'nested');

    $this->artisan('make:domain:controller Accounts AccountController --aa')
        ->assertExitCode(0);

    expect(file_exists(app_path('Domains/Accounts/Actions/Account/Create.php')))->toBeTrue();
    expect(file_exists(app_path('Domains/Accounts/Actions/Account/Update.php')))->toBeTrue();
    expect(file_exists(app_path('Domains/Accounts/Actions/Account/Destroy.php')))->toBeTrue();

    $controller = file_get_contents(app_path('Domains/Accounts/Controllers/AccountController.php'));
    expect($controller)->toContain('use App\Domains\Accounts\Actions\Account\Create;');
    expect($controller)->toContain('use App\Domains\Accounts\Actions\Account\Update;');
    expect($controller)->toContain('use App\Domains\Accounts\Actions\Account\Destroy;');
    expect($controller)->toContain('public function store(Create $createAction, Request $request): JsonResponse');
    expect($controller)->toContain('public function update(Update $updateAction, Request $request, int|string $id): JsonResponse');
    expect($controller)->toContain('public function destroy(Destroy $destroyAction, int|string $id): JsonResponse');
    expect($controller)->not->toContain('public function create(');
    expect($controller)->not->toContain('public function edit(');
});

it('fails when --action is used without --resource', function () {
    $this->artisan('make:domain:controller Users BadController --action')
        ->assertExitCode(1);
});
