<!DOCTYPE html>
<html lang="en">
<head>
  <title>Admin - Top Analysis Report</title>
  @include('includes.style')
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <style>
    .summary-card {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .summary-card h5 {
      font-size: 14px;
      opacity: 0.9;
      margin-bottom: 5px;
    }
    .summary-card h3 {
      font-weight: 700;
      margin-bottom: 0;
    }
    .veg-badge {
      background: #10b981;
      color: white;
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 12px;
    }
    .nonveg-badge {
      background: #ef4444;
      color: white;
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 12px;
    }
    .rank-badge {
      display: inline-block;
      width: 24px;
      height: 24px;
      background: #3b82f6;
      color: white;
      border-radius: 50%;
      text-align: center;
      line-height: 24px;
      font-weight: bold;
      margin-right: 10px;
    }
    .rank-badge.gold { background: #f59e0b; }
    .rank-badge.silver { background: #94a3b8; }
    .rank-badge.bronze { background: #b45309; }
    .table-responsive {
      margin-bottom: 30px;
    }
    .table th {
      background-color: #f8f9fa;
      font-weight: 600;
    }
    .date-filter-box {
      background: #f1f5f9;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 20px;
    }
    .report-title {
      color: #1e293b;
      font-weight: 600;
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 2px solid #e2e8f0;
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
                <h5 class="m-b-10">Top Analysis Report</h5>
              </div>
              <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('order.report') }}">Reports</a></li>
                <li class="breadcrumb-item" aria-current="page">Top Analysis</li>
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
              <h5>Top 10 Customers & Dishes Report</h5>
              <div class="float-end">
                <button id="printReport" class="btn btn-outline-primary btn-sm">
                  <i class="bi bi-printer"></i> Print
                </button>
              </div>
            </div>

            <div class="card-body">
              <!-- Date Filter Form -->
              <div class="date-filter-box">
                <form method="GET" action="{{ route('order.report.top.analysis') }}" class="row g-3 align-items-end">
                  <div class="col-md-3">
                    <label class="form-label">From Date</label>
                    <input type="date" name="from_date" value="{{ request('from_date') ?? \Carbon\Carbon::now()->subDays(30)->format('Y-m-d') }}" class="form-control">
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">To Date</label>
                    <input type="date" name="to_date" value="{{ request('to_date') ?? \Carbon\Carbon::now()->format('Y-m-d') }}" class="form-control">
                  </div>
                  <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                      <i class="bi bi-funnel"></i> Filter
                    </button>
                  </div>
                  <div class="col-md-3">
                    <a href="{{ route('order.report.top.analysis') }}" class="btn btn-outline-secondary w-100">
                      <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                  </div>
                </form>
              </div>

              <!-- Summary Stats -->
              <div class="row mb-4">
                <div class="col-md-3">
                  <div class="summary-card" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                    <h5 class="text-white">DATE RANGE</h5>
                    <h3 class="text-white">{{ $summary['date_range'] }}</h3>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="summary-card" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <h5 class="text-white">TOTAL CUSTOMERS</h5>
                    <h3 class="text-white">{{ $summary['total_customers'] }}</h3>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="summary-card" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <h5 class="text-white">TOTAL ORDERS</h5>
                    <h3 class="text-white">{{ $summary['total_orders'] }}</h3>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="summary-card" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                    <h5 class="text-white">TOTAL REVENUE</h5>
                    <h3 class="text-white">₹{{ $summary['total_revenue'] }}</h3>
                  </div>
                </div>
              </div>

              <!-- Two Column Layout -->
              <div class="row">
                <!-- Top 10 Customers -->
                <div class="col-lg-6">
                  <div class="card">
                    <div class="card-header">
                      <h5 class="report-title">
                        <i class="bi bi-people-fill text-primary"></i> 
                        Top 10 Customers (by Spending)
                      </h5>
                    </div>
                    <div class="card-body">
                      <div class="table-responsive">
                        <table id="topCustomersTable" class="table table-hover table-striped">
                          <thead>
                            <tr>
                              <th width="60">Rank</th>
                              <th>Customer Name</th>
                              <th>Phone</th>
                              <th>Orders</th>
                              <th>Total Spent</th>
                            </tr>
                          </thead>
                          <tbody>
                            @forelse($topCustomers as $customer)
                              <tr>
                                <td>
                                  @if($customer->rank == 1)
                                    <span class="rank-badge gold">{{ $customer->rank }}</span>
                                  @elseif($customer->rank == 2)
                                    <span class="rank-badge silver">{{ $customer->rank }}</span>
                                  @elseif($customer->rank == 3)
                                    <span class="rank-badge bronze">{{ $customer->rank }}</span>
                                  @else
                                    <span class="rank-badge">{{ $customer->rank }}</span>
                                  @endif
                                </td>
                                <td>
                                  <strong>{{ $customer->customer_name }}</strong>
                                  <br>
                                  <small class="text-muted">Last order: {{ $customer->last_order_date }}</small>
                                </td>
                                <td>{{ $customer->customer_phone ?? 'N/A' }}</td>
                                <td><span class="badge bg-primary">{{ $customer->total_orders }}</span></td>
                                <td><strong class="text-success">₹{{ $customer->total_spent }}</strong></td>
                              </tr>
                            @empty
                              
                            @endforelse
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Top 10 Dishes -->
                <div class="col-lg-6">
                  <div class="card">
                    <div class="card-header">
                      <h5 class="report-title">
                        <i class="bi bi-egg-fried text-success"></i> 
                        Top 10 Dishes (by Quantity Sold)
                      </h5>
                    </div>
                    <div class="card-body">
                      <div class="table-responsive">
                        <table id="topDishesTable" class="table table-hover table-striped">
                          <thead>
                            <tr>
                              <th width="60">Rank</th>
                              <th>Dish Name</th>
                              <th>Type</th>
                              <th>Qty Sold</th>
                              <th>Revenue</th>
                            </tr>
                          </thead>
                          <tbody>
                            @forelse($topDishes as $dish)
                              <tr>
                                <td>
                                  @if($dish->rank == 1)
                                    <span class="rank-badge gold">{{ $dish->rank }}</span>
                                  @elseif($dish->rank == 2)
                                    <span class="rank-badge silver">{{ $dish->rank }}</span>
                                  @elseif($dish->rank == 3)
                                    <span class="rank-badge bronze">{{ $dish->rank }}</span>
                                  @else
                                    <span class="rank-badge">{{ $dish->rank }}</span>
                                  @endif
                                </td>
                                <td>
                                  <strong>{{ $dish->dish_name }}</strong>
                                  <br>
                                  <small class="text-muted">Avg. Price: ₹{{ $dish->avg_price }}</small>
                                </td>
                                <td>
                                  @if($dish->food_type == 'veg')
                                    <span class="veg-badge">Veg</span>
                                  @else
                                    <span class="nonveg-badge">Non-Veg</span>
                                  @endif
                                </td>
                                <td><span class="badge bg-info">{{ $dish->total_quantity }}</span></td>
                                <td><strong class="text-success">₹{{ $dish->total_revenue }}</strong></td>
                              </tr>
                            @empty
                              
                            @endforelse
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Additional Stats -->
              <div class="row mt-4">
                <div class="col-md-6">
                  <div class="card">
                    <div class="card-body">
                      <h6 class="mb-3"><i class="bi bi-graph-up text-primary"></i> Customer Insights</h6>
                      <div class="row text-center">
                        <div class="col-6">
                          <h4 class="text-primary">₹{{ $summary['avg_order_value'] }}</h4>
                          <p class="text-muted mb-0">Avg. Order Value</p>
                        </div>
                        <div class="col-6">
                          <h4 class="text-success">{{ $summary['unique_dishes'] }}</h4>
                          <p class="text-muted mb-0">Unique Dishes Sold</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="card">
                    <div class="card-body">
                      <h6 class="mb-3"><i class="bi bi-basket text-success"></i> Dish Insights</h6>
                      <div class="row text-center">
                        <div class="col-6">
                          <h4 class="text-warning">{{ $summary['total_dishes_sold'] }}</h4>
                          <p class="text-muted mb-0">Total Items Sold</p>
                        </div>
                        <div class="col-6">
                          <h4 class="text-info">
                            @if($summary['total_orders'] > 0)
                              {{ number_format($summary['total_dishes_sold'] / $summary['total_orders'], 1) }}
                            @else
                              0
                            @endif
                          </h4>
                          <p class="text-muted mb-0">Items per Order</p>
                        </div>
                      </div>
                    </div>
                  </div>
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
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
  @include('includes.script')

  <script>
    $(document).ready(function() {
      // Initialize Top Customers Table with Export
      $('#topCustomersTable').DataTable({
        "paging": false,
        "searching": false,
        "ordering": false,
        "info": false,
        "dom": 'Bfrtip',
        "buttons": [
          {
            extend: 'excel',
            text: '<i class="bi bi-file-excel"></i> Export Excel',
            className: 'btn btn-success btn-sm',
            title: 'Top 10 Customers - {{ $summary["date_range"] }}',
            exportOptions: {
              columns: [0, 1, 2, 3, 4]
            }
          },
          
          {
            extend: 'print',
            text: '<i class="bi bi-printer"></i> Print',
            className: 'btn btn-primary btn-sm',
            title: '<h3 class="text-center">Top 10 Customers Report</h3><p class="text-center">{{ $summary["date_range"] }}</p>',
            exportOptions: {
              columns: [0, 1, 2, 3, 4]
            },
            customize: function (win) {
              $(win.document.body).find('table').addClass('print-table');
              $(win.document.body).find('h1').css('text-align', 'center');
            }
          }
        ]
      });

      // Initialize Top Dishes Table with Export
      $('#topDishesTable').DataTable({
        "paging": false,
        "searching": false,
        "ordering": false,
        "info": false,
        "dom": 'Bfrtip',
        "buttons": [
          {
            extend: 'excel',
            text: '<i class="bi bi-file-excel"></i> Export Excel',
            className: 'btn btn-success btn-sm',
            title: 'Top 10 Dishes - {{ $summary["date_range"] }}',
            exportOptions: {
              columns: [0, 1, 2, 3, 4]
            }
          },
          
          {
            extend: 'print',
            text: '<i class="bi bi-printer"></i> Print',
            className: 'btn btn-primary btn-sm',
            title: '<h3 class="text-center">Top 10 Dishes Report</h3><p class="text-center">{{ $summary["date_range"] }}</p>',
            exportOptions: {
              columns: [0, 1, 2, 3, 4]
            }
          }
        ]
      });

      // Print Full Report
      $('#printReport').click(function() {
        window.print();
      });

      // Auto-adjust DataTable buttons for responsive
      $(window).on('resize', function() {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
      });
    });

    // Print styles
    const style = document.createElement('style');
    style.innerHTML = `
      @media print {
        .sidebar, .page-header, .card-header .float-end, .dataTables_length, 
        .dataTables_filter, .dataTables_info, .dataTables_paginate, 
        .dt-buttons, .date-filter-box form button, .date-filter-box form .col-md-3:last-child,
        #printReport {
          display: none !important;
        }
        
        .card {
          border: none !important;
          box-shadow: none !important;
        }
        
        .card-header {
          background: #f8f9fa !important;
          color: #000 !important;
          border-bottom: 2px solid #dee2e6 !important;
        }
        
        .summary-card {
          color: #000 !important;
          background: #f1f5f9 !important;
          border: 1px solid #dee2e6 !important;
        }
        
        .table {
          border: 1px solid #dee2e6 !important;
        }
        
        .table th {
          background-color: #f8f9fa !important;
          color: #000 !important;
        }
        
        body {
          background: white !important;
          color: black !important;
        }
        
        .container, .pc-content {
          width: 100% !important;
          max-width: 100% !important;
          padding: 0 !important;
          margin: 0 !important;
        }
        
        .row {
          margin-left: 0 !important;
          margin-right: 0 !important;
        }
        
        .col-lg-6 {
          width: 50% !important;
          float: left !important;
        }
        
        .report-title {
          color: #000 !important;
        }
        
        .veg-badge, .nonveg-badge {
          color: #000 !important;
          background: #f1f5f9 !important;
          border: 1px solid #dee2e6 !important;
        }
      }
    `;
    document.head.appendChild(style);
  </script>

</body>
</html>