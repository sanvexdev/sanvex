---
title: Installation
---

# Installation

PHP 8.2+, Laravel 12 or 13, and Composer.

For a single checklist with troubleshooting, use the [Integration guide](./integration).

## 1. Install Sanvex

```bash
composer require sanvex/core sanvex/cli
php artisan migrate
```

`php artisan migrate` creates the `sv_*` tables (Sanvex migrations ship with `sanvex/core` and load automatically). You do not need a separate Sanvex migrate command.

Confirm:

```bash
php artisan sanvex:list
```

## 2. Add drivers

```bash
composer require sanvex/gmail
# composer require sanvex/github
# composer require sanvex/linear
# composer require sanvex/notion
# composer require sanvex/slack
```

Run `php artisan sanvex:list` again — the new driver should appear.

## 3. Configuration

### Environment variables

Set driver variables in `.env` (see each driver’s [Configuration](../drivers/) page). Example for Gmail OAuth:

```env
GMAIL_CLIENT_ID=....apps.googleusercontent.com
GMAIL_CLIENT_SECRET=...
GMAIL_REDIRECT_URI="${APP_URL}/sanvex/gmail/callback"
GMAIL_SUCCESS_REDIRECT=/
```

`APP_URL` must match your running app and the provider’s redirect URI in their console.

### Publish config (optional)

```bash
php artisan vendor:publish --tag=sanvex-config
```

Only needed if you want to edit `config/sanvex.php` in your app (custom `drivers` array, overrides). `.env` alone is enough for most installs.

### Encryption key (optional)

```bash
php artisan sanvex:keygen
```

Copy the printed line into `.env` as `SANVEX_KEK=...`. If omitted, Sanvex uses `APP_KEY`.

Per-driver details: [Drivers](../drivers/). Core settings: [Configuration](../concepts/configuration).

## 4. Store credentials

**API key drivers** (GitHub, Notion internal, etc.):

```bash
php artisan sanvex:setup github --api-key="ghp_..."
```

**OAuth drivers** (Gmail, Notion public) — set client ID/secret in `.env`, then visit the built-in login URL (routes register when client ID is set):

| Driver | Connect URL |
| ------ | ----------- |
| Gmail | `GET /sanvex/gmail/login` |
| Notion | `GET /sanvex/notion/login` |

See [Gmail configuration](../drivers/gmail/configuration) and [Notion configuration](../drivers/notion/configuration).

## 5. Verify

```php
use Sanvex\Core\SanvexManager;

app(SanvexManager::class)->resolveDriver('gmail')->isConfigured();
```

```bash
php artisan tinker --execute="dump(app(\Sanvex\Core\SanvexManager::class)->resolveDriver('gmail')->isConfigured());"
```

## Agent packages (optional)

```bash
composer require sanvex/laravel-ai
composer require sanvex/mcp
```

[Laravel AI](../integrations/laravel-ai) · [MCP](../integrations/mcp)

## Next step

[Usage](./usage) · [Quickstart](./quickstart) · [Database](../concepts/database)
