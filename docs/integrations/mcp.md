---
title: MCP
---

# MCP integration

The `sanvex/mcp` package exposes Sanvex drivers as an [MCP](https://modelcontextprotocol.io/) server for IDE agents and other MCP clients.

## Install

```bash
composer require sanvex/mcp
```

## Stdio server

Start the MCP server over stdin/stdout (one JSON-RPC message per line):

```bash
php artisan sanvex:mcp-stdio
```

Use this with Claude Desktop, Cursor, or any MCP client that supports stdio transport.

## HTTP SSE (optional)

Enable HTTP transport in `.env`:

```env
SANVEX_MCP_ENABLE_SERVER=true
```

Routes registered:

| Method | Path | Purpose |
| ------ | ---- | ------- |
| GET | `/sanvex/mcp/sse` | SSE connection |
| POST | `/sanvex/mcp/message` | JSON-RPC messages |

## Available tools

| Tool | Purpose |
| ---- | ------- |
| `sanvex_list_operations` | List resources across all registered drivers |
| `sanvex_action` (via JsonRpcServer) | Execute a driver resource action |
| `sanvex_setup` | Store credentials for a driver |
| `sanvex_get_schema` | Get operation schema/metadata |
| `sanvex_run_script` | Run PHP script (disabled by default) |

## Run script tool

`RunScriptTool` is **disabled by default** because it uses `eval`. Only enable in trusted environments:

```env
SANVEX_MCP_ALLOW_RUN_SCRIPT=true
```

## Tenancy note

The bundled `JsonRpcServer` uses global `resolveDriver()` — no `for($owner)` path. Multi-tenant apps should handle owner context in application code or custom MCP tooling.

See [Tenancy](../concepts/tenancy).

## Related

- [Laravel AI integration](./laravel-ai) — alternative for Laravel AI SDK agents
- [Usage](../getting-started/usage) — direct PHP driver calls
