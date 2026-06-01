---
title: GitHub
---

# GitHub driver

**Package:** `sanvex/github`  
**Driver id:** `github`  
**Auth:** `api_key`, `oauth2` (default: `api_key`)

## Install

Requires `sanvex/core`, `sanvex/cli`, and `php artisan migrate` first (see [Installation](../getting-started/installation)).

```bash
composer require sanvex/github
```

## Setup

### API key (personal access token)

```bash
php artisan sanvex:setup github --api-key="ghp_..."
```

Tenant-scoped:

```bash
php artisan sanvex:setup github --api-key="ghp_..." \
  --owner-type=App\\Models\\User --owner-id=1
```

### OAuth

The driver supports OAuth2 via `GitHubKeyBuilder::setOAuthToken()`, but this repo does not ship GitHub OAuth login/callback routes. Implement OAuth in your app or store tokens programmatically.

## Usage

```php
$github = $manager->resolveDriver('github');

$github->repositories()->list(['per_page' => 10]);
$github->issues()->create([
    'owner' => 'org',
    'repo' => 'repo',
    'title' => 'Bug report',
]);
$github->pullRequests()->merge(['owner' => 'org', 'repo' => 'repo', 'pull_number' => 42]);
```

## Resources

### `repositories`

| Action | Description |
| ------ | ----------- |
| `get` | Get a repository |
| `list` | List repositories |
| `create` | Create a repository |
| `delete` | Delete a repository |

API base: `https://api.github.com`

### `issues`

| Action | Description |
| ------ | ----------- |
| `list` | List issues |
| `get` | Get an issue |
| `create` | Create an issue |
| `update` | Update an issue |

### `pullRequests`

| Action | Description |
| ------ | ----------- |
| `list` | List pull requests |
| `get` | Get a pull request |
| `create` | Create a pull request |
| `merge` | Merge a pull request |

## Local DB

```php
$github->db()->repositories()->list();
$github->db()->issues()->list();
```

Entity types stored in `sv_entities` when synced.

## Credential keys

| Key | Purpose |
| --- | ------- |
| `api_key` | Personal access token |
| `access_token` | OAuth access token |
| `webhook_secret` | Webhook signature verification (via key builder) |
