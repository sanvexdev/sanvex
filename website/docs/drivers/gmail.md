---
title: Gmail
---

# Gmail driver

**Package:** `sanvex/gmail`  
**Driver id:** `gmail`  
**Auth:** `oauth2` only (default: `oauth2`)

## Install

Requires `sanvex/core`, `sanvex/cli`, and `php artisan migrate` first ([Installation](../getting-started/installation)).

```bash
composer require sanvex/gmail
```

## Setup

Gmail requires OAuth2 access tokens. The `sanvex:setup` command accepts `--api-key` globally, but Gmail's key builder reads `access_token` for API calls.

**This repo does not ship Gmail OAuth login/callback routes.** Store OAuth credentials in your application:

- Use `GmailKeyBuilder::setOAuthCredentials([...])` programmatically after your own OAuth flow
- Or store an `access_token` via the key manager if you obtain one externally

There is no dedicated `sanvex:setup gmail` OAuth flow in the CLI.

## Usage

```php
$gmail = $manager->resolveDriver('gmail');

$gmail->messages()->list(['maxResults' => 10]);
$gmail->messages()->get(['id' => 'message-id']);
$gmail->messages()->send([...]);
$gmail->threads()->list();
```

## Resources

### `messages`

| Action | Description |
| ------ | ----------- |
| `list` | List messages |
| `get` | Get a message |
| `send` | Send a message |
| `delete` | Delete a message |

### `threads`

| Action | Description |
| ------ | ----------- |
| `list` | List threads |
| `get` | Get a thread |

API base: `https://gmail.googleapis.com/gmail/v1/users/me`

## Local DB

```php
$gmail->db()->messages()->list();
```

Entity type: `email_message`

## Credential keys

| Key | Purpose |
| --- | ------- |
| `access_token` | OAuth access token (required at runtime) |
