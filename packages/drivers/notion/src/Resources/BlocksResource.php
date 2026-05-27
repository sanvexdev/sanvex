<?php

namespace Sanvex\Drivers\Notion\Resources;

use Sanvex\Core\BaseResource;
use Sanvex\Core\Attributes\Operation;

class BlocksResource extends BaseResource
{
    private const BASE_URL = 'https://api.notion.com/v1';

    #[Operation(
        description: 'Get child blocks of a page or block by ID.',
        readOnly: true,
        schema: [
            'id' => ['type' => 'string', 'required' => true, 'description' => 'Block or page ID'],
            'start_cursor' => ['type' => 'string', 'description' => 'Pagination cursor'],
            'page_size' => ['type' => 'integer', 'description' => 'Max blocks to return'],
        ],
    )]
    public function get(array $args): array
    {
        return $this->getManyChildBlocks($args);
    }

    #[Operation(
        description: 'Retrieve child blocks (alias for get).',
        readOnly: true,
        schema: [
            'id' => ['type' => 'string', 'required' => true, 'description' => 'Block or page ID'],
        ],
    )]
    public function retrieve(array $args): array
    {
        return $this->getManyChildBlocks($args);
    }

    #[Operation(
        description: 'List child blocks of a page or block.',
        readOnly: true,
        schema: [
            'id' => ['type' => 'string', 'required' => true, 'description' => 'Block or page ID'],
            'start_cursor' => ['type' => 'string', 'description' => 'Pagination cursor'],
            'page_size' => ['type' => 'integer', 'description' => 'Max blocks to return'],
        ],
    )]
    public function list(array $args): array
    {
        return $this->getManyChildBlocks($args);
    }

    public function getManyChildBlocks(array $args): array
    {
        $id = $args['block_id'] ?? $args['id'] ?? $args['page_id'] ?? null;

        if (is_string($id) && preg_match('/([a-f0-9]{32})(?:\?|$)/i', str_replace('-', '', $id), $matches)) {
            $id = $matches[1];
        }

        $params = [];

        if (isset($args['start_cursor'])) {
            $params['start_cursor'] = $args['start_cursor'];
        }

        if (isset($args['page_size'])) {
            $params['page_size'] = $args['page_size'];
        }

        $response = $this->driver->get(self::BASE_URL."/blocks/{$id}/children", $params);

        if (isset($response['results']) && is_array($response['results'])) {
            $extractedText = [];
            foreach ($response['results'] as &$block) {
                $type = $block['type'] ?? null;
                if ($type && isset($block[$type]['rich_text'])) {
                    $text = '';
                    foreach ($block[$type]['rich_text'] as $rt) {
                        $text .= $rt['plain_text'] ?? '';
                    }
                    $block['extracted_text'] = $text;
                    if (trim($text) !== '') {
                        if ($type === 'to_do') {
                            $checked = isset($block['to_do']['checked']) && $block['to_do']['checked'] ? '[x]' : '[ ]';
                            $extractedText[] = "{$checked} {$text}";
                        } else {
                            $extractedText[] = "- {$text}";
                        }
                    }
                }
            }
            $response['sanvex_extracted_text'] = implode("\n", $extractedText);
        }

        return $response;
    }

    #[Operation(
        description: 'Append child blocks to a page or block.',
        schema: [
            'id' => ['type' => 'string', 'required' => true, 'description' => 'Block or page ID to append to'],
            'children' => ['type' => 'array', 'description' => 'Array of block objects to append'],
            'content' => ['type' => 'object', 'description' => 'Simplified content with type and text'],
        ],
    )]
    public function append(array $args): array
    {
        $id = $args['block_id'] ?? $args['id'] ?? $args['page_id'] ?? null;

        if (is_string($id) && preg_match('/([a-f0-9]{32})(?:\?|$)/i', str_replace('-', '', $id), $matches)) {
            $id = $matches[1];
        }

        $data = ['children' => $args['children'] ?? []];

        if (isset($args['content']) && empty($data['children'])) {
            $contentNode = $args['content'];
            $contentType = $contentNode['type'] ?? 'paragraph';
            $contentText = $contentNode['text']['content'] ?? (is_string($contentNode) ? $contentNode : '');

            if (! empty($contentText)) {
                $data['children'] = [
                    [
                        'object' => 'block',
                        'type' => $contentType,
                        $contentType => [
                            'rich_text' => [
                                [
                                    'type' => 'text',
                                    'text' => [
                                        'content' => $contentText,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ];
            }
        }

        return $this->driver->patch(self::BASE_URL."/blocks/{$id}/children", $data);
    }

    #[Operation(
        description: 'Update a block (appends children, alias for append).',
        schema: [
            'id' => ['type' => 'string', 'required' => true, 'description' => 'Block or page ID'],
            'children' => ['type' => 'array', 'description' => 'Array of block objects to append'],
        ],
    )]
    public function update(array $args): array
    {
        return $this->append($args);
    }
}
