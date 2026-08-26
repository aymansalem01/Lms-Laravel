<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SsoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SsoController extends Controller
{
    public function __construct(
        private SsoService $sso,
    ) {}

    /**
     * GET /api/v1/sso/authorize
     * Generates an SSO token for the currently authenticated LMS user
     * and redirects them to SIS_CRM with the token.
     */
    public function authorize(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $token = $this->sso->generateToken($user);
        $sisCrmUrl = config('services.sis_crm.url', 'http://localhost:8010');
        $callbackUrl = $sisCrmUrl . '/api/v1/sso/callback';

        return redirect()->away($callbackUrl . '?' . http_build_query([
            'token' => $token,
            'email' => $user->email,
        ]));
    }

    /**
     * POST /api/v1/sso/validate
     * Called by SIS_CRM (with API token) to validate an SSO token.
     */
    public function validateToken(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
        ]);

        $payload = $this->sso->validateToken($request->input('token'));

        if ($payload === null) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid or expired token.',
            ], 401);
        }

        return response()->json([
            'valid' => true,
            'user' => [
                'id' => $payload['user_id'],
                'email' => $payload['email'],
                'name' => $payload['name'],
                'role' => $payload['role'],
            ],
        ]);
    }

    /**
     * GET /api/v1/sso/callback
     * Called by SIS_CRM after it generates its own session from the SSO token.
     */
    public function callback(Request $request): JsonResponse
    {
        return response()->json(['status' => 'ok']);
    }
}
