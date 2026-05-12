<!DOCTYPE html>
<html lang="en">
<head>
  <title>Admin - Order Management Report</title>
  @include('includes.style')
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .filter-card {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 25px;
    }
    .filter-card label {
      font-weight: 500;
      margin-bottom: 8px;
    }
    .summary-card {
      background: white;
      border-radius: 12px;
      padding: 18px;
      margin-bottom: 15px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      text-align: center;
      border-top: 4px solid;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .summary-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    }
    .summary-value {
      font-size: 1.8rem;
      font-weight: 700;
      margin-bottom: 5px;
    }
    .summary-label {
      color: #64748b;
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .status-badge {
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 500;
      display: inline-block;
    }
    .badge-paid { background: #10b981; color: white; }
    .badge-pending { background: #f59e0b; color: white; }
    .badge-misc { background: #8b5cf6; color: white; }
    .badge-dinein { background: #3b82f6; color: white; }
    .badge-takeaway { background: #10b981; color: white; }
    
    .payment-method-icon {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-right: 8px;
    }
    .icon-cash { background: #10b98120; color: #10b981; }
    .icon-upi { background: #3b82f620; color: #3b82f6; }
    .icon-card { background: #8b5cf620; color: #8b5cf6; }
    
    .amount-cell {
      font-weight: 600;
    }
    .amount-paid {
      color: #10b981;
    }
    .amount-pending {
      color: #ef4444;
    }
    
    .action-buttons {
      display: flex;
      gap: 5px;
      justify-content: center;
    }
    
    .table-responsive {
      overflow-x: auto;
    }
    
    #ordersTable {
      width: 100% !important;
    }
    
    #ordersTable th {
      white-space: nowrap;
    }
    
    .dataTables_filter {
      margin-bottom: 15px;
    }
    
    .dataTables_filter input {
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 6px 12px;
      margin-left: 8px;
    }
    
    .dt-buttons {
      margin-bottom: 15px;
    }
    
    .btn-sm {
      padding: 5px 12px;
      font-size: 0.8rem;
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
                <h5 class="m-b-10">Order Management Report</h5>
              </div>
              <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="#">Reports</a></li>
                <li class="breadcrumb-item active" aria-current="page">Order Management</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-12">
          <div class="card">
            <div class="card-body">
              <!-- Filter Form -->
              <div class="filter-card">
                <form method="GET" action="{{ route('order.report.management') }}" class="row g-3 align-items-end">
                  <div class="col-md-2">
                    <label class="form-label text-white">From Date</label>
                    <input type="date" name="from_date" value="{{ $fromDate->format('Y-m-d') }}" class="form-control">
                  </div>
                  <div class="col-md-2">
                    <label class="form-label text-white">To Date</label>
                    <input type="date" name="to_date" value="{{ $toDate->format('Y-m-d') }}" class="form-control">
                  </div>
                  <div class="col-md-2">
                    <label class="form-label text-white">Order Type</label>
                    <select name="order_type" class="form-control">
                      <option value="all" {{ ($orderType == 'all' || !$orderType) ? 'selected' : '' }}>All Types</option>
                      @foreach($orderTypes as $type)
                        <option value="{{ $type }}" {{ $orderType == $type ? 'selected' : '' }}>
                          {{ ucfirst(str_replace('_', ' ', strtolower($type))) }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-2">
                    <label class="form-label text-white">Payment Method</label>
                    <select name="payment_method" class="form-control">
                      <option value="all" {{ ($paymentMethod == 'all' || !$paymentMethod) ? 'selected' : '' }}>All Methods</option>
                      @foreach($paymentMethods as $method)
                        <option value="{{ $method }}" {{ $paymentMethod == $method ? 'selected' : '' }}>
                          {{ ucfirst($method) }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-2">
                    <label class="form-label text-white">Payment Status</label>
                    <select name="payment_status" class="form-control">
                      <option value="all" {{ ($paymentStatus == 'all' || !$paymentStatus) ? 'selected' : '' }}>All Status</option>
                      @foreach($paymentStatuses as $status)
                        <option value="{{ $status }}" {{ $paymentStatus == $status ? 'selected' : '' }}>
                          {{ ucfirst($status) }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-2">
                    <button type="submit" class="btn btn-light w-100">
                      <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                  </div>
                </form>
              </div>

              <!-- Summary Stats -->
              <div class="row mb-4">
                <div class="col-md-3">
                  <div class="summary-card" style="border-top-color: #3b82f6;">
                    <div class="summary-value text-primary">₹{{ number_format($summary['total_revenue'], 2) }}</div>
                    <div class="summary-label">Total Revenue</div>
                    <div class="mt-2">
                      <small class="text-muted">{{ $summary['total_orders'] }} Orders</small>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="summary-card" style="border-top-color: #10b981;">
                    <div class="summary-value text-success">₹{{ number_format($summary['total_collected'], 2) }}</div>
                    <div class="summary-label">Amount Collected</div>
                    <div class="mt-2">
                      <small class="text-muted">{{ $summary['paid_count'] }} Paid Orders</small>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="summary-card" style="border-top-color: #ef4444;">
                    <div class="summary-value text-danger">₹{{ number_format($summary['pending_amount'], 2) }}</div>
                    <div class="summary-label">Pending Amount</div>
                    <div class="mt-2">
                      <small class="text-muted">{{ $summary['pending_count'] }} Pending Orders</small>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="summary-card" style="border-top-color: #8b5cf6;">
                    <div class="summary-value text-purple">{{ $summary['total_orders'] }}</div>
                    <div class="summary-label">Total Orders</div>
                    <div class="mt-2">
                      <small class="text-muted">
                        Dine-in: {{ $summary['dine_in_count'] }} | Takeaway: {{ $summary['takeaway_count'] }}
                      </small>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Orders Table -->
              <div class="table-responsive">
                <table id="ordersTable" class="table table-hover table-striped">
                  <thead class="thead-light">
                    <tr>
                      <th>#</th>
                      <th>Order ID</th>
                      <th>Customer</th>
                      <th>Phone</th>
                      <th>Type</th>
                      <th>Grand Total</th>
                      <th>Paid</th>
                      <th>Balance</th>
                      <th>Payment Method</th>
                      <th>Status</th>
                      <th>Date</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($orders as $key => $order)
                      @php
                        $balance = round($order->grand_total, 2) - ($order->amount_paid ?? 0);
                        $isFullyPaid = $balance <= 0;
                      @endphp
                      <tr>
                        <td>{{ ($orders->currentPage() - 1) * $orders->perPage() + $key + 1 }}</td>
                        <td>
                          <span class="fw-bold text-primary">{{ $order->order_id }}</span>
                        </td>
                        <td>{{ $order->customer_name ?? 'Walk-in Customer' }}</td>
                        <td>{{ $order->customer_phone ?? '-' }}</td>
                        <td>
                          @if($order->order_type == 'DINE_IN')
                            <span class="status-badge badge-dinein">
                              <i class="bi bi-table"></i> Dine-in
                            </span>
                            @if($order->table)
                              <br><small class="text-muted">{{ $order->table->name }}</small>
                            @endif
                          @else
                            <span class="status-badge badge-takeaway">
                              <i class="bi bi-box"></i> Takeaway
                            </span>
                          @endif
                        </td>
                        <td class="amount-cell">
                          <strong>₹{{ number_format($order->grand_total, 2) }}</strong>
                        </td>
                        <td class="amount-cell amount-paid">
                          ₹{{ number_format($order->amount_paid ?? 0, 2) }}
                        </td>
                        <td class="amount-cell {{ $isFullyPaid ? 'amount-paid' : 'amount-pending' }}">
                          ₹{{ number_format($balance, 2) }}
                          @if(!$isFullyPaid)
                            <br><small class="text-danger"><i class="bi bi-exclamation-circle"></i> Due</small>
                          @endif
                        </td>
                        <td>
                          @if($order->payment_method)
                            <div class="d-flex align-items-center">
                              @php
                                $method = strtolower($order->payment_method);
                              @endphp
                              <span class="payment-method-icon 
                                {{ str_contains($method, 'cash') ? 'icon-cash' : (str_contains($method, 'upi') ? 'icon-upi' : (str_contains($method, 'card') ? 'icon-card' : '')) }}">
                                <i class="bi bi-{{ str_contains($method, 'cash') ? 'cash' : (str_contains($method, 'upi') ? 'phone' : (str_contains($method, 'card') ? 'credit-card' : 'wallet')) }}"></i>
                              </span>
                              <span>{{ ucfirst($order->payment_method) }}</span>
                            </div>
                          @else
                            <span class="text-muted">-</span>
                          @endif
                        </td>
                        <td>
                          @if($order->payment_status == 'PAID')
                            <span class="status-badge badge-paid">
                              <i class="bi bi-check-circle"></i> Paid
                            </span>
                          @elseif($order->payment_status == 'PENDING')
                            <span class="status-badge badge-pending">
                              <i class="bi bi-clock"></i> Pending
                            </span>
                          @elseif($order->payment_status == 'MISCORDER')
                            <span class="status-badge badge-misc">
                              <i class="bi bi-receipt"></i> Misc
                            </span>
                          @else
                            <span class="text-muted">{{ $order->payment_status }}</span>
                          @endif
                        </td>
                        <td>
                          <div>{{ $order->created_at->format('d M Y') }}</div>
                          <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                        </td>
                        <td>
                          <div class="action-buttons">
                            <a href="{{ route('order.invoice', $order->id) }}" 
                               class="btn btn-sm btn-outline-primary" 
                               title="View Invoice"
                               target="_blank">
                              <i class="bi bi-receipt"></i>
                            </a>
                            <a href="{{ route('order.report.order.details', $order->id) }}" 
                               class="btn btn-sm btn-outline-info" 
                               title="View Details">
                              <i class="bi bi-eye"></i>
                            </a>
                            @if($order->payment_status == 'PENDING')
                              <a href="{{ route('order.payment', $order->id) }}" 
                                 class="btn btn-sm btn-outline-success" 
                                 title="Add Payment">
                                <i class="bi bi-cash"></i>
                              </a>
                            @endif
                          </div>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="12" class="text-center py-5">
                          <div class="empty-state">
                            <i class="bi bi-inbox" style="font-size: 48px; color: #cbd5e1;"></i>
                            <h5 class="mt-3">No Orders Found</h5>
                            <p class="text-muted">No orders match your filter criteria.</p>
                          </div>
                        </td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>

              <!-- Pagination -->
              @if($orders->hasPages())
                <div class="mt-4 d-flex justify-content-end">
                  {{ $orders->appends(request()->query())->links() }}
                </div>
              @endif

              <!-- Filter Summary -->
              <div class="row mt-4">
                <div class="col-md-12">
                  <div class="alert alert-info bg-light border">
                    <div class="row align-items-center">
                      <div class="col-md-4">
                        <strong><i class="bi bi-calendar-range"></i> Date Range:</strong><br>
                        {{ $fromDate->format('d M Y') }} - {{ $toDate->format('d M Y') }}
                      </div>
                      <div class="col-md-4">
                        <strong><i class="bi bi-funnel"></i> Applied Filters:</strong><br>
                        <span class="badge bg-secondary me-1">Type: {{ $orderType == 'all' || !$orderType ? 'All' : ucfirst(strtolower($orderType)) }}</span>
                        <span class="badge bg-secondary me-1">Method: {{ $paymentMethod == 'all' || !$paymentMethod ? 'All' : ucfirst($paymentMethod) }}</span>
                        <span class="badge bg-secondary">Status: {{ $paymentStatus == 'all' || !$paymentStatus ? 'All' : ucfirst($paymentStatus) }}</span>
                      </div>
                      <div class="col-md-4 text-md-end">
                        <strong><i class="bi bi-table"></i> Showing:</strong><br>
                        {{ $orders->firstItem() ?? 0 }} - {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} orders
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
    // Initialize DataTable with export buttons
    $('#ordersTable').DataTable({
      "paging": false,
      "searching": true,
      "ordering": true,
      "info": false,
      "responsive": true,
      "dom": '<"d-flex justify-content-between align-items-center mb-3"<"dt-buttons"B><"dt-search"f>>rt<"row"<"col-sm-12"i>>',
      "buttons": [
        {
          extend: 'excelHtml5',
          text: '<i class="bi bi-file-excel me-1"></i> Export Excel',
          className: 'btn btn-success btn-sm',
          title: 'Order_Management_Report_{{ date('Y-m-d') }}',
          exportOptions: {
            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
            format: {
              body: function(data, row, column, node) {
                // Clean HTML for export
                let $node = $(node);
                if (column === 1) {
                  return $node.find('.fw-bold').text().trim();
                }
                if (column === 4) {
                  return $node.text().replace(/\s+/g, ' ').trim();
                }
                if (column === 5 || column === 6 || column === 7) {
                  let val = $node.find('strong').text() || $node.text();
                  return val.replace('₹', '').trim();
                }
                if (column === 8) {
                  return $node.find('span:last').text().trim() || '-';
                }
                if (column === 9) {
                  return $node.text().replace(/\s+/g, ' ').trim();
                }
                if (column === 10) {
                  let date = $node.find('div').text().trim();
                  let time = $node.find('small').text().trim();
                  return `${date} ${time}`;
                }
                return $node.text().trim();
              }
            }
          }
        },
        {
          extend: 'print',
          text: '<i class="bi bi-printer me-1"></i> Print',
          className: 'btn btn-primary btn-sm',
          title: 'Order Management Report',
          customize: function(win) {
            $(win.document.body).find('table').addClass('table table-bordered');
            $(win.document.body).find('h1').css({
              'text-align': 'center',
              'font-size': '18px'
            });
            
            // Add summary info to print
            $(win.document.body).prepend(`
              <div style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                <div class="row">
                  <div class="col-md-6">
                    <p><strong>Date Range:</strong> {{ $fromDate->format('d M Y') }} - {{ $toDate->format('d M Y') }}</p>
                    <p><strong>Total Orders:</strong> {{ $summary['total_orders'] }}</p>
                    <p><strong>Total Revenue:</strong> ₹{{ number_format($summary['total_revenue'], 2) }}</p>
                  </div>
                  <div class="col-md-6">
                    <p><strong>Filters:</strong> Type: {{ $orderType == 'all' || !$orderType ? 'All' : ucfirst(strtolower($orderType)) }}</p>
                    <p><strong>Amount Collected:</strong> ₹{{ number_format($summary['total_collected'], 2) }}</p>
                    <p><strong>Pending Amount:</strong> ₹{{ number_format($summary['pending_amount'], 2) }}</p>
                  </div>
                </div>
              </div>
            `);
          }
        }
      ],
      "language": {
        "search": "<i class='bi bi-search'></i>",
        "searchPlaceholder": "Search orders..."
      }
    });
  });
  </script>

  <style>
    .dt-buttons .btn {
      margin-right: 5px;
    }
    .dataTables_filter {
      text-align: right;
    }
    .dataTables_filter label {
      font-weight: normal;
      margin-bottom: 0;
    }
    .dataTables_filter input {
      border: 1px solid #ddd;
      border-radius: 4px;
      padding: 5px 10px;
      margin-left: 8px;
    }
    .text-purple {
      color: #8b5cf6;
    }
    .empty-state {
      text-align: center;
      padding: 40px;
    }
    .table td {
      vertical-align: middle;
    }
  </style>

</body>
</html>