<!DOCTYPE html>
<html lang="en">
<head>
  <title>Order Management Dashboard • Bill&Bite POS</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">

  @include('includes.style')

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Public+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Font Awesome 6 CDN Loaded after includes.style -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  
  <!-- External Order Dashboard CSS -->
  <link rel="stylesheet" href="{{ asset('admin_template/css/order-dashboard.css') }}?v={{ time() }}">
</head>
<body>
@include('includes.sidebar')

@php
  $tableList = $data ?? collect([]);
  $totalTables = count($tableList);
  $availableCount = $tableList->filter(function($t) {
      return $t->table_status != 'INACTIVE' && $t->activeOrders->count() == 0;
  })->count();
  $occupiedCount = $tableList->filter(function($t) {
      return $t->table_status != 'INACTIVE' && $t->activeOrders->count() > 0;
  })->count();
  $totalActiveBills = $tableList->sum(function($t) {
      return $t->activeOrders->count();
  });
  $restaurant = \App\Models\RestaurantMaster::find(auth()->user()->restaurant_id);
  $canOrder = in_array(auth()->user()->role_type, ['Manager', 'Cashier', 'ADMIN', 'STAFF']);
@endphp

<div class="pc-container">
  <div class="pc-content">
    
    <!-- Prominent Hero Header Architecture -->
    <div class="floor-header-card">
      <div class="floor-header-left">
        <div class="floor-header-icon">
          <i class="fa-solid fa-utensils"></i>
        </div>
        <div class="floor-header-meta">
          <span class="floor-eyebrow-tag">
            <i class="fa-solid fa-layer-group"></i> Table &amp; POS Order Dispatch
          </span>
          <h1 class="floor-main-title">Order Management Dashboard</h1>
          <p class="floor-sub-text">
            @if($restaurant)
              <span class="text-white fw-bold"><i class="fa-solid fa-store text-warning me-1"></i> {{ $restaurant->name }}</span>
              <span class="opacity-75">&bull;</span>
            @endif
            <span class="floor-live-pill">
              <span class="floor-pulse-dot"></span> Live Dining Sync
            </span>
            <span class="opacity-75">&bull; {{ $totalActiveBills }} Active {{ Str::plural('Bill', $totalActiveBills) }} in Service</span>
          </p>
        </div>
      </div>
      
      <div class="floor-header-actions">
        @if($canOrder)
          <a href="{{ route('order.create', 'TAKEAWAY') }}" class="btn-header-takeaway" title="Start a fast takeaway / parcel order">
            <i class="fa-solid fa-bolt text-warning"></i> Fast Takeaway Order
          </a>
        @endif
      </div>
    </div>

    <!-- Alert Messages -->
    @include('includes.message')

    <!-- Prominent KPI Live Stats Deck -->
    <div class="floor-kpi-grid">
      <div class="floor-kpi-card kpi-total-deck">
        <div class="floor-kpi-icon">
          <i class="fa-solid fa-chair"></i>
        </div>
        <div class="floor-kpi-info">
          <span class="floor-kpi-number">{{ $totalTables }}</span>
          <span class="floor-kpi-label">Total Floor Tables</span>
        </div>
      </div>

      <div class="floor-kpi-card kpi-vacant-deck">
        <div class="floor-kpi-icon">
          <i class="fa-solid fa-circle-check"></i>
        </div>
        <div class="floor-kpi-info">
          <span class="floor-kpi-number">{{ $availableCount }}</span>
          <span class="floor-kpi-label">Vacant &amp; Ready</span>
        </div>
      </div>

      <div class="floor-kpi-card kpi-occupied-deck">
        <div class="floor-kpi-icon">
          <i class="fa-solid fa-fire"></i>
        </div>
        <div class="floor-kpi-info">
          <span class="floor-kpi-number">{{ $occupiedCount }}</span>
          <span class="floor-kpi-label">Occupied Dining</span>
        </div>
      </div>

      <div class="floor-kpi-card kpi-bills-deck">
        <div class="floor-kpi-icon">
          <i class="fa-solid fa-file-invoice-dollar"></i>
        </div>
        <div class="floor-kpi-info">
          <span class="floor-kpi-number">{{ $totalActiveBills }}</span>
          <span class="floor-kpi-label">Active Running Bills</span>
        </div>
      </div>
    </div>

    <!-- Controls Toolbar & Live Filters -->
    <div class="floor-toolbar-panel">
      <div class="floor-filter-pills">
        <button type="button" class="floor-pill-btn active" data-filter="all">
          <i class="fa-solid fa-table-cells-large"></i> All Dining
          <span class="pill-counter">{{ $totalTables + ($canOrder ? 1 : 0) }}</span>
        </button>

        <button type="button" class="floor-pill-btn pill-available" data-filter="available">
          <i class="fa-solid fa-circle-check text-success"></i> Vacant Tables
          <span class="pill-counter">{{ $availableCount }}</span>
        </button>

        <button type="button" class="floor-pill-btn pill-occupied" data-filter="occupied">
          <i class="fa-solid fa-fire text-warning"></i> Occupied Tables
          <span class="pill-counter">{{ $occupiedCount }}</span>
        </button>

        @if($canOrder)
          <button type="button" class="floor-pill-btn pill-takeaway" data-filter="takeaway">
            <i class="fa-solid fa-bag-shopping text-info"></i> Takeaway
            <span class="pill-counter">1</span>
          </button>
        @endif
      </div>

      <!-- Real-time Instant Search -->
      <div class="floor-search-box">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        <input type="text" id="tableSearchInput" class="floor-search-input" placeholder="Search tables (e.g. Table 1, VIP)..." autocomplete="off">
        <button type="button" id="clearSearchBtn" class="search-clear-btn" style="display: none;" title="Clear search">&times;</button>
      </div>
    </div>

    <!-- Dining Floor Grid -->
    <div class="floor-grid" id="floorGridContainer">
      
      <!-- 1. Takeaway Card -->
      @if($canOrder)
      <a href="{{ route('order.create', 'TAKEAWAY') }}" 
         class="dining-card-wrap filter-item" 
         data-type="takeaway" 
         data-name="takeaway parcel fast order"
         title="Click to start a new Takeaway order">
        <div class="prominent-dining-card theme-takeaway">
          <div class="card-banner-strip">
            <span class="card-type-chip">
              <i class="fa-solid fa-bolt text-warning"></i> Quick Service
            </span>
            <span class="card-id-tag text-white" style="background: rgba(255,255,255,0.2);">FAST POS</span>
          </div>

          <div class="card-main-body">
            <div class="card-center-icon-wrap">
              <i class="fa-solid fa-bag-shopping"></i>
            </div>
            <h3 class="card-title-text">Takeaway &amp; Parcel</h3>
            <span class="card-status-badge">
              <i class="fa-solid fa-clock"></i> 15 - 20 Mins Prep
            </span>
            <div class="card-desc-notes text-white-50">
              Counter billing, takeaway packaging &amp; express checkout
            </div>
          </div>

          <div class="card-action-bar">
            <span>Start Express Order</span>
            <span class="card-action-btn">
              <i class="fa-solid fa-arrow-right"></i> Order Now
            </span>
          </div>
        </div>
      </a>
      @endif

      <!-- 2. Dining Tables Loop -->
      @foreach($tableList as $table)
        @php
          $activeOrders = $table->activeOrders;
          $activeBillsCount = $activeOrders->count();
          $isInactive = ($table->table_status == 'INACTIVE');
          $isOccupied = (!$isInactive && $activeBillsCount > 0);
          $isAvailable = (!$isInactive && $activeBillsCount == 0);

          if ($isInactive) {
              $cardType = 'inactive';
              $themeClass = 'theme-inactive';
              $statusText = 'Under Maintenance';
              $statusIcon = 'fa-wrench';
              $url = 'javascript:void(0);';
              $linkClass = '';
              $cursorStyle = 'pointer-events:none; cursor:not-allowed;';
          } elseif ($isOccupied) {
              $cardType = 'occupied';
              $themeClass = 'theme-occupied';
              $statusText = 'Occupied • ' . $activeBillsCount . ' ' . Str::plural('Bill', $activeBillsCount);
              $statusIcon = 'fa-users';
              $url = 'javascript:void(0);';
              $linkClass = 'occupied-table-link';
              $cursorStyle = '';
              $firstCustomer = $activeOrders->first()->customer_name ?? 'Guest';
              $firstOrderTime = $activeOrders->first()->created_at ? $activeOrders->first()->created_at->diffForHumans(null, true) : null;
              $runningTotal = $activeOrders->sum('grand_total');
          } else {
              $cardType = 'available';
              $themeClass = 'theme-available';
              $statusText = 'Vacant • Ready';
              $statusIcon = 'fa-circle-check';
              $url = route('order.create', $table->id);
              $linkClass = '';
              $cursorStyle = '';
          }
        @endphp

        <a href="{{ $url }}" 
           class="dining-card-wrap filter-item {{ $linkClass }}" 
           data-type="{{ $cardType }}"
           data-name="{{ strtolower($table->name . ' ' . ($table->description ?? '')) }}"
           @if($isOccupied)
             data-table-id="{{ $table->id }}"
             data-table-name="{{ $table->name }}"
             data-active-bills="{{ json_encode($activeOrders->map(function($o) {
                 return [
                     'id' => $o->id,
                     'order_id' => $o->order_id,
                     'customer_name' => $o->customer_name ?: 'Guest',
                     'grand_total' => (float)$o->grand_total,
                     'created_at' => $o->created_at ? $o->created_at->format('h:i A') : 'Just now',
                     'edit_url' => route('order.edit', $o->id)
                 ];
             })) }}"
           @endif
           style="{{ $cursorStyle }}">
          
          <div class="prominent-dining-card {{ $themeClass }}">
            <!-- Card Top Header Banner -->
            <div class="card-banner-strip">
              <span class="card-type-chip">
                @if($isOccupied)
                  <i class="fa-solid fa-fire text-warning"></i> In Service
                @elseif($isInactive)
                  <i class="fa-solid fa-ban text-danger"></i> Offline
                @else
                  <i class="fa-solid fa-circle-check text-success"></i> Vacant Table
                @endif
              </span>
              <span class="card-id-tag">ID #{{ $table->id }}</span>
            </div>

            <!-- Card Main Center Body -->
            <div class="card-main-body">
              <div class="card-center-icon-wrap">
                @if($isOccupied)
                  <i class="fa-solid fa-users"></i>
                @elseif($isInactive)
                  <i class="fa-solid fa-screwdriver-wrench"></i>
                @else
                  <i class="fa-solid fa-chair"></i>
                @endif
              </div>

              <h3 class="card-title-text">{{ $table->name }}</h3>
              
              <span class="card-status-badge">
                <i class="fa-solid {{ $statusIcon }}"></i> {{ $statusText }}
              </span>

              @if($isOccupied)
                <div class="occupied-info-box">
                  <span><i class="fa-solid fa-user-tag"></i> {{ $firstCustomer }}</span>
                  @if($firstOrderTime)
                    <span>&bull;</span>
                    <span><i class="fa-solid fa-clock"></i> {{ $firstOrderTime }}</span>
                  @endif
                </div>
              @elseif(!empty($table->description))
                <div class="card-desc-notes text-truncate">
                  {{ $table->description }}
                </div>
              @else
                <div class="card-desc-notes">
                  Ready for new guest order
                </div>
              @endif
            </div>

            <!-- Card Bottom Action Bar -->
            <div class="card-action-bar">
              @if($isOccupied)
                <span class="text-dark fw-bold">
                  ₹{{ number_format($runningTotal, 2) }}
                </span>
                <span class="card-action-btn">
                  <i class="fa-solid fa-receipt"></i> View Bills ({{ $activeBillsCount }})
                </span>
              @elseif($isInactive)
                <span class="text-muted small">Not in service</span>
                <span class="badge bg-secondary text-white">Disabled</span>
              @else
                <span class="text-success fw-bold">Ready</span>
                <span class="card-action-btn">
                  <i class="fa-solid fa-plus-circle"></i> Start Order
                </span>
              @endif
            </div>
          </div>
        </a>
      @endforeach

      <!-- Empty State for 0 Search Results or 0 Tables -->
      <div id="noResultsBox" class="floor-empty-state" style="display: none;">
        <div class="floor-empty-icon">
          <i class="fa-solid fa-magnifying-glass"></i>
        </div>
        <h4 class="floor-empty-title">No Matching Tables Found</h4>
        <p class="floor-empty-desc">No dining tables match your search query or selected filter. Try clearing your search input.</p>
      </div>

      @if($totalTables == 0 && !$canOrder)
      <div class="floor-empty-state">
        <div class="floor-empty-icon">
          <i class="fa-solid fa-chair"></i>
        </div>
        <h4 class="floor-empty-title">No Tables Configured</h4>
        <p class="floor-empty-desc">No floor tables have been set up for this restaurant yet. Please navigate to Table Master to add dining tables.</p>
      </div>
      @endif

    </div>

  </div>
