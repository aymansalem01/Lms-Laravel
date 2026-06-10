<?php

namespace App\Services;

use Firebase\JWT\JWT;
use GuzzleHttp\Client;

class LiveKitService
{
    protected string $apiKey;
    protected string $apiSecret;
    protected string $host;
    protected Client $http;

    public function __construct()
    {
        $this->apiKey = config('services.livekit.api_key');
        $this->apiSecret = config('services.livekit.api_secret');
        $this->host = str_replace('wss://', 'https://', config('services.livekit.host'));
        $this->http = new Client(['timeout' => 10]);
    }

    protected function accessToken(array $videoGrants): string
    {
        $payload = [
            'exp' => time() + 3600,
            'iss' => $this->apiKey,
            'sub' => 'api-service',
            'video' => $videoGrants,
        ];

        return JWT::encode($payload, $this->apiSecret, 'HS256');
    }

    public function createRoom(string $roomName): void
    {
        $token = $this->accessToken([
            'roomCreate' => true,
            'room' => '*',
            'roomAdmin' => true,
        ]);

        $this->http->post($this->host . '/twirp/twirp.livekit.RoomService/CreateRoom', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ],
            'json' => ['name' => $roomName],
        ]);
    }
}
