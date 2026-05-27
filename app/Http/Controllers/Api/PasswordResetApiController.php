<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use App\Mail\ResetPassword;

class PasswordResetApiController extends Controller
{
    /**
     * Send OTP to user email for password reset
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            
            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'This email is not registered yet'
                ], Response::HTTP_NOT_FOUND);
            }
            
            // Generate OTP (6-digit code)
            $otp = rand(100000, 999999);
            
            // Update OTP in database
            $user->email_vcode = $otp;
            $user->save();
            
            // Send email with OTP
            try {
                $data = [
                    'email' => $user->email,
                    'name' => $user->name,
                    'email_vcode' => $otp,
                    'id' => $user->id,
                ];
                Mail::send(new ResetPassword($data));
            } catch (\Exception $e) {
                \Log::error('Failed to send reset password email: ' . $e->getMessage());
                // Continue even if email fails (for testing)
            }
            
            return response()->json([
                'success' => true,
                'message' => 'OTP sent to your email successfully',
                'data' => [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'otp_sent' => true
                ]
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Verify OTP code
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'otp' => 'required|string|min:6|max:6'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            
            $user = User::where('id', $request->user_id)
                ->where('email_vcode', $request->otp)
                ->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP. Please try again.'
                ], Response::HTTP_BAD_REQUEST);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully',
                'data' => [
                    'user_id' => $user->id,
                    'verified' => true
                ]
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Reset password after OTP verification
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'otp' => 'required|string|min:6|max:6',
                'password' => 'required|string|min:6|confirmed'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            
            // Verify OTP
            $user = User::where('id', $request->user_id)
                ->where('email_vcode', $request->otp)
                ->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP. Please try again.'
                ], Response::HTTP_BAD_REQUEST);
            }
            
            // Update password
            $user->password = Hash::make($request->password);
            $user->email_vcode = null; // Clear OTP after successful reset
            $user->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully. Please login with your new password.'
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Alternative: Combined reset flow (request OTP + reset in one call)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPasswordCombined(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email',
                'password' => 'required|string|min:6|confirmed'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            
            // Generate OTP
            $otp = rand(100000, 999999);
            
            // Update user with OTP
            $user = User::where('email', $request->email)->first();
            $user->email_vcode = $otp;
            $user->save();
            
            // Send email with OTP
            try {
                $data = [
                    'email' => $user->email,
                    'name' => $user->name,
                    'email_vcode' => $otp,
                    'id' => $user->id,
                ];
                Mail::send(new ResetPassword($data));
            } catch (\Exception $e) {
                \Log::error('Failed to send reset password email: ' . $e->getMessage());
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Password reset initiated. OTP sent to your email.',
                'data' => [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}