<?php

namespace Sanvex\Drivers\Notion;

use Illuminate\Support\ServiceProvider;
use Sanvex\Core\SanvexManager;

class NotionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/notion.php', 'sanvex.driver_configs.notion');
    }

    public function boot(): void
    {
        if ($this->app->bound(SanvexManager::class)) {
            $this->app->make(SanvexManager::class)->registerDriver(NotionDriver::class);
        }

        $this->loadRoutesFrom(__DIR__.'/../routes/oauth.php');
    }
}
