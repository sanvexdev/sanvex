---
title: Setup
---

# Notion setup

```bash
composer require sanvex/core sanvex/cli sanvex/notion
php artisan migrate
```

Env vars and provider registration: [Notion configuration](./configuration).  
Full checklist: [Integration guide](../../getting-started/integration).

## API key (integration token)

```bash
php artisan sanvex:setup notion --api-key="secret_..."
```

## OAuth (public integration)

1. Set `NOTION_*` env vars from [configuration](./configuration).
2. Visit **`/sanvex/notion/login`** in a browser.
3. Callback stores `access_token` (default: global owner scope).

## Verify

```php
app(\Sanvex\Core\SanvexManager::class)
    ->resolveDriver('notion')
    ->isConfigured();
```

[Resources](./resources)
