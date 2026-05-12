<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EnquiryManagement;
use App\Models\RestaurantMaster;
use Illuminate\Support\Facades\DB;

class AdminEnquiryController extends Controller
{
    /**
     * Display all enquiries for admin
     */
    public function index(Request $request)
    {
        // Base query with relationships
        $query = EnquiryManagement::with(['restaurant', 'creator', 'replier']);
        
        // Apply filters
        if ($request->filled('status') && $request->input('status') != 'all') {
            $query->where('status', $request->input('status'));
        }
        
        if ($request->filled('restaurant_id') && $request->input('restaurant_id') != 'all') {
            $query->where('restaurant_id', $request->input('restaurant_id'));
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }
        
        $enquiries = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Get all active restaurants for filter
        $restaurants = RestaurantMaster::where('status', 'A')->get();
        
        $statuses = [
            'all' => 'All',
            'NEW' => 'New',
            'AT' => 'Action Taken'
        ];
        
        // Statistics
        $statistics = [
            'total' => EnquiryManagement::count(),
            'new' => EnquiryManagement::where('status', 'NEW')->count(),
            'resolved' => EnquiryManagement::where('status', 'AT')->count(),
            'this_week' => EnquiryManagement::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];
        
        return view('admin.enquiry.index', compact('enquiries', 'restaurants', 'statuses', 'statistics'));
    }
    
    /**
     * Show single enquiry details
     */
    public function show($id)
    {
        try {
            $enquiry = EnquiryManagement::with(['restaurant', 'creator', 'replier'])->findOrFail($id);
            
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
     * Reply to enquiry (Change status to AT - Action Taken)
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'reply' => 'required|string|min:2|max:1000'
        ]);
        
        try {
            $enquiry = EnquiryManagement::findOrFail($id);
            
            DB::beginTransaction();
            
            $enquiry->query_reply = $request->input('reply');
            $enquiry->replier_by = auth()->id();
            $enquiry->status = EnquiryManagement::STATUS_AT; // Action Taken
            $enquiry->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Reply sent successfully. Enquiry marked as Action Taken.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete enquiry (Admin only)
     */
    public function destroy($id)
    {
        try {
            $enquiry = EnquiryManagement::findOrFail($id);
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
     * Bulk action - Update multiple enquiries
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:enquiry_management,id',
            'action' => 'required|in:delete,mark_as_resolved'
        ]);
        
        try {
            if ($request->input('action') == 'delete') {
                EnquiryManagement::whereIn('id', $request->input('ids'))->delete();
                $message = count($request->input('ids')) . ' enquiries deleted successfully';
            } else {
                EnquiryManagement::whereIn('id', $request->input('ids'))->update([
                    'status' => EnquiryManagement::STATUS_AT,
                    'replier_by' => auth()->id(),
                    'updated_at' => now()
                ]);
                $message = count($request->input('ids')) . ' enquiries marked as resolved';
            }
            
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Export enquiries to Excel
     */
    public function export(Request $request)
    {
        $query = EnquiryManagement::with(['restaurant', 'creator']);
        
        if ($request->filled('status') && $request->input('status') != 'all') {
            $query->where('status', $request->input('status'));
        }
        
        if ($request->filled('restaurant_id') && $request->input('restaurant_id') != 'all') {
            $query->where('restaurant_id', $request->input('restaurant_id'));
        }
        
        $enquiries = $query->orderBy('created_at', 'desc')->get();
        
        // Prepare CSV data
        $csvData = [];
        $csvData[] = ['ID', 'Restaurant', 'Query', 'Status', 'Created By', 'Created At', 'Reply', 'Replied By', 'Replied At'];
        
        foreach ($enquiries as $enquiry) {
            $csvData[] = [
                $enquiry->id,
                $enquiry->restaurant->name ?? 'N/A',
                $enquiry->query,
                $enquiry->status == 'NEW' ? 'New' : 'Action Taken',
                $enquiry->creator->name ?? 'N/A',
                $enquiry->created_at->format('d M Y h:i A'),
                $enquiry->query_reply ?? 'No reply yet',
                $enquiry->replier->name ?? 'N/A',
                $enquiry->updated_at->format('d M Y h:i A')
            ];
        }
        
        // Create CSV file
        $filename = 'enquiries_report_' . date('Y-m-d') . '.csv';
        $handle = fopen('php://temp', 'w');
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);
        
        return response($content)
            ->withHeaders([
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
    }
    
    /**
     * Get statistics for dashboard
     */
    public function getStats()
    {
        $stats = [
            'total' => EnquiryManagement::count(),
            'new' => EnquiryManagement::where('status', 'NEW')->count(),
            'resolved' => EnquiryManagement::where('status', 'AT')->count(),
            'pending_percentage' => EnquiryManagement::count() > 0 
                ? round((EnquiryManagement::where('status', 'NEW')->count() / EnquiryManagement::count()) * 100, 1)
                : 0
        ];
        
        // Get last 7 days data for chart
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $last7Days[] = [
                'date' => $date->format('d M'),
                'new' => EnquiryManagement::whereDate('created_at', $date)->where('status', 'NEW')->count(),
                'resolved' => EnquiryManagement::whereDate('updated_at', $date)->where('status', 'AT')->count()
            ];
        }
        
        return response()->json([
            'success' => true,
            'stats' => $stats,
            'chart_data' => $last7Days
        ]);
    }
}