---
title: Drivers
description: Install and configure Sanvex drivers — GitHub, Gmail, Linear, Notion, Slack, and custom integrations.
---

# Drivers

Each driver is a Composer package with its own docs section in this tab. Install [Sanvex core and CLI](../getting-started/installation) first, then add the drivers you need.

```bash
composer require sanvex/github
php artisan sanvex:setup github --api-key="ghp_..."
```

## Available drivers

| Driver | Package | Auth | Docs |
| ------ | ------- | ---- | ---- |
| GitHub | `sanvex/github` | API key, OAuth2 | [GitHub](./github) |
| Gmail | `sanvex/gmail` | OAuth2 | [Gmail](./gmail) |
| Linear | `sanvex/linear` | API key, OAuth2 | [Linear](./linear) |
| Notion | `sanvex/notion` | API key, OAuth2 | [Notion](./notion) |
| Slack | `sanvex/slack` | API key, OAuth2 | [Slack](./slack) |

Use the sidebar to open a driver. Larger drivers can add sub-pages under their group later (setup, resources, webhooks, and so on).

## Setup command

All drivers share the same CLI:

```bash
php artisan sanvex:list
php artisan sanvex:setup {driver} [--api-key=] [--bot-token=] [--owner-type=] [--owner-id=]
```

See [Authentication](../concepts/authentication) for token types and tenancy.
