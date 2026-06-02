---
title: Resources
---

# Gmail resources

```php
$gmail = $manager->resolveDriver('gmail');

$gmail->messages()->list(['maxResults' => 10]);
$gmail->messages()->get(['id' => 'message-id']);
$gmail->messages()->send([...]);
$gmail->threads()->list();
```

## `messages`

| Action | Description |
| ------ | ----------- |
| `list` | List messages |
| `get` | Get a message |
| `send` | Send a message |
| `delete` | Delete a message |

## `threads`

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
