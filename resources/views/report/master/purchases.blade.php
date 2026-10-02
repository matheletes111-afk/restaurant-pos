@extends('layouts.app')

@section('title')
<title>Master Report - Purchases Report</title>
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
            <form method="GET" action="{{ route('admin.reports.master.purchases') }}" class="row g-3 align-items-end">
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
                    <label class="mr-form-label">Supplier</label>
                    <select name="supplier_id" class="form-select mr-input">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>
                                {{ $sup->supplier_name }} ({{ $sup->shop_name ?: 'Shop' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-2">
                    <label class="mr-form-label">Search</label>
                    <input type="text" name="search" class="form-control mr-input" placeholder="Invoice # / Supplier" value="{{ request('search') }}">
                </div>

                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="mr-btn mr-btn-primary flex-grow-1 justify-content-center">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.reports.master.purchases', $context['selectedOutletId'] ? ['outlet_id' => $context['selectedOutletId']] : []) }}" class="mr-btn mr-btn-outline" title="Reset Filters">
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
                        <span class="mr-kpi-label">Total Invoices</span>
                        <div class="mr-kpi-icon" style="background: #eef2ff; color: #4f46e5;">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value">{{ number_format($totalPurchasesCount) }}</div>
                    <div class="mr-kpi-footer">
                        <span>Total purchase bills recorded</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-4">
                <div class="mr-kpi-card">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">Total Purchase Amount</span>
                        <div class="mr-kpi-icon" style="background: #fffbeb; color: #d97706;">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value text-warning">₹{{ number_format($totalPurchasesAmount, 2) }}</div>
                    <div class="mr-kpi-footer">
                        <span>Total procurement outlay</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-4">
                <div class="mr-kpi-card">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">Total Items Procured</span>
                        <div class="mr-kpi-icon" style="background: #ecfeff; color: #0891b2;">
                            <i class="fas fa-boxes"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value">{{ number_format($totalItemsPurchased) }}</div>
                    <div class="mr-kpi-footer">
                        <span>Cumulative items volume</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. PURCHASES TABLE --}}
        <div class="mr-table-card">
            <div class="mr-table-header">
                <h5 class="mr-table-title">
                    <i class="fas fa-truck-loading text-primary"></i> Inventory Purchases Ledger
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="mr-btn mr-btn-success" onclick="exportActiveReportToExcel()">
                        <i class="fas fa-file-excel"></i> Download Excel
                    </button>
                </div>
            </div>
            <div class="table-responsive p-3">
                <table class="table mr-table align-middle" id="masterPurchasesTable">
                    <thead>
                        <tr>
                            <th>Invoice No</th>
                            <th>Purchase Date</th>
                            <th>Supplier Name</th>
                            <th>Shop / Organization</th>
                            <th>Total Items</th>
                            <th>Invoice Amount (₹)</th>
                            <th>Items Summary</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $purchase)
                            <tr>
                                <td class="font-weight-bold text-primary">
                                    {{ $purchase->invoice_no ?: ('PUR-'.$purchase->id) }}
                                </td>
                                <td>
                                    {{ $purchase->purchase_date ? $purchase->purchase_date->format('d M Y') : $purchase->created_at->format('d M Y') }}
                                </td>
                                <td>
                                    <div class="font-weight-bold text-dark">
                                        {{ $purchase->supplier ? $purchase->supplier->supplier_name : 'Direct Vendor' }}
                                    </div>
                                    @if($purchase->supplier && $purchase->supplier->phone)
                                        <small class="text-muted"><i class="fas fa-phone-alt small me-1"></i>{{ $purchase->supplier->phone }}</small>
                                    @endif
                                </td>
                                <td>
                                    {{ ($purchase->supplier && $purchase->supplier->shop_name) ? $purchase->supplier->shop_name : '-' }}
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1">
                                        {{ $purchase->total_items ?: $purchase->items->count() }} items
                                    </span>
                                </td>
                                <td class="font-weight-bold text-dark">
                                    ₹{{ number_format($purchase->total_amount, 2) }}
                                </td>
                                <td>
                                    @if($purchase->items->isNotEmpty())
                                        <div class="small text-muted">
                                            @foreach($purchase->items->take(2) as $pItem)
                                                <span>{{ $pItem->product ? $pItem->product->product_name : 'Product' }} ({{ $pItem->quantity }} {{ $pItem->unit ? $pItem->unit->name : '' }})</span>@if(!$loop->last), @endif
                                            @endforeach
                                            @if($purchase->items->count() > 2)
                                                <span>+{{ $purchase->items->count() - 2 }} more</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $purchase->remarks ?: '-' }}</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-truck-loading fa-3x mb-3 text-slate-300"></i>
                                    <h5>No Purchases Found</h5>
                                    <p class="mb-0">No purchase records found for the selected date range.</p>
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
    if ($('#masterPurchasesTable tbody tr').length > 1 || !$('#masterPurchasesTable tbody td[colspan]').length) {
        $('#masterPurchasesTable').DataTable({
            order: [[1, 'desc']],
            pageLength: 25,
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search purchases..."
            }
        });
    }
});

function exportActiveReportToExcel() {
    exportTableToExcel('masterPurchasesTable', 'Master_Purchases_Report_{{ $fromDate->format("Ymd") }}_to_{{ $toDate->format("Ymd") }}');
}
</script>
@endsection
