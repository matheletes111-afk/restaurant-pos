<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New QR Code Order Received</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #1e293b;
            margin: 0;
            padding: 0;
            background-color: #f1f5f9;
        }
        .container {
            max-width: 620px;
            margin: 25px auto;
            background-color: #ffffff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #ff6a00 0%, #ee0979 100%);
            padding: 32px 25px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .header p {
            margin: 8px 0 0;
            font-size: 14px;
            opacity: 0.92;
        }
        .content {
            padding: 30px 25px;
        }
        .badge-pill {
            display: inline-block;
            background: rgba(255, 255, 255, 0.25);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .card-info {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 18px;
            margin-bottom: 24px;
        }
        .info-grid {
            width: 100%;
            border-collapse: collapse;
        }
        .info-grid td {
            padding: 6px 0;
            vertical-align: top;
            font-size: 14px;
        }
        .info-label {
            color: #64748b;
            font-weight: 600;
            width: 38%;
        }
        .info-value {
            color: #0f172a;
            font-weight: 700;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .items-table th {
            background: #f1f5f9;
            color: #475569;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 12px;
            text-align: left;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
            color: #334155;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals-card {
            background: #faf5ff;
            border: 1px solid #e9d5ff;
            border-radius: 10px;
            padding: 16px 20px;
            margin-top: 15px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            font-size: 14px;
            color: #475569;
        }
        .grand-total-row {
            display: flex;
            justify-content: space-between;
            padding-top: 10px;
            margin-top: 8px;
            border-top: 2px dashed #d8b4fe;
            font-size: 17px;
            font-weight: 800;
            color: #7e22ce;
        }
        .btn-action {
            display: inline-block;
            background: linear-gradient(135deg, #ff6a00 0%, #ee0979 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 15px;
            box-shadow: 0 4px 14px rgba(238, 9, 121, 0.35);
            margin: 25px 0 10px;
            text-align: center;
        }
        .remarks-box {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 12px 16px;
            margin: 18px 0;
            border-radius: 0 8px 8px 0;
            font-size: 13px;
            color: #92400e;
        }
        .footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 22px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="badge-pill">🛎️ Table QR Ordering</div>
            <h1>New Customer Order Received!</h1>
            <p>{{ $restaurant->name ?? 'Restaurant POS' }}</p>
        </div>

        <!-- Content -->
        <div class="content">
            <p style="font-size: 15px; margin-top: 0; color: #334155;">
                Hello <strong>{{ $restaurant->name ?? 'Restaurant Team' }}</strong>,
            </p>
            <p style="font-size: 14px; color: #475569; margin-bottom: 20px;">
                A customer has just placed a new order using the Table QR Code. Please review the order details below and accept it in your POS dashboard.
            </p>

            <!-- Order Details Card -->
            <div class="card-info">
                <table class="info-grid">
                    <tr>
                        <td class="info-label">Order Number:</td>
                        <td class="info-value" style="color: #ff6a00; font-family: monospace; font-size: 15px;">
                            {{ $order->order_id ?? ('#' . $order->id) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="info-label">Table:</td>
                        <td class="info-value">
                            {{ $table ? ($table->name ?? 'Table ' . $table->id) : ($order->table_details->name ?? 'Dine-In Table') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="info-label">Customer Name:</td>
                        <td class="info-value">{{ $order->customer_name }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Phone Number:</td>
                        <td class="info-value">{{ $order->customer_phone ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Order Time:</td>
                        <td class="info-value">{{ $order->created_at ? $order->created_at->format('d M Y, h:i A') : now()->format('d M Y, h:i A') }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Order Status:</td>
                        <td class="info-value">
                            <span style="color: #f59e0b; background: #fef3c7; padding: 2px 8px; border-radius: 12px; font-size: 12px;">
                                {{ strtoupper($order->order_status ?? 'PENDING') }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>

            @if(!empty($order->remarks))
            <div class="remarks-box">
                <strong>Customer Note/Remarks:</strong> {{ $order->remarks }}
            </div>
            @endif

            <!-- Items Table -->
            <h3 style="font-size: 16px; margin: 20px 0 10px; color: #0f172a;">Ordered Items</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th class="text-center">Qty</th>
                        <th class="text-right">Price</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->menuItem->name ?? 'Dish' }}</strong>
                            @if(!empty($item->item_discount_percentage) && $item->item_discount_percentage > 0)
                                <br><small style="color: #10b981;">{{ $item->item_discount_percentage }}% OFF</small>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">₹{{ number_format($item->price, 2) }}</td>
                        <td class="text-right" style="font-weight: 600;">₹{{ number_format($item->total_amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Financial Summary -->
            <table style="width: 100%; border-collapse: collapse; margin-top: 15px; background: #faf5ff; border: 1px solid #e9d5ff; border-radius: 10px; padding: 15px;">
                <tr>
                    <td style="padding: 8px 15px; color: #64748b; font-size: 14px;">Subtotal</td>
                    <td style="padding: 8px 15px; text-align: right; font-weight: 600; font-size: 14px; color: #334155;">
                        ₹{{ number_format($order->total_amount, 2) }}
                    </td>
                </tr>
                @if($order->discount > 0)
                <tr>
                    <td style="padding: 6px 15px; color: #10b981; font-size: 14px;">Discount</td>
                    <td style="padding: 6px 15px; text-align: right; font-weight: 600; font-size: 14px; color: #10b981;">
                        - ₹{{ number_format($order->discount, 2) }}
                    </td>
                </tr>
                @endif
                @if($order->gst_amount > 0)
                <tr>
                    <td style="padding: 6px 15px; color: #64748b; font-size: 14px;">GST / Taxes</td>
                    <td style="padding: 6px 15px; text-align: right; font-weight: 600; font-size: 14px; color: #334155;">
                        ₹{{ number_format($order->gst_amount, 2) }}
                    </td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 12px 15px 8px; font-size: 16px; font-weight: 800; color: #7e22ce; border-top: 1px dashed #d8b4fe;">Grand Total</td>
                    <td style="padding: 12px 15px 8px; text-align: right; font-size: 18px; font-weight: 800; color: #7e22ce; border-top: 1px dashed #d8b4fe;">
                        ₹{{ number_format($order->grand_total, 2) }}
                    </td>
                </tr>
            </table>

            <!-- Call to Action -->
            <div style="text-align: center; margin: 30px 0 10px;">
                <a href="{{ route('temp.orders.view', $order->id) }}" class="btn-action" target="_blank">
                    👉 View & Approve Order in POS
                </a>
            </div>

            <p style="font-size: 12px; color: #94a3b8; text-align: center; margin-top: 15px;">
                You can also view all pending QR orders from your POS navigation menu under <strong>Pending Orders</strong>.
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 0 0 6px;">This is an automated notification from <strong>{{ config('app.name', 'Bill&Bite POS') }}</strong>.</p>
            <p style="margin: 0;">&copy; {{ date('Y') }} {{ $restaurant->name ?? config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
