---
title: Tenancy
---

# Tenancy

Sanvex scopes credentials, driver instances, and some data by **owner**. This lets a single Laravel app serve multiple users or teams with separate integration credentials.

## Global vs tenant scope

| Scope | `owner_type` | `owner_id` | When |
| ----- | ------------ | ---------- | ---- |
| Global | `global` | `default` | Default — one set of credentials for the whole app |
| Tenant | Eloquent model class | Model id | Per-user or per-team credentials |

CLI setup without owner options stores credentials globally:

```bash
php artisan sanvex:setup github --api-key="ghp_..."
```

Tenant-scoped setup:

```bash
php artisan sanvex:setup slack --bot-token="xoxb-..." \
  --owner-type=App\\Models\\Team --owner-id=42
```

## Runtime usage

Use `for($owner)` before resolving a driver:

```php
use Sanvex\Core\SanvexManager;

public function pages(SanvexManager $manager)
{
    return $manager
        ->for(auth()->user())
        ->resolveDriver('notion')
        ->pages()
        ->list(['page_size' => 10]);
}
```

Global scope is the default:

```php
// Same as for(null)->resolveDriver('github')
$manager->resolveDriver('github');
```

## Owner types

An owner can be:

- An Eloquent model — `owner_type` is the model class, `owner_id` is the primary key
- Any object implementing `Sanvex\Core\Contracts\SanvexOwner`
- Built via `Owner::fromTypeAndId($type, $id)` or `Owner::global()`

## What gets scoped

| Layer | Scoped? |
| ----- | ------- |
| `sv_accounts` credentials | Yes — per owner |
| Driver instance (cloned per tenant) | Yes — via `cloneForTenant` |
| `sv_entities` rows | Yes — unique per owner |
| `sv_events` | Yes — owner columns present |
| `sv_permissions` | Yes — owner columns present |
| MCP stdio server | No — uses global `resolveDriver()` |
| Notion OAuth callback (default) | No — stores tokens in global scope |

When building multi-tenant apps, ensure OAuth callbacks and MCP usage account for owner context in your own application code.
