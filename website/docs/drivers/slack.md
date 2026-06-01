---
title: Slack
---

# Slack driver

**Package:** `sanvex/slack`  
**Driver id:** `slack`  
**Auth:** `api_key`, `oauth2` (default: `api_key`)

## Install

Requires `sanvex/core`, `sanvex/cli`, and `php artisan migrate` first ([Installation](../getting-started/installation)).

```bash
composer require sanvex/slack
```

## Setup

### Bot token (recommended)

```bash
php artisan sanvex:setup slack --bot-token="xoxb-..."
```

### User token

```bash
php artisan sanvex:setup slack --api-key="xoxp-..."
```

Token resolution priority: `bot_token` → `api_key` → `access_token`.

### OAuth

OAuth credentials can be stored via `SlackKeyBuilder::setOAuthCredentials()`, but this repo does not ship Slack OAuth routes.

## Usage

```php
$slack = $manager->resolveDriver('slack');

$slack->channels()->list();
$slack->messages()->post(['channel' => 'C123', 'text' => 'Hello']);
$slack->users()->info(['user' => 'U123']);
```

## Resources

### `channels`

| Action | Description |
| ------ | ----------- |
| `list` | List channels |
| `info` | Get channel info |
| `join` | Join a channel |

### `messages`

| Action | Description |
| ------ | ----------- |
| `post` | Post a message |
| `list` | List messages (conversation history) |
| `delete` | Delete a message |
| `update` | Update a message |

### `users`

| Action | Description |
| ------ | ----------- |
| `list` | List users |
| `info` | Get user info |
| `lookupByEmail` | Lookup user by email |

API base: `https://slack.com/api`

## Local DB

```php
$slack->db()->messages()->list();
$slack->db()->channels()->list();
```

## Credential keys

| Key | Purpose |
| --- | ------- |
| `bot_token` | Bot user OAuth token |
| `api_key` | User token |
| `access_token` | OAuth access token |
