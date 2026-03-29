<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\FirebaseNotificationService;

class FcmController extends Controller
{
    protected $firebaseService;

    public function __construct()
    {
        $this->firebaseService = new FirebaseNotificationService();
    }

    /**
     * Register FCM token
     */
    public function registerToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'device_type' => 'nullable|string|in:android,ios,web',
        ]);

        try {
            $userId = Auth::id();
            $token = $request->token;
            $deviceType = $request->device_type ?? 'web';

            // Check if user is kitchen staff
            if (Auth::user()->role_type !== 'Kitchen Staff') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only kitchen staff can receive notifications'
                ], 403);
            }

            $result = $this->firebaseService->registerToken($userId, $token, $deviceType);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Token registered successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to register token'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unregister FCM token
     */
    public function unregisterToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        try {
            $result = $this->firebaseService->unregisterToken($request->token);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Token unregistered successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to unregister token'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}