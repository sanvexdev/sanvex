<?php

namespace Sanvex\Core;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Sanvex\Core\Http\Controllers\WebhookController;

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
        $this->publishes([
            __DIR__ . '/../config/sanvex.php' => config_path('sanvex.php'),
        ], 'sanvex-config');

        // Load package migrations (if present) so they can be published/run by the application.
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');

        // Automatically register the generic webhook endpoint natively as a stateless API
        Route::post('/sanvex/webhook', [WebhookController::class, 'handle']);
    }
}
