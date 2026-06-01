---
title: Drivers
description: Install and configure Sanvex drivers — GitHub, Gmail, Linear, Notion, Slack, and custom integrations.
---

# Drivers

Each driver has its own section in this tab: **Overview**, **Setup**, and **Resources**. Install [Sanvex core and CLI](../getting-started/installation) first.

## Available drivers

| Driver | Package | Auth | Start here |
| ------ | ------- | ---- | ---------- |
| GitHub | `sanvex/github` | API key, OAuth2 | [Overview](./github/overview) |
| Gmail | `sanvex/gmail` | OAuth2 | [Overview](./gmail/overview) |
| Linear | `sanvex/linear` | API key, OAuth2 | [Overview](./linear/overview) |
| Notion | `sanvex/notion` | API key, OAuth2 | [Overview](./notion/overview) |
| Slack | `sanvex/slack` | API key, OAuth2 | [Overview](./slack/overview) |

## Setup command

```bash
php artisan sanvex:list
php artisan sanvex:setup {driver} [--api-key=] [--bot-token=] [--owner-type=] [--owner-id=]
```

See [Authentication](../concepts/authentication) for token types and tenancy.
