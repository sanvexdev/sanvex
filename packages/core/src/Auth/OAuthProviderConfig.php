<?php

namespace Sanvex\Core\Auth;

class OAuthProviderConfig
{
    public function __construct(
        public readonly string $clientId,
        public readonly string $clientSecret,
        public readonly string $authorizationUrl,
        public readonly string $tokenUrl,
        public readonly string $redirectUri,
        public readonly array $scopes = [],
        public readonly TokenExchangeAuth $tokenExchange = TokenExchangeAuth::Basic,
        public readonly TokenBodyFormat $tokenBodyFormat = TokenBodyFormat::Form,
        /** Extra query params on the authorize URL (e.g. Google access_type; Notion owner=user). */
        public readonly array $authorizationParams = [],
    ) {}
}
