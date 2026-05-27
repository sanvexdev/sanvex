---
title: Quickstart
---

# Quickstart

Get from zero to a working GitHub integration in four steps.

## 1. Install packages

```bash
composer require sanvex/core sanvex/cli sanvex/github
```

## 2. Run migrations

```bash
php artisan migrate
```

## 3. Store credentials

```bash
php artisan sanvex:list
php artisan sanvex:setup github --api-key="ghp_..."
```

## 4. Call the driver

```php
use Sanvex\Core\SanvexManager;

public function repos(SanvexManager $manager)
{
    return $manager->resolveDriver('github')
        ->repositories()
        ->list(['per_page' => 10]);
}
```

## What's next

- [Installation](./installation) — config, KEK, multiple drivers
- [Usage](./usage) — resources, tenancy, agent integration
- [Drivers](../drivers/) — setup for GitHub, Slack, Notion, and others
