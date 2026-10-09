@extends('layouts.app')

@section('title')
<title>Paid Order Report - Admin</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
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
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div class="rpt-header-title-meta">
                    <span class="rpt-header-eyebrow">Paid Orders Ledger</span>
                    <h1 class="rpt-header-title">Paid Order Report</h1>
                    <p class="rpt-header-sub">Detailed ledger of all settled & paid guest orders</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('order.report.management') }}" class="btn-rpt-secondary">
                    <i class="fas fa-chart-pie me-1"></i> Advanced Management Report
                </a>
            </div>
        </div>

        {{-- 2. Filter Form --}}
        <div class="rpt-filter-card">
            <form method="GET" action="{{ route('order.report') }}" class="row g-3 align-items-end">
                <div class="col-md-4 col-sm-6">
                    <label class="rpt-filter-label">From Date</label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}" class="rpt-filter-control">
                </div>
                <div class="col-md-4 col-sm-6">
                    <label class="rpt-filter-label">To Date</label>
                    <input type="date" name="to_date" value="{{ request('to_date') }}" class="rpt-filter-control">
                </div>
                <div class="col-md-4 col-sm-12 d-flex gap-2">
                    <button type="submit" class="btn-rpt-primary flex-fill">
                        <i class="fas fa-filter"></i> Apply Filter
                    </button>
                    <a href="{{ route('order.report') }}" class="btn-rpt-secondary" title="Reset">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>

        @php
            $total_base = $orders->sum('total_amount');
            $total_gst = $orders->sum('gst_amount');
            $total_final = $orders->sum('grand_total');
        @endphp

        {{-- 3. Summary Stats Grid --}}
        <div class="rpt-stats-grid">
            <div class="rpt-stat-card stat-info">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Total Base Amount</span>
                    <span class="rpt-stat-val">₹{{ number_format($total_base, 2) }}</span>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-warning">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Total GST Collected</span>
                    <span class="rpt-stat-val">₹{{ number_format($total_gst, 2) }}</span>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-percent"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-success">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Grand Total Realized</span>
                    <span class="rpt-stat-val">₹{{ number_format($total_final, 2) }}</span>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-indian-rupee-sign"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-purple">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Total Orders Count</span>
                    <span class="rpt-stat-val">{{ count($orders) }}</span>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-bag-shopping"></i>
                </div>
            </div>
        </div>

        {{-- 4. Orders Table Card --}}
        <div class="rpt-card">
            <div class="rpt-card-header">
                <h3 class="rpt-card-title">
                    <i class="fas fa-list-check text-primary"></i>
                    Settled Orders
                    <span class="badge bg-light text-dark fw-bold border ms-2">{{ count($orders) }} Orders</span>
                </h3>
            </div>

            <div class="table-responsive">
                <table id="orderReportTable" class="rpt-table" style="width:100%">
                    <thead>
                        <tr>
                            <th width="40">#</th>
                            <th>Order ID</th>
                            <th>Order Channel</th>
                            <th>Customer</th>
                            <th class="text-end">Base Amount</th>
                            <th class="text-end">GST</th>
                            <th class="text-end">Final Amount</th>
                            <th class="text-center">Payment Status</th>
                            <th>Created At</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $key => $order)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                <a href="{{ route('order.report.order.details', @$order->id) }}" class="fw-bold text-primary font-monospace text-decoration-none">
                                    {{ $order->order_id }}
                                </a>
                            </td>
                            <td>
                                @if(@$order->order_type == "DINE_IN")
                                    <span class="rpt-badge badge-dinein"><i class="fas fa-utensils"></i> Dine In ({{ @$order->table->name ?? 'Table' }})</span>
                                @else
                                    <span class="rpt-badge badge-takeaway"><i class="fas fa-box"></i> Takeaway</span>
                                @endif
                            </td>
                            <td>
                                <strong class="text-dark">{{ $order->customer_name ?: 'Walk-in Guest' }}</strong>
                            </td>
                            <td class="text-end fw-semibold">₹{{ number_format($order->total_amount, 2) }}</td>
                            <td class="text-end text-muted">₹{{ number_format($order->gst_amount, 2) }}</td>
                            <td class="text-end text-dark fw-extrabold" style="font-family: 'Outfit', sans-serif; font-size: 0.95rem;">
                                ₹{{ number_format($order->grand_total ?? $order->final_amount, 2) }}
                            </td>
                            <td class="text-center">
                                @if($order->payment_status == 'PAID')
                                    <span class="rpt-badge badge-paid"><i class="fas fa-check-circle"></i> PAID</span>
                                @else
                                    <span class="rpt-badge badge-pending">{{ $order->payment_status }}</span>
                                @endif
                            </td>
                            <td class="text-muted small">
                                {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, h:i A') }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('order.report.order.details', @$order->id) }}" class="rpt-btn-action btn-view" title="View Order Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2 d-block text-muted opacity-50"></i>
                                No paid orders found matching your filter.
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
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
@include('includes.script')

<script>
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#orderReportTable')) {
        $('#orderReportTable').DataTable().destroy();
    }

    $('#orderReportTable').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        pageLength: 25,
        order: [[0, "asc"]],
        language: {
            search: "<i class='fas fa-search text-muted me-1'></i>",
            searchPlaceholder: "Search order, customer...",
            paginate: {
                previous: '<i class="fas fa-chevron-left"></i>',
                next: '<i class="fas fa-chevron-right"></i>'
            }
        }
    });
});
</script>
@endsection
