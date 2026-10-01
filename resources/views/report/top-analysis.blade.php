@extends('layouts.app')

@section('title')
<title>Top Analysis Report - Admin</title>
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
  .dt-buttons .dt-button {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 20px !important;
    padding: 5px 12px !important;
    font-size: 0.78rem !important;
    font-weight: 700 !important;
    color: var(--rpt-slate) !important;
    margin-right: 4px !important;
    transition: all 0.2s ease !important;
  }
  .dt-buttons .dt-button:hover {
    background: #f8fafc !important;
    border-color: var(--rpt-primary) !important;
    color: var(--rpt-primary) !important;
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
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="rpt-header-title-meta">
                    <span class="rpt-header-eyebrow">Performance & Sales Leaderboard</span>
                    <h1 class="rpt-header-title">Top Analysis Report</h1>
                    <p class="rpt-header-sub">Identify highest-spending loyal customers and top performing menu items</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" id="btnPrintReport" class="btn-rpt-secondary">
                    <i class="fas fa-print text-primary"></i> Print Full Report
                </button>
            </div>
        </div>

        {{-- 2. Filter Form --}}
        <div class="rpt-filter-card">
            <form method="GET" action="{{ route('order.report.top.analysis') }}" class="row g-3 align-items-end">
                <div class="col-md-4 col-sm-6">
                    <label class="rpt-filter-label">From Date</label>
                    <input type="date" name="from_date" value="{{ request('from_date') ?? \Carbon\Carbon::now()->subDays(30)->format('Y-m-d') }}" class="rpt-filter-control">
                </div>
                <div class="col-md-4 col-sm-6">
                    <label class="rpt-filter-label">To Date</label>
                    <input type="date" name="to_date" value="{{ request('to_date') ?? \Carbon\Carbon::now()->format('Y-m-d') }}" class="rpt-filter-control">
                </div>
                <div class="col-md-4 col-sm-12 d-flex gap-2">
                    <button type="submit" class="btn-rpt-primary flex-fill">
                        <i class="fas fa-filter"></i> Apply Filter
                    </button>
                    <a href="{{ route('order.report.top.analysis') }}" class="btn-rpt-secondary" title="Reset Filters">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>

        {{-- 3. Summary Stats Grid --}}
        <div class="rpt-stats-grid">
            <div class="rpt-stat-card stat-info">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Analysis Date Range</span>
                    <span class="rpt-stat-val" style="font-size: 1.15rem; line-height: 1.4;">{{ $summary['date_range'] }}</span>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-success">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Total Active Customers</span>
                    <span class="rpt-stat-val">{{ $summary['total_customers'] }}</span>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-warning">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Paid Orders Count</span>
                    <span class="rpt-stat-val">{{ $summary['total_orders'] }}</span>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-bag-shopping"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-purple">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Total Realized Revenue</span>
                    <span class="rpt-stat-val">₹{{ $summary['total_revenue'] }}</span>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-coins"></i>
                </div>
            </div>
        </div>

        {{-- 4. Two Column Leaderboard --}}
        <div class="row">
            {{-- Top 10 Customers --}}
            <div class="col-lg-6 mb-4">
                <div class="rpt-card h-100">
                    <div class="rpt-card-header">
                        <h3 class="rpt-card-title">
                            <i class="fas fa-crown text-warning"></i>
                            Top 10 Customers (By Spend)
                        </h3>
                    </div>
                    <div class="table-responsive">
                        <table id="topCustomersTable" class="rpt-table" style="width:100%">
                            <thead>
                                <tr>
                                    <th width="50">Rank</th>
                                    <th>Customer Details</th>
                                    <th>Phone</th>
                                    <th class="text-center">Orders</th>
                                    <th class="text-end">Total Spent</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topCustomers as $customer)
                                <tr>
                                    <td>
                                        @if($customer->rank == 1)
                                            <span class="rank-badge-circle rank-1"><i class="fas fa-medal"></i></span>
                                        @elseif($customer->rank == 2)
                                            <span class="rank-badge-circle rank-2">2</span>
                                        @elseif($customer->rank == 3)
                                            <span class="rank-badge-circle rank-3">3</span>
                                        @else
                                            <span class="rank-badge-circle rank-other">{{ $customer->rank }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong class="text-dark d-block">{{ $customer->customer_name }}</strong>
                                        <small class="text-muted">Last order: {{ $customer->last_order_date }}</small>
                                    </td>
                                    <td>
                                        <span class="text-muted small font-monospace">{{ $customer->customer_phone ?? 'N/A' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-2 py-1">{{ $customer->total_orders }}</span>
                                    </td>
                                    <td class="text-end text-success fw-extrabold" style="font-family: 'Outfit', sans-serif; font-size: 0.95rem;">
                                        ₹{{ $customer->total_spent }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Top 10 Dishes --}}
            <div class="col-lg-6 mb-4">
                <div class="rpt-card h-100">
                    <div class="rpt-card-header">
                        <h3 class="rpt-card-title">
                            <i class="fas fa-fire text-danger"></i>
                            Top 10 Dishes (By Quantity)
                        </h3>
                    </div>
                    <div class="table-responsive">
                        <table id="topDishesTable" class="rpt-table" style="width:100%">
                            <thead>
                                <tr>
                                    <th width="50">Rank</th>
                                    <th>Dish Name</th>
                                    <th>Type</th>
                                    <th class="text-center">Qty Sold</th>
                                    <th class="text-end">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topDishes as $dish)
                                <tr>
                                    <td>
                                        @if($dish->rank == 1)
                                            <span class="rank-badge-circle rank-1"><i class="fas fa-medal"></i></span>
                                        @elseif($dish->rank == 2)
                                            <span class="rank-badge-circle rank-2">2</span>
                                        @elseif($dish->rank == 3)
                                            <span class="rank-badge-circle rank-3">3</span>
                                        @else
                                            <span class="rank-badge-circle rank-other">{{ $dish->rank }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($dish->food_type == 'veg')
                                                <span class="food-type-icon veg" title="Vegetarian"><i class="fas fa-circle"></i></span>
                                            @else
                                                <span class="food-type-icon non-veg" title="Non-Vegetarian"><i class="fas fa-circle"></i></span>
                                            @endif
                                            <div>
                                                <strong class="text-dark d-block">{{ $dish->dish_name }}</strong>
                                                <small class="text-muted">Avg Price: ₹{{ $dish->avg_price }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($dish->food_type == 'veg')
                                            <span class="rpt-badge badge-paid">Veg</span>
                                        @else
                                            <span class="rpt-badge" style="background:#fef2f2; color:#ef4444; border:1px solid #fecaca;">Non-Veg</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-2 py-1">{{ $dish->total_quantity }}</span>
                                    </td>
                                    <td class="text-end text-dark fw-extrabold" style="font-family: 'Outfit', sans-serif; font-size: 0.95rem;">
                                        ₹{{ $dish->total_revenue }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. Additional Business Insights --}}
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="rpt-chart-box">
                    <div class="rpt-chart-title">
                        <div class="title-left">
                            <i class="fas fa-chart-line text-primary"></i>
                            <span>Customer Value Dynamics</span>
                        </div>
                    </div>
                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="rpt-counter-card" style="background: var(--rpt-info-bg); border: 1px solid var(--rpt-info-border);">
                                <span class="rpt-counter-lbl" style="color: var(--rpt-info-text);">Avg. Order Value (AOV)</span>
                                <span class="rpt-counter-val text-primary mt-1">₹{{ $summary['avg_order_value'] }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="rpt-counter-card" style="background: var(--rpt-success-bg); border: 1px solid var(--rpt-success-border);">
                                <span class="rpt-counter-lbl" style="color: var(--rpt-success-text);">Unique Dishes Ordered</span>
                                <span class="rpt-counter-val text-success mt-1">{{ $summary['unique_dishes'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="rpt-chart-box">
                    <div class="rpt-chart-title">
                        <div class="title-left">
                            <i class="fas fa-bowl-food text-warning"></i>
                            <span>Menu Volume Dynamics</span>
                        </div>
                    </div>
                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="rpt-counter-card" style="background: var(--rpt-warning-bg); border: 1px solid var(--rpt-warning-border);">
                                <span class="rpt-counter-lbl" style="color: var(--rpt-warning-text);">Total Items Sold</span>
                                <span class="rpt-counter-val text-warning mt-1">{{ $summary['total_dishes_sold'] }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="rpt-counter-card" style="background: var(--rpt-purple-bg); border: 1px solid var(--rpt-purple-border);">
                                <span class="rpt-counter-lbl" style="color: var(--rpt-purple-text);">Items per Order Ratio</span>
                                <span class="rpt-counter-val text-purple mt-1">
                                    @if($summary['total_orders'] > 0)
                                        {{ number_format($summary['total_dishes_sold'] / $summary['total_orders'], 1) }}
                                    @else
                                        0
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
</div>
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
@include('includes.script')

<script>
$(document).ready(function() {
    $('#topCustomersTable').DataTable({
        paging: false,
        searching: false,
        ordering: false,
        info: false,
        dom: '<"d-flex justify-content-end mb-2"<"dt-buttons"B>>rt',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel text-success me-1"></i> Excel',
                className: 'dt-button',
                title: 'Top_10_Customers_{{ $summary["date_range"] }}'
            }
        ],
        language: {
            emptyTable: "<div class='py-4 text-center text-muted'><i class='fas fa-users-slash fa-2x mb-2 d-block text-muted opacity-50'></i>No customer records found in this date period.</div>"
        }
    });

    $('#topDishesTable').DataTable({
        paging: false,
        searching: false,
        ordering: false,
        info: false,
        dom: '<"d-flex justify-content-end mb-2"<"dt-buttons"B>>rt',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel text-success me-1"></i> Excel',
                className: 'dt-button',
                title: 'Top_10_Dishes_{{ $summary["date_range"] }}'
            }
        ],
        language: {
            emptyTable: "<div class='py-4 text-center text-muted'><i class='fas fa-utensils fa-2x mb-2 d-block text-muted opacity-50'></i>No dish sales recorded in this date period.</div>"
        }
    });

    $('#btnPrintReport').on('click', function() {
        window.print();
    });
});
</script>
@endsection