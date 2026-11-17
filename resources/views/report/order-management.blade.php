@extends('layouts.app')

@section('title')
<title>Admin - Order Management Report</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="{{ asset('admin_template/css/report-analytics.css') }}">
<style>
  .dataTables_wrapper {
    padding: 16px 20px;
  }
  .dataTables_filter input {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 6px 14px;
    outline: none;
    font-size: 0.85rem;
  }
  .dataTables_filter input:focus {
    border-color: var(--rpt-primary);
    background: #ffffff;
  }
  .dt-buttons .dt-button {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 20px !important;
    padding: 6px 14px !important;
    font-size: 0.8rem !important;
    font-weight: 700 !important;
    color: var(--rpt-slate) !important;
    margin-right: 6px !important;
    transition: all 0.2s ease !important;
  }
  .dt-buttons .dt-button:hover {
    background: #f8fafc !important;
    border-color: var(--rpt-primary) !important;
    color: var(--rpt-primary) !important;
  }
  .rpt-custom-tabs {
    background: #ffffff;
    padding: 6px;
    border-radius: 30px;
    box-shadow: 0 2px 10px rgba(15, 23, 42, 0.05);
    border: 1px solid #e2e8f0;
    display: inline-flex;
    gap: 6px;
  }
  .rpt-custom-tabs .nav-link {
    border-radius: 20px;
    padding: 8px 22px;
    font-weight: 700;
    font-size: 0.85rem;
    color: #64748b;
    transition: all 0.25s ease;
    border: none;
    text-decoration: none;
    display: flex;
    align-items: center;
  }
  .rpt-custom-tabs .nav-link:hover:not(.active) {
    color: #0f172a;
    background: #f1f5f9;
  }
  .rpt-custom-tabs .nav-link.active.tab-active-orders {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
  }
  .rpt-custom-tabs .nav-link.active.tab-cancel-orders {
    background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
  }
  .rpt-badge-cancelled {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
    padding: 4px 10px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.75rem;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }
</style>
@endsection

@section('body')
@include('includes.sidebar')

