---
title: Configuration
---

# Slack configuration

**Package:** `sanvex/slack` · **Driver id:** `slack`

## Auth modes

| Mode | Supported | Default |
| ---- | --------- | ------- |
| Bot token | Yes (`bot_token`) | Recommended for bots |
| User token (`api_key`) | Yes | Optional |
| OAuth2 | Yes | No |

## What to register (Slack)

### Bot token (recommended)

1. [api.slack.com/apps](https://api.slack.com/apps) → **Create New App**.
2. **OAuth & Permissions** → add Bot Token Scopes (`chat:write`, `channels:read`, etc.).
3. **Install to Workspace** → copy **Bot User OAuth Token** (`xoxb-...`).
4. `php artisan sanvex:setup slack --bot-token="xoxb-..."`.

### User token

Use a user token (`xoxp-...`) with `sanvex:setup slack --api-key="xoxp-..."` when you need user-context API calls.

### OAuth2 (multi-workspace installs)

1. Same Slack app → **OAuth & Permissions**.
2. Add **Redirect URLs** matching `SLACK_REDIRECT_URI`.
3. Copy **Client ID** and **Client Secret** from **Basic Information**.
4. Implement OAuth in your app; store tokens with `SlackKeyBuilder::setOAuthCredentials()`.

## Environment variables

| Variable | Required | Maps to config | Purpose |
| -------- | -------- | -------------- | ------- |
| `SLACK_AUTH_TYPE` | No | `driver_configs.slack.auth_type` | `api_key` (default) or `oauth2` |
| `SLACK_CLIENT_ID` | OAuth only | `driver_configs.slack.oauth.client_id` | Slack app client ID |
| `SLACK_CLIENT_SECRET` | OAuth only | `driver_configs.slack.oauth.client_secret` | Slack app secret |
| `SLACK_REDIRECT_URI` | OAuth only | `driver_configs.slack.oauth.redirect_uri` | OAuth callback URL |
| `SLACK_SUCCESS_REDIRECT` | No | `driver_configs.slack.oauth.success_redirect` | Post-connect redirect |

Bot/user tokens belong in `sv_accounts` via CLI, not in `.env`.

## `config/sanvex.php` (optional)

Optional — **not read by the driver today**.

```php
'slack' => [
    'auth_type' => env('SLACK_AUTH_TYPE', 'api_key'),
    'oauth' => [
        'client_id' => env('SLACK_CLIENT_ID', ''),
        'client_secret' => env('SLACK_CLIENT_SECRET', ''),
        'redirect_uri' => env('SLACK_REDIRECT_URI', env('APP_URL') . '/sanvex/slack/callback'),
        'success_redirect' => env('SLACK_SUCCESS_REDIRECT', '/'),
    ],
],
```

## Stored credential keys (`sv_accounts`)

| Key | Setup method | Purpose |
| --- | ------------ | ------- |
| `bot_token` | `sanvex:setup slack --bot-token` | Bot OAuth token (`xoxb-`) |
| `api_key` | `sanvex:setup slack --api-key` | User token (`xoxp-`) |
| `access_token` | OAuth flow | OAuth access token |

Runtime token order: **`bot_token` → `api_key` → `access_token`**.

## CLI

```bash
php artisan sanvex:setup slack --bot-token="xoxb-..."
php artisan sanvex:setup slack --api-key="xoxp-..."
```

## Built-in Sanvex OAuth routes

**None.**

## What the driver reads at runtime

| Source | Used? |
| ------ | ----- |
| `sv_accounts` | Yes |
| `driver_configs.slack` | No |

Next: [Setup](./setup) · [Resources](./resources)
