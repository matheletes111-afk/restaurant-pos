@php
  $userRestId = auth()->user()->restaurant_id ?? null;
  $initialNotifications = collect();

  if ($userRestId) {
      // 1. Initial Pending Temp Orders
      $tempOrders = \App\Models\TempOrder::with(['table_details', 'items.menuItem'])
          ->where('restaurant_id', $userRestId)
          ->where(function ($q) {
              $q->where('order_status', 'PENDING')
                ->orWhereNull('order_status');
          })
          ->where('created_at', '>=', now()->subDays(7))
          ->orderBy('id', 'desc')
          ->take(20)
          ->get();

      foreach ($tempOrders as $ord) {
          $tableName = $ord->table_details ? $ord->table_details->name : ($ord->table_id ? 'Table ' . $ord->table_id : 'Dine-In');
          $itemsCount = $ord->items->count();
          $itemsSummary = $ord->items->map(function ($it) {
              $q = $it->quantity > 1 ? $it->quantity . 'x ' : '';
              $n = $it->menuItem ? $it->menuItem->name : 'Dish';
              return $q . $n;
          })->take(2)->implode(', ');
          if ($itemsCount > 2) {
              $itemsSummary .= ' +' . ($itemsCount - 2) . ' more';
          }

          $initialNotifications->push((object)[
              'id' => (string) $ord->id,
              'notif_type' => 'initial_order',
              'order_no' => $ord->order_id ?? ('#' . $ord->id),
              'table_name' => $tableName,
              'customer_name' => $ord->customer_name ?: 'Customer',
              'customer_phone' => $ord->customer_phone,
              'notification_title' => ($ord->customer_name ?: 'Customer') . ' placed a new order for ' . $tableName,
              'items_summary' => $itemsSummary,
              'grand_total' => (float)$ord->grand_total,
              'order_status' => 'PENDING',
              'is_read' => (bool)$ord->is_read,
              'view_url' => route('temp.orders.view', $ord->id),
              'created_at' => $ord->created_at,
          ]);
      }

      // 2. Active Orders with Additional Items
      $activeOrders = \App\Models\OrderManage::with(['table', 'orderItems.subcategory'])
          ->where('restaurant_id', $userRestId)
          ->whereHas('orderItems', function ($q) {
              $q->where('is_new', 1);
          })
          ->where('order_complete', '!=', 'DONE')
          ->where('payment_status', '!=', 'PAID')
          ->whereNotIn('order_status', ['COMPLETED', 'CANCELLED', 'REJECTED'])
          ->where('created_at', '>=', now()->subDays(7))
          ->orderBy('updated_at', 'desc')
          ->take(20)
          ->get();

      foreach ($activeOrders as $mainOrd) {
          $tableName = $mainOrd->table ? $mainOrd->table->name : ($mainOrd->table_id ? 'Table ' . $mainOrd->table_id : 'Table');
          $newItems = $mainOrd->orderItems->where('is_new', 1);
          $latestItem = $newItems->sortByDesc('id')->first();
          $latestKot = $latestItem ? $latestItem->kot_no : null;

          $itemsSummary = $newItems->map(function ($it) {
              $q = $it->quantity > 1 ? $it->quantity . 'x ' : '';
              $n = $it->subcategory ? $it->subcategory->name : 'Dish';
              return $q . $n;
          })->take(2)->implode(', ');
          if ($newItems->count() > 2) {
              $itemsSummary .= ' +' . ($newItems->count() - 2) . ' more';
          }
          if ($latestKot) {
              $itemsSummary = "[{$latestKot}] " . $itemsSummary;
          }

          $initialNotifications->push((object)[
              'id' => 'main_' . $mainOrd->id,
              'notif_type' => 'additional_items',
              'order_no' => $mainOrd->order_id ?? ('#' . $mainOrd->id),
              'table_name' => $tableName,
              'customer_name' => $mainOrd->customer_name ?: 'Customer',
              'customer_phone' => $mainOrd->customer_phone,
              'notification_title' => ($mainOrd->customer_name ?: 'Customer') . ' placed new order items on ' . $tableName,
              'items_summary' => $itemsSummary,
              'grand_total' => (float)$mainOrd->grand_total,
              'order_status' => 'NEW ITEMS',
              'is_read' => false,
              'view_url' => 'javascript:void(0);',
              'created_at' => $latestItem && $latestItem->created_at ? $latestItem->created_at : $mainOrd->updated_at,
          ]);
      }
  }

  $initialNotifications = $initialNotifications->sortByDesc(function ($n) {
      return $n->created_at ? $n->created_at->timestamp : 0;
  })->values();

  $initialUnreadCount = $initialNotifications->where('is_read', false)->count();
