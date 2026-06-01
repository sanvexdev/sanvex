---
title: Packages
---

# Packages

Sanvex is split into Composer packages. Install only what you need.

## `sanvex/core`

The foundation. Provides:

- `SanvexManager` — register and resolve drivers
- `BaseDriver` — base class all drivers extend
- Encryption via `KeyManager` and `EncryptionService`
- Database migrations (`sv_*` tables)
- Webhook endpoint: `POST /sanvex/webhook`
- Tenancy via `SanvexManager::for($owner)`

```bash
composer require sanvex/core
```

Requires PHP 8.2+ and Laravel 12 or 13.

## `sanvex/cli`

Artisan commands for setup and scaffolding. Registers only when the app runs in console.

```bash
composer require sanvex/cli
```

| Command | Purpose |
| ------- | ------- |
| `sanvex:list` | List registered drivers and auth metadata |
| `sanvex:setup {driver}` | Store credentials (`--api-key`, `--bot-token`, owner options) |
| `sanvex:keygen` | Print a `SANVEX_KEK=...` line for `.env` |
| `sanvex:backfill {driver}` | Placeholder backfill hook (no driver logic yet) |
| `sanvex:make-driver {name}` | Scaffold a new driver package |

## Driver packages

Each driver is a separate Composer package with its own service provider:

| Package | Driver id |
| ------- | --------- |
| `sanvex/github` | `github` |
| `sanvex/gmail` | `gmail` |
| `sanvex/linear` | `linear` |
| `sanvex/notion` | `notion` |
| `sanvex/slack` | `slack` |

Install only the drivers you use:

```bash
composer require sanvex/github sanvex/slack
```

Custom drivers are registered in `config/sanvex.php`:

```php
'drivers' => [
    \App\Sanvex\AcmeDriver::class,
],
```

## `sanvex/mcp`

Exposes Sanvex to MCP-compatible clients (IDE agents, Claude Desktop, etc.).

```bash
composer require sanvex/mcp
```

- Stdio server: `php artisan sanvex:mcp-stdio`
- Optional HTTP SSE when `SANVEX_MCP_ENABLE_SERVER=true`

See [MCP integration](../integrations/mcp).

## `sanvex/laravel-ai`

Wraps Sanvex for [Laravel AI](https://laravel.com/docs/ai) agents. Provides `SanvexAi` with tool discovery and execution.

```bash
composer require sanvex/laravel-ai
```

Requires `laravel/ai` ^0.6.0. See [Laravel AI integration](../integrations/laravel-ai).

## Typical install set

Minimal (one driver):

```bash
composer require sanvex/core sanvex/cli sanvex/github
php artisan migrate
php artisan sanvex:setup github --api-key="ghp_..."
```

With agent tooling:

```bash
composer require sanvex/core sanvex/cli sanvex/github sanvex/mcp
# or
composer require sanvex/core sanvex/cli sanvex/github sanvex/laravel-ai
```
