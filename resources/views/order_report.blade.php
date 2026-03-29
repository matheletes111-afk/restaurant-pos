<!DOCTYPE html>
<html lang="en">
<head>
  <title>Admin - Order Report</title>
  @include('includes.style')
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
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
                <h5 class="m-b-10">Paid Order Report</h5>
              </div>
              <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item" aria-current="page">Order Report</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <!-- Breadcrumb end -->

      <div class="row">
        <div class="col-sm-12">
          <div class="card">
            @include('includes.message')
            <div class="card-header">
              <h5>Filter Orders</h5>
            </div>

            <div class="card-body">
              <form method="GET" action="{{ route('order.report') }}" class="row g-3 align-items-end mb-4">
                <div class="col-md-4">
                  <label>From Date</label>
                  <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control">
                </div>
                <div class="col-md-4">
                  <label>To Date</label>
                  <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control">
                </div>
                <div class="col-md-4">
                  <button type="submit" class="btn btn-primary w-100">
                    <i class="fa fa-filter"></i> Apply Filter
                  </button>
                </div>
              </form>

              <div class="dt-responsive table-responsive">
                <table id="orderReportTable" class="table table-striped table-bordered nowrap">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Order ID</th>
                      <th>Order Type</th>
                      <th>Customer Name</th>
                      <th>Total (₹)</th>
                      <th>GST (₹)</th>
                      <th>Final (₹)</th>
                      <th>Payment Status</th>
                      <th>Created At</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php
                      $total = 0;
                      $gst_total = 0;
                      $final_total = 0;
                    @endphp

                    @forelse($orders as $key => $order)
                      @php
  $total = $orders->sum('total_amount');
  $gst_total = $orders->sum('gst_amount');
  $final_total = $orders->sum('grand_total');
@endphp
                      <tr>
                        <td>{{ $key + 1 }}</td>
                        <td><a href="{{route('order.report.order.details',@$order->id)}}"><strong>{{ $order->order_id }}</strong></a></td>
                        <td>@if(@$order->order_type=="DINE_IN") Dine In  - {{@$order->table->name}} @else Takeway @endif</td>
                        <td>{{ $order->customer_name ?? 'N/A' }}</td>
                        <td>₹{{ number_format($order->total_amount, 2) }}</td>
                        <td>₹{{ number_format($order->gst_amount, 2) }}</td>
                        <td><strong>₹{{ number_format($order->final_amount, 2) }}</strong></td>
                        <td><span class="badge bg-success">{{ $order->payment_status }}</span></td>
                        <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, h:i A') }}</td>
                      </tr>
                    @empty
                      
                    @endforelse
                  </tbody>
                </table>
              </div>

              @if(count($orders) > 0)
              <div class="mt-3">
                <div class="row">
                  <div class="col-md-4">
                    <div class="alert alert-secondary text-center"><b>Total:</b> ₹{{ number_format($total, 2) }}</div>
                  </div>
                  <div class="col-md-4">
                    <div class="alert alert-info text-center"><b>GST:</b> ₹{{ number_format($gst_total, 2) }}</div>
                  </div>
                  <div class="col-md-4">
                    <div class="alert alert-success text-center"><b>Final:</b> ₹{{ number_format($final_total, 2) }}</div>
                  </div>
                </div>
              </div>
              @endif

            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- JS -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  @include('includes.script')

  <script>
    $(document).ready(function() {
      // Safely destroy and reinitialize DataTable after filter reload
      if ($.fn.DataTable.isDataTable('#orderReportTable')) {
        $('#orderReportTable').DataTable().destroy();
      }

      $('#orderReportTable').DataTable({
        "order": [[0, "asc"]],
        "pageLength": 10
      });
    });
  </script>

</body>
</html>