@endphp

<li class="dropdown pc-h-item qr-notification-root me-2">
  <a
    class="pc-head-link dropdown-toggle arrow-none me-0 position-relative"
    data-bs-toggle="dropdown"
    data-bs-auto-close="outside"
    href="#"
    role="button"
    aria-haspopup="false"
    aria-expanded="false"
    id="qrNotifDropdownToggle"
    title="Customer QR Orders & Alerts"
  >
    <i class="ti ti-bell fs-4"></i>
    <span
      id="qrNotifBadge"
      class="badge bg-danger rounded-pill position-absolute qr-bell-badge"
      style="{{ $initialUnreadCount > 0 ? '' : 'display: none;' }}"
    >
      {{ $initialUnreadCount > 99 ? '99+' : $initialUnreadCount }}
    </span>
  </a>

  <div class="dropdown-menu dropdown-notification dropdown-menu-end pc-h-dropdown shadow-lg border-0 qr-notif-menu" style="width: 370px; max-width: 92vw; border-radius: 14px; overflow: hidden; padding: 0;">
    <!-- Dropdown Header -->
    <div class="dropdown-header px-3 py-3 d-flex align-items-center justify-content-between bg-white border-bottom">
      <div class="d-flex align-items-center gap-2">
        <span class="qr-bell-icon-wrap">
          <i class="fas fa-qrcode text-primary"></i>
        </span>
        <div>
          <h6 class="m-0 fw-bold text-dark" style="font-size: 0.95rem;">QR Code Orders</h6>
          <span class="text-muted" style="font-size: 0.72rem;">Live Customer Alerts</span>
        </div>
      </div>
      <div>
        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1" style="font-size: 0.7rem; font-weight: 600;">
          <i class="fas fa-bell me-1" style="font-size: 0.65rem; vertical-align: middle;"></i>Live
        </span>
      </div>
    </div>

    <!-- Dropdown List of Notifications for Past 1 Week -->
    <div
      class="dropdown-header px-0 text-wrap position-relative qr-notif-scroll-area"
      id="qrNotifListContainer"
      style="max-height: 400px; overflow-y: auto; padding: 0;"
    >
      @forelse($initialNotifications as $ord)
        @php
          $isUnread = !$ord->is_read;
          $isAdditional = ($ord->notif_type ?? '') === 'additional_items';
        @endphp
        <a
          href="{{ $isAdditional ? 'javascript:void(0);' : $ord->view_url }}"
          class="list-group-item list-group-item-action px-3 py-2.5 border-bottom qr-notif-item {{ $isUnread ? 'qr-item-unread' : '' }}"
          data-order-id="{{ $ord->id }}"
          style="text-decoration: none; transition: background 0.2s; {{ $isAdditional ? 'cursor: default;' : '' }}"
        >
          <div class="d-flex align-items-center justify-content-between mb-1">
            <div class="d-flex align-items-center gap-1.5">
              @if($isUnread)
                <span class="qr-unread-dot me-1" title="Unread"></span>
              @endif
              <span class="badge bg-dark-subtle text-dark border px-2 py-0.5 rounded-pill" style="font-size: 0.72rem; font-weight: 600;">
                <i class="fas fa-chair text-muted me-1"></i>{{ $ord->table_name }}
              </span>
              @if($isAdditional)
                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-1.5 py-0.5 rounded" style="font-size: 0.68rem; font-weight: 700;">NEW ITEMS</span>
              @elseif(strtoupper($ord->order_status ?? 'PENDING') === 'PENDING')
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-1.5 py-0.5 rounded" style="font-size: 0.68rem; font-weight: 700;">PENDING</span>
              @elseif(strtoupper($ord->order_status) === 'APPROVED')
                <span class="badge bg-success-subtle text-success border border-success-subtle px-1.5 py-0.5 rounded" style="font-size: 0.68rem; font-weight: 700;">APPROVED</span>
              @endif
            </div>
            @if(!$isAdditional)
              <span class="fw-bold text-primary font-monospace" style="font-size: 0.78rem;">
                {{ $ord->order_no }}
              </span>
            @endif
          </div>

          @if($isAdditional)
            <div class="d-flex align-items-center justify-content-between py-1">
              <div class="fw-semibold text-dark text-truncate" style="font-size: 0.83rem;">
                <i class="fas fa-plus-circle text-primary me-1"></i>{{ $ord->customer_name }} added new item in {{ $ord->table_name }}
              </div>
            </div>
          @else
            <div class="d-flex align-items-center justify-content-between">
              <div class="text-truncate me-2" style="max-width: 230px;">
                <div class="fw-semibold text-dark text-truncate" style="font-size: 0.83rem;">
                  {{ $ord->customer_name }} @if(!empty($ord->customer_phone))<span class="text-muted fw-normal">({{ $ord->customer_phone }})</span>@endif
                </div>
                @if(!empty($ord->items_summary))
                  <div class="text-muted text-truncate" style="font-size: 0.75rem;">
                    {{ $ord->items_summary }}
                  </div>
                @endif
              </div>
              <div class="text-end flex-shrink-0">
                <span class="fw-bold text-success" style="font-size: 0.88rem;">
                  ₹{{ number_format((float)$ord->grand_total, 2) }}
                </span>
              </div>
            </div>
          @endif

          <div class="d-flex align-items-center justify-content-between mt-1 text-muted" style="font-size: 0.7rem;">
            <span><i class="far fa-clock me-1"></i>{{ $ord->created_at ? $ord->created_at->diffForHumans() : '' }}</span>
            <span>{{ $ord->created_at ? $ord->created_at->format('M d, h:i A') : '' }}</span>
          </div>
        </a>
      @empty
        <div class="text-center py-4 px-3" id="qrNotifEmptyState">
          <div class="mb-2 text-muted opacity-50">
            <i class="fas fa-bell-slash fa-2x"></i>
          </div>
          <p class="text-muted mb-0 fw-semibold" style="font-size: 0.85rem;">No new QR orders</p>
          <small class="text-muted" style="font-size: 0.75rem;">New customer QR orders and table additions will appear here automatically</small>
        </div>
      @endforelse
    </div>

    <!-- Dropdown Footer -->
    <div class="dropdown-footer p-2 text-center bg-light border-top">
      <a href="{{ route('temp.orders') }}" class="btn btn-sm btn-outline-primary w-100 rounded-pill fw-semibold py-1.5" style="font-size: 0.82rem;">
        View All Pending Orders <i class="fas fa-arrow-right ms-1"></i>
      </a>
    </div>
  </div>
