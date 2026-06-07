<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LiveSession;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class LiveKitController extends Controller
{
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
