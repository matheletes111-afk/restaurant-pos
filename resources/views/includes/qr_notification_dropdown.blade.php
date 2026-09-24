@php
  $userRestId = auth()->user()->restaurant_id ?? null;
  $initialQrOrdersQuery = \App\Models\TempOrder::with(['table_details', 'items.menuItem'])
      ->where('created_at', '>=', now()->subDays(7));
  if ($userRestId) {
      $initialQrOrdersQuery->where('restaurant_id', $userRestId);
  }
  $initialQrOrders = $initialQrOrdersQuery->orderBy('id', 'desc')->take(30)->get();
  $initialUnreadCount = $initialQrOrders->where('is_read', false)->count();
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
    title="Customer QR Orders (Past 1 Week)"
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

  <div class="dropdown-menu dropdown-notification dropdown-menu-end pc-h-dropdown shadow-lg border-0 qr-notif-menu" style="width: 360px; max-width: 92vw; border-radius: 14px; overflow: hidden; padding: 0;">
    <!-- Dropdown Header -->
    <div class="dropdown-header px-3 py-3 d-flex align-items-center justify-content-between bg-white border-bottom">
      <div class="d-flex align-items-center gap-2">
        <span class="qr-bell-icon-wrap">
          <i class="fas fa-qrcode text-primary"></i>
        </span>
        <div>
          <h6 class="m-0 fw-bold text-dark" style="font-size: 0.95rem;">QR Code Orders</h6>
          <span class="text-muted" style="font-size: 0.72rem;">Past 7 Days History</span>
        </div>
      </div>
      <div>
        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1" style="font-size: 0.7rem; font-weight: 600;">
          <i class="fas fa-circle text-success me-1" style="font-size: 0.45rem; vertical-align: middle;"></i>Live
        </span>
      </div>
    </div>

    <!-- Dropdown List of Notifications for Past 1 Week -->
    <div
      class="dropdown-header px-0 text-wrap position-relative qr-notif-scroll-area"
      id="qrNotifListContainer"
      style="max-height: 400px; overflow-y: auto; padding: 0;"
    >
      @forelse($initialQrOrders as $ord)
        @php
          $tableName = $ord->table_details ? $ord->table_details->name : ($ord->table_id ? 'Table ' . $ord->table_id : 'Dine-In');
          $orderNo = $ord->order_id ?? ('#' . $ord->id);
          $isUnread = !$ord->is_read;
          $itemsCount = $ord->items->count();
          $itemsSummary = $ord->items->map(function ($it) {
              $q = $it->quantity > 1 ? $it->quantity . 'x ' : '';
              $n = $it->menuItem ? $it->menuItem->name : 'Dish';
              return $q . $n;
          })->take(2)->implode(', ');
          if ($itemsCount > 2) {
              $itemsSummary .= ' +' . ($itemsCount - 2) . ' more';
          }
        @endphp
        <a
          href="{{ route('temp.orders.view', $ord->id) }}"
          class="list-group-item list-group-item-action px-3 py-2.5 border-bottom qr-notif-item {{ $isUnread ? 'qr-item-unread' : '' }}"
          data-order-id="{{ $ord->id }}"
          style="text-decoration: none; transition: background 0.2s;"
        >
          <div class="d-flex align-items-center justify-content-between mb-1">
            <div class="d-flex align-items-center gap-1.5">
              @if($isUnread)
                <span class="qr-unread-dot me-1" title="Unread"></span>
              @endif
              <span class="badge bg-dark-subtle text-dark border px-2 py-0.5 rounded-pill" style="font-size: 0.72rem; font-weight: 600;">
                <i class="fas fa-chair text-muted me-1"></i>{{ $tableName }}
              </span>
              @if(strtoupper($ord->order_status ?? 'PENDING') === 'PENDING')
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-1.5 py-0.5 rounded" style="font-size: 0.68rem; font-weight: 700;">PENDING</span>
              @elseif(strtoupper($ord->order_status) === 'APPROVED')
                <span class="badge bg-success-subtle text-success border border-success-subtle px-1.5 py-0.5 rounded" style="font-size: 0.68rem; font-weight: 700;">APPROVED</span>
              @elseif(strtoupper($ord->order_status) === 'REJECTED')
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-1.5 py-0.5 rounded" style="font-size: 0.68rem; font-weight: 700;">REJECTED</span>
              @endif
            </div>
            <span class="fw-bold text-primary font-monospace" style="font-size: 0.78rem;">
              {{ $orderNo }}
            </span>
          </div>

          <div class="d-flex align-items-center justify-content-between">
            <div class="text-truncate me-2" style="max-width: 220px;">
              <div class="fw-semibold text-dark text-truncate" style="font-size: 0.83rem;">
                {{ $ord->customer_name }} @if(!empty($ord->customer_phone))<span class="text-muted fw-normal">({{ $ord->customer_phone }})</span>@endif
              </div>
              @if(!empty($itemsSummary))
                <div class="text-muted text-truncate" style="font-size: 0.75rem;">
                  {{ $itemsSummary }}
                </div>
              @endif
            </div>
            <div class="text-end flex-shrink-0">
              <span class="fw-bold text-success" style="font-size: 0.88rem;">
                ₹{{ number_format((float)$ord->grand_total, 2) }}
              </span>
            </div>
          </div>

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
          <p class="text-muted mb-0 fw-semibold" style="font-size: 0.85rem;">No QR orders in past 7 days</p>
          <small class="text-muted" style="font-size: 0.75rem;">New customer QR orders will appear here automatically</small>
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
