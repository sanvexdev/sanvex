<?php

namespace Sanvex\Drivers\Linear\Resources;

use Sanvex\Core\BaseResource;
use Sanvex\Core\Attributes\Operation;

class IssuesResource extends BaseResource
{
    private const BASE_URL = 'https://api.linear.app/graphql';

    #[Operation(
        description: 'List all Linear issues with their title and state.',
        readOnly: true,
    )]
    public function list(array $args = []): array
    {
        $query = '{ issues { nodes { id title state { name } } } }';
        return $this->driver->post(self::BASE_URL, ['query' => $query, 'variables' => $args]);
    }

    #[Operation(
        description: 'Get a specific Linear issue by ID.',
        readOnly: true,
        schema: [
            'id' => ['type' => 'string', 'required' => true, 'description' => 'Linear issue ID'],
        ],
    )]
    public function get(array $args): array
    {
        $query = '{ issue(id: $id) { id title description state { name } } }';
        return $this->driver->post(self::BASE_URL, [
            'query' => $query,
            'variables' => ['id' => $args['id']],
        ]);
    }

    #[Operation(
        description: 'Create a new Linear issue.',
        schema: [
            'title' => ['type' => 'string', 'required' => true, 'description' => 'Issue title'],
            'description' => ['type' => 'string', 'description' => 'Issue description (markdown)'],
            'teamId' => ['type' => 'string', 'required' => true, 'description' => 'Team ID to create issue in'],
            'priority' => ['type' => 'integer', 'description' => 'Priority (0=none, 1=urgent, 2=high, 3=medium, 4=low)'],
            'stateId' => ['type' => 'string', 'description' => 'Workflow state ID'],
        ],
    )]
    public function create(array $args): array
    {
        $mutation = 'mutation CreateIssue($input: IssueCreateInput!) { issueCreate(input: $input) { success issue { id title } } }';
        return $this->driver->post(self::BASE_URL, ['query' => $mutation, 'variables' => ['input' => $args]]);
    }

    #[Operation(
        description: 'Update an existing Linear issue.',
        schema: [
            'id' => ['type' => 'string', 'required' => true, 'description' => 'Issue ID to update'],
            'title' => ['type' => 'string', 'description' => 'New title'],
            'description' => ['type' => 'string', 'description' => 'New description'],
            'stateId' => ['type' => 'string', 'description' => 'New workflow state ID'],
            'priority' => ['type' => 'integer', 'description' => 'New priority level'],
        ],
    )]
    public function update(array $args): array
    {
        $id = $args['id'];
        $mutation = 'mutation UpdateIssue($id: String!, $input: IssueUpdateInput!) { issueUpdate(id: $id, input: $input) { success issue { id title } } }';
        return $this->driver->post(self::BASE_URL, ['query' => $mutation, 'variables' => ['id' => $id, 'input' => $args]]);
    }
}
