<?php

namespace Sanvex\Core\Tests\Unit;

use Sanvex\Core\Http\OAuthRoutes;
use Sanvex\Core\Tests\CoreTestCase;

class OAuthRoutesRegistrationTest extends CoreTestCase
{
    public function test_should_register_when_client_id_is_set(): void
    {
        config()->set('sanvex.driver_configs.gmail.oauth.client_id', 'abc');

        $this->assertTrue(OAuthRoutes::shouldRegister('gmail'));
    }

    public function test_should_register_when_auth_type_is_oauth(): void
    {
        config()->set('sanvex.driver_configs.notion.auth_type', 'oauth_2');
        config()->set('sanvex.driver_configs.notion.oauth.client_id', '');

        $this->assertTrue(OAuthRoutes::shouldRegister('notion'));
    }

    public function test_should_not_register_without_client_id_or_oauth_auth_type(): void
    {
        config()->set('sanvex.driver_configs.gmail.oauth.client_id', '');
        config()->set('sanvex.driver_configs.gmail.auth_type', 'api_key');

        $this->assertFalse(OAuthRoutes::shouldRegister('gmail'));
    }
}
