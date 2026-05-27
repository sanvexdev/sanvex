<?php

namespace Sanvex\LaravelAi\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use ReflectionAttribute;
use ReflectionMethod;
use Sanvex\Core\BaseDriver;
use Sanvex\Core\Attributes\Operation;
use Stringable;
use Throwable;

class SanvexOperationTool implements Tool
{
    private ?Operation $attribute;

    public function __construct(
        private readonly BaseDriver $driver,
        private readonly string $driverId,
        private readonly string $resourceName,
        private readonly string $actionName,
        private readonly object $resource,
    ) {
        $this->attribute = $this->resolveAttribute();
    }

    public function name(): string
    {
        return ToolFactory::className($this->driverId, $this->resourceName, $this->actionName);
    }

    public function description(): Stringable|string
    {
        if ($this->attribute && $this->attribute->description !== '') {
            return $this->attribute->description;
        }

        $action = ucfirst($this->actionName);
        return "{$action} {$this->resourceName} via {$this->driverId}";
    }

    public function schema(JsonSchema $schema): array
    {
        if ($this->attribute && $this->attribute->schema !== []) {
            return $this->buildSchemaFromAttribute($schema);
        }

        return [
            'args' => $schema->object()->description('Arguments object to pass to the operation.'),
        ];
    }

    public function handle(Request $request): Stringable|string
    {
        try {
            $args = $request['args'] ?? $request->toArray();

            if (isset($args['args']) && count($args) === 1) {
                $args = (array) $args['args'];
            }

            $result = $this->resource->{$this->actionName}($args);

            if (is_array($result)) {
                $result = $this->filterResponseFields($result);
            }

            if (is_array($result) && array_is_list($result) && count($result) > 10) {
                $result = array_slice($result, 0, 5);
                $result[] = ['note' => 'Results truncated to top 5 records.'];
            }

            return is_string($result) ? $result : json_encode($result, JSON_PRETTY_PRINT);
        } catch (Throwable $e) {
            return "Sanvex error: {$e->getMessage()}";
        }
    }

    public function isReadOnly(): bool
    {
        if ($this->attribute) {
            return $this->attribute->readOnly;
        }

        return in_array($this->actionName, ['list', 'get', 'search', 'find', 'show'], true);
    }

    private function resolveAttribute(): ?Operation
    {
        try {
            $ref = new ReflectionMethod($this->resource, $this->actionName);
            $attrs = $ref->getAttributes(Operation::class, ReflectionAttribute::IS_INSTANCEOF);

            if ($attrs !== []) {
                return $attrs[0]->newInstance();
            }
        } catch (Throwable) {
        }

        return null;
    }

    private function filterResponseFields(array $result): array
    {
        if (! $this->attribute || $this->attribute->responseFields === []) {
            return $result;
        }

        $fields = array_flip($this->attribute->responseFields);

        $isList = array_is_list($result);

        if ($isList) {
            return array_map(
                fn (mixed $item): mixed => is_array($item) ? array_intersect_key($item, $fields) : $item,
                $result,
            );
        }

        return array_intersect_key($result, $fields);
    }

    private function buildSchemaFromAttribute(JsonSchema $schema): array
    {
        $result = [];

        foreach ($this->attribute->schema as $key => $definition) {
            $type = $definition['type'] ?? 'string';
            $field = match ($type) {
                'integer', 'int' => $schema->integer(),
                'boolean', 'bool' => $schema->boolean(),
                'array' => $schema->array(),
                'object' => $schema->object(),
                default => $schema->string(),
            };

            if (! empty($definition['description'])) {
                $field = $field->description($definition['description']);
            }

            if (! empty($definition['required'])) {
                $field = $field->required();
            }

            $result[$key] = $field;
        }

        return $result;
    }
}
