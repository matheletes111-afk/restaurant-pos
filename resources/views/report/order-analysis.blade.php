@extends('layouts.app')

@section('title')
<title>Order Analysis Dashboard - Admin</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="{{ asset('admin_template/css/report-analytics.css') }}">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  .table-counters td {
    padding: 10px 14px;
    border-bottom: 1px solid var(--rpt-border-light);
    vertical-align: middle;
  }
  .table-counters tr:last-child td {
    border-bottom: none;
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
                <div class="rpt-header-icon icon-analysis">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="rpt-header-title-meta">
                    <span class="rpt-header-eyebrow">Business Intelligence & Trends</span>
                    <h1 class="rpt-header-title">Order Analysis Dashboard</h1>
                    <p class="rpt-header-sub">Operational insights into sales channels, payment modes, food preferences, and hourly traffic</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" id="btnPrintReport" class="btn-rpt-secondary">
                    <i class="fas fa-print text-primary"></i> Print Analytics
                </button>
            </div>
        </div>

        {{-- 2. Filter Form --}}
        <div class="rpt-filter-card">
            <form method="GET" action="{{ route('order.report.analysis') }}" class="row g-3 align-items-end">
                <div class="col-md-4 col-sm-6">
                    <label class="rpt-filter-label">From Date</label>
                    <input type="date" name="from_date" value="{{ request('from_date') ?? \Carbon\Carbon::now()->subDays(7)->format('Y-m-d') }}" class="rpt-filter-control">
                </div>
                <div class="col-md-4 col-sm-6">
                    <label class="rpt-filter-label">To Date</label>
                    <input type="date" name="to_date" value="{{ request('to_date') ?? \Carbon\Carbon::now()->format('Y-m-d') }}" class="rpt-filter-control">
                </div>
                <div class="col-md-4 col-sm-12 d-flex gap-2">
                    <button type="submit" class="btn-rpt-primary flex-fill">
                        <i class="fas fa-filter"></i> Run Analysis
                    </button>
                    <a href="{{ route('order.report.analysis') }}" class="btn-rpt-secondary" title="Reset Filters">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>

        {{-- 3. Summary Stats Grid --}}
        <div class="rpt-stats-grid">
            <div class="rpt-stat-card stat-primary">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Total Realized Revenue</span>
                    <span class="rpt-stat-val">₹{{ number_format($totalAmount, 2) }}</span>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-indian-rupee-sign"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-success">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Total Completed Orders</span>
                    <span class="rpt-stat-val">{{ $totalOrders }}</span>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-bag-shopping"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-warning">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Average Order Value</span>
                    <span class="rpt-stat-val">₹{{ number_format($avgOrderValue, 2) }}</span>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-scale-balanced"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-purple">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Peak Order Date</span>
                    <span class="rpt-stat-val" style="font-size: 1.35rem;">
                        @if($peakDay)
                            {{ \Carbon\Carbon::parse($peakDay->order_date)->format('d M Y') }}
                        @else
                            N/A
                        @endif
                    </span>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
        </div>

        {{-- 4. Main Two Column Analytics Grid --}}
        <div class="row">
            {{-- Left Column: Channels, Payment Status, Daily Trend --}}
            <div class="col-lg-8 mb-4">
                {{-- Order Type Channel Distribution --}}
                <div class="rpt-chart-box">
                    <div class="rpt-chart-title">
                        <div class="title-left">
                            <i class="fas fa-store text-primary"></i>
                            <span>Order Channel Distribution</span>
                        </div>
                    </div>
                    @php
                        $dineIn = $orderTypeCounts['DINE_IN'] ?? (object)['count' => 0, 'total_amount' => 0];
                        $takeaway = $orderTypeCounts['TAKEAWAY'] ?? (object)['count' => 0, 'total_amount' => 0];
                        $totalTypeOrders = $dineIn->count + $takeaway->count;
                        $dineInPct = $totalTypeOrders > 0 ? ($dineIn->count / $totalTypeOrders) * 100 : 0;
                        $takeawayPct = $totalTypeOrders > 0 ? ($takeaway->count / $totalTypeOrders) * 100 : 0;
                    @endphp
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="rpt-counter-card" style="background: var(--rpt-info-bg); border: 1px solid var(--rpt-info-border);">
                                <span class="rpt-counter-lbl" style="color: var(--rpt-info-text);"><i class="fas fa-utensils me-1"></i> Dine-In Orders</span>
                                <span class="rpt-counter-val text-primary mt-1">{{ $dineIn->count }} <small class="fs-6 text-muted fw-bold">({{ number_format($dineInPct, 1) }}%)</small></span>
                                <span class="rpt-counter-sub text-dark">₹{{ number_format($dineIn->total_amount ?? 0, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="rpt-counter-card" style="background: var(--rpt-success-bg); border: 1px solid var(--rpt-success-border);">
                                <span class="rpt-counter-lbl" style="color: var(--rpt-success-text);"><i class="fas fa-box me-1"></i> Takeaway Orders</span>
                                <span class="rpt-counter-val text-success mt-1">{{ $takeaway->count }} <small class="fs-6 text-muted fw-bold">({{ number_format($takeawayPct, 1) }}%)</small></span>
                                <span class="rpt-counter-sub text-dark">₹{{ number_format($takeaway->total_amount ?? 0, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    @if($totalTypeOrders > 0)
                    <div class="rpt-progress-bar-wrap">
                        <div class="d-flex justify-content-between mb-1 small fw-bold text-muted">
                            <span><i class="fas fa-circle text-primary me-1" style="font-size: 8px;"></i> Dine-In ({{ number_format($dineInPct, 1) }}%)</span>
                            <span>Takeaway ({{ number_format($takeawayPct, 1) }}%) <i class="fas fa-circle text-success ms-1" style="font-size: 8px;"></i></span>
                        </div>
                        <div class="rpt-progress-bar-track">
                            <div class="rpt-progress-bar-fill bg-primary" style="width: {{ $dineInPct }}%;"></div>
                            <div class="rpt-progress-bar-fill bg-success" style="width: {{ $takeawayPct }}%;"></div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Payment Status Audit --}}
                <div class="rpt-chart-box">
                    <div class="rpt-chart-title">
                        <div class="title-left">
                            <i class="fas fa-wallet text-success"></i>
                            <span>Payment Status Realization</span>
                        </div>
                    </div>
                    @php
                        $paid = $paymentStatusCounts['PAID'] ?? (object)['count' => 0, 'total_amount' => 0];
                        $misc = $paymentStatusCounts['MISCORDER'] ?? (object)['count' => 0, 'total_amount' => 0];
                    @endphp
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="rpt-counter-card" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                                <span class="rpt-counter-lbl text-success"><i class="fas fa-check-circle me-1"></i> Settled (PAID)</span>
                                <span class="rpt-counter-val text-success mt-1">{{ $paid->count }}</span>
                                <span class="rpt-counter-sub text-dark">₹{{ number_format($paid->total_amount ?? 0, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="rpt-counter-card" style="background: #fef2f2; border: 1px solid #fecaca;">
                                <span class="rpt-counter-lbl text-danger"><i class="fas fa-exclamation-triangle me-1"></i> Miscellaneous (MISCORDER)</span>
                                <span class="rpt-counter-val text-danger mt-1">{{ $misc->count }}</span>
                                <span class="rpt-counter-sub text-dark">₹{{ number_format($misc->total_amount ?? 0, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Daily Order & Revenue Curve --}}
                @if($dailyTrend->count() > 0)
                <div class="rpt-chart-box">
                    <div class="rpt-chart-title">
                        <div class="title-left">
                            <i class="fas fa-chart-area text-warning"></i>
                            <span>Daily Order & Revenue Trajectory</span>
                        </div>
                    </div>
                    <div style="height: 320px; position: relative;">
                        <canvas id="dailyTrendChart"></canvas>
                    </div>
                </div>
                @endif
            </div>

            {{-- Right Column: Payment Gateways, Food Types, Peak Day, Rush Hours --}}
            <div class="col-lg-4 mb-4">
                {{-- Payment Methods List --}}
                <div class="rpt-chart-box">
                    <div class="rpt-chart-title">
                        <div class="title-left">
                            <i class="fas fa-credit-card text-info"></i>
                            <span>Payment Gateways & Modes</span>
                        </div>
                    </div>
                    @if(count($paymentMethods) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-counters w-100">
                                <tbody>
                                @foreach($paymentMethods as $method)
                                    @php
                                        $methodData = $paymentMethodCounts[$method] ?? (object)['count' => 0, 'total_amount' => 0];
                                        $methodLower = strtolower($method ?? '');
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if(str_contains($methodLower, 'cash'))
                                                    <span class="badge bg-success-subtle text-success p-2 rounded-circle"><i class="fas fa-money-bill-wave"></i></span>
                                                @elseif(str_contains($methodLower, 'upi') || str_contains($methodLower, 'qr'))
                                                    <span class="badge bg-primary-subtle text-primary p-2 rounded-circle"><i class="fas fa-qrcode"></i></span>
                                                @elseif(str_contains($methodLower, 'card'))
                                                    <span class="badge bg-info-subtle text-info p-2 rounded-circle"><i class="fas fa-credit-card"></i></span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary p-2 rounded-circle"><i class="fas fa-wallet"></i></span>
                                                @endif
                                                <strong class="text-dark">{{ $method ?? 'Unknown' }}</strong>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border px-2 py-1">{{ $methodData->count }}</span>
                                        </td>
                                        <td class="text-end fw-bold text-dark">
                                            ₹{{ number_format($methodData->total_amount ?? 0, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="fas fa-credit-card fa-2x mb-2 opacity-50"></i>
                            <p class="small mb-0">No payment methods recorded in this range.</p>
                        </div>
                    @endif
                </div>

                {{-- Food Type (Veg / Non-Veg) --}}
                <div class="rpt-chart-box">
                    <div class="rpt-chart-title">
                        <div class="title-left">
                            <i class="fas fa-seedling text-success"></i>
                            <span>Food Type Preference</span>
                        </div>
                    </div>
                    @php
                        $vegCount = 0;
                        $nonVegCount = 0;
                        $vegItemCount = 0;
                        $nonVegItemCount = 0;
                        
                        foreach($vegNonVegCounts as $type => $data) {
                            $typeLower = strtolower($type);
                            if(str_contains($typeLower, 'veg') && !str_contains($typeLower, 'non')) {
                                $vegCount = $data->order_count ?? 0;
                                $vegItemCount = $data->item_count ?? 0;
                            } elseif(str_contains($typeLower, 'non') || str_contains($typeLower, 'non-veg')) {
                                $nonVegCount = $data->order_count ?? 0;
                                $nonVegItemCount = $data->item_count ?? 0;
                            }
                        }
                        
                        $totalVegNonVeg = $vegCount + $nonVegCount;
                        $vegPct = $totalVegNonVeg > 0 ? ($vegCount / $totalVegNonVeg) * 100 : 0;
                        $nonVegPct = $totalVegNonVeg > 0 ? ($nonVegCount / $totalVegNonVeg) * 100 : 0;
                    @endphp
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="p-3 rounded-3" style="background: var(--rpt-success-bg); border: 1px solid var(--rpt-success-border);">
                                <span class="food-type-icon veg mb-1"><i class="fas fa-circle"></i></span>
                                <div class="rpt-counter-val text-success">{{ $vegCount }}</div>
                                <div class="rpt-counter-lbl" style="color: var(--rpt-success-text);">Veg Orders</div>
                                <small class="text-muted fw-semibold">{{ $vegItemCount }} items</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-3" style="background: #fef2f2; border: 1px solid #fecaca;">
                                <span class="food-type-icon non-veg mb-1"><i class="fas fa-circle"></i></span>
                                <div class="rpt-counter-val text-danger">{{ $nonVegCount }}</div>
                                <div class="rpt-counter-lbl" style="color: #b91c1c;">Non-Veg Orders</div>
                                <small class="text-muted fw-semibold">{{ $nonVegItemCount }} items</small>
                            </div>
                        </div>
                    </div>
                    @if($totalVegNonVeg > 0)
                    <div class="rpt-progress-bar-wrap">
                        <div class="d-flex justify-content-between mb-1 small fw-bold text-muted">
                            <span>Veg: {{ number_format($vegPct, 1) }}%</span>
                            <span>Non-Veg: {{ number_format($nonVegPct, 1) }}%</span>
                        </div>
                        <div class="rpt-progress-bar-track">
                            <div class="rpt-progress-bar-fill bg-success" style="width: {{ $vegPct }}%;"></div>
                            <div class="rpt-progress-bar-fill bg-danger" style="width: {{ $nonVegPct }}%;"></div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Peak Day Highlight --}}
                @if($peakDay)
                <div class="rpt-peak-card">
                    <span class="badge bg-white text-dark fw-bold mb-2 px-3 py-1 text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Busiest Day Highlight</span>
                    <div class="rpt-peak-date">{{ \Carbon\Carbon::parse($peakDay->order_date)->format('d M Y') }}</div>
                    <div class="rpt-peak-metric">{{ $peakDay->order_count }} Orders Completed</div>
                    <div class="fs-5 fw-extrabold text-white mt-1">₹{{ number_format($peakDay->total_amount, 2) }} Revenue</div>
                </div>
                @endif

                {{-- Busiest Hours Bar Chart --}}
                @if($hourlyDistribution->count() > 0)
                <div class="rpt-chart-box">
                    <div class="rpt-chart-title">
                        <div class="title-left">
                            <i class="fas fa-clock text-primary"></i>
                            <span>Hourly Rush Peak</span>
                        </div>
                    </div>
                    <div style="height: 220px; position: relative;">
                        <canvas id="hourlyChart"></canvas>
                    </div>
                </div>
                @endif
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
$(document).ready(function() {
    $('#btnPrintReport').on('click', function() {
        window.print();
    });

    @if($dailyTrend->count() > 0)
    const dailyCtx = document.getElementById('dailyTrendChart').getContext('2d');
    new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: [
                @foreach($dailyTrend as $day)
                    "{{ \Carbon\Carbon::parse($day->order_date)->format('d M') }}",
                @endforeach
            ],
            datasets: [
                {
                    label: 'Revenue (₹)',
                    data: [
                        @foreach($dailyTrend as $day)
                            {{ $day->total_amount }},
                        @endforeach
                    ],
                    borderColor: '#ff5e14',
                    backgroundColor: 'rgba(255, 94, 20, 0.08)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    yAxisID: 'y1'
                },
                {
                    label: 'Orders Count',
                    data: [
                        @foreach($dailyTrend as $day)
                            {{ $day->order_count }},
                        @endforeach
                    ],
                    borderColor: '#0284c7',
                    backgroundColor: 'rgba(2, 132, 199, 0.08)',
                    borderWidth: 2.5,
                    fill: false,
                    tension: 0.35,
                    yAxisID: 'y'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: {
                            family: "'Outfit', sans-serif",
                            weight: '600'
                        }
                    }
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    },
                    title: {
                        display: true,
                        text: 'Orders Count',
                        font: { weight: 'bold' }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false,
                    },
                    title: {
                        display: true,
                        text: 'Revenue (₹)',
                        font: { weight: 'bold' }
                    }
                }
            }
        }
    });
    @endif

    @if($hourlyDistribution->count() > 0)
    const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
    new Chart(hourlyCtx, {
        type: 'bar',
        data: {
            labels: [
                @foreach($hourlyDistribution as $hour)
                    "{{ sprintf('%02d:00', $hour->order_hour) }}",
                @endforeach
            ],
            datasets: [{
                label: 'Orders',
                data: [
                    @foreach($hourlyDistribution as $hour)
                        {{ $hour->order_count }},
                    @endforeach
                ],
                backgroundColor: 'rgba(99, 102, 241, 0.75)',
                borderColor: '#6366f1',
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0
                    }
                }
            }
        }
    });
    @endif
});
</script>
@endsection