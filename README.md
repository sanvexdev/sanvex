# Sanvex

Laravel integration layer for third-party APIs: one pattern (`driver` → `resource` → `action`), encrypted credentials, optional multi-tenant scoping, and hooks for AI (MCP stdio/SSE, generic tool payloads).

This repo is a monorepo (`packages/core`, `packages/cli`, `packages/mcp`, `packages/drivers/*`). Published packages use the `sanvex` vendor on Packagist; stable installs follow SemVer tags on this repo.

---

## Quick install

**1. Require what you need**

```bash
composer require sanvex/core:^0.1.0
composer require sanvex/cli sanvex/mcp
composer require sanvex/github sanvex/gmail sanvex/linear sanvex/notion sanvex/slack
```

Use `dev-main` (or path repos for local monorepo work) if you are not on a tagged release yet.

**2. Migrate**

Core registers migrations with Laravel. Run the app migration stack as usual:

```bash
php artisan migrate
```

Alternatively, the CLI can run only Sanvex migrations against the vendor path:

```bash
php artisan sanvex:migrate
# php artisan sanvex:migrate --fresh   # destructive reset of those migrations only
```

**3. Register drivers**

Official driver packages ship a `*ServiceProvider` that calls `SanvexManager::registerDriver()` on boot. With Composer [package discovery](https://laravel.com/docs/packages#package-discovery) enabled (default), you do **not** hand-list each driver in `bootstrap/providers.php` unless discovery is disabled.

Custom drivers: add the class to `config/sanvex.php` under `drivers`, or call `registerDriver()` from your own service provider after the manager is bound.

**4. Configure credentials**

```bash
php artisan sanvex:list
php artisan sanvex:setup github --api-key="ghp_..."
```

Tenant-scoped setup (stores keys against an owner):

```bash
php artisan sanvex:setup notion --api-key="secret_..." --owner-type=App\\Models\\Team --owner-id=1
```

Optional: `php artisan sanvex:keygen` / `php artisan sanvex:backfill {driver}` (see CLI below).

---

## Packages at a glance


| Package       | Role                                                         |
| ------------- | ------------------------------------------------------------ |
| `sanvex/core` | `SanvexManager`, encryption, DB tables, webhooks, tenancy    |
| `sanvex/cli`  | Artisan: setup, migrate, list, backfill, scaffolding         |
| `sanvex/mcp`  | MCP server (stdio + optional HTTP SSE) exposing Sanvex tools |


Require `sanvex/cli` only if you want those commands; require `sanvex/mcp` only if an agent or IDE will speak MCP.

---

## Configuration (`config/sanvex.php`)

Merged from core. Publish a copy into your app:

```bash
php artisan vendor:publish --tag=sanvex-config
```

That writes `config/sanvex.php`. Laravel loads your file over the package defaults. You can still copy from `vendor/sanvex/core/config/sanvex.php` if you prefer not to use Artisan.


| Key                        | Purpose                                                                           |
| -------------------------- | --------------------------------------------------------------------------------- |
| `kek`                      | Key-encryption key for stored credentials. Defaults to `SANVEX_KEK` or `APP_KEY`. |
| `drivers`                  | Extra driver classes to register (custom / in-app drivers).                       |
| `permissions.approval_url` | Base URL segment for permission flows (`SANVEX_APPROVAL_URL`).                    |
| `mcp.enable_server`        | When true, registers SSE MCP routes (`SANVEX_MCP_ENABLE_SERVER`).                 |
| `mcp.allow_run_script`     | Gates the `sanvex_run_script` MCP tool (`SANVEX_MCP_ALLOW_RUN_SCRIPT`).           |
| `driver_configs`           | Per-driver options (e.g. Notion OAuth vs API key under `driver_configs.notion`).  |


Environment variables used in the default config include `SANVEX_KEK`, `SANVEX_APPROVAL_URL`, `SANVEX_MCP_ENABLE_SERVER`, `SANVEX_MCP_ALLOW_RUN_SCRIPT`, and Notion-related `NOTION_*` keys where applicable.

---

## Supported drivers (first-party)


| Composer package | Driver id | Typical surface                     |
| ---------------- | --------- | ----------------------------------- |
| `sanvex/github`  | `github`  | Repositories, issues, pull requests |
| `sanvex/gmail`   | `gmail`   | Messages, threads                   |
| `sanvex/linear`  | `linear`  | Issues, projects                    |
| `sanvex/notion`  | `notion`  | Pages, databases, blocks, search    |
| `sanvex/slack`   | `slack`   | Channels, messages, users           |


Each package’s `composer.json` declares its Laravel service provider for discovery.

---

## Use in PHP

```php
use Sanvex\Core\SanvexManager;

public function index(SanvexManager $manager)
{
    $github = $manager->resolveDriver('github');

    return $github->repositories()->list(['per_page' => 10]);
}
```

---

## Multi-tenancy

**Why:** In a SaaS or multi-workspace app, each customer has their own tokens and synced entities. Resolving a driver **for** an owner keeps credentials and DB-backed state partitioned instead of sharing one global integration.

**How:** Pass an Eloquent model (or an object implementing `Sanvex\Core\Contracts\SanvexOwner`) into `for()` before `resolveDriver()`.

```php
// Per authenticated user
$linear = $manager->for(auth()->user())->resolveDriver('linear');
$issues = $linear->issues()->list(['limit' => 20]);
```

```php
// Explicit owner key via CLI when storing credentials
// php artisan sanvex:setup slack --bot-token=xoxb-... --owner-type=App\\Models\\Workspace --owner-id=42
$slack = $manager->for($workspace)->resolveDriver('slack');
```

```php
// Optional app-wide default owner binding
app()->bind('sanvex.current_owner', fn () => auth()->user());
$github = app(SanvexManager::class)->for(app('sanvex.current_owner'))->resolveDriver('github');
```

Global (single-tenant) behavior is unchanged: `resolveDriver('github')` is equivalent to `for(null)->resolveDriver('github')`.

---

## AI integration

### Generic tool shape (any LLM)

Expose one tool that mirrors Sanvex’s calling convention; your backend resolves the driver and forwards the call.

```json
{
  "name": "sanvex_action",
  "description": "Call a Sanvex driver resource action.",
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
$driver = $manager->for($tenant)->resolveDriver($instruction['driver']);
$result = $driver->{$instruction['resource']}()->{$instruction['action']}($instruction['args'] ?? []);
```

For tenant-aware agents, resolve `$tenant` the same way you would for a normal HTTP request (session, JWT, etc.).

### MCP (Cursor, Claude Desktop, other stdio clients)

The MCP package registers `php artisan sanvex:mcp-stdio`, which runs a JSON-RPC loop over stdin/stdout. Point your client at the Laravel app’s PHP binary and that Artisan command; set `cwd` to the project root that contains `artisan`.

Tools exposed by the server include `sanvex_list_operations`, `sanvex_get_schema`, `sanvex_setup`, and (if enabled in config) `sanvex_run_script`.

**HTTP SSE (optional):** set `SANVEX_MCP_ENABLE_SERVER=true` (and `mcp.enable_server` true) to register `GET /sanvex/mcp/sse` and `POST /sanvex/mcp/message` for SSE-based MCP transports.

---

## CLI reference


| Command                     | Purpose                                                                                             |
| --------------------------- | --------------------------------------------------------------------------------------------------- |
| `sanvex:list`               | Registered drivers and auth metadata                                                                |
| `sanvex:setup {driver}`     | Store credentials (options: `--api-key`, `--bot-token`, `--owner-type`, `--owner-id`, `--backfill`) |
| `sanvex:migrate`            | Run Sanvex migrations from vendor path                                                              |
| `sanvex:backfill {driver}`  | Pull API data into `sv_entities` (optional `--owner-type` / `--owner-id`)                           |
| `sanvex:keygen`             | Helper for encryption/key material                                                                  |
| `sanvex:make-driver {name}` | Scaffold a new driver package                                                                       |
| `sanvex:mcp-stdio`          | Start MCP stdio server                                                                              |


---

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.