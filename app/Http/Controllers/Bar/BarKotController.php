<?php

namespace App\Http\Controllers\Bar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderItems;
use App\Models\OrderManage;
use App\Models\TableManage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BarKotController extends Controller
{
    /**
     * Display the Live Bar KOT Counter.
     */
    public function index(Request $request)
    {
        $restaurantId = auth()->user()->restaurant_id;

        $tables = TableManage::where('restaurant_id', $restaurantId)
            ->where('status', 'A')
            ->orderBy('name', 'asc')
            ->get();

        // Query only bar-related order items
        $query = OrderItems::with(['order.table', 'subcategory.category', 'subcategory.brand', 'barVariation'])
            ->where('restaurant_id', $restaurantId)
            ->where(function($q) {
                $q->whereNotNull('bar_variation_id')
                  ->orWhere('vat_rate', '>', 0)
                  ->orWhereHas('subcategory', function($sq) {
                      $sq->where('food_type', 'BAR')
                        ->orWhereHas('category', function($cq) {
                            $cq->where('category_type', 'BAR');
                        });
                  });
            });

        // Date range filter (default: last 2 days)
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        } elseif ($request->filled('from_date')) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $query->where('created_at', '>=', $fromDate);
        } elseif ($request->filled('to_date')) {
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $query->where('created_at', '<=', $toDate);
        } else {
            $fromDate = Carbon::now()->subDays(1)->startOfDay();
            $toDate = Carbon::now()->endOfDay();
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        }

        // Status Filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('order_status', $request->status);
        }

        // Table Filter
        if ($request->filled('table_id')) {
            $query->whereHas('order', function($q) use ($request) {
                $q->where('table_id', $request->table_id);
            });
        }

        $barItems = $query->orderBy('created_at', 'desc')->get();

        // Group into Bar KOT ticket cards (same KOT No. and Order ID share a single ticket)
        $kotGroups = $barItems->groupBy(function($item) {
            $kotKey = !empty($item->kot_no) ? $item->kot_no : ('BAR_' . $item->id);
            return ($item->order_id ?? 0) . '_' . $kotKey;
        })->map(function($items) {
            $first = $items->first();

            // Status for the Bar KOT card: 'PENDING' vs 'SERVED'
            $statuses = $items->pluck('order_status')->unique()->toArray();
            if (count($statuses) === 1) {
                $overallStatus = $statuses[0];
            } elseif (in_array('PENDING', $statuses)) {
                $overallStatus = 'PENDING';
            } else {
                $overallStatus = 'SERVED';
            }

            return (object) [
                'card_id' => 'barkot_' . ($first->kot_no ? preg_replace('/[^a-zA-Z0-9_-]/', '_', $first->kot_no) : $first->id) . '_' . ($first->order_id ?? 0),
                'order_id' => $first->order_id,
                'order_no' => @$first->order->order_id ?? $first->order_id,
                'kot_no' => $first->kot_no,
                'table_name' => @$first->order->table->name ?? 'Take Away',
                'is_takeaway' => empty(@$first->order->table),
                'customer_name' => @$first->order->customer_name ?: 'Walk-in Guest',
                'created_at' => $first->created_at,
                'status' => $overallStatus,
                'items' => $items,
                'item_ids' => $items->pluck('id')->toArray(),
                'primary_item_id' => $first->id,
                'total_quantity' => $items->sum('quantity'),
                'total_drinks' => $items->count(),
            ];
        });

        $fromDateStr = $request->from_date ?? Carbon::now()->subDays(1)->format('Y-m-d');
        $toDateStr = $request->to_date ?? Carbon::now()->format('Y-m-d');
        $selectedStatus = $request->status ?? 'all';
        $selectedTable = $request->table_id ?? '';

        return view('bar.kot_index', compact(
            'tables',
            'barItems',
            'kotGroups',
            'fromDateStr',
            'toDateStr',
            'selectedStatus',
            'selectedTable'
        ));
    }

    /**
     * Update status of Bar KOT item(s) (e.g. PENDING -> SERVED)
     */
    public function updateStatus(Request $request)
    {
        $restaurantId = auth()->user()->restaurant_id;
        $newStatus = strtoupper($request->order_status ?? 'SERVED');

        // 1. By KOT Number + Order ID
        if ($request->filled('kot_no') && $request->filled('order_id')) {
            OrderItems::where('restaurant_id', $restaurantId)
                ->where('order_id', $request->order_id)
                ->where('kot_no', $request->kot_no)
                ->update([
                    'order_status' => $newStatus,
                    'is_new' => 0
                ]);

            return response()->json([
                'success' => true,
                'message' => "All drinks in KOT {$request->kot_no} updated to {$newStatus}."
            ]);
        }

        // 2. By Item IDs Array
        if ($request->filled('item_ids') && is_array($request->item_ids)) {
            OrderItems::where('restaurant_id', $restaurantId)
                ->whereIn('id', $request->item_ids)
                ->update([
                    'order_status' => $newStatus,
                    'is_new' => 0
                ]);

            return response()->json([
                'success' => true,
                'message' => "Bar items status updated to {$newStatus}."
            ]);
        }

        // 3. By Single Item ID
        if ($request->filled('id')) {
            $item = OrderItems::where('restaurant_id', $restaurantId)->find($request->id);
            if ($item) {
                $item->order_status = $newStatus;
                $item->is_new = 0;
                $item->save();

                return response()->json([
                    'success' => true,
                    'message' => "Drink item status updated to {$newStatus}."
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Bar item or KOT not found.'
        ], 404);
    }

    /**
     * Delete a Bar KOT item or whole KOT ticket.
     * Restriction: Allowed ONLY when status is PENDING. Once SERVED, it CANNOT be deleted.
     */
    public function deleteItem(Request $request, $id = null)
    {
        $restaurantId = auth()->user()->restaurant_id;
        $itemId = $id ?: $request->id;

        if ($request->filled('kot_no') && $request->filled('order_id')) {
            // Check if any item in this KOT is SERVED
            $servedCount = OrderItems::where('restaurant_id', $restaurantId)
                ->where('order_id', $request->order_id)
                ->where('kot_no', $request->kot_no)
                ->where('order_status', 'SERVED')
                ->count();

            if ($servedCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete this Bar KOT because one or more drinks have already been SERVED.'
                ], 403);
            }

            OrderItems::where('restaurant_id', $restaurantId)
                ->where('order_id', $request->order_id)
                ->where('kot_no', $request->kot_no)
                ->where('order_status', 'PENDING')
                ->delete();

            return response()->json([
                'success' => true,
                'message' => "Bar KOT {$request->kot_no} was successfully removed."
            ]);
        }

        $item = OrderItems::where('restaurant_id', $restaurantId)->find($itemId);
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Bar item not found.'
            ], 404);
        }

        // Rule: If already served, CANNOT delete!
        if (strtoupper($item->order_status) === 'SERVED') {
            return response()->json([
                'success' => false,
                'message' => 'This drink has already been SERVED and cannot be deleted.'
            ], 403);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bar order item removed successfully.'
        ]);
    }

    /**
     * Auto-sync check for new pending bar orders.
     */
    public function refreshOrders()
    {
        $restaurantId = auth()->user()->restaurant_id;

        $newOrdersCount = OrderItems::where('created_at', '>=', Carbon::now()->subSeconds(30))
            ->where('order_status', 'PENDING')
            ->where('is_new', 1)
            ->where('restaurant_id', $restaurantId)
            ->where(function($q) {
                $q->whereNotNull('bar_variation_id')
                  ->orWhere('vat_rate', '>', 0)
                  ->orWhereHas('subcategory', function($sq) {
                      $sq->where('food_type', 'BAR');
                  });
            })
            ->count();

        return response()->json([
            'new_orders' => $newOrdersCount > 0,
            'count' => $newOrdersCount
        ]);
    }
}
