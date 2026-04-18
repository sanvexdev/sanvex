<?php

namespace Sanvex\Drivers\Notion;

use Illuminate\Support\ServiceProvider;
use Sanvex\Core\SanvexManager;

class NotionServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->bound(SanvexManager::class)) {
            $this->app->make(SanvexManager::class)->registerDriver(NotionDriver::class);
        }
    }
}
