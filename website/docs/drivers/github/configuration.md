---
title: Configuration
---

# GitHub configuration

**Package:** `sanvex/github` · **Driver id:** `github`

## Auth modes

| Mode | Supported | Default |
| ---- | --------- | ------- |
| API key (PAT) | Yes | Yes |
| OAuth2 | Yes | No |

## What to register (GitHub)

### API key (simplest)

1. GitHub → **Settings → Developer settings → Personal access tokens**.
2. Create a token with scopes your app needs (`repo`, `read:org`, etc.).
3. Store via CLI (see below) — no OAuth app required.

### OAuth2 (multi-user or GitHub App flow in your app)

1. GitHub → **Settings → Developer settings → OAuth Apps** (or **GitHub Apps** for app installations).
2. Set **Authorization callback URL** to match `GITHUB_REDIRECT_URI`.
3. Note **Client ID** and **Client secret** for `.env`.

This repo does **not** ship `/sanvex/github/login` routes — you implement the OAuth UI and store tokens.

## Environment variables

| Variable | Required | Maps to config | Purpose |
| -------- | -------- | -------------- | ------- |
| `GITHUB_AUTH_TYPE` | No | `driver_configs.github.auth_type` | `api_key` (default) or `oauth2` |
| `GITHUB_CLIENT_ID` | OAuth only | `driver_configs.github.oauth.client_id` | OAuth App client ID |
| `GITHUB_CLIENT_SECRET` | OAuth only | `driver_configs.github.oauth.client_secret` | OAuth App secret |
| `GITHUB_REDIRECT_URI` | OAuth only | `driver_configs.github.oauth.redirect_uri` | Callback URL |
| `GITHUB_SUCCESS_REDIRECT` | No | `driver_configs.github.oauth.success_redirect` | Post-connect redirect |

PATs are **not** stored in `.env` long-term — use `sanvex:setup` or programmatic `setApiKey()`.

## `config/sanvex.php` (optional)

Not required for API key setup. Use only if you want OAuth app settings in published config for your own OAuth flow.

```php
'github' => [
    'auth_type' => env('GITHUB_AUTH_TYPE', 'api_key'),
    'oauth' => [
        'client_id' => env('GITHUB_CLIENT_ID', ''),
        'client_secret' => env('GITHUB_CLIENT_SECRET', ''),
        'redirect_uri' => env('GITHUB_REDIRECT_URI', env('APP_URL') . '/sanvex/github/callback'),
        'success_redirect' => env('GITHUB_SUCCESS_REDIRECT', '/'),
    ],
],
```

## Stored credential keys (`sv_accounts`)

| Key | Setup method | Purpose |
| --- | ------------ | ------- |
| `api_key` | `sanvex:setup github --api-key` or `setApiKey()` | Personal access token |
| `access_token` | OAuth flow + `setOAuthToken()` | OAuth access token |
| `webhook_secret` | Key builder | Webhook signature verification |

Token resolution: `api_key` → `bot_token` → `access_token`.

## CLI

```bash
php artisan sanvex:setup github --api-key="ghp_..."
php artisan sanvex:setup github --api-key="ghp_..." \
  --owner-type=App\\Models\\User --owner-id=1
```

## Built-in Sanvex OAuth routes

**None.**

## What the driver reads at runtime

| Source | Used? |
| ------ | ----- |
| `sv_accounts` | Yes |
| `driver_configs.github` | No |

Next: [Setup](./setup) · [Resources](./resources)
