<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class SsoService
{
    private string $secretKey;
    private int $tokenExpiry;

    public function __construct()
    {
        $this->secretKey = config('services.sso.secret_key', config('app.key'));
        $this->tokenExpiry = (int) config('services.sso.token_expiry', 3600);
    }

    /**
     * Generate an SSO token for a user.
     */
    public function generateToken(User $user): string
    {
        $payload = [
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'role' => $user->role,
            'iat' => now()->timestamp,
            'exp' => now()->addSeconds($this->tokenExpiry)->timestamp,
            'jti' => Str::uuid()->toString(),
        ];

        return Crypt::encryptString(json_encode($payload));
    }

    /**
     * Validate an SSO token and return the decoded payload.
     */
    public function validateToken(string $token): ?array
    {
        try {
            $decoded = Crypt::decryptString($token);
            $payload = json_decode($decoded, true);

            if (! is_array($payload)) {
                return null;
            }

            if (isset($payload['exp']) && $payload['exp'] < now()->timestamp) {
                return null;
            }

            return $payload;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Validate an SSO token by calling the remote system's validate endpoint.
     */
    public function validateRemoteToken(string $token, string $remoteUrl): ?array
    {
        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.sis_crm.api_secret', ''),
                'Accept' => 'application/json',
            ])->timeout(10)->post($remoteUrl, ['token' => $token]);

            if ($response->successful() && $response->json('valid', false)) {
                return $response->json('user');
            }

            return null;
        } catch (\Throwable) {
            return null;
        }
    }
}
