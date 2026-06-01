---
title: Installation
---

# Installation

PHP 8.2+, Laravel 12 or 13, and Composer.

## 1. Install Sanvex

```bash
composer require sanvex/core sanvex/cli
php artisan migrate
```

`php artisan migrate` creates the `sv_*` tables (and your normal Laravel tables on a new app). You do not need a separate Sanvex migrate command.

## 2. Add drivers

```bash
composer require sanvex/github
# composer require sanvex/gmail
# composer require sanvex/linear
# composer require sanvex/notion
# composer require sanvex/slack
```

Confirm the driver is registered:

```bash
php artisan sanvex:list
```

## 3. Store credentials

```bash
php artisan sanvex:setup github --api-key="ghp_..."
```

Per-team or per-user keys:

```bash
php artisan sanvex:setup notion --api-key="secret_..." \
  --owner-type=App\\Models\\Team --owner-id=1
```

More per driver: [Drivers](../drivers/) and [Authentication](../concepts/authentication).

## 4. Verify

```php
use Sanvex\Core\SanvexManager;

$driver = app(SanvexManager::class)->resolveDriver('github');

$driver->isConfigured(); // true after sanvex:setup
```

Or from the shell:

```bash
php artisan tinker --execute="dump(app(\Sanvex\Core\SanvexManager::class)->resolveDriver('github')->isConfigured());"
```

## Agent packages (optional)

```bash
composer require sanvex/laravel-ai
composer require sanvex/mcp
```

[Laravel AI](../integrations/laravel-ai) · [MCP](../integrations/mcp)

## Configuration (optional)

Skip publishing if defaults are enough. To customize drivers or encryption:

```bash
php artisan vendor:publish --tag=sanvex-config
```

Dedicated encryption key (optional):

```bash
php artisan sanvex:keygen
```

Copy the printed line into `.env` as `SANVEX_KEK=...`. If omitted, Sanvex uses `APP_KEY`.

Add custom driver classes under `drivers` in `config/sanvex.php`.

[Database](../concepts/database) · [Packages](../concepts/packages)

## Next step

[Usage](./usage)
