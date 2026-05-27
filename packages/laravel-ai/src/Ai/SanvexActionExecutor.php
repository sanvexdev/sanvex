<?php

namespace Sanvex\LaravelAi\Ai;

use ReflectionAttribute;
use ReflectionMethod;
use Sanvex\Core\Attributes\Operation;
use Sanvex\Core\SanvexManager;
use Throwable;

class SanvexActionExecutor
{
    public function metadata(SanvexManager $manager, ?array $onlyDriverIds = null): array
    {
        $drivers = [];

        foreach ($manager->getRegisteredDriverIds() as $driverId) {
            if ($onlyDriverIds !== null && ! in_array($driverId, $onlyDriverIds, true)) {
                continue;
            }
            try {
                $driver = $manager->resolveDriver($driverId);
                $resources = [];

                foreach (get_class_methods($driver) as $driverMethod) {
                    if ($this->isExcludedDriverMethod($driverMethod)) {
                        continue;
                    }

                    try {
                        $resource = $driver->{$driverMethod}();
                    } catch (Throwable) {
                        continue;
                    }

                    if (! is_object($resource)) {
                        continue;
                    }

                    $resources[$driverMethod] = $this->discoverActions($resource);
                }

                $drivers[$driverId] = [
                    'configured' => $driver->isConfigured(),
                    'resources' => $resources,
                ];
            } catch (Throwable $e) {
                $drivers[$driverId] = [
                    'configured' => false,
                    'resources' => [],
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $drivers;
    }

    public function execute(SanvexManager $manager, array $instruction, ?array $allowedDriverIds = null): array|string
    {
        $driverId = $instruction['driver'] ?? null;
        $resource = $instruction['resource'] ?? null;
        $action = $instruction['action'] ?? null;
        $args = $instruction['args'] ?? [];

        if (! $driverId || ! $resource || ! $action || ! is_array($args)) {
            return 'Invalid instruction. Expected driver, resource, action, args.';
        }

        if ($driverId === 'system' && $resource === 'drivers') {
            return $this->handleSystemDriversAction($manager, $action, $allowedDriverIds);
        }

        if ($allowedDriverIds !== null && ! in_array($driverId, $allowedDriverIds, true)) {
            return "Driver '{$driverId}' not allowed for this tool. Allowed: ".implode(', ', $allowedDriverIds).'.';
        }

        try {
            $driver = $manager->resolveDriver($driverId);

            if (! $driver->isConfigured()) {
                return "Driver '{$driverId}' is not configured. Run: php artisan sanvex:setup {$driverId}";
            }

            if (! method_exists($driver, $resource)) {
                return "Resource '{$resource}' not found on driver '{$driverId}'.";
            }

            $module = $driver->{$resource}();

            if (! is_object($module) || ! method_exists($module, $action)) {
                return "Action '{$action}' not supported for resource '{$resource}'.";
            }

            if (! in_array($action, $this->discoverActions($module), true)) {
                return "Action '{$action}' is not exposed for resource '{$resource}'.";
            }

            $result = $module->{$action}($args);

            if (is_array($result) && array_is_list($result) && count($result) > 10) {
                $result = array_slice($result, 0, 5);
                $result[] = ['note' => 'Results truncated to top 5 records.'];
            }

            return $result;
        } catch (Throwable $e) {
            return 'Sanvex error: '.$e->getMessage();
        }
    }

    private function isExcludedDriverMethod(string $method): bool
    {
        static $excluded = [
            'handleWebhook',
            'verifySignature',
            'oauth',
            'oauthConfig',
            'isConfigured',
            'configure',
            'setManager',
            'setOwner',
            'cloneForTenant',
            'setKeyManager',
            'keys',
            'db',
            'get',
            'post',
            'put',
            'patch',
            'delete',
            'resolveDriver',
        ];

        return in_array($method, $excluded, true) || str_starts_with($method, '__');
    }

    /**
     * @return list<string>
     */
    private function discoverActions(object $resource): array
    {
        $methods = array_values(array_filter(
            get_class_methods($resource),
            fn (string $method): bool => ! str_starts_with($method, '__')
        ));

        $annotatedMethods = array_values(array_filter(
            $methods,
            fn (string $method): bool => $this->hasOperationAttribute($resource, $method),
        ));

        return $annotatedMethods !== [] ? $annotatedMethods : $methods;
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

    private function handleSystemDriversAction(SanvexManager $manager, string $action, ?array $onlyDriverIds = null): array|string
    {
        $metadata = $this->metadata($manager, $onlyDriverIds);

        return match ($action) {
            'list', 'list.active' => [
                'drivers' => $this->formatDriverMetadata($metadata),
            ],
            'list.configured' => [
                'drivers' => $this->formatDriverMetadata($metadata, configuredOnly: true),
            ],
            default => "Unsupported system action '{$action}'. Use: list, list.active, or list.configured.",
        };
    }

    private function formatDriverMetadata(array $metadata, bool $configuredOnly = false): array
    {
        $drivers = [];

        foreach ($metadata as $id => $driverMeta) {
            $configured = (bool) ($driverMeta['configured'] ?? false);

            if ($configuredOnly && ! $configured) {
                continue;
            }

            $byResource = $driverMeta['resources'] ?? [];

            $drivers[] = [
                'id' => $id,
                'configured' => $configured,
                'resources' => array_keys($byResource),
                'resource_actions' => $byResource,
            ];
        }

        return $drivers;
    }
}

