<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DebugController extends Controller
{
    public function check(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'authenticated' => auth()->check(),
            'id' => $user?->id,
            'email' => $user?->email,
            'role' => $user?->role,
            'isInstructor' => $user?->isInstructor(),
            'isAdmin' => $user?->isAdmin(),
            'session_has' => $request->session()->has('_token'),
            'session_id' => $request->session()->getId(),
        ]);
    }
}
