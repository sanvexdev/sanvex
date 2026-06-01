---
title: Setup
---

# Linear setup

## API key

```bash
php artisan sanvex:setup linear --api-key="lin_api_..."
```

## OAuth

OAuth is supported via `LinearKeyBuilder::setOAuthToken()`. This repo does not ship Linear OAuth login/callback routes.

## Credential keys

| Key | Purpose |
| --- | ------- |
| `api_key` | Linear API key |
| `access_token` | OAuth access token |
