---
title: Setup
---

# Gmail setup

Gmail requires OAuth2 access tokens. The `sanvex:setup` command accepts `--api-key` globally, but Gmail's key builder reads `access_token` for API calls.

**This repo does not ship Gmail OAuth login/callback routes.** Store credentials in your application:

- Use `GmailKeyBuilder::setOAuthCredentials([...])` after your own OAuth flow
- Or store an `access_token` via the key manager if you obtain one externally

There is no dedicated `sanvex:setup gmail` OAuth flow in the CLI.

## Credential keys

| Key | Purpose |
| --- | ------- |
| `access_token` | OAuth access token (required at runtime) |
