---
title: Setup
---

# GitHub setup

## API key (personal access token)

```bash
php artisan sanvex:setup github --api-key="ghp_..."
```

Tenant-scoped:

```bash
php artisan sanvex:setup github --api-key="ghp_..." \
  --owner-type=App\\Models\\User --owner-id=1
```

## OAuth

The driver supports OAuth2 via `GitHubKeyBuilder::setOAuthToken()`, but this repo does not ship GitHub OAuth login/callback routes. Implement OAuth in your app or store tokens programmatically.

## Credential keys

| Key | Purpose |
| --- | ------- |
| `api_key` | Personal access token |
| `access_token` | OAuth access token |
| `webhook_secret` | Webhook signature verification (via key builder) |
