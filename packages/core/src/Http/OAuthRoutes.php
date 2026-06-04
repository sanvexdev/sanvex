<?php

namespace Sanvex\Core\Http;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Sanvex\Core\SanvexManager;

class OAuthRoutes
{
    /**
     * Register login/callback routes when OAuth is configured for this driver.
     *
     * Routes use global owner scope via buildState(). For per-user connections,
     * use custom routes and pass an Owner to oauth()->buildState($owner).
     */
    public static function shouldRegister(string $driverId): bool
    {
        $clientId = config("sanvex.driver_configs.{$driverId}.oauth.client_id");

        if (is_string($clientId) && $clientId !== '') {
            return true;
        }

        $authType = config("sanvex.driver_configs.{$driverId}.auth_type");

        return in_array($authType, ['oauth_2', 'oauth2'], true);
    }

    public static function registerIfConfigured(string $driverId): void
    {
        if (self::shouldRegister($driverId)) {
            self::register($driverId);
        }
    }

    public static function register(string $driverId): void
    {
        $redirectUri = config(
            "sanvex.driver_configs.{$driverId}.oauth.redirect_uri",
            '/sanvex/'.$driverId.'/callback',
        );
        $callbackPath = parse_url($redirectUri, PHP_URL_PATH) ?? '/sanvex/'.$driverId.'/callback';

        Route::get('/sanvex/'.$driverId.'/login', function (SanvexManager $manager) use ($driverId) {
            if (! config("sanvex.driver_configs.{$driverId}.oauth.client_id")) {
                abort(403, ucfirst($driverId).' OAuth client ID is not configured.');
            }

            $driver = $manager->resolveDriver($driverId);
            $config = $driver->oauthConfig();

            if ($config === null) {
                abort(500, ucfirst($driverId).' does not implement oauthConfig().');
            }

            $state = $driver->oauth()->buildState();

            return redirect($driver->oauth()->getAuthorizationUrl($config, $state));
        })->middleware('web');

        Route::get($callbackPath, function (Request $request, SanvexManager $manager) use ($driverId) {
            $redirect = config("sanvex.driver_configs.{$driverId}.oauth.success_redirect", '/');

            if ($request->filled('error')) {
                return redirect($redirect)->with(
                    'error',
                    ucfirst($driverId).' connection was denied: '.$request->query('error_description', $request->query('error')),
                );
            }

            $state = $request->query('state');

            if (! is_string($state) || $state === '') {
                return redirect($redirect)->with('error', ucfirst($driverId).' connection failed: missing OAuth state.');
            }

            $owner = $manager->resolveDriver($driverId)->oauth()->verifyState($state);

            if ($owner === null) {
                return redirect($redirect)->with('error', ucfirst($driverId).' connection failed: invalid OAuth state.');
            }

            $code = $request->query('code');

            if (! is_string($code) || $code === '') {
                return redirect($redirect)->with(
                    'error',
                    ucfirst($driverId).' connection failed: '.$request->query('error_description', 'no authorization code.'),
                );
            }

            $driver = $manager->for($owner)->resolveDriver($driverId);
            $config = $driver->oauthConfig();

            if ($config === null) {
                abort(500, ucfirst($driverId).' does not implement oauthConfig().');
            }

            if ($driver->oauth()->exchangeCode($code, $config)) {
                return redirect($redirect)->with('success', $driver->name.' connected successfully.');
            }

            return redirect($redirect)->with('error', 'Failed to exchange '.ucfirst($driverId).' authorization code.');
        })->middleware('web');
    }
}
