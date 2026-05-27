<?php

namespace Sanvex\Drivers\GitHub\Resources;

use Sanvex\Core\BaseResource;
use Sanvex\Core\Attributes\Operation;

class PullRequestsResource extends BaseResource
{
    private const BASE_URL = 'https://api.github.com';

    #[Operation(
        description: 'List pull requests for a repository.',
        readOnly: true,
        schema: [
            'owner' => ['type' => 'string', 'required' => true, 'description' => 'Repository owner'],
            'repo' => ['type' => 'string', 'required' => true, 'description' => 'Repository name'],
        ],
        responseFields: ['id', 'number', 'title', 'state', 'html_url', 'body', 'user', 'head', 'base', 'labels', 'assignee', 'assignees', 'milestone', 'locked', 'draft', 'created_at', 'updated_at', 'closed_at', 'merged_at'],
    )]
    public function list(array $args = []): array
    {
        $owner = $args['owner'];
        $repo = $args['repo'];
        return $this->driver->get(self::BASE_URL . "/repos/{$owner}/{$repo}/pulls", $args);
    }

    #[Operation(
        description: 'Get a single pull request by number.',
        readOnly: true,
        schema: [
            'owner' => ['type' => 'string', 'required' => true, 'description' => 'Repository owner'],
            'repo' => ['type' => 'string', 'required' => true, 'description' => 'Repository name'],
            'number' => ['type' => 'integer', 'required' => true, 'description' => 'Pull request number'],
        ],
        responseFields: ['id', 'number', 'title', 'state', 'html_url', 'body', 'user', 'head', 'base', 'labels', 'assignee', 'assignees', 'milestone', 'locked', 'draft', 'merged', 'mergeable', 'comments', 'review_comments', 'commits', 'additions', 'deletions', 'changed_files', 'created_at', 'updated_at', 'closed_at', 'merged_at', 'merge_commit_sha'],
    )]
    public function get(array $args): array
    {
        $owner = $args['owner'];
        $repo = $args['repo'];
        $number = $args['number'];
        return $this->driver->get(self::BASE_URL . "/repos/{$owner}/{$repo}/pulls/{$number}");
    }

    #[Operation(
        description: 'Create a new pull request.',
        schema: [
            'owner' => ['type' => 'string', 'required' => true, 'description' => 'Repository owner'],
            'repo' => ['type' => 'string', 'required' => true, 'description' => 'Repository name'],
            'title' => ['type' => 'string', 'required' => true, 'description' => 'PR title'],
            'head' => ['type' => 'string', 'required' => true, 'description' => 'Branch to merge from'],
            'base' => ['type' => 'string', 'required' => true, 'description' => 'Branch to merge into'],
            'body' => ['type' => 'string', 'description' => 'PR description'],
        ],
    )]
    public function create(array $args): array
    {
        $owner = $args['owner'];
        $repo = $args['repo'];
        return $this->driver->post(self::BASE_URL . "/repos/{$owner}/{$repo}/pulls", $args);
    }

    #[Operation(
        description: 'Merge a pull request.',
        schema: [
            'owner' => ['type' => 'string', 'required' => true, 'description' => 'Repository owner'],
            'repo' => ['type' => 'string', 'required' => true, 'description' => 'Repository name'],
            'number' => ['type' => 'integer', 'required' => true, 'description' => 'Pull request number'],
        ],
    )]
    public function merge(array $args): array
    {
        $owner = $args['owner'];
        $repo = $args['repo'];
        $number = $args['number'];
        return $this->driver->put(
            self::BASE_URL . "/repos/{$owner}/{$repo}/pulls/{$number}/merge",
            $args
        );
    }
}
