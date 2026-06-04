---
title: Configuration
---

# Configuration

Sanvex uses **`config/sanvex.php`** for core settings. Driver credentials and OAuth app settings are usually set in **`.env`** (see each driver’s Configuration page).

Publish config only when you need to edit it in your app:

```bash
php artisan vendor:publish --tag=sanvex-config
```

## Core (all apps)

| Variable | Config key | Purpose |
| -------- | ----------- | ------- |
| `SANVEX_KEK` | `kek` | Encrypts credentials in `sv_accounts` (`php artisan sanvex:keygen`) |
| `APP_KEY` | `kek` (fallback) | Used when `SANVEX_KEK` is unset |
| `SANVEX_APPROVAL_URL` | `permissions.approval_url` | Permission-guard approval URL |
| `SANVEX_MCP_ENABLE_SERVER` | `mcp.enable_server` | Enable MCP HTTP server |
| `SANVEX_MCP_ALLOW_RUN_SCRIPT` | `mcp.allow_run_script` | Allow `RunScriptTool` (default off) |

Register custom driver classes in the `drivers` array in `config/sanvex.php`.

## `driver_configs`

Optional per-driver block for OAuth client id/secret, redirect URI, and auth mode. Set matching **`.env`** variables for each driver you use — see that driver’s Configuration page.

```php
'driver_configs' => [
    '{driver_id}' => [
        'auth_type' => 'api_key',  // or oauth_2 / oauth2 — see driver docs
        'oauth' => [
            'client_id' => env('...'),
            'client_secret' => env('...'),
            'redirect_uri' => env('...'),
            'success_redirect' => env('...', '/'),
        ],
    ],
],
```

Add entries under `driver_configs` in published `config/sanvex.php` only when you want to override defaults. Otherwise `.env` is enough.

## Driver configuration guides

| Driver | Package | Configuration |
| ------ | ------- | ------------- |
| GitHub | `sanvex/github` | [GitHub configuration](../drivers/github/configuration) |
| Gmail | `sanvex/gmail` | [Gmail configuration](../drivers/gmail/configuration) |
| Linear | `sanvex/linear` | [Linear configuration](../drivers/linear/configuration) |
| Notion | `sanvex/notion` | [Notion configuration](../drivers/notion/configuration) |
| Slack | `sanvex/slack` | [Slack configuration](../drivers/slack/configuration) |

## Quick comparison

| Driver | Auth modes | Built-in OAuth login |
| ------ | ---------- | -------------------- |
| GitHub | API key, OAuth2 | No |
| Gmail | OAuth2 only | Yes — `/sanvex/gmail/login` |
| Linear | API key, OAuth2 | No |
| Notion | API key, OAuth2 | Yes — `/sanvex/notion/login` |
| Slack | API key, OAuth2 | No |

Step-by-step setup: [Integration guide](../getting-started/integration).

CLI credentials: [Authentication](./authentication). Per-user keys: [Tenancy](./tenancy).

## Related

- [Installation](../getting-started/installation)
- [Drivers](../drivers/)
