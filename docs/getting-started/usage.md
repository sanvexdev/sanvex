---
title: Usage
---

# Usage

Resolve a driver and call a resource action:

```php
use Sanvex\Core\SanvexManager;

public function repos(SanvexManager $manager)
{
    return $manager->resolveDriver('github')
        ->repositories()
        ->list(['per_page' => 10]);
}
```

## Multi-tenancy

```php
$notion = $manager->for(auth()->user())->resolveDriver('notion');
$pages = $notion->pages()->list(['page_size' => 10]);
```

## MCP (optional)

```bash
php artisan sanvex:mcp-stdio
```

## Typical workflow

1. Add or update docs for new drivers and operations
2. Publish docs (GitHub Pages or Vercel)
3. Test agent flows against real credentials in staging
4. Refine examples based on support questions
