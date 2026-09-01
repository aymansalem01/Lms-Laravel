<?php

namespace App\Services;

use App\Models\User;
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

        $encoded = $this->base64UrlEncode((string) json_encode($payload));

        return $encoded . '.' . $this->sign($encoded);
    }

    /**
     * Validate an SSO token and return the decoded payload.
     */
    public function validateToken(string $token): ?array
    {
        [$encoded, $signature] = array_pad(explode('.', $token, 2), 2, null);

        if ($encoded === null || $signature === null) {
            return null;
        }

        if (! hash_equals($this->sign($encoded), $signature)) {
            return null;
        }

        $decoded = $this->base64UrlDecode($encoded);

        if ($decoded === null) {
            return null;
        }

        $payload = json_decode($decoded, true);

        if (! is_array($payload)) {
            return null;
        }

        // Allow a small clock-skew grace period (5 minutes).
        if (isset($payload['exp']) && $payload['exp'] < (now()->timestamp - 300)) {
            return null;
        }

        return $payload;
    }

    private function sign(string $data): string
    {
        return hash_hmac('sha256', $data, $this->secretKey);
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $data): ?string
    {
        $decoded = base64_decode(strtr($data, '-_', '+/'), true);

        return $decoded === false ? null : $decoded;
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
