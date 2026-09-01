<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiSecret
{
    /**
     * Authenticate server-to-server API calls using the shared API secret
     * presented as a Bearer token (see SsoService::validateRemoteToken and
     * the SIS_CRM UserSyncService).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secret = (string) config('services.sis_crm.api_secret', '');
        $token = (string) $request->bearerToken();

        if ($secret === '' || $token === '' || ! hash_equals($secret, $token)) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return $next($request);
    }
}