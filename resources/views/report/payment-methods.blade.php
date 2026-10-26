@extends('layouts.app')

@section('title')
<title>Payment Method Report with Graph - Admin</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="{{ asset('admin_template/css/report-analytics.css') }}">
<style>
  .method-badge {
    font-size: 0.74rem;
    font-weight: 800;
    padding: 3px 10px;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }
  .method-cash {
    background-color: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
  }
  .method-upi {
    background-color: #f5f3ff;
    color: #6d28d9;
    border: 1px solid #ddd6fe;
  }
  .method-card {
    background-color: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
  }
  .method-other {
    background-color: #f8fafc;
    color: #475569;
    border: 1px solid #e2e8f0;
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
                <div class="rpt-header-icon icon-gst">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="rpt-header-title-meta">
                    <span class="rpt-header-eyebrow">Payment Reconciliation & Multi-Channel</span>
                    <h1 class="rpt-header-title">Payment Method Report with Graph</h1>
                    <p class="rpt-header-sub">
                        <i class="far fa-calendar-alt text-primary me-1"></i> Range: <strong>{{ $summary['date_range'] }}</strong>
                        <span class="mx-2">•</span>
                        <span>Analysis of collections across Cash, UPI, Cards, and online gateways</span>
                    </p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('report.payment.methods.export', request()->all()) }}" class="btn-rpt-secondary">
                    <i class="fas fa-file-excel text-success"></i> Export Excel
                </a>
                <button type="button" class="btn-rpt-secondary" onclick="window.print();">
                    <i class="fas fa-print text-primary"></i> Print Analytics
                </button>
            </div>
        </div>

        {{-- 2. Filter Bar Deck (Always defaults to current month) --}}
        <div class="rpt-filter-card">
            <form action="{{ route('report.payment.methods') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-xl-3 col-md-3 col-sm-6">
                    <label class="rpt-filter-label"><i class="far fa-calendar me-1"></i> From Date</label>
                    <input type="date" name="from_date" class="rpt-filter-control"
                           value="{{ request('from_date', $fromDate->format('Y-m-d')) }}">
                </div>

                <div class="col-xl-3 col-md-3 col-sm-6">
                    <label class="rpt-filter-label"><i class="far fa-calendar-check me-1"></i> To Date</label>
                    <input type="date" name="to_date" class="rpt-filter-control"
                           value="{{ request('to_date', $toDate->format('Y-m-d')) }}">
                </div>

                <div class="col-xl-3 col-md-3 col-sm-6">
                    <label class="rpt-filter-label"><i class="fas fa-wallet me-1"></i> Payment Method</label>
                    <select name="payment_method" class="rpt-filter-control">
                        <option value="all">All Payment Methods</option>
                        @foreach($methodSummaries as $m)
                            <option value="{{ $m->method }}" {{ request('payment_method') == $m->method ? 'selected' : '' }}>
                                {{ strtoupper($m->method) }} ({{ $m->total_orders }} orders)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xl-3 col-md-3 col-sm-6 d-flex gap-2">
                    <button type="submit" class="btn-rpt-primary flex-fill">
                        <i class="fas fa-filter"></i> Apply Filter
                    </button>
                    <a href="{{ route('report.payment.methods') }}" class="btn-rpt-secondary" title="Reset to Current Month">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>

        {{-- 3. Metric KPI Cards --}}
        <div class="rpt-stats-grid">
            <div class="rpt-stat-card stat-success">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Total Realized Revenue</span>
                    <span class="rpt-stat-val">₹{{ number_format($summary['total_revenue'], 2) }}</span>
                    <small class="text-success mt-1 fw-bold">Settled payments in period</small>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-coins"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-primary">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Total Paid Orders</span>
                    <span class="rpt-stat-val">{{ number_format($summary['total_orders']) }}</span>
                    <small class="text-muted mt-1 fw-semibold">Completed transactions</small>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-info">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Avg Order Value (AOV)</span>
                    <span class="rpt-stat-val">₹{{ number_format($summary['avg_order_value'], 2) }}</span>
                    <small class="text-muted mt-1 fw-semibold">Per completed ticket</small>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-scale-balanced"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-warning">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Leading Method</span>
                    <span class="rpt-stat-val text-truncate" style="font-size: 1.25rem;" title="{{ strtoupper($summary['top_method']) }}">
                        {{ strtoupper($summary['top_method']) }}
                    </span>
                    <small class="text-warning mt-1 fw-bold">
                        <i class="fas fa-trophy me-1"></i>₹{{ number_format($summary['top_method_rev'], 2) }}
                    </small>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-award"></i>
                </div>
            </div>
        </div>

        {{-- 4. Interactive Charts Grid --}}
        <div class="row g-4 mb-4">
            {{-- Donut Chart: Revenue Share --}}
            <div class="col-lg-6">
                <div class="rpt-card h-100 mb-0">
                    <div class="rpt-card-header">
                        <h3 class="rpt-card-title">
                            <i class="fas fa-chart-pie text-primary"></i> Revenue Share by Payment Method
                        </h3>
                        <small class="text-muted">Total: ₹{{ number_format($summary['total_revenue'], 2) }}</small>
                    </div>
                    <div class="p-3" style="height: 310px; position: relative;">
                        <canvas id="paymentRevenuePieChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Bar Chart: Order Volume by Method --}}
            <div class="col-lg-6">
                <div class="rpt-card h-100 mb-0">
                    <div class="rpt-card-header">
                        <h3 class="rpt-card-title">
                            <i class="fas fa-chart-bar text-success"></i> Order Volume by Payment Method
                        </h3>
                        <small class="text-muted">Total: {{ number_format($summary['total_orders']) }} Orders</small>
                    </div>
                    <div class="p-3" style="height: 310px; position: relative;">
                        <canvas id="paymentOrdersBarChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Line Chart: Daily Collection Trend (Full Width) --}}
            <div class="col-12">
                <div class="rpt-card mb-0">
                    <div class="rpt-card-header">
                        <h3 class="rpt-card-title">
                            <i class="fas fa-chart-line text-warning"></i> Daily Payment Collection Trend (₹)
                        </h3>
                        <small class="text-muted">Time-series daily trajectory</small>
                    </div>
                    <div class="p-3" style="height: 320px; position: relative;">
                        <canvas id="paymentTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. Payment Methods Summary Table --}}
        <div class="rpt-card mb-4">
            <div class="rpt-card-header">
                <h3 class="rpt-card-title">
                    <i class="fas fa-table text-primary"></i> Payment Method Performance Breakdown
                </h3>
            </div>

            <div class="table-responsive">
                <table class="rpt-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Payment Method</th>
                            <th class="text-end">Total Orders</th>
                            <th class="text-end">Order Share (%)</th>
                            <th class="text-end">Total Revenue (₹)</th>
                            <th class="text-end">Revenue Share (%)</th>
                            <th class="text-end">Average Order Value (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($methodSummaries as $item)
                        @php
                            $mLower = strtolower($item->method);
                            $badgeClass = 'method-other';
                            if (str_contains($mLower, 'cash')) $badgeClass = 'method-cash';
                            elseif (str_contains($mLower, 'upi') || str_contains($mLower, 'phonepe') || str_contains($mLower, 'gpay') || str_contains($mLower, 'paytm')) $badgeClass = 'method-upi';
                            elseif (str_contains($mLower, 'card')) $badgeClass = 'method-card';
                        @endphp
                        <tr>
                            <td>
                                <span class="method-badge {{ $badgeClass }}">
                                    @if(str_contains($mLower, 'cash')) <i class="fas fa-money-bill-wave"></i>
                                    @elseif(str_contains($mLower, 'upi')) <i class="fas fa-mobile-alt"></i>
                                    @elseif(str_contains($mLower, 'card')) <i class="fas fa-credit-card"></i>
                                    @else <i class="fas fa-wallet"></i>
                                    @endif
                                    {{ strtoupper($item->method) }}
                                </span>
                            </td>
                            <td class="text-end fw-semibold">{{ number_format($item->total_orders) }}</td>
                            <td class="text-end text-muted">{{ $item->orders_pct }}%</td>
                            <td class="text-end fw-bold text-success" style="font-family: 'Outfit', sans-serif;">
                                ₹{{ number_format($item->total_collected, 2) }}
                            </td>
                            <td class="text-end">
                                <span class="fw-bold text-dark">{{ $item->rev_pct }}%</span>
                            </td>
                            <td class="text-end fw-semibold text-primary" style="font-family: 'Outfit', sans-serif;">
                                ₹{{ number_format($item->avg_order_val, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No payment records found for this period.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($methodSummaries->count() > 0)
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td class="py-3">TOTAL</td>
                            <td class="text-end py-3">{{ number_format($summary['total_orders']) }}</td>
                            <td class="text-end py-3">100.0%</td>
                            <td class="text-end py-3 text-success fs-6" style="font-family: 'Outfit', sans-serif;">
                                ₹{{ number_format($summary['total_revenue'], 2) }}
                            </td>
                            <td class="text-end py-3">100.0%</td>
                            <td class="text-end py-3 text-primary fs-6" style="font-family: 'Outfit', sans-serif;">
                                ₹{{ number_format($summary['avg_order_value'], 2) }}
                            </td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

        {{-- 6. Recent Paid Invoices Table --}}
        <div class="rpt-card">
            <div class="rpt-card-header">
                <h3 class="rpt-card-title">
                    <i class="fas fa-receipt text-info"></i> Recent Invoices in Range
                </h3>
                <span class="text-muted small">Showing page {{ $recentOrders->currentPage() }} of {{ $recentOrders->lastPage() }}</span>
            </div>

            <div class="table-responsive">
                <table class="rpt-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Date & Time</th>
                            <th>Customer</th>
                            <th>Order Type</th>
                            <th>Payment Method</th>
                            <th class="text-end">Amount Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $ord)
                        @php
                            $mLower = strtolower($ord->payment_method ?: 'unspecified');
                            $badgeClass = 'method-other';
                            if (str_contains($mLower, 'cash')) $badgeClass = 'method-cash';
                            elseif (str_contains($mLower, 'upi')) $badgeClass = 'method-upi';
                            elseif (str_contains($mLower, 'card')) $badgeClass = 'method-card';
                        @endphp
                        <tr>
                            <td>
                                <strong class="text-dark">{{ $ord->order_id ?: ('#' . $ord->id) }}</strong>
                            </td>
                            <td class="text-muted small">{{ \Carbon\Carbon::parse($ord->created_at)->format('d M Y h:i A') }}</td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $ord->customer_name ?: 'Guest' }}</span>
                                @if($ord->customer_phone)
                                    <small class="text-muted font-monospace d-block">{{ $ord->customer_phone }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1">{{ strtoupper($ord->order_type) }}</span>
                            </td>
                            <td>
                                <span class="method-badge {{ $badgeClass }}">
                                    {{ strtoupper($ord->payment_method ?: 'UNSPECIFIED') }}
                                </span>
                            </td>
                            <td class="text-end fw-bold text-success" style="font-family: 'Outfit', sans-serif;">
                                ₹{{ number_format(floatval($ord->amount_paid ?: $ord->grand_total), 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No orders found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-3 d-flex justify-content-end">
                {{ $recentOrders->links() }}
            </div>
        </div>

    </div>

</div>
</div>
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@include('includes.script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
      const pieLabels = {!! json_encode($chartPieLabels) !!};
      const pieRevenue = {!! json_encode($chartPieRevenue) !!};
      const pieOrders = {!! json_encode($chartPieOrders) !!};
      const colorPalette = ['#ff5e14', '#10b981', '#0284c7', '#8b5cf6', '#f59e0b', '#ec4899', '#6366f1', '#64748b'];

      // 1. Revenue Donut Chart
      const ctxPie = document.getElementById('paymentRevenuePieChart');
      if (ctxPie && pieRevenue.length > 0) {
          new Chart(ctxPie, {
              type: 'doughnut',
              data: {
                  labels: pieLabels.map(l => l.toUpperCase()),
                  datasets: [{
                      data: pieRevenue,
                      backgroundColor: colorPalette.slice(0, pieLabels.length),
                      borderWidth: 3,
                      borderColor: '#ffffff',
                      hoverOffset: 6
                  }]
              },
              options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  plugins: {
                      legend: { 
                          position: 'bottom',
                          labels: { boxWidth: 14, font: { family: "'Plus Jakarta Sans', sans-serif", weight: '600' } }
                      },
                      tooltip: {
                          callbacks: {
                              label: function(context) {
                                  let value = context.raw || 0;
                                  return ' ' + context.label + ': ₹' + Number(value).toLocaleString();
                              }
                          }
                      }
                  },
                  cutout: '68%'
              }
          });
      }

      // 2. Orders Bar Chart
      const ctxBar = document.getElementById('paymentOrdersBarChart');
      if (ctxBar && pieOrders.length > 0) {
          new Chart(ctxBar, {
              type: 'bar',
              data: {
                  labels: pieLabels.map(l => l.toUpperCase()),
                  datasets: [{
                      label: 'Total Orders',
                      data: pieOrders,
                      backgroundColor: colorPalette.slice(0, pieLabels.length),
                      borderRadius: 6
                  }]
              },
              options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  plugins: {
                      legend: { display: false },
                      tooltip: {
                          callbacks: {
                              label: function(context) {
                                  return ' ' + context.raw + ' Orders';
                              }
                          }
                      }
                  },
                  scales: {
                      x: { grid: { display: false } },
                      y: {
                          beginAtZero: true,
                          ticks: { precision: 0 }
                      }
                  }
              }
          });
      }

      // 3. Daily Trend Line Chart
      const ctxTrend = document.getElementById('paymentTrendChart');
      const uniqueDates = {!! json_encode($uniqueDates) !!};
      const trendDatasets = {!! json_encode($trendDatasets) !!};

      if (ctxTrend && uniqueDates.length > 0) {
          new Chart(ctxTrend, {
              type: 'line',
              data: {
                  labels: uniqueDates,
                  datasets: trendDatasets
              },
              options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  interaction: {
                      mode: 'index',
                      intersect: false
                  },
                  plugins: {
                      legend: { 
                          position: 'top',
                          labels: { boxWidth: 14, font: { family: "'Plus Jakarta Sans', sans-serif", weight: '600' } }
                      },
                      tooltip: {
                          callbacks: {
                              label: function(context) {
                                  return ' ' + context.dataset.label + ': ₹' + Number(context.raw).toLocaleString();
                              }
                          }
                      }
                  },
                  scales: {
                      x: { grid: { display: false } },
                      y: {
                          beginAtZero: true,
                          ticks: {
                              callback: function(val) {
                                  return '₹' + Number(val).toLocaleString();
                              }
                          }
                      }
                  }
              }
          });
      }
  });
</script>
@endsection
