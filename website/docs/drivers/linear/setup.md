---
title: Setup
---

# Linear setup

Install: `composer require sanvex/linear`

**Configuration** (API key vs OAuth, `.env`, `config/sanvex.php`): [Linear configuration](./configuration)

## API key

```bash
php artisan sanvex:setup linear --api-key="lin_api_..."
```

## OAuth

Use `LinearKeyBuilder::setOAuthToken()` after your OAuth flow. See [configuration](./configuration).

## Usage

[Resources](./resources)
