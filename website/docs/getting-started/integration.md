---
title: Integration guide
description: Step-by-step checklist to install Sanvex, connect a driver, and verify — without getting stuck.
---

# Integration guide

Follow these steps in order. Skip optional steps unless you need them.

## Checklist

| Step | Action | Done when |
| ---- | ------ | --------- |
| 1 | [Install core](#step-1-install-core) | `php artisan sanvex:list` runs |
| 2 | [Install a driver](#step-2-install-a-driver) | Driver appears in `sanvex:list` |
| 3 | [Configure `.env`](#step-3-configure-env) | Env vars set for your auth mode |
| 4 | [Connect credentials](#step-4-connect-credentials) | `isConfigured()` is `true` |
| 5 | [Verify](#step-5-verify) | API call or agent tool succeeds |
| 6 | [Agents (optional)](#step-6-agents-optional) | Tools registered on Laravel AI or MCP |

---

## Step 1: Install core

**Requirements:** PHP 8.2+, Laravel 12 or 13, Composer.

```bash
composer require sanvex/core sanvex/cli
php artisan migrate
php artisan sanvex:list
```

Migrations create `sv_*` tables automatically. No separate Sanvex migrate command.

**Optional:** production encryption key:

```bash
php artisan sanvex:keygen
# Add SANVEX_KEK=... to .env (otherwise APP_KEY is used)
```

---

## Step 2: Install a driver

```bash
composer require sanvex/github   # API key — fastest path
# composer require sanvex/gmail   # OAuth only
# composer require sanvex/notion
# composer require sanvex/slack
# composer require sanvex/linear
```

```bash
php artisan sanvex:list
```

Your driver id (e.g. `github`, `gmail`) must appear in the table.

Driver packages register with Laravel automatically after Composer install. You do **not** add them to `config/sanvex.php` unless you built a custom driver class.

---

## Step 3: Configure `.env`

Open the driver’s **Configuration** page for exact variable names:

| Driver | Configuration |
| ------ | ------------- |
| GitHub | [GitHub configuration](../drivers/github/configuration) |
| Gmail | [Gmail configuration](../drivers/gmail/configuration) |
| Linear | [Linear configuration](../drivers/linear/configuration) |
| Notion | [Notion configuration](../drivers/notion/configuration) |
| Slack | [Slack configuration](../drivers/slack/configuration) |

**API key drivers (GitHub, Notion internal, Linear, Slack):** you can skip OAuth env vars. Store the token in step 4 with `sanvex:setup`.

**OAuth drivers (Gmail; Notion public integration):** set client id, secret, and redirect URI in `.env`. `APP_URL` must match your running app and the provider console redirect URL.

```env
# Example: Gmail
GMAIL_CLIENT_ID=....apps.googleusercontent.com
GMAIL_CLIENT_SECRET=...
GMAIL_REDIRECT_URI="${APP_URL}/sanvex/gmail/callback"
GMAIL_SUCCESS_REDIRECT=/
```

Publishing `config/sanvex.php` is **optional** — only if you want to edit core or driver settings in your app:

```bash
php artisan vendor:publish --tag=sanvex-config
```

See [Configuration](../concepts/configuration).

---

## Step 4: Connect credentials

Choose **one** path for your driver.

### A — API key or bot token (CLI)

```bash
php artisan sanvex:setup github --api-key="ghp_..."
php artisan sanvex:setup notion --api-key="secret_..."
php artisan sanvex:setup slack --bot-token="xoxb-..."
```

Per-user or per-team credentials:

```bash
php artisan sanvex:setup notion --api-key="secret_..." \
  --owner-type=App\\Models\\User --owner-id=1
```

See [Authentication](../concepts/authentication) and [Tenancy](../concepts/tenancy).

### B — OAuth (browser)

Built-in login routes (Gmail and Notion today):

| Driver | Start URL | When routes are active |
| ------ | --------- | ---------------------- |
| Gmail | `/sanvex/gmail/login` | `GMAIL_CLIENT_ID` is set |
| Notion | `/sanvex/notion/login` | `NOTION_CLIENT_ID` is set or `NOTION_AUTH_TYPE=oauth_2` |

1. Set OAuth env vars (step 3).
2. Open the login URL in a browser (same app as `APP_URL`).
3. Approve access at the provider.
4. You are redirected back; tokens are stored in `sv_accounts`.

Default OAuth connections use the **global** owner. For per-user OAuth, implement your own callback and use `oauth()->buildState($owner)` — see [OAuth](../concepts/oauth).

GitHub, Linear, and Slack do not ship login routes yet — store tokens with `sanvex:setup` or your own OAuth flow.

---

## Step 5: Verify

```bash
php artisan tinker --execute="dump(app(\Sanvex\Core\SanvexManager::class)->resolveDriver('github')->isConfigured());"
```

Replace `github` with your driver id. Expect `true`.

**API call:**

```php
use Sanvex\Core\SanvexManager;

app(SanvexManager::class)
    ->resolveDriver('github')
    ->repositories()
    ->list(['per_page' => 5]);
```

If this fails with `401`, the token is missing or invalid — repeat step 4.

**OAuth routes missing?** Run `php artisan route:list --path=sanvex` and confirm login/callback routes exist. If not, check client id env vars (step 3).

---

## Step 6: Agents (optional)

After step 5 succeeds:

```bash
composer require sanvex/laravel-ai
# or
composer require sanvex/mcp
```

- [Laravel AI](../integrations/laravel-ai) — `SanvexAi::driver('github')->tools()`
- [MCP](../integrations/mcp) — `php artisan sanvex:mcp-stdio`

Only register tools when `isConfigured()` is true for that driver.

---

## Troubleshooting

| Symptom | What to check |
| ------- | ------------- |
| Driver not in `sanvex:list` | `composer require sanvex/{driver}` completed; clear `php artisan optimize:clear` |
| `isConfigured()` is false | Step 4 not done, or wrong owner scope (`for($user)` vs global) |
| OAuth login returns 403 | Client id missing or empty in `.env`; run `php artisan config:clear` |
| OAuth callback “invalid state” | `APP_KEY` set and stable; cache driver working (nonce stored in cache) |
| Redirect URI mismatch | `APP_URL`, `.env` redirect URI, and provider console must match **exactly** |
| Gmail/Google errors | Gmail API enabled; OAuth consent screen configured; redirect URI registered |
| API 401 after setup | Token revoked or placeholder token; create a new token at the provider |
| Agent tools empty | Driver not configured; wrong driver id in `SanvexAi::driver(...)` |

---

## Next

- [Usage](./usage) — resources, webhooks, tenancy patterns
- [Quickstart](./quickstart) — minimal GitHub example
- [Installation](./installation) — same steps with extra notes
- [Drivers](../drivers/) — per-driver setup and resources
