---
title: Authentication
---

# Authentication

Each driver declares which auth methods it supports. Credentials are stored encrypted in `sv_accounts` and resolved at runtime through the driver's key builder.

## Auth types per driver

| Driver | Auth types | Default | CLI setup | Env & config guide |
| ------ | ---------- | ------- | --------- | ------------------ |
| GitHub | `api_key`, `oauth2` | `api_key` | `--api-key` | [GitHub configuration](../drivers/github/configuration) |
| Gmail | `oauth2` | `oauth2` | No dedicated CLI flow | [Gmail configuration](../drivers/gmail/configuration) |
| Linear | `api_key`, `oauth2` | `api_key` | `--api-key` | [Linear configuration](../drivers/linear/configuration) |
| Notion | `api_key`, `oauth_2` | `api_key` | `--api-key` or OAuth routes | [Notion configuration](../drivers/notion/configuration) |
| Slack | `api_key`, `oauth2` | `api_key` | `--api-key`, `--bot-token` | [Slack configuration](../drivers/slack/configuration) |

Run `php artisan sanvex:list` to see auth metadata for installed drivers.

Core `SANVEX_*` settings: [Configuration](./configuration).

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

Unified flow: [OAuth concept](./oauth) (`OAuthRoutes`, `OAuthManager`, `oauthConfig()`).

| Driver | Built-in OAuth routes | Configuration guide |
| ------ | --------------------- | ------------------- |
| Notion | Yes (`/sanvex/notion/login`) | [Notion configuration](../drivers/notion/configuration) |
| Gmail | Yes (`/sanvex/gmail/login`) | [Gmail configuration](../drivers/gmail/configuration) |
| GitHub, Linear, Slack | No | Each driver’s **Configuration** page under [Drivers](../drivers/) |

**Notion:** set `NOTION_*` env vars, then visit `/sanvex/notion/login`. Active when `NOTION_CLIENT_ID` is set or `NOTION_AUTH_TYPE=oauth_2`.

**Gmail:** set `GMAIL_*` env vars, then visit `/sanvex/gmail/login`. Active when `GMAIL_CLIENT_ID` is set.

Both use global owner scope by default. See [Integration guide](../getting-started/integration).

**GitHub, Linear, Slack:** no packaged login/callback routes yet. Follow each driver’s configuration page for provider registration and env vars, then store tokens via the driver key builder or `oauth()->storeTokens()`.

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
