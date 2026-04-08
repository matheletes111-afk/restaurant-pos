<!DOCTYPE html>
<html lang="en">
<head>
  <title>Place Order - Select Table or Takeaway</title>
  @include('includes.style')
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
  
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: #f0f2f8;
    }

    /* Page Header Enhancement */
    .page-header-custom {
      background: linear-gradient(135deg, #1e2a3a 0%, #0f172a 100%);
      border-radius: 28px;
      padding: 24px 32px;
      margin-bottom: 32px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      position: relative;
      overflow: hidden;
    }

    .page-header-custom::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -20%;
      width: 300px;
      height: 300px;
      background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
      border-radius: 50%;
    }

    .page-header-custom h5 {
      font-size: 1.75rem;
      font-weight: 700;
      color: white;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .page-header-custom h5 i {
      font-size: 2rem;
      background: linear-gradient(135deg, #f59e0b, #ef4444);
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
    }

    .stats-badge {
      background: rgba(255,255,255,0.15);
      backdrop-filter: blur(10px);
      border-radius: 40px;
      padding: 8px 20px;
      display: inline-flex;
      align-items: center;
      gap: 12px;
      margin-top: 16px;
    }

    .stats-badge span {
      color: white;
      font-size: 0.85rem;
      font-weight: 500;
    }

    .stats-badge .stat-number {
      font-weight: 800;
      font-size: 1.1rem;
      background: rgba(255,255,255,0.25);
      padding: 2px 12px;
      border-radius: 30px;
    }

    /* Card Design - Modern Glassmorphism Style */
    .table-card {
      border-radius: 24px;
      min-height: 220px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      transition: all 0.35s cubic-bezier(0.2, 0.9, 0.4, 1.1);
      text-align: center;
      position: relative;
      overflow: hidden;
      cursor: pointer;
      border: none;
      backdrop-filter: blur(0px);
    }

    .table-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: left 0.5s;
    }

    .table-card:hover::before {
      left: 100%;
    }

    .table-card:hover {
      transform: translateY(-10px) scale(1.02);
      box-shadow: 0 25px 45px -12px rgba(0, 0, 0, 0.35);
    }

    .table-card .card-body {
      padding: 1.8rem 1.5rem;
    }

    .table-icon {
      width: 70px;
      height: 70px;
      background: rgba(255,255,255,0.2);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 16px auto;
      transition: all 0.3s;
    }

    .table-card:hover .table-icon {
      transform: scale(1.1);
      background: rgba(255,255,255,0.3);
    }

    .table-icon i {
      font-size: 2rem;
    }

    .card-title {
      font-weight: 800;
      font-size: 1.3rem;
      margin-bottom: 8px;
      letter-spacing: -0.3px;
    }

    .status-label {
      font-weight: 600;
      font-size: 0.8rem;
      padding: 6px 16px;
      border-radius: 40px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: rgba(0,0,0,0.2);
      backdrop-filter: blur(4px);
    }

    /* Takeaway Card Special */
    .takeaway-card {
      background: linear-gradient(135deg, #8B5CF6 0%, #6D28D9 100%);
      color: #fff;
      position: relative;
    }

    .takeaway-card .table-icon {
      background: rgba(255,255,255,0.25);
    }

    .takeaway-badge {
      position: absolute;
      top: 16px;
      right: 16px;
      background: rgba(255,215,0,0.3);
      border-radius: 30px;
      padding: 4px 12px;
      font-size: 0.7rem;
      font-weight: 600;
    }

    /* Available Tables */
    .available-table {
      background: linear-gradient(135deg, #10B981 0%, #059669 100%);
      color: #fff;
    }

    /* Occupied Tables */
    .occupied-table {
      background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
      color: #fff;
    }

    .occupied-table .status-label {
      background: rgba(0,0,0,0.25);
    }

    /* Inactive Tables */
    .inactive-table {
      background: linear-gradient(135deg, #6B7280 0%, #4B5563 100%);
      color: #fff;
      opacity: 0.7;
      cursor: not-allowed;
    }

    .inactive-table:hover {
      transform: translateY(0);
      opacity: 0.7;
    }

    /* Customer info on occupied table */
    .customer-info {
      font-size: 0.7rem;
      margin-top: 8px;
      opacity: 0.9;
      background: rgba(0,0,0,0.15);
      padding: 4px 10px;
      border-radius: 30px;
      display: inline-block;
    }

    /* Grid Enhancements */
    .row-cards {
      margin: 0 -12px;
    }

    .col-card {
      padding: 0 12px;
      margin-bottom: 24px;
    }

    /* Empty State */
    .empty-state {
      text-align: center;
      padding: 60px 20px;
      background: white;
      border-radius: 32px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }

    .empty-state i {
      font-size: 4rem;
      color: #cbd5e1;
      margin-bottom: 1rem;
    }

    .empty-state p {
      color: #64748b;
      font-size: 1rem;
    }

    /* Animation */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .table-card {
      animation: fadeInUp 0.5s ease backwards;
    }

    .table-card:nth-child(1) { animation-delay: 0.05s; }
    .table-card:nth-child(2) { animation-delay: 0.1s; }
    .table-card:nth-child(3) { animation-delay: 0.15s; }
    .table-card:nth-child(4) { animation-delay: 0.2s; }
    .table-card:nth-child(5) { animation-delay: 0.25s; }
    .table-card:nth-child(6) { animation-delay: 0.3s; }

    /* Responsive */
    @media (max-width: 768px) {
      .page-header-custom h5 {
        font-size: 1.3rem;
      }
      .table-card {
        min-height: 180px;
      }
      .table-icon {
        width: 55px;
        height: 55px;
      }
      .card-title {
        font-size: 1.1rem;
      }
    }

    /* Floating decoration */
    .bg-decoration {
      position: fixed;
      bottom: 0;
      right: 0;
      width: 400px;
      height: 400px;
      background: radial-gradient(circle, rgba(139,92,246,0.08) 0%, transparent 70%);
      pointer-events: none;
      z-index: 0;
    }

    .pc-content {
      position: relative;
      z-index: 1;
    }
  </style>
</head>

<body>
  @include('includes.sidebar')

  <div class="pc-container">
    <div class="pc-content">
      
      <!-- Enhanced Header with Stats -->
      <div class="page-header-custom">
        <h5>
          <i class="fas fa-utensils"></i> 
          Select Dining Option
        </h5>
        <div class="stats-badge">
          <span><i class="fas fa-chair"></i> Total Tables</span>
          <span class="stat-number">{{ count($data) }}</span>
          <span class="mx-2">•</span>
          <span><i class="fas fa-circle" style="color:#10B981; font-size: 0.6rem;"></i> Available</span>
          <span class="stat-number">{{ $data->where('table_status', 'AVAILABLE')->count() }}</span>
          <span class="mx-2">•</span>
          <span><i class="fas fa-circle" style="color:#F59E0B; font-size: 0.6rem;"></i> Occupied</span>
          <span class="stat-number">{{ $data->where('table_status', 'OCCUPIED')->count() }}</span>
        </div>
      </div>

      <div class="row row-cards">
        <!-- Takeaway Card - Enhanced -->
        @if(auth()->user()->role_type=="Manager" || auth()->user()->role_type=="Cashier" || auth()->user()->role_type=="ADMIN")
        <div class="col-md-3 col-sm-6 col-card">
          <a href="{{ route('order.create', 'TAKEAWAY') }}" style="text-decoration:none;">
            <div class="table-card takeaway-card">
              <div class="takeaway-badge">
                <i class="fas fa-bolt"></i> Fast Order
              </div>
              <div class="card-body d-flex flex-column justify-content-center align-items-center">
                <div class="table-icon">
                  <i class="fas fa-shopping-bag fa-2x"></i>
                </div>
                <h5 class="card-title">Takeaway</h5>
                <span class="status-label">
                  <i class="fas fa-clock"></i> Ready in 15-20 min
                </span>
                <small style="margin-top: 12px; opacity:0.8;">
                  <i class="fas fa-arrow-right"></i> Click to start order
                </small>
              </div>
            </div>
          </a>
        </div>
        @endif

        <!-- Loop through all tables with enhanced design -->
        @foreach($data as $table)
          @php
              $bgClass = '';
              $statusText = '';
              $url = '#';
              $style = '';
              $iconClass = 'fa-utensils';
              $customerName = '';

              // if table is inactive
              if($table->table_status == 'INACTIVE') {
                  $bgClass = 'inactive-table';
                  $statusText = 'Under Maintenance';
                  $style = 'pointer-events:none; opacity:0.6; cursor:not-allowed;';
                  $iconClass = 'fa-tools';

              // if table is occupied
              } elseif($table->table_status == 'OCCUPIED' && $table->order) {
                  $bgClass = 'occupied-table';
                  $statusText = 'Occupied';
                  $customerName = $table->order->customer_name ?? 'Guest';
                  $url = route('order.edit', $table->order_id);
                  $iconClass = 'fa-users';

              // if available
              } else {
                  $bgClass = 'available-table';
                  $statusText = 'Available • Ready';
                  $url = route('order.create', $table->id);
                  $iconClass = 'fa-chair';
              }
          @endphp

          <div class="col-md-3 col-sm-6 col-card">
            <a href="{{ $url }}" style="text-decoration:none; {{ $style }}">
              <div class="table-card {{ $bgClass }}">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                  <div class="table-icon">
                    <i class="fas {{ $iconClass }} fa-2x"></i>
                  </div>
                  <h5 class="card-title">{{ $table->name }}</h5>
                  
                  @if($table->table_status == 'OCCUPIED' && $table->order)
                    <span class="status-label">
                      <i class="fas fa-user-check"></i> {{ $customerName }}
                    </span>
                    <div class="customer-info">
                      <i class="fas fa-hourglass-half"></i> Order in progress
                    </div>
                  @elseif($table->table_status == 'INACTIVE')
                    <span class="status-label">
                      <i class="fas fa-wrench"></i> {{ $statusText }}
                    </span>
                  @else
                    <span class="status-label">
                      <i class="fas fa-check-circle"></i> {{ $statusText }}
                    </span>
                    <small style="margin-top: 10px; opacity:0.85;">
                      <i class="fas fa-plus-circle"></i> New Order
                    </small>
                  @endif
                </div>
              </div>
            </a>
          </div>
        @endforeach
      </div>

      <!-- Empty State (if no tables exist) -->
      @if(count($data) == 0)
      <div class="empty-state">
        <i class="fas fa-chair"></i>
        <p>No tables configured yet. Please contact administrator to add tables.</p>
      </div>
      @endif

    </div>
  </div>

  <!-- Background Decoration -->
  <div class="bg-decoration"></div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  @include('includes.script')

  <script>
    // Add ripple effect on card click
    $(document).ready(function() {
      $('.table-card').on('click', function(e) {
        let target = $(this);
        if(target.closest('a').attr('style')?.includes('pointer-events:none')) {
          e.preventDefault();
          // Optional: Show toast message for inactive tables
          if(target.hasClass('inactive-table')) {
            alert('This table is currently under maintenance. Please choose another option.');
          }
        }
      });

      // Animate stats on load
      $('.stat-number').each(function() {
        let final = $(this).text();
        $(this).text('0');
        let current = 0;
        let interval = setInterval(() => {
          if(current >= parseInt(final)) {
            clearInterval(interval);
            $(this).text(final);
          } else {
            current++;
            $(this).text(current);
          }
        }, 20);
      });
    });
  </script>

  <style>
    /* Additional polish */
    a[style*="pointer-events:none"] {
      cursor: default;
    }
    
    .table-card {
      position: relative;
    }
    
    .table-card .card-body {
      z-index: 2;
      position: relative;
    }
    
    /* Glow effect on hover for available tables */
    .available-table:hover {
      box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.4), 0 20px 35px -10px rgba(0,0,0,0.3);
    }
    
    .occupied-table:hover {
      box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.4), 0 20px 35px -10px rgba(0,0,0,0.3);
    }
    
    .takeaway-card:hover {
      box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.4), 0 20px 35px -10px rgba(0,0,0,0.3);
    }
    
    /* Responsive grid improvements */
    @media (max-width: 1200px) {
      .col-md-3 {
        flex: 0 0 33.333%;
        max-width: 33.333%;
      }
    }
    
    @media (max-width: 768px) {
      .col-md-3 {
        flex: 0 0 50%;
        max-width: 50%;
      }
      .stats-badge {
        flex-wrap: wrap;
        gap: 8px;
      }
    }
    
    @media (max-width: 480px) {
      .col-md-3 {
        flex: 0 0 100%;
        max-width: 100%;
      }
    }
    
    /* Scrollbar styling */
    ::-webkit-scrollbar {
      width: 8px;
    }
    
    ::-webkit-scrollbar-track {
      background: #e2e8f0;
      border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb {
      background: #94a3b8;
      border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
      background: #64748b;
    }
  </style>
</body>
</html>