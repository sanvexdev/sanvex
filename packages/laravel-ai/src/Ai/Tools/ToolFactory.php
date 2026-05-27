<?php

namespace Sanvex\LaravelAi\Ai\Tools;

use Laravel\Ai\Contracts\Tool;
use Sanvex\Core\BaseDriver;

// Laravel AI uses class_basename($tool) as the provider tool name, so each operation needs its own class.
class ToolFactory
{
    private static array $registered = [];

    public static function make(
        BaseDriver $driver,
        string $driverId,
        string $resourceName,
        string $actionName,
        object $resource,
    ): Tool {
        $className = self::className($driverId, $resourceName, $actionName);

        if (! isset(self::$registered[$className])) {
            self::register($className);
        }

        return new $className($driver, $driverId, $resourceName, $actionName, $resource);
    }

    public static function className(string $driverId, string $resourceName, string $actionName): string
    {
        return 'Sanvex_'
            .self::classSegment($driverId).'_'
            .self::classSegment($resourceName).'_'
            .self::classSegment($actionName);
    }

    private static function register(string $className): void
    {
        if (! class_exists($className, false)) {
            eval("class {$className} extends \\Sanvex\\LaravelAi\\Ai\\Tools\\SanvexOperationTool {}");
        }

        self::$registered[$className] = true;
    }

    private static function classSegment(string $value): string
    {
        $segment = preg_replace('/[^A-Za-z0-9]+/', ' ', $value) ?: '';
        $segment = str_replace(' ', '', ucwords(trim($segment)));

        return $segment !== '' ? $segment : 'Unknown';
    }
}