</div>

<!-- ===================================================
     ACTIVE BILLS LUXURY MODAL
     =================================================== -->
<div class="modal fade" id="activeBillsModal" tabindex="-1" role="dialog" aria-labelledby="activeBillsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md" role="document">
    <div class="modal-content modal-pos-content">
      <div class="modal-pos-header">
        <h5 class="modal-pos-title" id="activeBillsModalLabel">
          <i class="fa-solid fa-receipt text-warning"></i>
          Active Bills for <span id="modalTableName" class="text-warning">Table</span>
        </h5>
        <button type="button" class="modal-btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">&times;</button>
      </div>

      <div class="modal-pos-body">
        <div class="mb-3">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-muted small fw-bold text-uppercase">
              <i class="fa-solid fa-list-check me-1"></i> Running Bills / KOTs
            </span>
            <span id="modalBillsCountBadge" class="badge bg-warning text-dark font-weight-bold">0 Bills</span>
          </div>
          
          <!-- Dynamic active bills injected here -->
          <div id="activeBillsList"></div>
        </div>
        
        <!-- Add New Split Bill CTA -->
        <a href="#" id="modalAddNewBillBtn" class="btn-modal-new-bill">
          <i class="fa-solid fa-plus-circle"></i> Add Another Bill to Table
        </a>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
