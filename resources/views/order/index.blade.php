<!DOCTYPE html>
<html lang="en">
<head>
  <title>Place Order</title>
  @include('includes.style')
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>

<body>
  @include('includes.sidebar')

  <div class="pc-container">
    <div class="pc-content">
      <div class="page-header mb-4">
        <h5 class="m-b-10">
          <i class="fas fa-utensils me-2"></i>Table Selection / Takeaway
        </h5>
      </div>

      <div class="row">
        <!-- Takeaway Card -->
        @if(auth()->user()->role_type=="Manager" || auth()->user()->role_type=="Cashier" || auth()->user()->role_type=="ADMIN")
        <div class="col-md-3 mb-4">
          <a href="{{ route('order.create', 'TAKEAWAY') }}" style="text-decoration:none;">
            <div class="card table-card takeaway-card text-center shadow-lg">
              <div class="card-body d-flex flex-column justify-content-center align-items-center">
                <h4 class="card-title mb-2">
                  <i class="fas fa-shopping-bag"></i> Takeaway
                </h4>
                <span class="status-label bg-primary text-white px-3 py-1 rounded-pill">No Table</span>
              </div>
            </div>
          </a>
        </div>
        @endif

        <!-- Loop through all tables -->
        @foreach($data as $table)
          @php
              $bgClass = '';
              $statusText = '';
              $url = '#';
              $style = '';

              // if table is inactive
              if($table->table_status == 'INACTIVE') {
                  $bgClass = 'inactive-table';
                  $statusText = 'Inactive';
                  $style = 'pointer-events:none; opacity:0.6;';

              // if table is occupied
              } elseif($table->table_status == 'OCCUPIED' && $table->order) {
                  $bgClass = 'occupied-table';
                  $statusText = $table->order->customer_name ?? 'Occupied';
                  $url = route('order.edit', $table->order_id);

              // if available
              } else {
                  $bgClass = 'available-table';
                  $statusText = 'Available';
                  $url = route('order.create', $table->id);
              }
          @endphp

          <div class="col-md-3 mb-4">
            <a href="{{ $url }}" style="text-decoration:none; {{ $style }}">
              <div class="card table-card shadow-lg {{ $bgClass }} text-center">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                  <div class="table-icon mb-3">
                    <i class="fas fa-utensils fa-2x"></i>
                  </div>
                  <h5 class="card-title mb-2">{{ $table->name }}</h5>
                  <span class="status-label px-3 py-1 rounded-pill">{{ $statusText }}</span>
                </div>
              </div>
            </a>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  @include('includes.script')

  <style>
    body { font-family: 'Poppins', sans-serif; }

    .table-card {
        border-radius: 20px;
        min-height: 180px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        text-align: center;
        color: #fff;
    }
    .table-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.25);
    }
    .takeaway-card {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: #fff;
    }
    .available-table {
        background: linear-gradient(135deg, #28a745, #71d06b);
        color: #fff;
    }
    .occupied-table {
        background: linear-gradient(135deg, #ffc107, #ffdd57);
        color: #333;
    }
    .inactive-table {
        background: linear-gradient(135deg, #dc3545, #e57373);
        color: #fff;
    }
    .status-label {
        font-weight: 600;
        font-size: 0.9rem;
    }
  </style>
</body>
</html>
