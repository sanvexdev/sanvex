# Sanvex

AI agents hit GitHub, Gmail, Linear, Notion, Slack, or your own drivers through one Laravel surface: `SanvexManager` → `resolveDriver($id)` → `$driver->resourceName()->action($args)`.

[Latest Version on Packagist](https://packagist.org/packages/sanvex/core)
[Total Downloads](https://packagist.org/packages/sanvex/core)

---

## Packages at a glance


| Package       | Role                                                         |
| ------------- | ------------------------------------------------------------ |
| `sanvex/core` | `SanvexManager`, encryption, DB tables, webhooks, tenancy    |
| `sanvex/cli`  | Artisan: `sanvex:setup`, `sanvex:list`, `sanvex:keygen`, scaffolding |
| `sanvex/mcp`  | MCP server (stdio + optional HTTP SSE) exposing Sanvex tools |


Add `sanvex/mcp` or `sanvex/laravel-ai` when you expose Sanvex to agents.

---

## Install and setup

```bash
composer require sanvex/core sanvex/cli sanvex/github
php artisan migrate
php artisan sanvex:list
php artisan sanvex:setup github --api-key="ghp_..."
```

Optional:

```bash
php artisan vendor:publish --tag=sanvex-config
php artisan sanvex:keygen   # add SANVEX_KEK to .env
```

Custom drivers: register classes in `config/sanvex.php` under `drivers`.

---

## Drivers


| Composer package | Driver id | Typical surface                     |
| ---------------- | --------- | ----------------------------------- |
| `sanvex/github`  | `github`  | Repositories, issues, pull requests |
| `sanvex/gmail`   | `gmail`   | Messages, threads                   |
| `sanvex/linear`  | `linear`  | Issues, projects                    |
| `sanvex/notion`  | `notion`  | Pages, databases, blocks, search    |
| `sanvex/slack`   | `slack`   | Channels, messages, users           |


Each package’s `composer.json` declares its Laravel service provider for discovery.

---

## Core PHP example

```php
use Sanvex\Core\SanvexManager;

public function repos(SanvexManager $manager)
{
    return $manager->resolveDriver('github')
        ->repositories()
        ->list(['per_page' => 10]);
}
```

---

## AI example

```json
{
  "name": "sanvex_action",
  "parameters": {
    "type": "object",
    "properties": {
      "driver": { "type": "string" },
      "resource": { "type": "string" },
      "action": { "type": "string" },
      "args": { "type": "object" }
    },
    "required": ["driver", "resource", "action"]
  }
}
```

```php
$d = $manager->for($tenant)->resolveDriver($instruction['driver']);
$result = $d->{$instruction['resource']}()->{$instruction['action']}($instruction['args'] ?? []);
```

---

## MCP (optional)

Package: `sanvex/mcp`. Entry command:

```bash
php artisan sanvex:mcp-stdio
```


| Topic        | Detail                                                                 |
| ------------ | ---------------------------------------------------------------------- |
| Transport    | JSON-RPC one message per line on stdin, replies on stdout              |
| Server class | `Sanvex\Mcp\Server\JsonRpcServer`                                      |
| Tools        | `sanvex_action`, `sanvex_list_operations`                              |
| Tenancy      | Uses global `resolveDriver()` (no `for($owner)` in the bundled server) |


SSE (optional): when `SANVEX_MCP_ENABLE_SERVER` / `config('sanvex.mcp.enable_server')` is true, routes `GET /sanvex/mcp/sse` and `POST /sanvex/mcp/message` use the same server for payloads.

---

## CLI

Package: `sanvex/cli`. Commands register only when the app runs in console.


| Command                     | Purpose                                                                      |
| --------------------------- | ---------------------------------------------------------------------------- |
| `sanvex:list`               | Registered drivers and auth metadata                                         |
| `sanvex:setup {driver}`     | Store credentials (`--api-key`, `--bot-token`, `--owner-type`, `--owner-id`) |
| `sanvex:keygen`             | Print a random `SANVEX_KEK=...` line for `.env`                              |
| `sanvex:make-driver {name}` | Scaffold under `packages/drivers/{name}` in the consuming app                |

Migrations: use `php artisan migrate` after installing `sanvex/core`. MCP: `php artisan sanvex:mcp-stdio` (requires `sanvex/mcp`).


---

## Multi-tenancy

Use `SanvexManager::for($owner)` before `resolveDriver()` so keys and driver clones are scoped to that owner (`Owner` from an Eloquent model or `Sanvex\Core\Contracts\SanvexOwner`).

```php
$notion = $manager->for(auth()->user())->resolveDriver('notion');
$pages = $notion->pages()->list(['page_size' => 10]);
```

Global scope stays available: `resolveDriver('github')` is the same as `for(null)->resolveDriver('github')`.

CLI setup with owner: `php artisan sanvex:setup slack --bot-token=... --owner-type=App\\Models\\Workspace --owner-id=42`.

---

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.