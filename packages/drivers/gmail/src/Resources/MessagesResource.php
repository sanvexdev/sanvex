<?php

namespace Sanvex\Drivers\Gmail\Resources;

use Sanvex\Core\BaseResource;
use Sanvex\Core\Attributes\Operation;

class MessagesResource extends BaseResource
{
    private const BASE_URL = 'https://gmail.googleapis.com/gmail/v1/users/me';

    #[Operation(
        description: 'List Gmail messages for the authenticated user.',
        readOnly: true,
        schema: [
            'q' => ['type' => 'string', 'description' => 'Gmail search query (e.g. "from:user@example.com")'],
            'maxResults' => ['type' => 'integer', 'description' => 'Max number of messages to return'],
            'pageToken' => ['type' => 'string', 'description' => 'Page token for pagination'],
        ],
        responseFields: ['messages', 'nextPageToken', 'resultSizeEstimate'],
    )]
    public function list(array $args = []): array
    {
        return $this->driver->get(self::BASE_URL . '/messages', $args);
    }

    #[Operation(
        description: 'Get a specific Gmail message by ID.',
        readOnly: true,
        schema: [
            'id' => ['type' => 'string', 'required' => true, 'description' => 'Message ID'],
        ],
        responseFields: ['id', 'threadId', 'labelIds', 'snippet', 'historyId', 'internalDate', 'payload', 'sizeEstimate'],
    )]
    public function get(array $args): array
    {
        $id = $args['id'];
        return $this->driver->get(self::BASE_URL . "/messages/{$id}", $args);
    }

    #[Operation(
        description: 'Send a new email message.',
        schema: [
            'raw' => ['type' => 'string', 'required' => true, 'description' => 'Base64url-encoded RFC 2822 email message'],
        ],
    )]
    public function send(array $args): array
    {
        return $this->driver->post(self::BASE_URL . '/messages/send', $args);
    }

    #[Operation(
        description: 'Delete a Gmail message permanently. This cannot be undone.',
        schema: [
            'id' => ['type' => 'string', 'required' => true, 'description' => 'Message ID to delete'],
        ],
    )]
    public function delete(array $args): array
    {
        $id = $args['id'];
        return $this->driver->delete(self::BASE_URL . "/messages/{$id}");
    }
}
