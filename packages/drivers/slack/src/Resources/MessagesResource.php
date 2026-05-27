<?php

namespace Sanvex\Drivers\Slack\Resources;

use Sanvex\Core\BaseResource;
use Sanvex\Core\Attributes\Operation;

class MessagesResource extends BaseResource
{
    private const BASE_URL = 'https://slack.com/api';

    #[Operation(
        description: 'Post a message to a Slack channel.',
        schema: [
            'channel' => ['type' => 'string', 'required' => true, 'description' => 'Channel ID to post to'],
            'text' => ['type' => 'string', 'required' => true, 'description' => 'Message text'],
            'thread_ts' => ['type' => 'string', 'description' => 'Thread timestamp to reply in a thread'],
        ],
    )]
    public function post(array $args): array
    {
        return $this->driver->post(self::BASE_URL . '/chat.postMessage', $args);
    }

    #[Operation(
        description: 'List messages (conversation history) in a Slack channel.',
        readOnly: true,
        schema: [
            'channel' => ['type' => 'string', 'required' => true, 'description' => 'Channel ID'],
            'limit' => ['type' => 'integer', 'description' => 'Max messages to return (default 100)'],
            'cursor' => ['type' => 'string', 'description' => 'Pagination cursor'],
            'oldest' => ['type' => 'string', 'description' => 'Only messages after this Unix timestamp'],
            'latest' => ['type' => 'string', 'description' => 'Only messages before this Unix timestamp'],
        ],
        responseFields: ['ok', 'messages', 'has_more', 'pin_count', 'response_metadata'],
    )]
    public function list(array $args = []): array
    {
        return $this->driver->get(self::BASE_URL . '/conversations.history', $args);
    }

    #[Operation(
        description: 'Delete a Slack message.',
        schema: [
            'channel' => ['type' => 'string', 'required' => true, 'description' => 'Channel ID containing the message'],
            'ts' => ['type' => 'string', 'required' => true, 'description' => 'Timestamp of the message to delete'],
        ],
    )]
    public function delete(array $args): array
    {
        return $this->driver->post(self::BASE_URL . '/chat.delete', $args);
    }

    #[Operation(
        description: 'Update an existing Slack message.',
        schema: [
            'channel' => ['type' => 'string', 'required' => true, 'description' => 'Channel ID containing the message'],
            'ts' => ['type' => 'string', 'required' => true, 'description' => 'Timestamp of the message to update'],
            'text' => ['type' => 'string', 'required' => true, 'description' => 'New message text'],
        ],
    )]
    public function update(array $args): array
    {
        return $this->driver->post(self::BASE_URL . '/chat.update', $args);
    }
}
