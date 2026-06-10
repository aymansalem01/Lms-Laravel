<?php

namespace App\Http\Controllers\Api;

use Agence104\LiveKit\AccessToken;
use Agence104\LiveKit\AccessTokenOptions;
use Agence104\LiveKit\VideoGrant;
use App\Http\Controllers\Controller;
use App\Models\LiveSession;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LiveKitController extends Controller
{
    public function getPublicToken(Request $request)
    {
        $room = $request->input('room', 'main-room');
        $identity = 'guest-' . Str::random(8);

        $tokenOptions = (new AccessTokenOptions())
            ->setIdentity($identity);

        $videoGrant = (new VideoGrant())
            ->setRoomJoin()
            ->setRoomCreate()
            ->setRoomName($room);

        $token = (new AccessToken())
            ->init($tokenOptions)
            ->setGrant($videoGrant)
            ->toJwt();

        return response()->json([
            'token' => $token,
            'identity' => $identity,
            'apiHost' => config('services.livekit.host'),
        ]);
    }

    public function token(Request $request)
    {
        $data = $request->validate([
            'roomName' => 'required|string|max:255',
            'sessionId' => 'nullable|integer|exists:live_sessions,id',
        ]);

        $roomName = $data['roomName'];
        $user = $request->user();

        $videoGrants = [
            'room' => $roomName,
            'roomJoin' => true,
            'roomCreate' => true,
            'canPublish' => true,
            'canSubscribe' => true,
            'canPublishData' => true,
        ];

        if ($user->isInstructorOrAdmin()) {
            $videoGrants['roomAdmin'] = true;
            $videoGrants['roomRecord'] = true;
        }

        $payload = [
            'exp' => time() + 3600,
            'iss' => config('services.livekit.api_key'),
            'sub' => (string) $user->id,
            'video' => $videoGrants,
        ];

        $token = JWT::encode($payload, config('services.livekit.api_secret'), 'HS256');

        if (!empty($data['sessionId'])) {
            LiveSession::where('id', $data['sessionId'])->update(['status' => 'live']);
        }

        return response()->json([
            'token' => $token,
            'apiHost' => config('services.livekit.host'),
        ]);
    }
}
