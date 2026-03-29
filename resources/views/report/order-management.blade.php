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
      color: white;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 25px;
    }
    .summary-card {
      background: white;
      border-radius: 12px;
      padding: 15px;
      margin-bottom: 15px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      text-align: center;
      border-top: 4px solid;
      transition: transform 0.3s ease;
    }
    .summary-card:hover {
      transform: translateY(-5px);
    }
    .summary-value {
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 5px;
    }
    .summary-label {
      color: #64748b;
      font-size: 0.85rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .status-badge {
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 500;
    }
    .badge-paid { background: #10b981; color: white; }
    .badge-pending { background: #f59e0b; color: white; }
    .badge-misc { background: #8b5cf6; color: white; }
    .badge-dinein { background: #3b82f6; color: white; }
    .badge-takeaway { background: #10b981; color: white; }
    .table-hover tbody tr:hover {
      background-color: #f8fafc;
    }
    .payment-method-icon {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-right: 8px;
    }
    .icon-cash { background: #10b98120; color: #10b981; }
    .icon-upi { background: #3b82f620; color: #3b82f6; }
    .icon-card { background: #8b5cf620; color: #8b5cf6; }
    .action-buttons {
      display: flex;
      gap: 5px;
    }
    .amount-cell {
      font-weight: 600;
    }
    .amount-paid {
      color: #10b981;
    }
    .amount-pending {
      color: #ef4444;
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
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('order.report') }}">Reports</a></li>
                <li class="breadcrumb-item" aria-current="page">Order Management</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <!-- Breadcrumb end -->

      <div class="row">
        <div class="col-sm-12">
          <div class="card">
            {{-- <div class="card-header">
              <h5>Order Management Dashboard</h5>
              <div class="float-end">
                <button id="exportExcel" class="btn btn-success btn-sm">
                  <i class="bi bi-file-excel"></i> Export Excel
                </button>
                <button id="printReport" class="btn btn-primary btn-sm">
                  <i class="bi bi-printer"></i> Print
                </button>
              </div>
            </div> --}}

            <div class="card-body">
              <!-- Filter Form -->
              <div class="filter-card">
                <form method="GET" action="{{ route('order.report.management') }}" class="row g-3 align-items-end">
                  <div class="col-md-2">
                    <label class="form-label text-white">From Date</label>
                    <input type="date" name="from_date" value="{{ request('from_date') ?? \Carbon\Carbon::now()->subDays(30)->format('Y-m-d') }}" class="form-control">
                  </div>
                  <div class="col-md-2">
                    <label class="form-label text-white">To Date</label>
                    <input type="date" name="to_date" value="{{ request('to_date') ?? \Carbon\Carbon::now()->format('Y-m-d') }}" class="form-control">
                  </div>
                  <div class="col-md-2">
                    <label class="form-label text-white">Order Type</label>
                    <select name="order_type" class="form-control">
                      <option value="all" {{ (request('order_type') == 'all' || !request('order_type')) ? 'selected' : '' }}>All Types</option>
                      @foreach($orderTypes as $type)
                        <option value="{{ $type }}" {{ request('order_type') == $type ? 'selected' : '' }}>
                          {{ ucfirst(str_replace('_', ' ', strtolower($type))) }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-2">
                    <label class="form-label text-white">Payment Method</label>
                    <select name="payment_method" class="form-control">
                      <option value="all" {{ (request('payment_method') == 'all' || !request('payment_method')) ? 'selected' : '' }}>All Methods</option>
                      @foreach($paymentMethods as $method)
                        <option value="{{ $method }}" {{ request('payment_method') == $method ? 'selected' : '' }}>
                          {{ ucfirst($method) }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-2">
                    <label class="form-label text-white">Payment Status</label>
                    <select name="payment_status" class="form-control">
                      <option value="all" {{ (request('payment_status') == 'all' || !request('payment_status')) ? 'selected' : '' }}>All Status</option>
                      @foreach($paymentStatuses as $status)
                        <option value="{{ $status }}" {{ request('payment_status') == $status ? 'selected' : '' }}>
                          {{ ucfirst($status) }}
                        </option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-2">
                    <button type="submit" class="btn btn-light w-100">
                      <i class="bi bi-funnel"></i> Filter
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
                        {{ $summary['dine_in_count'] }} Dine-in | {{ $summary['takeaway_count'] }} Takeaway
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
                      <th>Customer Details</th>
                      <th>Order Type</th>
                      <th>Grand Total</th>
                      <th>Amount Paid</th>
                      <th>Balance</th>
                      <th>Payment Method</th>
                      <th>Payment Status</th>
                      <th>Date & Time</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($orders as $key => $order)
                      @php
                        $balance = round($order->grand_total,2) - $order->amount_paid;
                        $isFullyPaid = $balance <= 0;
                        $paymentMethodLower = strtolower($order->payment_method ?? '');
                      @endphp
                      <tr>
                        <td>{{ ($orders->currentPage() - 1) * $orders->perPage() + $key + 1 }}</td>
                        <td>
                          <strong class="text-primary">{{ $order->order_id }}</strong>
                        </td>
                        <td>
                          <div>
                            <strong>{{ $order->customer_name ?? 'Walk-in Customer' }}</strong>
                            @if($order->customer_phone)
                              <br>
                              <small class="text-muted">{{ $order->customer_phone }}</small>
                            @endif
                          </div>
                        </td>
                        <td>
                          @if($order->order_type == 'DINE_IN')
                            <span class="status-badge badge-dinein">
                               Dine-in
                              
                            </span>
                            @if($order->table_name)
                                <br> - {{ $order->table_name }}
                              @endif
                          @else
                            <span class="status-badge badge-takeaway">
                               Takeaway
                            </span>
                          @endif
                        </td>
                        <td class="amount-cell">
                          <strong>₹{{ number_format($order->grand_total, 2) }}</strong>
                          <br>
                          <small class="text-muted">
                            Amt: ₹{{ number_format($order->total_amount, 2) }}
                            | GST: ₹{{ number_format($order->gst_amount, 2) }}
                          </small>
                        </td>
                        <td class="amount-cell amount-paid">
                          <strong>₹{{ number_format($order->amount_paid, 2) }}</strong>
                        </td>
                        <td class="amount-cell {{ $isFullyPaid ? 'amount-paid' : 'amount-pending' }}">
                          <strong>₹{{ number_format($balance, 2) }}</strong>
                          @if(!$isFullyPaid)
                            <br>
                            <small class="text-danger">
                              <i class="bi bi-exclamation-circle"></i> Pending
                            </small>
                          @endif
                        </td>
                        <td>
                          @if($order->payment_method)
                            <div class="d-flex align-items-center">
                              @if(str_contains($paymentMethodLower, 'cash'))
                                <span class="payment-method-icon icon-cash">
                                  <i class="bi bi-cash"></i>
                                </span>
                              @elseif(str_contains($paymentMethodLower, 'upi'))
                                <span class="payment-method-icon icon-upi">
                                  <i class="bi bi-phone"></i>
                                </span>
                              @elseif(str_contains($paymentMethodLower, 'card'))
                                <span class="payment-method-icon icon-card">
                                  <i class="bi bi-credit-card"></i>
                                </span>
                              @else
                                <span class="payment-method-icon" style="background: #94a3b820; color: #64748b;">
                                  <i class="bi bi-wallet"></i>
                                </span>
                              @endif
                              <span>{{ ucfirst($order->payment_method) }}</span>
                            </div>
                          @else
                            <span class="text-muted">Not Specified</span>
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
                          @endif
                        </td>
                        <td>
                          <div>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}</div>
                          <small class="text-muted">{{ \Carbon\Carbon::parse($order->created_at)->format('h:i A') }}</small>
                        </td>
                        <td>
                          <div class="action-buttons">
                            <a href="{{ route('order.invoice', $order->id) }}" 
                               class="btn btn-sm btn-outline-primary" 
                               title="View Invoice"
                               target="_blank">
                              <i class="bi bi-receipt"></i>
                            </a>
                            @if($order->payment_status == 'PENDING')
                              <a href="" 
                                 class="btn btn-sm btn-outline-success" 
                                 title="Add Payment">
                                <i class="bi bi-cash"></i>
                              </a>
                            @endif
                            <a href="{{ route('order.report.order.details', $order->id) }}" 
                               class="btn btn-sm btn-outline-info" 
                               title="View Details">
                              <i class="bi bi-eye"></i>
                            </a>
                          </div>
                        </td>
                      </tr>
                    @empty
                      
                    @endforelse
                  </tbody>
                </table>
              </div>

              <!-- Pagination -->
              @if($orders->hasPages())
                <div class="mt-4">
                  {{ $orders->appends(request()->query())->links() }}
                </div>
              @endif

              <!-- Export Summary -->
              <div class="row mt-4">
                <div class="col-md-12">
                  <div class="alert alert-info">
                    <div class="row">
                      <div class="col-md-3">
                        <strong>Date Range:</strong><br>
                        {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}
                      </div>
                      <div class="col-md-3">
                        <strong>Applied Filters:</strong><br>
                        Type: {{ $orderType == 'all' || !$orderType ? 'All' : ucfirst(strtolower($orderType)) }}<br>
                        Method: {{ $paymentMethod == 'all' || !$paymentMethod ? 'All' : ucfirst($paymentMethod) }}<br>
                        Status: {{ $paymentStatus == 'all' || !$paymentStatus ? 'All' : ucfirst($paymentStatus) }}
                      </div>
                      <div class="col-md-3">
                        <strong>Showing:</strong><br>
                        {{ $orders->firstItem() ?? 0 }}-{{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} orders
                      </div>
                      <div class="col-md-3 text-end">
                        <button id="exportSummary" class="btn btn-outline-success">
                          <i class="bi bi-download"></i> Export Summary
                        </button>
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
  const table = $('#ordersTable').DataTable({
    "paging": false,
    "searching": true,
    "ordering": true,
    "info": false,
    "dom": 'Bfrtip',
    "buttons": [
      {
        extend: 'excel',
        text: '<i class="bi bi-file-excel"></i> Export Excel',
        className: 'btn btn-success btn-sm',
        title: 'Order Management Report - {{ \Carbon\Carbon::now()->format("d M Y") }}',
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
          format: {
            body: function(data, row, column, node) {
              // Clean up HTML content for export
              let text = '';
              
              if (column === 1) { // Order ID
                // Get text content only
                text = $(node).find('strong').text() || $(node).text() || data;
              }
              else if (column === 2) { // Customer Details
                // Get customer name and phone
                const name = $(node).find('strong').text() || '';
                const phone = $(node).find('small.text-muted').text() || '';
                text = `${name} ${phone}`.trim();
              }
              else if (column === 3) { // Order Type
                // Clean order type and table name
                const orderType = $(node).text().includes('Dine-in') ? 'Dine-in' : 'Takeaway';
                const tableName = $(node).find('br').next().text().trim();
                if (tableName) {
                  text = `${orderType} - ${tableName}`;
                } else {
                  text = orderType;
                }
              }
              else if (column === 4 || column === 5 || column === 6) { // Amount columns
                // Remove ₹ symbol and get first line
                text = $(node).find('strong').text() || data;
                text = text.replace('₹', '').replace(/[^\d.-]/g, '').trim();
              }
              else if (column === 7) { // Payment Method
                // Get just the method text
                text = $(node).find('span:not(.payment-method-icon)').last().text() || 
                       $(node).text().replace(/\s+/g, ' ').trim();
              }
              else if (column === 8) { // Payment Status
                // Clean status badge
                text = $(node).text().replace(/\s+/g, ' ').replace(/[^\w\s]/gi, '').trim();
              }
              else if (column === 9) { // Date & Time
                // Combine date and time
                const date = $(node).find('div').text() || '';
                const time = $(node).find('small.text-muted').text() || '';
                text = `${date} ${time}`.trim();
              }
              else {
                // For other columns, just get text content
                text = $(node).text().trim();
              }
              
              return text || data;
            }
          }
        },
        customize: function(xlsx) {
          var sheet = xlsx.xl.worksheets['sheet1.xml'];
          
          // Add summary row at the end
          const totalRows = $('row', sheet).length;
          const summaryRow = $('<row></row>').append(
            '<c t="inlineStr" s="1"><is><t>Summary</t></is></c>',
            '<c t="inlineStr"></c>',
            '<c t="inlineStr"></c>',
            '<c t="inlineStr"></c>',
            '<c t="inlineStr"><is><t>Total Revenue: ₹{{ number_format($summary["total_revenue"], 2) }}</t></is></c>',
            '<c t="inlineStr"><is><t>Amount Collected: ₹{{ number_format($summary["total_collected"], 2) }}</t></is></c>',
            '<c t="inlineStr"><is><t>Pending Amount: ₹{{ number_format($summary["pending_amount"], 2) }}</t></is></c>',
            '<c t="inlineStr"></c>',
            '<c t="inlineStr"></c>',
            '<c t="inlineStr"></c>',
            '<c t="inlineStr"></c>'
          );
          
          $('sheetData', sheet).append(summaryRow);
          
          // Apply bold style to summary row
          $('row:last c', sheet).attr('s', '2');
        }
      },
      
      {
        extend: 'print',
        text: '<i class="bi bi-printer"></i> Print',
        className: 'btn btn-primary btn-sm',
        title: '<h3>Order Management Report</h3>' + 
               '<p>Date Range: {{ \Carbon\Carbon::parse($fromDate)->format("d M Y") }} to {{ \Carbon\Carbon::parse($toDate)->format("d M Y") }}</p>' +
               '<p>Filters: Type: {{ $orderType == "all" || !$orderType ? "All" : ucfirst(strtolower($orderType)) }} | ' +
               'Method: {{ $paymentMethod == "all" || !$paymentMethod ? "All" : ucfirst($paymentMethod) }} | ' +
               'Status: {{ $paymentStatus == "all" || !$paymentStatus ? "All" : ucfirst($paymentStatus) }}</p>',
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
          format: {
            body: function(data, row, column, node) {
              // Use same cleaning logic
              let text = '';
              
              if (column === 1) {
                text = $(node).find('strong').text() || $(node).text() || data;
              }
              else if (column === 2) {
                const name = $(node).find('strong').text() || '';
                const phone = $(node).find('small.text-muted').text() || '';
                text = `${name} ${phone}`.trim();
              }
              else if (column === 3) {
                const orderType = $(node).text().includes('Dine-in') ? 'Dine-in' : 'Takeaway';
                const tableName = $(node).find('br').next().text().trim();
                if (tableName) {
                  text = `${orderType} - ${tableName}`;
                } else {
                  text = orderType;
                }
              }
              else if (column === 4 || column === 5 || column === 6) {
                text = $(node).find('strong').text() || data;
                text = text.replace('₹', '').replace(/[^\d.-]/g, '').trim();
              }
              else if (column === 7) {
                text = $(node).find('span:not(.payment-method-icon)').last().text() || 
                       $(node).text().replace(/\s+/g, ' ').trim();
              }
              else if (column === 8) {
                text = $(node).text().replace(/\s+/g, ' ').replace(/[^\w\s]/gi, '').trim();
              }
              else if (column === 9) {
                const date = $(node).find('div').text() || '';
                const time = $(node).find('small.text-muted').text() || '';
                text = `${date} ${time}`.trim();
              }
              else {
                text = $(node).text().trim();
              }
              
              return text || data;
            }
          }
        },
        customize: function(win) {
          $(win.document.body).find('table').addClass('table table-bordered');
          $(win.document.body).find('h1').css('text-align', 'center');
          
          // Add summary at the end
          $(win.document.body).append(
            '<div style="margin-top: 20px; padding: 15px; border-top: 2px solid #333;">' +
            '<h4>Summary</h4>' +
            '<div class="row">' +
            '<div style="width: 50%; float: left;">' +
            '<p><strong>Date Range:</strong> {{ \Carbon\Carbon::parse($fromDate)->format("d M Y") }} to {{ \Carbon\Carbon::parse($toDate)->format("d M Y") }}</p>' +
            '<p><strong>Total Orders:</strong> {{ $summary["total_orders"] }}</p>' +
            '<p><strong>Total Revenue:</strong> ₹{{ number_format($summary["total_revenue"], 2) }}</p>' +
            '<p><strong>Amount Collected:</strong> ₹{{ number_format($summary["total_collected"], 2) }}</p>' +
            '<p><strong>Pending Amount:</strong> ₹{{ number_format($summary["pending_amount"], 2) }}</p>' +
            '</div>' +
            '<div style="width: 50%; float: left;">' +
            '<p><strong>Applied Filters:</strong></p>' +
            '<p>Type: {{ $orderType == "all" || !$orderType ? "All" : ucfirst(strtolower($orderType)) }}</p>' +
            '<p>Method: {{ $paymentMethod == "all" || !$paymentMethod ? "All" : ucfirst($paymentMethod) }}</p>' +
            '<p>Status: {{ $paymentStatus == "all" || !$paymentStatus ? "All" : ucfirst($paymentStatus) }}</p>' +
            '</div>' +
            '<div style="clear: both;"></div>' +
            '</div>'
          );
        }
      }
    ]
  });

  // Export Excel button click
  $('#exportExcel').click(function() {
    table.button('.buttons-excel').trigger();
  });

  // Print button click
  $('#printReport').click(function() {
    table.button('.buttons-print').trigger();
  });

  // Export Summary button
  $('#exportSummary').click(function() {
    // Create a summary report
    const summaryData = [
      ['Order Management Report Summary'],
      ['Date Range', '{{ \Carbon\Carbon::parse($fromDate)->format("d M Y") }} to {{ \Carbon\Carbon::parse($toDate)->format("d M Y") }}'],
      ['Total Orders', '{{ $summary["total_orders"] }}'],
      ['Total Revenue', '₹{{ number_format($summary["total_revenue"], 2) }}'],
      ['Amount Collected', '₹{{ number_format($summary["total_collected"], 2) }}'],
      ['Pending Amount', '₹{{ number_format($summary["pending_amount"], 2) }}'],
      ['Dine-in Orders', '{{ $summary["dine_in_count"] }}'],
      ['Takeaway Orders', '{{ $summary["takeaway_count"] }}'],
      ['Paid Orders', '{{ $summary["paid_count"] }}'],
      ['Pending Orders', '{{ $summary["pending_count"] }}'],
      ['Misc Orders', '{{ $summary["miscorder_count"] }}'],
      [],
      ['Applied Filters'],
      ['Order Type', '{{ $orderType == "all" || !$orderType ? "All" : ucfirst(strtolower($orderType)) }}'],
      ['Payment Method', '{{ $paymentMethod == "all" || !$paymentMethod ? "All" : ucfirst($paymentMethod) }}'],
      ['Payment Status', '{{ $paymentStatus == "all" || !$paymentStatus ? "All" : ucfirst($paymentStatus) }}']
    ];

    // Convert to CSV
    const csvContent = summaryData.map(row => row.join(',')).join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'order_summary_{{ \Carbon\Carbon::now()->format("Y-m-d") }}.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  });
});
</script>

</body>
</html>