@include('includes.script')

<script>
$(document).ready(function() {
  var activeFilter = 'all';

  // Real-time table filtering & search
  function applyFloorFilters() {
    var searchQuery = $('#tableSearchInput').val().toLowerCase().trim();
    var visibleCount = 0;

    $('.filter-item').each(function() {
      var itemType = $(this).data('type');
      var itemName = $(this).data('name') || '';

      var matchesFilter = (activeFilter === 'all') || (itemType === activeFilter);
      var matchesSearch = (searchQuery === '') || (itemName.indexOf(searchQuery) !== -1);

      if (matchesFilter && matchesSearch) {
        $(this).fadeIn(150);
        visibleCount++;
      } else {
        $(this).hide();
      }
    });

    if (visibleCount === 0) {
      $('#noResultsBox').fadeIn(150);
    } else {
      $('#noResultsBox').hide();
    }
  }

  // Filter pills click handler
  $('.floor-pill-btn').on('click', function() {
    $('.floor-pill-btn').removeClass('active');
    $(this).addClass('active');
    activeFilter = $(this).data('filter');
    applyFloorFilters();
  });

  // Search input handler
  $('#tableSearchInput').on('input', function() {
    var val = $(this).val();
    if (val.length > 0) {
      $('#clearSearchBtn').show();
    } else {
      $('#clearSearchBtn').hide();
    }
    applyFloorFilters();
  });

  // Clear search button handler
  $('#clearSearchBtn').on('click', function() {
    $('#tableSearchInput').val('').focus();
    $(this).hide();
    applyFloorFilters();
  });

  // Occupied table popup modal trigger
  $(document).on('click', '.occupied-table-link', function(e) {
    e.preventDefault();
    var tableId = $(this).data('table-id');
    var tableName = $(this).data('table-name');
    var activeBills = $(this).data('active-bills');
    
    $('#modalTableName').text(tableName);
    
    // Set Add New Bill link
    var createUrl = "{{ route('order.create', ':table_id') }}".replace(':table_id', tableId);
    $('#modalAddNewBillBtn').attr('href', createUrl);
    
    var listDiv = $('#activeBillsList');
    listDiv.empty();
    
    if (activeBills && activeBills.length > 0) {
      $('#modalBillsCountBadge').text(activeBills.length + (activeBills.length === 1 ? ' Bill' : ' Bills'));
      activeBills.forEach(function(bill) {
        var formattedAmount = Number(bill.grand_total).toLocaleString('en-IN', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        });

        var billHtml = `
          <div class="active-bill-row-card">
            <div class="bill-meta-left">
              <div class="bill-order-number">
                <i class="fa-solid fa-file-invoice text-primary me-1"></i> ${bill.order_id}
              </div>
              <div class="bill-guest-subtext">
                <span><i class="fa-solid fa-user me-1 text-secondary"></i> ${bill.customer_name}</span>
                <span>&bull;</span>
                <span><i class="fa-solid fa-clock me-1 text-secondary"></i> ${bill.created_at}</span>
              </div>
            </div>
            <div class="bill-amount-dock">
              <div class="bill-grand-total">₹${formattedAmount}</div>
              <a href="${bill.edit_url}" class="btn-edit-bill">
                <i class="fa-solid fa-pen-to-square"></i> Manage Bill
              </a>
            </div>
          </div>
        `;
        listDiv.append(billHtml);
      });
    } else {
      $('#modalBillsCountBadge').text('0 Bills');
      listDiv.append(`
        <div class="text-center py-4 text-muted">
          <i class="fa-solid fa-receipt fa-2x mb-2 d-block text-muted"></i>
          No active bills found for this table.
        </div>
      `);
    }
    
    $('#activeBillsModal').modal('show');
  });
});
</script>

</body>
</html>