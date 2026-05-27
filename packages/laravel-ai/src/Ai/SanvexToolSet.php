<?php

namespace Sanvex\LaravelAi\Ai;

use ReflectionAttribute;
use ReflectionMethod;
use Sanvex\Core\BaseDriver;
use Sanvex\Core\Attributes\Operation;
use Sanvex\Core\SanvexManager;
use Sanvex\LaravelAi\Ai\Tools\SanvexOperationTool;
use Sanvex\LaravelAi\Ai\Tools\ToolFactory;
use Throwable;

class SanvexToolSet
{
    private array $driverIds;

    private ?array $onlyOperations = null;

    private array $exceptOperations = [];

    private bool $readOnlyFilter = false;

    private static array $excludedDriverMethods = [
        'handleWebhook', 'verifySignature', 'oauth', 'oauthConfig',
        'isConfigured', 'configure', 'setManager', 'setOwner',
        'cloneForTenant', 'setKeyManager', 'keys', 'db',
        'get', 'post', 'put', 'patch', 'delete', 'resolveDriver',
    ];

    public function __construct(
        private readonly SanvexManager $manager,
        array $driverIds,
    ) {
        $this->driverIds = $driverIds;
    }

    public function only(array $operations): static
    {
        $this->onlyOperations = $operations;

        return $this;
    }

    public function except(array $operations): static
    {
        $this->exceptOperations = $operations;

        return $this;
    }

    public function readOnly(): static
    {
        $this->readOnlyFilter = true;

        return $this;
    }

    public function tools(): array
    {
        $tools = [];

        foreach ($this->driverIds as $driverId) {
            try {
                $driver = $this->manager->resolveDriver($driverId);
            } catch (Throwable) {
                continue;
            }

            foreach ($this->discoverResources($driver) as $resourceName => $resource) {
                foreach ($this->discoverActions($resource) as $actionName) {
                    $tool = ToolFactory::make(
                        $driver,
                        $driverId,
                        $resourceName,
                        $actionName,
                        $resource,
                    );

                    if (! $this->passesFilters($tool, $driverId, $resourceName, $actionName)) {
                        continue;
                    }

                    $tools[] = $tool;
                }
            }
        }

        return $tools;
    }

    private function discoverResources(BaseDriver $driver): array
    {
        $resources = [];

        foreach (get_class_methods($driver) as $method) {
            if ($this->isExcludedDriverMethod($method)) {
                continue;
            }

            try {
                $result = $driver->{$method}();
            } catch (Throwable) {
                continue;
            }

            if (is_object($result)) {
                $resources[$method] = $result;
            }
        }

        return $resources;
    }

    private function discoverActions(object $resource): array
    {
        $methods = array_values(array_filter(
            get_class_methods($resource),
            fn (string $m): bool => ! str_starts_with($m, '__'),
        ));

        $annotatedMethods = array_values(array_filter(
            $methods,
            fn (string $method): bool => $this->hasOperationAttribute($resource, $method),
        ));

        return $annotatedMethods !== [] ? $annotatedMethods : $methods;
    }

    private function passesFilters(
        SanvexOperationTool $tool,
        string $driverId,
        string $resourceName,
        string $actionName,
    ): bool {
        if ($this->onlyOperations !== null && ! $this->matchesAny($this->onlyOperations, $driverId, $resourceName, $actionName)) {
            return false;
        }

        if ($this->exceptOperations !== [] && $this->matchesAny($this->exceptOperations, $driverId, $resourceName, $actionName)) {
            return false;
        }

        if ($this->readOnlyFilter && ! $tool->isReadOnly()) {
            return false;
        }

        return true;
    }

    private function matchesAny(array $patterns, string $driverId, string $resourceName, string $actionName): bool
    {
        foreach ($patterns as $pattern) {
            $parts = explode('.', $pattern);

            $matched = match (count($parts)) {
                1 => $parts[0] === $resourceName,
                2 => $this->matchesTwoPart($parts, $driverId, $resourceName, $actionName),
                3 => $parts[0] === $driverId && $parts[1] === $resourceName && $parts[2] === $actionName,
                default => false,
            };

            if ($matched) {
                return true;
            }
        }

        return false;
    }

    // "a.b" is either driver.resource or resource.action depending on whether $parts[0] is the driver id.
    private function matchesTwoPart(array $parts, string $driverId, string $resourceName, string $actionName): bool
    {
        if ($parts[0] === $driverId && $parts[1] === $resourceName) {
            return true;
        }

        if ($parts[0] === $resourceName && $parts[1] === $actionName) {
            return true;
        }

        return false;
    }

    private function isExcludedDriverMethod(string $method): bool
    {
        return in_array($method, self::$excludedDriverMethods, true)
            || str_starts_with($method, '__');
    }

    private function hasOperationAttribute(object $resource, string $method): bool
    {
        try {
            $reflection = new ReflectionMethod($resource, $method);

            return $reflection->getAttributes(Operation::class, ReflectionAttribute::IS_INSTANCEOF) !== [];
        } catch (Throwable) {
            return false;
        }
    }
}
