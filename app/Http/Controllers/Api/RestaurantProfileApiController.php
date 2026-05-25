<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RestaurantMaster;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

class RestaurantProfileApiController extends Controller
{
    /**
     * Update restaurant profile
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'restaurant_name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'address' => 'required|string',
                'pincode' => 'required|string|max:10',
                'gstin' => 'nullable|string|max:50',
                'gst_percentage' => 'nullable|numeric|min:0|max:100'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            
            DB::beginTransaction();
            
            // Get restaurant
            $restaurant = RestaurantMaster::where('id', $request->user()->restaurant_id)->firstOrFail();
            
            // Get user (owner)
            $user = User::find($restaurant->owner_id);
            
            // Update User Table (Phone only - email is readonly)
            $user->phone = $request->phone;
            $user->save();
            
            // Update Restaurant Master
            $restaurant->name = $request->restaurant_name;
            $restaurant->address = $request->address;
            $restaurant->pincode = $request->pincode;
            $restaurant->gstin = $request->gstin;
            $restaurant->gst_percentage = $request->gst_percentage ?? 0;
            $restaurant->save();
            
            DB::commit();
            
            // Prepare response data
            $data = [
                'restaurant' => [
                    'id' => $restaurant->id,
                    'name' => $restaurant->name,
                    'address' => $restaurant->address,
                    'pincode' => $restaurant->pincode,
                    'gstin' => $restaurant->gstin,
                    'gst_percentage' => $restaurant->gst_percentage,
                    'status' => $restaurant->status
                ],
                'owner' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone
                ]
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => $data
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Update restaurant owner password
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(Request $request)
    {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'new_password' => 'required|min:6',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            
            $user = User::find($request->user()->id);
            
            // Check current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], Response::HTTP_BAD_REQUEST);
            }
            
            // Update password
            $user->password = Hash::make($request->new_password);
            $user->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully'
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    

}