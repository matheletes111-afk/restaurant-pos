<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\RestaurantMaster;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PaymentHistoryController extends Controller
{
    /**
     * Display payment history for all restaurants (Admin)
     */
    public function index(Request $request)
    {
        // Base query with relationships
        $query = Payment::with(['subscription.restaurant_details', 'subscription.plan', 'plan'])
            ->orderBy('created_at', 'desc');
        
        // Apply filters
        if ($request->filled('restaurant_id')) {
            $query->whereHas('subscription', function($q) use ($request) {
                $q->where('user_id', $request->restaurant_id);
            });
        }
        
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('plan_id')) {
            $query->where('plan_id', $request->plan_id);
        }
        
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        
        $payments = $query->paginate(15);
        
        // Get all restaurants for filter
        $restaurants = RestaurantMaster::where('status', 'A')->orderBy('name', 'asc')->get();
        
        // Get all plans for filter
        $plans = Plan::where('is_delete', 'N')->orderBy('name', 'asc')->get();
        
        // Statistics
        $statistics = [
            'total_amount' => Payment::sum('amount') ?? 0,
            'total_payments' => Payment::count(),
            'successful_payments' => Payment::where('status', 'captured')->count(),
            'failed_payments' => Payment::where('status', 'failed')->count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'this_month_amount' => Payment::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount') ?? 0,
        ];
        
        $statuses = [
            'all' => 'All',
            'captured' => 'Success',
            'pending' => 'Pending',
            'failed' => 'Failed',
            'refunded' => 'Refunded'
        ];
        
        return view('admin.payment-history', compact('payments', 'restaurants', 'plans', 'statistics', 'statuses'));
    }
    
    /**
     * Show payment details
     */
    public function show($id)
    {
        $payment = Payment::with(['subscription.restaurant_details', 'subscription.plan', 'plan'])
            ->findOrFail($id);
        
        // Get restaurant details from subscription
        $restaurant = null;
        if ($payment->subscription) {
            $restaurant = $payment->subscription->restaurant_details;
        }
        
        return response()->json([
            'success' => true,
            'data' => $payment,
            'restaurant' => $restaurant
        ]);
    }
}