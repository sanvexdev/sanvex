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
        public readonly array $scopes = []
    ) {}
}
