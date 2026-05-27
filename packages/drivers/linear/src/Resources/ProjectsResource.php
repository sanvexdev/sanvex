<?php

namespace Sanvex\Drivers\Linear\Resources;

use Sanvex\Core\BaseResource;
use Sanvex\Core\Attributes\Operation;

class ProjectsResource extends BaseResource
{
    private const BASE_URL = 'https://api.linear.app/graphql';

    #[Operation(
        description: 'List all Linear projects.',
        readOnly: true,
    )]
    public function list(array $args = []): array
    {
        $query = '{ projects { nodes { id name description } } }';
        return $this->driver->post(self::BASE_URL, ['query' => $query, 'variables' => $args]);
    }

    #[Operation(
        description: 'Get a specific Linear project by ID.',
        readOnly: true,
        schema: [
            'id' => ['type' => 'string', 'required' => true, 'description' => 'Project ID'],
        ],
    )]
    public function get(array $args): array
    {
        $query = '{ project(id: $id) { id name description } }';
        return $this->driver->post(self::BASE_URL, [
            'query' => $query,
            'variables' => ['id' => $args['id']],
        ]);
    }
}
