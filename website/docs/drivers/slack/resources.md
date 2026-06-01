---
title: Resources
---

# Slack resources

```php
$slack = $manager->resolveDriver('slack');

$slack->channels()->list();
$slack->messages()->post(['channel' => 'C123', 'text' => 'Hello']);
$slack->users()->info(['user' => 'U123']);
```

## `channels`

| Action | Description |
| ------ | ----------- |
| `list` | List channels |
| `info` | Get channel info |
| `join` | Join a channel |

## `messages`

| Action | Description |
| ------ | ----------- |
| `post` | Post a message |
| `list` | List messages (conversation history) |
| `delete` | Delete a message |
| `update` | Update a message |

## `users`

| Action | Description |
| ------ | ----------- |
| `list` | List users |
| `info` | Get user info |
| `lookupByEmail` | Lookup user by email |

API base: `https://slack.com/api`

## Local DB

```php
$slack->db()->messages()->list();
$slack->db()->channels()->list();
```
