---
title: Resources
---

# GitHub resources

```php
$github = $manager->resolveDriver('github');

$github->repositories()->list(['per_page' => 10]);
$github->issues()->create([
    'owner' => 'org',
    'repo' => 'repo',
    'title' => 'Bug report',
]);
$github->pullRequests()->merge(['owner' => 'org', 'repo' => 'repo', 'pull_number' => 42]);
```

## `repositories`

| Action | Description |
| ------ | ----------- |
| `get` | Get a repository |
| `list` | List repositories |
| `create` | Create a repository |
| `delete` | Delete a repository |

API base: `https://api.github.com`

## `issues`

| Action | Description |
| ------ | ----------- |
| `list` | List issues |
| `get` | Get an issue |
| `create` | Create an issue |
| `update` | Update an issue |

## `pullRequests`

| Action | Description |
| ------ | ----------- |
| `list` | List pull requests |
| `get` | Get a pull request |
| `create` | Create a pull request |
| `merge` | Merge a pull request |

## Local DB

```php
$github->db()->repositories()->list();
$github->db()->issues()->list();
```

Entity types are stored in `sv_entities` when synced.
