<?php

namespace Sanvex\LaravelAi\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Sanvex\Core\SanvexManager;
use Sanvex\LaravelAi\Ai\SanvexActionExecutor;
use Stringable;

class SanvexActionTool implements Tool
{
    /**
     * @param  list<string>|null  $allowedDriverIds  null = all registered drivers; always allows driver "system" for introspection.
     */
    public function __construct(
        private readonly SanvexManager $manager,
        private readonly SanvexActionExecutor $executor,
        private readonly ?array $allowedDriverIds = null,
    ) {
    }

    public function description(): Stringable|string
    {
        $scope = $this->allowedDriverIds === null
            ? 'All registered Sanvex drivers.'
            : 'Allowed drivers only: '.implode(', ', $this->allowedDriverIds).' (+ system for driver list).';

        return implode(' ', array_filter([
            'Execute a Sanvex driver action.',
            $scope,
            'Tool name is exactly "SanvexActionTool".',
            'Pass arguments ONLY via the JSON arguments object (do not append JSON to the tool name).',
            'Arguments shape (all required): driver (string), resource (string), action (string), args (object — use {} if no parameters).',
            'Multi-step workflows are normal: first call system/drivers/list (or list.configured) to get resource names AND resource_actions per driver; then call the target driver/resource/action.',
            'To list drivers: { "driver": "system", "resource": "drivers", "action": "list", "args": {} }. Configured-only: action "list.configured".',
        ]));
    }

    public function schema(JsonSchema $schema): array
    {
        $driverField = $schema->string()->required();
        if ($this->allowedDriverIds !== null && $this->allowedDriverIds !== []) {
            $enum = array_values(array_unique(array_merge($this->allowedDriverIds, ['system'])));
            $driverField = $schema->string()->enum($enum)->required();
        }

        return [
            'driver' => $driverField,
            'resource' => $schema->string()->required(),
            'action' => $schema->string()->required(),
            'args' => $schema->object()->required(),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        $result = $this->executor->execute($this->manager, [
            'driver' => $request['driver'] ?? null,
            'resource' => $request['resource'] ?? null,
            'action' => $request['action'] ?? null,
            'args' => $request['args'] ?? [],
        ], $this->allowedDriverIds);

        return is_string($result) ? $result : json_encode($result, JSON_PRETTY_PRINT);
    }
}

