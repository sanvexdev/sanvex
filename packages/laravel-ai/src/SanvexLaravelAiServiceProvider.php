<?php

namespace Sanvex\LaravelAi;

use Illuminate\Support\ServiceProvider;
use Sanvex\Core\SanvexManager;
use Sanvex\LaravelAi\Ai\SanvexActionExecutor;
use Sanvex\LaravelAi\Ai\SanvexAi;

class SanvexLaravelAiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SanvexActionExecutor::class);

        $this->app->singleton(SanvexAi::class, function ($app) {
            return new SanvexAi(
                $app->make(SanvexManager::class),
                $app->make(SanvexActionExecutor::class),
            );
        });

        $this->app->alias(SanvexAi::class, 'sanvex.ai');
    }
}

