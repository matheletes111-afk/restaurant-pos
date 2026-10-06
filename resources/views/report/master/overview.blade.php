@extends('layouts.app')

@section('title')
<title>Master Executive Report - Overview</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@endsection

@section('body')
@include('includes.sidebar')

<div class="pc-container">
<div class="pc-content">

    <div class="mr-wrap">
        {{-- Flash Messages --}}
        @include('includes.message')

        {{-- Master Header & Nav --}}
        @include('report.master.nav')

        {{-- 1. TOP EXECUTIVE KPI CARDS --}}
        <div class="row g-3 mb-4">
            {{-- Dishes Count --}}
            <div class="col-12 col-sm-6 col-xl-2">
                <div class="mr-kpi-card">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">Active Dishes</span>
                        <div class="mr-kpi-icon" style="background: #eef2ff; color: #4f46e5;">
                            <i class="fas fa-utensils"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value">{{ number_format($totalDishes) }}</div>
                    <div class="mr-kpi-footer">
                        <i class="fas fa-layer-group text-primary"></i>
                        <span>{{ number_format($totalCategories) }} Categories</span>
                    </div>
                </div>
            </div>

            {{-- Today's Orders --}}
            <div class="col-12 col-sm-6 col-xl-2">
                <div class="mr-kpi-card">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">Today's Orders</span>
                        <div class="mr-kpi-icon" style="background: #ecfeff; color: #0891b2;">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value">{{ number_format($todayOrdersCount) }}</div>
                    <div class="mr-kpi-footer">
                        <i class="fas fa-calendar-day text-info"></i>
                        <span>Live Today Count</span>
                    </div>
                </div>
            </div>

            {{-- Today's Order Value (Gross Sales) --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="mr-kpi-card">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">Today's Order Value</span>
                        <div class="mr-kpi-icon" style="background: #fdf4ff; color: #c026d3;">
                            <i class="fas fa-receipt"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value">₹{{ number_format($todayOrderValue, 2) }}</div>
                    <div class="mr-kpi-footer">
                        <i class="fas fa-percent text-muted"></i>
                        <span>GST: ₹{{ number_format($todayTaxAmount, 2) }} | Disc: ₹{{ number_format($todayDiscountAmount, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Today's Collected Amount --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="mr-kpi-card">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">Today's Collected</span>
                        <div class="mr-kpi-icon" style="background: #ecfdf5; color: #059669;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value text-success">₹{{ number_format($todayCollectedAmount, 2) }}</div>
                    <div class="mr-kpi-footer">
                        <i class="fas fa-wallet text-success"></i>
                        <span>Realized Cash & Digital Inflow</span>
                    </div>
                </div>
            </div>

            {{-- Today's Due Amount --}}
            <div class="col-12 col-sm-6 col-xl-2">
                <div class="mr-kpi-card">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">Today's Due</span>
                        <div class="mr-kpi-icon" style="background: #fef2f2; color: #dc2626;">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value text-danger">₹{{ number_format($todayDueAmount, 2) }}</div>
                    <div class="mr-kpi-footer">
                        <i class="fas fa-exclamation-circle text-danger"></i>
                        <span>Outstanding Unpaid</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. MONTH-TO-DATE HIGHLIGHTS & FINANCIAL BAR --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-lg-8">
                <div class="mr-table-card h-100 mb-0">
                    <div class="mr-table-header">
                        <h5 class="mr-table-title">
                            <i class="fas fa-calendar-alt text-primary"></i> Current Month Performance Snapshot
                        </h5>
                        <span class="badge bg-light text-dark font-weight-bold">
                            {{ now()->format('F Y') }}
                        </span>
                    </div>
                    <div class="p-4">
                        <div class="row g-3 text-center">
                            <div class="col-6 col-md-3">
                                <div class="p-3 bg-light rounded-3">
                                    <span class="text-muted small text-uppercase font-weight-bold d-block mb-1">Month Orders</span>
                                    <h4 class="font-weight-bold mb-0 text-dark">{{ number_format($monthOrdersCount) }}</h4>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-3 bg-light rounded-3">
                                    <span class="text-muted small text-uppercase font-weight-bold d-block mb-1">Month Gross Value</span>
                                    <h4 class="font-weight-bold mb-0 text-primary">₹{{ number_format($monthOrderValue, 2) }}</h4>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-3 bg-light rounded-3">
                                    <span class="text-muted small text-uppercase font-weight-bold d-block mb-1">Month Collected</span>
                                    <h4 class="font-weight-bold mb-0 text-success">₹{{ number_format($monthCollected, 2) }}</h4>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="p-3 bg-light rounded-3">
                                    <span class="text-muted small text-uppercase font-weight-bold d-block mb-1">Month Due</span>
                                    <h4 class="font-weight-bold mb-0 text-danger">₹{{ number_format($monthDue, 2) }}</h4>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="font-weight-bold text-dark"><i class="fas fa-truck-loading text-warning me-1"></i> Today's Purchases</span>
                                        <span class="font-weight-bold text-warning">₹{{ number_format($todayPurchases, 2) }}</span>
                                    </div>
                                    <small class="text-muted">Procurement expenses recorded today</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="font-weight-bold text-dark"><i class="fas fa-wallet text-danger me-1"></i> Today's Expenses</span>
                                        <span class="font-weight-bold text-danger">₹{{ number_format($todayExpenses, 2) }}</span>
                                    </div>
                                    <small class="text-muted">Direct operational expenses logged today</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Today's Payment & Service Channels --}}
            <div class="col-12 col-lg-4">
                <div class="mr-table-card h-100 mb-0">
                    <div class="mr-table-header">
                        <h5 class="mr-table-title">
                            <i class="fas fa-chart-pie text-info"></i> Today's Payment Modes
                        </h5>
                    </div>
                    <div class="p-3">
                        @if($paymentMethods->isEmpty())
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-info-circle fa-2x mb-2 text-slate-300"></i>
                                <p class="mb-0 small">No payment transactions recorded today yet.</p>
                            </div>
                        @else
                            <div class="list-group list-group-flush">
                                @foreach($paymentMethods as $pm)
                                    <div class="list-group-item px-2 py-2 d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-primary-subtle text-primary me-2">{{ strtoupper($pm->payment_method) }}</span>
                                            <span class="text-muted small">{{ $pm->count }} order(s)</span>
                                        </div>
                                        <span class="font-weight-bold text-dark">₹{{ number_format($pm->total, 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. RECENT 5 ORDERS TODAY TABLE --}}
        <div class="mr-table-card">
            <div class="mr-table-header">
                <h5 class="mr-table-title">
                    <i class="fas fa-clock text-primary"></i> Latest Orders Placed Today
                </h5>
                <a href="{{ route('admin.reports.master.orders', $context['selectedOutletId'] ? ['outlet_id' => $context['selectedOutletId']] : []) }}" class="btn btn-sm btn-outline-primary">
                    View All Orders <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table mr-table mb-0" id="overviewRecentOrdersTable">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Time</th>
                            <th>Customer</th>
                            <th>Table / Type</th>
                            <th>Items</th>
                            <th>Grand Total</th>
                            <th>Collected</th>
                            <th>Due</th>
                            <th>Payment Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                            @php
                                $due = max(0, $order->grand_total - ($order->amount_paid ?? 0));
                                $pStatus = strtoupper($order->payment_status ?? 'UNPAID');
                            @endphp
                            <tr>
                                <td class="font-weight-bold text-primary">#{{ $order->id }}</td>
                                <td>{{ $order->created_at->format('h:i A') }}</td>
                                <td>
                                    <div class="font-weight-bold text-dark">{{ $order->customer_name ?: 'Walk-in Guest' }}</div>
                                    <small class="text-muted">{{ $order->customer_phone ?: '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ ucfirst(str_replace('_', ' ', $order->order_type ?? 'Dine In')) }}</span>
                                    @if($order->table)
                                        <small class="d-block text-muted">{{ $order->table->table_name ?? 'Table #'.$order->table_id }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary">{{ $order->items->sum('quantity') }} items</span>
                                </td>
                                <td class="font-weight-bold text-dark">₹{{ number_format($order->grand_total, 2) }}</td>
                                <td class="text-success font-weight-bold">₹{{ number_format($order->amount_paid ?? 0, 2) }}</td>
                                <td class="text-danger font-weight-bold">₹{{ number_format($due, 2) }}</td>
                                <td>
                                    @if($pStatus === 'PAID')
                                        <span class="mr-badge mr-badge-paid"><i class="fas fa-check-circle"></i> Paid</span>
                                    @elseif($pStatus === 'PARTIAL')
                                        <span class="mr-badge mr-badge-partial"><i class="fas fa-adjust"></i> Partial</span>
                                    @else
                                        <span class="mr-badge mr-badge-unpaid"><i class="fas fa-times-circle"></i> Unpaid</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('order.invoice', ['id' => $order->id]) }}" target="_blank" class="btn btn-sm btn-light" title="View Invoice">
                                        <i class="fas fa-file-invoice text-primary"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">
                                    <i class="fas fa-receipt fa-2x mb-2 text-slate-300"></i>
                                    <p class="mb-0">No orders placed today yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
</div>

@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@include('includes.script')
<script>
function exportActiveReportToExcel() {
    exportTableToExcel('overviewRecentOrdersTable', 'Master_Overview_Recent_Orders');
}
</script>
@endsection
