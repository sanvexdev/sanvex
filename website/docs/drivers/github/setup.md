---
title: Setup
---

# GitHub setup

Install: `composer require sanvex/github`

[GitHub configuration](./configuration) · [Integration guide](../../getting-started/integration)

## API key (fastest)

```bash
php artisan sanvex:setup github --api-key="ghp_..."
```

Tenant-scoped:

```bash
php artisan sanvex:setup github --api-key="ghp_..." \
  --owner-type=App\\Models\\User --owner-id=1
```

## OAuth

Implement GitHub OAuth in your app, then store tokens via `GitHubKeyBuilder::setOAuthToken()`. See [configuration](./configuration).

## Usage

[Resources](./resources)
