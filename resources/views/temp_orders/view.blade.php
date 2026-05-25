<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin - View Order #{{ $order->id }}</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --gold: #C9A84C;
            --gold-light: #E8C97A;
            --gold-dim: rgba(201,168,76,0.15);
            --primary-dark: #1e293b;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
        }

        .order-header {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            border-radius: 20px;
            padding: 24px 30px;
            margin-bottom: 28px;
            position: relative;
            overflow: hidden;
        }
        
        .order-header::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(201,168,76,0.15), transparent);
            border-radius: 50%;
        }

        .order-header h3 {
            color: white;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .order-header .order-badge {
            background: rgba(255,255,255,0.15);
            padding: 6px 15px;
            border-radius: 30px;
            font-size: 0.85rem;
            color: #a5f3fc;
            display: inline-block;
        }

        .info-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border: 1px solid #eef2f8;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px;
            background: #f8fafc;
            border-radius: 14px;
            transition: all 0.3s;
        }

        .info-item:hover {
            background: #f1f5f9;
            transform: translateX(3px);
        }

        .info-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .info-content {
            flex: 1;
        }

        .info-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 4px;
        }

        .info-value {
            font-weight: 700;
            color: #1e293b;
            font-size: 1rem;
        }

        .badge-gst {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .badge-non-gst {
            background: #64748b;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .summary-card {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border-radius: 16px;
            padding: 20px;
            margin-top: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .summary-label {
            color: #64748b;
            font-size: 0.9rem;
        }

        .summary-value {
            font-weight: 600;
            color: #1e293b;
        }

        .summary-total {
            background: linear-gradient(135deg, #1e293b, #334155);
            border-radius: 12px;
            padding: 15px;
            margin-top: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .summary-total .label {
            color: white;
            font-weight: 600;
        }

        .summary-total .value {
            color: var(--gold-light);
            font-weight: 800;
            font-size: 1.2rem;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table thead th {
            background: linear-gradient(135deg, #1e293b, #334155);
            color: white;
            font-weight: 500;
            padding: 14px 16px;
            font-size: 0.85rem;
        }

        .items-table tbody td {
            padding: 12px 16px;
            border-bottom: 1px solid #eef2f8;
            vertical-align: middle;
        }

        .items-table tbody tr:hover {
            background: #f8fafc;
        }

        .btn-approve {
            background: linear-gradient(135deg, var(--success), #059669);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 14px 40px;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-approve:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16,185,129,0.3);
            color: white;
        }

        .btn-approve:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
    </style>
</head>

<body data-pc-theme="light">

@include('includes.sidebar')

<div class="pc-container">
<div class="pc-content">

    <!-- Order Header -->
    <div class="order-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h3><i class="fas fa-receipt me-2"></i>Order Details</h3>
                <div class="order-badge mt-2">
                    <i class="fas fa-hashtag me-1"></i> Order #{{ $order->order_id ?? $order->id }}
                </div>
            </div>
            <div>
                @php $isGstBill = ($order->is_gst_bill ?? 'NO') == 'YES'; @endphp
                @if($isGstBill)
                    <span class="badge-gst"><i class="fas fa-file-invoice-dollar"></i> GST Bill</span>
                @else
                    <span class="badge-non-gst"><i class="fas fa-receipt"></i> Non-GST Bill</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Customer & Order Information -->
    <div class="info-card">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-icon"><i class="fas fa-user"></i></div>
                <div class="info-content">
                    <div class="info-label">Customer Name</div>
                    <div class="info-value">{{ $order->customer_name ?? 'Guest' }}</div>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon"><i class="fas fa-phone-alt"></i></div>
                <div class="info-content">
                    <div class="info-label">Phone Number</div>
                    <div class="info-value">{{ $order->customer_phone ?? '-' }}</div>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon"><i class="fas fa-table"></i></div>
                <div class="info-content">
                    <div class="info-label">Table Number</div>
                    <div class="info-value">{{ $order->table_details->name ?? 'Takeaway' }}</div>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon"><i class="fas fa-calendar-alt"></i></div>
                <div class="info-content">
                    <div class="info-label">Order Date</div>
                    <div class="info-value">{{ $order->created_at->format('d M Y, h:i A') }}</div>
                </div>
            </div>
            @if($order->order_type)
            <div class="info-item">
                <div class="info-icon"><i class="fas fa-utensils"></i></div>
                <div class="info-content">
                    <div class="info-label">Order Type</div>
                    <div class="info-value">
                        @if($order->order_type == 'DINE_IN')
                            <span class="btn btn-info">Dine In</span>
                        @else
                            <span class="btn btn-success">Takeaway</span>
                        @endif
                    </div>
                </div>
            </div>
            @endif
            @if($order->remarks)
            <div class="info-item">
                <div class="info-icon"><i class="fas fa-sticky-note"></i></div>
                <div class="info-content">
                    <div class="info-label">Remarks</div>
                    <div class="info-value">{{ $order->remarks }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Order Items Card -->
    <div class="card mt-3">
        <div class="card-header">
            <h5><i class="fas fa-list-ul me-2"></i>Order Items <span class="badge bg-secondary ms-2">{{ count($order->items) }} Items</span></h5>
        </div>
        @include('includes.message')
        <div class="card-body table-responsive">
            <table class="items-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Food Item</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Discount</th>
                        <th>Taxable</th>
                        <th>GST</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $key => $i)
                    @php
                        $itemDiscount = $i->item_discount_percentage ?? 0;
                        $discountedPrice = $i->discounted_price ?? ($i->price - ($i->price * $itemDiscount / 100));
                        $taxableAmount = $i->taxable_amount ?? ($discountedPrice * $i->quantity);
                        $gstAmount = $i->gst_amount ?? (($taxableAmount * ($i->gst_rate ?? 0)) / 100);
                        $itemTotal = $taxableAmount + $gstAmount;
                    @endphp
                    <tr>
                        <td>{{ $key+1 }}</div>
                        <td>
                            <strong>{{ $i->menuItem->name ?? 'N/A' }}</strong>
                            @if($itemDiscount > 0)
                                <br><small class="text-success">{{ $itemDiscount }}% OFF</small>
                            @endif
                        </div>
                        <td>{{ $i->quantity }}</div>
                        <td>
                            @if($itemDiscount > 0)
                                <del class="text-muted">₹{{ number_format($i->price, 2) }}</del><br>
                                <span class="text-success">₹{{ number_format($discountedPrice, 2) }}</span>
                            @else
                                ₹{{ number_format($i->price, 2) }}
                            @endif
                        </div>
                        <td>
                            @if($itemDiscount > 0)
                                <span class="text-danger">- ₹{{ number_format(($i->price * $i->quantity) - $taxableAmount, 2) }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                        <td>₹{{ number_format($taxableAmount, 2) }}</div>
                        <td>{{ $i->gst_rate ?? 0 }}%</div>
                        <td><strong class="text-primary">₹{{ number_format($itemTotal, 2) }}</strong></div>
                        <td>
                            <a href="{{ route('temp.orders.view.delete.item', $i->id) }}"
                               onclick="return confirm('Delete this item?')"
                               class="btn btn-danger btn-sm"
                               title="Remove Item">
                               <i class="fa fa-trash"></i>
                            </a>
                        </div>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Billing Summary -->
    <div class="summary-card">
        <div class="summary-row">
            <span class="summary-label">Original Subtotal</span>
            <span class="summary-value">₹{{ number_format($order->total_amount ?? 0, 2) }}</span>
        </div>
        
        @php
            $totalItemDiscount = 0;
            foreach($order->items as $item) {
                $itemDiscount = $item->item_discount_percentage ?? 0;
                $discountedPrice = $item->discounted_price ?? ($item->price - ($item->price * $itemDiscount / 100));
                $taxableAmount = $item->taxable_amount ?? ($discountedPrice * $item->quantity);
                $totalItemDiscount += ($item->price * $item->quantity) - $taxableAmount;
            }
        @endphp
        
        @if($totalItemDiscount > 0)
        <div class="summary-row">
            <span class="summary-label">Item Discount</span>
            <span class="summary-value text-success">- ₹{{ number_format($totalItemDiscount, 2) }}</span>
        </div>
        @endif
        
        <div class="summary-row">
            <span class="summary-label">Taxable Amount</span>
            <span class="summary-value">₹{{ number_format($order->taxable_amount ?? ($order->total_amount - $totalItemDiscount), 2) }}</span>
        </div>
        
        @if($isGstBill)
        <div class="summary-row">
            <span class="summary-label">GST Total ({{ $order->restaurant_gst_percentage ?? 0 }}%)</span>
            <span class="summary-value">₹{{ number_format($order->gst_amount ?? 0, 2) }}</span>
        </div>
        @endif
        
        @if($order->discount_percentage > 0)
        <div class="summary-row">
            <span class="summary-label">Order Discount ({{ $order->discount_percentage }}%)</span>
            <span class="summary-value text-success">- ₹{{ number_format($order->discount ?? 0, 2) }}</span>
        </div>
        @endif
        
        @php
            $grandTotal = $order->grand_total ?? 0;
            $finalAmount = round($grandTotal);
            $roundOff = $finalAmount - $grandTotal;
        @endphp

        @if(abs($roundOff) > 0)
        <div class="summary-row">
            <span class="summary-label">Round Off</span>
            <span class="summary-value">₹{{ number_format($roundOff, 2) }}</span>
        </div>
        @endif
        
        <div class="summary-total">
            <span class="label"><i class="fas fa-rupee-sign me-1"></i> Grand Total</span>
            <span class="value">₹{{ number_format($grandTotal, 2) }}</span>
        </div>
        
        <div class="summary-total mt-2" style="background: linear-gradient(135deg, var(--success), #059669);">
            <span class="label"><i class="fas fa-check-circle me-1"></i> Final Bill Amount</span>
            <span class="value">₹{{ number_format($finalAmount, 2) }}</span>
        </div>
    </div>

    <!-- Approve Order Section -->
    <div class="text-center mt-4">
        @php
            $tableAvailable = !$order->table_id || ($order->table_details && $order->table_details->table_status == 'AVAILABLE');
        @endphp
        
        @if($tableAvailable)
            <div class="action-buttons">
                <a href="{{ route('admin.temporder.approve', $order->id) }}"
                   class="btn-approve"
                   onclick="return confirm('Approve this order? It will be moved to main orders with a new order number.')">
                    <i class="fas fa-check-circle"></i> Approve Order
                </a>
                <a href="{{ route('temp.orders') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-arrow-left"></i> Back to Pending Orders
                </a>
            </div>
        @else
            <div class="alert alert-warning d-inline-flex align-items-center gap-2 px-4 py-3 rounded-lg">
                <i class="fas fa-exclamation-triangle fa-lg"></i>
                <strong>Table is not available!</strong> The table is currently occupied. Please check table status.
            </div>
            <div class="mt-3">
                <a href="{{ route('temp.orders') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-arrow-left"></i> Back to Pending Orders
                </a>
            </div>
        @endif
    </div>

</div>
</div>

@include('includes.script')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.info-card, .card');
        cards.forEach((card, index) => {
            card.style.animation = `fadeInUp 0.5s ease-out ${index * 0.1}s both`;
        });
    });
    
    style = document.createElement('style');
    style.textContent = `
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    `;
    document.head.appendChild(style);
</script>

</body>
</html>