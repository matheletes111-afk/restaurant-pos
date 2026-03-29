<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt #{{ $order->id }}</title>
    <style>
        body {
            font-family: monospace;
            font-size: 10px;
            width: 57mm;
            margin: 0;
            padding: 0;
        }
        .center { text-align: center; }
        .line { border-bottom: 1px dashed #000; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 2px 0; }
        th { text-align: left; }
        td.right { text-align: right; }
        .total { font-weight: bold; }
    </style>
</head>
<body>
    <!-- Restaurant Info -->
    <div class="center">
        <strong>{{$restaurant_details->name}}</strong><br>
       {{@$restaurant_details->address}}<br>
       <br>
    </div>
    <div class="line"></div>

    <!-- Order Info -->
    <p>
        <strong>Order ID:</strong> {{ $order->order_id }}<br>
        <strong>Customer:</strong> {{ $order->customer_name }}<br>
        <strong>Table:</strong> {{ $order->table_id ? 'Table '.$order->table_id : 'Takeaway' }}<br>
        <strong>Order Type:</strong> {{ strtoupper($order->order_type) }}<br>
        <strong>Date:</strong> {{ $order->created_at->format('d M Y, h:i A') }}<br>
    </p>
    <div class="line"></div>

    <!-- Items -->
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="right">Qty</th>
                <th class="right">Rate</th>
                <th class="right">Amt</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ Str::limit($item->subcategory->name ?? 'Item', 20) }}</td>
                <td class="right">{{ $item->quantity }}</td>
                <td class="right">{{ number_format($item->price, 2) }}</td>
                <td class="right">{{ number_format($item->price * $item->quantity, 2) }}</td>
            </tr>
            <tr>
                <td colspan="4" style="font-size:9px; text-align:right;">
                    GST ({{ number_format($item->gst_rate, 2) }}%): {{ number_format(($item->price * $item->quantity * $item->gst_rate) / 100, 2) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="line"></div>

    <!-- Totals -->
    @php
        $subtotal = $order->total_amount;
        $gst = $order->gst_amount;
        $discountAmount = (($subtotal + $gst) * ($order->discount / 100));
        $grandTotal = $order->grand_total;
    @endphp

    <table>
        <tr>
            <td>Subtotal</td>
            <td class="right">{{ number_format($subtotal,2) }}</td>
        </tr>
        <tr>
            <td>GST Total</td>
            <td class="right">{{ number_format($gst,2) }}</td>
        </tr>
        <tr>
            <td>Discount ({{ $order->discount }}%)</td>
            <td class="right">{{ number_format($discountAmount,2) }}</td>
        </tr>

        @php
            $finalAmount = round($grandTotal);   // round off
            $roundOff = $finalAmount - $grandTotal;
        @endphp

        <tr>
            <td>Grand Total</td>
            <td class="right">{{ number_format($grandTotal, 2) }}</td>
        </tr>

        <tr>
            <td>Round Off</td>
            <td class="right">{{ number_format($roundOff, 2) }}</td>
        </tr>

        <tr class="total">
            <td><strong>Final Bill Amount</strong></td>
            <td class="right"><strong>{{ number_format($finalAmount, 2) }}</strong></td>
        </tr>

    </table>
    <div class="line"></div>

    <div class="center">
        Thank you for visiting!<br>
        www.demorestaurant.com
    </div>
</body>
</html>
