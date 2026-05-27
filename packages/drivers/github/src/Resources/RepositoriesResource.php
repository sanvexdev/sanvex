<?php

namespace Sanvex\Drivers\GitHub\Resources;

use Sanvex\Core\BaseResource;
use Sanvex\Core\Attributes\Operation;

class RepositoriesResource extends BaseResource
{
    private const BASE_URL = 'https://api.github.com';

    #[Operation(
        description: 'Get a specific GitHub repository by owner and name.',
        readOnly: true,
        schema: [
            'owner' => ['type' => 'string', 'required' => true, 'description' => 'Repository owner username or org'],
            'repo' => ['type' => 'string', 'required' => true, 'description' => 'Repository name'],
        ],
        responseFields: ['id', 'name', 'full_name', 'private', 'owner', 'html_url', 'description', 'fork', 'language', 'default_branch', 'visibility', 'topics', 'license', 'stargazers_count', 'watchers_count', 'forks_count', 'open_issues_count', 'size', 'archived', 'disabled', 'created_at', 'updated_at', 'pushed_at'],
    )]
    public function get(array $args): array
    {
        $owner = $args['owner'];
        $repo = $args['repo'];
        return $this->driver->get(self::BASE_URL . "/repos/{$owner}/{$repo}");
    }

    #[Operation(
        description: 'List repositories for the authenticated user.',
        readOnly: true,
        responseFields: ['id', 'name', 'full_name', 'private', 'owner', 'html_url', 'description', 'fork', 'language', 'default_branch', 'visibility', 'topics', 'license', 'stargazers_count', 'watchers_count', 'forks_count', 'open_issues_count', 'size', 'archived', 'disabled', 'created_at', 'updated_at', 'pushed_at'],
    )]
    public function list(array $args = []): array
    {
        return $this->driver->get(self::BASE_URL . '/user/repos', $args);
    }

    #[Operation(
        description: 'Create a new repository (optionally under an org).',
        schema: [
            'name' => ['type' => 'string', 'required' => true, 'description' => 'Repository name'],
            'org' => ['type' => 'string', 'description' => 'Organization name (omit for personal repo)'],
            'private' => ['type' => 'boolean', 'description' => 'Whether the repo should be private'],
        ],
    )]
    public function create(array $args): array
    {
        $org = $args['org'] ?? null;
        $url = $org
            ? self::BASE_URL . "/orgs/{$org}/repos"
            : self::BASE_URL . '/user/repos';
        return $this->driver->post($url, $args);
    }

    #[Operation(
        description: 'Delete a repository. This is destructive and cannot be undone.',
        schema: [
            'owner' => ['type' => 'string', 'required' => true, 'description' => 'Repository owner'],
            'repo' => ['type' => 'string', 'required' => true, 'description' => 'Repository name'],
        ],
    )]
    public function delete(array $args): array
    {
        $owner = $args['owner'];
        $repo = $args['repo'];
        return $this->driver->delete(self::BASE_URL . "/repos/{$owner}/{$repo}");
    }
}
