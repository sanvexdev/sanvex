---
title: Resources
---

# Linear resources

```php
$linear = $manager->resolveDriver('linear');

$linear->issues()->list();
$linear->issues()->create([...]);
$linear->projects()->get(['id' => 'project-id']);
```

## `issues`

| Action | Description |
| ------ | ----------- |
| `list` | List issues |
| `get` | Get an issue |
| `create` | Create an issue |
| `update` | Update an issue |

## `projects`

| Action | Description |
| ------ | ----------- |
| `list` | List projects |
| `get` | Get a project |

API: GraphQL at `https://api.linear.app/graphql`

## Local DB

```php
$linear->db()->issues()->list();
```

Entity type: `linear_issue`
