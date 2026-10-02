@extends('layouts.app')

@section('title')
<title>Master Report - Order Items</title>
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

        {{-- 1. FILTERS --}}
        <div class="mr-filter-card">
            <form method="GET" action="{{ route('admin.reports.master.order-items') }}" class="row g-3 align-items-end">
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
                    <label class="mr-form-label">Category</label>
                    <select name="category_id" class="form-select mr-input">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-2">
                    <label class="mr-form-label">Search</label>
                    <input type="text" name="search" class="form-control mr-input" placeholder="Dish / Order # / Customer" value="{{ request('search') }}">
                </div>

                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="mr-btn mr-btn-primary flex-grow-1 justify-content-center">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.reports.master.order-items', $context['selectedOutletId'] ? ['outlet_id' => $context['selectedOutletId']] : []) }}" class="mr-btn mr-btn-outline" title="Reset Filters">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>
            </form>
        </div>

        {{-- 2. SUMMARY METRICS BAR --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-4">
                <div class="mr-kpi-card">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">Total Sold Quantity</span>
                        <div class="mr-kpi-icon" style="background: #eef2ff; color: #4f46e5;">
                            <i class="fas fa-boxes"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value">{{ number_format($totalItemsCount) }}</div>
                    <div class="mr-kpi-footer">
                        <span>Total item units served</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-4">
                <div class="mr-kpi-card">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">Total Item Sales Revenue</span>
                        <div class="mr-kpi-icon" style="background: #ecfdf5; color: #059669;">
                            <i class="fas fa-rupee-sign"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value text-success">₹{{ number_format($totalItemsRevenue, 2) }}</div>
                    <div class="mr-kpi-footer">
                        <span>Cumulative gross dish revenue</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-4">
                <div class="mr-kpi-card">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">Distinct Dishes Sold</span>
                        <div class="mr-kpi-icon" style="background: #fdf4ff; color: #c026d3;">
                            <i class="fas fa-utensils"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value">{{ number_format($distinctDishesCount) }}</div>
                    <div class="mr-kpi-footer">
                        <span>Unique menu items ordered</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. ORDER ITEMS TABLE --}}
        <div class="mr-table-card">
            <div class="mr-table-header">
                <h5 class="mr-table-title">
                    <i class="fas fa-utensils text-primary"></i> Itemized Sales Ledger
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="mr-btn mr-btn-success" onclick="exportActiveReportToExcel()">
                        <i class="fas fa-file-excel"></i> Download Excel
                    </button>
                </div>
            </div>
            <div class="table-responsive p-3">
                <table class="table mr-table align-middle" id="masterOrderItemsTable">
                    <thead>
                        <tr>
                            <th>Dish / Food Name</th>
                            <th>Category</th>
                            <th>Order No</th>
                            <th>Customer Name</th>
                            <th>Customer Phone</th>
                            <th>Unit Price (₹)</th>
                            <th>Quantity</th>
                            <th>Taxable (₹)</th>
                            <th>GST Amount (₹)</th>
                            <th>Final Amount (₹)</th>
                            <th>Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orderItems as $item)
                            <tr>
                                <td>
                                    <div class="font-weight-bold text-dark">
                                        {{ $item->subcategory ? $item->subcategory->name : 'Unknown Dish' }}
                                    </div>
                                    @if($item->subcategory && $item->subcategory->food_type)
                                        <small class="badge bg-light text-muted border">{{ strtoupper($item->subcategory->food_type) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary">
                                        {{ ($item->subcategory && $item->subcategory->category) ? $item->subcategory->category->name : 'General' }}
                                    </span>
                                </td>
                                <td class="font-weight-bold text-primary">
                                    <a href="{{ route('order.invoice', ['id' => $item->order_id]) }}" target="_blank">
                                        #{{ $item->order_id }}
                                    </a>
                                </td>
                                <td>
                                    <div class="font-weight-bold text-dark">{{ $item->order ? ($item->order->customer_name ?: 'Walk-in') : '-' }}</div>
                                </td>
                                <td>
                                    @if($item->order && $item->order->customer_phone)
                                        <span><i class="fas fa-phone-alt text-muted small me-1"></i> {{ $item->order->customer_phone }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>₹{{ number_format($item->price, 2) }}</td>
                                <td>
                                    <span class="badge bg-dark font-weight-bold px-2 py-1">{{ $item->quantity }}</span>
                                </td>
                                <td>₹{{ number_format($item->taxable_amount ?: ($item->price * $item->quantity), 2) }}</td>
                                <td>
                                    <div>₹{{ number_format($item->gst_amount ?? 0, 2) }}</div>
                                    @if($item->gst_rate)
                                        <small class="text-muted">({{ $item->gst_rate }}%)</small>
                                    @endif
                                </td>
                                <td class="font-weight-bold text-success">₹{{ number_format($item->total_amount, 2) }}</td>
                                <td>
                                    <div>{{ $item->created_at->format('d M Y') }}</div>
                                    <small class="text-muted">{{ $item->created_at->format('h:i A') }}</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-5 text-muted">
                                    <i class="fas fa-utensils fa-3x mb-3 text-slate-300"></i>
                                    <h5>No Order Items Found</h5>
                                    <p class="mb-0">No itemized sales match the selected filters and date range.</p>
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
    if ($('#masterOrderItemsTable tbody tr').length > 1 || !$('#masterOrderItemsTable tbody td[colspan]').length) {
        $('#masterOrderItemsTable').DataTable({
            order: [[10, 'desc']],
            pageLength: 25,
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search dish or customer..."
            }
        });
    }
});

function exportActiveReportToExcel() {
    exportTableToExcel('masterOrderItemsTable', 'Master_Order_Items_{{ $fromDate->format("Ymd") }}_to_{{ $toDate->format("Ymd") }}');
}
</script>
@endsection
