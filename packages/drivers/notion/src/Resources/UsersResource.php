<?php

namespace Sanvex\Drivers\Notion\Resources;

use Sanvex\Core\BaseResource;
use Sanvex\Core\Attributes\Operation;

class UsersResource extends BaseResource
{
    private const BASE_URL = 'https://api.notion.com/v1';

    #[Operation(
        description: 'Retrieve a Notion user (alias for get).',
        readOnly: true,
        schema: [
            'id' => ['type' => 'string', 'required' => true, 'description' => 'User ID'],
        ],
    )]
    public function retrieve(array $args): array
    {
        return $this->get($args);
    }

    #[Operation(
        description: 'Get a Notion user by ID.',
        readOnly: true,
        schema: [
            'id' => ['type' => 'string', 'required' => true, 'description' => 'User ID'],
        ],
    )]
    public function get(array $args): array
    {
        $id = $args['user_id'] ?? $args['id'] ?? null;
        return $this->driver->get(self::BASE_URL . "/users/{$id}");
    }

    #[Operation(
        description: 'List all users in the Notion workspace.',
        readOnly: true,
        schema: [
            'start_cursor' => ['type' => 'string', 'description' => 'Pagination cursor'],
            'page_size' => ['type' => 'integer', 'description' => 'Max users to return'],
        ],
    )]
    public function list(array $args = []): array
    {
        $params = [];
        
        if (isset($args['start_cursor'])) {
            $params['start_cursor'] = $args['start_cursor'];
        }
        
        if (isset($args['page_size'])) {
            $params['page_size'] = $args['page_size'];
        }

        return $this->driver->get(self::BASE_URL . '/users', $params);
    }

    #[Operation(
        description: 'Search users (alias for list).',
        readOnly: true,
    )]
    public function search(array $args = []): array
    {
        return $this->list($args);
    }
}
