---
title: Setup
---

# Notion setup

## Integration token (API key)

```bash
php artisan sanvex:setup notion --api-key="secret_..."
```

## OAuth (built-in routes)

1. Configure `.env`:

```env
NOTION_CLIENT_ID=your-client-id
NOTION_CLIENT_SECRET=your-client-secret
NOTION_REDIRECT_URI=https://your-app.test/sanvex/notion/callback
NOTION_AUTH_TYPE=oauth_2
NOTION_SUCCESS_REDIRECT=/
```

2. Visit `/sanvex/notion/login` to authorize
3. Callback at `NOTION_REDIRECT_URI` exchanges the code and stores `access_token`

OAuth routes register when `auth_type` is `oauth_2` or `client_id` is set.

The default OAuth callback uses global `resolveDriver('notion')`. Tokens are stored in global owner scope unless you customize the flow.

## Credential keys

| Key | Purpose |
| --- | ------- |
| `api_key` | Internal integration token |
| `access_token` | OAuth access token |
