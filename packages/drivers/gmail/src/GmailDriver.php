<?php

namespace Sanvex\Drivers\Gmail;

use Sanvex\Core\Auth\KeyBuilder;
use Sanvex\Core\Auth\OAuthProviderConfig;
use Sanvex\Core\Auth\TokenExchangeAuth;
use Sanvex\Core\BaseDriver;
use Sanvex\Core\DTOs\WebhookResult;
use Sanvex\Drivers\Gmail\Auth\GmailKeyBuilder;
use Sanvex\Drivers\Gmail\Resources\MessagesResource;
use Sanvex\Drivers\Gmail\Resources\ThreadsResource;

class GmailDriver extends BaseDriver
{
    public string $id = 'gmail';

    public string $name = 'Gmail';

    public array $authTypes = ['oauth2'];

    public string $defaultAuthType = 'oauth2';

    public function oauthConfig(): ?OAuthProviderConfig
    {
        return new OAuthProviderConfig(
            clientId: config('sanvex.driver_configs.gmail.oauth.client_id', ''),
            clientSecret: config('sanvex.driver_configs.gmail.oauth.client_secret', ''),
            authorizationUrl: 'https://accounts.google.com/o/oauth2/v2/auth',
            tokenUrl: 'https://oauth2.googleapis.com/token',
            redirectUri: config('sanvex.driver_configs.gmail.oauth.redirect_uri', config('app.url').'/sanvex/gmail/callback'),
            scopes: [
                'https://www.googleapis.com/auth/gmail.readonly',
            ],
            tokenExchange: TokenExchangeAuth::RequestBody,
            authorizationParams: [
                'access_type' => 'offline',
                'prompt' => 'consent',
            ],
        );
    }

    public function messages(): MessagesResource
    {
        return new MessagesResource($this);
    }

    public function threads(): ThreadsResource
    {
        return new ThreadsResource($this);
    }

    public function handleWebhook(array $headers, array|string $payload): WebhookResult
    {
        return WebhookResult::ok(['status' => 'ok'], 'gmail', 'notification');
    }

    public function verifySignature(array $headers, string $rawBody, string $secret): bool
    {
        return true;
    }

    protected function makeKeyBuilder(): KeyBuilder
    {
        return new GmailKeyBuilder(
            driver: $this->id,
            keyManager: $this->keyManager,
            owner: $this->owner(),
        );
    }
}
