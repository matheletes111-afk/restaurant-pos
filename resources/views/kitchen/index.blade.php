<!DOCTYPE html>
<html lang="en">
<head>
  <title>Kitchen Panel</title>
  @include('includes.style')
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
:root {
  --page-bg: #ffffff;
  --primary-green: #009d1a;
  --light-green: #e8f7eb;
  --card-border: #009d1a;
  --text-primary: #0f3d19;
  --text-secondary: #2e6b3a;
  
  /* Status Colors - Background and Text */
  --status-pending: #ff9800;
  --status-pending-bg: #fff3e0;
  --status-pending-text: #b45f06;
  
  --status-cooking: #2196f3;
  --status-cooking-bg: #e3f2fd;
  --status-cooking-text: #0d47a1;
  
  --status-done: #4caf50;
  --status-done-bg: #e8f5e9;
  --status-done-text: #1b5e20;
}

/* ================= BODY ================= */
body {
  background: var(--page-bg);
  color: var(--text-primary);
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* ================= CONTAINER ================= */
.kitchen-container {
  padding: 15px;
}

/* ================= FILTER SECTION ================= */
.filter-section {
  background: #f8f9fa;
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 25px;
  border: 1px solid #e9ecef;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
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
  margin-bottom: 5px;
  font-weight: 600;
  font-size: 13px;
  color: #495057;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.filter-group input,
.filter-group select {
  width: 100%;
  padding: 10px 12px;
  border: 2px solid #dee2e6;
  border-radius: 8px;
  font-size: 14px;
  transition: all 0.3s;
}

.filter-group input:focus,
.filter-group select:focus {
  border-color: var(--primary-green);
  outline: none;
  box-shadow: 0 0 0 3px rgba(0, 157, 26, 0.1);
}

.filter-actions {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.btn-filter {
  padding: 10px 24px;
  background: var(--primary-green);
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  height: 42px;
}

.btn-filter:hover {
  background: #00b11e;
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(0, 157, 26, 0.3);
}

.btn-reset {
  padding: 10px 24px;
  background: #6c757d;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  height: 42px;
}

.btn-reset:hover {
  background: #5a6268;
  transform: translateY(-2px);
}

/* ================= DATE SUMMARY ================= */
.date-summary {
  margin: 15px 0;
  padding: 12px 18px;
  background: #e8f4fd;
  border-radius: 10px;
  font-size: 14px;
  color: #0d47a1;
  display: flex;
  align-items: center;
  gap: 10px;
  border-left: 4px solid var(--status-cooking);
}

.date-summary i {
  font-size: 16px;
}

.order-count {
  margin-left: auto;
  background: var(--primary-green);
  color: white;
  padding: 4px 15px;
  border-radius: 30px;
  font-weight: 600;
  font-size: 13px;
}

/* ================= FILTER BUTTONS ================= */
.filter-buttons {
  margin: 20px 0;
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.filter-btn {
  padding: 8px 20px;
  background: #ffffff;
  border: 1px solid var(--primary-green);
  color: var(--primary-green);
  border-radius: 30px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s;
}

.filter-btn.active,
.filter-btn:hover {
  background: linear-gradient(135deg, #009d1a, #00c72c);
  color: white;
  border-color: transparent;
  box-shadow: 0 4px 10px rgba(0, 157, 26, 0.3);
}

/* ================= GRID ================= */
.kitchen-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
  padding: 10px 0;
}

@media (min-width: 768px) {
  .kitchen-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
  }
}

@media (min-width: 1200px) {
  .kitchen-grid {
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
  }
}

@media (max-width: 480px) {
  .kitchen-grid {
    grid-template-columns: 1fr;
  }
}

/* ================= ORDER CARD ================= */
.order-card {
  border-radius: 12px;
  padding: 15px;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  min-height: 200px;
  display: flex;
  flex-direction: column;
  position: relative;
}

.order-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
}

/* ================= STATUS BACKGROUND COLORS ================= */
.order-card.PENDING {
  background: var(--status-pending-bg);
  border-left: 6px solid var(--status-pending);
}

.order-card.COOKING {
  background: var(--status-cooking-bg);
  border-left: 6px solid var(--status-cooking);
}

.order-card.DONE {
  background: var(--status-done-bg);
  border-left: 6px solid var(--status-done);
}

/* ================= STATUS TEXT COLORS ================= */
.order-card.PENDING .order-id,
.order-card.PENDING .product-name {
  color: var(--status-pending-text);
}

.order-card.COOKING .order-id,
.order-card.COOKING .product-name {
  color: var(--status-cooking-text);
}

.order-card.DONE .order-id,
.order-card.DONE .product-name {
  color: var(--status-done-text);
}

/* ================= CARD HEADER ================= */
.card-header {
  margin-bottom: 15px;
  padding-bottom: 10px;
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

.order-id {
  font-size: 14px;
  font-weight: 800;
  margin: 0;
}

.order-time {
  font-size: 11px;
  color: #666;
  margin-top: 5px;
  display: block;
}

.order-time i {
  margin-right: 3px;
}

/* ================= PRODUCT ================= */
.product-info {
  margin-bottom: 15px;
  flex-grow: 1;
}

.product-name {
  font-size: 16px;
  font-weight: 800;
  margin-bottom: 8px;
}

.food-type {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 15px;
  font-size: 10px;
  font-weight: 800;
  text-transform: uppercase;
  margin-bottom: 12px;
}

.food-type.VEG {
  background: rgba(0, 157, 26, 0.15);
  color: var(--primary-green);
  border: 1px solid rgba(0, 157, 26, 0.4);
}

.food-type.NON-VEG {
  background: rgba(244, 67, 54, 0.15);
  color: #c62828;
  border: 1px solid rgba(244, 67, 54, 0.4);
}

/* ================= INFO ROW ================= */
.info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
  padding: 6px 0;
  border-bottom: 1px dashed rgba(0, 0, 0, 0.05);
}

.info-row:last-child {
  border-bottom: none;
}

.info-label {
  font-size: 12px;
  color: var(--text-secondary);
  font-weight: 600;
}

.info-label i {
  margin-right: 5px;
  color: var(--primary-green);
}

.info-value {
  font-size: 14px;
  font-weight: 700;
}

/* ================= QUANTITY ================= */
.quantity-badge {
  background: linear-gradient(135deg, #009d1a, #00c72c);
  color: white;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  font-weight: 800;
  box-shadow: 0 2px 5px rgba(0, 157, 26, 0.3);
}

/* ================= TABLE INFO ================= */
.table-info {
  display: inline-flex;
  align-items: center;
  padding: 4px 12px;
  background: rgba(0, 157, 26, 0.1);
  border-radius: 20px;
  font-size: 12px;
  font-weight: 700;
}

.table-info i {
  margin-right: 5px;
  color: var(--primary-green);
}

/* ================= STATUS SELECT ================= */
.status-section {
  margin-top: auto;
  padding-top: 12px;
  border-top: 1px solid rgba(0, 0, 0, 0.1);
}

.status-select {
  width: 100%;
  border: 2px solid;
  border-radius: 8px;
  padding: 8px 12px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
  height: 40px;
  background: white;
}

/* Border colors based on current status */
.order-card.PENDING .status-select {
  border-color: var(--status-pending);
  color: var(--status-pending-text);
}

.order-card.COOKING .status-select {
  border-color: var(--status-cooking);
  color: var(--status-cooking-text);
}

.order-card.DONE .status-select {
  border-color: var(--status-done);
  color: var(--status-done-text);
}

.status-select:focus {
  outline: none;
  box-shadow: 0 0 0 2px rgba(0, 157, 26, 0.2);
}

.status-select option {
  background: white;
  color: var(--text-primary);
}

/* ================= NOTE STYLE ================= */
.order-note {
  margin-top: 8px;
  padding: 6px 10px;
  background: rgba(255, 193, 7, 0.1);
  border-left: 3px solid #ffc107;
  border-radius: 4px;
  font-size: 11px;
  color: #856404;
}

/* ================= EMPTY STATE ================= */
.empty-state {
  padding: 60px 20px;
  text-align: center;
  background: #f8f9fa;
  border-radius: 12px;
  border: 2px dashed #dee2e6;
  grid-column: 1 / -1;
}

.empty-state i {
  color: #adb5bd;
  margin-bottom: 15px;
}

.empty-state h4 {
  font-weight: 700;
  color: #495057;
  margin-bottom: 10px;
}

.empty-state p {
  color: #6c757d;
}

/* ================= TOAST NOTIFICATION ================= */
.notification {
  position: fixed;
  top: 20px;
  right: 20px;
  min-width: 250px;
  padding: 12px 20px;
  color: #ffffff;
  font-size: 14px;
  font-weight: 600;
  border-radius: 8px;
  z-index: 99999;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
  animation: slideInToast 0.4s ease forwards;
}

@keyframes slideInToast {
  from {
    transform: translateX(120%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}
  </style>
</head>
<body>
@include('includes.sidebar')

<div class="pc-container">
  <div class="pc-content">
    <div class="page-header">
      <div class="row align-items-center">
        <div class="col-md-6">
          <h5 class="page-title">Kitchen Orders</h5>
          <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
            <li class="breadcrumb-item" aria-current="page">Kitchen</li>
          </ul>
        </div>
        <div class="col-md-6 text-right">
          <button id="refreshBtn" class="btn btn-primary">
            <i class="fa fa-sync-alt"></i> Refresh
          </button>
        </div>
      </div>
    </div>

    <div class="kitchen-container">
      <!-- Date Range Filter Section -->
      <div class="filter-section">
        <form method="GET" action="{{ route('manage.kitchen-panel') }}" id="filterForm">
          <div class="filter-row">
            <div class="filter-group">
              <label><i class="fa fa-calendar"></i> From Date</label>
              <input type="date" name="from_date" value="{{ $from_date }}" max="{{ date('Y-m-d') }}">
            </div>
            <div class="filter-group">
              <label><i class="fa fa-calendar"></i> To Date</label>
              <input type="date" name="to_date" value="{{ $to_date }}" max="{{ date('Y-m-d') }}">
            </div>
            <div class="filter-group">
              <label><i class="fa fa-filter"></i> Status</label>
              <select name="status">
                <option value="all" {{ $selected_status == 'all' ? 'selected' : '' }}>All Status</option>
                <option value="PENDING" {{ $selected_status == 'PENDING' ? 'selected' : '' }}>⏳ Pending</option>
                <option value="COOKING" {{ $selected_status == 'COOKING' ? 'selected' : '' }}>👨‍🍳 Cooking</option>
                <option value="DONE" {{ $selected_status == 'DONE' ? 'selected' : '' }}>✅ Done</option>
              </select>
            </div>
            <div class="filter-group">
              <label><i class="fa fa-table"></i> Table</label>
              <select name="table_id">
                <option value="">All Tables</option>
                @foreach($tables as $table)
                  <option value="{{ $table->id }}" {{ $selected_table == $table->id ? 'selected' : '' }}>{{ $table->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="filter-actions">
              <button type="submit" class="btn-filter">
                <i class="fa fa-search"></i> Apply
              </button>
              <a href="{{ route('manage.kitchen-panel') }}" class="btn-reset">
                <i class="fa fa-undo"></i> Reset
              </a>
            </div>
          </div>
        </form>
      </div>

      <!-- Date Range Summary -->
      <div class="date-summary">
        <i class="fa fa-clock-o"></i>
        <span>Showing orders from <strong>{{ \Carbon\Carbon::parse($from_date)->format('d M Y') }}</strong> to <strong>{{ \Carbon\Carbon::parse($to_date)->format('d M Y') }}</strong></span>
        <span class="order-count">{{ count($OrderItems) }} orders</span>
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
            <span class="order-time">
              <i class="fa fa-clock-o"></i> {{ $item->created_at->format('d M, h:i A') }}
            </span>
          </div>
          
          <div class="product-info">
            <h4 class="product-name">{{ $item->subcategory->name }}</h4>
            <span class="food-type {{ $item->subcategory->food_type }}">
              {{ $item->subcategory->food_type }}
            </span>
            
            <div class="info-row">
              <span class="info-label"><i class="fa fa-cubes"></i> Quantity</span>
              <span class="quantity-badge">{{ $item->quantity }}</span>
            </div>
            
            <div class="info-row">
              <span class="info-label"><i class="fa fa-map-marker"></i> Table</span>
              <span class="table-info">
                @if($item->order->table)
                  <i class="fa fa-table"></i> {{ $item->order->table->name }}
                @else
                  <i class="fa fa-shopping-bag"></i> Take Away
                @endif
              </span>
            </div>

            @if($item->note)
            <div class="info-row">
              <span class="info-label"><i class="fa fa-sticky-note"></i> Note</span>
              <span class="info-value" style="color: #856404;">{{ $item->note }}</span>
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
          <i class="fa fa-utensils fa-4x"></i>
          <h4>No Orders Found</h4>
          <p>No orders available for the selected date range</p>
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
  
  // Auto-refresh every 30 seconds (without confirm)
  setTimeout(function() {
    location.reload();
  }, 30000);
  
  // Notification function
  function showNotification(message, type) {
    $('.notification').remove();
    
    const notification = $('<div class="notification"></div>');
    
    if (type === 'success') {
      notification.css('background', 'linear-gradient(135deg, #4CAF50, #45a049)');
    } else {
      notification.css('background', 'linear-gradient(135deg, #f44336, #d32f2f)');
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