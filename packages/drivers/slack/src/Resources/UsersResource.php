<?php

namespace Sanvex\Drivers\Slack\Resources;

use Sanvex\Core\BaseResource;
use Sanvex\Core\Attributes\Operation;

class UsersResource extends BaseResource
{
    private const BASE_URL = 'https://slack.com/api';

    #[Operation(
        description: 'List all users in the Slack workspace.',
        readOnly: true,
        schema: [
            'limit' => ['type' => 'integer', 'description' => 'Max users to return'],
            'cursor' => ['type' => 'string', 'description' => 'Pagination cursor'],
        ],
        responseFields: ['ok', 'members', 'cache_ts', 'response_metadata'],
    )]
    public function list(array $args = []): array
    {
        return $this->driver->get(self::BASE_URL . '/users.list', $args);
    }

    #[Operation(
        description: 'Get info about a specific Slack user.',
        readOnly: true,
        schema: [
            'user' => ['type' => 'string', 'required' => true, 'description' => 'User ID'],
        ],
        responseFields: ['ok', 'user'],
    )]
    public function info(array $args): array
    {
        return $this->driver->get(self::BASE_URL . '/users.info', $args);
    }

    #[Operation(
        description: 'Look up a Slack user by their email address.',
        readOnly: true,
        schema: [
            'email' => ['type' => 'string', 'required' => true, 'description' => 'Email address to look up'],
        ],
    )]
    public function lookupByEmail(array $args): array
    {
        return $this->driver->get(self::BASE_URL . '/users.lookupByEmail', $args);
    }
}
