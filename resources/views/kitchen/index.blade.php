<!DOCTYPE html>
<html lang="en">
<head>
  <title>Kitchen Display System (KDS) • Bill&Bite POS</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">

  @include('includes.style')

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Font Awesome 6 CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Kitchen Panel CSS -->
  <link rel="stylesheet" href="{{ asset('admin_template/css/kitchen-panel.css') }}">
</head>
<body>
@include('includes.sidebar')

<div class="pc-container">
  <div class="pc-content">
    
    <div class="kds-container">
      
      <!-- ===================================================
           1. TOP HEADER & KDS CONTROLS
           =================================================== -->
      <div class="kds-header-card">
        <div class="kds-header-left">
          <div class="kds-header-icon">
            <i class="fa-solid fa-fire-burner"></i>
          </div>
          <div class="kds-header-title-wrap">
            <span class="kds-header-eyebrow">Kitchen Display System (KDS)</span>
            <h1 class="kds-header-title">Live Kitchen Orders</h1>
            <p class="kds-header-subtitle">
              Showing KOTs from <strong>{{ date('d M Y', strtotime($from_date)) }}</strong> to <strong>{{ date('d M Y', strtotime($to_date)) }}</strong>
              @if($from_date == date('Y-m-d', strtotime('-1 day')) && $to_date == date('Y-m-d'))
                <span class="badge bg-light-primary text-primary ms-1" style="font-size: 0.7rem; font-weight: 700;">Last 2 Days</span>
              @endif
            </p>
          </div>
        </div>

        <div class="kds-header-actions">
          <!-- Live Auto-Sync Pill -->
          <div class="kds-live-indicator" title="Connected to POS Server">
            <span class="kds-pulse-dot"></span>
            <span class="d-none d-sm-inline">Live Auto-Sync</span>
          </div>

          <!-- Sound Toggle -->
          <button type="button" class="btn-kds-action" id="soundToggleBtn" title="Toggle New Order Audio Chime">
            <i class="fa-solid fa-volume-high text-primary" id="soundIcon"></i>
            <span class="d-none d-md-inline" id="soundLabel">Sound On</span>
          </button>

          <!-- Fullscreen Toggle for Kitchen Monitors -->
          <button type="button" class="btn-kds-action" id="fullscreenToggleBtn" title="Toggle Fullscreen Mode">
            <i class="fa-solid fa-expand"></i>
            <span class="d-none d-md-inline">Fullscreen</span>
          </button>

          <!-- Manual Refresh Button -->
          <button type="button" class="btn-kds-action btn-kds-primary" id="refreshBtn" title="Refresh Orders">
            <i class="fa-solid fa-rotate"></i>
            <span>Refresh</span>
          </button>
        </div>
      </div>

      <!-- Flash Messages -->
      @include('includes.message')

      @php
        $kotList = $kotGroups ?? collect([]);
        $totalKotCount = $kotList->count();
        $pendingKotCount = $kotList->where('status', 'PENDING')->count();
        $cookingKotCount = $kotList->where('status', 'COOKING')->count();
        $doneKotCount = $kotList->where('status', 'DONE')->count();
        $totalDishesCount = count($OrderItems ?? []);
      @endphp

      <!-- ===================================================
           2. QUICK METRICS STAT DECK (KOT GROUPED)
           =================================================== -->
      <div class="kds-stats-grid">
        <!-- All KOT Orders -->
        <div class="kds-stat-card stat-all {{ $selected_status == 'all' ? 'active' : '' }}" data-filter="all">
          <div class="kds-stat-info">
            <span class="kds-stat-label">Total KOT Cards</span>
            <span class="kds-stat-value" id="statAllCount">{{ $totalKotCount }}</span>
            <small class="text-muted" style="font-size: 0.72rem; font-weight: 600; margin-top: 2px;">{{ $totalDishesCount }} dishes total</small>
          </div>
          <div class="kds-stat-badge-icon">
            <i class="fa-solid fa-utensils"></i>
          </div>
        </div>

        <!-- Pending -->
        <div class="kds-stat-card stat-pending {{ $selected_status == 'PENDING' ? 'active' : '' }}" data-filter="PENDING">
          <div class="kds-stat-info">
            <span class="kds-stat-label">⏳ Pending Prep</span>
            <span class="kds-stat-value" id="statPendingCount">{{ $pendingKotCount }}</span>
            <small class="text-muted" style="font-size: 0.72rem; font-weight: 600; margin-top: 2px;">Awaiting cook</small>
          </div>
          <div class="kds-stat-badge-icon">
            <i class="fa-solid fa-clock"></i>
          </div>
        </div>

        <!-- Cooking -->
        <div class="kds-stat-card stat-cooking {{ $selected_status == 'COOKING' ? 'active' : '' }}" data-filter="COOKING">
          <div class="kds-stat-info">
            <span class="kds-stat-label">👨‍🍳 Cooking</span>
            <span class="kds-stat-value" id="statCookingCount">{{ $cookingKotCount }}</span>
            <small class="text-muted" style="font-size: 0.72rem; font-weight: 600; margin-top: 2px;">In preparation</small>
          </div>
          <div class="kds-stat-badge-icon">
            <i class="fa-solid fa-fire"></i>
          </div>
        </div>

        <!-- Ready / Done -->
        <div class="kds-stat-card stat-done {{ $selected_status == 'DONE' ? 'active' : '' }}" data-filter="DONE">
          <div class="kds-stat-info">
            <span class="kds-stat-label">✅ Ready / Done</span>
            <span class="kds-stat-value" id="statDoneCount">{{ $doneKotCount }}</span>
            <small class="text-muted" style="font-size: 0.72rem; font-weight: 600; margin-top: 2px;">Prepared tickets</small>
          </div>
          <div class="kds-stat-badge-icon">
            <i class="fa-solid fa-circle-check"></i>
          </div>
        </div>
      </div>

      <!-- ===================================================
           3. CONTROLS BAR: SEARCH & STATUS TABS
           =================================================== -->
      <div class="kds-controls-bar">
        <!-- Real-Time Search Box -->
        <div class="kds-search-box">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" id="kdsSearchInput" class="kds-search-input" placeholder="Search dish, order #, table, KOT...">
        </div>

        <!-- Status Filter Tabs -->
        <div class="kds-filter-tabs">
          <button type="button" class="kds-tab-btn {{ $selected_status == 'all' ? 'active' : '' }}" data-filter="all">
            <i class="fa-solid fa-border-all"></i> All ({{ $totalKotCount }})
          </button>
          <button type="button" class="kds-tab-btn {{ $selected_status == 'PENDING' ? 'active' : '' }}" data-filter="PENDING">
            ⏳ Pending ({{ $pendingKotCount }})
          </button>
          <button type="button" class="kds-tab-btn {{ $selected_status == 'COOKING' ? 'active' : '' }}" data-filter="COOKING">
            👨‍🍳 Cooking ({{ $cookingKotCount }})
          </button>
          <button type="button" class="kds-tab-btn {{ $selected_status == 'DONE' ? 'active' : '' }}" data-filter="DONE">
            ✅ Done ({{ $doneKotCount }})
          </button>
          <button type="button" class="kds-tab-btn" id="toggleFilterPanelBtn" title="Toggle Date & Table Filter Panel">
            <i class="fa-solid fa-sliders"></i>
            <span class="d-none d-sm-inline">Date &amp; Filter</span>
          </button>
        </div>
      </div>

      <!-- ===================================================
           4. DATE RANGE & TABLE FILTER PANEL (COLLAPSIBLE)
           =================================================== -->
      <div class="kds-date-filter-panel" id="dateFilterPanel" style="{{ ($from_date != date('Y-m-d', strtotime('-1 day')) || $to_date != date('Y-m-d') || !empty($selected_table)) ? '' : 'display: none;' }}">
        <form method="GET" action="{{ route('manage.kitchen-panel') }}" id="filterForm">
          <div class="kds-filter-grid">
            <div class="kds-form-group">
              <label class="kds-form-label"><i class="fa-solid fa-calendar-days text-primary"></i> From Date</label>
              <input type="date" name="from_date" id="formFromDate" class="kds-form-control" value="{{ $from_date }}" max="{{ date('Y-m-d') }}">
            </div>
            
            <div class="kds-form-group">
              <label class="kds-form-label"><i class="fa-solid fa-calendar-days text-primary"></i> To Date</label>
              <input type="date" name="to_date" id="formToDate" class="kds-form-control" value="{{ $to_date }}" max="{{ date('Y-m-d') }}">
            </div>

            <div class="kds-form-group">
              <label class="kds-form-label"><i class="fa-solid fa-filter text-primary"></i> Status Filter</label>
              <select name="status" class="kds-form-control" id="formStatusSelect">
                <option value="all" {{ $selected_status == 'all' ? 'selected' : '' }}>All Statuses</option>
                <option value="PENDING" {{ $selected_status == 'PENDING' ? 'selected' : '' }}>⏳ Pending</option>
                <option value="COOKING" {{ $selected_status == 'COOKING' ? 'selected' : '' }}>👨‍🍳 Cooking</option>
                <option value="DONE" {{ $selected_status == 'DONE' ? 'selected' : '' }}>✅ Done</option>
              </select>
            </div>

            <div class="kds-form-group">
              <label class="kds-form-label"><i class="fa-solid fa-chair text-primary"></i> Table</label>
              <select name="table_id" class="kds-form-control">
                <option value="">All Tables / Take Away</option>
                @foreach($tables as $table)
                  <option value="{{ $table->id }}" {{ $selected_table == $table->id ? 'selected' : '' }}>{{ $table->name }}</option>
                @endforeach
              </select>
            </div>

            <div class="kds-filter-btn-group">
              <button type="submit" class="btn-kds-action btn-kds-primary">
                <i class="fa-solid fa-magnifying-glass"></i> Apply
              </button>
              <a href="{{ route('manage.kitchen-panel') }}" class="btn-kds-action">
                <i class="fa-solid fa-rotate-left"></i> Reset (Last 2 Days)
              </a>
            </div>
          </div>

          <!-- Quick Date Presets -->
          <div class="kds-quick-date-pills">
            <span class="text-muted me-1" style="font-size: 0.74rem; font-weight: 700;">Quick Presets:</span>
            <button type="button" class="kds-quick-date-btn {{ ($from_date == date('Y-m-d', strtotime('-1 day')) && $to_date == date('Y-m-d')) ? 'active' : '' }}" data-days="2">
              Last 2 Days (Default)
            </button>
            <button type="button" class="kds-quick-date-btn {{ ($from_date == date('Y-m-d') && $to_date == date('Y-m-d')) ? 'active' : '' }}" data-days="1">
              Today Only
            </button>
            <button type="button" class="kds-quick-date-btn {{ ($from_date == date('Y-m-d', strtotime('-6 days')) && $to_date == date('Y-m-d')) ? 'active' : '' }}" data-days="7">
              Last 7 Days
            </button>
            <button type="button" class="kds-quick-date-btn {{ ($from_date == date('Y-m-01') && $to_date == date('Y-m-d')) ? 'active' : '' }}" data-days="month">
              This Month
            </button>
          </div>
        </form>
      </div>

      <!-- ===================================================
           5. KDS ORDERS GRID (MERGED BY KOT NUMBER & ORDER)
           =================================================== -->
      <div class="kds-orders-grid" id="kdsOrdersGrid">
        @forelse($kotList as $kot)
        @php
          $tableName = $kot->table_name;
          $orderNo = $kot->order_no;
          $kotNo = $kot->kot_no ?? 'N/A';
          $status = $kot->status ?? 'PENDING';
          
          // Build search string combining all dishes, notes, and addons in this KOT
          $allDishNames = [];
          $allNotes = [];
          $allAddons = [];
          foreach($kot->items as $it) {
            $allDishNames[] = @$it->subcategory->name ?? 'Dish Item';
            if (!empty($it->note)) $allNotes[] = $it->note;
            if (!empty($it->addons_list) && !empty($it->subcategory_id)) {
              foreach($it->addons_list as $add) {
                $allAddons[] = $add['name'] ?? '';
              }
            }
          }
          $searchData = strtolower($orderNo . ' ' . $kotNo . ' ' . $tableName . ' ' . implode(' ', $allDishNames) . ' ' . implode(' ', $allNotes) . ' ' . implode(' ', $allAddons));
        @endphp
        
        <div class="kds-order-card {{ $status }}" 
             data-status="{{ $status }}" 
             data-search="{{ $searchData }}" 
             data-card-id="{{ $kot->card_id }}"
             data-kot-no="{{ $kot->kot_no }}"
             data-order-id="{{ $kot->order_id }}"
             data-item-ids="{{ json_encode($kot->item_ids) }}"
             id="card_{{ $kot->card_id }}">
          
          <!-- Card Header -->
          <div class="kds-card-header">
            <div class="kds-card-top-row">
              <span class="kds-order-badge">
                <i class="fa-solid fa-receipt text-primary"></i> #ORD-{{ $orderNo }}
              </span>

              <div style="display: flex; align-items: center; gap: 8px;">
                @if($kot->kot_no)
                <span class="kds-kot-badge">
                  <i class="fa-solid fa-fire text-warning"></i> {{ $kot->kot_no }}
                </span>
                @endif

                @if($kot->total_dishes > 1)
                <span class="kds-items-count-pill" title="Multiple dishes in this KOT">
                  <i class="fa-solid fa-layer-group"></i> {{ $kot->total_dishes }} Items
                </span>
                @endif

                <!-- Print KOT Trigger Button (prints entire KOT lot) -->
                <button type="button" class="btn-kds-print print-kot-trigger" 
                        data-id="{{ $kot->primary_item_id }}"
                        title="Print KOT Ticket">
                  <i class="fa-solid fa-print"></i>
                </button>
              </div>
            </div>

            <div class="kds-card-meta-row">
              <span class="kds-meta-item">
                <i class="fa-regular fa-clock"></i> {{ $kot->created_at ? $kot->created_at->format('d M, h:i A') : 'Just now' }}
              </span>

              <span class="kds-table-pill">
                @if(!$kot->is_takeaway)
                  <i class="fa-solid fa-chair text-primary"></i> {{ $tableName }}
                @else
                  <i class="fa-solid fa-bag-shopping text-success"></i> Take Away
                @endif
              </span>
            </div>
          </div>

          <!-- Card Body: Contains All Dishes in this KOT -->
          <div class="kds-card-body">
            <div class="kds-dishes-deck">
              @foreach($kot->items as $item)
              @php
                $dishName = @$item->subcategory->name ?? 'Dish Item';
                $foodType = strtoupper(@$item->subcategory->food_type ?? 'VEG');
                $addons = $item->addons_list;
              @endphp
              <div class="kds-dish-block" id="item_{{ $item->id }}">
                <div class="kds-dish-title-row">
                  <div>
                    <h3 class="kds-dish-name">{{ $dishName }}</h3>
                    <span class="kds-food-type-tag {{ $foodType }}">
                      <span class="kds-type-dot"></span>
                      {{ $foodType }}
                    </span>
                  </div>

                  <div class="kds-qty-badge" title="Quantity to Prepare">
                    x{{ $item->quantity }}
                  </div>
                </div>

                @if(!empty($addons) && !empty($item->subcategory_id))
                <div class="kds-addons-container">
                  <div class="kds-addons-header">
                    <i class="fa-solid fa-puzzle-piece"></i> Mapped Add-ons ({{ count($addons) }})
                  </div>
                  <div class="kds-addons-list">
                    @foreach($addons as $a)
                    @php
                      $aQty = $a['qty'] ?? $a['quantity'] ?? 1;
                      $aName = $a['name'] ?? 'Add-on';
                      $aFoodType = strtoupper($a['food_type'] ?? 'VEG');
                    @endphp
                    <div class="kds-addon-item-row">
                      <div class="kds-addon-left">
                        <span class="kds-addon-pill"><i class="fa-solid fa-plus me-1"></i>ADD-ON</span>
                        <span class="kds-food-type-tag {{ $aFoodType }}" style="margin-bottom: 0; padding: 1px 6px; font-size: 0.6rem;">
                          <span class="kds-type-dot"></span>
                          {{ $aFoodType }}
                        </span>
                        <span class="kds-addon-title" title="{{ $aName }}">{{ $aName }}</span>
                      </div>
                      <div class="kds-addon-qty-chip" title="Add-on Quantity">
                        x{{ $aQty }}
                      </div>
                    </div>
                    @endforeach
                  </div>
                </div>
                @endif

                @if($item->note)
                <div class="kds-order-note">
                  <i class="fa-solid fa-note-sticky"></i>
                  <span><strong>Note:</strong> {{ $item->note }}</span>
                </div>
                @endif
              </div>
              @endforeach
            </div>
          </div>

          <!-- Card Footer: Unified 1-Tap Status Switcher for entire KOT -->
          <div class="kds-card-footer">
            <div class="kds-status-segmented" 
                 data-card-id="{{ $kot->card_id }}"
                 data-kot-no="{{ $kot->kot_no }}"
                 data-order-id="{{ $kot->order_id }}"
                 data-item-ids="{{ json_encode($kot->item_ids) }}"
                 data-primary-id="{{ $kot->primary_item_id }}">
              <button type="button" class="kds-status-btn {{ $status == 'PENDING' ? 'active' : '' }}" data-status="PENDING">
                ⏳ Pending
              </button>
              <button type="button" class="kds-status-btn {{ $status == 'COOKING' ? 'active' : '' }}" data-status="COOKING">
                👨‍🍳 Cooking
              </button>
              <button type="button" class="kds-status-btn {{ $status == 'DONE' ? 'active' : '' }}" data-status="DONE">
                ✅ Done
              </button>
            </div>
          </div>

        </div><!-- /.kds-order-card -->
        @empty
        <!-- Empty State -->
        <div class="kds-empty-deck" id="kdsEmptyDeck">
          <div class="kds-empty-icon">
            <i class="fa-solid fa-kitchen-set"></i>
          </div>
          <h3 class="kds-empty-title">No Kitchen Orders Found</h3>
          <p class="kds-empty-desc">All current kitchen order tickets (KOT) have been prepared or no orders match the selected filters.</p>
          <a href="{{ route('manage.kitchen-panel') }}" class="btn-kds-action btn-kds-primary">
            <i class="fa-solid fa-rotate-left"></i> Reset to Last 2 Days
          </a>
        </div>
        @endforelse
      </div>

    </div><!-- /.kds-container -->

  </div><!-- /.pc-content -->
