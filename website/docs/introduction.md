---
title: Introduction
---

# Introduction

Sanvex is a Laravel integration layer for external services. Your app resolves a driver by id, then calls resource actions through one consistent API:

```
SanvexManager → resolveDriver('github') → repositories()->list([...])
```

## What problem it solves

Without Sanvex, each integration needs its own HTTP client, credential storage, and agent wiring. Sanvex centralizes:

- **Driver registration** — install a Composer package, the driver auto-registers
- **Encrypted credentials** — tokens stored in `sv_accounts`, scoped per owner
- **Resource actions** — each driver exposes named resources (`repositories`, `messages`, `pages`, …) with typed methods
- **Agent exposure** — optional Laravel AI tools and MCP server over the same driver surface

## Supported drivers

| Composer package | Driver id | Auth |
| ---------------- | --------- | ---- |
| `sanvex/github` | `github` | API key, OAuth2 |
| `sanvex/gmail` | `gmail` | OAuth2 |
| `sanvex/linear` | `linear` | API key, OAuth2 |
| `sanvex/notion` | `notion` | API key, OAuth2 |
| `sanvex/slack` | `slack` | API key, OAuth2 |

Custom drivers can be registered in `config/sanvex.php`.

## Packages in the ecosystem

| Package | Purpose |
| ------- | ------- |
| `sanvex/core` | Manager, encryption, migrations, webhooks, tenancy |
| `sanvex/cli` | Artisan commands (`setup`, `list`, `migrate`, …) |
| `sanvex/mcp` | MCP stdio server + optional HTTP SSE |
| `sanvex/laravel-ai` | Laravel AI SDK tool integration |
| `sanvex/{driver}` | One package per external service |

See [Packages](./concepts/packages) for details on each.

## Next steps

- [Quickstart](./getting-started/quickstart) — install and call your first driver
- [Installation](./getting-started/installation) — full setup in a Laravel app
- [Authentication](./concepts/authentication) — tokens, OAuth, encryption
- [Drivers](./drivers/) — per-driver setup and capabilities
