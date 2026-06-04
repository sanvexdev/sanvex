<?php

namespace Sanvex\Core\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Sanvex\Core\Auth\OAuthManager;
use Sanvex\Core\Auth\OAuthProviderConfig;
use Sanvex\Core\Auth\TokenBodyFormat;
use Sanvex\Core\Auth\TokenExchangeAuth;
use Sanvex\Core\Http\OAuthRoutes;
use Sanvex\Core\Tests\CoreTestCase;

class OAuthManagerTokenExchangeTest extends CoreTestCase
{
    public function test_exchange_code_sends_google_form_body_credentials(): void
    {
        Http::fake([
            'oauth2.googleapis.com/*' => Http::response(['access_token' => 'tok', 'expires_in' => 3600]),
        ]);

        $config = new OAuthProviderConfig(
            clientId: 'google-client-id',
            clientSecret: 'google-secret',
            authorizationUrl: 'https://accounts.google.com/o/oauth2/v2/auth',
            tokenUrl: 'https://oauth2.googleapis.com/token',
            redirectUri: 'https://app.test/sanvex/gmail/callback',
            tokenExchange: TokenExchangeAuth::RequestBody,
        );

        $manager = new OAuthManager('gmail');
        $this->assertTrue($manager->exchangeCode('auth-code', $config));

        Http::assertSent(function ($request) {
            if (! str_contains($request->url(), 'oauth2.googleapis.com/token')) {
                return false;
            }

            $body = $request->data();

            return empty($request->header('Authorization'))
                && ($body['grant_type'] ?? null) === 'authorization_code'
                && ($body['code'] ?? null) === 'auth-code'
                && ($body['client_id'] ?? null) === 'google-client-id'
                && ($body['client_secret'] ?? null) === 'google-secret';
        });
    }

    public function test_exchange_code_sends_notion_json_body_with_basic_auth(): void
    {
        Http::fake([
            'api.notion.com/*' => Http::response(['access_token' => 'notion-tok', 'expires_in' => 3600]),
        ]);

        $config = new OAuthProviderConfig(
            clientId: 'notion-client-id',
            clientSecret: 'notion-secret',
            authorizationUrl: 'https://api.notion.com/v1/oauth/authorize',
            tokenUrl: 'https://api.notion.com/v1/oauth/token',
            redirectUri: 'https://app.test/sanvex/notion/callback',
            tokenBodyFormat: TokenBodyFormat::Json,
        );

        $manager = new OAuthManager('notion');
        $this->assertTrue($manager->exchangeCode('notion-code', $config));

        Http::assertSent(function ($request) {
            if (! str_contains($request->url(), 'api.notion.com/v1/oauth/token')) {
                return false;
            }

            $authHeader = $request->header('Authorization')[0] ?? '';
            $expectedBasic = 'Basic '.base64_encode('notion-client-id:notion-secret');
            $body = json_decode($request->body(), true);

            return $authHeader === $expectedBasic
                && is_array($body)
                && ($body['grant_type'] ?? null) === 'authorization_code'
                && ($body['code'] ?? null) === 'notion-code';
        });
    }

    public function test_exchange_code_sends_basic_auth_form_for_default_config(): void
    {
        Http::fake([
            'example.com/*' => Http::response(['access_token' => 'basic-tok']),
        ]);

        $config = new OAuthProviderConfig(
            clientId: 'client-id',
            clientSecret: 'client-secret',
            authorizationUrl: 'https://example.com/oauth/authorize',
            tokenUrl: 'https://example.com/oauth/token',
            redirectUri: 'https://app.test/callback',
        );

        $manager = new OAuthManager('example');
        $this->assertTrue($manager->exchangeCode('code', $config));

        Http::assertSent(function ($request) {
            if (! str_contains($request->url(), 'example.com/oauth/token')) {
                return false;
            }

            $authHeader = $request->header('Authorization')[0] ?? '';
            $body = $request->data();

            return $authHeader === 'Basic '.base64_encode('client-id:client-secret')
                && ($body['grant_type'] ?? null) === 'authorization_code'
                && ! isset($body['client_id']);
        });
    }
}
