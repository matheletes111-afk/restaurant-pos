<!DOCTYPE html>
<html lang="en">
<head>
    <title>Order Details</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #27ae60;
            --danger: #e74c3c;
            --warning: #f39c12;
            --info: #17a2b8;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --gray: #95a5a6;
        }

        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .page-header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }

        .page-header h3 {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 10px;
        }

        /* Card Styling */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-bottom: 25px;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary), #34495e);
            color: white;
            border-bottom: none;
            padding: 15px 20px;
            border-radius: 12px 12px 0 0 !important;
        }

        .card-header h5 {
            margin: 0;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header h5 i {
            font-size: 1.1rem;
        }

        /* Order Info Badges */
        .info-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .status-paid { background: #d1fae5; color: #065f46; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-miscorder { background: #fce7f3; color: #9d174d; }

        /* Table Styling */
        .info-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .info-table th {
            background: linear-gradient(135deg, var(--secondary), #2980b9);
            color: white;
            font-weight: 500;
            padding: 12px 15px;
            text-align: left;
            width: 30%;
        }

        .info-table td {
            background: white;
            padding: 12px 15px;
            border-bottom: 1px solid #e0e6ed;
        }

        .info-table tr:last-child td {
            border-bottom: none;
        }

        .info-table tr:hover td {
            background: #f8f9fa;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .items-table thead {
            background: linear-gradient(135deg, var(--primary), #34495e);
        }

        .items-table th {
            color: white;
            font-weight: 500;
            padding: 12px 15px;
            text-align: left;
        }

        .items-table tbody tr {
            transition: background 0.3s;
        }

        .items-table tbody tr:hover {
            background: #f8f9fa;
        }

        .items-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e0e6ed;
        }

        .items-table tr:last-child td {
            border-bottom: none;
        }

        /* Summary Box */
        .summary-box {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-top: 10px;
            border-top: 4px solid var(--secondary);
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e0e6ed;
        }

        .summary-item:last-child {
            border-bottom: none;
        }

        .summary-label {
            color: var(--gray);
            font-size: 0.95rem;
        }

        .summary-value {
            color: var(--primary);
            font-weight: 600;
            font-size: 1.05rem;
        }

        .summary-total {
            background: linear-gradient(135deg, var(--primary), #34495e);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-label {
            font-size: 1.1rem;
        }

        .total-value {
            font-size: 1.4rem;
            font-weight: 700;
        }

        .payment-section {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 8px;
            padding: 20px;
            margin-top: 15px;
            border-left: 4px solid var(--success);
        }

        .payment-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
        }

        .payment-label {
            color: var(--primary);
            font-weight: 500;
        }

        .payment-value {
            color: var(--success);
            font-weight: 600;
            font-size: 1.1rem;
        }

        /* Back Button */
        .back-btn {
            background: linear-gradient(135deg, var(--secondary), #2980b9);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 8px 20px;
            font-weight: 500;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(52, 152, 219, 0.2);
            color: white;
            text-decoration: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .card-header h5 {
                font-size: 1rem;
            }
            
            .info-table, .items-table {
                display: block;
                overflow-x: auto;
            }
            
            .summary-box {
                padding: 20px;
            }
        }

        /* Customer Info Card */
        .customer-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid var(--info);
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .customer-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .customer-icon {
            width: 40px;
            height: 40px;
            background: rgba(23, 162, 184, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--info);
        }
    </style>
</head>

<body data-pc-theme="light">
@include('includes.sidebar')

<div class="pc-container">
    <div class="pc-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3>Order Details</h3>
                    <p class="text-muted mb-0">Order #{{ $order->order_id ?? $order->id }}</p>
                </div>
               
            </div>
        </div>

        <div class="row">
            <!-- Left Column: Order Info & Customer -->
            <div class="col-lg-4">
                <!-- Order Information -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-info-circle me-2"></i>Order Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="info-table">
                            <tbody>
                                <tr>
                                    <th>Order ID</th>
                                    <td>#{{ $order->order_id }}</td>
                                </tr>
                                <tr>
                                    <th>Order Date</th>
                                    <td>{{ $order->created_at->format('d M Y, h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Table</th>
                                    <td>
                                        @if($order->table)
                                            <span class="info-badge" style="background: var(--secondary); color: white;">
                                                <i class="fas fa-table"></i>
                                                {{ $order->table->name }}
                                            </span>
                                        @else
                                            <span class="info-badge" style="background: var(--success); color: white;">
                                                <i class="fas fa-takeout-box"></i>
                                                Takeaway
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if($order->payment_status == 'PAID')
                                            <span class="info-badge status-paid">
                                                <i class="fas fa-check-circle"></i> PAID
                                            </span>
                                        @elseif($order->payment_status == 'PENDING')
                                            <span class="info-badge status-pending">
                                                <i class="fas fa-clock"></i> PENDING
                                            </span>
                                        @elseif($order->payment_status == 'MISCORDER')
                                            <span class="info-badge status-miscorder">
                                                <i class="fas fa-exclamation-circle"></i> MISCORDER
                                            </span>
                                        @else
                                            <span class="info-badge" style="background: #e0e6ed; color: #4a5568;">
                                                {{ $order->payment_status }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Payment Method</th>
                                    <td>{{ $order->payment_method ?? 'Not specified' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="customer-card">
                    <h5 class="mb-3"><i class="fas fa-user me-2"></i>Customer Details</h5>
                    <div class="customer-item">
                        <div class="customer-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $order->customer_name }}</h6>
                            <small class="text-muted">Customer Name</small>
                        </div>
                    </div>
                    @if($order->customer_phone)
                    <div class="customer-item">
                        <div class="customer-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $order->customer_phone }}</h6>
                            <small class="text-muted">Phone Number</small>
                        </div>
                    </div>
                    @endif
                    <div class="customer-item">
                        <div class="customer-icon">
                            <i class="fas fa-user-tag"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $order->user ? $order->user->name : 'Walk-in Customer' }}</h6>
                            <small class="text-muted">Order By</small>
                        </div>
                    </div>
                    @if($order->remarks)
                    <div class="customer-item">
                        <div class="customer-icon">
                            <i class="fas fa-sticky-note"></i>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $order->remarks }}</h6>
                            <small class="text-muted">Remarks</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Items & Summary -->
            <div class="col-lg-8">
                <!-- Order Items -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-list-alt me-2"></i>Order Items ({{ count($order->items) }})</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="items-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Item Name</th>
                                        <th>Qty</th>
                                        <th>Rate</th>
                                        <th>GST</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php 
                                        $subtotal = 0;
                                        $gstTotal = 0;
                                    @endphp
                                    @foreach ($order->items as $index => $item)
                                    @php
                                        $itemSubtotal = $item->price * $item->quantity;
                                        $itemGst = ($itemSubtotal * $item->gst_rate) / 100;
                                        $itemTotal = $itemSubtotal + $itemGst;
                                        $subtotal += $itemSubtotal;
                                        $gstTotal += $itemGst;
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $item->subcategory->name ?? 'N/A' }}</strong>
                                        </td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>₹{{ number_format($item->price, 2) }}</td>
                                        <td>{{ $item->gst_rate }}%</td>
                                        <td><strong>₹{{ number_format($itemTotal, 2) }}</strong></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Billing Summary -->
                <div class="summary-box">
                    <h5 class="mb-3"><i class="fas fa-receipt me-2"></i>Billing Summary</h5>
                    
                    <div class="summary-item">
                        <span class="summary-label">Subtotal:</span>
                        <span class="summary-value">₹{{ number_format($subtotal, 2) }}</span>
                    </div>
                    
                    <div class="summary-item">
                        <span class="summary-label">GST Total:</span>
                        <span class="summary-value">₹{{ number_format($gstTotal, 2) }}</span>
                    </div>
                    
                    <div class="summary-item">
                        <span class="summary-label">Discount:</span>
                        <span class="summary-value text-success">- ₹{{ number_format($order->discount, 2) }}</span>
                    </div>
                    
                    @php
                        $grandTotal = $order->grand_total;
                        $finalAmount = round($grandTotal);
                        $roundOff = $finalAmount - $grandTotal;
                    @endphp

                    <div class="summary-total">
                        <span class="total-label">Grand Total:</span>
                        <span class="total-value">₹{{ number_format($grandTotal, 2) }}</span>
                    </div>

                    <div class="summary-total">
                        <span class="total-label">Round Off:</span>
                        <span class="total-value">₹{{ number_format($roundOff, 2) }}</span>
                    </div>

                    <div class="summary-total final">
                        <span class="total-label"><strong>Final Bill Amount:</strong></span>
                        <span class="total-value"><strong>₹{{ number_format($finalAmount, 2) }}</strong></span>
                    </div>


                    <!-- Payment Details -->
                    <div class="payment-section">
                        <h6 class="mb-3"><i class="fas fa-credit-card me-2"></i>Payment Details</h6>
                        
                        @if($order->payment_status == 'PAID')
                        <div class="payment-item">
                            <span class="payment-label">Amount Paid:</span>
                            <span class="payment-value">₹{{ number_format($order->grand_total, 2) }}</span>
                        </div>
                        <div class="payment-item">
                            <span class="payment-label">Balance:</span>
                            <span class="payment-value">₹0.00</span>
                        </div>
                        @elseif($order->payment_status == 'MISCORDER')
                        <div class="payment-item">
                            <span class="payment-label">Amount Paid:</span>
                            <span class="payment-value">₹{{ number_format($order->amount_paid ?? 0, 2) }}</span>
                        </div>
                        <div class="payment-item">
                            <span class="payment-label">Balance:</span>
                            <span class="payment-value">
                                ₹{{ number_format($order->grand_total - ($order->amount_paid ?? 0), 2) }}
                            </span>
                        </div>
                        @else
                        <div class="payment-item">
                            <span class="payment-label">Amount Paid:</span>
                            <span class="payment-value">₹{{ number_format($order->grand_total, 2) }}</span>
                        </div>
                        @endif
                        
                        <div class="payment-item">
                            <span class="payment-label">Payment Method:</span>
                            <span>{{ $order->payment_method ?? 'Not specified' }}</span>
                        </div>
                        
                        @if($order->payment_status == 'PAID')
                        <div class="alert alert-success mt-3 mb-0">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Payment Completed:</strong> This order has been fully paid.
                        </div>
                        @elseif($order->payment_status == 'MISCORDER')
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Miscorder Status:</strong> Customer ate but payment not completed.
                        </div>
                        @else
                        <div class="alert alert-info mt-3 mb-0">
                            <i class="fas fa-clock me-2"></i>
                            <strong>Payment Pending:</strong> This order requires payment.
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Print & Actions -->
                <div class="d-flex gap-2 mt-3">
                    <a href="{{route('order.report')}}" class="btn btn-primary text-white">
                        <i class="fas fa-print me-2"></i>Back
                    </a>
                    <a href="{{ route('order.invoice', $order->id) }}" class="btn btn-success" target="_blank">
                        <i class="fas fa-file-pdf me-2"></i>Download PDF
                    </a>
                    @if($order->payment_status == 'PENDING')
                    <a href="{{ route('order.edit', $order->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Edit Order
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('includes.script')
<script>
    // Print styling
    @media print {
        body * {
            visibility: hidden;
        }
        .pc-content, .pc-content * {
            visibility: visible;
        }
        .pc-content {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .back-btn, .btn {
            display: none !important;
        }
        .card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
    }
</script>
</body>
</html>