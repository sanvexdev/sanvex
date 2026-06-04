<?php

namespace Sanvex\Drivers\Gmail\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Sanvex\Drivers\Gmail\Support\GmailMessageParser;

class GmailMessageParserTest extends TestCase
{
    public function test_summarize_extracts_headers(): void
    {
        $message = [
            'id' => 'abc123',
            'threadId' => 'thread1',
            'snippet' => 'Hello there',
            'labelIds' => ['INBOX'],
            'internalDate' => '1700000000000',
            'payload' => [
                'headers' => [
                    ['name' => 'Subject', 'value' => 'Invoice #42'],
                    ['name' => 'From', 'value' => 'Alice <alice@example.com>'],
                    ['name' => 'To', 'value' => 'bob@example.com'],
                    ['name' => 'Date', 'value' => 'Mon, 1 Jan 2024 10:00:00 +0000'],
                ],
            ],
        ];

        $summary = GmailMessageParser::summarize($message);

        $this->assertSame('abc123', $summary['id']);
        $this->assertSame('thread1', $summary['threadId']);
        $this->assertSame('Invoice #42', $summary['subject']);
        $this->assertSame('Alice <alice@example.com>', $summary['from']);
        $this->assertSame('bob@example.com', $summary['to']);
        $this->assertSame('Mon, 1 Jan 2024 10:00:00 +0000', $summary['date']);
        $this->assertSame('Hello there', $summary['snippet']);
        $this->assertSame(['INBOX'], $summary['labelIds']);
    }

    public function test_body_text_prefers_plain_over_html(): void
    {
        $message = [
            'payload' => [
                'mimeType' => 'multipart/alternative',
                'parts' => [
                    [
                        'mimeType' => 'text/html',
                        'body' => ['data' => base64_encode('<p>HTML</p>')],
                    ],
                    [
                        'mimeType' => 'text/plain',
                        'body' => ['data' => rtrim(strtr(base64_encode('Plain body'), '+/', '-_'), '=')],
                    ],
                ],
            ],
        ];

        $this->assertSame('Plain body', GmailMessageParser::bodyText($message));
    }

    public function test_enrich_includes_body_when_requested(): void
    {
        $message = [
            'id' => 'x',
            'payload' => [
                'mimeType' => 'text/plain',
                'headers' => [
                    ['name' => 'Subject', 'value' => 'Hi'],
                ],
                'body' => ['data' => rtrim(strtr(base64_encode('Full content'), '+/', '-_'), '=')],
            ],
        ];

        $enriched = GmailMessageParser::enrich($message, includeBody: true);

        $this->assertSame('Hi', $enriched['subject']);
        $this->assertSame('Full content', $enriched['bodyText']);
    }

    public function test_format_internal_date_from_milliseconds(): void
    {
        $formatted = GmailMessageParser::formatInternalDate('1704067200000');

        $this->assertNotNull($formatted);
        $this->assertStringContainsString('2024', $formatted);
    }
}
