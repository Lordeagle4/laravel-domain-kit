# Security Policy

## Supported Versions

Only the latest major version receives security updates.

| Version | Supported |
|--------|-----------|
| 1.x    | ✅ |
| < 1.0  | ❌ |

## Reporting a Vulnerability

If you discover a security issue, **do not open a public issue**.

Instead, email:

security@laravel-domain-kit.dev

Include:
- Laravel version
- PHP version
- Description of the issue
- Steps to reproduce

You will receive a response within 72 hours.

## Scope

Laravel Domain Kit is a **development-time tool**.

It does **not**:
- Handle authentication
- Execute user input
- Run in production request lifecycle

Security impact is therefore limited to:
- Filesystem access
- Code generation integrity
