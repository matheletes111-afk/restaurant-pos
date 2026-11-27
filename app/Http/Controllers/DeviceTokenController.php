<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserFcmToken;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DeviceTokenController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $user = Auth::user() ?? Auth::guard('api')->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        // Dissociate this device token from any previous user account (e.g. shared device or account switch)
        UserFcmToken::where('fcm_token', $request->token)
            ->where('user_id', '!=', $user->id)
            ->delete();

        $fcmRecord = UserFcmToken::updateOrCreate(
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

        Log::info("FCM Token saved successfully for User #{$user->id} ({$user->name})", [
            'device_type' => $request->input('device_type', 'web'),
            'ip'          => $request->ip()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Device token registered successfully.',
            'data'    => [
                'user_id'   => $user->id,
                'token_id'  => $fcmRecord->id,
            ]
        ]);
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
