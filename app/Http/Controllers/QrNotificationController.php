<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TempOrder;
use App\Models\OrderManage;
use App\Models\OrderItems;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class QrNotificationController extends Controller
{
    /**
     * Get list of QR order notifications for the past 1 week (7 days)
     */
    public function getNotifications(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $restaurantId = $user->restaurant_id;

        if (!$restaurantId) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
                'total_count' => 0,
                'notifications' => [],
                'latest_id' => 0,
            ]);
        }

        $notifications = collect();

        // 1. Initial Pending Temp Orders
        $tempOrders = TempOrder::with(['table_details', 'items.menuItem'])
            ->where('restaurant_id', $restaurantId)
            ->where(function ($q) {
                $q->where('order_status', 'PENDING')
                  ->orWhereNull('order_status');
            })
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('id', 'DESC')
            ->get();

        foreach ($tempOrders as $order) {
            $tableName = $order->table_details ? $order->table_details->name : ($order->table_id ? 'Table ' . $order->table_id : 'Dine-In');
            $customerName = $order->customer_name ?: 'Customer';
            $itemsSummary = $order->items->map(function ($item) {
                $qty = $item->quantity > 1 ? $item->quantity . 'x ' : '';
                $name = $item->menuItem ? $item->menuItem->name : 'Dish';
                return $qty . $name;
            })->take(3)->implode(', ');

            if ($order->items->count() > 3) {
                $itemsSummary .= ' +' . ($order->items->count() - 3) . ' more';
            }

            $notifications->push([
                'id' => $order->id,
                'raw_id' => $order->id,
                'notif_type' => 'initial_order',
                'order_no' => $order->order_id ?? ('#' . $order->id),
                'table_name' => $tableName,
                'customer_name' => $customerName,
                'customer_phone' => $order->customer_phone,
                'notification_title' => "{$customerName} placed a new order for {$tableName}",
                'items_count' => $order->items->count(),
                'items_summary' => $itemsSummary,
                'grand_total' => number_format((float)$order->grand_total, 2),
                'order_status' => 'PENDING',
                'is_read' => (bool)$order->is_read,
                'created_at_human' => $order->created_at ? $order->created_at->diffForHumans() : '',
                'created_at_time' => $order->created_at ? $order->created_at->format('h:i A') : '',
                'created_at_date' => $order->created_at ? $order->created_at->format('M d') : '',
                'timestamp' => $order->created_at ? $order->created_at->timestamp : 0,
                'view_url' => route('temp.orders.view', $order->id),
            ]);
        }

        // 2. Active Orders with Additional Items ordered by customer (after order acceptance)
        $activeOrdersWithItems = OrderManage::with(['table', 'orderItems.subcategory'])
            ->where('restaurant_id', $restaurantId)
            ->whereHas('orderItems', function ($q) {
                $q->where('is_new', 1);
            })
            ->where('order_complete', '!=', 'DONE')
            ->where('payment_status', '!=', 'PAID')
            ->whereNotIn('order_status', ['COMPLETED', 'CANCELLED', 'REJECTED'])
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('updated_at', 'DESC')
            ->get();

        foreach ($activeOrdersWithItems as $mainOrder) {
            $tableName = $mainOrder->table ? $mainOrder->table->name : ($mainOrder->table_id ? 'Table ' . $mainOrder->table_id : 'Table');
            $customerName = $mainOrder->customer_name ?: 'Customer';
            $newItems = $mainOrder->orderItems->where('is_new', 1);
            $latestItem = $newItems->sortByDesc('id')->first();
            $latestKot = $latestItem ? $latestItem->kot_no : null;

            $itemsSummary = $newItems->map(function ($item) {
                $qty = $item->quantity > 1 ? $item->quantity . 'x ' : '';
                $name = $item->subcategory ? $item->subcategory->name : 'Dish';
                return $qty . $name;
            })->take(3)->implode(', ');

            if ($newItems->count() > 3) {
                $itemsSummary .= ' +' . ($newItems->count() - 3) . ' more';
            }

            if ($latestKot) {
                $itemsSummary = "[{$latestKot}] " . $itemsSummary;
            }

            $notifications->push([
                'id' => 'main_' . $mainOrder->id,
                'raw_id' => $mainOrder->id,
                'notif_type' => 'additional_items',
                'order_no' => '',
                'table_name' => $tableName,
                'customer_name' => $customerName,
                'customer_phone' => $mainOrder->customer_phone,
                'notification_title' => "{$customerName} added new item in {$tableName}",
                'items_count' => $newItems->count(),
                'items_summary' => "{$customerName} added new item in {$tableName}",
                'grand_total' => '',
                'order_status' => 'NEW ITEMS',
                'is_read' => false,
                'created_at_human' => $latestItem && $latestItem->created_at ? $latestItem->created_at->diffForHumans() : ($mainOrder->updated_at ? $mainOrder->updated_at->diffForHumans() : ''),
                'created_at_time' => $latestItem && $latestItem->created_at ? $latestItem->created_at->format('h:i A') : ($mainOrder->updated_at ? $mainOrder->updated_at->format('h:i A') : ''),
                'created_at_date' => $latestItem && $latestItem->created_at ? $latestItem->created_at->format('M d') : ($mainOrder->updated_at ? $mainOrder->updated_at->format('M d') : ''),
                'timestamp' => $latestItem && $latestItem->created_at ? $latestItem->created_at->timestamp : ($mainOrder->updated_at ? $mainOrder->updated_at->timestamp : 0),
                'view_url' => url('order-edit/' . $mainOrder->id),
            ]);
        }

        // Sort all notifications by newest timestamp
        $sortedNotifications = $notifications->sortByDesc('timestamp')->values();
        $unreadCount = $sortedNotifications->where('is_read', false)->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
            'total_count' => $sortedNotifications->count(),
            'notifications' => $sortedNotifications,
            'latest_id' => $sortedNotifications->first()['id'] ?? 0,
        ]);
    }

    /**
     * Mark a single notification as read
     */
    public function markRead(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $restaurantId = $user->restaurant_id;
        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized - No restaurant assigned'], 403);
        }

        if (str_starts_with((string)$id, 'main_')) {
            $mainOrderId = (int) str_replace('main_', '', (string)$id);
            OrderItems::where('order_id', $mainOrderId)
                ->where('restaurant_id', $restaurantId)
                ->where('is_new', 1)
                ->update(['is_new' => 0]);
            return response()->json(['success' => true]);
        }

        $order = TempOrder::where('id', $id)
            ->where('restaurant_id', $restaurantId)
            ->first();

        if ($order) {
            $order->update([
                'is_read' => true,
                'read_at' => Carbon::now()
            ]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Order not found'], 404);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllRead(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $restaurantId = $user->restaurant_id;
        if (!$restaurantId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized - No restaurant assigned'], 403);
        }

        TempOrder::where('restaurant_id', $restaurantId)
            ->where(function ($q) {
                $q->where('is_read', false)
                  ->orWhereNull('is_read')
                  ->orWhere('is_read', 0);
            })->update([
                'is_read' => true,
                'read_at' => Carbon::now()
            ]);

        OrderItems::where('restaurant_id', $restaurantId)
            ->where('is_new', 1)
            ->update(['is_new' => 0]);

        return response()->json([
            'success' => true,
            'unread_count' => 0
        ]);
    }
}
