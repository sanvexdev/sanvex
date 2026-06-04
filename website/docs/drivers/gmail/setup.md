---
title: Setup
---

# Gmail setup

```bash
composer require sanvex/gmail
```

Register the app in Google Cloud and set OAuth credentials — [Gmail configuration](./configuration).

## Prerequisites

```bash
composer require sanvex/core sanvex/cli sanvex/gmail
php artisan migrate
```

Set `GMAIL_*` in `.env` — see [Configuration](./configuration). Full checklist: [Integration guide](../../getting-started/integration).

## Connect Gmail (built-in routes)

1. Set `GMAIL_CLIENT_ID`, `GMAIL_CLIENT_SECRET`, and `GMAIL_REDIRECT_URI` ([configuration](./configuration)). Redirect URI must match Google Console exactly.
2. Visit **`GET /sanvex/gmail/login`**.
3. Google redirects to your callback; Sanvex exchanges the code and stores `access_token` / `refresh_token` on the **global** owner.

Optional: `GMAIL_SUCCESS_REDIRECT` — path to redirect after a successful connect (default `/`).

## Laravel AI agent

```bash
composer require sanvex/laravel-ai
```

```php
return app(\Sanvex\LaravelAi\Ai\SanvexAi::class)
    ->driver('gmail')
    ->readOnly()
    ->tools();
```

Only expose tools after `resolveDriver('gmail')->isConfigured()` is true. See [Laravel AI](../../integrations/laravel-ai).

## Custom OAuth flow

You only need a custom flow if you want a different owner scope, callback path, or scopes than the driver ships. In that case:

1. Build the authorize URL (or call `$driver->oauth()->getAuthorizationUrl($driver->oauthConfig(), $state)`).
2. Exchange the code with `$driver->oauth()->exchangeCode($code, $driver->oauthConfig())` — Google requires client id/secret in the **POST body** (`TokenExchangeAuth::RequestBody` on `OAuthProviderConfig`).

Manual storage (if not using the callback route):

```php
use Sanvex\Core\SanvexManager;

$driver = app(SanvexManager::class)
    ->for($user)
    ->resolveDriver('gmail');

$driver->keys()->setOAuthCredentials([
    'access_token' => $tokens['access_token'],
    'refresh_token' => $tokens['refresh_token'] ?? null,
]);
```

## Verify

```php
app(SanvexManager::class)
    ->resolveDriver('gmail')
    ->isConfigured();
```

[Resources](./resources)
