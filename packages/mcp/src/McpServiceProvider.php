<?php

namespace Sanvex\Mcp;

use Illuminate\Support\ServiceProvider;
use Sanvex\Core\SanvexManager;
use Sanvex\Mcp\Tools\GetSchemaTool;
use Sanvex\Mcp\Tools\ListOperationsTool;
use Sanvex\Mcp\Tools\RunScriptTool;
use Sanvex\Mcp\Tools\SetupTool;

class McpServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SetupTool::class, fn ($app) => new SetupTool($app->make(SanvexManager::class)));
        $this->app->singleton(ListOperationsTool::class, fn ($app) => new ListOperationsTool($app->make(SanvexManager::class)));
        $this->app->singleton(GetSchemaTool::class, fn ($app) => new GetSchemaTool($app->make(SanvexManager::class)));
        $this->app->singleton(RunScriptTool::class, fn ($app) => new RunScriptTool($app->make(SanvexManager::class)));
    }

    public function getTools(): array
    {
        return [
            $this->app->make(SetupTool::class),
            $this->app->make(ListOperationsTool::class),
            $this->app->make(GetSchemaTool::class),
            $this->app->make(RunScriptTool::class),
        ];
    }
}
