---
title: Authentication
---

# Authentication

Each driver declares which auth methods it supports. Credentials are stored encrypted in `sv_accounts` and resolved at runtime through the driver's key builder.

## Auth types per driver

| Driver | Auth types | Default | CLI setup |
| ------ | ---------- | ------- | --------- |
| GitHub | `api_key`, `oauth2` | `api_key` | `--api-key` |
| Gmail | `oauth2` | `oauth2` | No dedicated CLI flow |
| Linear | `api_key`, `oauth2` | `api_key` | `--api-key` |
| Notion | `api_key`, `oauth_2` | `api_key` | `--api-key` or OAuth routes |
| Slack | `api_key`, `oauth2` | `api_key` | `--api-key`, `--bot-token` |

Run `php artisan sanvex:list` to see auth metadata for installed drivers.

## Token resolution

At runtime, `KeyBuilder::getToken()` resolves credentials in this order:

1. `api_key`
2. `bot_token`
3. `access_token`

Drivers may override this in their own key builder (Slack prioritizes `bot_token` first).

## API key / token setup (CLI)

The `sanvex:setup` command stores credentials for a driver:

```bash
# Global scope (default owner: global/default)
php artisan sanvex:setup github --api-key="ghp_..."

# Slack bot token
php artisan sanvex:setup slack --bot-token="xoxb-..."

# Tenant-scoped
php artisan sanvex:setup notion --api-key="secret_..." \
  --owner-type=App\\Models\\Team --owner-id=1
```

Both `--owner-type` and `--owner-id` must be provided together, or neither.

Optional `--backfill` runs `sanvex:backfill` after setup (placeholder — no driver backfill logic implemented yet).

## OAuth setup

### Notion (built-in routes)

Notion is the only driver with OAuth web routes in this repo.

1. Set env vars:

```env
NOTION_CLIENT_ID=...
NOTION_CLIENT_SECRET=...
NOTION_REDIRECT_URI=https://your-app.test/sanvex/notion/callback
NOTION_AUTH_TYPE=oauth_2
```

2. Visit `/sanvex/notion/login` to start OAuth
3. Callback exchanges the code and stores `access_token` via `OAuthManager`

Routes are registered when `auth_type` is `oauth_2` or `client_id` is set. The default Notion OAuth callback uses global owner scope (`resolveDriver('notion')` without `for($owner)`).

Success redirect: `NOTION_SUCCESS_REDIRECT` (default `/`).

### Other drivers (GitHub, Gmail, Linear, Slack)

These drivers declare `oauth2` support and key builders can store OAuth tokens (`access_token`, refresh tokens), but **this repo does not ship OAuth login/callback routes** for them.

To use OAuth for these drivers today, store tokens programmatically through the driver's key builder or extend your app with custom OAuth flows.

## Credential keys stored

| Key name | Used by |
| -------- | ------- |
| `api_key` | GitHub PAT, Notion integration token, Linear API key, Slack user token |
| `bot_token` | Slack bot token |
| `access_token` | OAuth access tokens (all OAuth-capable drivers) |

Driver-specific key builders may store additional keys (e.g. GitHub webhook secret, OAuth refresh/expiry metadata).

## Owner scoping

Credentials are scoped by `(owner_type, owner_id)`:

- **Global** — `owner_type=global`, `owner_id=default` (CLI default)
- **Tenant** — pass `--owner-type` and `--owner-id`, or use `SanvexManager::for($owner)` at runtime

See [Tenancy](./tenancy) for runtime scoping.

## Security notes

- Use `SANVEX_KEK` (from `sanvex:keygen`) in production instead of relying on `APP_KEY`
- MCP `RunScriptTool` is disabled by default and uses `eval` when enabled — only enable in trusted environments (`SANVEX_MCP_ALLOW_RUN_SCRIPT=true`)
