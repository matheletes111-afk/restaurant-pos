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

  <style>
    /* =========================================================
       LUXURY 6-CARDS PER ROW & CATEGORY GROUPED ARCHITECTURE
       ========================================================= */

    /* Category Section Box */
    .category-floor-section {
      background: #ffffff;
      border-radius: 18px;
      border: 1px solid #e2e8f0;
      padding: 16px 20px 20px 20px;
      margin-bottom: 22px;
      box-shadow: 0 3px 16px -2px rgba(15, 23, 42, 0.04);
      transition: all 0.25s ease;
    }

    .category-floor-section:hover {
      box-shadow: 0 8px 24px -4px rgba(15, 23, 42, 0.08);
      border-color: #cbd5e1;
    }

    /* Category Header */
    .category-section-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 14px;
      padding-bottom: 10px;
      border-bottom: 1.5px dashed #f1f5f9;
      gap: 12px;
      flex-wrap: wrap;
    }

    .category-header-title-wrap {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .category-header-icon {
      width: 32px;
      height: 32px;
      border-radius: 9px;
      background: linear-gradient(135deg, rgba(255, 94, 20, 0.12) 0%, rgba(255, 140, 66, 0.22) 100%);
      color: #ff5e14;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 0.9rem;
      flex-shrink: 0;
    }

    .category-section-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.12rem;
      font-weight: 800;
      color: #0f172a;
      margin: 0;
      letter-spacing: -0.01em;
    }

    .category-count-chip {
      font-size: 0.74rem;
      font-weight: 700;
      background: #f1f5f9;
      color: #475569;
      padding: 2.5px 10px;
      border-radius: 9999px;
      border: 1px solid #e2e8f0;
    }

    .category-header-meta {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .cat-meta-stat {
      font-size: 0.74rem;
      font-weight: 700;
      padding: 3px 9px;
      border-radius: 6px;
      display: inline-flex;
      align-items: center;
      gap: 4px;
    }

    .cat-meta-stat.meta-vacant {
      background: rgba(16, 185, 129, 0.1);
      color: #059669;
      border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .cat-meta-stat.meta-occupied {
      background: rgba(245, 158, 11, 0.12);
      color: #d97706;
      border: 1px solid rgba(245, 158, 11, 0.25);
    }

    /* 6-CARDS PER LINE GRID */
    .category-cards-grid {
      display: grid;
      grid-template-columns: repeat(6, 1fr);
      gap: 12px;
    }

    @media (max-width: 1599px) {
      .category-cards-grid {
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
      }
    }

    @media (max-width: 1299px) {
      .category-cards-grid {
        grid-template-columns: repeat(4, 1fr);
      }
    }

    @media (max-width: 991px) {
      .category-cards-grid {
        grid-template-columns: repeat(3, 1fr);
      }
    }

    @media (max-width: 640px) {
      .category-cards-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 420px) {
      .category-cards-grid {
        grid-template-columns: 1fr;
      }
    }

    /* =========================================================
       COMPACT SHORT HEIGHT PREMIUM DINING CARD
       ========================================================= */
    .table-card-anchor {
      text-decoration: none !important;
      color: inherit !important;
      display: block;
    }

    .compact-table-card {
      background: #ffffff;
      border-radius: 12px;
      border: 1.5px solid #e2e8f0;
      box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);
      padding: 10px 12px;
      min-height: 112px;
      max-height: 124px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      position: relative;
      overflow: hidden;
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
      cursor: pointer;
    }

    .compact-table-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3.5px;
      background: var(--card-bar, #94a3b8);
      transition: height 0.2s ease;
    }

    .compact-table-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 20px -3px rgba(15, 23, 42, 0.1);
      border-color: var(--card-bar, #94a3b8);
    }

    .compact-table-card:hover::before {
      height: 5px;
    }

    /* Card Top Line: Table Name & Status Badge */
    .card-row-top {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 6px;
    }

    .card-table-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.02rem;
      font-weight: 800;
      color: #0f172a;
      letter-spacing: -0.01em;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .card-table-id {
      font-size: 0.68rem;
      font-weight: 700;
      color: #94a3b8;
    }

    .card-status-pill {
      font-size: 0.66rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      padding: 2px 7px;
      border-radius: 9999px;
      display: inline-flex;
      align-items: center;
      gap: 4px;
      flex-shrink: 0;
    }

    /* Card Middle Row */
    .card-row-mid {
      font-size: 0.74rem;
      color: #64748b;
      margin: 4px 0 5px 0;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 6px;
    }

    .card-row-mid .mid-left {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      flex: 1;
    }

    .card-row-mid .mid-right {
      flex-shrink: 0;
      font-weight: 600;
      color: #94a3b8;
      font-size: 0.7rem;
    }

    /* Card Bottom Row */
    .card-row-bottom {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding-top: 6px;
      border-top: 1px dashed #f1f5f9;
      font-size: 0.74rem;
      gap: 6px;
    }

    /* THEME: VACANT / READY */
    .card-theme-vacant {
      --card-bar: #10b981;
    }
    .card-theme-vacant .card-status-pill {
      background: rgba(16, 185, 129, 0.1);
      color: #059669;
      border: 1px solid rgba(16, 185, 129, 0.25);
    }
    .card-theme-vacant .pulse-dot {
      width: 5px;
      height: 5px;
      background: #10b981;
      border-radius: 50%;
      display: inline-block;
      animation: vacantPulse 1.8s infinite;
    }
    @keyframes vacantPulse {
      0% { opacity: 1; transform: scale(1); }
      50% { opacity: 0.4; transform: scale(1.3); }
      100% { opacity: 1; transform: scale(1); }
    }
    .card-theme-vacant .vacant-cue-text {
      color: #059669;
      font-weight: 700;
      font-size: 0.72rem;
    }
    .card-theme-vacant .btn-card-action {
      background: #ecfdf5;
      color: #059669;
      border: 1px solid #a7f3d0;
      padding: 2.5px 7px;
      border-radius: 6px;
      font-weight: 700;
      font-size: 0.69rem;
      display: inline-flex;
      align-items: center;
      gap: 3px;
      transition: all 0.15s ease;
    }
    .card-theme-vacant:hover .btn-card-action {
      background: #10b981;
      color: #ffffff;
      border-color: #10b981;
    }

    /* THEME: OCCUPIED */
    .card-theme-occupied {
      --card-bar: #f59e0b;
      background: linear-gradient(180deg, #ffffff 0%, #fffdfa 100%);
      border-color: rgba(245, 158, 11, 0.35);
    }
    .card-theme-occupied .card-status-pill {
      background: rgba(245, 158, 11, 0.14);
      color: #d97706;
      border: 1px solid rgba(245, 158, 11, 0.35);
    }
    .card-theme-occupied .occupied-total {
      font-family: 'Outfit', sans-serif;
      font-size: 0.95rem;
      font-weight: 800;
      color: #b45309;
      letter-spacing: -0.01em;
    }
    .card-theme-occupied .btn-card-action {
      background: rgba(245, 158, 11, 0.14);
      color: #b45309;
      border: 1px solid rgba(245, 158, 11, 0.3);
      padding: 2.5px 7px;
      border-radius: 6px;
      font-weight: 700;
      font-size: 0.69rem;
      display: inline-flex;
      align-items: center;
      gap: 3px;
      transition: all 0.15s ease;
    }
    .card-theme-occupied:hover .btn-card-action {
      background: #f59e0b;
      color: #ffffff;
      border-color: #f59e0b;
    }

    /* THEME: INACTIVE */
    .card-theme-inactive {
      --card-bar: #94a3b8;
      background: #f8fafc;
      opacity: 0.65;
    }
    .card-theme-inactive .card-status-pill {
      background: #e2e8f0;
      color: #64748b;
    }

    /* Print Button & Modal Preview Enhancements */
    .bill-action-row {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .btn-print-bill {
      width: 34px;
      height: 34px;
      border-radius: 50%;
      background: #ffffff;
      border: 1.5px solid #cbd5e1;
      color: #1e293b;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 0.88rem;
      cursor: pointer;
      box-shadow: 0 2px 6px rgba(15, 23, 42, 0.05);
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
      padding: 0;
      flex-shrink: 0;
      text-decoration: none;
    }
    .btn-print-bill:hover {
      background: #0f172a;
      color: #ffffff;
      border-color: #0f172a;
      transform: translateY(-2px);
      box-shadow: 0 6px 14px rgba(15, 23, 42, 0.2);
    }

  </style>
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

  // Group tables by Category Name
  $tablesByCategory = $tableList->groupBy(function($t) {
      if ($t->category && $t->category->status !== 'D') {
          return $t->category->name;
      }
      return 'General Category';
  });

  // Sort groups: named categories first, General Category at the end
  $sortedCategories = $tablesByCategory->keys()->sort(function($a, $b) {
      if ($a === 'General Category') return 1;
      if ($b === 'General Category') return -1;
      return strcasecmp($a, $b);
  });
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
        <a href="{{ route('rapid.bill') }}" class="btn text-white fw-bold px-3 py-2 d-inline-flex align-items-center gap-2" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25); border-radius: 30px; font-size: 0.88rem; text-decoration: none;" title="Open Rapid Bill POS">
          <i class="fa-solid fa-receipt text-warning"></i> Rapid Bill POS
        </a>
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
          <i class="fa-solid fa-table-cells-large"></i> All Tables
          <span class="pill-counter">{{ $totalTables }}</span>
        </button>

        <button type="button" class="floor-pill-btn pill-available" data-filter="available">
          <i class="fa-solid fa-circle-check text-success"></i> Vacant Tables
          <span class="pill-counter">{{ $availableCount }}</span>
        </button>

        <button type="button" class="floor-pill-btn pill-occupied" data-filter="occupied">
          <i class="fa-solid fa-fire text-warning"></i> Occupied Tables
          <span class="pill-counter">{{ $occupiedCount }}</span>
        </button>
      </div>

      <!-- Real-time Instant Search -->
      <div class="floor-search-box">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        <input type="text" id="tableSearchInput" class="floor-search-input" placeholder="Search tables (e.g. Table 1, VIP)..." autocomplete="off">
        <button type="button" id="clearSearchBtn" class="search-clear-btn" style="display: none;" title="Clear search">&times;</button>
      </div>
    </div>

    <!-- ===================================================
         CATEGORY-WISE FLOOR SECTIONS (6 CARDS PER LINE)
         =================================================== -->
    <div id="floorCategoriesContainer">
      @forelse($sortedCategories as $categoryName)
        @php
          $catTables = $tablesByCategory[$categoryName];
          // Skip if category has no tables
          if ($catTables->count() == 0) {
              continue;
          }
          $catVacant = $catTables->filter(fn($t) => $t->table_status != 'INACTIVE' && $t->activeOrders->count() == 0)->count();
          $catOccupied = $catTables->filter(fn($t) => $t->table_status != 'INACTIVE' && $t->activeOrders->count() > 0)->count();
        @endphp

        <div class="category-floor-section" data-category="{{ Str::slug($categoryName) }}">
          <!-- Category Section Header -->
          <div class="category-section-header">
            <div class="category-header-title-wrap">
              <span class="category-header-icon">
                <i class="fa-solid fa-layer-group"></i>
              </span>
              <h3 class="category-section-title">{{ $categoryName }}</h3>
              <span class="category-count-chip">
                {{ $catTables->count() }} {{ Str::plural('Table', $catTables->count()) }}
              </span>
            </div>

            <div class="category-header-meta">
              <span class="cat-meta-stat meta-vacant">
                <i class="fa-solid fa-circle-check"></i> {{ $catVacant }} Vacant
              </span>
              @if($catOccupied > 0)
                <span class="cat-meta-stat meta-occupied">
                  <i class="fa-solid fa-fire"></i> {{ $catOccupied }} Occupied
                </span>
              @endif
            </div>
          </div>

          <!-- 6 Cards Per Line Compact Grid -->
          <div class="category-cards-grid">
            @foreach($catTables as $table)
              @php
                $activeOrders = $table->activeOrders;
                $activeBillsCount = $activeOrders->count();
                $isInactive = ($table->table_status == 'INACTIVE');
                $isOccupied = (!$isInactive && $activeBillsCount > 0);
                $isAvailable = (!$isInactive && $activeBillsCount == 0);

                if ($isInactive) {
                    $cardType = 'inactive';
                    $themeClass = 'card-theme-inactive';
                    $statusText = 'Offline';
                    $url = 'javascript:void(0);';
                    $linkClass = '';
                    $cursorStyle = 'pointer-events:none; cursor:not-allowed;';
                } elseif ($isOccupied) {
                    $cardType = 'occupied';
                    $themeClass = 'card-theme-occupied';
                    $statusText = $activeBillsCount . ' ' . Str::plural('Bill', $activeBillsCount);
                    $url = 'javascript:void(0);';
                    $linkClass = 'occupied-table-link';
                    $cursorStyle = '';
                    $firstCustomer = $activeOrders->first()->customer_name ?? 'Guest';
                    $firstOrderTime = $activeOrders->first()->created_at ? $activeOrders->first()->created_at->diffForHumans(null, true) : null;
                    $runningTotal = $activeOrders->sum('grand_total');
                } else {
                    $cardType = 'available';
                    $themeClass = 'card-theme-vacant';
                    $statusText = 'Vacant';
                    $url = route('order.create', $table->id);
                    $linkClass = '';
                    $cursorStyle = '';
                }
              @endphp

              <a href="{{ $url }}" 
                 class="table-card-anchor filter-item {{ $linkClass }}" 
                 data-type="{{ $cardType }}"
                 data-name="{{ strtolower($table->name . ' ' . $categoryName . ' ' . ($table->description ?? '')) }}"
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
                           'edit_url' => route('order.edit', $o->id),
                            'receipt_url' => route('order.receipt.pdf', $o->id),
                            'invoice_url' => route('order.invoice', $o->id)
                       ];
                   })) }}"
                 @endif
                 style="{{ $cursorStyle }}">
                
                <div class="compact-table-card {{ $themeClass }}">
                  <!-- Top Row: Table Name & Status Badge -->
                  <div class="card-row-top">
                    <h4 class="card-table-title" title="{{ $table->name }}">
                      {{ $table->name }}
                      <span class="card-table-id">#{{ $table->id }}</span>
                    </h4>
                    
                    <span class="card-status-pill">
                      @if($isOccupied)
                        <i class="fa-solid fa-fire text-warning"></i> {{ $statusText }}
                      @elseif($isInactive)
                        <i class="fa-solid fa-ban text-muted"></i> {{ $statusText }}
                      @else
                        <span class="pulse-dot"></span> {{ $statusText }}
                      @endif
                    </span>
                  </div>

                  <!-- Middle Row: Context Info -->
                  <div class="card-row-mid">
                    @if($isOccupied)
                      <span class="mid-left text-truncate">
                        <i class="fa-solid fa-user-tag me-1 text-secondary"></i> {{ $firstCustomer }}
                      </span>
                      @if($firstOrderTime)
                        <span class="mid-right">
                          <i class="fa-solid fa-clock me-1"></i> {{ $firstOrderTime }}
                        </span>
                      @endif
                    @elseif(!empty($table->description))
                      <span class="mid-left text-truncate" title="{{ $table->description }}">
                        <i class="fa-solid fa-chair me-1 text-secondary"></i> {{ $table->description }}
                      </span>
                    @else
                      <span class="mid-left text-muted">
                        <i class="fa-solid fa-chair me-1 text-secondary"></i> Ready for guests
                      </span>
                    @endif
                  </div>

                  <!-- Bottom Row: Price / Quick Action -->
                  <div class="card-row-bottom">
                    @if($isOccupied)
                      <span class="occupied-total">
                        ₹{{ number_format($runningTotal, 2) }}
                      </span>
                      <span class="btn-card-action">
                        Bills <i class="fa-solid fa-arrow-right"></i>
                      </span>
                    @elseif($isInactive)
                      <span class="text-muted small">Offline</span>
                      <span class="badge bg-secondary text-white" style="font-size:0.65rem;">Disabled</span>
                    @else
                      <span class="vacant-cue-text">Open</span>
                      <span class="btn-card-action">
                        <i class="fa-solid fa-plus"></i> Order
                      </span>
                    @endif
                  </div>
                </div>
              </a>
            @endforeach
          </div>
        </div>
      @empty
        <div class="floor-empty-state">
          <div class="floor-empty-icon">
            <i class="fa-solid fa-chair"></i>
          </div>
          <h4 class="floor-empty-title">No Tables Configured</h4>
          <p class="floor-empty-desc">No floor tables have been set up for this restaurant yet. Please navigate to Table Master to add dining tables.</p>
        </div>
      @endforelse

      <!-- Empty State for 0 Search Results -->
      <div id="noResultsBox" class="floor-empty-state" style="display: none;">
        <div class="floor-empty-icon">
          <i class="fa-solid fa-magnifying-glass"></i>
        </div>
        <h4 class="floor-empty-title">No Matching Tables Found</h4>
        <p class="floor-empty-desc">No dining tables match your search query or selected filter. Try clearing your search input.</p>
      </div>
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

  // Real-time table filtering & search per category
  function applyFloorFilters() {
    var searchQuery = $('#tableSearchInput').val().toLowerCase().trim();
    var overallVisible = 0;

    $('.category-floor-section').each(function() {
      var section = $(this);
      var categoryVisible = 0;

      section.find('.filter-item').each(function() {
        var itemType = $(this).data('type');
        var itemName = $(this).data('name') || '';

        var matchesFilter = (activeFilter === 'all') || (itemType === activeFilter);
        var matchesSearch = (searchQuery === '') || (itemName.indexOf(searchQuery) !== -1);

        if (matchesFilter && matchesSearch) {
          $(this).show();
          categoryVisible++;
          overallVisible++;
        } else {
          $(this).hide();
        }
      });

      // Show/Hide category group depending on whether it has matching visible tables
      if (categoryVisible > 0) {
        section.show();
      } else {
        section.hide();
      }
    });

    if (overallVisible === 0) {
      $('#noResultsBox').show();
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
              <div class="bill-action-row">
                <a href="${bill.edit_url}" class="btn-edit-bill">
                  <i class="fa-solid fa-pen-to-square"></i> Manage Bill
                </a>
                <a href="${bill.receipt_url}" target="_blank" class="btn-print-bill" title="Print Thermal Slip">
                  <i class="fa-solid fa-print"></i>
                </a>
              </div>
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
    
    var modalEl = document.getElementById('activeBillsModal');
    if (window.bootstrap && bootstrap.Modal) {
      bootstrap.Modal.getOrCreateInstance(modalEl).show();
    } else if ($.fn.modal) {
      $('#activeBillsModal').modal('show');
    }
  });

});
</script>

</body>
</html>