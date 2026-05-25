<!DOCTYPE html>
<html lang="en">
<head>
    <title>Order Details | #{{ $order->order_id ?? $order->id }}</title>
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
            --purple: #8b5cf6;
            --pink: #ec489a;
            --orange: #f97316;
            --light: #f8f9fa;
            --dark: #1e293b;
            --gray: #64748b;
        }

        body {
            background: linear-gradient(135deg, #f5f7fb 0%, #eef2f8 100%);
            font-family: 'Inter', 'Segoe UI', sans-serif;
        }

        /* Page Header */
        .page-header-custom {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            border-radius: 20px;
            padding: 24px 30px;
            margin-bottom: 28px;
            position: relative;
            overflow: hidden;
        }
        
        .page-header-custom::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(52,152,219,0.15), transparent);
            border-radius: 50%;
        }

        .page-header-custom h3 {
            color: white;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .page-header-custom .order-badge {
            background: rgba(255,255,255,0.15);
            padding: 6px 15px;
            border-radius: 30px;
            font-size: 0.85rem;
            color: #a5f3fc;
        }

        /* Cards */
        .card-modern {
            background: white;
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            margin-bottom: 25px;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }

        .card-header-modern {
            background: white;
            border-bottom: 2px solid #eef2f8;
            padding: 18px 24px;
        }

        .card-header-modern h5 {
            margin: 0;
            font-weight: 700;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header-modern h5 i {
            color: var(--secondary);
            font-size: 1.2rem;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            padding: 20px;
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
            background: linear-gradient(135deg, var(--secondary), #2980b9);
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
            color: var(--gray);
            margin-bottom: 4px;
        }

        .info-value {
            font-weight: 700;
            color: var(--dark);
            font-size: 1rem;
        }

        /* Status Badges */
        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-paid { background: linear-gradient(135deg, #10b981, #059669); color: white; }
        .badge-pending { background: linear-gradient(135deg, #f59e0b, #d97706); color: white; }
        .badge-misc { background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; }
        .badge-dinein { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; }
        .badge-takeaway { background: linear-gradient(135deg, #10b981, #059669); color: white; }
        .badge-gst { background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; }
        .badge-non-gst { background: #64748b; color: white; }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table thead th {
            background: #f1f5f9;
            padding: 14px 16px;
            font-weight: 600;
            font-size: 0.8rem;
            color: var(--dark);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .items-table tbody td {
            padding: 14px 16px;
            border-bottom: 1px solid #eef2f8;
            vertical-align: middle;
        }

        .items-table tbody tr:hover {
            background: #f8fafc;
        }

        .item-name {
            font-weight: 600;
            color: var(--dark);
        }

        .item-category {
            font-size: 0.7rem;
            color: var(--gray);
        }

        /* GST Info Box */
        .gst-info-box {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            padding: 12px 15px;
            margin-bottom: 15px;
        }
        .non-gst-info-box {
            background: #fef3c7;
            border: 1px solid #fde68a;
            border-radius: 12px;
            padding: 12px 15px;
            margin-bottom: 15px;
        }

        /* Summary Section */
        .summary-container {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border-radius: 20px;
            padding: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .summary-label {
            color: var(--gray);
            font-size: 0.9rem;
        }

        .summary-value {
            font-weight: 600;
            color: var(--dark);
        }

        .summary-total {
            background: linear-gradient(135deg, var(--primary), #34495e);
            border-radius: 16px;
            padding: 18px;
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
            color: white;
            font-weight: 800;
            font-size: 1.3rem;
        }

        .final-total {
            background: linear-gradient(135deg, var(--success), #059669);
        }

        /* Payment Card */
        .payment-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            margin-top: 20px;
            border-left: 4px solid var(--success);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 10px 24px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-action:hover {
            transform: translateY(-2px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .page-header-custom {
                padding: 20px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn-action {
                justify-content: center;
            }
        }

        /* Animations */
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

        .card-modern {
            animation: fadeInUp 0.5s ease-out;
        }

        /* Discount Badge */
        .discount-badge {
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
    </style>
</head>

<body>
@include('includes.sidebar')

<div class="pc-container">
    <div class="pc-content">
        <!-- Page Header -->
        <div class="page-header-custom">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h3><i class="fas fa-receipt me-2"></i>Order Details</h3>
                    <div class="order-badge mt-2">
                        <i class="fas fa-hashtag me-1"></i> {{ $order->order_id ?? $order->id }}
                    </div>
                </div>
                <div>
                    <span class="badge-status {{ $order->payment_status == 'PAID' ? 'badge-paid' : ($order->payment_status == 'PENDING' ? 'badge-pending' : 'badge-misc') }}">
                        <i class="fas {{ $order->payment_status == 'PAID' ? 'fa-check-circle' : ($order->payment_status == 'PENDING' ? 'fa-clock' : 'fa-exclamation-triangle') }}"></i>
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column -->
            <div class="col-lg-4">
                <!-- Order Info Card -->
                <div class="card-modern">
                    <div class="card-header-modern">
                        <h5><i class="fas fa-info-circle"></i> Order Information</h5>
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-calendar-alt"></i></div>
                            <div class="info-content">
                                <div class="info-label">Order Date</div>
                                <div class="info-value">{{ $order->created_at->format('d M Y, h:i A') }}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-utensils"></i></div>
                            <div class="info-content">
                                <div class="info-label">Order Type</div>
                                <div class="info-value">
                                    <span class="badge-status {{ $order->order_type == 'DINE_IN' ? 'badge-dinein' : 'badge-takeaway' }}" style="padding: 3px 10px; font-size: 0.7rem;">
                                        <i class="fas {{ $order->order_type == 'DINE_IN' ? 'fa-table' : 'fa-box' }}"></i>
                                        {{ $order->order_type == 'DINE_IN' ? 'Dine In' : 'Takeaway' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @if($order->table)
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-table"></i></div>
                            <div class="info-content">
                                <div class="info-label">Table</div>
                                <div class="info-value">{{ $order->table->name }}</div>
                            </div>
                        </div>
                        @endif
                        <div class="info-item">
                            <div class="info-icon"><i class="fas fa-credit-card"></i></div>
                            <div class="info-content">
                                <div class="info-label">Payment Method</div>
                                <div class="info-value">{{ $order->payment_method ?? 'Not specified' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Info Card -->
                <div class="card-modern">
                    <div class="card-header-modern">
                        <h5><i class="fas fa-user-circle"></i> Customer Details</h5>
                    </div>
                    <div class="info-grid" style="grid-template-columns: 1fr;">
                        <div class="info-item">
                            <div class="info-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Customer Name</div>
                                <div class="info-value">{{ $order->customer_name ?? 'Walk-in Customer' }}</div>
                            </div>
                        </div>
                        @if($order->customer_phone)
                        <div class="info-item">
                            <div class="info-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Phone Number</div>
                                <div class="info-value">{{ $order->customer_phone }}</div>
                            </div>
                        </div>
                        @endif
                        <div class="info-item">
                            <div class="info-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Order Taken By</div>
                                <div class="info-value">{{ $order->user ? $order->user->name : 'System' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Remarks Card -->
                @if($order->remarks)
                <div class="card-modern">
                    <div class="card-header-modern">
                        <h5><i class="fas fa-sticky-note"></i> Remarks</h5>
                    </div>
                    <div class="p-4">
                        <p class="mb-0 text-muted"><i class="fas fa-quote-left me-2"></i> {{ $order->remarks }}</p>
                    </div>
                </div>
                @endif

                <!-- GST Info Box -->
                @php
                    $isGstBill = ($order->is_gst_bill ?? 'NO') == 'YES';
                    $gstPercentage = $order->restaurant_gst_percentage ?? 0;
                @endphp
                @if($isGstBill)
                <div class="gst-info-box">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-file-invoice-dollar text-success fa-lg"></i>
                            <strong class="ml-2">GST Bill</strong>
                        </div>
                        <div>
                            @if($order->restaurant_gstin)
                            <span class="badge badge-success">GSTIN: {{ $order->restaurant_gstin }}</span>
                            @endif
                            <span class="badge badge-info ml-1">GST: {{ $gstPercentage }}%</span>
                        </div>
                    </div>
                </div>
                @else
                <div class="non-gst-info-box">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-receipt text-muted fa-lg"></i>
                            <strong class="ml-2">Non-GST Bill</strong>
                        </div>
                        <div>
                            <span class="badge badge-secondary">No GST Applicable</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column -->
            <div class="col-lg-8">
                <!-- Order Items Card -->
                <div class="card-modern">
                    <div class="card-header-modern">
                        <h5><i class="fas fa-list-ul"></i> Order Items <span class="badge bg-secondary ms-2">{{ count($order->items) }} Items</span></h5>
                    </div>
                    <div class="table-responsive">
                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Discount</th>
                                    @if($isGstBill)
                                    <th>GST</th>
                                    @endif
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php 
                                    $subtotal = 0;
                                    $gstTotal = 0;
                                    $discountTotal = 0;
                                @endphp
                                @foreach ($order->items as $index => $item)
                                @php
                                    $itemDiscount = $item->item_discount_percentage ?? 0;
                                    $originalPrice = $item->price;
                                    $discountedPrice = $item->discounted_price ?? ($originalPrice - ($originalPrice * $itemDiscount / 100));
                                    $quantity = $item->quantity;
                                    $taxableAmount = $item->taxable_amount ?? ($discountedPrice * $quantity);
                                    
                                    // Use stored GST amount or calculate
                                    $itemGst = $item->gst_amount ?? 0;
                                    $gstRate = $item->gst_rate ?? 0;
                                    $itemTotal = $taxableAmount + $itemGst;
                                    
                                    $subtotal += $originalPrice * $quantity;
                                    $gstTotal += $itemGst;
                                    $discountTotal += ($originalPrice * $quantity) - $taxableAmount;
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</div>
                                    <td>
                                        <div class="item-name">{{ $item->subcategory->name ?? 'N/A' }}</div>
                                        <div class="item-category">{{ $item->subcategory->category->name ?? '' }}</div>
                                    </div>
                                    <td>{{ $quantity }}</div>
                                    <td>
                                        @if($itemDiscount > 0)
                                            <del class="text-muted">₹{{ number_format($originalPrice, 2) }}</del><br>
                                            <span class="text-success">₹{{ number_format($discountedPrice, 2) }}</span>
                                        @else
                                            ₹{{ number_format($originalPrice, 2) }}
                                        @endif
                                    </div>
                                    <td>
                                        @if($itemDiscount > 0)
                                            <span class="discount-badge">{{ $itemDiscount }}% OFF</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                    @if($isGstBill)
                                    <td class="text-center">
                                        {{ $gstRate }}%
                                        <br><small class="text-muted">₹{{ number_format($itemGst, 2) }}</small>
                                    </div>
                                    @endif
                                    <td class="text-end">
                                        <strong class="text-primary">₹{{ number_format($itemTotal, 2) }}</strong>
                                    </div>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Billing Summary -->
                <div class="summary-container">
                    <div class="summary-row">
                        <span class="summary-label">Original Subtotal</span>
                        <span class="summary-value">₹{{ number_format($subtotal, 2) }}</span>
                    </div>
                    
                    @if($discountTotal > 0)
                    <div class="summary-row">
                        <span class="summary-label">Item Discount</span>
                        <span class="summary-value text-success">- ₹{{ number_format($discountTotal, 2) }}</span>
                    </div>
                    @endif
                    
                    <div class="summary-row">
                        <span class="summary-label">Taxable Amount</span>
                        <span class="summary-value">₹{{ number_format($subtotal - $discountTotal, 2) }}</span>
                    </div>
                    
                    @if($isGstBill)
                    <div class="summary-row">
                        <span class="summary-label">GST Total ({{ $gstPercentage }}%)</span>
                        <span class="summary-value">₹{{ number_format($gstTotal, 2) }}</span>
                    </div>
                    @endif
                    
                    @if($order->discount_percentage > 0)
                    <div class="summary-row">
                        <span class="summary-label">Order Discount ({{ $order->discount_percentage }}%)</span>
                        <span class="summary-value text-success">- ₹{{ number_format($order->discount, 2) }}</span>
                    </div>
                    @endif
                    
                    @php
                        $grandTotal = $order->grand_total;
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
                    
                    <div class="summary-total final-total mt-3">
                        <span class="label"><i class="fas fa-check-circle me-1"></i> Final Bill Amount</span>
                        <span class="value">₹{{ number_format($finalAmount, 2) }}</span>
                    </div>

                    <!-- Payment Details -->
                    <div class="payment-card">
                        <h6 class="mb-3"><i class="fas fa-credit-card me-2"></i>Payment Summary</h6>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Amount Paid:</span>
                            <span class="fw-bold text-success">₹{{ number_format($order->amount_paid ?? 0, 2) }}</span>
                        </div>
                        
                        @php
                            $balance = $finalAmount - ($order->amount_paid ?? 0);
                        @endphp
                        
                        @if($balance > 0)
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Balance Due:</span>
                            <span class="fw-bold text-danger">₹{{ number_format($balance, 2) }}</span>
                        </div>
                        @endif
                        
                        <div class="alert {{ $order->payment_status == 'PAID' ? 'alert-success' : ($order->payment_status == 'MISCORDER' ? 'alert-warning' : 'alert-info') }} mt-2 mb-0">
                            <i class="fas {{ $order->payment_status == 'PAID' ? 'fa-check-circle' : ($order->payment_status == 'MISCORDER' ? 'fa-exclamation-triangle' : 'fa-clock') }} me-2"></i>
                            @if($order->payment_status == 'PAID')
                                <strong>Payment Completed:</strong> This order has been fully paid.
                            @elseif($order->payment_status == 'MISCORDER')
                                <strong>Miscorder Status:</strong> Customer ate but payment not completed.
                            @else
                                <strong>Payment Pending:</strong> This order requires payment.
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons mt-4">
                    <a href="{{ route('order.report.management') }}" class="btn btn-secondary btn-action">
                        <i class="fas fa-arrow-left"></i> Back to Reports
                    </a>
                    <a href="{{ route('order.invoice', $order->id) }}" class="btn btn-success btn-action" target="_blank">
                        <i class="fas fa-print"></i> Print Invoice
                    </a>
                    @if($order->payment_status == 'PENDING')
                    <a href="{{ route('order.edit', $order->id) }}" class="btn btn-warning btn-action">
                        <i class="fas fa-edit"></i> Edit Order
                    </a>
                    <a href="{{ route('order.payment', $order->id) }}" class="btn btn-info btn-action">
                        <i class="fas fa-cash-register"></i> Add Payment
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('includes.script')

<script>
// Smooth page load animation
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.card-modern');
    cards.forEach((card, index) => {
        card.style.animationDelay = (index * 0.1) + 's';
    });
});
</script>

<style>
    /* Additional styles */
    .text-purple {
        color: #8b5cf6;
    }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, var(--primary), #34495e);
    }
    
    .table-responsive::-webkit-scrollbar {
        height: 6px;
    }
    
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .table-responsive::-webkit-scrollbar-thumb {
        background: var(--secondary);
        border-radius: 10px;
    }
    
    .final-total {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.01); }
        100% { transform: scale(1); }
    }
    
    .gst-info-box, .non-gst-info-box {
        margin-top: 15px;
    }
</style>

</body>
</html>