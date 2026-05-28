---
title: Database
---

# Database

Sanvex core ships five migrations that create `sv_*` tables. Run them with:

```bash
php artisan migrate
```

Or via the CLI helper:

```bash
php artisan sanvex:migrate
```

## Tables

### `sv_accounts`

Stores encrypted credentials per driver and owner.

| Column | Purpose |
| ------ | ------- |
| `owner_type` | Owner class or `global` (default) |
| `owner_id` | Owner id or `default` (default) |
| `driver` | Driver id (e.g. `github`) |
| `key_name` | Credential key (e.g. `api_key`, `access_token`, `bot_token`) |
| `encrypted_value` | Encrypted credential value |
| `encrypted_dek` | Encrypted data encryption key |

Unique constraint: `(owner_type, owner_id, driver, key_name)`.

This is where `sanvex:setup` writes tokens.

### `sv_entities`

Cached or synced entities from external services.

| Column | Purpose |
| ------ | ------- |
| `owner_type`, `owner_id` | Owner scope |
| `driver` | Driver id |
| `entity_type` | Entity kind (e.g. `repository`, `notion_page`) |
| `entity_id` | External id |
| `data` | JSON payload |

Unique constraint: `(owner_type, owner_id, driver, entity_type, entity_id)`.

Drivers expose local DB access via `$driver->db()` (e.g. `db()->repositories()`).

### `sv_events`

Webhook and event log.

| Column | Purpose |
| ------ | ------- |
| `owner_type`, `owner_id` | Owner scope |
| `driver` | Driver id |
| `event_type` | Event name |
| `payload` | JSON payload |
| `status` | Processing status |
| `error` | Error message if failed |

### `sv_permissions`

Approval records for guarded actions.

| Column | Purpose |
| ------ | ------- |
| `owner_type`, `owner_id` | Owner scope |
| `driver` | Driver id |
| `resource` | Resource name |
| `action` | Action name |
| `args` | JSON arguments |
| `approval_token` | Unique approval token |
| `status` | Default `pending` |
| `approved_at` | Timestamp when approved |

Approval URL is configured via `SANVEX_APPROVAL_URL` (default `/sanvex/approve`).

### `sv_integrations`

| Column | Purpose |
| ------ | ------- |
| `name` | Integration name |
| `driver` | Driver id |
| `is_active` | Active flag |
| `config` | JSON config |

This table is created by migration but is not read or written by current core/driver runtime code. Treat it as reserved for future use.

## Encryption

Credentials in `sv_accounts` are encrypted at rest:

1. A random DEK (data encryption key) encrypts the credential value
2. The DEK is encrypted with the KEK (key encryption key)
3. KEK comes from `SANVEX_KEK` in `.env`, falling back to `APP_KEY`

Generate a dedicated KEK:

```bash
php artisan sanvex:keygen
```

Add the output to `.env`:

```
SANVEX_KEK=base64:...
```

If no `KeyManager` is configured (no KEK), credentials are held in memory only and not persisted.
