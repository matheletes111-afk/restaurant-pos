<!DOCTYPE html>
<html lang="en">
<head>
  <title>Pending Customer QR Orders • Bill&Bite POS</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">

  @include('includes.style')

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Font Awesome 6 CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

  <!-- Pending Orders CSS -->
  <link rel="stylesheet" href="{{ asset('admin_template/css/pending-temp-orders.css') }}">
</head>
<body data-pc-theme="light">

@include('includes.sidebar')

<div class="pc-container">
  <div class="pc-content">
    
    <div class="pos-page-wrap">
      
      <!-- ===================================================
           1. TOP HEADER DECK
           =================================================== -->
      <div class="pos-header-deck">
        <div class="pos-header-left">
          <div class="pos-header-icon">
            <i class="fa-solid fa-qrcode"></i>
          </div>
          <div class="pos-header-title-meta">
            <span class="pos-header-eyebrow">Customer Self-Service QR</span>
            <h1 class="pos-header-title">Pending Customer Orders</h1>
            <p class="pos-header-sub">Real-time contactless customer QR orders awaiting manager approval &amp; kitchen dispatch</p>
          </div>
        </div>

        <div class="pos-header-actions">
          <div class="pos-table-badge dine-in">
            <i class="fa-solid fa-bell text-primary"></i>
            <span>{{ $orders->count() }} Orders Awaiting Review</span>
          </div>

          <button type="button" class="btn-pos-pill btn-pos-pill-primary" id="refreshOrdersBtn">
            <i class="fa-solid fa-rotate"></i>
            <span>Refresh</span>
          </button>
        </div>
      </div>

      <!-- Flash Messages -->
      @include('includes.message')

      @php
        $totalPendingCount = $orders->count();
        $dineInCount = $orders->where('order_type', 'DINE_IN')->count();
        $takeawayCount = $orders->where('order_type', '!=', 'DINE_IN')->count();
        $totalPendingValue = $orders->sum('grand_total');
      @endphp

      <!-- ===================================================
           2. QUICK METRICS STATS DECK
           =================================================== -->
      <div class="pos-metrics-grid">
        <!-- Total Pending -->
        <div class="pos-metric-card metric-pending">
          <div class="pos-metric-info">
            <span class="pos-metric-label">⏳ Pending Orders</span>
            <span class="pos-metric-val">{{ $totalPendingCount }}</span>
          </div>
          <div class="pos-metric-icon">
            <i class="fa-solid fa-clock"></i>
          </div>
        </div>

        <!-- Dine In Orders -->
        <div class="pos-metric-card metric-dinein">
          <div class="pos-metric-info">
            <span class="pos-metric-label">🪑 Dine-In QR</span>
            <span class="pos-metric-val">{{ $dineInCount }}</span>
          </div>
          <div class="pos-metric-icon">
            <i class="fa-solid fa-chair"></i>
          </div>
        </div>

        <!-- Takeaway Orders -->
        <div class="pos-metric-card metric-takeaway">
          <div class="pos-metric-info">
            <span class="pos-metric-label">🛍️ Takeaway Orders</span>
            <span class="pos-metric-val">{{ $takeawayCount }}</span>
          </div>
          <div class="pos-metric-icon">
            <i class="fa-solid fa-bag-shopping"></i>
          </div>
        </div>

        <!-- Total Pending Value -->
        <div class="pos-metric-card metric-value">
          <div class="pos-metric-info">
            <span class="pos-metric-label">💰 Total Order Value</span>
            <span class="pos-metric-val">₹{{ number_format($totalPendingValue, 2) }}</span>
          </div>
          <div class="pos-metric-icon">
            <i class="fa-solid fa-receipt"></i>
          </div>
        </div>
      </div>

      <!-- ===================================================
           3. PENDING ORDERS CONTAINER
           =================================================== -->
      <div class="pos-card-container">
        
        <div class="pos-card-header">
          <h2 class="pos-card-title">
            <i class="fa-solid fa-list-check text-primary"></i>
            <span>Pending Order Queue</span>
            <span class="pos-badge-count">{{ $totalPendingCount }} Pending</span>
          </h2>

          <div class="d-flex align-items-center gap-2 flex-wrap">
            <div class="pos-search-wrap">
              <i class="fa-solid fa-magnifying-glass"></i>
              <input type="text" id="customOrderSearch" class="pos-search-input" placeholder="Search order #, customer, table...">
            </div>
          </div>
        </div>

        <!-- Desktop Table View -->
        <div class="pos-desktop-table-wrap table-responsive">
          <table id="pendingOrdersTable" class="pos-table-custom">
            <thead>
              <tr>
                <th>Order #</th>
                <th>Customer Details</th>
                <th>Dining Type &amp; Table</th>
                <th>Items</th>
                <th>Grand Total</th>
                <th>Bill Type</th>
                <th>Placed Time</th>
                <th class="text-end">Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse($orders as $o)
              @php
                $itemCount = $o->items->count();
                $isGst = ($o->is_gst_bill ?? 'NO') == 'YES';
                $customerInitial = strtoupper(substr($o->customer_name ?? 'G', 0, 1));
                $orderDisplayNo = $o->order_id ?? ('#' . $o->id);
              @endphp
              <tr>
                <!-- Order ID -->
                <td>
                  <span class="pos-order-id-badge">
                    <i class="fa-solid fa-hashtag text-primary"></i> {{ $orderDisplayNo }}
                  </span>
                </td>

                <!-- Customer Details -->
                <td>
                  <div class="pos-customer-cell">
                    <div class="pos-avatar-initial">{{ $customerInitial }}</div>
                    <div class="pos-customer-meta">
                      <span class="pos-customer-name">{{ $o->customer_name ?? 'Guest Customer' }}</span>
                      <span class="pos-customer-phone">
                        <i class="fa-solid fa-phone me-1"></i> {{ $o->customer_phone ?? 'No Phone' }}
                      </span>
                    </div>
                  </div>
                </td>

                <!-- Dining Type & Table -->
                <td>
                  @if($o->order_type == 'DINE_IN')
                    <span class="pos-table-badge dine-in">
                      <i class="fa-solid fa-chair text-primary"></i> {{ @$o->table_details->name ?? 'Table' }}
                    </span>
                  @else
                    <span class="pos-table-badge takeaway">
                      <i class="fa-solid fa-bag-shopping text-success"></i> Takeaway
                    </span>
                  @endif
                </td>

                <!-- Items Count -->
                <td>
                  <span class="pos-items-count-pill">
                    <i class="fa-solid fa-utensils me-1"></i> {{ $itemCount }} {{ $itemCount == 1 ? 'item' : 'items' }}
                  </span>
                </td>

                <!-- Grand Total -->
                <td>
                  <span class="pos-amount-text">₹{{ number_format($o->grand_total ?? 0, 2) }}</span>
                </td>

                <!-- Bill Type -->
                <td>
                  @if($isGst)
                    <span class="pos-bill-type-tag gst">
                      <i class="fa-solid fa-percent me-1"></i> GST ({{ $o->restaurant_gst_percentage ?? 0 }}%)
                    </span>
                  @else
                    <span class="pos-bill-type-tag nongst">Non-GST</span>
                  @endif
                </td>

                <!-- Time Placed -->
                <td>
                  <span class="text-muted" style="font-size: 0.82rem;">
                    <i class="fa-regular fa-clock me-1"></i> {{ $o->created_at ? $o->created_at->format('d M, h:i A') : '-' }}
                  </span>
                </td>

                <!-- Action Button -->
                <td class="text-end">
                  <a href="{{ route('temp.orders.view', $o->id) }}" class="btn-review-order" title="Review & Approve Order">
                    <i class="fa-solid fa-eye"></i>
                    <span>Review &amp; Approve</span>
                  </a>
                </td>
              </tr>
              @empty
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Mobile Touch Cards Grid -->
        <div class="pos-mobile-cards-grid">
          @forelse($orders as $o)
          @php
            $itemCount = $o->items->count();
            $isGst = ($o->is_gst_bill ?? 'NO') == 'YES';
            $customerInitial = strtoupper(substr($o->customer_name ?? 'G', 0, 1));
            $orderDisplayNo = $o->order_id ?? ('#' . $o->id);
          @endphp
          <div class="pos-mobile-order-card">
            <div class="pos-mobile-card-header">
              <span class="pos-order-id-badge">
                <i class="fa-solid fa-hashtag text-primary"></i> {{ $orderDisplayNo }}
              </span>

              @if($o->order_type == 'DINE_IN')
                <span class="pos-table-badge dine-in">
                  <i class="fa-solid fa-chair text-primary"></i> {{ @$o->table_details->name ?? 'Table' }}
                </span>
              @else
                <span class="pos-table-badge takeaway">
                  <i class="fa-solid fa-bag-shopping text-success"></i> Takeaway
                </span>
              @endif
            </div>

            <div class="pos-customer-cell">
              <div class="pos-avatar-initial">{{ $customerInitial }}</div>
              <div class="pos-customer-meta">
                <span class="pos-customer-name">{{ $o->customer_name ?? 'Guest Customer' }}</span>
                <span class="pos-customer-phone">
                  <i class="fa-solid fa-phone me-1"></i> {{ $o->customer_phone ?? 'No Phone' }}
                </span>
              </div>
            </div>

            <div class="pos-mobile-card-row">
              <span class="text-muted" style="font-size: 0.8rem;">
                <i class="fa-solid fa-utensils me-1"></i> {{ $itemCount }} items • {{ $isGst ? 'GST Bill' : 'Non-GST' }}
              </span>
              <span class="pos-amount-text">₹{{ number_format($o->grand_total ?? 0, 2) }}</span>
            </div>

            <a href="{{ route('temp.orders.view', $o->id) }}" class="btn-review-order" style="justify-content: center; height: 42px; width: 100%;">
              <i class="fa-solid fa-eye"></i>
              <span>Review &amp; Approve Order</span>
            </a>
          </div>
          @empty
          @endforelse
        </div>

        @if($orders->count() == 0)
        <!-- Empty State -->
        <div class="text-center py-5 px-3">
          <div style="width: 70px; height: 70px; border-radius: 50%; background: #fff0e6; color: #ff5e14; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin: 0 auto 16px;">
            <i class="fa-solid fa-clipboard-check"></i>
          </div>
          <h3 style="font-family: 'Outfit', sans-serif; font-weight: 800; color: #0f172a; margin-bottom: 6px;">No Pending Customer Orders</h3>
          <p style="color: #64748b; font-size: 0.88rem; max-width: 400px; margin: 0 auto;">All contactless customer QR orders have been reviewed and dispatched to the kitchen.</p>
        </div>
        @endif

      </div>

    </div><!-- /.pos-page-wrap -->

  </div><!-- /.pc-content -->
</div><!-- /.pc-container -->

<!-- JS & Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
@include('includes.script')

<script>
$(document).ready(function() {
  const table = $('#pendingOrdersTable').DataTable({
    order: [[0, 'desc']],
    responsive: true,
    pageLength: 25,
    dom: 't<"d-flex justify-content-between align-items-center p-3"ip>',
    language: {
      zeroRecords: "No matching pending customer orders found"
    }
  });

  // Connect Custom Search
  $('#customOrderSearch').on('keyup', function() {
    table.search($(this).val()).draw();
  });

  // Refresh Button
  $('#refreshOrdersBtn').on('click', function() {
    $(this).find('i').addClass('fa-spin');
    location.reload();
  });
});
</script>

</body>
</html>