</div><!-- /.pc-container -->


<!-- Hidden Audio Element for New Order Audio Alert -->
<audio id="kotAudioAlert" preload="auto" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3"></audio>

<!-- JS & Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
@include('includes.script')

<script>
$(document).ready(function() {
  let soundEnabled = true;
  let statusChangeInProgress = false;
  let currentActiveFilter = "{{ $selected_status ?? 'all' }}";

  // 1. Fullscreen Toggle
  $('#fullscreenToggleBtn').on('click', function() {
    if (!document.fullscreenElement) {
      document.documentElement.requestFullscreen().catch(err => {
        console.warn(`Error attempting to enable fullscreen: ${err.message}`);
      });
      $(this).find('i').removeClass('fa-expand').addClass('fa-compress');
    } else {
      if (document.exitFullscreen) {
        document.exitFullscreen();
        $(this).find('i').removeClass('fa-compress').addClass('fa-expand');
      }
    }
  });

  // 2. Sound Toggle
  $('#soundToggleBtn').on('click', function() {
    soundEnabled = !soundEnabled;
    if (soundEnabled) {
      $('#soundIcon').removeClass('fa-volume-xmark text-muted').addClass('fa-volume-high text-primary');
      $('#soundLabel').text('Sound On');
      showToast('Sound alerts enabled', 'info');
      playChime();
    } else {
      $('#soundIcon').removeClass('fa-volume-high text-primary').addClass('fa-volume-xmark text-muted');
      $('#soundLabel').text('Sound Muted');
      showToast('Sound alerts muted', 'info');
    }
  });

  function playChime() {
    if (!soundEnabled) return;
    try {
      const audio = document.getElementById('kotAudioAlert');
      if (audio) {
        audio.currentTime = 0;
        audio.play().catch(e => {
          synthBeep();
        });
      } else {
        synthBeep();
      }
    } catch(e) {
      synthBeep();
    }
  }

  function synthBeep() {
    try {
      const ctx = new (window.AudioContext || window.webkitAudioContext)();
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      osc.connect(gain);
      gain.connect(ctx.destination);
      osc.type = 'sine';
      osc.frequency.setValueAtTime(587.33, ctx.currentTime);
      osc.frequency.setValueAtTime(880, ctx.currentTime + 0.15);
      gain.gain.setValueAtTime(0.3, ctx.currentTime);
      gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.4);
      osc.start();
      osc.stop(ctx.currentTime + 0.4);
    } catch(e) {}
  }

  // 3. Toggle Date Filter Panel & Quick Presets
  $('#toggleFilterPanelBtn').on('click', function() {
    $('#dateFilterPanel').slideToggle(200);
  });

  $('.kds-quick-date-btn').on('click', function() {
    const days = $(this).data('days');
    const today = new Date();
    const formatDate = d => d.toISOString().split('T')[0];

    let fromD = new Date();
    let toD = new Date();

    if (days === 1) {
      // Today
      fromD = today;
    } else if (days === 2) {
      // Last 2 days
      fromD.setDate(today.getDate() - 1);
    } else if (days === 7) {
      // Last 7 days
      fromD.setDate(today.getDate() - 6);
    } else if (days === 'month') {
      // 1st of this month
      fromD = new Date(today.getFullYear(), today.getMonth(), 1);
    }

    $('#formFromDate').val(formatDate(fromD));
    $('#formToDate').val(formatDate(toD));
    $('#filterForm').submit();
  });

  // 4. Live Client-Side Search & Filter
  $('#kdsSearchInput').on('keyup', function() {
    applyFilters();
  });

  $('.kds-tab-btn, .kds-stat-card').on('click', function() {
    const filter = $(this).data('filter');
    if (!filter) return;
    
    currentActiveFilter = filter;
    
    // Update active states
    $('.kds-tab-btn').removeClass('active');
    $(`.kds-tab-btn[data-filter="${filter}"]`).addClass('active');

    $('.kds-stat-card').removeClass('active');
    $(`.kds-stat-card[data-filter="${filter}"]`).addClass('active');

    $('#formStatusSelect').val(filter);

    applyFilters();
  });

  function applyFilters() {
    const query = ($('#kdsSearchInput').val() || '').toLowerCase().trim();
    let visibleCount = 0;

    $('.kds-order-card').each(function() {
      const card = $(this);
      const status = card.attr('data-status') || '';
      const searchData = card.attr('data-search') || '';

      const matchesSearch = query === '' || searchData.includes(query);
      const matchesStatus = (currentActiveFilter === 'all') || (status === currentActiveFilter);

      if (matchesSearch && matchesStatus) {
        card.show();
        visibleCount++;
      } else {
        card.hide();
      }
    });

    if (visibleCount === 0 && $('.kds-order-card').length > 0) {
      if (!$('#noSearchResults').length) {
        $('#kdsOrdersGrid').append(`
          <div class="kds-empty-deck" id="noSearchResults">
            <div class="kds-empty-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
            <h3 class="kds-empty-title">No Matching Orders</h3>
            <p class="kds-empty-desc">No kitchen tickets found matching "${query}" in this view.</p>
          </div>
        `);
      }
      $('#noSearchResults').show();
    } else {
      $('#noSearchResults').remove();
    }
  }

  // 5. One-Tap Status Update (Batch updates entire KOT ticket)
  $(document).on('click', '.kds-status-btn', function(e) {
    e.preventDefault();
    if (statusChangeInProgress) return;

    const btn = $(this);
    const targetStatus = btn.data('status');
    const segmentedWrap = btn.closest('.kds-status-segmented');
    const cardId = segmentedWrap.data('card-id');
    const kotNo = segmentedWrap.data('kot-no');
    const orderId = segmentedWrap.data('order-id');
    const itemIds = segmentedWrap.data('item-ids');
    const primaryId = segmentedWrap.data('primary-id');

    const card = $(`#card_${cardId}`);
    const currentStatus = card.attr('data-status');

    if (targetStatus === currentStatus) return;

    statusChangeInProgress = true;

    // Optimistic UI Update
    segmentedWrap.find('.kds-status-btn').removeClass('active');
    btn.addClass('active');
    card.removeClass('PENDING COOKING DONE').addClass(targetStatus).attr('data-status', targetStatus);
    card.css('opacity', '0.75');

    // Update Counts Dynamically
    updateMetricsCount(currentStatus, targetStatus);

    const postPayload = {
      _token: "{{ csrf_token() }}",
      order_status: targetStatus
    };

    if (kotNo && kotNo !== 'N/A') {
      postPayload.kot_no = kotNo;
      postPayload.order_id = orderId;
    }
    if (itemIds && itemIds.length) {
      postPayload.item_ids = itemIds;
    }
    if (primaryId) {
      postPayload.id = primaryId;
    }

    $.ajax({
      url: "{{ route('update.kitchen.status') }}",
      method: "POST",
      data: postPayload,
      success: function(res) {
        if (res.success) {
          const displayLabel = kotNo && kotNo !== 'N/A' ? kotNo : `#ORD-${orderId}`;
          showToast(`✓ ${displayLabel} marked as ${targetStatus}`, 'success');
        } else {
          revertStatus();
          showToast('✗ Status update failed', 'error');
        }
      },
      error: function() {
        revertStatus();
        showToast('⚠️ Server connection error', 'error');
      },
      complete: function() {
        card.css('opacity', '1');
        statusChangeInProgress = false;
        applyFilters();
      }
    });

    function revertStatus() {
      segmentedWrap.find('.kds-status-btn').removeClass('active');
      segmentedWrap.find(`[data-status="${currentStatus}"]`).addClass('active');
      card.removeClass('PENDING COOKING DONE').addClass(currentStatus).attr('data-status', currentStatus);
      updateMetricsCount(targetStatus, currentStatus);
    }
  });

  function updateMetricsCount(oldStatus, newStatus) {
    const statPending = $('#statPendingCount');
    const statCooking = $('#statCookingCount');
    const statDone = $('#statDoneCount');

    let p = parseInt(statPending.text()) || 0;
    let c = parseInt(statCooking.text()) || 0;
    let d = parseInt(statDone.text()) || 0;

    if (oldStatus === 'PENDING') p = Math.max(0, p - 1);
    if (oldStatus === 'COOKING') c = Math.max(0, c - 1);
    if (oldStatus === 'DONE') d = Math.max(0, d - 1);

    if (newStatus === 'PENDING') p++;
    if (newStatus === 'COOKING') c++;
    if (newStatus === 'DONE') d++;

    statPending.text(p);
    statCooking.text(c);
    statDone.text(d);

    $(`.kds-tab-btn[data-filter="PENDING"]`).html(`⏳ Pending (${p})`);
    $(`.kds-tab-btn[data-filter="COOKING"]`).html(`👨‍🍳 Cooking (${c})`);
    $(`.kds-tab-btn[data-filter="DONE"]`).html(`✅ Done (${d})`);
  }

  // 6. Manual Refresh
  $('#refreshBtn').on('click', function() {
    const btn = $(this);
    btn.find('i').addClass('fa-spin');
    location.reload();
  });

  // 7. Auto-Sync Polling (checks every 15 seconds)
  setInterval(function() {
    $.ajax({
      url: "{{ route('kitchen.orders.refresh') }}",
      method: "GET",
      success: function(res) {
        if (res.new_orders && res.count > 0) {
          playChime();
          showToast(`🔔 ${res.count} new kitchen order(s) received! Refreshing...`, 'info');
          setTimeout(() => location.reload(), 1500);
        }
      }
    });
  }, 15000);

  // 8. Direct KOT Ticket Thermal Printing
  $(document).on('click', '.print-kot-trigger', function(e) {
    e.preventDefault();
    const btn = $(this);
    const id = btn.data('id');
    const originalHtml = btn.html();

    btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');

    const pdfUrl = "{{ route('kitchen.kot.pdf', ':id') }}".replace(':id', id);

    $('#kotPdfFrame').remove();

    const iframe = $('<iframe>', {
      id: 'kotPdfFrame',
      src: pdfUrl,
      style: 'position: absolute; width: 0; height: 0; border: 0; visibility: hidden;'
    }).appendTo('body');

    iframe[0].onload = function() {
      btn.prop('disabled', false).html(originalHtml);
      try {
        iframe[0].contentWindow.focus();
        iframe[0].contentWindow.print();
      } catch(e) {
        window.open(pdfUrl, '_blank');
      }
    };

    setTimeout(function() {
      btn.prop('disabled', false).html(originalHtml);
    }, 5000);
  });

  // 9. Toast Notification Helper
  function showToast(message, type = 'info') {
    $('.kds-toast').remove();
    const toast = $(`<div class="kds-toast ${type}">${message}</div>`);
    $('body').append(toast);

    setTimeout(function() {
      toast.fadeOut(300, function() {
        $(this).remove();
      });
    }, 3200);
  }

});
</script>

</body>
</html>