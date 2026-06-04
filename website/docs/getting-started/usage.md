---
title: Usage
---

# Usage

Sanvex follows one pattern everywhere: resolve a driver, pick a resource, call an action.

## Basic pattern

```php
use Sanvex\Core\SanvexManager;

public function repos(SanvexManager $manager)
{
    return $manager->resolveDriver('github')
        ->repositories()
        ->list(['per_page' => 10]);
}
```

Each driver exposes resources as methods on the driver instance:

```php
$github = $manager->resolveDriver('github');
$github->repositories()->get(['owner' => 'laravel', 'repo' => 'framework']);
$github->issues()->list(['owner' => 'laravel', 'repo' => 'framework']);
$github->pullRequests()->create([...]);
```

## Check configuration

Before calling a driver, verify credentials are stored:

```php
$driver = $manager->resolveDriver('github');

if (! $driver->isConfigured()) {
    // Run: php artisan sanvex:setup github --api-key=...
}
```

## Multi-tenancy

Scope credentials and driver instances to an owner:

```php
$notion = $manager
    ->for(auth()->user())
    ->resolveDriver('notion');

$pages = $notion->pages()->list(['page_size' => 10]);
```

See [Tenancy](../concepts/tenancy).

## Local database access

Some drivers cache entities in `sv_entities`. Access them via `db()`:

```php
$github = $manager->resolveDriver('github');
$repos = $github->db()->repositories()->list();
```

Note: DB resource queries filter by `driver` and `entity_type` but not by owner in the base implementation.

## Webhooks

Sanvex registers `POST /sanvex/webhook` for incoming webhook payloads. Drivers implement `handleWebhook()` and `verifySignature()`.

## Agent integration

Expose Sanvex to AI agents through:

- [Laravel AI](../integrations/laravel-ai) — `SanvexAi` tools for Laravel AI SDK agents
- [MCP](../integrations/mcp) — stdio or HTTP SSE server for MCP clients

## Discover available operations

```bash
php artisan sanvex:list
```

For MCP clients, use the `sanvex_list_operations` tool after starting `php artisan sanvex:mcp-stdio`.

## Next steps

- [Integration guide](./integration) — install → connect → verify checklist
- [Drivers](../drivers/) — per-driver setup and API surface
- [Authentication](../concepts/authentication) — token and OAuth setup
