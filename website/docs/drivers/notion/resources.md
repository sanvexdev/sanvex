---
title: Resources
---

# Notion resources

```php
$notion = $manager->resolveDriver('notion');

$notion->pages()->list(['page_size' => 10]);
$notion->databases()->query(['database_id' => '...']);
$notion->search()->list(['query' => 'roadmap']);
```

## `pages`

`get`, `retrieve`, `list`, `create`, `update`

## `databases`

`query`, `get`, `list`, `retrieve`

## `blocks`

`get`, `retrieve`, `list`, `getManyChildBlocks`, `append`, `update`

## `search`

`list`

## `users`

`retrieve`, `get`, `list`

API base: `https://api.notion.com/v1`

## Local DB

```php
$notion->db()->pages()->list();
```

Entity type: `notion_page`
