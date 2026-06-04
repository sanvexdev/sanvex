---
title: Drivers
description: Install and configure Sanvex drivers — GitHub, Gmail, Linear, Notion, Slack, and custom integrations.
---

# Drivers

Each driver has four pages: **Overview**, **Configuration**, **Setup**, and **Resources**.

**New to Sanvex?** Start with the [Integration guide](../getting-started/integration) — full checklist from install to first API call.

## Integration flow

```text
1. composer require sanvex/core sanvex/cli  →  migrate
2. composer require sanvex/{driver}
3. Set .env (Configuration page for your driver)
4. Connect: sanvex:setup (API key) OR /sanvex/{driver}/login (OAuth)
5. Verify: isConfigured() and a test API call
```

## Available drivers

| Driver | Package | Auth | Start here |
| ------ | ------- | ---- | ---------- |
| GitHub | `sanvex/github` | API key, OAuth2 | [Configure](./github/configuration) → [Setup](./github/setup) |
| Gmail | `sanvex/gmail` | OAuth2 only | [Configure](./gmail/configuration) → [Setup](./gmail/setup) |
| Linear | `sanvex/linear` | API key, OAuth2 | [Configure](./linear/configuration) → [Setup](./linear/setup) |
| Notion | `sanvex/notion` | API key, OAuth2 | [Configure](./notion/configuration) → [Setup](./notion/setup) |
| Slack | `sanvex/slack` | API key, OAuth2 | [Configure](./slack/configuration) → [Setup](./slack/setup) |

## OAuth login URLs

| Driver | URL | Requires |
| ------ | --- | -------- |
| Gmail | `/sanvex/gmail/login` | `GMAIL_CLIENT_ID` in `.env` |
| Notion | `/sanvex/notion/login` | `NOTION_CLIENT_ID` or `NOTION_AUTH_TYPE=oauth_2` |

## Setup command (API keys)

```bash
php artisan sanvex:list
php artisan sanvex:setup {driver} [--api-key=] [--bot-token=] [--owner-type=] [--owner-id=]
```

See [Authentication](../concepts/authentication) for token types and [Tenancy](../concepts/tenancy) for scoped credentials.
