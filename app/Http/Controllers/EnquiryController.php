<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EnquiryManagement;
use App\Models\RestaurantMaster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EnquiryController extends Controller
{
    /**
     * Display enquiry management page for restaurant
     */
    public function index(Request $request)
    {
        $restaurantId = auth()->user()->restaurant_id;
        
        // Base query - only show enquiries for this restaurant
        $query = EnquiryManagement::with(['restaurant', 'creator', 'replier'])
            ->where('restaurant_id', $restaurantId);
        
        // Apply filters - FIXED: Use $request->input() instead of direct access
        if ($request->filled('status') && $request->input('status') != 'all') {
            $query->where('status', $request->input('status'));
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }
        
        $enquiries = $query->orderBy('created_at', 'desc')->paginate(20);
        
        $statuses = [
            'all' => 'All',
            'NEW' => 'New',
            'AT' => 'Action Taken'
        ];
        
        return view('enquiry.index', compact('enquiries', 'statuses'));
    }
    
    /**
     * Store new enquiry (Restaurant User)
     */
    public function store(Request $request)
    {
       
        try {
            $enquiry = new EnquiryManagement();
            $enquiry->restaurant_id = auth()->user()->restaurant_id;
            $enquiry->query = $request->input('query');
            $enquiry->created_by = auth()->id();
            $enquiry->status = EnquiryManagement::STATUS_NEW; // Default status NEW
            $enquiry->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Enquiry submitted successfully. We will get back to you soon.',
                'data' => $enquiry
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete enquiry (Restaurant User can delete their own enquiries)
     */
    public function destroy($id)
    {
        try {
            $enquiry = EnquiryManagement::where('id', $id)
                ->where('restaurant_id', auth()->user()->restaurant_id)
                ->first();
            
            if (!$enquiry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Enquiry not found or unauthorized'
                ], 404);
            }
            
            $enquiry->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Enquiry deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get enquiry details for view
     */
    public function show($id)
    {
        try {
            $enquiry = EnquiryManagement::with(['restaurant', 'creator', 'replier'])
                ->where('id', $id)
                ->where('restaurant_id', auth()->user()->restaurant_id)
                ->first();
            
            if (!$enquiry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Enquiry not found'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $enquiry
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Enquiry not found'
            ], 404);
        }
    }
    
    /**
     * Get count of new enquiries for notification
     */
    public function getNewEnquiriesCount(Request $request)
    {
        $count = EnquiryManagement::where('restaurant_id', auth()->user()->restaurant_id)
            ->where('status', 'NEW')
            ->count();
        
        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }
}