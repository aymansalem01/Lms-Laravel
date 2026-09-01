<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SsoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $sisCrmUrl = config('services.sis_crm.url', 'https://crm.luminusdigital.jo');
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
     * Here it is the receiving side: a SIS_CRM user is redirected here with a
     * signed token, so we log them into the LMS and send them to the dashboard.
     */
    public function callback(Request $request): RedirectResponse
    {
        $payload = $this->sso->validateToken((string) $request->query('token', ''));

        $user = isset($payload['email'])
            ? User::where('email', $payload['email'])->first()
            : null;

        if ($user === null) {
            return redirect()->route('login');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}
