<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserFcmToken;
use Illuminate\Support\Facades\Auth;

class DeviceTokenController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        UserFcmToken::updateOrCreate(
            [
                'user_id'   => $user->id,
                'fcm_token' => $request->token,
            ],
            [
                'device_type' => $request->input('device_type', 'web'),
                'user_agent'  => substr($request->userAgent() ?? '', 0, 500),
                'ip_address'  => $request->ip(),
                'updated_at'  => now(),
            ]
        );

        return response()->json(['success' => true, 'message' => 'Device token registered successfully.']);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        UserFcmToken::where('fcm_token', $request->token)->delete();

        return response()->json(['success' => true, 'message' => 'Device token removed.']);
    }
}
