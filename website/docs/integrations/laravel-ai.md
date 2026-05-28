---
title: Laravel AI
---

# Laravel AI integration

The `sanvex/laravel-ai` package connects Sanvex drivers to [Laravel AI](https://laravel.com/docs/ai) agents as callable tools.

## Install

```bash
composer require sanvex/laravel-ai
```

Requires `sanvex/core`, `laravel/ai` ^0.6.0, and Laravel 12 or 13.

The service provider registers `SanvexAi` as a singleton (alias: `sanvex.ai`).

## Two integration modes

### 1. Single action tool

One tool that accepts `{ driver, resource, action, args }` and executes any exposed operation:

```php
use Sanvex\LaravelAi\Ai\SanvexAi;

public function agent(SanvexAi $sanvex)
{
    return SomeAgent::make()
        ->tools([$sanvex->tool()])
        ->prompt('List my GitHub repositories');
}
```

Restrict to specific drivers:

```php
$sanvex->tool(['github', 'slack']);
```

The tool always allows `driver: "system"` for introspection.

List configured drivers:

```json
{
  "driver": "system",
  "resource": "drivers",
  "action": "list.configured",
  "args": {}
}
```

### 2. Per-operation tools

Generate one Laravel AI tool per driver resource action:

```php
$sanvex->driver('github')->tools();
// or multiple drivers:
$sanvex->drivers(['github', 'slack'])->tools();
```

Filter tools:

```php
$sanvex->driver('github')
    ->readOnly()
    ->except(['repositories.delete'])
    ->tools();
```

## How execution works

`SanvexActionExecutor`:

1. Resolves the driver via `SanvexManager::resolveDriver()` (global scope)
2. Checks `isConfigured()` — returns a setup hint if not
3. Calls `$driver->{$resource}()->{$action}($args)`
4. Discovers actions via `#[Operation]` attributes when present, otherwise all public methods

Large list results are truncated to 5 items with a note.

## Metadata

Inspect available drivers and actions without executing:

```php
$metadata = app(SanvexAi::class)->metadata();
// or filter:
$metadata = app(SanvexAi::class)->metadata(['github']);
```

Returns driver id, `configured` status, resource names, and action lists.

## Tenancy note

The bundled executor uses global `resolveDriver()`. For per-user credentials, resolve the manager with `for($owner)` in your own agent logic or wrap tool execution in a tenant context.

See [Tenancy](../concepts/tenancy).
