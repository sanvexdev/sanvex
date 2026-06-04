---
title: Configuration
---

# Notion configuration

**Package:** `sanvex/notion` · **Driver id:** `notion`

## Auth modes

| Mode | Supported | Default |
| ---- | --------- | ------- |
| API key (integration token) | Yes | Yes |
| OAuth2 (`oauth_2`) | Yes | No |

## What to register (Notion)

### API key

1. [Notion integrations](https://www.notion.so/my-integrations) → **New integration**.
2. Copy the **Internal Integration Secret** (`secret_...`).
3. Share pages/databases with the integration in Notion.
4. Store with `sanvex:setup notion --api-key="secret_..."`.

### OAuth2 (public integration)

1. Same integrations UI → configure as **Public** integration where applicable.
2. Set **OAuth domain & redirect URI** to match `NOTION_REDIRECT_URI`.
3. Copy **OAuth client ID** and **OAuth client secret** for `.env`.

## Environment variables

| Variable | Required | Maps to config | Purpose |
| -------- | -------- | -------------- | ------- |
| `NOTION_AUTH_TYPE` | No | `driver_configs.notion.auth_type` | `api_key` (default) or `oauth_2` |
| `NOTION_CLIENT_ID` | OAuth | `driver_configs.notion.oauth.client_id` | OAuth client ID |
| `NOTION_CLIENT_SECRET` | OAuth | `driver_configs.notion.oauth.client_secret` | OAuth client secret |
| `NOTION_REDIRECT_URI` | OAuth | `driver_configs.notion.oauth.redirect_uri` | Callback (default `{APP_URL}/sanvex/notion/callback`) |
| `NOTION_SUCCESS_REDIRECT` | No | `driver_configs.notion.oauth.success_redirect` | After successful connect (default `/`) |

## `config/sanvex.php` (optional)

Set the variables in `.env` above — that is enough for most apps.

To override defaults in published config:

```php
'driver_configs' => [
    'notion' => [
        'auth_type' => env('NOTION_AUTH_TYPE', 'api_key'),
        'oauth' => [
            'client_id' => env('NOTION_CLIENT_ID', ''),
            'client_secret' => env('NOTION_CLIENT_SECRET', ''),
            'redirect_uri' => env('NOTION_REDIRECT_URI', env('APP_URL') . '/sanvex/notion/callback'),
            'success_redirect' => env('NOTION_SUCCESS_REDIRECT', '/'),
        ],
    ],
],
```

## Stored credential keys (`sv_accounts`)

| Key | Setup method | Purpose |
| --- | ------------ | ------- |
| `api_key` | CLI `--api-key` | Integration token |
| `access_token` | OAuth callback | OAuth access token |

## Built-in Sanvex OAuth routes

**Yes** — registered when `NOTION_AUTH_TYPE=oauth_2` or `NOTION_CLIENT_ID` is set (via `OAuthRoutes::registerIfConfigured`).

| Route | Purpose |
| ----- | ------- |
| `GET /sanvex/notion/login` | Start OAuth (includes required `owner=user` per [Notion authorization](https://developers.notion.com/guides/get-started/authorization)) |
| `GET {NOTION_REDIRECT_URI path}` | Exchange code via JSON + HTTP Basic per Notion token API |

Default callback uses **global** owner scope unless you customize the flow.

Example OAuth `.env`:

```env
NOTION_CLIENT_ID=your-client-id
NOTION_CLIENT_SECRET=your-client-secret
NOTION_REDIRECT_URI=https://your-app.test/sanvex/notion/callback
NOTION_AUTH_TYPE=oauth_2
NOTION_SUCCESS_REDIRECT=/
```

## CLI

```bash
php artisan sanvex:setup notion --api-key="secret_..."
```

## What the driver reads at runtime

| Source | Used? |
| ------ | ----- |
| `driver_configs.notion` | Yes (`oauthConfig()`, route registration) |
| `sv_accounts` | Yes |

Next: [Setup](./setup) · [Resources](./resources)
