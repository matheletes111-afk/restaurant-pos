<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TableManage;
use App\Models\Category;
use App\Models\User;
use App\Models\SubCategory;
use App\Models\OrderManage;
use App\Models\OrderItems;
use App\Models\RestaurantMaster;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\FirebasePushService;
class OrderManagementController extends Controller
{
    public function index()
    {
        $data['data'] = TableManage::where('restaurant_id',auth()->user()->restaurant_id)->where('status', 'A')->get();
        return view('order.index', $data);
    }

public function create($table_id = null)
{
    $data = [];
    $data['takeaway'] = false;
    $data['table'] = null;

    if ($table_id === 'TAKEAWAY') {
        $data['takeaway'] = true;
    } elseif ($table_id) {
        $data['table'] = TableManage::find($table_id);

        if ($data['table']->restaurant_id!=auth()->user()->restaurant_id) {
            return redirect()->back()
                    ->with('error', 'Unauthorized Access');
        }

        if ($data['table'] && $data['table']->table_status === 'OCCUPIED') {
            $order = OrderManage::where('table_id', $table_id)
                ->where('order_status', 'PENDING')
                ->latest()
                ->first();

            if ($order) {
                return redirect()->route('order.edit', $order->id);
            }
        }
    }

    $data['categories'] = Category::where('restaurant_id',auth()->user()->restaurant_id)->with('subcategories')->get();
    $data['payment_methods'] = ['Cash', 'UPI', 'Card']; // for dropdown

    return view('order.create', $data);
}

public function edit($order_id)
{
    $order = OrderManage::with('orderItems.subcategory')->findOrFail($order_id);
    if (@$order->restaurant_id!=auth()->user()->restaurant_id) {
        return redirect()->back()
                    ->with('error', 'Unauthorized Access');
    }

    // Calculate totals
    $subtotal = 0;
    $gstTotal = 0;
    foreach ($order->orderItems as $item) {
        $price = $item->price;
        $qty = $item->quantity;
        $gstRate = $item->subcategory->gst_rate ?? 0;
        $subtotal += $price * $qty;
        $gstTotal += ($price * $qty * $gstRate) / 100;
    }

    $finalTotal = $subtotal + $gstTotal - (($order->discount ?? 0) / 100 * $subtotal);

    $data['order'] = $order;
    $data['table'] = $order->table_id ? TableManage::find($order->table_id) : null;
    $data['categories'] = Category::where('restaurant_id',auth()->user()->restaurant_id)->with('subcategories')->get();
    $data['payment_methods'] = ['CASH', 'CARD', 'UPI'];

    // ✅ Pass calculated values
    $data['subtotal'] = $subtotal;
    $data['gstTotal'] = $gstTotal;
    $data['finalTotal'] = $finalTotal;

    return view('order.edit', $data);
}



public function store(Request $request)
{
    $validated = $request->validate([
        'customer_name' => 'required|string|max:255',
        'order_items' => 'required|array|min:1',
        'discount' => 'nullable|numeric|min:0',
    ]);

    $isTakeaway = empty($request->table_id);
    $subtotal = 0;
    $gstTotal = 0;

    foreach ($request->order_items as $item) {
        $subtotal += $item['price'] * $item['qty'];
        $gstTotal += ($item['price'] * $item['qty'] * $item['gst']) / 100;
    }

    $discountAmount = (($subtotal + $gstTotal) * ($request->discount ?? 0)) / 100;
    $finalTotal = $subtotal + $gstTotal - $discountAmount;

    $restaurantId = auth()->user()->restaurant_id;
    $today = Carbon::now()->format('Ymd');

    // count today's orders for this restaurant
    $todayCount = OrderManage::where('restaurant_id', $restaurantId)
        ->whereDate('created_at', Carbon::today())
        ->count() + 1;

    // pad sequence number (0001, 0002…)
    $sequence = str_pad($todayCount, 4, '0', STR_PAD_LEFT);

    // final order number
    $orderNo = "ORD-{$restaurantId}-{$today}-{$sequence}";

    $order = new OrderManage();
    $order->customer_name = $request->customer_name;
    $order->customer_phone = $request->customer_phone;
    $order->order_id = $orderNo;
    $order->table_id = $request->table_id;
    $order->discount = $request->discount ?? 0;
    $order->total_amount = $subtotal;
    $order->gst_amount = $gstTotal;
    $order->grand_total = $finalTotal;
     if (@$isTakeaway) {
        $order->amount_paid = round($finalTotal);
    }
    $order->order_type = $isTakeaway ? 'TAKEAWAY' : 'DINE_IN';
    $order->remarks = $request->remarks ?? null;
    $order->payment_status = $isTakeaway ? ($request->payment_status ?? 'PENDING') : 'PENDING';
    $order->payment_method = $isTakeaway ? ($request->payment_method ?? null) : null;
    $order->order_status = 'PENDING';
    $order->restaurant_id = auth()->user()->restaurant_id;
    $order->user_id = auth()->user()->id;
    $order->save();
   

    foreach ($request->order_items as $item) {
        OrderItems::create([
            'order_id' => $order->id,
            'subcategory_id' => $item['id'], // assuming $item['id'] is subcategory_id
            'quantity' => $item['qty'],
            'price' => $item['price'],
            'gst_rate' => $item['gst'],
            'total_amount' => ($item['price'] * $item['qty']) + (($item['price'] * $item['qty'] * $item['gst']) / 100),
            'order_status' => 'PENDING',
            'restaurant_id'=>auth()->user()->restaurant_id,
            'user_id'=>auth()->user()->id,
        ]);
    }

    $table = TableManage::where('id',$request->table_id)->update([
        'table_status'=>'OCCUPIED',
        'order_id'=>$order->id,
    ]);
   
  
      $kitchenStaffs = User::where('role_type', 'Kitchen Staff')
    ->whereNotNull('fcm_token')->where('restaurant_id',auth()->user()->restaurant_id)
    ->where('status', 'A')
    ->get();

    foreach ($kitchenStaffs as $staff) {
    FirebasePushService::send(
        $staff->fcm_token,
        'New Order Received',
        'Order #' . $order->id . ' received',
        [
            'order_id' => (string) $order->id,
            'type' => 'new_order'
        ]
    );
}


   return response()->json([
    'success' => true,
    'final_total' => $finalTotal,
    'invoice_url' => $isTakeaway
        ? route('order.invoice', $order->id)
        : null
]);

}


public function pdfReceipt($order_id)
{
    $order = OrderManage::with('items.subcategory')->findOrFail($order_id);
    $details = OrderManage::where('id',$order_id)->first();
    $restaurant_details = RestaurantMaster::where('id',$order->restaurant_id)->first();

    $pdf = Pdf::loadView('receipt', compact('order','restaurant_details'))
        ->setPaper([0, 0, 226, 600]); // 57mm thermal

    return $pdf->stream('receipt_'.$order->id.'.pdf');
}

public function update(Request $request, $id)
{
    DB::beginTransaction();
    try {
        $order = OrderManage::findOrFail($id);

        // Update payment fields with new fields
        $order->discount = $request->discount ?? 0;
        $order->payment_method = $request->payment_method;
        $order->payment_status = $request->payment_status;
        $order->remarks = $request->remarks;
        $order->customer_phone = $request->customer_phone; 
        
        // Store amount_paid if provided
        if ($request->has('amount_paid') && $request->amount_paid !== null) {
            $order->amount_paid = floatval($request->amount_paid);
        }
        
        $order->save();

        // Handle new item additions
        if ($request->has('order_items') && is_array($request->order_items)) {
            foreach ($request->order_items as $item) {
                $subcategory_id = $item['id'];
                $qty = intval($item['qty']);
                $price = floatval($item['price']);
                $gst = floatval($item['gst']);

                $gst_amount = ($price * $qty * $gst) / 100;
                $total_amount = ($price * $qty) + $gst_amount;

                // Create new row for new items
                OrderItems::create([
                    'order_id'      => $id,
                    'subcategory_id'=> $subcategory_id,
                    'quantity'      => $qty,
                    'price'         => $price,
                    'gst_rate'      => $gst,
                    'total_amount'  => $total_amount,
                    'restaurant_id'=> auth()->user()->restaurant_id,
                    'user_id'=> auth()->user()->id,
                    'order_status'  => 'PENDING'
                ]);
            }
        }

        // Handle item deletion
        if ($request->has('delete_item_id')) {
            OrderItems::where('id', $request->delete_item_id)->delete();
        }

        // Recalculate totals
        $items = OrderItems::where('order_id', $id)->get();
        $total = $items->sum(fn($i) => $i->price * $i->quantity);
        $gst = $items->sum(fn($i) => ($i->price * $i->quantity * $i->gst_rate) / 100);
        $discount = (($total + $gst) * $order->discount) / 100;
        $grandTotal = ($total + $gst) - $discount;
        
        // Round off logic
        $finalAmount = round($grandTotal);
        $roundOff = $finalAmount - $grandTotal;

        $order->total_amount = $total;
        $order->gst_amount = $gst;
        $order->grand_total = $finalAmount;
        
        // Auto-fill amount_paid if status is PAID and amount_paid is empty
        if ($order->payment_status === 'PAID' && empty($order->amount_paid)) {
            $order->amount_paid = $finalAmount;
        }
        
        // Clear amount_paid if status is not PAID (but keep for MISCORDER)
        if ($order->payment_status !== 'PAID' && $order->payment_status !== 'MISCORDER') {
            $order->amount_paid = null;
        }
        
        $order->save();

        // Release table for PAID or MISCORDER status
        if (in_array($order->payment_status, ['PAID', 'MISCORDER']) && $order->table_id) {
            $table = TableManage::find($order->table_id);
            if ($table) {
                $table->table_status = 'AVAILABLE';
                $table->order_id = null;
                $table->save();
            }
        }

        DB::commit();

        // AJAX Response
        if ($request->expectsJson() || $request->ajax()) {
            $response = [
                'success'     => true,
                'final_total' => number_format($finalAmount, 2),
                'amount_paid' => $order->amount_paid ? number_format($order->amount_paid, 2) : '0.00',
                'items'       => $items,
                'subtotal'    => (float) $total,
                'total_gst'   => (float) $gst,
                'round_off'   => number_format($roundOff, 2), // Add round off to response
            ];
            
            // 🔴 CHANGE HERE: Add redirect_url for both PAID and MISCORDER
            if (in_array($order->payment_status, ['PAID', 'MISCORDER'])) {
                $response['redirect_url'] = route('order.invoice', $order->id);
            }
            
            return response()->json($response);
        }

        // Non-AJAX redirect
        if (in_array($order->payment_status, ['PAID', 'MISCORDER'])) {
            return redirect()->route('order.management.dashboard')
                ->with('success', 'Order updated successfully. Table released.');
        }

        return redirect()->back()->with('success', 'Order updated successfully.');

    } catch (\Exception $e) {
        DB::rollBack();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }

        return redirect()->back()->with('error', $e->getMessage());
    }
}


