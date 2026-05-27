<?php

namespace Sanvex\Drivers\GitHub\Resources;

use Sanvex\Core\BaseResource;
use Sanvex\Core\Attributes\Operation;

class IssuesResource extends BaseResource
{
    private const BASE_URL = 'https://api.github.com';

    #[Operation(
        description: 'List issues for a repository.',
        readOnly: true,
        schema: [
            'owner' => ['type' => 'string', 'required' => true, 'description' => 'Repository owner'],
            'repo' => ['type' => 'string', 'required' => true, 'description' => 'Repository name'],
        ],
        responseFields: ['id', 'number', 'title', 'state', 'state_reason', 'html_url', 'body', 'user', 'labels', 'assignee', 'assignees', 'milestone', 'locked', 'comments', 'pull_request', 'draft', 'created_at', 'updated_at', 'closed_at'],
    )]
    public function list(array $args = []): array
    {
        $owner = $args['owner'];
        $repo = $args['repo'];
        return $this->driver->get(self::BASE_URL . "/repos/{$owner}/{$repo}/issues", $args);
    }

    #[Operation(
        description: 'Get a single issue by number.',
        readOnly: true,
        schema: [
            'owner' => ['type' => 'string', 'required' => true, 'description' => 'Repository owner'],
            'repo' => ['type' => 'string', 'required' => true, 'description' => 'Repository name'],
            'number' => ['type' => 'integer', 'required' => true, 'description' => 'Issue number'],
        ],
        responseFields: ['id', 'number', 'title', 'state', 'state_reason', 'html_url', 'body', 'user', 'labels', 'assignee', 'assignees', 'milestone', 'locked', 'comments', 'pull_request', 'draft', 'closed_by', 'created_at', 'updated_at', 'closed_at'],
    )]
    public function get(array $args): array
    {
        $owner = $args['owner'];
        $repo = $args['repo'];
        $number = $args['number'];
        return $this->driver->get(self::BASE_URL . "/repos/{$owner}/{$repo}/issues/{$number}");
    }

    #[Operation(
        description: 'Create a new issue in a repository.',
        schema: [
            'owner' => ['type' => 'string', 'required' => true, 'description' => 'Repository owner'],
            'repo' => ['type' => 'string', 'required' => true, 'description' => 'Repository name'],
            'title' => ['type' => 'string', 'required' => true, 'description' => 'Issue title'],
            'body' => ['type' => 'string', 'description' => 'Issue body/description'],
        ],
    )]
    public function create(array $args): array
    {
        $owner = $args['owner'];
        $repo = $args['repo'];
        return $this->driver->post(self::BASE_URL . "/repos/{$owner}/{$repo}/issues", $args);
    }

    #[Operation(
        description: 'Update an existing issue.',
        schema: [
            'owner' => ['type' => 'string', 'required' => true, 'description' => 'Repository owner'],
            'repo' => ['type' => 'string', 'required' => true, 'description' => 'Repository name'],
            'number' => ['type' => 'integer', 'required' => true, 'description' => 'Issue number'],
            'title' => ['type' => 'string', 'description' => 'New title'],
            'body' => ['type' => 'string', 'description' => 'New body'],
        ],
    )]
    public function update(array $args): array
    {
        $owner = $args['owner'];
        $repo = $args['repo'];
        $number = $args['number'];
        return $this->driver->put(self::BASE_URL . "/repos/{$owner}/{$repo}/issues/{$number}", $args);
    }

    #[Operation(
        description: 'Close an issue by setting its state to closed.',
        schema: [
            'owner' => ['type' => 'string', 'required' => true, 'description' => 'Repository owner'],
            'repo' => ['type' => 'string', 'required' => true, 'description' => 'Repository name'],
            'number' => ['type' => 'integer', 'required' => true, 'description' => 'Issue number'],
        ],
    )]
    public function close(array $args): array
    {
        $owner = $args['owner'];
        $repo = $args['repo'];
        $number = $args['number'];
        return $this->driver->put(
            self::BASE_URL . "/repos/{$owner}/{$repo}/issues/{$number}",
            array_merge($args, ['state' => 'closed'])
        );
    }
}
