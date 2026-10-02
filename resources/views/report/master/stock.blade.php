@extends('layouts.app')

@section('title')
<title>Master Report - Live Stock Report</title>
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
            <form method="GET" action="{{ route('admin.reports.master.stock') }}" class="row g-3 align-items-end">
                @if($context['selectedOutletId'])
                    <input type="hidden" name="outlet_id" value="{{ $context['selectedOutletId'] }}">
                @endif

                <div class="col-12 col-sm-6 col-md-4">
                    <label class="mr-form-label">Stock Status</label>
                    <select name="status" class="form-select mr-input">
                        <option value="ALL">All Stock Levels</option>
                        <option value="IN_STOCK" {{ request('status') == 'IN_STOCK' ? 'selected' : '' }}>In Stock (>10 units)</option>
                        <option value="LOW_STOCK" {{ request('status') == 'LOW_STOCK' ? 'selected' : '' }}>Low Stock Alert (1 - 10 units)</option>
                        <option value="OUT_OF_STOCK" {{ request('status') == 'OUT_OF_STOCK' ? 'selected' : '' }}>Out of Stock (0 or less)</option>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-5">
                    <label class="mr-form-label">Search Product</label>
                    <input type="text" name="search" class="form-control mr-input" placeholder="Search product name..." value="{{ request('search') }}">
                </div>

                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="mr-btn mr-btn-primary flex-grow-1 justify-content-center">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.reports.master.stock', $context['selectedOutletId'] ? ['outlet_id' => $context['selectedOutletId']] : []) }}" class="mr-btn mr-btn-outline" title="Reset Filters">
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
                        <span class="mr-kpi-label">Total In-Stock Items</span>
                        <div class="mr-kpi-icon" style="background: #ecfdf5; color: #059669;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value text-success">{{ number_format($inStockCount) }}</div>
                    <div class="mr-kpi-footer">
                        <span>Healthy inventory levels</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="mr-kpi-card">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">Low Stock Alerts</span>
                        <div class="mr-kpi-icon" style="background: #fffbeb; color: #d97706;">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value text-warning">{{ number_format($lowStockCount) }}</div>
                    <div class="mr-kpi-footer">
                        <span>Items below threshold (&le; 10)</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="mr-kpi-card">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">Out of Stock</span>
                        <div class="mr-kpi-icon" style="background: #fef2f2; color: #dc2626;">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value text-danger">{{ number_format($outOfStockCount) }}</div>
                    <div class="mr-kpi-footer">
                        <span>Items requiring immediate purchase</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="mr-kpi-card">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">Total Stock Quantity</span>
                        <div class="mr-kpi-icon" style="background: #eef2ff; color: #4f46e5;">
                            <i class="fas fa-cubes"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value">{{ number_format($totalStockUnits, 2) }}</div>
                    <div class="mr-kpi-footer">
                        <span>Cumulative inventory volume</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. STOCK DATA TABLE --}}
        <div class="mr-table-card">
            <div class="mr-table-header">
                <h5 class="mr-table-title">
                    <i class="fas fa-warehouse text-primary"></i> Live Inventory Stock Summary
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="mr-btn mr-btn-success" onclick="exportActiveReportToExcel()">
                        <i class="fas fa-file-excel"></i> Download Excel
                    </button>
                </div>
            </div>
            <div class="table-responsive p-3">
                <table class="table mr-table align-middle" id="masterStockTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product Name</th>
                            <th>Unit</th>
                            <th>Opening Quantity</th>
                            <th>Current Live Stock</th>
                            <th>Stock Status</th>
                            <th>Health Indicator</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $index => $product)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="font-weight-bold text-dark">{{ $product->product_name }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $product->unit ? $product->unit->name : 'Units' }}</span>
                                </td>
                                <td>{{ number_format($product->opening_qty, 2) }}</td>
                                <td class="font-weight-bold {{ $product->current_stock <= 0 ? 'text-danger' : ($product->current_stock <= 10 ? 'text-warning' : 'text-success') }}">
                                    {{ number_format($product->current_stock, 2) }} {{ $product->unit ? $product->unit->name : '' }}
                                </td>
                                <td>
                                    @if($product->stock_status === 'IN_STOCK')
                                        <span class="mr-badge mr-badge-in-stock"><i class="fas fa-check"></i> In Stock</span>
                                    @elseif($product->stock_status === 'LOW_STOCK')
                                        <span class="mr-badge mr-badge-low-stock"><i class="fas fa-exclamation-triangle"></i> Low Stock</span>
                                    @else
                                        <span class="mr-badge mr-badge-out-stock"><i class="fas fa-times"></i> Out of Stock</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $percent = min(100, max(5, ($product->current_stock / max(1, $product->opening_qty ?: 50)) * 100));
                                        $color = $product->current_stock <= 0 ? 'bg-danger' : ($product->current_stock <= 10 ? 'bg-warning' : 'bg-success');
                                    @endphp
                                    <div class="progress" style="height: 6px; width: 120px;">
                                        <div class="progress-bar {{ $color }}" role="progressbar" style="width: {{ $percent }}%"></div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-warehouse fa-3x mb-3 text-slate-300"></i>
                                    <h5>No Inventory Products Found</h5>
                                    <p class="mb-0">No product records match the specified search or filter.</p>
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
    if ($('#masterStockTable tbody tr').length > 1 || !$('#masterStockTable tbody td[colspan]').length) {
        $('#masterStockTable').DataTable({
            order: [[4, 'asc']],
            pageLength: 25,
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search stock..."
            }
        });
    }
});

function exportActiveReportToExcel() {
    exportTableToExcel('masterStockTable', 'Master_Live_Stock_Report');
}
</script>
@endsection
