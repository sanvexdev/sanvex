# Installation

## Prerequisites

- PHP 8.2+
- Laravel 11+
- Composer

## Install packages

```bash
composer require sanvex/core sanvex/cli
# Optional: MCP server for IDE/agents
# composer require sanvex/mcp
# Drivers you need:
composer require sanvex/github
```

## Database

```bash
php artisan migrate
```

## Credentials

```bash
php artisan sanvex:list
php artisan sanvex:setup github --api-key="ghp_..."
```

For tenant-scoped keys:

```bash
php artisan sanvex:setup notion --api-key="secret_..." --owner-type=App\\Models\\Team --owner-id=1
```

## Next step

Continue with [Usage](./usage).
