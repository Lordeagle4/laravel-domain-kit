# Laravel Domain Kit

Opinionated, domain-first tooling for Laravel generators.

## Install

```bash
composer require --dev awtechs/laravel-domain-kit:^1.0
```

Laravel auto-discovers the service provider.

Publish package config:

```bash
php artisan vendor:publish --tag=domain-kit-config
```

Publish stubs:

```bash
php artisan vendor:publish --tag=domain-kit-stubs
```

## What it generates

Domain Kit writes under `app/Domains/{Domain}/...`.

Default `make:domain` folders are controlled by `config/domain-kit.php`.

## Commands

### Domain root

```bash
php artisan make:domain Orders
```

### Controller

```bash
php artisan make:domain:controller Orders OrderController
```

Resource controller:

```bash
php artisan make:domain:controller Orders OrderController --resource
# or
php artisan make:domain:controller Orders OrderController -r
```

Resource methods generated:

- `index`
- `create`
- `store`
- `show`
- `edit`
- `update`
- `destroy`

API resource controller:

```bash
php artisan make:domain:controller Orders OrderController --api
```

API resource methods generated:

- `index`
- `store`
- `show`
- `update`
- `destroy`

`create` and `edit` are intentionally omitted for API controllers.

Resource + actions shortcut:

```bash
php artisan make:domain:controller Orders OrderController --ra
```

API resource + actions shortcut:

```bash
php artisan make:domain:controller Orders OrderController --aa
```

Legacy equivalent flags still work:

- `--resource --action` for resource + actions
- `--resource --api --action` for api resource + actions

### Action

```bash
php artisan make:domain:action Orders CreateOrder
```

Action naming is verb-first:

- `CreateUser`
- `UpdateUser`
- `DestroyUser`

When matching controller methods exist, action generation auto-wires the controller:

- Adds action `use` import
- Adds type-hint parameter into `store`, `update`, or `destroy`

### Model

```bash
php artisan make:domain:model Orders Order
```

When generating actions, Domain Kit checks for a matching domain model:

- If model exists, it is imported into the action class
- If model does not exist, you are prompted to create it

### Event

```bash
php artisan make:domain:event Orders OrderPlaced
```

### Listener

```bash
php artisan make:domain:listener Orders SendOrderConfirmation
```

### Job

```bash
php artisan make:domain:job Orders ProcessOrderPayment
```

## Action style config

`config/domain-kit.php`:

```php
'controller_actions' => [
    'style' => 'flat', // or 'nested'
],
```

`flat` style:

- `App\\Domains\\Users\\Actions\\CreateUser`
- `App\\Domains\\Users\\Actions\\UpdateUser`
- `App\\Domains\\Users\\Actions\\DestroyUser`

`nested` style:

- `App\\Domains\\Users\\Actions\\User\\Create`
- `App\\Domains\\Users\\Actions\\User\\Update`
- `App\\Domains\\Users\\Actions\\User\\Destroy`

## Requirements

- PHP `^8.2`
- `illuminate/support` `^11 || ^12`

## Status

Current stable line is `1.0.x`.
