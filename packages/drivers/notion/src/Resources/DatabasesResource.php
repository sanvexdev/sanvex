<?php

namespace Sanvex\Drivers\Notion\Resources;

use Sanvex\Core\BaseResource;
use Sanvex\Core\Attributes\Operation;

class DatabasesResource extends BaseResource
{
    private const BASE_URL = 'https://api.notion.com/v1';

    #[Operation(
        description: 'Query a Notion database with optional filters and sorts.',
        readOnly: true,
        schema: [
            'id' => ['type' => 'string', 'required' => true, 'description' => 'Database ID'],
            'filter' => ['type' => 'object', 'description' => 'Notion filter object'],
            'sorts' => ['type' => 'array', 'description' => 'Array of sort objects'],
            'page_size' => ['type' => 'integer', 'description' => 'Max results per page'],
        ],
    )]
    public function query(array $args): array
    {
        $id = $args['database_id'] ?? $args['id'] ?? null;
        return $this->driver->post(self::BASE_URL . "/databases/{$id}/query", $args);
    }

    #[Operation(
        description: 'Get a Notion database schema and metadata by ID.',
        readOnly: true,
        schema: [
            'id' => ['type' => 'string', 'required' => true, 'description' => 'Database ID'],
        ],
    )]
    public function get(array $args): array
    {
        $id = $args['database_id'] ?? $args['id'] ?? null;
        return $this->driver->get(self::BASE_URL . "/databases/{$id}");
    }

    #[Operation(
        description: 'List all databases in the Notion workspace.',
        readOnly: true,
        schema: [
            'page_size' => ['type' => 'integer', 'description' => 'Max results to return'],
        ],
    )]
    public function list(array $args = []): array
    {
        return $this->driver->post(self::BASE_URL . '/search', array_merge($args, ['filter' => ['value' => 'database', 'property' => 'object']]));
    }

    #[Operation(
        description: 'Retrieve databases (alias for list).',
        readOnly: true,
    )]
    public function retrieve(array $args = []): array
    {
        return $this->list($args);
    }
}
