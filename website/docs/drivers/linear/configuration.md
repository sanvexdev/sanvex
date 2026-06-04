---
title: Configuration
---

# Linear configuration

**Package:** `sanvex/linear` · **Driver id:** `linear`

## Auth modes

| Mode | Supported | Default |
| ---- | --------- | ------- |
| API key | Yes | Yes |
| OAuth2 | Yes | No |

## What to register (Linear)

### API key

1. Linear → **Settings → Account → API** (or workspace API settings).
2. Create a **Personal API key** (`lin_api_...`).
3. Store via CLI.

### OAuth2

1. Linear → **Settings → API → OAuth applications** (or developer settings).
2. Create an application; set redirect URL to `LINEAR_REDIRECT_URI`.
3. Copy **Client ID** and **Client secret**.

No packaged Sanvex OAuth routes — implement authorize/callback in your app.

## Environment variables

| Variable | Required | Maps to config | Purpose |
| -------- | -------- | -------------- | ------- |
| `LINEAR_AUTH_TYPE` | No | `driver_configs.linear.auth_type` | `api_key` (default) or `oauth2` |
| `LINEAR_CLIENT_ID` | OAuth only | `driver_configs.linear.oauth.client_id` | OAuth client ID |
| `LINEAR_CLIENT_SECRET` | OAuth only | `driver_configs.linear.oauth.client_secret` | OAuth secret |
| `LINEAR_REDIRECT_URI` | OAuth only | `driver_configs.linear.oauth.redirect_uri` | Callback URL |
| `LINEAR_SUCCESS_REDIRECT` | No | `driver_configs.linear.oauth.success_redirect` | Post-connect redirect |

## `config/sanvex.php` (optional)

Optional — **not read by the driver today**.

```php
'linear' => [
    'auth_type' => env('LINEAR_AUTH_TYPE', 'api_key'),
    'oauth' => [
        'client_id' => env('LINEAR_CLIENT_ID', ''),
        'client_secret' => env('LINEAR_CLIENT_SECRET', ''),
        'redirect_uri' => env('LINEAR_REDIRECT_URI', env('APP_URL') . '/sanvex/linear/callback'),
        'success_redirect' => env('LINEAR_SUCCESS_REDIRECT', '/'),
    ],
],
```

## Stored credential keys (`sv_accounts`)

| Key | Setup method | Purpose |
| --- | ------------ | ------- |
| `api_key` | `sanvex:setup linear --api-key` | Linear API key |
| `access_token` | OAuth + `setOAuthToken()` | OAuth access token |

## CLI

```bash
php artisan sanvex:setup linear --api-key="lin_api_..."
```

## Built-in Sanvex OAuth routes

**None.**

## What the driver reads at runtime

| Source | Used? |
| ------ | ----- |
| `sv_accounts` | Yes |
| `driver_configs.linear` | No |

Next: [Setup](./setup) · [Resources](./resources)