</li>

<!-- Top Right Floating Toast Container for Real-time QR Order Alerts -->
<div id="qrOrderToastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 10800; pointer-events: none; margin-top: 60px;"></div>

<style>
  .qr-bell-badge {
    top: 6px !important;
    right: 6px !important;
    font-size: 0.65rem;
    padding: 0.25em 0.5em;
    box-shadow: 0 0 0 2px #fff;
    animation: qrBadgePulse 2s infinite;
  }
  @keyframes qrBadgePulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.18); }
    100% { transform: scale(1); }
  }
  .qr-item-unread {
    background-color: #fff9f5 !important;
    border-left: 3px solid #ff6a00 !important;
  }
  .qr-item-unread:hover {
    background-color: #fff2ea !important;
  }
  .qr-unread-dot {
    display: inline-block;
    width: 7px;
    height: 7px;
    background-color: #ff6a00;
    border-radius: 50%;
  }
  .qr-bell-icon-wrap {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: rgba(255, 106, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .qr-notif-scroll-area::-webkit-scrollbar {
    width: 5px;
  }
  .qr-notif-scroll-area::-webkit-scrollbar-track {
    background: #f8fafc;
  }
  .qr-notif-scroll-area::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
  }
  .qr-toast-card {
    pointer-events: auto;
    background: #ffffff;
    border-radius: 14px;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.18), 0 2px 8px rgba(255, 106, 0, 0.2);
    border-left: 5px solid #ff6a00;
    overflow: hidden;
    width: 350px;
    animation: slideInRight 0.35s cubic-bezier(0.16, 1, 0.3, 1);
  }
  @keyframes slideInRight {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
  }
</style>
