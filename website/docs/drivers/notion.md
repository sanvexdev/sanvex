---
title: Notion
---

# Notion driver

**Package:** `sanvex/notion`  
**Driver id:** `notion`  
**Auth:** `api_key`, `oauth_2` (default: `api_key`)

Note: Notion uses `oauth_2` (underscore) in driver code, not `oauth2`.

## Install

Requires `sanvex/core`, `sanvex/cli`, and `php artisan migrate` first ([Installation](../getting-started/installation)).

```bash
composer require sanvex/notion
```

## Setup

### Integration token (API key)

```bash
php artisan sanvex:setup notion --api-key="secret_..."
```

### OAuth (built-in routes)

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

**Tenancy note:** The default OAuth callback uses global `resolveDriver('notion')`. Tokens are stored in global owner scope unless you customize the flow.

## Usage

```php
$notion = $manager->resolveDriver('notion');

$notion->pages()->list(['page_size' => 10]);
$notion->databases()->query(['database_id' => '...']);
$notion->search()->list(['query' => 'roadmap']);
```

## Resources

### `pages`

`get`, `retrieve`, `list`, `create`, `update`

### `databases`

`query`, `get`, `list`, `retrieve`

### `blocks`

`get`, `retrieve`, `list`, `getManyChildBlocks`, `append`, `update`

### `search`

`list`

### `users`

`retrieve`, `get`, `list`

API base: `https://api.notion.com/v1`

## Local DB

```php
$notion->db()->pages()->list();
```

Entity type: `notion_page`

## Credential keys

| Key | Purpose |
| --- | ------- |
| `api_key` | Internal integration token |
| `access_token` | OAuth access token |
