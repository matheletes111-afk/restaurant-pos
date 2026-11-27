<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{


public function login(Request $request)
{
    // Validation
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required|min:6',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => 'Validation Error',
            'errors' => $validator->errors()
        ], 422);
    }

    $credentials = $request->only('email', 'password');

    // Attempt Login
    if (!$token = Auth::guard('api')->attempt($credentials)) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid Email or Password'
        ], 401);
    }

    // Fetch Logged In User
    $user = Auth::guard('api')->user();

    // If device FCM token provided in login request, store it
    $fcmToken = $request->input('fcm_token', $request->input('token'));
    if (!empty($fcmToken)) {
        \App\Models\UserFcmToken::where('fcm_token', $fcmToken)
            ->where('user_id', '!=', $user->id)
            ->delete();

        \App\Models\UserFcmToken::updateOrCreate(
            [
                'user_id'   => $user->id,
                'fcm_token' => $fcmToken,
            ],
            [
                'device_type' => $request->input('device_type', 'mobile'),
                'user_agent'  => substr($request->userAgent() ?? '', 0, 500),
                'ip_address'  => $request->ip(),
                'updated_at'  => now(),
            ]
        );

        \Illuminate\Support\Facades\Log::info("FCM Token saved on API Login for User #{$user->id} ({$user->name})");
    }

    return response()->json([
        'status' => true,
        'message' => 'Login Successful',
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => auth('api')->factory()->getTTL() * 60,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ]
    ]);
}

    public function profile()
    {
        return response()->json(Auth::guard('api')->user());
    }

    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function refresh()
    {
        return $this->respondWithToken(Auth::guard('api')->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
