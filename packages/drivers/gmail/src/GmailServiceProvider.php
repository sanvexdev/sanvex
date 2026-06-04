<?php

namespace Sanvex\Drivers\Gmail;

use Illuminate\Support\ServiceProvider;
use Sanvex\Core\SanvexManager;

class GmailServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/gmail.php', 'sanvex.driver_configs.gmail');
    }

    public function boot(): void
    {
        if ($this->app->bound(SanvexManager::class)) {
            $this->app->make(SanvexManager::class)->registerDriver(GmailDriver::class);
        }

        $this->loadRoutesFrom(__DIR__.'/../routes/oauth.php');
    }
}
