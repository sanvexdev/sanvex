<?php

namespace Sanvex\Core;

use Illuminate\Support\ServiceProvider;

class SanvexServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/sanvex.php', 'sanvex');

        $this->app->singleton(SanvexManager::class, function ($app) {
            $config = $app['config']->get('sanvex', []);
            return SanvexManager::make($config);
        });

        $this->app->alias(SanvexManager::class, 'sanvex.manager');
    }

    public function boot(): void
    {
        // Load package migrations (if present) so they can be published/run by the application.
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');
    }
}
