<?php

namespace Sanvex\Core\Auth;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Sanvex\Core\Encryption\KeyManager;
use Throwable;

class OAuthManager
{
    public function __construct(
        private readonly string $driver,
        private readonly ?KeyManager $keyManager = null,
    ) {}

    public function storeTokens(array $tokens): void
    {
        foreach ($tokens as $key => $value) {
            if ($this->keyManager && is_scalar($value)) {
                $this->keyManager->storeCredential($this->driver, $key, (string) $value);
            }
        }
    }

    public function getAccessToken(): ?string
    {
        return $this->keyManager?->getCredential($this->driver, 'access_token');
    }

    public function getRefreshToken(): ?string
    {
        return $this->keyManager?->getCredential($this->driver, 'refresh_token');
    }

    public function getExpiresAt(): ?int
    {
        $val = $this->keyManager?->getCredential($this->driver, 'expires_at');
        return $val ? (int) $val : null;
    }

    public function getAuthorizationUrl(OAuthProviderConfig $config, string $state): string
    {
        $query = http_build_query([
            'client_id' => $config->clientId,
            'redirect_uri' => $config->redirectUri,
            'response_type' => 'code',
            'scope' => implode(' ', $config->scopes),
            'state' => $state,
        ]);

        $separator = str_contains($config->authorizationUrl, '?') ? '&' : '?';
        
        return $config->authorizationUrl . $separator . $query;
    }

    public function exchangeCode(string $code, OAuthProviderConfig $config): bool
    {
        // Notion requires Basic Auth for exchanging the token as per their docs
        // So we will use withBasicAuth to encode clientId:clientSecret in the header
        $response = Http::withBasicAuth($config->clientId, $config->clientSecret)
            ->asForm()
            ->post($config->tokenUrl, [
                'grant_type' => 'authorization_code',
                'redirect_uri' => $config->redirectUri,
                'code' => $code,
            ]);

        if ($response->successful()) {
            $data = $response->json();
            $this->saveTokenResponse($data);
            return true;
        }

        // Output error to logs if you need to debug the response
        // \Illuminate\Support\Facades\Log::error('OAuth Exchange Failed', $response->json());

        return false;
    }

    public function refreshIfExpired(OAuthProviderConfig $config): bool
    {
        $accessToken = $this->getAccessToken();
        $refreshToken = $this->getRefreshToken();
        $expiresAt = $this->getExpiresAt();

        // If we don't have a token, or we don't know when it expires, or no refresh token... skip
        if (!$accessToken || !$refreshToken || !$expiresAt) {
            return false;
        }

        // Give it a 60 second buffer to ensure request doesn't fail mid-flight
        if (time() >= ($expiresAt - 60)) {
            try {
                $response = Http::withBasicAuth($config->clientId, $config->clientSecret)
                    ->asForm()
                    ->post($config->tokenUrl, [
                        'grant_type' => 'refresh_token',
                        'refresh_token' => $refreshToken,
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    // Sometimes IDPs don't return a new refresh token, keep the old one
                    if (!isset($data['refresh_token'])) {
                        $data['refresh_token'] = $refreshToken;
                    }
                    
                    $this->saveTokenResponse($data);
                    return true;
                }
            } catch (Throwable $e) {
                // Log or handle refresh failure if needed
            }
        }

        return false; // Not expired, or refresh failed silently
    }

    protected function saveTokenResponse(array $data): void
    {
        $tokens = [];
        
        if (isset($data['access_token'])) {
            $tokens['access_token'] = $data['access_token'];
        }
        
        if (isset($data['refresh_token'])) {
            $tokens['refresh_token'] = $data['refresh_token'];
        }
        
        if (isset($data['expires_in'])) {
            $tokens['expires_at'] = time() + (int) $data['expires_in'];
        }

        $this->storeTokens($tokens);
    }
}
