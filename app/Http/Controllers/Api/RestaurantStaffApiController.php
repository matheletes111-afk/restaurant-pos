<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

class RestaurantStaffApiController extends Controller
{
    /**
     * Get all staff members
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $staff = User::where('restaurant_id', $request->user()->restaurant_id)
                ->where('role_type', '!=', 'ADMIN')
                ->where('role_type', '!=', 'Super Admin')
                ->where('status', '!=', 'D')
                ->orderBy('id', 'DESC')
                ->get(['id', 'name', 'email', 'phone', 'role_type', 'address', 'pincode', 'status', 'created_at']);
            
            return response()->json([
                'success' => true,
                'message' => 'Staff list retrieved successfully',
                'data' => $staff,
                'total' => $staff->count()
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Get single staff member details
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        try {
            $staff = User::where('id', $id)
                ->where('restaurant_id', $request->user()->restaurant_id)
                ->where('role_type', '!=', 'ADMIN')
                ->first(['id', 'name', 'email', 'phone', 'role_type', 'address', 'pincode', 'status', 'created_at']);
            
            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff member not found'
                ], Response::HTTP_NOT_FOUND);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Staff details retrieved successfully',
                'data' => $staff
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Create new staff member
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|string|max:20',
                'role_type' => 'required|string|in:Kitchen Staff,Waiter,Cashier,Manager',
                'password' => 'required|string|min:6',
                'address' => 'nullable|string|max:500',
                'pincode' => 'nullable|string|max:10',
                'status' => 'nullable|in:A,I'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            
            // Create new staff
            $staff = new User();
            $staff->name = $request->name;
            $staff->email = $request->email;
            $staff->phone = $request->phone;
            $staff->role = 'RES';
            $staff->role_type = $request->role_type;
            $staff->restaurant_id = $request->user()->restaurant_id;
            $staff->address = $request->address;
            $staff->pincode = $request->pincode;
            $staff->status = $request->status ?? 'A';
            $staff->password = Hash::make($request->password);
            $staff->save();
            
            // Prepare response data
            $data = [
                'id' => $staff->id,
                'name' => $staff->name,
                'email' => $staff->email,
                'phone' => $staff->phone,
                'role_type' => $staff->role_type,
                'address' => $staff->address,
                'pincode' => $staff->pincode,
                'status' => $staff->status,
                'created_at' => $staff->created_at
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Staff added successfully',
                'data' => $data
            ], Response::HTTP_CREATED);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Update staff member
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'role_type' => 'required|string|in:Kitchen Staff,Waiter,Cashier,Manager',
                'address' => 'nullable|string|max:500',
                'pincode' => 'nullable|string|max:10',
                'status' => 'nullable|in:A,I'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            
            // Find staff member
            $staff = User::where('id', $id)
                ->where('restaurant_id', $request->user()->restaurant_id)
                ->where('role_type', '!=', 'ADMIN')
                ->first();
            
            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff member not found'
                ], Response::HTTP_NOT_FOUND);
            }
            
            // Update staff details
            $staff->name = $request->name;
            $staff->email = $request->email;
            $staff->phone = $request->phone;
            $staff->role_type = $request->role_type;
            $staff->address = $request->address;
            $staff->pincode = $request->pincode;
            if ($request->has('status')) {
                $staff->status = $request->status;
            }
            $staff->save();
            
            // Prepare response data
            $data = [
                'id' => $staff->id,
                'name' => $staff->name,
                'email' => $staff->email,
                'phone' => $staff->phone,
                'role_type' => $staff->role_type,
                'address' => $staff->address,
                'pincode' => $staff->pincode,
                'status' => $staff->status,
                'updated_at' => $staff->updated_at
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Staff updated successfully',
                'data' => $data
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Delete staff member (soft delete)
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, $id)
    {
        try {
            $staff = User::where('id', $id)
                ->where('restaurant_id', $request->user()->restaurant_id)
                ->where('role_type', '!=', 'ADMIN')
                ->first();
            
            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff member not found'
                ], Response::HTTP_NOT_FOUND);
            }
            
            $staff->status = 'D';
            $staff->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Staff deleted successfully'
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    /**
     * Change staff status (Active/Inactive)
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus(Request $request, $id)
    {
        try {
            $staff = User::where('id', $id)
                ->where('restaurant_id', $request->user()->restaurant_id)
                ->where('role_type', '!=', 'ADMIN')
                ->first();
            
            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff member not found'
                ], Response::HTTP_NOT_FOUND);
            }
            
            $newStatus = $staff->status == 'A' ? 'I' : 'A';
            $staff->status = $newStatus;
            $staff->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Staff status changed successfully',
                'data' => [
                    'id' => $staff->id,
                    'name' => $staff->name,
                    'status' => $staff->status,
                    'status_text' => $staff->status == 'A' ? 'Active' : 'Inactive'
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
     * Bulk delete staff members
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkDelete(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:users,id'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            
            $deletedCount = User::whereIn('id', $request->ids)
                ->where('restaurant_id', $request->user()->restaurant_id)
                ->where('role_type', '!=', 'ADMIN')
                ->update(['status' => 'D']);
            
            return response()->json([
                'success' => true,
                'message' => $deletedCount . ' staff members deleted successfully',
                'data' => [
                    'deleted_count' => $deletedCount
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