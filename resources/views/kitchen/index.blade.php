<!DOCTYPE html>
<html lang="en">
<head>
  <title>Kitchen Panel</title>
  @include('includes.style')
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    :root {
      --page-bg: #f8fafc;
      --primary-green: #009d1a;
      --light-green: #e8f7eb;
      --text-primary: #1e293b;
      --text-secondary: #64748b;
      
      --status-pending: #f59e0b;
      --status-pending-bg: #fffbeb;
      --status-pending-text: #b45309;
      
      --status-cooking: #3b82f6;
      --status-cooking-bg: #eff6ff;
      --status-cooking-text: #1d4ed8;
      
      --status-done: #10b981;
      --status-done-bg: #ecfdf5;
      --status-done-text: #047857;
    }

    body {
      background: var(--page-bg);
      color: var(--text-primary);
      font-family: 'Public Sans', sans-serif;
    }

    .kitchen-container {
      padding: 15px 0;
    }

    /* Premium Filter Card */
    .filter-section {
      background: #ffffff;
      border-radius: 16px;
      padding: 24px;
      margin-bottom: 25px;
      border: 1px solid rgba(0, 0, 0, 0.05);
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
    }

    .filter-row {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      align-items: flex-end;
    }

    .filter-group {
      flex: 1 1 200px;
    }

    .filter-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 700;
      font-size: 0.75rem;
      color: #64748b;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }

    .filter-group input,
    .filter-group select {
      width: 100%;
      padding: 10px 14px;
      border: 1px solid #e2e8f0;
      background-color: #f8fafc;
      border-radius: 10px;
      font-size: 0.9rem;
      color: #1e293b;
      transition: all 0.2s ease;
    }

    .filter-group input:focus,
    .filter-group select:focus {
      background-color: #ffffff;
      border-color: var(--primary-green);
      outline: none;
      box-shadow: 0 0 0 3px rgba(0, 157, 26, 0.1);
    }

    .btn-filter {
      padding: 10px 28px;
      background: linear-gradient(135deg, #009d1a 0%, #00bc20 100%);
      color: white;
      border: none;
      border-radius: 30px;
      font-weight: 700;
      cursor: pointer;
      box-shadow: 0 4px 15px rgba(0, 157, 26, 0.2);
      transition: all 0.3s ease;
      height: 42px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    .btn-filter:hover {
      background: linear-gradient(135deg, #00bc20 0%, #009d1a 100%);
      transform: translateY(-1px);
      box-shadow: 0 6px 20px rgba(0, 157, 26, 0.3);
    }

    .btn-reset {
      padding: 10px 24px;
      background: #e2e8f0;
      color: #475569;
      border: none;
      border-radius: 30px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      height: 42px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      text-decoration: none;
    }

    .btn-reset:hover {
      background: #cbd5e1;
      color: #1e293b;
    }

    /* Date Summary Box */
    .date-summary {
      margin: 20px 0;
      padding: 14px 20px;
      background: #eff6ff;
      border-radius: 12px;
      font-size: 0.9rem;
      color: #1d4ed8;
      display: flex;
      align-items: center;
      gap: 12px;
      border-left: 4px solid var(--status-cooking);
    }

    .order-count {
      margin-left: auto;
      background: var(--primary-green);
      color: white;
      padding: 6px 16px;
      border-radius: 30px;
      font-weight: 700;
      font-size: 0.8rem;
      box-shadow: 0 4px 10px rgba(0, 157, 26, 0.15);
    }

    /* Status Filters */
    .filter-buttons {
      margin: 24px 0;
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    .filter-btn {
      padding: 8px 24px;
      background: #ffffff;
      border: 1px solid #e2e8f0;
      color: #64748b;
      border-radius: 30px;
      font-size: 0.85rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.25s ease;
    }

    .filter-btn.active,
    .filter-btn:hover {
      background: linear-gradient(135deg, #009d1a, #00c72c);
      color: white;
      border-color: transparent;
      box-shadow: 0 4px 12px rgba(0, 157, 26, 0.25);
    }

    /* Kitchen Grid layout */
    .kitchen-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px;
      padding: 10px 0;
    }

    /* Premium KOT Cards */
    .order-card {
      background: #ffffff;
      border-radius: 16px;
      padding: 20px;
      border: 1px solid rgba(0, 0, 0, 0.05);
      box-shadow: 0 8px 30px rgba(15, 23, 42, 0.03);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      display: flex;
      flex-direction: column;
      position: relative;
      overflow: hidden;
    }

    .order-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      z-index: 10;
    }

    .order-card.PENDING::before {
      background: var(--status-pending);
    }
    .order-card.COOKING::before {
      background: var(--status-cooking);
    }
    .order-card.DONE::before {
      background: var(--status-done);
    }

    .order-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 35px rgba(15, 23, 42, 0.08);
      border-color: rgba(0, 157, 26, 0.15);
    }

    .card-header {
      margin-bottom: 16px;
      padding-bottom: 12px;
      border-bottom: 1px solid #f1f5f9;
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .order-id {
      font-size: 0.85rem;
      font-weight: 800;
      color: #0f172a;
      margin: 0;
    }

    .kot-badge {
      display: inline-flex;
      padding: 4px 12px;
      border-radius: 30px;
      font-size: 0.75rem;
      font-weight: 800;
      background-color: #0f172a;
      color: #ffffff;
      align-self: flex-start;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .order-time {
      font-size: 0.75rem;
      color: #64748b;
      display: flex;
      align-items: center;
      gap: 4px;
    }

    /* Product layout */
    .product-info {
      margin-bottom: 15px;
      flex-grow: 1;
    }

    .product-name {
      font-size: 1.1rem;
      font-weight: 800;
      color: #0f172a;
      margin-bottom: 10px;
    }

    .food-type {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 3px 10px;
      border-radius: 30px;
      font-size: 0.65rem;
      font-weight: 800;
      text-transform: uppercase;
      margin-bottom: 16px;
    }

    .food-type.VEG {
      background: #d1fae5;
      color: #065f46;
    }

    .food-type.NON-VEG {
      background: #fee2e2;
      color: #991b1b;
    }

    /* Card Details Rows */
    .info-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 10px;
      padding-bottom: 8px;
      border-bottom: 1px dashed #f1f5f9;
    }

    .info-row:last-of-type {
      border-bottom: none;
      margin-bottom: 0;
    }

    .info-label {
      font-size: 0.8rem;
      color: #64748b;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .info-value {
      font-size: 0.85rem;
      font-weight: 700;
      color: #0f172a;
    }

    .quantity-badge {
      background: linear-gradient(135deg, #009d1a, #00c72c);
      color: white;
      width: 28px;
      height: 28px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.85rem;
      font-weight: 800;
      box-shadow: 0 4px 10px rgba(0, 157, 26, 0.2);
    }

    .table-info {
      display: inline-flex;
      align-items: center;
      padding: 4px 12px;
      background: #f1f5f9;
      border-radius: 30px;
      font-size: 0.75rem;
      font-weight: 700;
      color: #475569;
    }

    /* Note Container */
    .order-note-box {
      margin-top: 12px;
      padding: 10px 14px;
      background: #fffbeb;
      border-radius: 10px;
      border: 1px solid #fef3c7;
      font-size: 0.8rem;
      color: #92400e;
      font-weight: 600;
      display: flex;
      align-items: start;
      gap: 8px;
    }

    /* Select Styles */
    .status-section {
      margin-top: auto;
      padding-top: 16px;
    }

    .status-select {
      width: 100%;
      border: 1px solid #e2e8f0 !important;
      border-radius: 10px !important;
      padding: 10px 14px !important;
      font-size: 0.85rem !important;
      font-weight: 700 !important;
      cursor: pointer;
      background: #f8fafc !important;
      color: #0f172a !important;
      transition: all 0.2s ease !important;
      outline: none;
    }

    .order-card.PENDING .status-select {
      border-left: 3px solid var(--status-pending) !important;
      color: var(--status-pending-text) !important;
    }

    .order-card.COOKING .status-select {
      border-left: 3px solid var(--status-cooking) !important;
      color: var(--status-cooking-text) !important;
    }

    .order-card.DONE .status-select {
      border-left: 3px solid var(--status-done) !important;
      color: var(--status-done-text) !important;
    }

    .status-select:focus {
      border-color: var(--primary-green) !important;
      background: #ffffff !important;
    }

    /* Empty view */
    .empty-state {
      padding: 80px 20px;
      text-align: center;
      background: #ffffff;
      border-radius: 16px;
      border: 2px dashed #e2e8f0;
      grid-column: 1 / -1;
      box-shadow: 0 4px 15px rgba(0,0,0,0.01);
    }

    .empty-state i {
      color: #cbd5e1;
      margin-bottom: 20px;
    }

    .empty-state h4 {
      font-weight: 800;
      color: #0f172a;
      margin-bottom: 8px;
    }

    .empty-state p {
      color: #64748b;
    }

    /* Notification box */
    .notification {
      position: fixed;
      top: 24px;
      right: 24px;
      min-width: 280px;
      padding: 16px 24px;
      color: #ffffff;
      font-size: 0.9rem;
      font-weight: 700;
      border-radius: 12px;
      z-index: 99999;
      box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15);
      animation: slideInToast 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    @keyframes slideInToast {
      from {
        transform: translateY(-20px) scale(0.95);
        opacity: 0;
      }
      to {
        transform: translateY(0) scale(1);
        opacity: 1;
      }
    }

    /* Auto-refresh indicator styling */
    .refresh-info {
      font-size: 0.75rem;
      color: #64748b;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .pulse-dot {
      width: 8px;
      height: 8px;
      background-color: var(--primary-green);
      border-radius: 50%;
      display: inline-block;
      animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
      0% {
        transform: scale(0.9);
        box-shadow: 0 0 0 0 rgba(0, 157, 26, 0.4);
      }
      70% {
        transform: scale(1);
        box-shadow: 0 0 0 6px rgba(0, 157, 26, 0);
      }
      100% {
        transform: scale(0.9);
        box-shadow: 0 0 0 0 rgba(0, 157, 26, 0);
      }
    }
  </style>
</head>
<body>
@include('includes.sidebar')

<div class="pc-container">
  <div class="pc-content">
    
    <!-- Premium Header -->
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="m-b-10 text-dark font-weight-bold" style="font-size: 1.5rem;"><i class="fas fa-utensils text-success me-2"></i> Kitchen Orders</h5>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">View and manage real-time KOT and order preparations</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="refresh-info d-none d-sm-flex align-items-center gap-2">
                <span class="pulse-dot"></span>
                <span class="text-secondary font-weight-bold" style="font-size: 0.8rem; letter-spacing: 0.02em; text-transform: uppercase;">Real-time auto-sync</span>
            </div>
            <button id="refreshBtn" class="btn px-4 rounded-pill font-weight-bold text-white d-flex align-items-center gap-2" style="background: linear-gradient(135deg, #009d1a 0%, #00bc20 100%); border: none; box-shadow: 0 4px 15px rgba(0, 157, 26, 0.2); height: 40px; transition: all 0.3s ease;">
                <i class="fa fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>

    <div class="kitchen-container">
      <!-- Date Range Filter Section -->
      <div class="filter-section">
        <form method="GET" action="{{ route('manage.kitchen-panel') }}" id="filterForm">
          <div class="filter-row">
            <div class="filter-group">
              <label><i class="fa fa-calendar me-1"></i> From Date</label>
              <input type="date" name="from_date" value="{{ $from_date }}" max="{{ date('Y-m-d') }}">
            </div>
            <div class="filter-group">
              <label><i class="fa fa-calendar me-1"></i> To Date</label>
              <input type="date" name="to_date" value="{{ $to_date }}" max="{{ date('Y-m-d') }}">
            </div>
            <div class="filter-group">
              <label><i class="fa fa-filter me-1"></i> Status</label>
              <select name="status">
                <option value="all" {{ $selected_status == 'all' ? 'selected' : '' }}>All Status</option>
                <option value="PENDING" {{ $selected_status == 'PENDING' ? 'selected' : '' }}>⏳ Pending</option>
                <option value="COOKING" {{ $selected_status == 'COOKING' ? 'selected' : '' }}>👨‍🍳 Cooking</option>
                <option value="DONE" {{ $selected_status == 'DONE' ? 'selected' : '' }}>✅ Done</option>
              </select>
            </div>
            <div class="filter-group">
              <label><i class="fa fa-table me-1"></i> Table</label>
              <select name="table_id">
                <option value="">All Tables</option>
                @foreach($tables as $table)
                  <option value="{{ $table->id }}" {{ $selected_table == $table->id ? 'selected' : '' }}>{{ $table->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="filter-actions">
              <button type="submit" class="btn-filter">
                <i class="fa fa-search"></i> Apply Filters
              </button>
              <a href="{{ route('manage.kitchen-panel') }}" class="btn-reset">
                <i class="fa fa-undo"></i> Reset
              </a>
            </div>
          </div>
        </form>
      </div>

      <!-- Date Range Summary Box -->
      <div class="date-summary shadow-sm">
        <i class="fa fa-info-circle"></i>
        <span>Showing orders from <strong>{{ \Carbon\Carbon::parse($from_date)->format('d M Y') }}</strong> to <strong>{{ \Carbon\Carbon::parse($to_date)->format('d M Y') }}</strong></span>
        <span class="order-count">{{ count($OrderItems) }} items</span>
      </div>

      <!-- Filter Buttons -->
      <div class="filter-buttons">
        <button class="filter-btn {{ $selected_status == 'all' ? 'active' : '' }}" data-filter="all">All</button>
        <button class="filter-btn {{ $selected_status == 'PENDING' ? 'active' : '' }}" data-filter="PENDING">⏳ Pending</button>
        <button class="filter-btn {{ $selected_status == 'COOKING' ? 'active' : '' }}" data-filter="COOKING">👨‍🍳 Cooking</button>
        <button class="filter-btn {{ $selected_status == 'DONE' ? 'active' : '' }}" data-filter="DONE">✅ Done</button>
      </div>
      
      <!-- Orders Grid -->
      <div class="kitchen-grid">
        @forelse($OrderItems as $item)
        <div class="order-card {{ $item->order_status }}" data-status="{{ $item->order_status }}" id="card_{{ $item->id }}">
          <div class="card-header">
            <h6 class="order-id">ORDER #{{ $item->order->order_id }}</h6>
            @if($item->kot_no)
              <span class="kot-badge">
                KOT: {{ $item->kot_no }}
              </span>
            @endif
            <span class="order-time">
              <i class="fa fa-clock"></i> {{ $item->created_at->format('d M, h:i A') }}
            </span>
          </div>
          
          <div class="product-info">
            <h4 class="product-name">{{ $item->subcategory->name }}</h4>
            <span class="food-type {{ $item->subcategory->food_type }}">
              <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> {{ $item->subcategory->food_type }}
            </span>
            
            <div class="info-row">
              <span class="info-label"><i class="fa fa-cubes"></i> Quantity</span>
              <span class="quantity-badge">{{ $item->quantity }}</span>
            </div>
            
            <div class="info-row">
              <span class="info-label"><i class="fa fa-map-marker-alt"></i> Table</span>
              <span class="table-info">
                @if($item->order->table)
                  <i class="fa fa-table"></i> {{ $item->order->table->name }}
                @else
                  <i class="fa fa-shopping-bag"></i> Take Away
                @endif
              </span>
            </div>

            @if($item->note)
            <div class="order-note-box">
              <i class="fa fa-sticky-note mt-1"></i>
              <span>{{ $item->note }}</span>
            </div>
            @endif
          </div>
          
          <div class="status-section">
            <select class="status-select" data-id="{{ $item->id }}">
              <option value="PENDING" {{ $item->order_status == 'PENDING' ? 'selected' : '' }}>⏳ Pending</option>
              <option value="COOKING" {{ $item->order_status == 'COOKING' ? 'selected' : '' }}>👨‍🍳 Cooking</option>
              <option value="DONE" {{ $item->order_status == 'DONE' ? 'selected' : '' }}>✅ Done</option>
            </select>
          </div>
        </div>
        @empty
        <div class="empty-state">
          <i class="fa fa-utensils fa-4x mb-3"></i>
          <h4>No Orders Found</h4>
          <p>No active kitchen orders found for the selected filter criteria.</p>
        </div>
        @endforelse
      </div>
    </div>
  </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@include('includes.script')

<script>
$(document).ready(function() {
  let statusChangeInProgress = false;
  
  // Handle status change
  $(document).on('change', '.status-select', function() {
    if (statusChangeInProgress) return;
    
    let id = $(this).data('id');
    let order_status = $(this).val();
    let card = $('#card_' + id);
    let selectElement = $(this);
    let currentStatus = card.attr('data-status');
    
    if (order_status === currentStatus) return;
    
    statusChangeInProgress = true;
    
    // Update UI immediately
    card.removeClass('PENDING COOKING DONE').addClass(order_status);
    card.attr('data-status', order_status);
    card.css('opacity', '0.7');
    selectElement.prop('disabled', true);
    
    $.ajax({
      url: "{{ route('update.kitchen.status') }}",
      method: "POST",
      data: {
        _token: "{{ csrf_token() }}",
        id: id,
        order_status: order_status
      },
      success: function(response) {
        if (response.success) {
          showNotification('✓ Status updated to ' + order_status, 'success');
        } else {
          revertStatus();
          showNotification('✗ Update failed', 'error');
        }
      },
      error: function() {
        revertStatus();
        showNotification('⚠️ Server error', 'error');
      },
      complete: function() {
        card.css('opacity', '1');
        selectElement.prop('disabled', false);
        statusChangeInProgress = false;
      }
    });
    
    function revertStatus() {
      card.removeClass('PENDING COOKING DONE').addClass(currentStatus);
      card.attr('data-status', currentStatus);
      selectElement.val(currentStatus);
    }
  });
  
  // Filter functionality
  $('.filter-btn').click(function() {
    const filter = $(this).data('filter');
    
    // Update URL with status filter
    const url = new URL(window.location.href);
    if (filter === 'all') {
      url.searchParams.delete('status');
    } else {
      url.searchParams.set('status', filter);
    }
    window.location.href = url.toString();
  });
  
  // Manual refresh button
  $('#refreshBtn').click(function() {
    const btn = $(this);
    btn.html('<i class="fa fa-spinner fa-spin"></i> Refreshing...');
    btn.prop('disabled', true);
    location.reload();
  });
  
  // Auto-refresh every 30 seconds
  setTimeout(function() {
    location.reload();
  }, 30000);
  
  // Notification function
  function showNotification(message, type) {
    $('.notification').remove();
    
    const notification = $('<div class="notification"></div>');
    
    if (type === 'success') {
      notification.css('background', 'linear-gradient(135deg, #10b981, #059669)');
    } else {
      notification.css('background', 'linear-gradient(135deg, #ef4444, #dc2626)');
    }
    
    notification.text(message);
    $('body').append(notification);
    
    setTimeout(function() {
      notification.fadeOut(300, function() {
        $(this).remove();
      });
    }, 3000);
  }
});
</script>

</body>
</html>