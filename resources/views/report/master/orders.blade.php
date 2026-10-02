@extends('layouts.app')

@section('title')
<title>Master Report - Orders List</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
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

        {{-- 1. DATE & PARAMETERS FILTER CARD --}}
        <div class="mr-filter-card">
            <form method="GET" action="{{ route('admin.reports.master.orders') }}" class="row g-3 align-items-end">
                @if($context['selectedOutletId'])
                    <input type="hidden" name="outlet_id" value="{{ $context['selectedOutletId'] }}">
                @endif

                <div class="col-12 col-sm-6 col-md-3">
                    <label class="mr-form-label"><i class="fas fa-calendar-alt text-primary me-1"></i> From Date</label>
                    <input type="date" name="from_date" class="form-control mr-input" value="{{ $fromDate->format('Y-m-d') }}">
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label class="mr-form-label"><i class="fas fa-calendar-alt text-primary me-1"></i> To Date</label>
                    <input type="date" name="to_date" class="form-control mr-input" value="{{ $toDate->format('Y-m-d') }}">
                </div>

                <div class="col-12 col-sm-6 col-md-2">
                    <label class="mr-form-label">Payment Status</label>
                    <select name="payment_status" class="form-select mr-input">
                        <option value="">All Statuses</option>
                        <option value="PAID" {{ request('payment_status') == 'PAID' ? 'selected' : '' }}>Paid</option>
                        <option value="PARTIAL" {{ request('payment_status') == 'PARTIAL' ? 'selected' : '' }}>Partial</option>
                        <option value="UNPAID" {{ request('payment_status') == 'UNPAID' ? 'selected' : '' }}>Unpaid / Pending</option>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-2">
                    <label class="mr-form-label">Search</label>
                    <input type="text" name="search" class="form-control mr-input" placeholder="Customer / Phone / Order #" value="{{ request('search') }}">
                </div>

                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="mr-btn mr-btn-primary flex-grow-1 justify-content-center">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.reports.master.orders', $context['selectedOutletId'] ? ['outlet_id' => $context['selectedOutletId']] : []) }}" class="mr-btn mr-btn-outline" title="Reset Filters">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>
            </form>
        </div>

        {{-- 2. SUMMARY METRICS BAR --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="mr-kpi-card">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">Filtered Orders</span>
                        <div class="mr-kpi-icon" style="background: #eef2ff; color: #4f46e5;">
                            <i class="fas fa-receipt"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value">{{ number_format($totalOrdersCount) }}</div>
                    <div class="mr-kpi-footer">
                        <span>Total orders in period</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="mr-kpi-card">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">Total Order Value</span>
                        <div class="mr-kpi-icon" style="background: #fdf4ff; color: #c026d3;">
                            <i class="fas fa-coins"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value">₹{{ number_format($totalOrderValue, 2) }}</div>
                    <div class="mr-kpi-footer">
                        <span>Gross invoice value</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="mr-kpi-card">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">Total Collected</span>
                        <div class="mr-kpi-icon" style="background: #ecfdf5; color: #059669;">
                            <i class="fas fa-check-double"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value text-success">₹{{ number_format($totalCollected, 2) }}</div>
                    <div class="mr-kpi-footer">
                        <span class="text-success">Total realized payments</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="mr-kpi-card">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">Total Due Amount</span>
                        <div class="mr-kpi-icon" style="background: #fef2f2; color: #dc2626;">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value text-danger">₹{{ number_format($totalDue, 2) }}</div>
                    <div class="mr-kpi-footer">
                        <span class="text-danger">Pending collection balance</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. ORDERS DATA TABLE --}}
        <div class="mr-table-card">
            <div class="mr-table-header">
                <h5 class="mr-table-title">
                    <i class="fas fa-list-ul text-primary"></i> Detailed Orders Report
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="mr-btn mr-btn-success" onclick="exportActiveReportToExcel()">
                        <i class="fas fa-file-excel"></i> Download Excel
                    </button>
                </div>
            </div>
            <div class="table-responsive p-3">
                <table class="table mr-table align-middle" id="masterOrdersTable">
                    <thead>
                        <tr>
                            <th>Order No</th>
                            <th>Date & Time</th>
                            <th>Customer Name</th>
                            <th>Phone Number</th>
                            <th>Order Type</th>
                            <th>Order Value (₹)</th>
                            <th>Collected (₹)</th>
                            <th>Due Amount (₹)</th>
                            <th>Payment Status</th>
                            <th>Payment Mode</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            @php
                                $due = max(0, $order->grand_total - ($order->amount_paid ?? 0));
                                $pStatus = strtoupper($order->payment_status ?? 'UNPAID');
                            @endphp
                            <tr>
                                <td class="font-weight-bold text-primary">#{{ $order->id }}</td>
                                <td>
                                    <div>{{ $order->created_at->format('d M Y') }}</div>
                                    <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                                </td>
                                <td>
                                    <div class="font-weight-bold text-dark">{{ $order->customer_name ?: 'Walk-in Guest' }}</div>
                                </td>
                                <td>
                                    @if($order->customer_phone)
                                        <span class="text-dark"><i class="fas fa-phone-alt text-muted small me-1"></i> {{ $order->customer_phone }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ ucfirst(str_replace('_', ' ', $order->order_type ?? 'Dine In')) }}</span>
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
                                    @if($order->payment_method)
                                        <span class="badge bg-primary-subtle text-primary">{{ strtoupper($order->payment_method) }}</span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('order.invoice', ['id' => $order->id]) }}" target="_blank" class="btn btn-sm btn-light border" title="View Order Invoice">
                                        <i class="fas fa-file-invoice text-primary"></i> Invoice
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-5 text-muted">
                                    <i class="fas fa-receipt fa-3x mb-3 text-slate-300"></i>
                                    <h5>No Orders Found</h5>
                                    <p class="mb-0">No orders match the selected date range and filter criteria.</p>
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
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    if ($('#masterOrdersTable tbody tr').length > 1 || !$('#masterOrdersTable tbody td[colspan]').length) {
        $('#masterOrdersTable').DataTable({
            order: [[0, 'desc']],
            pageLength: 25,
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search orders table..."
            }
        });
    }
});

function exportActiveReportToExcel() {
    exportTableToExcel('masterOrdersTable', 'Master_Orders_Report_{{ $fromDate->format("Ymd") }}_to_{{ $toDate->format("Ymd") }}');
}
</script>
@endsection
