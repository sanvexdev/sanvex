---
title: Installation
---

# Installation

Set up Sanvex in a fresh or existing Laravel application.

## Prerequisites

- PHP 8.2+
- Laravel 12 or 13
- Composer

## Install core and CLI

```bash
composer require sanvex/core sanvex/cli
```

## Install drivers

Add one package per external service you need:

```bash
composer require sanvex/github
# composer require sanvex/gmail
# composer require sanvex/linear
# composer require sanvex/notion
# composer require sanvex/slack
```

Optional agent packages:

```bash
composer require sanvex/mcp          # MCP server
composer require sanvex/laravel-ai   # Laravel AI SDK tools
```

## Database

Sanvex core auto-loads its migrations. Run them with your app:

```bash
php artisan migrate
```

Or use the Sanvex-specific command:

```bash
php artisan sanvex:migrate
```

This creates `sv_accounts`, `sv_entities`, `sv_events`, `sv_permissions`, and `sv_integrations` tables. See [Database](../concepts/database).

## Configuration

Publish config (optional — package defaults work out of the box):

```bash
php artisan vendor:publish --tag=sanvex-config
```

Key settings in `config/sanvex.php`:

| Setting | Env var | Default |
| ------- | ------- | ------- |
| Encryption key | `SANVEX_KEK` | Falls back to `APP_KEY` |
| MCP server | `SANVEX_MCP_ENABLE_SERVER` | `false` |
| MCP run script | `SANVEX_MCP_ALLOW_RUN_SCRIPT` | `false` |
| Approval URL | `SANVEX_APPROVAL_URL` | `/sanvex/approve` |

Generate a dedicated encryption key:

```bash
php artisan sanvex:keygen
```

Add the output to `.env`:

```
SANVEX_KEK=base64:...
```

## Register custom drivers

Add driver classes to the `drivers` array in `config/sanvex.php`:

```php
'drivers' => [
    \App\Sanvex\AcmeDriver::class,
],
```

## Store credentials

List installed drivers:

```bash
php artisan sanvex:list
```

Set up a driver (global scope):

```bash
php artisan sanvex:setup github --api-key="ghp_..."
```

Tenant-scoped credentials:

```bash
php artisan sanvex:setup notion --api-key="secret_..." \
  --owner-type=App\\Models\\Team --owner-id=1
```

See [Authentication](../concepts/authentication) for OAuth and token details per driver.

## Verify

```php
use Sanvex\Core\SanvexManager;

$driver = app(SanvexManager::class)->resolveDriver('github');
$configured = $driver->isConfigured(); // true after setup
```

## Next step

Continue with [Usage](./usage).