<div class="pc-container">
<div class="pc-content">

    <div class="rpt-page-wrap">
        {{-- Flash / Error Messages --}}
        @include('includes.message')

        {{-- 1. Header Deck --}}
        <div class="rpt-header-deck">
            <div class="rpt-header-left">
                <div class="rpt-header-icon icon-orders">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div class="rpt-header-title-meta">
                    <span class="rpt-header-eyebrow">Financial & Sales Audit</span>
                    <h1 class="rpt-header-title">Order Management Report</h1>
                    <p class="rpt-header-sub">Comprehensive order ledger, revenue analytics, tax breakdown, and payment auditing</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small fw-bold">Period: {{ $fromDate->format('d M Y') }} - {{ $toDate->format('d M Y') }}</span>
            </div>
        </div>

        {{-- 2. Filter Form --}}
        <div class="rpt-filter-card">
            <form method="GET" action="{{ route('order.report.management') }}" class="row g-3 align-items-end">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="col-md-3 col-sm-6">
                    <label class="rpt-filter-label">From Date</label>
                    <input type="date" name="from_date" value="{{ $fromDate->format('Y-m-d') }}" class="rpt-filter-control">
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="rpt-filter-label">To Date</label>
                    <input type="date" name="to_date" value="{{ $toDate->format('Y-m-d') }}" class="rpt-filter-control">
                </div>
                <div class="col-md-2 col-sm-6">
                    <label class="rpt-filter-label">Order Type</label>
                    <select name="order_type" class="rpt-filter-control">
                        <option value="all" {{ ($orderType == 'all' || !$orderType) ? 'selected' : '' }}>All Types</option>
                        @foreach($orderTypes as $type)
                            <option value="{{ $type }}" {{ $orderType == $type ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', strtolower($type))) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-sm-6">
                    <label class="rpt-filter-label">Payment Status</label>
                    <select name="payment_status" class="rpt-filter-control">
                        <option value="all" {{ ($paymentStatus == 'all' || !$paymentStatus) ? 'selected' : '' }}>All Status</option>
                        @foreach($paymentStatuses as $status)
                            <option value="{{ $status }}" {{ $paymentStatus == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-sm-12">
                    <button type="submit" class="btn-rpt-primary w-100 justify-content-center">
                        <i class="fas fa-filter"></i> Apply Filter
                    </button>
                </div>
            </form>
        </div>

        {{-- 3. Summary Stats Deck --}}
        <div class="rpt-stats-grid">
            <div class="rpt-stat-card stat-primary">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Total Revenue</span>
                    <span class="rpt-stat-val">₹{{ number_format($summary['total_revenue'], 2) }}</span>
                    <small class="text-muted mt-1">{{ $summary['total_orders'] }} Total Orders</small>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-success">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Amount Collected</span>
                    <span class="rpt-stat-val text-success">₹{{ number_format($summary['total_collected'], 2) }}</span>
                    <small class="text-muted mt-1">{{ $summary['paid_count'] }} Paid Orders</small>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-danger">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Pending Balance</span>
                    <span class="rpt-stat-val text-danger">₹{{ number_format($summary['pending_amount'], 2) }}</span>
                    <small class="text-muted mt-1">{{ $summary['pending_count'] }} Pending Orders</small>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-purple">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">GST Invoices</span>
                    <span class="rpt-stat-val text-purple">{{ $summary['gst_bills_count'] ?? 0 }}</span>
                    <small class="text-muted mt-1">₹{{ number_format($summary['total_gst_amount'] ?? 0, 2) }} Tax Collected</small>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>
        </div>

        <div class="rpt-stats-grid">
            <div class="rpt-stat-card stat-info">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Dine-in Orders</span>
                    <span class="rpt-stat-val text-info">{{ $summary['dine_in_count'] }}</span>
                    <small class="text-muted mt-1">Table Service</small>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-chair"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-indigo">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Takeaway Orders</span>
                    <span class="rpt-stat-val" style="color: var(--rpt-indigo); font-family: 'Outfit', sans-serif;">{{ $summary['takeaway_count'] }}</span>
                    <small class="text-muted mt-1">Parcel / Delivery</small>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-warning">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Item Discounts</span>
                    <span class="rpt-stat-val text-warning">₹{{ number_format($summary['total_item_discount'] ?? 0, 2) }}</span>
                    <small class="text-muted mt-1">Menu Dish Discounts</small>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-percentage"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-dark">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Order Discounts</span>
                    <span class="rpt-stat-val text-danger">₹{{ number_format($summary['total_order_discount'] ?? 0, 2) }}</span>
                    <small class="text-muted mt-1">Overall Bill Discounts</small>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-tags"></i>
                </div>
            </div>
        </div>

        {{-- 4. Navigation Tabs: Active Orders vs Cancel Order --}}
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
            <div class="rpt-custom-tabs">
                <a class="nav-link tab-active-orders {{ $tab !== 'cancelled' ? 'active' : '' }}" 
                   href="{{ route('order.report.management', array_merge(request()->query(), ['tab' => 'active'])) }}">
                    <i class="fas fa-file-invoice me-2"></i> Active Orders
                    <span class="badge {{ $tab !== 'cancelled' ? 'bg-light text-primary' : 'bg-secondary text-white' }} ms-2" style="font-size: 0.75rem;">
                        {{ $activeOrdersCount }}
                    </span>
                </a>
                <a class="nav-link tab-cancel-orders {{ $tab === 'cancelled' ? 'active' : '' }}" 
                   href="{{ route('order.report.management', array_merge(request()->query(), ['tab' => 'cancelled'])) }}">
                    <i class="fas fa-ban me-2"></i> Cancel Order
                    <span class="badge {{ $tab === 'cancelled' ? 'bg-light text-danger' : 'bg-secondary text-white' }} ms-2" style="font-size: 0.75rem;">
                        {{ $cancelledOrdersCount }}
                    </span>
                </a>
            </div>

            @if($tab === 'cancelled')
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-danger text-white px-3 py-2" style="border-radius: 12px; font-size: 0.82rem;">
                        <i class="fas fa-ban me-1"></i> {{ $cancelledOrdersCount }} Cancelled Invoices (₹{{ number_format($summary['cancelled_total_amount'] ?? 0, 2) }})
                    </span>
                </div>
            @else
                <div class="d-flex align-items-center gap-2 text-muted small">
                    <i class="fas fa-shield-alt text-primary"></i> Live audit &bull; Cancelled orders are excluded from financial counters
                </div>
            @endif
        </div>

        {{-- 5. Orders Datatable Card --}}
        <div class="rpt-card">
            <div class="rpt-card-header">
                <h5 class="rpt-card-title">
                    @if($tab === 'cancelled')
                        <i class="fas fa-ban text-danger"></i> Cancelled Invoices Ledger
                    @else
                        <i class="fas fa-list-alt text-primary"></i> Detailed Orders Audit
                    @endif
                </h5>
                <span class="text-muted small fw-bold">Displaying {{ $orders->count() }} records (Total: {{ $tab === 'cancelled' ? $cancelledOrdersCount : $activeOrdersCount }})</span>
            </div>

            <div class="table-responsive">
                <table id="ordersTable" class="rpt-table table-hover">
                    <thead>
                        @if($tab === 'cancelled')
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th class="text-center" style="width: 100px;">Actions</th>
                            <th>Order UID</th>
                            <th>Customer Info</th>
                            <th>Service Type</th>
                            <th class="text-end">Subtotal</th>
                            <th class="text-end">Grand Total</th>
                            <th class="text-center">Status</th>
                            <th>Cancellation Reason</th>
                            <th>Cancelled By</th>
                            <th>Cancelled Date</th>
                            <th>Order Date</th>
                        </tr>
                        @else
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th class="text-center">Actions</th>
                            <th>Order UID</th>
                            <th>Customer Info</th>
                            <th>Service Type</th>
                            <th class="text-end">Subtotal</th>
                            <th class="text-end">Discounts</th>
                            <th class="text-end">Taxable</th>
                            <th class="text-end">GST</th>
                            <th class="text-end">Grand Total</th>
                            <th class="text-end">Paid</th>
                            <th class="text-end">Due Balance</th>
                            <th class="text-center">Bill Type</th>
                            <th class="text-center">Status</th>
                            <th>Timestamp</th>
                        </tr>
                        @endif
                    </thead>
                    <tbody>
                        @if($tab === 'cancelled')
                            @forelse($orders as $key => $order)
                            <tr>
                                <td class="text-center text-muted fw-bold">
                                    {{ method_exists($orders, 'currentPage') ? ($orders->currentPage() - 1) * $orders->perPage() + $key + 1 : $key + 1 }}
                                </td>
                                <td>
                                    <div class="rpt-actions justify-content-center">
                                        <a href="{{ route('order.invoice', $order->id) }}" 
                                           class="rpt-btn-action btn-print" 
                                           title="View / Print Tax Invoice"
                                           target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <a href="{{ route('order.report.order.details', $order->id) }}" 
                                           class="rpt-btn-action btn-view" 
                                           title="View Order Breakdown">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold text-danger text-decoration-line-through" style="font-family: 'Outfit', sans-serif;">
                                        #{{ $order->order_id }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <strong class="text-dark">{{ $order->customer_name ?? 'Walk-in Guest' }}</strong>
                                        @if($order->customer_phone)
                                        <div class="text-muted small">
                                            <i class="fas fa-phone-alt me-1 text-secondary"></i> {{ $order->customer_phone }}
                                        </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($order->order_type == 'DINE_IN')
                                        <span class="rpt-badge badge-dinein">
                                            <i class="fas fa-chair"></i> Dine-in
                                        </span>
                                        @if($order->table)
                                            <div class="text-muted small mt-1">{{ $order->table->name }}</div>
                                        @endif
                                    @else
                                        <span class="rpt-badge badge-takeaway">
                                            <i class="fas fa-shopping-bag"></i> Takeaway
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end fw-semibold">₹{{ number_format($order->total_amount ?? 0, 2) }}</td>
                                <td class="text-end">
                                    <strong style="font-family: 'Outfit', sans-serif; font-size: 1rem; color: #dc2626;">
                                        ₹{{ number_format($order->grand_total, 2) }}
                                    </strong>
                                </td>
                                <td class="text-center">
                                    <span class="rpt-badge-cancelled">
                                        <i class="fas fa-ban"></i> CANCELLED
                                    </span>
                                </td>
                                <td>
                                    <div class="text-danger fw-semibold" style="max-width: 250px; word-wrap: break-word;">
                                        {{ $order->cancel_reason ?: 'No reason specified' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <i class="fas fa-user-shield me-1 text-secondary"></i> {{ $order->cancelledBy->name ?? 'Admin' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-danger fw-semibold small">{{ $order->deleted_at ? $order->deleted_at->format('d M Y') : '-' }}</div>
                                    <small class="text-muted">{{ $order->deleted_at ? $order->deleted_at->format('h:i A') : '' }}</small>
                                </td>
                                <td>
                                    <div class="text-dark fw-semibold small">{{ $order->created_at->format('d M Y') }}</div>
                                    <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="12" class="text-center py-5 text-muted">
                                    <i class="fas fa-ban fa-3x mb-3 text-secondary opacity-50"></i>
                                    <h6 class="fw-bold">No Cancelled Orders Found</h6>
                                    <p class="small text-muted mb-0">No deleted or cancelled orders match the selected date or filter criteria.</p>
                                </td>
                            </tr>
                            @endforelse
                        @else
                            @forelse($orders as $key => $order)
                            @php
                                $balance = round($order->grand_total, 2) - ($order->amount_paid ?? 0);
                                $isFullyPaid = $balance <= 0;
                                $isGstBill = ($order->is_gst_bill ?? 'NO') == 'YES';
                                $gstPercentage = $order->restaurant_gst_percentage ?? 0;
                                
                                $totalItemDiscount = 0;
                                if($order->orderItems) {
                                    foreach ($order->orderItems as $item) {
                                        $itemDiscPercent = floatval($item->item_discount_percentage ?? 0);
                                        $qty = intval($item->quantity ?? 1);
                                        $basePrice = floatval($item->price ?? 0);
                                        if ($itemDiscPercent > 0) {
                                            $totalItemDiscount += ($basePrice * $itemDiscPercent / 100) * $qty;
                                        } elseif (!empty($item->discounted_price) && $item->discounted_price < $basePrice) {
                                            $totalItemDiscount += ($basePrice - floatval($item->discounted_price)) * $qty;
                                        }
                                    }
                                }
                                $orderDiscountAmount = floatval($order->discount ?? 0);
                                $totalDiscount = $totalItemDiscount + $orderDiscountAmount;
                            @endphp
                            <tr>
                                <td class="text-center text-muted fw-bold">
                                    {{ method_exists($orders, 'currentPage') ? ($orders->currentPage() - 1) * $orders->perPage() + $key + 1 : $key + 1 }}
                                </td>
                                <td>
                                    <div class="rpt-actions justify-content-center">
                                        <a href="{{ route('order.invoice', $order->id) }}" 
                                           class="rpt-btn-action btn-print" 
                                           title="View / Print Tax Invoice"
                                           target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <a href="{{ route('order.report.order.details', $order->id) }}" 
                                           class="rpt-btn-action btn-view" 
                                           title="View Order Breakdown">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" 
                                                class="rpt-btn-action btn-del btn-delete-order" 
                                                data-order-id="{{ $order->id }}"
                                                data-order-uid="{{ $order->order_id }}"
                                                title="Delete Order">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold" style="font-family: 'Outfit', sans-serif; color: var(--rpt-primary);">
                                        #{{ $order->order_id }}
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <strong class="text-dark">{{ $order->customer_name ?? 'Walk-in Guest' }}</strong>
                                        @if($order->customer_phone)
                                        <div class="text-muted small">
                                            <i class="fas fa-phone-alt me-1 text-secondary"></i> {{ $order->customer_phone }}
                                        </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($order->order_type == 'DINE_IN')
                                        <span class="rpt-badge badge-dinein">
                                            <i class="fas fa-chair"></i> Dine-in
                                        </span>
                                        @if($order->table)
                                            <div class="text-muted small mt-1">{{ $order->table->name }}</div>
                                        @endif
                                    @else
                                        <span class="rpt-badge badge-takeaway">
                                            <i class="fas fa-shopping-bag"></i> Takeaway
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end fw-semibold">₹{{ number_format($order->total_amount ?? 0, 2) }}</td>
                                <td class="text-end {{ $totalDiscount > 0 ? 'text-danger' : 'text-muted' }} fw-semibold">
                                    {{ $totalDiscount > 0 ? '- ₹' . number_format($totalDiscount, 2) : '₹0.00' }}
                                </td>
                                <td class="text-end fw-semibold">₹{{ number_format($order->taxable_amount ?? 0, 2) }}</td>
                                <td class="text-end">
                                    @if($isGstBill)
                                        <span class="fw-bold text-dark">₹{{ number_format($order->gst_amount ?? 0, 2) }}</span>
                                        <div class="text-muted small">({{ $gstPercentage }}%)</div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <strong style="font-family: 'Outfit', sans-serif; font-size: 1rem; color: var(--rpt-dark);">
                                        ₹{{ number_format($order->grand_total, 2) }}
                                    </strong>
                                </td>
                                <td class="text-end text-success fw-bold">
                                    ₹{{ number_format($order->amount_paid ?? 0, 2) }}
                                </td>
                                <td class="text-end">
                                    @if($isFullyPaid)
                                        <span class="text-success fw-bold">₹0.00</span>
                                    @else
                                        <span class="text-danger fw-bold">₹{{ number_format($balance, 2) }}</span>
                                        <div class="text-danger small"><i class="fas fa-exclamation-circle"></i> Due</div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($isGstBill)
                                        <span class="rpt-badge badge-gst">
                                            <i class="fas fa-file-invoice"></i> GST
                                        </span>
                                    @else
                                        <span class="rpt-badge badge-nongst">Non-GST</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($order->payment_status == 'PAID')
                                        <span class="rpt-badge badge-paid">
                                            <i class="fas fa-check-circle"></i> Paid
                                        </span>
                                    @elseif($order->payment_status == 'PENDING')
                                        <span class="rpt-badge badge-pending">
                                            <i class="fas fa-clock"></i> Pending
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">{{ $order->payment_status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-dark fw-semibold small">{{ $order->created_at->format('d M Y') }}</div>
                                    <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="15" class="text-center py-5 text-muted">
                                    <i class="fas fa-file-invoice fa-3x mb-3 text-secondary opacity-50"></i>
                                    <h6 class="fw-bold">No Orders Found</h6>
                                    <p class="small text-muted mb-0">No transaction records match the selected date or filter criteria.</p>
                                </td>
                            </tr>
                            @endforelse
                        @endif
                    </tbody>
                </table>
            </div>

            @if(method_exists($orders, 'links'))
            <div class="p-3 border-top">
                {{ $orders->links() }}
            </div>
            @endif
        </div>

    </div>

</div>
</div>

{{-- Delete Order Modal (Reason confirmation - No OTP) --}}
<div class="modal fade" id="deleteOrderModal" tabindex="-1" aria-labelledby="deleteOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rpt-modal-content" style="border-radius: var(--rpt-radius-lg); border: none; box-shadow: 0 20px 40px rgba(15, 23, 42, 0.15);">
            <div class="modal-header p-4 border-bottom">
                <h5 class="modal-title fw-bold text-danger d-flex align-items-center gap-2" id="deleteOrderModalLabel" style="font-family: 'Outfit', sans-serif;">
                    <i class="fas fa-trash-alt"></i> Cancel / Delete Order
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-warning border-0 p-3 mb-3 d-flex align-items-start gap-2" style="border-radius: 12px; font-size: 0.88rem;">
                    <i class="fas fa-exclamation-triangle text-warning mt-1"></i>
                    <div>
                        Are you sure you want to delete order <strong id="deleteOrderUIDDisplay"></strong>?
                        <div class="small text-muted mt-1">This will soft delete the invoice, deduct it from report &amp; dashboard counters, and move it to the <strong>Cancel Order</strong> tab.</div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="rpt-filter-label">Reason for Cancellation / Deletion <span class="text-danger">*</span></label>
                    <textarea class="rpt-filter-control" id="deleteReason" rows="3" placeholder="Enter reason (e.g. Customer cancelled order, Duplicate punch, Test order, Wrong table bill)..." required></textarea>
                    <div class="text-danger small mt-1 fw-bold" id="deleteReasonFeedback" style="display: none;">Reason is required.</div>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger rounded-pill px-4 fw-bold" id="btnConfirmDeleteOrder">
                        <i class="fas fa-trash-alt me-1"></i> Confirm Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@include('includes.script')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    let currentDeleteOrderId = null;

    // Suppress intrusive DataTables alert popups
    if ($.fn.dataTable) {
        $.fn.dataTable.ext.errMode = 'none';
    }

    // Initialize DataTable only when table has actual data rows (not the empty state with colspan)
    const hasColspanEmpty = $('#ordersTable tbody tr td[colspan]').length > 0;
    const hasRows = $('#ordersTable tbody tr').length > 0;

    if ($('#ordersTable').length && hasRows && !hasColspanEmpty && !$.fn.DataTable.isDataTable('#ordersTable')) {
        $('#ordersTable').DataTable({
            paging: false,
            info: false,
            searching: true,
            ordering: true,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel me-1 text-success"></i> Export Excel',
                    className: 'btn-export'
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print me-1 text-primary"></i> Print Table',
                    className: 'btn-print'
                }
            ]
        });
    }

    // Handle delete button click
    $(document).on('click', '.btn-delete-order', function(e) {
        e.preventDefault();
        currentDeleteOrderId = $(this).data('order-id');
        let orderUID = $(this).data('order-uid');

        $('#deleteOrderUIDDisplay').text('#' + orderUID);
        $('#deleteReason').val('');
        $('#deleteReasonFeedback').hide();

        const deleteModal = new bootstrap.Modal(document.getElementById('deleteOrderModal'));
        deleteModal.show();
    });

    // Confirm Delete
    $('#btnConfirmDeleteOrder').on('click', function() {
        let reason = $('#deleteReason').val().trim();
        if (!reason) {
            $('#deleteReasonFeedback').text('Reason is required.').show();
            $('#deleteReason').focus();
            return;
        }
        $('#deleteReasonFeedback').hide();

        let btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Deleting...');

        $.ajax({
            url: "{{ route('order.report.management.delete') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                order_id: currentDeleteOrderId,
                cancel_reason: reason
            },
            success: function(response) {
                btn.prop('disabled', false).html('<i class="fas fa-trash-alt me-1"></i> Confirm Delete');
                if (response.success) {
                    const modalEl = document.getElementById('deleteOrderModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) modalInstance.hide();
                    
                    alert(response.message || 'Order cancelled successfully.');
                    window.location.reload();
                } else {
                    $('#deleteReasonFeedback').text(response.message || 'An error occurred.').show();
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="fas fa-trash-alt me-1"></i> Confirm Delete');
                let msg = 'An error occurred while deleting the order.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                $('#deleteReasonFeedback').text(msg).show();
            }
        });
    });
});
</script>
@endsection