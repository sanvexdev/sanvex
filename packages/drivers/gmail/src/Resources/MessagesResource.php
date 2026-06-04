<?php

namespace Sanvex\Drivers\Gmail\Resources;

use Sanvex\Core\Attributes\Operation;
use Sanvex\Core\BaseResource;
use Sanvex\Drivers\Gmail\Support\GmailMessageParser;

class MessagesResource extends BaseResource
{
    private const BASE_URL = 'https://gmail.googleapis.com/gmail/v1/users/me';

    /** @var list<string> Gmail expects repeated metadataHeaders query params, not one comma-separated value. */
    private const METADATA_HEADERS = ['From', 'To', 'Subject', 'Date'];

    private const LIST_QUERY_KEYS = ['q', 'maxResults', 'pageToken', 'labelIds', 'includeSpamTrash'];

    private const MAX_ENRICHED_RESULTS = 25;

    #[Operation(
        description: 'List Gmail messages. Each item includes id, threadId, subject, from, to, date, and snippet (metadata fetched per message; maxResults capped at 25). Supports Gmail search via q.',
        readOnly: true,
        schema: [
            'q' => ['type' => 'string', 'description' => 'Gmail search query (e.g. "from:alice@example.com", "subject:report", "is:unread")'],
            'maxResults' => ['type' => 'integer', 'description' => 'Max messages to return (capped at 25)'],
            'pageToken' => ['type' => 'string', 'description' => 'Page token for pagination'],
            'labelIds' => ['type' => 'array', 'description' => 'Filter by label IDs'],
            'includeSpamTrash' => ['type' => 'boolean', 'description' => 'Include spam and trash'],
        ],
        responseFields: ['messages', 'nextPageToken', 'resultSizeEstimate'],
    )]
    public function list(array $args = []): array
    {
        $listArgs = array_intersect_key($args, array_flip(self::LIST_QUERY_KEYS));

        $maxResults = (int) ($listArgs['maxResults'] ?? 10);

        if ($maxResults < 1) {
            $maxResults = 10;
        }

        $listArgs['maxResults'] = min($maxResults, self::MAX_ENRICHED_RESULTS);

        $response = $this->driver->get(self::BASE_URL.'/messages', $listArgs);

        if (empty($response['messages']) || ! is_array($response['messages'])) {
            return $response;
        }

        $enriched = [];

        foreach ($response['messages'] as $stub) {
            if (! is_array($stub) || empty($stub['id'])) {
                continue;
            }

            $enriched[] = $this->fetchMessageSummary((string) $stub['id'], $stub);
        }

        $response['messages'] = $enriched;

        return $response;
    }

    #[Operation(
        description: 'Get one Gmail message by id. Returns flattened subject, from, to, date, snippet, and bodyText when format is full.',
        readOnly: true,
        schema: [
            'id' => ['type' => 'string', 'required' => true, 'description' => 'Message ID from list results'],
            'format' => ['type' => 'string', 'description' => 'minimal, metadata, full (default), or raw'],
            'metadataHeaders' => ['type' => 'array', 'description' => 'Headers when format is metadata (default From, To, Subject, Date)'],
        ],
    )]
    public function get(array $args): array
    {
        $id = $args['id'] ?? '';

        if ($id === '') {
            throw new \InvalidArgumentException('Message id is required.');
        }

        $format = $args['format'] ?? 'full';

        $query = ['format' => $format];

        if ($format === 'metadata') {
            $query['metadataHeaders'] = $this->normalizeMetadataHeaders($args['metadataHeaders'] ?? null);
        }

        $message = $this->driver->get(self::BASE_URL."/messages/{$id}", $query);

        if ($format === 'raw') {
            return $message;
        }

        return GmailMessageParser::enrich($message, includeBody: $format === 'full');
    }

    #[Operation(
        description: 'Send a new email message.',
        schema: [
            'raw' => ['type' => 'string', 'required' => true, 'description' => 'Base64url-encoded RFC 2822 email message'],
        ],
    )]
    public function send(array $args): array
    {
        return $this->driver->post(self::BASE_URL.'/messages/send', $args);
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

        return $this->driver->delete(self::BASE_URL."/messages/{$id}");
    }

    /**
     * @param  array<string, mixed>  $stub
     * @return array<string, mixed>
     */
    private function fetchMessageSummary(string $id, array $stub): array
    {
        try {
            $message = $this->driver->get(self::BASE_URL."/messages/{$id}", [
                'format' => 'metadata',
                'metadataHeaders' => self::METADATA_HEADERS,
            ]);

            return GmailMessageParser::summarize($message);
        } catch (\Throwable) {
            return array_filter([
                'id' => $id,
                'threadId' => isset($stub['threadId']) ? (string) $stub['threadId'] : null,
                'subject' => null,
                'from' => null,
                'to' => null,
                'date' => null,
                'snippet' => null,
            ], fn ($value) => $value !== null);
        }
    }

    /**
     * @return list<string>
     */
    private function normalizeMetadataHeaders(mixed $headers): array
    {
        if ($headers === null) {
            return self::METADATA_HEADERS;
        }

        if (is_array($headers)) {
            return array_values(array_filter(array_map('strval', $headers)));
        }

        if (is_string($headers) && $headers !== '') {
            return array_map('trim', explode(',', $headers));
        }

        return self::METADATA_HEADERS;
    }
}
