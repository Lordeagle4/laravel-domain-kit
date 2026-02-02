# Contributing to Laravel Domain Kit

First off — **thank you** for considering a contribution.

Laravel Domain Kit is not a typical Laravel package. It is **architecture‑enforcing tooling**, which means contributions must prioritise **stability, consistency, and long‑term maintainability** over cleverness.

Please read this document carefully before opening a pull request.

---

## Core Principles (Non‑Negotiable)

All contributions **must** respect the following principles:

1. **Architecture over convenience**
2. **Explicit conventions beat implicit magic**
3. **Breaking changes are forbidden outside major versions**
4. **DX is a first‑class concern**
5. **AI tooling compatibility must be preserved**

If a change violates any of the above, it will be rejected.

---

## What This Project Is

Laravel Domain Kit is:

* A domain‑first generator framework
* A convention enforcement tool
* A DX optimisation layer for Laravel

It is **not**:

* A runtime abstraction library
* A DDD theory framework
* An experiment playground

Keep this distinction clear when proposing changes.

---

## Contribution Types Welcome

We actively welcome:

* New **domain generators** (policy, service, aggregate, etc.)
* Improvements to **existing stubs** (non‑breaking)
* Additional **tests**
* Documentation improvements
* Bug fixes
* Performance improvements (generator execution)

---

## Contribution Types NOT Accepted

The following will be rejected:

* Breaking changes without a major version
* Alternative directory conventions
* Implicit auto‑discovery features
* Runtime behaviour changes
* "Optional" shortcuts that bypass conventions

Laravel Domain Kit is intentionally opinionated.

---

## Development Setup

### Requirements

* PHP **8.2+**
* Composer
* Laravel knowledge (intermediate+)

### Install Dependencies

```bash
composer install
```

---

## Running Tests

All contributions **must include tests**.

```bash
vendor/bin/pest
```

Before submitting a PR:

* Tests must pass
* No new warnings
* No filesystem pollution

---

## Writing Tests (Important)

This package tests **tooling**, not runtime logic.

### Focus on:

* Artisan command execution
* Filesystem output
* Namespace correctness
* Config toggles
* Idempotency (no overwrites)
* Metadata generation for AI/MCP

### Do NOT test:

* Laravel internals
* Queue execution
* Event dispatching

Example:

```php
it('creates a domain action', function () {
    $this->artisan('make:domain:action Orders CreateOrder')
        ->assertExitCode(0);

    expect(file_exists(
        app_path('Domains/Orders/Actions/CreateOrder.php')
    ))->toBeTrue();
});
```

---

## Coding Standards

### PHP

* `declare(strict_types=1);` **mandatory**
* Typed properties and return types **required**
* `final` classes by default
* No facades inside generators (unless justified)
* No global helpers

### Structure

* Generators must use shared resolvers
* No duplicated path or namespace logic
* All filesystem writes must be explicit

---

## Stubs Guidelines

* Stubs must be **Laravel‑12 compatible**
* No framework‑specific hacks
* Keep stubs minimal and readable
* Avoid comments that explain obvious PHP

Stubs are part of the public developer experience.

---

## Configuration Changes

* New config keys require documentation
* Defaults must be backwards‑compatible
* Renaming config keys requires a major version

---

## AI / MCP Compatibility (Critical)

Any contribution **must preserve**:

* `.ai/domain-kit/` directory
* JSON schema structure
* Deterministic output

Breaking AI metadata is considered a **breaking change**.

---

## Versioning Rules

Laravel Domain Kit follows **Semantic Versioning**.

| Change          | Version |
| --------------- | ------- |
| Bug fix         | Patch   |
| New command     | Minor   |
| New option      | Minor   |
| Breaking change | Major   |

Do not propose breaking changes casually.

---

## Pull Request Checklist

Before submitting:

* [ ] Tests added or updated
* [ ] No breaking changes (unless major)
* [ ] Commands follow existing patterns
* [ ] README updated if needed
* [ ] No opinion drift

PRs that do not meet this checklist will be closed.

---

## Review Process

* Every PR is reviewed for **architecture impact**
* Changes may be requested even if tests pass
* Maintainers prioritise long‑term clarity over speed

---

## Final Note

Laravel Domain Kit is intentionally strict.

That strictness is what makes it valuable.

If you agree with the philosophy, you are very welcome here.
If you want flexibility over consistency, this may not be the right project — and that’s okay.

Thank you for contributing responsibly.
