<?php

namespace Sanvex\Drivers\Slack\Resources;

use Sanvex\Core\BaseResource;
use Sanvex\Core\Attributes\Operation;

class ChannelsResource extends BaseResource
{
    private const BASE_URL = 'https://slack.com/api';

    #[Operation(
        description: 'List Slack channels the bot has access to.',
        readOnly: true,
        schema: [
            'types' => ['type' => 'string', 'description' => 'Comma-separated channel types (public_channel, private_channel, mpim, im)'],
            'limit' => ['type' => 'integer', 'description' => 'Max channels to return (default 100)'],
            'cursor' => ['type' => 'string', 'description' => 'Pagination cursor'],
        ],
        responseFields: ['ok', 'channels', 'response_metadata'],
    )]
    public function list(array $args = []): array
    {
        return $this->driver->get(self::BASE_URL . '/conversations.list', $args);
    }

    #[Operation(
        description: 'Get detailed info about a Slack channel.',
        readOnly: true,
        schema: [
            'channel' => ['type' => 'string', 'required' => true, 'description' => 'Channel ID'],
        ],
        responseFields: ['ok', 'channel'],
    )]
    public function info(array $args): array
    {
        return $this->driver->get(self::BASE_URL . '/conversations.info', $args);
    }

    #[Operation(
        description: 'Join a Slack channel.',
        schema: [
            'channel' => ['type' => 'string', 'required' => true, 'description' => 'Channel ID to join'],
        ],
    )]
    public function join(array $args): array
    {
        return $this->driver->post(self::BASE_URL . '/conversations.join', $args);
    }
}
