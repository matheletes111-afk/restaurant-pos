<!DOCTYPE html>
<html lang="en">
<head>
  <title>Admin - Order Analysis Report</title>
  @include('includes.style')
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .stat-card {
      background: white;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      border-left: 5px solid;
      transition: transform 0.3s ease;
    }
    .stat-card:hover {
      transform: translateY(-5px);
    }
    .stat-icon {
      font-size: 2.5rem;
      opacity: 0.8;
      margin-bottom: 15px;
    }
    .stat-value {
      font-size: 1.8rem;
      font-weight: 700;
      margin-bottom: 5px;
    }
    .stat-label {
      color: #64748b;
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .counter-card {
      text-align: center;
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 15px;
    }
    .counter-value {
      font-size: 1.8rem;
      font-weight: 700;
    }
    .counter-label {
      font-size: 0.85rem;
      color: #64748b;
    }
    .chart-container {
      background: white;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 25px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .chart-title {
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 20px;
      color: #1e293b;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .badge-custom {
      padding: 5px 12px;
      border-radius: 20px;
      font-weight: 500;
    }
    .date-range-box {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 25px;
    }
    .table-counters td {
      vertical-align: middle;
    }
    .progress-thin {
      height: 6px;
      border-radius: 3px;
    }
  </style>
</head>

<body data-pc-theme="light">
  <div class="loader-bg">
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>

  @include('includes.sidebar')

  <div class="pc-container">
    <div class="pc-content">

      <!-- Breadcrumb -->
      <div class="page-header">
        <div class="page-block">
          <div class="row align-items-center">
            <div class="col-md-12">
              <div class="page-header-title">
                <h5 class="m-b-10">Order Analysis Report</h5>
              </div>
              <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('order.report') }}">Reports</a></li>
                <li class="breadcrumb-item" aria-current="page">Order Analysis</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <!-- Breadcrumb end -->

      <div class="row">
        <div class="col-sm-12">
          <div class="card">
            <div class="card-header">
              <h5>Order Analysis Dashboard</h5>
              <div class="float-end">
                <button id="printReport" class="btn btn-outline-primary btn-sm">
                  <i class="bi bi-printer"></i> Print
                </button>
              </div>
            </div>

            <div class="card-body">
              <!-- Date Filter Form -->
              <div class="date-range-box">
                <form method="GET" action="{{ route('order.report.analysis') }}" class="row g-3 align-items-end">
                  <div class="col-md-3">
                    <label class="form-label text-white">From Date</label>
                    <input type="date" name="from_date" value="{{ request('from_date') ?? \Carbon\Carbon::now()->subDays(7)->format('Y-m-d') }}" class="form-control">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label text-white">To Date</label>
                    <input type="date" name="to_date" value="{{ request('to_date') ?? \Carbon\Carbon::now()->format('Y-m-d') }}" class="form-control">
                  </div>
                  <div class="col-md-3">
                    <button type="submit" class="btn btn-light w-100">
                      <i class="bi bi-funnel"></i> Filter
                    </button>
                  </div>
                  <div class="col-md-3">
                    <a href="{{ route('order.report.analysis') }}" class="btn btn-outline-light w-100">
                      <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                  </div>
                </form>
              </div>

              <!-- Summary Stats -->
              <div class="row mb-4">
                <div class="col-md-3">
                  <div class="stat-card" style="border-left-color: #3b82f6;">
                    <div class="stat-icon text-primary">
                      <i class="bi bi-cash-coin"></i>
                    </div>
                    <div class="stat-value text-primary">₹{{ number_format($totalAmount, 2) }}</div>
                    <div class="stat-label">Total Revenue</div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="stat-card" style="border-left-color: #10b981;">
                    <div class="stat-icon text-success">
                      <i class="bi bi-cart-check"></i>
                    </div>
                    <div class="stat-value text-success">{{ $totalOrders }}</div>
                    <div class="stat-label">Total Orders</div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="stat-card" style="border-left-color: #f59e0b;">
                    <div class="stat-icon text-warning">
                      <i class="bi bi-graph-up"></i>
                    </div>
                    <div class="stat-value text-warning">₹{{ number_format($avgOrderValue, 2) }}</div>
                    <div class="stat-label">Avg. Order Value</div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="stat-card" style="border-left-color: #8b5cf6;">
                    <div class="stat-icon text-purple">
                      <i class="bi bi-trophy"></i>
                    </div>
                    <div class="stat-value text-purple">
                      @if($peakDay)
                        {{ \Carbon\Carbon::parse($peakDay->order_date)->format('d M') }}
                      @else
                        N/A
                      @endif
                    </div>
                    <div class="stat-label">Peak Order Day</div>
                  </div>
                </div>
              </div>

              <!-- Two Column Layout -->
              <div class="row">
                <!-- Left Column -->
                <div class="col-lg-8">
                  <!-- Order Type Distribution -->
                  <div class="chart-container">
                    <h6 class="chart-title">
                      <i class="bi bi-shop text-primary"></i> Order Type Distribution
                    </h6>
                    <div class="row">
                      @php
                        $dineIn = $orderTypeCounts['DINE_IN'] ?? (object)['count' => 0, 'total_amount' => 0];
                        $takeaway = $orderTypeCounts['TAKEAWAY'] ?? (object)['count' => 0, 'total_amount' => 0];
                        $totalTypeOrders = $dineIn->count + $takeaway->count;
                      @endphp
                      
                      <div class="col-md-6 mb-3">
                        <div class="counter-card" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white;">
                          <div class="counter-value">{{ $dineIn->count }}</div>
                          <div class="counter-label text-white">DINE-IN ORDERS</div>
                          <div class="mt-2">₹{{ number_format($dineIn->total_amount ?? 0, 2) }}</div>
                        </div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <div class="counter-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                          <div class="counter-value">{{ $takeaway->count }}</div>
                          <div class="counter-label text-white">TAKEAWAY ORDERS</div>
                          <div class="mt-2">₹{{ number_format($takeaway->total_amount ?? 0, 2) }}</div>
                        </div>
                      </div>
                      
                      @if($totalTypeOrders > 0)
                      <div class="col-12">
                        <div class="mt-3">
                          <div class="d-flex justify-content-between mb-1">
                            <small>Dine-In: {{ number_format(($dineIn->count / $totalTypeOrders) * 100, 1) }}%</small>
                            <small>Takeaway: {{ number_format(($takeaway->count / $totalTypeOrders) * 100, 1) }}%</small>
                          </div>
                          <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-primary" style="width: {{ ($dineIn->count / $totalTypeOrders) * 100 }}%"></div>
                            <div class="progress-bar bg-success" style="width: {{ ($takeaway->count / $totalTypeOrders) * 100 }}%"></div>
                          </div>
                        </div>
                      </div>
                      @endif
                    </div>
                  </div>

                  <!-- Payment Status -->
                  <div class="chart-container">
                    <h6 class="chart-title">
                      <i class="bi bi-wallet2 text-success"></i> Payment Status
                    </h6>
                    <div class="row">
                      @php
                        $paid = $paymentStatusCounts['PAID'] ?? (object)['count' => 0, 'total_amount' => 0];
                        $misc = $paymentStatusCounts['MISCORDER'] ?? (object)['count' => 0, 'total_amount' => 0];
                      @endphp
                      
                      <div class="col-md-6 mb-3">
                        <div class="counter-card" style="background: rgba(16, 185, 129, 0.1); border: 2px solid #10b981;">
                          <div class="counter-value text-success">{{ $paid->count }}</div>
                          <div class="counter-label">PAID ORDERS</div>
                          <div class="mt-2 text-success">₹{{ number_format($paid->total_amount ?? 0, 2) }}</div>
                        </div>
                      </div>
                      <div class="col-md-6 mb-3">
                        <div class="counter-card" style="background: rgba(239, 68, 68, 0.1); border: 2px solid #ef4444;">
                          <div class="counter-value text-danger">{{ $misc->count }}</div>
                          <div class="counter-label">MISC ORDERS</div>
                          <div class="mt-2 text-danger">₹{{ number_format($misc->total_amount ?? 0, 2) }}</div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Daily Order Trend Chart -->
                  @if($dailyTrend->count() > 0)
                  <div class="chart-container">
                    <h6 class="chart-title">
                      <i class="bi bi-calendar-week text-warning"></i> Daily Order Trend
                    </h6>
                    <div style="height: 300px;">
                      <canvas id="dailyTrendChart"></canvas>
                    </div>
                  </div>
                  @endif
                </div>

                <!-- Right Column -->
                <div class="col-lg-4">
                  <!-- Payment Methods -->
                  <div class="chart-container">
                    <h6 class="chart-title">
                      <i class="bi bi-credit-card text-info"></i> Payment Methods
                      <small class="text-muted">(Found: {{ count($paymentMethods) }})</small>
                    </h6>
                    
                    @if(count($paymentMethods) > 0)
                      <table class="table table-sm table-counters">
                        @foreach($paymentMethods as $method)
                          @php
                            $methodData = $paymentMethodCounts[$method] ?? (object)['count' => 0, 'total_amount' => 0];
                          @endphp
                          <tr>
                            <td>
                              @php
                                $methodLower = strtolower($method ?? '');
                              @endphp
                              
                              @if(str_contains($methodLower, 'cash'))
                                <i class="bi bi-cash text-success"></i>
                              @elseif(str_contains($methodLower, 'upi'))
                                <i class="bi bi-phone text-primary"></i>
                              @elseif(str_contains($methodLower, 'card'))
                                <i class="bi bi-credit-card text-info"></i>
                              @else
                                <i class="bi bi-wallet text-secondary"></i>
                              @endif
                              
                              <strong>{{ $method ?? 'Unknown' }}</strong>
                            </td>
                            <td class="text-end">
                              <span class="badge bg-primary">{{ $methodData->count }}</span>
                            </td>
                            <td class="text-end">
                              <small class="text-muted">₹{{ number_format($methodData->total_amount ?? 0, 2) }}</small>
                            </td>
                          </tr>
                        @endforeach
                      </table>
                    @else
                      <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No payment method data found for the selected period.
                      </div>
                    @endif
                  </div>

                  <!-- Veg/Non-Veg Orders -->
                  <div class="chart-container">
                    <h6 class="chart-title">
                      <i class="bi bi-egg-fried text-success"></i> Food Type Analysis
                    </h6>
                    <div class="row text-center">
                      @php
                        // Handle different possible food type values
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
                      @endphp
                      
                      <div class="col-6">
                        <div class="p-3" style="background: rgba(16, 185, 129, 0.1); border-radius: 10px;">
                          <div class="text-success" style="font-size: 1.5rem; font-weight: 600;">{{ $vegCount }}</div>
                          <div class="text-muted">Veg Orders</div>
                          <small class="text-muted">{{ $vegItemCount }} items</small>
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="p-3" style="background: rgba(239, 68, 68, 0.1); border-radius: 10px;">
                          <div class="text-danger" style="font-size: 1.5rem; font-weight: 600;">{{ $nonVegCount }}</div>
                          <div class="text-muted">Non-Veg Orders</div>
                          <small class="text-muted">{{ $nonVegItemCount }} items</small>
                        </div>
                      </div>
                      
                      @if($totalVegNonVeg > 0)
                      <div class="col-12 mt-3">
                        <div class="d-flex justify-content-between mb-1">
                          <small>Veg: {{ number_format(($vegCount / $totalVegNonVeg) * 100, 1) }}%</small>
                          <small>Non-Veg: {{ number_format(($nonVegCount / $totalVegNonVeg) * 100, 1) }}%</small>
                        </div>
                        <div class="progress progress-thin">
                          <div class="progress-bar bg-success" style="width: {{ ($vegCount / $totalVegNonVeg) * 100 }}%"></div>
                          <div class="progress-bar bg-danger" style="width: {{ ($nonVegCount / $totalVegNonVeg) * 100 }}%"></div>
                        </div>
                      </div>
                      @else
                      <div class="col-12">
                        <div class="alert alert-warning">
                          <i class="bi bi-exclamation-triangle"></i> No food type data available
                        </div>
                      </div>
                      @endif
                    </div>
                  </div>

                  <!-- Peak Day Details -->
                  @if($peakDay)
                  <div class="chart-container">
                    <h6 class="chart-title">
                      <i class="bi bi-star-fill text-warning"></i> Peak Order Day
                    </h6>
                    <div class="text-center p-4" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); color: white; border-radius: 10px;">
                      <div style="font-size: 2rem; font-weight: 700;">
                        {{ \Carbon\Carbon::parse($peakDay->order_date)->format('d M Y') }}
                      </div>
                      <div class="mt-2">
                        <div style="font-size: 1.5rem; font-weight: 600;">{{ $peakDay->order_count }}</div>
                        <div>Orders</div>
                      </div>
                      <div class="mt-2">
                        <div style="font-size: 1.2rem;">₹{{ number_format($peakDay->total_amount, 2) }}</div>
                        <div>Revenue</div>
                      </div>
                    </div>
                  </div>
                  @endif

                  <!-- Hourly Distribution -->
                  @if($hourlyDistribution->count() > 0)
                  <div class="chart-container">
                    <h6 class="chart-title">
                      <i class="bi bi-clock text-primary"></i> Busiest Hours
                    </h6>
                    <div style="height: 200px;">
                      <canvas id="hourlyChart"></canvas>
                    </div>
                  </div>
                  @endif
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- JS Libraries -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  @include('includes.script')

  <script>
    $(document).ready(function() {
      // Print Report
      $('#printReport').click(function() {
        window.print();
      });

      // Daily Trend Chart
      @if($dailyTrend->count() > 0)
      const dailyCtx = document.getElementById('dailyTrendChart').getContext('2d');
      const dailyChart = new Chart(dailyCtx, {
        type: 'line',
        data: {
          labels: [
            @foreach($dailyTrend as $day)
              "{{ \Carbon\Carbon::parse($day->order_date)->format('d M') }}",
            @endforeach
          ],
          datasets: [{
            label: 'Orders',
            data: [
              @foreach($dailyTrend as $day)
                {{ $day->order_count }},
              @endforeach
            ],
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.3
          }, {
            label: 'Revenue (₹)',
            data: [
              @foreach($dailyTrend as $day)
                {{ $day->total_amount }},
              @endforeach
            ],
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.3
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'top',
            }
          },
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
      @endif

      // Hourly Distribution Chart
      @if($hourlyDistribution->count() > 0)
      const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
      const hourlyChart = new Chart(hourlyCtx, {
        type: 'bar',
        data: {
          labels: [
            @foreach($hourlyDistribution as $hour)
              "{{ $hour->order_hour }}:00",
            @endforeach
          ],
          datasets: [{
            label: 'Orders per Hour',
            data: [
              @foreach($hourlyDistribution as $hour)
                {{ $hour->order_count }},
              @endforeach
            ],
            backgroundColor: 'rgba(59, 130, 246, 0.7)',
            borderColor: '#3b82f6',
            borderWidth: 1
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
                stepSize: 1
              }
            }
          }
        }
      });
      @endif
    });
  </script>

</body>
</html>