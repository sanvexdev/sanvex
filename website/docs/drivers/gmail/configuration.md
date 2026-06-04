---
title: Configuration
---

# Gmail configuration

**Package:** `sanvex/gmail` · **Driver id:** `gmail` · **Auth:** OAuth2 only

Tokens are stored in encrypted `sv_accounts`. The Gmail driver reads **`access_token`** from there at runtime.

## Google Cloud

1. [Google Cloud Console](https://console.cloud.google.com/) — create or select a project.
2. **APIs & Services → Library** — enable **Gmail API**.
3. **OAuth consent screen** — configure (use **External** if end users connect their own Gmail).
4. **Credentials → OAuth client ID** — type **Web application**.
5. **Authorized redirect URIs** — must match `GMAIL_REDIRECT_URI` (default `{APP_URL}/sanvex/gmail/callback`).

Save the **Client ID** and **Client secret** for `.env`.

## Environment variables

| Variable | Required | Maps to config | Purpose |
| -------- | -------- | -------------- | ------- |
| `GMAIL_CLIENT_ID` | Yes | `driver_configs.gmail.oauth.client_id` | Google OAuth client ID |
| `GMAIL_CLIENT_SECRET` | Yes | `driver_configs.gmail.oauth.client_secret` | Google OAuth client secret |
| `GMAIL_REDIRECT_URI` | Yes | `driver_configs.gmail.oauth.redirect_uri` | Callback (default `{APP_URL}/sanvex/gmail/callback`) |
| `GMAIL_SUCCESS_REDIRECT` | No | `driver_configs.gmail.oauth.success_redirect` | After successful connect (default `/`) |

```env
GMAIL_CLIENT_ID=....apps.googleusercontent.com
GMAIL_CLIENT_SECRET=...
GMAIL_REDIRECT_URI=https://your-app.test/sanvex/gmail/callback
GMAIL_SUCCESS_REDIRECT=/
```

## `config/sanvex.php` (optional)

Set the variables in `.env` above — that is enough for most apps.

To override defaults in published config:

```php
'driver_configs' => [
    'gmail' => [
        'oauth' => [
            'client_id' => env('GMAIL_CLIENT_ID', ''),
            'client_secret' => env('GMAIL_CLIENT_SECRET', ''),
            'redirect_uri' => env('GMAIL_REDIRECT_URI', env('APP_URL') . '/sanvex/gmail/callback'),
            'success_redirect' => env('GMAIL_SUCCESS_REDIRECT', '/'),
        ],
    ],
],
```

OAuth routes are available when `GMAIL_CLIENT_ID` is set.

## Google OAuth scopes

The built-in authorize URL requests:

| Scope | Purpose |
| ----- | ------- |
| `https://www.googleapis.com/auth/gmail.readonly` | List/read messages and threads |

For send or delete, customize `GmailDriver::oauthConfig()` scopes in your app or fork.

Authorize follows [Google’s web server flow](https://developers.google.com/identity/protocols/oauth2/web-server): `access_type=offline`, `prompt=consent`. Token exchange is `application/x-www-form-urlencoded` with `client_id` and `client_secret` in the POST body.

## Built-in Sanvex OAuth routes

**Yes** — registered when `GMAIL_CLIENT_ID` is set (via `OAuthRoutes::registerIfConfigured`).

| Route | Purpose |
| ----- | ------- |
| `GET /sanvex/gmail/login` | Start OAuth |
| `GET {GMAIL_REDIRECT_URI path}` | Exchange code, store tokens |

Default callback uses **global** owner scope (same as Notion).

Token exchange uses **form body** credentials (Google), not HTTP Basic Auth.

## Stored credentials (`sv_accounts`)

| Key | Required | Purpose |
| --- | -------- | ------- |
| `access_token` | Yes | Bearer token for Gmail API |
| `refresh_token` | Recommended | Long-lived refresh |
| `expires_at` | No | Unix expiry; core refreshes when expired |

Set `SANVEX_KEK` in production so tokens are encrypted at rest ([Installation](../../getting-started/installation)).

## What the driver reads

| Source | Used? |
| ------ | ----- |
| `sv_accounts` (`access_token`, etc.) | Yes |
| `config('sanvex.driver_configs.gmail.oauth')` | Yes (OAuth routes + `oauthConfig()`) |
| Built-in `/sanvex/gmail/*` routes | Yes (when client ID configured) |

Next: [Setup](./setup) · [Resources](./resources)
