<?php

namespace Sanvex\Drivers\Notion\Resources;

use Sanvex\Core\BaseResource;
use Sanvex\Core\Attributes\Operation;

class SearchResource extends BaseResource
{
    private const BASE_URL = 'https://api.notion.com/v1';

    #[Operation(
        description: 'Search across all pages and databases in the Notion workspace.',
        readOnly: true,
        schema: [
            'query' => ['type' => 'string', 'description' => 'Search query text'],
            'filter' => ['type' => 'object', 'description' => 'Filter by object type (page or database)'],
            'sort' => ['type' => 'object', 'description' => 'Sort direction and timestamp'],
            'page_size' => ['type' => 'integer', 'description' => 'Max results to return'],
        ],
    )]
    public function search(array $args = []): array
    {
        return $this->driver->post(self::BASE_URL . '/search', $args);
    }

    #[Operation(
        description: 'List workspace content (alias for search).',
        readOnly: true,
        schema: [
            'query' => ['type' => 'string', 'description' => 'Search query text'],
            'page_size' => ['type' => 'integer', 'description' => 'Max results to return'],
        ],
    )]
    public function list(array $args = []): array
    {
        return $this->search($args);
    }
}
