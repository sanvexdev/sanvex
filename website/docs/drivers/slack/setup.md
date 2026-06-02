---
title: Setup
---

# Slack setup

## Bot token (recommended)

```bash
php artisan sanvex:setup slack --bot-token="xoxb-..."
```

## User token

```bash
php artisan sanvex:setup slack --api-key="xoxp-..."
```

Token resolution priority: `bot_token` → `api_key` → `access_token`.

## OAuth

Store OAuth credentials via `SlackKeyBuilder::setOAuthCredentials()`. This repo does not ship Slack OAuth routes.

## Credential keys

| Key | Purpose |
| --- | ------- |
| `bot_token` | Bot user OAuth token |
| `api_key` | User token |
| `access_token` | OAuth access token |
