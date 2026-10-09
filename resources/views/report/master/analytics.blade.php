@extends('layouts.app')

@section('title')
<title>Master Report - Business Intelligence & Analytics</title>
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

        {{-- 1. DATE RANGE FILTER --}}
        <div class="mr-filter-card">
            <form method="GET" action="{{ route('admin.reports.master.analytics') }}" class="row g-3 align-items-end">
                @if($context['selectedOutletId'])
                    <input type="hidden" name="outlet_id" value="{{ $context['selectedOutletId'] }}">
                @endif

                <div class="col-12 col-sm-6 col-md-4">
                    <label class="mr-form-label"><i class="fas fa-calendar-alt text-primary me-1"></i> From Date</label>
                    <input type="date" name="from_date" class="form-control mr-input" value="{{ $fromDate->format('Y-m-d') }}">
                </div>

                <div class="col-12 col-sm-6 col-md-4">
                    <label class="mr-form-label"><i class="fas fa-calendar-alt text-primary me-1"></i> To Date</label>
                    <input type="date" name="to_date" class="form-control mr-input" value="{{ $toDate->format('Y-m-d') }}">
                </div>

                <div class="col-12 col-md-4 d-flex gap-2">
                    <button type="submit" class="mr-btn mr-btn-primary flex-grow-1 justify-content-center">
                        <i class="fas fa-chart-line"></i> Generate Insights
                    </button>
                    <a href="{{ route('admin.reports.master.analytics', $context['selectedOutletId'] ? ['outlet_id' => $context['selectedOutletId']] : []) }}" class="mr-btn mr-btn-outline" title="Reset Filters">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>
            </form>
        </div>

        {{-- 2. PROFIT & SALES DYNAMICS HIGH-LEVEL OVERVIEW --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="mr-kpi-card">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">Total Period Sales</span>
                        <div class="mr-kpi-icon" style="background: #ecfdf5; color: #059669;">
                            <i class="fas fa-rupee-sign"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value text-success">₹{{ number_format($totalSalesRevenue, 2) }}</div>
                    <div class="mr-kpi-footer">
                        <span>Gross customer billing</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="mr-kpi-card">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">Purchases Outlay</span>
                        <div class="mr-kpi-icon" style="background: #fffbeb; color: #d97706;">
                            <i class="fas fa-truck-loading"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value text-warning">₹{{ number_format($totalPurchasesAmount, 2) }}</div>
                    <div class="mr-kpi-footer">
                        <span>Inventory procurement cost</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="mr-kpi-card">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">Operating Expenses</span>
                        <div class="mr-kpi-icon" style="background: #fef2f2; color: #dc2626;">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value text-danger">₹{{ number_format($totalExpensesAmount, 2) }}</div>
                    <div class="mr-kpi-footer">
                        <span>Direct restaurant expenses</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="mr-kpi-card">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">Operating Margin</span>
                        <div class="mr-kpi-icon" style="background: {{ $grossMargin >= 0 ? '#eef2ff' : '#fef2f2' }}; color: {{ $grossMargin >= 0 ? '#4f46e5' : '#dc2626' }};">
                            <i class="fas fa-balance-scale"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value {{ $grossMargin >= 0 ? 'text-primary' : 'text-danger' }}">
                        ₹{{ number_format($grossMargin, 2) }}
                    </div>
                    <div class="mr-kpi-footer">
                        <span>Sales - (Purchases + Expenses)</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. PEAK DISCOVERIES CARDS (Peak Day & Peak Hour) --}}
        <div class="row g-3 mb-4">
            {{-- Peak Sales Day --}}
            <div class="col-12 col-md-6">
                <div class="mr-kpi-card bg-gradient" style="border-left: 5px solid #4f46e5;">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="mr-kpi-label text-primary"><i class="fas fa-crown me-1 text-warning"></i> Peak Performing Date</span>
                        <span class="badge bg-primary-subtle text-primary">Highest Revenue Day</span>
                    </div>
                    @if($peakSalesDay)
                        <div class="d-flex align-items-baseline gap-2 mb-1">
                            <h3 class="font-weight-bold text-dark mb-0">{{ \Carbon\Carbon::parse($peakSalesDay->order_date)->format('d M Y (l)') }}</h3>
                        </div>
                        <div class="d-flex gap-3 text-muted small mt-2">
                            <span><i class="fas fa-shopping-bag text-primary me-1"></i> <strong>{{ $peakSalesDay->order_count }}</strong> orders</span>
                            <span><i class="fas fa-rupee-sign text-success me-1"></i> <strong>₹{{ number_format($peakSalesDay->total_sales, 2) }}</strong> revenue</span>
                        </div>
                    @else
                        <p class="text-muted mb-0">No sales records in selected date range.</p>
                    @endif
                </div>
            </div>

            {{-- Peak Hour --}}
            <div class="col-12 col-md-6">
                <div class="mr-kpi-card bg-gradient" style="border-left: 5px solid #ff6a00;">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="mr-kpi-label text-warning"><i class="fas fa-fire me-1 text-danger"></i> Peak Hour of the Day</span>
                        <span class="badge bg-warning-subtle text-warning">Busiest Rush Hour</span>
                    </div>
                    @if($peakHour)
                        @php
                            $startHour = \Carbon\Carbon::createFromTime($peakHour->order_hour, 0)->format('h:i A');
                            $endHour = \Carbon\Carbon::createFromTime($peakHour->order_hour + 1, 0)->format('h:i A');
                        @endphp
                        <div class="d-flex align-items-baseline gap-2 mb-1">
                            <h3 class="font-weight-bold text-dark mb-0">{{ $startHour }} - {{ $endHour }}</h3>
                        </div>
                        <div class="d-flex gap-3 text-muted small mt-2">
                            <span><i class="fas fa-users text-warning me-1"></i> <strong>{{ $peakHour->order_count }}</strong> orders processed</span>
                            <span><i class="fas fa-coins text-success me-1"></i> <strong>₹{{ number_format($peakHour->total_sales, 2) }}</strong> revenue generated</span>
                        </div>
                    @else
                        <p class="text-muted mb-0">No order timing records in selected date range.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- 4. TRENDING DISHES & BEST CUSTOMERS (2-COLUMNS) --}}
        <div class="row g-4 mb-4">
            {{-- Top Trending Food Items --}}
            <div class="col-12 col-xl-6">
                <div class="mr-table-card h-100">
                    <div class="mr-table-header">
                        <h5 class="mr-table-title">
                            <i class="fas fa-fire text-danger"></i> Top Trending Food Items (Best Sellers)
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="exportTableToExcel('trendingDishesTable', 'Top_Trending_Dishes')">
                            <i class="fas fa-file-excel"></i> Export
                        </button>
                    </div>
                    <div class="table-responsive p-3">
                        <table class="table mr-table align-middle" id="trendingDishesTable">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Dish Name</th>
                                    <th>Category</th>
                                    <th>Units Sold</th>
                                    <th>Revenue (₹)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($trendingDishes as $index => $dish)
                                    <tr>
                                        <td>
                                            <span class="badge {{ $index == 0 ? 'bg-warning text-dark' : ($index == 1 ? 'bg-secondary' : ($index == 2 ? 'bg-bronze text-dark' : 'bg-light text-muted border')) }} font-weight-bold">
                                                #{{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold text-dark">{{ $dish->dish_name }}</div>
                                            @if($dish->food_type)
                                                <small class="badge bg-light text-muted border">{{ strtoupper($dish->food_type) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary">{{ $dish->category_name ?: 'General' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-dark px-2 py-1">{{ number_format($dish->total_qty) }}</span>
                                        </td>
                                        <td class="font-weight-bold text-success">
                                            ₹{{ number_format($dish->total_revenue, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No dish sales data found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Best VIP Customers --}}
            <div class="col-12 col-xl-6">
                <div class="mr-table-card h-100">
                    <div class="mr-table-header">
                        <h5 class="mr-table-title">
                            <i class="fas fa-trophy text-warning"></i> Best Customers (High Value VIPs)
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="exportTableToExcel('bestCustomersTable', 'Best_Customers_Report')">
                            <i class="fas fa-file-excel"></i> Export
                        </button>
                    </div>
                    <div class="table-responsive p-3">
                        <table class="table mr-table align-middle" id="bestCustomersTable">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Customer Name</th>
                                    <th>Phone</th>
                                    <th>Orders</th>
                                    <th>Total Spend (₹)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bestCustomers as $index => $cust)
                                    <tr>
                                        <td>
                                            <span class="badge {{ $index == 0 ? 'bg-warning text-dark' : ($index == 1 ? 'bg-secondary' : ($index == 2 ? 'bg-bronze text-dark' : 'bg-light text-muted border')) }} font-weight-bold">
                                                #{{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold text-dark">{{ $cust->customer_name }}</div>
                                            <small class="text-muted">Last visit: {{ $cust->last_visit ? \Carbon\Carbon::parse($cust->last_visit)->format('d M Y') : '-' }}</small>
                                        </td>
                                        <td>
                                            @if($cust->customer_phone)
                                                <span><i class="fas fa-phone-alt small text-muted me-1"></i>{{ $cust->customer_phone }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary">{{ $cust->total_orders }} orders</span>
                                        </td>
                                        <td class="font-weight-bold text-success">
                                            ₹{{ number_format($cust->total_spent, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">No customer purchase data found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. HOURLY BREAKDOWN & NON-SELLING ITEMS --}}
        <div class="row g-4 mb-4">
            {{-- Hourly Distribution --}}
            <div class="col-12 col-xl-6">
                <div class="mr-table-card h-100">
                    <div class="mr-table-header">
                        <h5 class="mr-table-title">
                            <i class="fas fa-clock text-info"></i> Sales & Orders by Hour of Day
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="exportTableToExcel('hourlyDistributionTable', 'Hourly_Sales_Distribution')">
                            <i class="fas fa-file-excel"></i> Export
                        </button>
                    </div>
                    <div class="table-responsive p-3" style="max-height: 420px; overflow-y: auto;">
                        <table class="table mr-table align-middle" id="hourlyDistributionTable">
                            <thead>
                                <tr>
                                    <th>Hour of Day</th>
                                    <th>Order Volume</th>
                                    <th>Sales Value (₹)</th>
                                    <th>Volume Share</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $maxHourlyOrders = $hourlyOrders->max('order_count') ?: 1;
                                @endphp
                                @for($h = 0; $h < 24; $h++)
                                    @php
                                        $hourData = $hourlyOrders->get($h);
                                        $cnt = $hourData ? $hourData->order_count : 0;
                                        $val = $hourData ? $hourData->total_sales : 0;
                                        $hourLabel = \Carbon\Carbon::createFromTime($h, 0)->format('h:00 A') . ' - ' . \Carbon\Carbon::createFromTime($h + 1, 0)->format('h:00 A');
                                        $isPeak = ($peakHour && $peakHour->order_hour == $h && $cnt > 0);
                                    @endphp
                                    @if($cnt > 0 || ($h >= 9 && $h <= 23))
                                        <tr class="{{ $isPeak ? 'table-warning font-weight-bold' : '' }}">
                                            <td>
                                                <span>{{ $hourLabel }}</span>
                                                @if($isPeak)
                                                    <span class="badge bg-danger ms-1">Peak Hour</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $cnt > 0 ? 'bg-dark' : 'bg-light text-muted' }} px-2 py-1">
                                                    {{ $cnt }} orders
                                                </span>
                                            </td>
                                            <td class="font-weight-bold {{ $val > 0 ? 'text-dark' : 'text-muted' }}">
                                                ₹{{ number_format($val, 2) }}
                                            </td>
                                            <td>
                                                <div class="progress" style="height: 6px; width: 100px;">
                                                    <div class="progress-bar {{ $isPeak ? 'bg-danger' : 'bg-primary' }}" role="progressbar" style="width: {{ ($cnt / $maxHourlyOrders) * 100 }}%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Non-Selling Food Items (0 Orders in Period) --}}
            <div class="col-12 col-xl-6">
                <div class="mr-table-card h-100">
                    <div class="mr-table-header">
                        <h5 class="mr-table-title">
                            <i class="fas fa-ban text-danger"></i> Non-Selling Items (Zero Sales in Period)
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="exportTableToExcel('nonSellingDishesTable', 'Non_Selling_Dishes')">
                            <i class="fas fa-file-excel"></i> Export
                        </button>
                    </div>
                    <div class="table-responsive p-3" style="max-height: 420px; overflow-y: auto;">
                        <table class="table mr-table align-middle" id="nonSellingDishesTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Dish Name</th>
                                    <th>Category</th>
                                    <th>Menu Price (₹)</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($nonSellingDishes as $index => $dish)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="font-weight-bold text-dark">{{ $dish->name }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $dish->category ? $dish->category->name : 'General' }}</span>
                                        </td>
                                        <td class="font-weight-bold text-dark">
                                            ₹{{ number_format($dish->price, 2) }}
                                        </td>
                                        <td>
                                            <span class="badge bg-danger-subtle text-danger">0 Orders</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-success font-weight-bold">
                                            <i class="fas fa-check-circle fa-2x mb-2 d-block"></i>
                                            All active menu dishes recorded sales in this period!
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

</div>
</div>

@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@include('includes.script')
<script>
function exportActiveReportToExcel() {
    exportTableToExcel('trendingDishesTable', 'Master_BI_Analytics_{{ $fromDate->format("Ymd") }}_to_{{ $toDate->format("Ymd") }}');
}
</script>
@endsection
