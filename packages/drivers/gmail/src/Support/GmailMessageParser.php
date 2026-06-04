<?php

namespace Sanvex\Drivers\Gmail\Support;

class GmailMessageParser
{
    /**
     * @return array{id: string, threadId: string|null, subject: string|null, from: string|null, to: string|null, date: string|null, snippet: string|null, labelIds: array<int, string>|null}
     */
    public static function summarize(array $message): array
    {
        $payload = $message['payload'] ?? null;

        return [
            'id' => (string) ($message['id'] ?? ''),
            'threadId' => isset($message['threadId']) ? (string) $message['threadId'] : null,
            'subject' => self::subject($message),
            'from' => self::from($message),
            'to' => self::to($message),
            'date' => self::date($message),
            'snippet' => isset($message['snippet']) ? (string) $message['snippet'] : null,
            'labelIds' => isset($message['labelIds']) && is_array($message['labelIds'])
                ? $message['labelIds']
                : null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function enrich(array $message, bool $includeBody = false): array
    {
        $result = self::summarize($message);

        if ($includeBody) {
            $body = self::bodyText($message);
            if ($body !== null) {
                $result['bodyText'] = $body;
            }
        }

        return $result;
    }

    public static function subject(array $message): ?string
    {
        return self::headerFromPayload($message['payload'] ?? null, 'Subject');
    }

    public static function from(array $message): ?string
    {
        return self::headerFromPayload($message['payload'] ?? null, 'From');
    }

    public static function to(array $message): ?string
    {
        return self::headerFromPayload($message['payload'] ?? null, 'To');
    }

    public static function date(array $message): ?string
    {
        $headerDate = self::headerFromPayload($message['payload'] ?? null, 'Date');

        if ($headerDate !== null && $headerDate !== '') {
            return $headerDate;
        }

        return self::formatInternalDate($message['internalDate'] ?? null);
    }

    public static function bodyText(array $message): ?string
    {
        $payload = $message['payload'] ?? null;

        if (! is_array($payload)) {
            return null;
        }

        return self::extractBodyFromPart($payload);
    }

    /**
     * @param  array<string, mixed>|null  $payload
     */
    public static function headerFromPayload(?array $payload, string $name): ?string
    {
        if ($payload === null) {
            return null;
        }

        $value = self::headerOnPart($payload, $name);

        if ($value !== null) {
            return $value;
        }

        foreach ($payload['parts'] ?? [] as $part) {
            if (! is_array($part)) {
                continue;
            }

            $value = self::headerOnPart($part, $name);

            if ($value !== null) {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $part
     */
    private static function headerOnPart(array $part, string $name): ?string
    {
        $headers = $part['headers'] ?? null;

        if (! is_array($headers)) {
            return null;
        }

        $needle = strtolower($name);

        foreach ($headers as $header) {
            if (! is_array($header)) {
                continue;
            }

            if (strtolower((string) ($header['name'] ?? '')) === $needle) {
                $value = $header['value'] ?? null;

                return is_string($value) && $value !== '' ? $value : null;
            }
        }

        return null;
    }

    public static function formatInternalDate(mixed $internalDate): ?string
    {
        if ($internalDate === null || $internalDate === '') {
            return null;
        }

        $ms = (int) $internalDate;

        if ($ms <= 0) {
            return null;
        }

        return date('c', (int) floor($ms / 1000));
    }

    /**
     * @param  array<string, mixed>  $part
     */
    private static function extractBodyFromPart(array $part): ?string
    {
        $plainText = null;
        $htmlText = null;

        $bodyData = $part['body']['data'] ?? null;

        if (is_string($bodyData) && $bodyData !== '') {
            $decoded = self::decodeBodyData($bodyData);

            if ($decoded !== null) {
                $mimeType = (string) ($part['mimeType'] ?? '');

                if ($mimeType === 'text/plain') {
                    $plainText = $decoded;
                } elseif ($mimeType === 'text/html') {
                    $htmlText = $decoded;
                }
            }
        }

        $parts = $part['parts'] ?? null;

        if (is_array($parts)) {
            foreach ($parts as $subPart) {
                if (! is_array($subPart)) {
                    continue;
                }

                $text = self::extractBodyFromPart($subPart);

                if ($text === null) {
                    continue;
                }

                $subMime = (string) ($subPart['mimeType'] ?? '');

                if ($subMime === 'text/plain') {
                    $plainText = $text;
                } elseif ($subMime === 'text/html' && $plainText === null) {
                    $htmlText = $text;
                }
            }
        }

        return $plainText ?? $htmlText;
    }

    private static function decodeBodyData(string $data): ?string
    {
        $decoded = base64_decode(strtr($data, '-_', '+/'), true);

        return $decoded !== false ? $decoded : null;
    }
}
