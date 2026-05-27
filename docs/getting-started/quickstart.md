# Quickstart

This quickstart gets you from nothing to a working integration.

1. Install Sanvex packages with Composer
2. Run migrations
3. Store credentials with `sanvex:setup`
4. Call a driver from PHP or expose tools to your agent

If you are new, continue with [Installation](./installation).

```bash
composer require sanvex/core sanvex/cli
composer require sanvex/github
php artisan migrate
php artisan sanvex:setup github --api-key="ghp_..."
```
