---
title: Resources
---

# Gmail resources

```php
$gmail = $manager->resolveDriver('gmail');

$gmail->messages()->list(['maxResults' => 10]);
$gmail->messages()->get(['id' => 'message-id-from-list']);
$gmail->messages()->send([...]);
$gmail->threads()->list();
```

## `messages`

| Action | Description |
| ------ | ----------- |
| `list` | List messages with `subject`, `from`, `to`, `date`, and `snippet` per item |
| `get` | Get one message with flattened `subject`, `from`, `to`, `date`, and `bodyText` when `format` is `full` |
| `send` | Send a message |
| `delete` | Delete a message |

### `list` (metadata enrichment)

Gmail’s [`messages.list`](https://developers.google.com/gmail/api/reference/rest/v1/users.messages/list) returns only `id` and `threadId`. Sanvex fetches `format=metadata` per message (up to **25** per call) and returns summarized fields.

**Cost:** one list request plus up to `maxResults` metadata requests (capped at 25). Use raw Gmail API shapes only if you need bulk listing without enrichment.

Each item in `messages`:

| Field | Description |
| ----- | ----------- |
| `id` | Message ID (use with `get`) |
| `threadId` | Thread ID |
| `subject` | Email subject |
| `from` | Sender |
| `to` | Recipients |
| `date` | `Date` header or formatted `internalDate` |
| `snippet` | Gmail preview text |
| `labelIds` | Labels (optional) |

Use **`q`** for Gmail search:

```php
$gmail->messages()->list([
    'q' => 'from:alice@example.com is:unread',
    'maxResults' => 10,
]);
```

### `get`

| Arg | Description |
| --- | ----------- |
| `id` | From `list` results |
| `format` | `full` (default), `metadata`, `minimal`, or `raw` |
| `metadataHeaders` | When `format=metadata`, comma-separated or array |

Returns a flat object (`subject`, `from`, `to`, `date`, `snippet`, `bodyText` when `format=full`) instead of raw MIME `payload`—unless `format=raw`.

## `threads`

| Action | Description |
| ------ | ----------- |
| `list` | List threads (includes `snippet` from Gmail) |
| `get` | Get a thread with its messages |

## Local DB

```php
$gmail->db()->messages()->list();
```

Entity type: `email_message`

API base: `https://gmail.googleapis.com/gmail/v1/users/me`
