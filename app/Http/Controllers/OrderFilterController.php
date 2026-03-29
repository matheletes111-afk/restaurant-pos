<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TableManage;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\OrderManage;
use App\Models\OrderItems;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class OrderFilterController extends Controller
{
    public function index(Request $request)
    {
        $query = OrderManage::query()
            ->whereIN('payment_status', ['PAID','MISCORDER']);

        // Apply date filters
        if ($request->filled('from_date')) {
            $from = Carbon::parse($request->from_date)->startOfDay();
            $query->where('created_at', '>=', $from);
        }

        if ($request->filled('to_date')) {
            $to = Carbon::parse($request->to_date)->endOfDay();
            $query->where('created_at', '<=', $to);
        }

        $orders = $query->orderBy('created_at', 'desc')->where('restaurant_id',auth()->user()->restaurant_id)->get();

        return view('order_report', compact('orders'));
    }

    public function orderDetails($order_id)
    {
        $restaurantId = auth()->user()->restaurant_id;

        $order = OrderManage::with([
            'items.subcategory',
            'table',
            'user'
        ])
        ->where('id', $order_id)
        ->where('restaurant_id', $restaurantId)
        ->first();

        if (!$order) {
            return redirect()->back()->with('error', 'Order not found');
        }

        return view('order.order-details', compact('order'));
    }

}
