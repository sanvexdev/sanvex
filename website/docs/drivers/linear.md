---
title: Linear
---

# Linear driver

**Package:** `sanvex/linear`  
**Driver id:** `linear`  
**Auth:** `api_key`, `oauth2` (default: `api_key`)

## Install

```bash
composer require sanvex/linear
php artisan migrate
```

## Setup

### API key

```bash
php artisan sanvex:setup linear --api-key="lin_api_..."
```

### OAuth

The driver supports OAuth via `LinearKeyBuilder::setOAuthToken()`, but this repo does not ship Linear OAuth login/callback routes.

## Usage

```php
$linear = $manager->resolveDriver('linear');

$linear->issues()->list();
$linear->issues()->create([...]);
$linear->projects()->get(['id' => 'project-id']);
```

## Resources

### `issues`

| Action | Description |
| ------ | ----------- |
| `list` | List issues |
| `get` | Get an issue |
| `create` | Create an issue |
| `update` | Update an issue |

### `projects`

| Action | Description |
| ------ | ----------- |
| `list` | List projects |
| `get` | Get a project |

API: GraphQL at `https://api.linear.app/graphql`

## Local DB

```php
$linear->db()->issues()->list();
```

Entity type: `linear_issue`

## Credential keys

| Key | Purpose |
| --- | ------- |
| `api_key` | Linear API key |
| `access_token` | OAuth access token |
