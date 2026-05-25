<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TableManage;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TableManageApiController extends Controller
{
    /**
     * List all tables for the authenticated restaurant
     */
    public function index(Request $request)
    {
        try {
            $restaurantId = $request->user()->restaurant_id;
            
            $tables = TableManage::where('status', '!=', 'D')
                ->where('restaurant_id', $restaurantId)
                ->orderBy('id', 'desc')
                ->get();
            
            // Add full QR code URL
            foreach ($tables as $table) {
                $table->qr_url = $table->qr_code ? asset('qrcodes/' . $table->qr_code) : null;
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Tables retrieved successfully',
                'data' => $tables
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
    
    /**
     * Store new table with QR code
     */
    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            // Create table
            $table = new TableManage();
            $table->name = $request->name;
            $table->description = $request->description;
            $table->user_id = $request->user()->id;
            $table->restaurant_id = $request->user()->restaurant_id;
            $table->status = 'A';
            $table->save();
            
            // Generate QR link
            $qrLink = url('/restaurant/table/' . $table->id . '/' . $table->restaurant_id);
            
            // Generate QR file name
            $fileName = 'qr_' . $table->id . '.png';
            $qrPath = public_path('qrcodes/' . $fileName);
            
            // Create directory if not exists
            if (!file_exists(public_path('qrcodes'))) {
                mkdir(public_path('qrcodes'), 0777, true);
            }
            
            // Generate QR code
            \QrCode::format('png')
                ->size(300)
                ->generate($qrLink, $qrPath);
            
            // Save QR name
            $table->qr_code = $fileName;
            $table->save();
            
            // Add QR URL to response
            $table->qr_url = asset('qrcodes/' . $fileName);
            
            return response()->json([
                'success' => true,
                'message' => 'Table added successfully with QR Code',
                'data' => $table
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
    
    /**
     * Update table details
     */
    public function update(Request $request, $id)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $table = TableManage::where('id', $id)
                ->where('restaurant_id', $request->user()->restaurant_id)
                ->first();
            
            if (!$table) {
                return response()->json([
                    'success' => false,
                    'message' => 'Table not found',
                    'data' => null
                ], 404);
            }
            
            $table->name = $request->name;
            $table->description = $request->description;
            $table->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Table updated successfully',
                'data' => $table
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
    
    /**
     * Change table status (Active/Inactive)
     */
    public function status(Request $request, $id)
    {
        try {
            $table = TableManage::where('id', $id)
                ->where('restaurant_id', $request->user()->restaurant_id)
                ->first();
            
            if (!$table) {
                return response()->json([
                    'success' => false,
                    'message' => 'Table not found',
                    'data' => null
                ], 404);
            }
            
            $table->status = $table->status === 'A' ? 'I' : 'A';
            $table->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'data' => [
                    'id' => $table->id,
                    'status' => $table->status,
                    'status_text' => $table->status === 'A' ? 'Active' : 'Inactive'
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
    
    /**
     * Delete table (Soft delete - status 'D')
     */
    public function delete(Request $request, $id)
    {
        try {
            $table = TableManage::where('id', $id)
                ->where('restaurant_id', $request->user()->restaurant_id)
                ->first();
            
            if (!$table) {
                return response()->json([
                    'success' => false,
                    'message' => 'Table not found',
                    'data' => null
                ], 404);
            }
            
            $table->status = 'D';
            $table->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Table deleted successfully',
                'data' => null
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}