public function invoicePage($order_id)
{
    $order = OrderManage::findOrFail($order_id);
   
    return view('order.invoice', compact('order'));
}






// Show payment page
    public function paymentPage($order_id)
    {
        $order = OrderManage::with('table')->findOrFail($order_id);
        $table = $order->table_id ? TableManage::find($order->table_id) : null;

        return view('order.payment', compact('order','table'));
    }

public function deleteOrderItem($id)
{
    $item = OrderItems::find($id);
    if ($item) {
        $item->delete();
        return response()->json(['success' => true]);
    }
    return response()->json(['success' => false, 'message' => 'Item not found']);
}



    // Submit payment
    public function submitPayment(Request $request, $order_id)
    {
        $request->validate([
            'payment_method' => 'required',
            'payment_status' => 'required|in:PENDING,PAID',
        ]);

        DB::transaction(function() use($request, $order_id){
            $order = OrderManage::findOrFail($order_id);
            $order->payment_method = $request->payment_method;
            $order->remarks = $request->remarks;
            $order->payment_status = $request->payment_status;
            $order->save();

            // Clear table if PAID
            if($request->payment_status == 'PAID' && $order->table_id){
                $table = TableManage::find($order->table_id);
                if($table){
                    $table->table_status = 'AVAILABLE';
                    $table->order_id = null;
                    $table->save();
                }
            }
        });

        return redirect()->route('order.management.dashboard')
                         ->with('success', 'Payment recorded successfully!');
    }

    public function kitchen(Request $request)
    {
        // Get tables for filter dropdown
        $data['tables'] = TableManage::where('restaurant_id', auth()->user()->restaurant_id)
            ->where('status', 'A')
            ->get();

        // Base query
        $query = OrderItems::with(['order', 'subcategory', 'order.table'])
            ->where('restaurant_id', auth()->user()->restaurant_id);

        // Date range filter
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $fromDate = Carbon::parse($request->from_date)->startOfDay();
            $toDate = Carbon::parse($request->to_date)->endOfDay();
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        } else {
            // Default: last 3 days
            $query->where('created_at', '>=', Carbon::now()->subDays(3)->startOfDay());
        }

        // Status filter
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('order_status', $request->status);
        }

        // Table filter
        if ($request->filled('table_id')) {
            $query->whereHas('order', function($q) use ($request) {
                $q->where('table_id', $request->table_id);
            });
        }

        // Order by latest first (using created_at desc)
        $data['OrderItems'] = $query->orderBy('created_at', 'desc')->get();

        // Pass filter values to view for form persistence
        $data['from_date'] = $request->from_date ?? Carbon::now()->subDays(3)->format('Y-m-d');
        $data['to_date'] = $request->to_date ?? Carbon::now()->format('Y-m-d');
        $data['selected_status'] = $request->status ?? 'all';
        $data['selected_table'] = $request->table_id ?? '';

        return view('kitchen.index', $data);
    }

    public function updateKitchenStatus(Request $request)
    {
        $item = OrderItems::find($request->id);
        if ($item) {
            $item->order_status = $request->order_status;
            $item->is_new = 0;
            $item->save();

            return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Item not found.']);
    }


    public function refreshOrders()
    {
        // Get count of new orders in last 30 seconds
        $newOrdersCount = OrderItems::where('created_at', '>=', Carbon::now()->subSeconds(30))
            ->where('order_status', 'PENDING')
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->count();
            
        return response()->json([
            'new_orders' => $newOrdersCount > 0,
            'count' => $newOrdersCount
        ]);
    }


}
