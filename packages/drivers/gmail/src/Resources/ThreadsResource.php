<?php

namespace Sanvex\Drivers\Gmail\Resources;

use Sanvex\Core\BaseResource;
use Sanvex\Core\Attributes\Operation;

class ThreadsResource extends BaseResource
{
    private const BASE_URL = 'https://gmail.googleapis.com/gmail/v1/users/me';

    #[Operation(
        description: 'List Gmail threads for the authenticated user.',
        readOnly: true,
        schema: [
            'q' => ['type' => 'string', 'description' => 'Gmail search query (e.g. "is:unread")'],
            'maxResults' => ['type' => 'integer', 'description' => 'Max number of threads to return'],
            'pageToken' => ['type' => 'string', 'description' => 'Page token for pagination'],
        ],
        responseFields: ['threads', 'nextPageToken', 'resultSizeEstimate'],
    )]
    public function list(array $args = []): array
    {
        return $this->driver->get(self::BASE_URL . '/threads', $args);
    }

    #[Operation(
        description: 'Get a specific Gmail thread with all its messages.',
        readOnly: true,
        schema: [
            'id' => ['type' => 'string', 'required' => true, 'description' => 'Thread ID'],
        ],
        responseFields: ['id', 'historyId', 'messages'],
    )]
    public function get(array $args): array
    {
        $id = $args['id'];
        return $this->driver->get(self::BASE_URL . "/threads/{$id}", $args);
    }
}
