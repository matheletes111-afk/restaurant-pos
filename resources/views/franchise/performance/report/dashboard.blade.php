@extends('layouts.app')

@section('title')
<title>Performance Dashboard - {{ $restaurant->name }}</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
.chart-container-card {
    background: #ffffff;
    border-radius: var(--mr-radius, 16px);
    border: 1px solid var(--mr-slate-200, #e2e8f0);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    padding: 24px;
    height: 100%;
    display: flex;
    flex-direction: column;
    transition: all 0.25s ease;
}

.chart-container-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 25px -4px rgba(0, 0, 0, 0.08);
    border-color: #cbd5e1;
}

.chart-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 1px solid #f1f5f9;
}

.chart-title {
    font-family: 'Outfit', sans-serif;
    font-size: 1.1rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.chart-subtitle {
    font-size: 0.78rem;
    color: #64748b;
    margin-top: 2px;
}

.chart-canvas-wrap {
    position: relative;
    flex-grow: 1;
    min-height: 280px;
    width: 100%;
}

.rank-badge {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.82rem;
}

.rank-1 { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
.rank-2 { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
.rank-3 { background: #ffedd5; color: #c2410c; border: 1px solid #fed7aa; }
.rank-other { background: #f8fafc; color: #94a3b8; border: 1px solid #f1f5f9; }

.dish-progress-bar {
    height: 6px;
    background: #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
    margin-top: 6px;
}

.dish-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #ff6a00 0%, #ff8c42 100%);
    border-radius: 10px;
}
</style>
@endsection

@section('body')
@include('includes.sidebar')

<div class="pc-container">
<div class="pc-content">

    <div class="mr-wrap">
        {{-- Flash Messages --}}
        @include('includes.message')

        {{-- Master Header & Nav --}}
        @include('franchise.performance.report.nav')

        {{-- 1. EXECUTIVE KPI HIGHLIGHTS ROW --}}
        <div class="row g-3 mb-4">
            {{-- Total Sales Sum Till Now --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="mr-kpi-card" style="border-left: 4px solid var(--mr-success, #10b981);">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">Total Sales Sum Till Now</span>
                        <div class="mr-kpi-icon" style="background: #ecfdf5; color: #10b981;">
                            <i class="fas fa-coins"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value text-success">
                        ₹{{ number_format($totalSalesSum, 2) }}
                    </div>
                    <div class="mr-kpi-footer">
                        <i class="fas fa-receipt text-success"></i>
                        <span><strong>{{ number_format($totalOrdersCount) }}</strong> all-time completed orders</span>
                    </div>
                </div>
            </div>

            {{-- Today's Order Value --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="mr-kpi-card" style="border-left: 4px solid var(--mr-primary, #4f46e5);">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">Today's Order Value</span>
                        <div class="mr-kpi-icon" style="background: #eef2ff; color: #4f46e5;">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value text-primary">
                        ₹{{ number_format($todayOrderValue, 2) }}
                    </div>
                    <div class="mr-kpi-footer">
                        <i class="fas fa-chart-line text-primary"></i>
                        <span><strong>{{ number_format($todayOrdersCount) }}</strong> orders today ({{ number_format($todayItemsQty) }} items)</span>
                    </div>
                </div>
            </div>

            {{-- Average Order Value (AOV) --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="mr-kpi-card" style="border-left: 4px solid var(--mr-warning, #f59e0b);">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">Average Order Value</span>
                        <div class="mr-kpi-icon" style="background: #fffbeb; color: #f59e0b;">
                            <i class="fas fa-calculator"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value text-warning">
                        ₹{{ number_format($avgOrderValue, 2) }}
                    </div>
                    <div class="mr-kpi-footer">
                        <i class="fas fa-balance-scale text-warning"></i>
                        <span>Average spending per invoice</span>
                    </div>
                </div>
            </div>

            {{-- Current Month Sales --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="mr-kpi-card" style="border-left: 4px solid var(--mr-accent, #ff6a00);">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">This Month Billing</span>
                        <div class="mr-kpi-icon" style="background: #fff3eb; color: #ff6a00;">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value" style="color: #ff6a00;">
                        ₹{{ number_format($monthOrderValue, 2) }}
                    </div>
                    <div class="mr-kpi-footer">
                        <i class="fas fa-shopping-bag text-danger"></i>
                        <span><strong>{{ number_format($monthOrdersCount) }}</strong> orders recorded this month</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. FINANCIAL REVENUE HEALTH STRIP --}}
        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="p-3 bg-white rounded-4 border shadow-sm d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white" style="width: 42px; height: 42px; background: #0f172a;">
                            <i class="fas fa-wallet fs-5"></i>
                        </div>
                        <div>
                            <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem;">Collected Revenue</div>
                            <div class="fw-bold text-dark fs-5">₹{{ number_format($totalCollected, 2) }}</div>
                        </div>
                    </div>

                    <div class="vr d-none d-md-block" style="height: 36px;"></div>

                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white" style="width: 42px; height: 42px; background: #ef4444;">
                            <i class="fas fa-hand-holding-usd fs-5"></i>
                        </div>
                        <div>
                            <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem;">Outstanding Due</div>
                            <div class="fw-bold text-danger fs-5">₹{{ number_format($totalDue, 2) }}</div>
                        </div>
                    </div>

                    <div class="vr d-none d-md-block" style="height: 36px;"></div>

                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white" style="width: 42px; height: 42px; background: #4f46e5;">
                            <i class="fas fa-utensils fs-5"></i>
                        </div>
                        <div>
                            <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem;">Live Menu Dishes</div>
                            <div class="fw-bold text-primary fs-5">{{ number_format($totalDishes) }} Active Dishes</div>
                        </div>
                    </div>

                    <div class="vr d-none d-md-block" style="height: 36px;"></div>

                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white" style="width: 42px; height: 42px; background: #06b6d4;">
                            <i class="fas fa-layer-group fs-5"></i>
                        </div>
                        <div>
                            <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem;">Food Categories</div>
                            <div class="fw-bold text-info fs-5">{{ number_format($totalCategories) }} Categories</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. TRENDING GRAPHS (LAST 14 DAYS & PEAK HOURLY RUSH) --}}
        <div class="row g-4 mb-4">
            {{-- Left: 14-Day Sales Velocity & Orders Growth Curve --}}
            <div class="col-12 col-xl-8">
                <div class="chart-container-card">
                    <div class="chart-header">
                        <div>
                            <h4 class="chart-title"><i class="fas fa-chart-line text-primary"></i> 14-Day Sales & Order Velocity Trend</h4>
                            <div class="chart-subtitle">Daily sales revenue (₹) paired with completed order quantity</div>
                        </div>
                        <span class="badge bg-primary-subtle text-primary border px-3 py-1 fw-bold rounded-pill">
                            Last 14 Days
                        </span>
                    </div>
                    <div class="chart-canvas-wrap" style="height: 320px;">
                        <canvas id="salesVelocityChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Right: Peak Rush Hours Curve --}}
            <div class="col-12 col-xl-4">
                <div class="chart-container-card">
                    <div class="chart-header">
                        <div>
                            <h4 class="chart-title"><i class="fas fa-clock text-warning"></i> Peak Rush Hours</h4>
                            <div class="chart-subtitle">24-hour order concentration curve</div>
                        </div>
                        <span class="badge bg-warning-subtle text-warning border px-2 py-1 small rounded-pill">
                            24 Hours
                        </span>
                    </div>
                    <div class="chart-canvas-wrap" style="height: 320px;">
                        <canvas id="hourlyRushChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. DISTRIBUTION PIE & DONUT CHARTS (3 COLUMNS) --}}
        <div class="row g-4 mb-4">
            {{-- Payment Methods Distribution Donut --}}
            <div class="col-12 col-md-6 col-xl-4">
                <div class="chart-container-card">
                    <div class="chart-header">
                        <div>
                            <h4 class="chart-title"><i class="fas fa-credit-card text-success"></i> Payment Methods</h4>
                            <div class="chart-subtitle">Collection breakdown by mode</div>
                        </div>
                    </div>
                    <div class="chart-canvas-wrap" style="height: 250px;">
                        <canvas id="paymentMethodsChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Order Types Distribution Pie --}}
            <div class="col-12 col-md-6 col-xl-4">
                <div class="chart-container-card">
                    <div class="chart-header">
                        <div>
                            <h4 class="chart-title"><i class="fas fa-concierge-bell text-primary"></i> Order Types</h4>
                            <div class="chart-subtitle">Dine-in vs Takeaway vs Delivery</div>
                        </div>
                    </div>
                    <div class="chart-canvas-wrap" style="height: 250px;">
                        <canvas id="orderTypesChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Category Share Donut --}}
            <div class="col-12 col-md-12 col-xl-4">
                <div class="chart-container-card">
                    <div class="chart-header">
                        <div>
                            <h4 class="chart-title"><i class="fas fa-tags text-info"></i> Category Contribution</h4>
                            <div class="chart-subtitle">Top revenue-generating categories</div>
                        </div>
                    </div>
                    <div class="chart-canvas-wrap" style="height: 250px;">
                        <canvas id="categoryShareChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. TOP 10 BEST DISHES DIRECTORY --}}
        <div class="mr-card mb-4">
            <div class="mr-card-header d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mr-card-title"><i class="fas fa-trophy text-warning me-2"></i>Top 10 Best Selling Dishes</h3>
                    <div class="mr-card-subtitle">Highest grossing and top ordered menu items for {{ $restaurant->name }}</div>
                </div>
                <span class="badge bg-warning-subtle text-warning border px-3 py-2 rounded-pill fw-bold">
                    <i class="fas fa-crown me-1"></i> Top 10 Bestsellers
                </span>
            </div>

            <div class="mr-card-body p-0">
                <div class="table-responsive">
                    <table class="table mr-table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 60px;" class="text-center">Rank</th>
                                <th>Dish Name & Category</th>
                                <th class="text-center">Food Type</th>
                                <th class="text-end">Base Price</th>
                                <th class="text-center">Total Quantity Sold</th>
                                <th class="text-end">Total Revenue Generated</th>
                                <th style="width: 180px;">Volume Share</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $maxQty = $topDishes->max('total_qty') ?: 1;
                            @endphp
                            @forelse($topDishes as $idx => $dish)
                                <tr>
                                    <td class="text-center">
                                        <span class="rank-badge {{ $idx === 0 ? 'rank-1' : ($idx === 1 ? 'rank-2' : ($idx === 2 ? 'rank-3' : 'rank-other')) }}">
                                            #{{ $idx + 1 }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            @if($dish->dish_image)
                                                <img src="{{ asset('storage/' . $dish->dish_image) }}" alt="{{ $dish->dish_name }}" class="rounded-3" style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="rounded-3 d-flex align-items-center justify-content-center text-white fw-bold" style="width: 40px; height: 40px; background: linear-gradient(135deg, #ff6a00 0%, #ff8c42 100%);">
                                                    {{ strtoupper(substr($dish->dish_name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold text-dark fs-6">{{ $dish->dish_name }}</div>
                                                <span class="badge bg-light text-muted border px-2 py-0 small">{{ $dish->category_name ?: 'General' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if(strtolower($dish->food_type ?? '') === 'veg')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fas fa-leaf me-1"></i> Veg</span>
                                        @elseif(strtolower($dish->food_type ?? '') === 'non-veg')
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="fas fa-drumstick-bite me-1"></i> Non-Veg</span>
                                        @else
                                            <span class="badge bg-light text-muted border px-2 py-1">Standard</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-semibold text-muted">
                                        ₹{{ number_format($dish->base_price, 2) }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill" style="background: #eef2ff; color: #4f46e5; font-size: 13px; font-weight: 800; padding: 6px 14px;">
                                            {{ number_format($dish->total_qty) }} units
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-success fs-6">
                                        ₹{{ number_format($dish->total_revenue, 2) }}
                                    </td>
                                    <td>
                                        @php
                                            $pct = round(($dish->total_qty / $maxQty) * 100);
                                        @endphp
                                        <div class="d-flex align-items-center justify-content-between small text-muted">
                                            <span>{{ $pct }}%</span>
                                            <span>{{ $dish->total_orders_count }} orders</span>
                                        </div>
                                        <div class="dish-progress-bar">
                                            <div class="dish-progress-fill" style="width: {{ $pct }}%;"></div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-utensils fa-2x mb-2 d-block"></i>
                                        No dish orders found for this restaurant yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- 6. RECENT 5 LIVE TRANSACTIONS FEED --}}
        <div class="mr-card mb-4">
            <div class="mr-card-header d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mr-card-title"><i class="fas fa-receipt text-primary me-2"></i>Recent Transactions Feed</h3>
                    <div class="mr-card-subtitle">Last 5 completed transactions for {{ $restaurant->name }}</div>
                </div>
                <a href="{{ route('franchise.performance.report.orders', ['restaurantId' => $restaurant->id]) }}" class="btn btn-outline-primary btn-sm px-3 rounded-pill fw-bold">
                    View All Orders <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>

            <div class="mr-card-body p-0">
                <div class="table-responsive">
                    <table class="table mr-table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Date & Time</th>
                                <th>Customer / Table</th>
                                <th>Order Type</th>
                                <th class="text-end">Invoice Amount</th>
                                <th class="text-center">Payment Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $ro)
                                <tr>
                                    <td>
                                        <span class="fw-bold text-primary">#{{ $ro->id }}</span>
                                    </td>
                                    <td>
                                        <div class="text-dark fw-semibold">{{ $ro->created_at ? $ro->created_at->format('d M Y') : '-' }}</div>
                                        <div class="text-muted small">{{ $ro->created_at ? $ro->created_at->format('h:i A') : '' }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $ro->customer_name ?: 'Walk-in' }}</div>
                                        @if($ro->table)
                                            <span class="badge bg-light text-muted border small">{{ $ro->table->table_name }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">{{ $ro->order_type ?: 'Dine-In' }}</span>
                                    </td>
                                    <td class="text-end fw-bold text-dark">
                                        ₹{{ number_format($ro->grand_total, 2) }}
                                    </td>
                                    <td class="text-center">
                                        @if($ro->payment_status === 'PAID')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1">PAID</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning border px-3 py-1">{{ $ro->payment_status ?: 'PENDING' }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        No recent orders found.
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

@endsection

@section('script')
@include('includes.script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // 1. Sales & Orders Dual Axis Area/Line Chart
    const salesCanvas = document.getElementById('salesVelocityChart');
    if (salesCanvas) {
        new Chart(salesCanvas.getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode($chartDays) !!},
                datasets: [
                    {
                        label: 'Daily Sales (₹)',
                        data: {!! json_encode($chartSales) !!},
                        borderColor: '#ff6a00',
                        backgroundColor: 'rgba(255, 106, 0, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.35,
                        yAxisID: 'ySales',
                        pointBackgroundColor: '#ff6a00',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Daily Orders (Qty)',
                        data: {!! json_encode($chartOrders) !!},
                        borderColor: '#4f46e5',
                        backgroundColor: 'transparent',
                        borderWidth: 2.5,
                        borderDash: [5, 5],
                        yAxisID: 'yOrders',
                        pointBackgroundColor: '#4f46e5',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            font: { family: 'Plus Jakarta Sans', weight: '600', size: 12 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                if (ctx.dataset.yAxisID === 'ySales') {
                                    return ' ' + ctx.dataset.label + ': ₹' + Number(ctx.parsed.y).toLocaleString();
                                }
                                return ' ' + ctx.dataset.label + ': ' + ctx.parsed.y + ' orders';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Plus Jakarta Sans', size: 11 } }
                    },
                    ySales: {
                        type: 'linear',
                        position: 'left',
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: value => '₹' + Number(value).toLocaleString()
                        }
                    },
                    yOrders: {
                        type: 'linear',
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    }

    // 2. Peak Rush Hours Bar Chart
    const rushCanvas = document.getElementById('hourlyRushChart');
    if (rushCanvas) {
        new Chart(rushCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($hourlyLabels) !!},
                datasets: [{
                    label: 'Orders',
                    data: {!! json_encode($hourlyValues) !!},
                    backgroundColor: 'rgba(245, 158, 11, 0.75)',
                    borderRadius: 4,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 9 },
                            maxRotation: 0,
                            callback: function(val, index) {
                                return index % 3 === 0 ? this.getLabelForValue(val) : '';
                            }
                        }
                    },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    }

    // 3. Payment Methods Donut Chart
    const payCanvas = document.getElementById('paymentMethodsChart');
    if (payCanvas) {
        const payLabels = {!! json_encode($paymentMethods->pluck('payment_method')->toArray()) !!};
        const payData = {!! json_encode($paymentMethods->pluck('count')->toArray()) !!};
        const colors = ['#10b981', '#3b82f6', '#f59e0b', '#6366f1', '#ec4899', '#8b5cf6'];

        new Chart(payCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: payLabels.length ? payLabels : ['No Data'],
                datasets: [{
                    data: payData.length ? payData : [1],
                    backgroundColor: payLabels.length ? colors.slice(0, payLabels.length) : ['#e2e8f0'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { size: 11 } }
                    }
                },
                cutout: '68%'
            }
        });
    }

    // 4. Order Types Pie Chart
    const typeCanvas = document.getElementById('orderTypesChart');
    if (typeCanvas) {
        const typeLabels = {!! json_encode($orderTypes->pluck('order_type')->toArray()) !!};
        const typeData = {!! json_encode($orderTypes->pluck('count')->toArray()) !!};
        const typeColors = ['#ff6a00', '#06b6d4', '#8b5cf6', '#10b981', '#f59e0b'];

        new Chart(typeCanvas.getContext('2d'), {
            type: 'pie',
            data: {
                labels: typeLabels.length ? typeLabels : ['No Data'],
                datasets: [{
                    data: typeData.length ? typeData : [1],
                    backgroundColor: typeLabels.length ? typeColors.slice(0, typeLabels.length) : ['#e2e8f0'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { size: 11 } }
                    }
                }
            }
        });
    }

    // 5. Category Share Donut Chart
    const catCanvas = document.getElementById('categoryShareChart');
    if (catCanvas) {
        const catLabels = {!! json_encode($categoryShare->pluck('category_name')->toArray()) !!};
        const catData = {!! json_encode($categoryShare->pluck('total_amount')->map(fn($v) => round((float)$v, 2))->toArray()) !!};
        const catColors = ['#4f46e5', '#ff6a00', '#10b981', '#06b6d4', '#f59e0b'];

        new Chart(catCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: catLabels.length ? catLabels : ['No Data'],
                datasets: [{
                    data: catData.length ? catData : [1],
                    backgroundColor: catLabels.length ? catColors.slice(0, catLabels.length) : ['#e2e8f0'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { size: 11 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                return ' ' + ctx.label + ': ₹' + Number(ctx.parsed).toLocaleString();
                            }
                        }
                    }
                },
                cutout: '68%'
            }
        });
    }
});
</script>
@endsection
