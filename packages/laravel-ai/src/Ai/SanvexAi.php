<?php

namespace Sanvex\LaravelAi\Ai;

use Laravel\Ai\Contracts\Tool;
use Sanvex\Core\SanvexManager;
use Sanvex\LaravelAi\Ai\Tools\SanvexActionTool;

class SanvexAi
{
    public function __construct(
        private readonly SanvexManager $manager,
        private readonly SanvexActionExecutor $executor,
    ) {
    }

    public function driver(string $driverId): SanvexToolSet
    {
        return new SanvexToolSet($this->manager, [$driverId]);
    }

    public function drivers(array $driverIds): SanvexToolSet
    {
        return new SanvexToolSet($this->manager, $driverIds);
    }

    public function tool(?array $allowedDriverIds = null): Tool
    {
        return new SanvexActionTool($this->manager, $this->executor, $allowedDriverIds);
    }

    public function metadata(?array $onlyDriverIds = null): array
    {
        return $this->executor->metadata($this->manager, $onlyDriverIds);
    }
}
