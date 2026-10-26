@extends('layouts.app')

@section('title')
<title>New vs Returning Customer Analysis Report - Admin</title>
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
  .badge-new-cust {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.04em;
    padding: 3px 10px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
  }
  .badge-ret-cust {
    background: #eef2ff;
    color: #4338ca;
    border: 1px solid #c7d2fe;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.04em;
    padding: 3px 10px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
  }
  .cohort-box {
    border-radius: var(--rpt-radius-md);
    padding: 20px;
    transition: all 0.2s ease;
  }
  .cohort-box-new {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
  }
  .cohort-box-ret {
    background: #eef2ff;
    border: 1px solid #c7d2fe;
  }
  .cohort-metric-lbl {
    font-size: 0.72rem;
    font-weight: 800;
    text-transform: uppercase;
    color: var(--rpt-muted);
    letter-spacing: 0.05em;
    margin-bottom: 2px;
  }
  .cohort-metric-val {
    font-family: 'Outfit', sans-serif;
    font-weight: 800;
    font-size: 1.25rem;
    color: var(--rpt-dark);
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
                <div class="rpt-header-icon icon-analysis">
                    <i class="fas fa-user-friends"></i>
                </div>
                <div class="rpt-header-title-meta">
                    <span class="rpt-header-eyebrow">Customer Retention & Loyalty Intelligence</span>
                    <h1 class="rpt-header-title">New vs Returning Customer Report</h1>
                    <p class="rpt-header-sub">
                        <i class="far fa-calendar-alt text-primary me-1"></i> Range: <strong>{{ $summary['date_range'] }}</strong>
                        <span class="mx-2">•</span>
                        <span>Cohort tracking of new acquisition vs repeat diner retention</span>
                    </p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('report.customer.analysis.export', request()->all()) }}" class="btn-rpt-secondary">
                    <i class="fas fa-file-excel text-success"></i> Export Excel
                </a>
                <button type="button" class="btn-rpt-secondary" onclick="window.print();">
                    <i class="fas fa-print text-primary"></i> Print Analytics
                </button>
            </div>
        </div>

        {{-- 2. Filter Bar Deck (Always defaults to current month) --}}
        <div class="rpt-filter-card">
            <form action="{{ route('report.customer.analysis') }}" method="GET" class="row g-3 align-items-end">
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
                    <label class="rpt-filter-label"><i class="fas fa-user-tag me-1"></i> Customer Segment</label>
                    <select name="customer_type" class="rpt-filter-control">
                        <option value="all">All Segments (New & Returning)</option>
                        <option value="new" {{ request('customer_type') == 'new' ? 'selected' : '' }}>New Customers Only</option>
                        <option value="returning" {{ request('customer_type') == 'returning' ? 'selected' : '' }}>Returning Customers Only</option>
                    </select>
                </div>

                <div class="col-xl-3 col-md-3 col-sm-6 d-flex gap-2">
                    <button type="submit" class="btn-rpt-primary flex-fill">
                        <i class="fas fa-filter"></i> Run Analysis
                    </button>
                    <a href="{{ route('report.customer.analysis') }}" class="btn-rpt-secondary" title="Reset to Current Month">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>

        {{-- 3. Metric KPI Cards --}}
        <div class="rpt-stats-grid">
            <div class="rpt-stat-card stat-purple">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Total Unique Patrons</span>
                    <span class="rpt-stat-val">{{ number_format($summary['total_customers']) }}</span>
                    <small class="text-muted mt-1 font-semibold">Active diners in period</small>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-success">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">New Customers</span>
                    <span class="rpt-stat-val">{{ number_format($summary['new_customers']) }}</span>
                    <small class="text-success mt-1 fw-bold">
                        <i class="fas fa-arrow-up me-1"></i>{{ $summary['new_customers_pct'] }}% (₹{{ number_format($summary['new_revenue'], 2) }})
                    </small>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-indigo">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Returning Customers</span>
                    <span class="rpt-stat-val">{{ number_format($summary['returning_customers']) }}</span>
                    <small class="text-indigo mt-1 fw-bold" style="color: #6366f1;">
                        <i class="fas fa-sync me-1"></i>{{ $summary['returning_customers_pct'] }}% (₹{{ number_format($summary['returning_revenue'], 2) }})
                    </small>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-history"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-warning">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Repeat Retention Rate</span>
                    <span class="rpt-stat-val">{{ $summary['returning_customers_pct'] }}%</span>
                    <small class="text-muted mt-1 fw-semibold">
                        Repeat AOV: ₹{{ number_format($summary['returning_aov'], 2) }}
                    </small>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-percentage"></i>
                </div>
            </div>
        </div>

        {{-- 4. Cohort Comparison Side-by-Side Deck --}}
        <div class="rpt-card mb-4">
            <div class="rpt-card-header">
                <h3 class="rpt-card-title">
                    <i class="fas fa-balance-scale text-primary"></i> Cohort Performance Comparison (New vs Returning)
                </h3>
                <span class="text-muted small">Period Total: ₹{{ number_format($summary['total_revenue'], 2) }}</span>
            </div>
            <div class="p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="cohort-box cohort-box-new">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge-new-cust"><i class="fas fa-star"></i> NEW CUSTOMERS COHORT</span>
                                <span class="fw-bold text-success fs-5">{{ $summary['new_customers_pct'] }}% Share</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="cohort-metric-lbl">Acquired Diners</div>
                                    <div class="cohort-metric-val">{{ number_format($summary['new_customers']) }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="cohort-metric-lbl">Total Period Revenue</div>
                                    <div class="cohort-metric-val text-success">₹{{ number_format($summary['new_revenue'], 2) }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="cohort-metric-lbl">Total Orders Placed</div>
                                    <div class="cohort-metric-val">{{ number_format($summary['new_orders']) }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="cohort-metric-lbl">Avg Spend Per Order (AOV)</div>
                                    <div class="cohort-metric-val">₹{{ number_format($summary['new_aov'], 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="cohort-box cohort-box-ret">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge-ret-cust"><i class="fas fa-sync"></i> RETURNING DINERS COHORT</span>
                                <span class="fw-bold fs-5" style="color: #4f46e5;">{{ $summary['returning_customers_pct'] }}% Share</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="cohort-metric-lbl">Returning Diners</div>
                                    <div class="cohort-metric-val">{{ number_format($summary['returning_customers']) }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="cohort-metric-lbl">Total Period Revenue</div>
                                    <div class="cohort-metric-val" style="color: #4f46e5;">₹{{ number_format($summary['returning_revenue'], 2) }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="cohort-metric-lbl">Total Orders Placed</div>
                                    <div class="cohort-metric-val">{{ number_format($summary['returning_orders']) }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="cohort-metric-lbl">Avg Spend Per Order (AOV)</div>
                                    <div class="cohort-metric-val">₹{{ number_format($summary['returning_aov'], 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. Interactive Graphs Grid --}}
        <div class="row g-4 mb-4">
            {{-- Donut Chart: Customer Distribution --}}
            <div class="col-lg-5">
                <div class="rpt-card h-100 mb-0">
                    <div class="rpt-card-header">
                        <h3 class="rpt-card-title">
                            <i class="fas fa-chart-pie text-primary"></i> Customer Ratio Breakdown
                        </h3>
                        <small class="text-muted">Total: {{ number_format($summary['total_customers']) }}</small>
                    </div>
                    <div class="p-3" style="height: 320px; position: relative;">
                        <canvas id="customerRatioPieChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Daily Stacked Bar Chart: Daily Customer Traffic --}}
            <div class="col-lg-7">
                <div class="rpt-card h-100 mb-0">
                    <div class="rpt-card-header">
                        <h3 class="rpt-card-title">
                            <i class="fas fa-chart-bar text-indigo"></i> Daily Customer Visits Trend
                        </h3>
                        <small class="text-muted">New vs Returning Patrons per day</small>
                    </div>
                    <div class="p-3" style="height: 320px; position: relative;">
                        <canvas id="customerDailyBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- 6. Individual Customer Data Table --}}
        <div class="rpt-card">
            <div class="rpt-card-header">
                <h3 class="rpt-card-title">
                    <i class="fas fa-address-book text-primary"></i> Individual Customer Spending & Visit Details
                </h3>
                <span class="text-muted small">Showing {{ $customersList->count() }} patrons</span>
            </div>

            <div class="table-responsive">
                <table class="rpt-table" id="customerTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Customer Name</th>
                            <th>Contact Phone</th>
                            <th class="text-center">Segment</th>
                            <th class="text-end">Orders in Period</th>
                            <th class="text-end">Spend in Period (₹)</th>
                            <th class="text-end">Lifetime Orders</th>
                            <th class="text-end">Lifetime Spend (₹)</th>
                            <th>First Visit</th>
                            <th>Latest Order</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customersList as $cust)
                        <tr>
                            <td>
                                <strong class="text-dark d-block">{{ $cust->name }}</strong>
                            </td>
                            <td>
                                @if($cust->phone && $cust->phone !== 'N/A')
                                    <span class="text-muted small font-monospace"><i class="fas fa-phone-alt me-1 text-secondary opacity-75"></i>{{ $cust->phone }}</span>
                                @else
                                    <span class="text-muted small">N/A</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($cust->type === 'NEW')
                                    <span class="badge-new-cust"><i class="fas fa-star" style="font-size: 0.65rem;"></i> NEW</span>
                                @else
                                    <span class="badge-ret-cust"><i class="fas fa-sync" style="font-size: 0.65rem;"></i> RETURNING</span>
                                @endif
                            </td>
                            <td class="text-end fw-semibold">{{ number_format($cust->period_orders) }}</td>
                            <td class="text-end fw-bold text-success" style="font-family: 'Outfit', sans-serif;">
                                ₹{{ number_format($cust->period_spent, 2) }}
                            </td>
                            <td class="text-end text-muted">{{ number_format($cust->lifetime_orders) }}</td>
                            <td class="text-end fw-semibold text-primary" style="font-family: 'Outfit', sans-serif;">
                                ₹{{ number_format($cust->lifetime_spent, 2) }}
                            </td>
                            <td class="text-muted small">{{ $cust->first_order_date }}</td>
                            <td class="text-muted small">{{ $cust->latest_order_date }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-users-slash fa-3x mb-3 text-secondary opacity-50"></i>
                                <h6 class="fw-bold">No Customer Records Found</h6>
                                <p class="mb-0 text-secondary">No customer transactions matched your filter criteria for this date range.</p>
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
{{-- Load jQuery first to prevent "ReferenceError: $ is not defined" --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@include('includes.script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
  $(document).ready(function() {
      if ($('#customerTable tbody tr').length > 0 && !$('#customerTable tbody tr td[colspan]').length) {
          $('#customerTable').DataTable({
              paging: true,
              pageLength: 25,
              lengthMenu: [10, 25, 50, 100],
              ordering: true,
              order: [[4, 'desc']], // Sort by Spend in Period desc
              info: true,
              language: {
                  search: "_INPUT_",
                  searchPlaceholder: "Search customer name or phone..."
              }
          });
      }
  });

  document.addEventListener('DOMContentLoaded', function() {
      const newCustCount = {{ $summary['new_customers'] }};
      const returningCustCount = {{ $summary['returning_customers'] }};

      // 1. Customer Ratio Donut Chart
      const ctxPie = document.getElementById('customerRatioPieChart');
      if (ctxPie && (newCustCount > 0 || returningCustCount > 0)) {
          new Chart(ctxPie, {
              type: 'doughnut',
              data: {
                  labels: ['New Customers', 'Returning Customers'],
                  datasets: [{
                      data: [newCustCount, returningCustCount],
                      backgroundColor: ['#10b981', '#6366f1'],
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
                          labels: {
                              boxWidth: 14,
                              font: { family: "'Plus Jakarta Sans', sans-serif", weight: '600' }
                          }
                      },
                      tooltip: {
                          callbacks: {
                              label: function(context) {
                                  let value = context.raw || 0;
                                  let total = newCustCount + returningCustCount;
                                  let pct = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                  return ' ' + context.label + ': ' + value + ' (' + pct + '%)';
                              }
                          }
                      }
                  },
                  cutout: '68%'
              }
          });
      }

      // 2. Daily Stacked Bar Chart
      const ctxBar = document.getElementById('customerDailyBarChart');
      const periodDates = {!! json_encode($periodDates) !!};
      const chartNewData = {!! json_encode($chartNewData) !!};
      const chartReturningData = {!! json_encode($chartReturningData) !!};

      if (ctxBar && periodDates.length > 0) {
          new Chart(ctxBar, {
              type: 'bar',
              data: {
                  labels: periodDates,
                  datasets: [
                      {
                          label: 'New Customers',
                          data: chartNewData,
                          backgroundColor: '#10b981',
                          borderRadius: 4
                      },
                      {
                          label: 'Returning Customers',
                          data: chartReturningData,
                          backgroundColor: '#6366f1',
                          borderRadius: 4
                      }
                  ]
              },
              options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  scales: {
                      x: {
                          stacked: true,
                          grid: { display: false }
                      },
                      y: {
                          stacked: true,
                          beginAtZero: true,
                          ticks: { precision: 0 }
                      }
                  },
                  plugins: {
                      legend: { 
                          position: 'top',
                          labels: {
                              boxWidth: 14,
                              font: { family: "'Plus Jakarta Sans', sans-serif", weight: '600' }
                          }
                      },
                      tooltip: {
                          callbacks: {
                              afterBody: function(items) {
                                  let sum = 0;
                                  items.forEach(function(item) {
                                      sum += item.raw;
                                  });
                                  return 'Total Patrons: ' + sum;
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
