---
title: Quickstart
---

# Quickstart

GitHub from Composer to a live API call.

## 1. Install

```bash
composer require sanvex/core sanvex/cli sanvex/github
php artisan migrate
php artisan sanvex:list
```

You should see `github` in the table.

## 2. Connect GitHub

Create a [GitHub personal access token](https://github.com/settings/tokens), then:

```bash
php artisan sanvex:setup github --api-key="ghp_YOUR_TOKEN"
```

## 3. Call the API

```php
use Sanvex\Core\SanvexManager;

public function repos(SanvexManager $manager)
{
    return $manager->resolveDriver('github')
        ->repositories()
        ->list(['per_page' => 10]);
}
```

Use a real token in step 2. A placeholder token stores successfully but GitHub returns `401 Bad credentials` on API calls.

## Next

- [Installation](./installation) — split install, more drivers, config
- [Usage](./usage) — tenancy, webhooks, agents
- [Drivers](../drivers/github/setup) — GitHub resources and setup
