# Laravel Domain Kit

> **Opinionated, domain-first tooling for Laravel.**
>
> Laravel Domain Kit turns architecture into tooling. Instead of *documenting* best practices, it **enforces them at generation time**.

---

## What This Is

Laravel Domain Kit is a **framework-style package** that introduces **Domain‑Driven structure**, **event discipline**, and **queue conventions** into Laravel projects through first‑class Artisan commands.

It is inspired by:

* Laravel’s own generators
* Symfony MakerBundle
* Real‑world, long‑lived Laravel systems

This is **not** a snippet collection.
This is **not** a helper repo.

This is tooling that encodes architectural decisions.

---

## Core Philosophy

1. **Architecture must be enforceable**
2. **Domains are first‑class citizens**
3. **One responsibility per class**
4. **Events describe facts, not actions**
5. **Queues are intent‑driven, not string‑driven**
6. **DX matters as much as runtime correctness**
7. **AI tooling must understand your system**

---

## Installation

```bash
composer require --dev awtechs/laravel-domain-kit
```

Laravel auto‑discovers the service provider.

Optional (recommended):

```bash
php artisan vendor:publish --tag=domain-kit-config
```

---

## Directory Structure Enforced

```txt
app/
 └── Domains/
     └── Orders/
         ├── Actions/
         ├── Events/
         ├── Listeners/
         ├── Jobs/
         ├── Policies/
         ├── Models/
```

Each domain is a **bounded context**.

---

## Commands Overview

### Domain Root

```bash
php artisan make:domain Orders
```

Creates the domain skeleton.

---

### Events

```bash
php artisan make:domain:event Orders OrderPlaced
```

Result:

```php
namespace App\Domains\Orders\Events;

final class OrderPlaced {}
```

---

### Listeners

```bash
php artisan make:domain:listener Orders SendOrderConfirmation --event=OrderPlaced
```

Features:

* Native Laravel listener stub
* Domain‑scoped namespace
* Optional auto‑registration

---

### Jobs (Queue‑Aware)

```bash
php artisan make:domain:job Orders ProcessOrderPayment --queue=heavy
```

Jobs declare **intent**, not queue names.

```php
final class ProcessOrderPayment implements ShouldQueue, HeavyQueue
{
    use UsesDomainQueue;
}
```

Automatically resolves to:

```
orders-heavy
```

---

### Actions (Use‑Cases)

```bash
php artisan make:domain:action Orders CreateOrder
```

Actions are:

* Synchronous
* Reusable
* Testable

Controllers, listeners, and jobs **call actions**, not each other.

---

### Policies

```bash
php artisan make:domain:policy Orders OrderPolicy --model=Order
```

Policies are:

* Domain‑scoped
* Optionally auto‑registered

---

### Domain Config

```bash
php artisan make:domain:config Orders orders
```

Creates:

```txt
config/domains/orders.php
```

Used for:

* Feature flags
* Queue overrides
* Integrations

---

## Queue System (Intent‑Driven)

### Marker Interfaces

* `EmailQueue`
* `SyncQueue`
* `HeavyQueue`

### Resolution Rule

```
<domain>-<intent>
```

Example:

```
orders-emails
users-sync
billing-heavy
```

No hard‑coded strings.

---

## Event Auto‑Registration

Controlled via config:

```php
return [
    'events' => [
        'auto_register' => true,
    ],

    'policies' => [
        'auto_register' => true,
    ],
];
```

Turn it off if you prefer explicit wiring.

---

## AI / MCP / Laravel Boost Support

Domain Kit generates **machine‑readable architecture metadata** under:

```txt
.ai/domain-kit/
 ├── domains.json
 ├── events.json
 ├── queues.json
```

This allows:

* Laravel Boost to reason about flows
* MCP agents to safely modify code
* Zero hallucination about system structure

This package is **AI‑first by design**.

---

## Versioning Strategy (Very Important)

### Pre‑1.0

* Rapid iteration
* Minor breaking changes allowed

### v1.0.0 (API Freeze)

Once v1.0 is tagged:

* Command signatures are frozen
* Directory conventions are frozen
* Public contracts are frozen
* Config keys are frozen

Breaking changes **require v2.0**.

This ensures long‑term project safety.

---

## Testing Strategy

Domain Kit is tested as a **tooling framework**, not a runtime library.

### Test Layers

1. **Command execution tests**
2. **Filesystem assertions**
3. **Stub correctness**
4. **Config toggles**
5. **Idempotency checks**

### Example (Pest)

```php
it('creates a domain event in the correct location', function () {
    $this->artisan('make:domain:event Orders OrderPlaced')
        ->assertExitCode(0);

    expect(file_exists(
        app_path('Domains/Orders/Events/OrderPlaced.php')
    ))->toBeTrue();
});
```

Tests live in:

```txt
tests/Feature/Commands/
```

---

## GitHub & Packagist Release Checklist

### 1. Repository Setup

```txt
quiler/laravel-domain-kit
```

* MIT License
* Clean README
* `main` branch protected

---

### 2. Composer Metadata

* Proper package name
* Laravel auto‑discovery
* PHP ^8.2
* Illuminate ^11 | ^12

---

### 3. Tagging

```bash
git tag v0.1.0
git push origin v0.1.0
```

Packagist auto‑indexes tags.

---

### 4. CI (Recommended)

* PHPStan (level 8)
* Pest
* PHP CS Fixer

---

## What This Package Is NOT

* Not a full DDD framework
* Not an ORM replacement
* Not a runtime abstraction layer

It is **tooling that enforces discipline**.

---

## Final Word

Laravel Domain Kit exists because **architecture that lives only in documents always dies**.

This package makes good architecture:

* Easy to start
* Hard to misuse
* Obvious to read
* Friendly to humans *and* AI

If Laravel is your long‑term platform, this package pays for itself in months.

---

**Welcome to domain‑first Laravel.**
