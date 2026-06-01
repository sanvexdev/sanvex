---
title: Laravel AI
---

# Laravel AI integration

Use `sanvex/laravel-ai` to give [Laravel AI](https://laravel.com/docs/ai) agents access to Sanvex drivers (GitHub, Gmail, Slack, and others) as tools.

## Install

```bash
composer require sanvex/laravel-ai
```

Requires `sanvex/core`, `laravel/ai` ^0.6.0, and Laravel 12 or 13.

Configure at least one driver first (see [Quickstart](../getting-started/quickstart)). The package registers `SanvexAi` automatically as `app(SanvexAi::class)` or `app('sanvex.ai')`.

## Add tools to an agent

Implement `HasTools` and return Sanvex tools from `tools()`:

```php
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Promptable;
use Sanvex\LaravelAi\Ai\SanvexAi;

class MyAgent implements Agent, HasTools
{
    use Promptable;

    public function instructions(): string
    {
        return 'You help the user with their GitHub repositories.';
    }

    public function tools(): iterable
    {
        return app(SanvexAi::class)->driver('github')->tools();
    }
}
```

The agent receives one tool per operation (for example `Sanvex_Github_Repositories_List`) with parameters the model can fill in directly.

Inject `SanvexAi` in a controller or action instead of `app()` if you prefer:

```php
public function agent(SanvexAi $sanvex)
{
    return MyAgent::make()
        ->tools($sanvex->driver('github')->tools())
        ->prompt('List my open pull requests');
}
```

## Choose an approach

| | Per-operation tools | Generic router |
|---|---------------------|----------------|
| **How** | `$sanvex->driver('github')->tools()` | `$sanvex->tool()` |
| **When** | Default for most agents | Exploration, many drivers, or legacy resources without rich schemas |
| **Model experience** | Sees each operation and its parameters | One tool; may call `system` / `drivers` / `list` first to discover actions |

**Recommendation:** start with per-operation tools for the drivers you need. Add the router only when you have a clear reason.

## Per-operation tools

### One driver

```php
return app(SanvexAi::class)->driver('github')->tools();
```

### Several drivers

```php
return app(SanvexAi::class)->drivers(['github', 'slack', 'notion'])->tools();
```

### Read-only agents

Safe default for support or reporting bots:

```php
return app(SanvexAi::class)->driver('github')->readOnly()->tools();
```

### Allow only specific actions

```php
return app(SanvexAi::class)
    ->drivers(['github', 'slack'])
    ->only([
        'github.repositories.list',
        'github.issues.create',
        'slack.messages.post',
    ])
    ->tools();
```

### Block dangerous actions

```php
return app(SanvexAi::class)
    ->drivers(['github', 'gmail'])
    ->except(['github.repositories.delete', 'gmail.messages.delete'])
    ->tools();
```

### Exclude a whole resource

```php
return app(SanvexAi::class)->driver('github')->except(['repositories'])->tools();
```

### Combine filters

`only()` and `except()` stack; `readOnly()` applies last.

```php
return app(SanvexAi::class)
    ->drivers(['github', 'gmail'])
    ->readOnly()
    ->except(['gmail.threads.list'])
    ->tools();
```

## Filter patterns (multi-driver)

With **one driver**, short names are enough: `repositories.delete`, `issues.list`.

With **several drivers**, prefix by driver when resource names overlap (for example `repositories` on GitHub and Bitbucket):

| Pattern | Effect |
|---------|--------|
| `repositories.delete` | Delete on **every** driver in the set that has `repositories` |
| `github.repositories.delete` | Delete only on GitHub |
| `bitbucket.repositories` | All repository tools on Bitbucket only |

```php
// Wrong: removes delete on both GitHub and Bitbucket
->except(['repositories.delete'])

// Right: only Bitbucket
->except(['bitbucket.repositories.delete'])
```

## Generic router tool

A single tool accepts JSON: `driver`, `resource`, `action`, `args`.

```php
return [app(SanvexAi::class)->tool()];
```

Limit which drivers the model may use:

```php
return [app(SanvexAi::class)->tool(['github', 'slack'])];
```

`driver: "system"` is always allowed so the model can discover what exists.

**Discover drivers** (all registered):

```json
{ "driver": "system", "resource": "drivers", "action": "list", "args": {} }
```

**Configured drivers only:**

```json
{ "driver": "system", "resource": "drivers", "action": "list.configured", "args": {} }
```

**Call an operation** (example):

```json
{
  "driver": "github",
  "resource": "repositories",
  "action": "list",
  "args": { "per_page": 10 }
}
```

Use `args: {}` when an action takes no parameters.

## Mix both approaches

Common pattern: typed GitHub tools plus a router for less-used integrations.

```php
return [
    ...app(SanvexAi::class)->driver('github')->readOnly()->tools(),
    app(SanvexAi::class)->tool(['notion', 'linear']),
];
```

## Inspect drivers without calling the API

Useful when building prompts or debugging agent setup:

```php
$meta = app(SanvexAi::class)->metadata();
$meta = app(SanvexAi::class)->metadata(['github', 'slack']);
```

Each entry includes whether the driver is configured and which resources/actions are available.

## Multi-tenant apps

Out of the box, tools resolve credentials for the **global** Sanvex scope. For per-user accounts, resolve drivers with your tenant context before the agent runs, or wrap tool execution so `SanvexManager::for($owner)` applies for that request.

See [Tenancy](../concepts/tenancy).

## Troubleshooting

| Issue | What to do |
|-------|------------|
| Model says driver is not configured | Run `php artisan sanvex:setup {driver}` for that service |
| Too many tools / high token use | Narrow with `only()`, one driver, or the router |
| Model picks wrong GitHub repo action | Prefer per-operation tools over the router |
| Huge list responses | Long lists are truncated automatically in tool output |
| Custom Sanvex driver, weak tool args | Add `#[Operation]` on resource methods so the model gets typed parameters |

## Related

- [Usage](../getting-started/usage) — call drivers directly in PHP
- [MCP](./mcp) — expose Sanvex to Cursor, Claude Desktop, and other MCP clients
- [Packages](../concepts/packages) — which Composer packages to install
