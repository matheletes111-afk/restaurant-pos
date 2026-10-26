@extends('layouts.app')

@section('title')
<title>Sales By Category Food Item Report - Admin</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="{{ asset('admin_template/css/report-analytics.css') }}">
<style>
  .category-group-header {
    background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%) !important;
    border-left: 4px solid var(--rpt-primary) !important;
    font-weight: 700;
    color: #9a3412;
  }
  .category-subtotal-badge {
    background: #ff5e14;
    color: #ffffff;
    font-size: 0.76rem;
    padding: 3px 9px;
    border-radius: 12px;
    font-weight: 600;
  }
  .progress-cell-bar {
    height: 6px;
    border-radius: 3px;
    background: #e2e8f0;
    overflow: hidden;
    margin-top: 4px;
    width: 100px;
  }
  .progress-cell-fill {
    height: 100%;
    background: linear-gradient(90deg, #ff5e14, #ff8c42);
    border-radius: 3px;
  }
  .badge-veg {
    background-color: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 6px;
  }
  .badge-nonveg {
    background-color: #fef2f2;
    color: #b91c1c;
    border: 1px solid #fecaca;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 6px;
  }
  .btn-export-excel {
    background: #10b981;
    color: #ffffff;
    border: none;
    border-radius: 8px;
    padding: 8px 16px;
    font-weight: 600;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: all 0.2s ease;
  }
  .btn-export-excel:hover {
    background: #059669;
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
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
                <div class="rpt-header-icon" style="background: linear-gradient(135deg, #ff5e14 0%, #ff8c42 100%);">
                    <i class="fas fa-layer-group text-white"></i>
                </div>
                <div class="rpt-header-title-meta">
                    <span class="rpt-header-eyebrow">Category & Item Sales Performance</span>
                    <h1 class="rpt-header-title">Sales By Category & Food Item Report</h1>
                    <p class="rpt-header-sub">Comprehensive breakdown of itemized sales, quantity sold, taxable value, and GST grouped by menu category</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('report.sales.by.category.export', ['from_date' => $fromDate->format('Y-m-d'), 'to_date' => $toDate->format('Y-m-d'), 'category_id' => $selectedCategoryId]) }}" class="btn-export-excel">
                    <i class="fas fa-file-excel"></i> Export to Excel
                </a>
                <button type="button" onclick="window.print()" class="btn-rpt-secondary">
                    <i class="fas fa-print text-primary"></i> Print Report
                </button>
            </div>
        </div>

        {{-- 2. Filter Form (Current Month by default) --}}
        <div class="rpt-filter-card">
            <form method="GET" action="{{ route('report.sales.by.category') }}" class="row g-3 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label class="rpt-filter-label">From Date</label>
                    <input type="date" name="from_date" value="{{ $fromDate->format('Y-m-d') }}" class="rpt-filter-control" required>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="rpt-filter-label">To Date</label>
                    <input type="date" name="to_date" value="{{ $toDate->format('Y-m-d') }}" class="rpt-filter-control" required>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="rpt-filter-label">Filter Category</label>
                    <select name="category_id" class="rpt-filter-control">
                        <option value="all">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (string)$selectedCategoryId === (string)$cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-sm-6 d-flex gap-2">
                    <button type="submit" class="btn-rpt-primary flex-fill">
                        <i class="fas fa-filter"></i> Apply Filter
                    </button>
                    <a href="{{ route('report.sales.by.category') }}" class="btn-rpt-secondary" title="Reset to Current Month">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- 3. Summary KPI Deck --}}
        <div class="rpt-stats-grid">
            <div class="rpt-stat-card stat-info">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Analysis Period</span>
                    <span class="rpt-stat-val" style="font-size: 1.1rem;">{{ $summary['date_range'] }}</span>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-primary">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Total Realized Revenue</span>
                    <span class="rpt-stat-val">₹{{ number_format($summary['total_revenue'], 2) }}</span>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-indian-rupee-sign"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-success">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Total Food Items Sold (Qty)</span>
                    <span class="rpt-stat-val">{{ number_format($summary['total_quantity']) }}</span>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-utensils"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-warning">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Active Categories & Dishes</span>
                    <span class="rpt-stat-val">{{ $summary['unique_categories'] }} Cats / {{ $summary['unique_dishes'] }} Dishes</span>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
            </div>
        </div>

        {{-- 4. Grouped Sales Table --}}
        <div class="rpt-table-card">
            <div class="rpt-table-header">
                <div>
                    <h5 class="rpt-table-title"><i class="fas fa-chart-pie me-2 text-primary"></i>Category-wise Itemized Sales</h5>
                    <p class="rpt-table-sub">Grouped sales performance showing orders count, volume sold, taxable base and total revenue</p>
                </div>
                <div>
                    <span class="badge bg-light text-dark border px-3 py-2 fw-semibold">
                        GST Collected: <strong class="text-primary">₹{{ number_format($summary['total_gst'], 2) }}</strong>
                    </span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table rpt-table mb-0" id="salesByCategoryTable">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Food Item Name</th>
                            <th>Food Type</th>
                            <th class="text-center">Orders Placed</th>
                            <th class="text-center">Quantity Sold</th>
                            <th class="text-end">Avg Unit Price</th>
                            <th class="text-end">Taxable Amount</th>
                            <th class="text-end">GST Amount</th>
                            <th class="text-end">Total Net Sales</th>
                            <th style="width: 120px;">Share (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groupedByCategory as $categoryName => $items)
                            @php
                                $catQty = $items->sum('total_qty');
                                $catRevenue = $items->sum('total_amount');
                                $catTaxable = $items->sum('total_taxable');
                                $catGst = $items->sum('total_gst');
                                $catOrders = $items->sum('order_count');
                            @endphp
                            {{-- Category Group Header Row --}}
                            <tr class="category-group-header">
                                <td colspan="4" class="py-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-folder-open text-primary"></i>
                                        <span class="fw-bold fs-6">{{ $categoryName }}</span>
                                        <span class="badge bg-white text-dark border ms-2" style="font-size: 0.7rem;">{{ count($items) }} Items</span>
                                    </div>
                                </td>
                                <td class="text-center py-2 fw-bold text-dark">{{ number_format($catQty) }}</td>
                                <td class="text-end py-2">-</td>
                                <td class="text-end py-2 fw-semibold text-muted">₹{{ number_format($catTaxable, 2) }}</td>
                                <td class="text-end py-2 fw-semibold text-muted">₹{{ number_format($catGst, 2) }}</td>
                                <td class="text-end py-2 fw-bold text-primary">₹{{ number_format($catRevenue, 2) }}</td>
                                <td class="py-2">
                                    <span class="category-subtotal-badge">
                                        {{ $summary['total_revenue'] > 0 ? round(($catRevenue / $summary['total_revenue']) * 100, 1) : 0 }}%
                                    </span>
                                </td>
                            </tr>

                            {{-- Item Rows --}}
                            @foreach($items as $idx => $item)
                                <tr>
                                    <td class="text-muted small ps-4">{{ $idx + 1 }}</td>
                                    <td>
                                        <span class="fw-semibold text-dark">{{ $item->item_name }}</span>
                                    </td>
                                    <td>
                                        @if(strtolower($item->food_type ?? '') == 'veg')
                                            <span class="badge-veg"><i class="fas fa-leaf me-1"></i> Veg</span>
                                        @elseif(strtolower($item->food_type ?? '') == 'non-veg' || strtolower($item->food_type ?? '') == 'non_veg')
                                            <span class="badge-nonveg"><i class="fas fa-drumstick-bite me-1"></i> Non-Veg</span>
                                        @else
                                            <span class="badge bg-light text-muted border" style="font-size: 0.72rem;">N/A</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border">{{ $item->order_count }}</span>
                                    </td>
                                    <td class="text-center fw-bold text-dark">{{ $item->total_qty }}</td>
                                    <td class="text-end text-muted">₹{{ number_format($item->avg_price, 2) }}</td>
                                    <td class="text-end text-muted">₹{{ number_format($item->total_taxable, 2) }}</td>
                                    <td class="text-end text-muted">₹{{ number_format($item->total_gst, 2) }}</td>
                                    <td class="text-end fw-bold text-dark">₹{{ number_format($item->total_amount, 2) }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress-cell-bar flex-grow-1">
                                                <div class="progress-cell-fill" style="width: {{ min(100, $item->share_pct) }}%;"></div>
                                            </div>
                                            <span class="small fw-semibold text-muted">{{ $item->share_pct }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-chart-pie" style="font-size: 2.8rem; color: #cbd5e1;"></i>
                                        <h6 class="mt-3 text-muted fw-bold">No Sales Data Found</h6>
                                        <p class="text-muted small mb-0">No paid orders were recorded in the selected period ({{ $fromDate->format('d M Y') }} - {{ $toDate->format('d M Y') }}).</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($groupedByCategory->count() > 0)
                        <tfoot>
                            <tr style="background: #f8fafc; font-weight: 800; border-top: 2px solid var(--rpt-border);">
                                <td colspan="4" class="text-uppercase py-3">Grand Total</td>
                                <td class="text-center py-3 text-primary fs-6">{{ number_format($summary['total_quantity']) }}</td>
                                <td class="text-end py-3">-</td>
                                <td class="text-end py-3">₹{{ number_format($summary['total_taxable'], 2) }}</td>
                                <td class="text-end py-3">₹{{ number_format($summary['total_gst'], 2) }}</td>
                                <td class="text-end py-3 text-primary fs-6">₹{{ number_format($summary['total_revenue'], 2) }}</td>
                                <td class="py-3">100%</td>
                            </tr>
                        </tfoot>
                    @endif
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
    // Ready for print or quick filtering
</script>
@endsection
