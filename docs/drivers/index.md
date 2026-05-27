---
title: Drivers
---

# Drivers

Each Sanvex driver is a separate Composer package. Install the package, run `sanvex:setup`, then call resources from PHP or expose them to agents.

## Driver matrix

| Package | Driver id | Auth types | Default auth | OAuth routes in repo |
| ------- | --------- | ---------- | ------------ | -------------------- |
| `sanvex/github` | `github` | `api_key`, `oauth2` | `api_key` | No |
| `sanvex/gmail` | `gmail` | `oauth2` | `oauth2` | No |
| `sanvex/linear` | `linear` | `api_key`, `oauth2` | `api_key` | No |
| `sanvex/notion` | `notion` | `api_key`, `oauth_2` | `api_key` | Yes |
| `sanvex/slack` | `slack` | `api_key`, `oauth2` | `api_key` | No |

Run `php artisan sanvex:list` after installing packages to confirm registration.

## Setup commands

All drivers use the same CLI:

```bash
php artisan sanvex:setup {driver} [--api-key=] [--bot-token=] [--owner-type=] [--owner-id=]
```

| Driver | Typical setup |
| ------ | ------------- |
| GitHub | `--api-key` (personal access token) |
| Gmail | OAuth token via app code (no CLI OAuth flow) |
| Linear | `--api-key` |
| Notion | `--api-key` (integration token) or OAuth via `/sanvex/notion/login` |
| Slack | `--bot-token` or `--api-key` |

See [Authentication](../concepts/authentication) for details.

## Driver pages

- [GitHub](./github)
- [Gmail](./gmail)
- [Linear](./linear)
- [Notion](./notion)
- [Slack](./slack)
