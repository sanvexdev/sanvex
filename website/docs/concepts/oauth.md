---
title: OAuth
---

# OAuth

Sanvex shares **routes and token storage** across drivers. Each provider still follows **its own official OAuth rules** — we do not force one HTTP shape for every API.

## What is shared

| Piece | Role |
| ----- | ---- |
| `OAuthProviderConfig` | URLs, scopes, and **how** this provider expects token HTTP (see below) |
| `OAuthManager` | Authorize URL, signed `state`, exchange, refresh, `sv_accounts` |
| `OAuthRoutes::registerIfConfigured($driverId)` | Optional `GET /sanvex/{driver}/login` + callback when OAuth is configured |

## Provider requirements (official docs)

Configure each driver’s `oauthConfig()` to match the provider — not the other way around.

| Provider | Authorize | Token request | Credentials |
| -------- | --------- | ------------- | ----------- |
| **Google (Gmail)** | [Web server flow](https://developers.google.com/identity/protocols/oauth2/web-server) — `access_type=offline`, `prompt=consent` recommended for refresh | `POST` `application/x-www-form-urlencoded` to `https://oauth2.googleapis.com/token` | `client_id` + `client_secret` in **body** |
| **Notion** | [Authorization](https://developers.notion.com/guides/get-started/authorization) — requires `owner=user` | `POST` `application/json` to `https://api.notion.com/v1/oauth/token` | HTTP **Basic** + JSON body |

Sanvex maps these in `OAuthProviderConfig`:

- **Gmail:** `TokenExchangeAuth::RequestBody`, `TokenBodyFormat::Form`, `authorizationParams` for Google offline consent.
- **Notion:** `TokenExchangeAuth::Basic`, `TokenBodyFormat::Json`, `authorizationParams: ['owner' => 'user']`.

If a future provider differs (e.g. PKCE-only, custom headers), set the config fields on that driver — do not change Gmail/Notion settings to fit a generic helper.

## Standard URLs (when routes are enabled)

Set OAuth client id and secret in `.env` (see the driver’s Configuration page). Built-in routes register when the client id is set, or when `auth_type` is `oauth_2` / `oauth2`.

| Route | Action |
| ----- | ------ |
| `GET /sanvex/{driver}/login` | Redirect to provider (`state` is signed; embeds owner) |
| `GET {redirect_uri path}` | Verify `state`, exchange `code`, store tokens |

```env
{ DRIVER }_CLIENT_ID=
{ DRIVER }_CLIENT_SECRET=
{ DRIVER }_REDIRECT_URI="${APP_URL}/sanvex/{driver}/callback"
{ DRIVER }_SUCCESS_REDIRECT=/
```

## Adding OAuth to a new driver

1. Read the provider’s OAuth documentation (authorize URL, required query params, token `Content-Type`, where `client_secret` goes).
2. Implement `oauthConfig()` with the correct `tokenExchange`, `tokenBodyFormat`, and `authorizationParams`.
3. Add `config/{driver}.php`, merge via `mergeConfigFrom(..., 'sanvex.driver_configs.{id}')`, and `OAuthRoutes::registerIfConfigured('{id}')` in `routes/oauth.php`.
4. Document env vars on `docs/drivers/{id}/configuration.md`.

## Custom flows

Use built-in routes when the default **global** owner and shipped scopes are enough.

Use your own routes/controllers when you need different owners, scopes, or callbacks — still call `$driver->oauth()->getAuthorizationUrl()` / `exchangeCode()` with a matching `OAuthProviderConfig`.

## Drivers today

| Driver | Built-in routes | Guide |
| ------ | --------------- | ----- |
| Notion | Yes | [Notion configuration](../drivers/notion/configuration) |
| Gmail | Yes | [Gmail configuration](../drivers/gmail/configuration) |
| GitHub, Linear, Slack | Not yet | Per-driver configuration |

See [Authentication](./authentication).
