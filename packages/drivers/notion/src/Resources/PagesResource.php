<?php

namespace Sanvex\Drivers\Notion\Resources;

use Sanvex\Core\BaseResource;
use Sanvex\Core\Attributes\Operation;

class PagesResource extends BaseResource
{
    private const BASE_URL = 'https://api.notion.com/v1';

    #[Operation(
        description: 'Get a Notion page by ID.',
        readOnly: true,
        schema: [
            'id' => ['type' => 'string', 'required' => true, 'description' => 'Page ID or URL'],
        ],
    )]
    public function get(array $args): array
    {
        $id = $args['id'] ?? $args['page_id'] ?? null;

        if (is_string($id) && preg_match('/([a-f0-9]{32})(?:\?|$)/i', str_replace('-', '', $id), $matches)) {
            $id = $matches[1];
        }

        return $this->driver->get(self::BASE_URL . "/pages/{$id}");
    }

    #[Operation(
        description: 'Retrieve a Notion page (alias for get).',
        readOnly: true,
        schema: [
            'id' => ['type' => 'string', 'required' => true, 'description' => 'Page ID or URL'],
        ],
    )]
    public function retrieve(array $args): array
    {
        return $this->get($args);
    }

    #[Operation(
        description: 'List pages in the Notion workspace.',
        readOnly: true,
        schema: [
            'page_size' => ['type' => 'integer', 'description' => 'Max results to return (default 100)'],
        ],
    )]
    public function list(array $args = []): array
    {
        return $this->driver->search()->search([
            'filter' => ['value' => 'page', 'property' => 'object'],
            'page_size' => $args['page_size'] ?? 100
        ]);
    }

    #[Operation(
        description: 'Create a new Notion page. If no parent is provided, one will be auto-resolved.',
        schema: [
            'title' => ['type' => 'string', 'description' => 'Page title'],
            'parent' => ['type' => 'object', 'description' => 'Parent object with database_id or page_id'],
            'properties' => ['type' => 'object', 'description' => 'Page properties (Notion format)'],
            'content' => ['type' => 'object', 'description' => 'Simplified content block with type and text'],
        ],
    )]
    public function create(array $args): array
    {
        $isDummyParent = false;
        if (isset($args['parent'])) {
            $parentId = $args['parent']['database_id'] ?? $args['parent']['page_id'] ?? '';
            $isDummyParent = empty($parentId) || str_contains((string)$parentId, 'your_') || str_contains((string)$parentId, 'selected_') || str_contains((string)$parentId, 'dummy');
        }

        if (!isset($args['parent']) || $isDummyParent) {
            $searchResource = $this->driver->search();
            $results = $searchResource->search([
                'filter' => ['value' => 'page', 'property' => 'object'],
                'page_size' => 1
            ]);
            
            if (empty($results['results'])) {
                $results = $searchResource->search([
                    'filter' => ['value' => 'database', 'property' => 'object'],
                    'page_size' => 1
                ]);
                
                if (!empty($results['results'])) {
                    $args['parent'] = [
                        'type' => 'database_id',
                        'database_id' => $results['results'][0]['id']
                    ];
                }
            } else {
                $args['parent'] = [
                    'type' => 'page_id',
                    'page_id' => $results['results'][0]['id']
                ];
            }
        }
        
        if (isset($args['title']) && !isset($args['properties']['title'])) {
            $args['properties'] = $args['properties'] ?? [];
            $args['properties']['title'] = [
                'title' => [
                    [
                        'text' => [
                            'content' => $args['title']
                        ]
                    ]
                ]
            ];
            unset($args['title']);
        }

        if (isset($args['content']) && !isset($args['children'])) {
            $contentNode = $args['content'];
            $contentType = $contentNode['type'] ?? 'paragraph';
            $contentText = $contentNode['text']['content'] ?? (is_string($contentNode) ? $contentNode : '');
            
            if (!empty($contentText)) {
                $args['children'] = [
                    [
                        'object' => 'block',
                        'type' => $contentType,
                        $contentType => [
                            'rich_text' => [
                                [
                                    'type' => 'text',
                                    'text' => [
                                        'content' => $contentText
                                    ]
                                ]
                            ]
                        ]
                    ]
                ];
            }
            unset($args['content']);
        }

        return $this->driver->post(self::BASE_URL . '/pages', $args);
    }

    #[Operation(
        description: 'Update a Notion page properties.',
        schema: [
            'id' => ['type' => 'string', 'required' => true, 'description' => 'Page ID to update'],
            'properties' => ['type' => 'object', 'description' => 'Properties to update'],
        ],
    )]
    public function update(array $args): array
    {
        $id = $args['id'];
        return $this->driver->put(self::BASE_URL . "/pages/{$id}", $args);
    }
}
