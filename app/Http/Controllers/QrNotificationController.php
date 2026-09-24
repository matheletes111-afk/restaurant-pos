<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TempOrder;
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

        $query = TempOrder::with(['table_details', 'items.menuItem'])
            ->where('created_at', '>=', Carbon::now()->subDays(7));

        if ($restaurantId) {
            $query->where('restaurant_id', $restaurantId);
        }

        $orders = $query->orderBy('id', 'DESC')->get();
        $unreadCount = $orders->where('is_read', false)->count();

        $notifications = $orders->map(function ($order) {
            $tableName = $order->table_details ? $order->table_details->name : ($order->table_id ? 'Table ' . $order->table_id : 'Dine-In');
            $itemsSummary = $order->items->map(function ($item) {
                $qty = $item->quantity > 1 ? $item->quantity . 'x ' : '';
                $name = $item->menuItem ? $item->menuItem->name : 'Dish';
                return $qty . $name;
            })->take(3)->implode(', ');

            if ($order->items->count() > 3) {
                $itemsSummary .= ' +' . ($order->items->count() - 3) . ' more';
            }

            return [
                'id' => $order->id,
                'order_no' => $order->order_id ?? ('#' . $order->id),
                'table_name' => $tableName,
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'items_count' => $order->items->count(),
                'items_summary' => $itemsSummary,
                'grand_total' => number_format((float)$order->grand_total, 2),
                'order_status' => strtoupper($order->order_status ?? 'PENDING'),
                'is_read' => (bool)$order->is_read,
                'created_at_human' => $order->created_at ? $order->created_at->diffForHumans() : '',
                'created_at_time' => $order->created_at ? $order->created_at->format('h:i A') : '',
                'created_at_date' => $order->created_at ? $order->created_at->format('M d') : '',
                'timestamp' => $order->created_at ? $order->created_at->timestamp : 0,
                'view_url' => route('temp.orders.view', $order->id),
            ];
        });

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
            'total_count' => $orders->count(),
            'notifications' => $notifications,
            'latest_id' => $orders->max('id') ?? 0,
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

        $query = TempOrder::where('id', $id);
        if ($user->restaurant_id) {
            $query->where('restaurant_id', $user->restaurant_id);
        }

        $order = $query->first();
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

        $query = TempOrder::query();

        if ($restaurantId) {
            $query->where('restaurant_id', $restaurantId);
        }

        $query->where(function ($q) {
            $q->where('is_read', false)
              ->orWhereNull('is_read')
              ->orWhere('is_read', 0);
        })->update([
            'is_read' => true,
            'read_at' => Carbon::now()
        ]);

        return response()->json([
            'success' => true,
            'unread_count' => 0
        ]);
    }
}
