@extends('layouts.app')

@section('title')
<title>Popular Item Report - Admin</title>
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
  .progress-cell-bar {
    height: 6px;
    border-radius: 3px;
    background: #e2e8f0;
    overflow: hidden;
    margin-top: 4px;
    width: 95px;
  }
  .progress-cell-fill {
    height: 100%;
    background: linear-gradient(90deg, #f59e0b, #ea580c);
    border-radius: 3px;
  }
  /* DataTables styling refinement */
  .dataTables_wrapper .dataTables_filter {
    margin-bottom: 14px;
    padding: 0 16px;
  }
  .dataTables_wrapper .dataTables_filter input {
    border: 1px solid var(--rpt-border);
    border-radius: 8px;
    padding: 6px 12px;
    font-size: 0.88rem;
    background: #f8fafc;
  }
  .dataTables_wrapper .dataTables_length {
    padding: 0 16px;
    margin-bottom: 14px;
    font-size: 0.85rem;
  }
  .dataTables_wrapper .dataTables_info,
  .dataTables_wrapper .dataTables_paginate {
    padding: 14px 16px;
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
                <div class="rpt-header-icon icon-top">
                    <i class="fas fa-fire-alt"></i>
                </div>
                <div class="rpt-header-title-meta">
                    <span class="rpt-header-eyebrow">Menu Popularity & Performance</span>
                    <h1 class="rpt-header-title">Popular Item Report</h1>
                    <p class="rpt-header-sub">
                        <i class="far fa-calendar-alt text-primary me-1"></i> Range: <strong>{{ $summary['date_range'] }}</strong>
                        <span class="mx-2">•</span>
                        <span>Best-selling dishes ranked by quantities, order frequency, and revenue</span>
                    </p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('report.popular.items.export', request()->all()) }}" class="btn-rpt-secondary">
                    <i class="fas fa-file-excel text-success"></i> Export Excel
                </a>
                <button type="button" class="btn-rpt-secondary" onclick="window.print();">
                    <i class="fas fa-print text-primary"></i> Print Analytics
                </button>
            </div>
        </div>

        {{-- 2. Filter Bar Deck (Always defaults to current month) --}}
        <div class="rpt-filter-card">
            <form action="{{ route('report.popular.items') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-xl-2 col-md-3 col-sm-6">
                    <label class="rpt-filter-label"><i class="far fa-calendar me-1"></i> From Date</label>
                    <input type="date" name="from_date" class="rpt-filter-control"
                           value="{{ request('from_date', $fromDate->format('Y-m-d')) }}">
                </div>

                <div class="col-xl-2 col-md-3 col-sm-6">
                    <label class="rpt-filter-label"><i class="far fa-calendar-check me-1"></i> To Date</label>
                    <input type="date" name="to_date" class="rpt-filter-control"
                           value="{{ request('to_date', $toDate->format('Y-m-d')) }}">
                </div>

                <div class="col-xl-2 col-md-3 col-sm-6">
                    <label class="rpt-filter-label"><i class="fas fa-folder me-1"></i> Category</label>
                    <select name="category_id" class="rpt-filter-control">
                        <option value="all">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xl-2 col-md-3 col-sm-6">
                    <label class="rpt-filter-label"><i class="fas fa-leaf me-1"></i> Food Type</label>
                    <select name="food_type" class="rpt-filter-control">
                        <option value="">All Types</option>
                        <option value="veg" {{ request('food_type') == 'veg' ? 'selected' : '' }}>Veg Only</option>
                        <option value="non-veg" {{ request('food_type') == 'non-veg' ? 'selected' : '' }}>Non-Veg Only</option>
                    </select>
                </div>

                <div class="col-xl-2 col-md-3 col-sm-6">
                    <label class="rpt-filter-label"><i class="fas fa-sort-amount-down me-1"></i> Sort By</label>
                    <select name="sort_by" class="rpt-filter-control">
                        <option value="qty" {{ request('sort_by', 'qty') == 'qty' ? 'selected' : '' }}>Quantity Sold (High to Low)</option>
                        <option value="orders" {{ request('sort_by') == 'orders' ? 'selected' : '' }}>Order Frequency</option>
                        <option value="revenue" {{ request('sort_by') == 'revenue' ? 'selected' : '' }}>Total Revenue (₹)</option>
                    </select>
                </div>

                <div class="col-xl-2 col-md-3 col-sm-12 d-flex gap-2">
                    <button type="submit" class="btn-rpt-primary flex-fill">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('report.popular.items') }}" class="btn-rpt-secondary" title="Reset to Current Month">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>

        {{-- 3. Metric KPI Cards --}}
        <div class="rpt-stats-grid">
            <div class="rpt-stat-card stat-warning">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">#1 Top Dish</span>
                    <span class="rpt-stat-val text-truncate" style="font-size: 1.25rem;" title="{{ $summary['top_item_name'] }}">
                        {{ $summary['top_item_name'] }}
                    </span>
                    <small class="text-warning fw-bold mt-1">
                        <i class="fas fa-trophy me-1"></i>{{ number_format($summary['top_item_qty']) }} Units Sold
                    </small>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-crown"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-primary">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Total Quantity Sold</span>
                    <span class="rpt-stat-val">{{ number_format($summary['total_quantity_sold']) }}</span>
                    <small class="text-muted mt-1 fw-semibold">Across {{ $summary['total_unique_items'] }} menu items</small>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-boxes"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-success">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Total Dishes Revenue</span>
                    <span class="rpt-stat-val">₹{{ number_format($summary['total_revenue'], 2) }}</span>
                    <small class="text-success mt-1 fw-bold">Realized in period</small>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-coins"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-purple">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Times Ordered</span>
                    <span class="rpt-stat-val">{{ number_format($summary['total_orders']) }}</span>
                    <small class="text-muted mt-1 fw-semibold">Total ticket inclusions</small>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>
        </div>

        {{-- 4. Main Popular Items Data Table --}}
        <div class="rpt-card">
            <div class="rpt-card-header">
                <h3 class="rpt-card-title">
                    <i class="fas fa-list-ol text-warning"></i> Ranked Dish Popularity
                </h3>
                <span class="text-muted small">Showing {{ $popularItems->count() }} items for current selection</span>
            </div>

            <div class="table-responsive">
                <table class="rpt-table" id="popularItemsTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 65px;" class="text-center">Rank</th>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th class="text-center">Food Type</th>
                            <th class="text-end">Times Ordered</th>
                            <th class="text-end">Quantity Sold</th>
                            <th class="text-end">Avg Price</th>
                            <th class="text-end">Total Revenue</th>
                            <th class="text-end" style="width: 125px;">Sales Share</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($popularItems as $item)
                        <tr>
                            <td class="text-center">
                                @if($item->rank == 1)
                                    <span class="rank-badge-circle rank-1" title="Top #1 Seller"><i class="fas fa-medal"></i></span>
                                @elseif($item->rank == 2)
                                    <span class="rank-badge-circle rank-2" title="Rank 2">2</span>
                                @elseif($item->rank == 3)
                                    <span class="rank-badge-circle rank-3" title="Rank 3">3</span>
                                @else
                                    <span class="rank-badge-circle rank-other">#{{ $item->rank }}</span>
                                @endif
                            </td>
                            <td>
                                <strong class="text-dark d-block">{{ $item->item_name }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1">{{ $item->category_name }}</span>
                            </td>
                            <td class="text-center">
                                @if(strtolower($item->food_type) === 'veg')
                                    <span class="food-type-icon veg" title="Vegetarian"><i class="fas fa-circle"></i></span>
                                    <span class="text-success small fw-bold">Veg</span>
                                @elseif(strtolower($item->food_type) === 'non-veg' || strtolower($item->food_type) === 'non_veg')
                                    <span class="food-type-icon non-veg" title="Non-Vegetarian"><i class="fas fa-play" style="transform: rotate(-90deg);"></i></span>
                                    <span class="text-danger small fw-bold">Non-Veg</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-end fw-semibold">
                                {{ number_format($item->total_orders) }}
                            </td>
                            <td class="text-end">
                                <span class="fw-bold text-dark">{{ number_format($item->total_qty) }}</span>
                                <div class="progress-cell-bar ms-auto">
                                    <div class="progress-cell-fill" style="width: {{ $item->qty_share_pct }}%;"></div>
                                </div>
                            </td>
                            <td class="text-end text-muted">
                                ₹{{ number_format($item->avg_price, 2) }}
                            </td>
                            <td class="text-end fw-bold text-success" style="font-family: 'Outfit', sans-serif;">
                                ₹{{ number_format($item->total_revenue, 2) }}
                            </td>
                            <td class="text-end">
                                <span class="fw-bold text-dark">{{ $item->rev_share_pct }}%</span>
                                <small class="text-muted d-block" style="font-size: 0.72rem;">of revenue</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-utensils fa-3x mb-3 text-secondary opacity-50"></i>
                                <h6 class="fw-bold">No Item Sales Data Found</h6>
                                <p class="mb-0 text-secondary">No completed orders were found for the selected date range and filter criteria.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($popularItems->count() > 0)
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td colspan="4" class="text-end py-3">TOTAL</td>
                            <td class="text-end py-3">{{ number_format($summary['total_orders']) }}</td>
                            <td class="text-end py-3">{{ number_format($summary['total_quantity_sold']) }}</td>
                            <td class="text-end py-3">-</td>
                            <td class="text-end py-3 text-success fs-6" style="font-family: 'Outfit', sans-serif;">
                                ₹{{ number_format($summary['total_revenue'], 2) }}
                            </td>
                            <td class="text-end py-3">100.0%</td>
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
{{-- Load jQuery first --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@include('includes.script')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
  $(document).ready(function() {
      if ($('#popularItemsTable tbody tr').length > 0 && !$('#popularItemsTable tbody tr td[colspan]').length) {
          $('#popularItemsTable').DataTable({
              paging: true,
              pageLength: 25,
              lengthMenu: [10, 25, 50, 100],
              ordering: true,
              info: true,
              language: {
                  search: "_INPUT_",
                  searchPlaceholder: "Search popular items..."
              }
          });
      }
  });
</script>
@endsection
