---
title: Setup
---

# Slack setup

Install: `composer require sanvex/slack`

**Configuration** (Slack app, scopes, `.env`, tokens): [Slack configuration](./configuration)

## Bot token

```bash
php artisan sanvex:setup slack --bot-token="xoxb-..."
```

## User token

```bash
php artisan sanvex:setup slack --api-key="xoxp-..."
```

## OAuth

Store credentials via `SlackKeyBuilder::setOAuthCredentials()` after your OAuth flow. See [configuration](./configuration).

## Usage

[Resources](./resources)
