<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bar Item &amp; Variation Management • Bill&amp;Bite POS</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">

  @include('includes.style')

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
  
  <!-- Font Awesome 6 CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    :root {
      --bar-primary: #8b5cf6;
      --bar-primary-hover: #7c3aed;
      --bar-gradient: linear-gradient(135deg, #7c3aed 0%, #a855f7 50%, #ec4899 100%);
      --bar-amber: #f59e0b;
      --bar-dark: #0f172a;
      --card-bg: #ffffff;
      --border-color: #e2e8f0;
      --text-main: #1e293b;
      --text-muted: #64748b;
      --radius-sm: 8px;
      --radius-md: 14px;
      --radius-lg: 18px;
    }

    * { box-sizing: border-box; }

    body {
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
      background-color: #f6f7fb;
      color: var(--text-main);
      overflow-x: hidden;
    }

    .pc-content {
      padding: 24px 28px;
    }

    @media (max-width: 768px) {
      .pc-content { padding: 16px 14px; }
    }

    /* Header Deck */
    .bar-header-card {
      background: #ffffff;
      border-radius: var(--radius-lg);
      padding: 22px 26px;
      margin-bottom: 22px;
      box-shadow: 0 4px 20px rgba(124, 58, 237, 0.05);
      border: 1px solid #ede9fe;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      flex-wrap: wrap;
    }

    .header-left-cluster {
      display: flex;
      align-items: center;
      gap: 16px;
      flex: 1 1 auto;
      min-width: 240px;
    }

    .bar-header-icon {
      width: 54px;
      height: 54px;
      border-radius: 14px;
      background: var(--bar-gradient);
      color: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.4rem;
      box-shadow: 0 8px 22px rgba(124, 58, 237, 0.35);
      flex-shrink: 0;
    }

    .bar-main-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.45rem;
      font-weight: 800;
      color: var(--bar-dark);
      margin: 0 0 2px 0;
      letter-spacing: -0.02em;
      line-height: 1.25;
    }

    /* Category Filter Navigation Bar */
    .category-filter-bar {
      display: flex;
      align-items: center;
      gap: 8px;
      overflow-x: auto;
      padding-bottom: 6px;
      margin-bottom: 20px;
      scrollbar-width: thin;
    }

    .cat-filter-btn {
      white-space: nowrap;
      border: 1.5px solid #e2e8f0;
      background: #ffffff;
      color: #475569;
      padding: 7px 16px;
      border-radius: 30px;
      font-size: 0.82rem;
      font-weight: 700;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.2s ease;
      text-decoration: none;
      flex-shrink: 0;
    }

    .cat-filter-btn:hover {
      background: #faf5ff;
      border-color: #ddd6fe;
      color: var(--bar-primary);
    }

    .cat-filter-btn.active {
      background: var(--bar-gradient);
      border-color: transparent;
      color: #ffffff;
      box-shadow: 0 4px 14px rgba(124, 58, 237, 0.32);
    }

    /* KPI Cards Deck */
    .kpi-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
      gap: 16px;
      margin-bottom: 24px;
    }

    .kpi-card {
      background: #ffffff;
      border-radius: var(--radius-md);
      padding: 18px 20px;
      border: 1px solid #f1f5f9;
      box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
      display: flex;
      align-items: center;
      gap: 16px;
      transition: all 0.2s ease;
    }

    .kpi-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 18px rgba(124, 58, 237, 0.08);
      border-color: #ddd6fe;
    }

    .kpi-icon {
      width: 46px;
      height: 46px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.25rem;
      flex-shrink: 0;
    }

    .kpi-icon.purple { background: #faf5ff; color: #7c3aed; }
    .kpi-icon.amber  { background: #fffbeb; color: #f59e0b; }
    .kpi-icon.blue   { background: #eff6ff; color: #3b82f6; }
    .kpi-icon.green  { background: #ecfdf5; color: #10b981; }

    .kpi-val {
      font-family: 'Outfit', sans-serif;
      font-size: 1.5rem;
      font-weight: 800;
      color: var(--bar-dark);
      line-height: 1.1;
    }

    .kpi-label {
      font-size: 0.76rem;
      font-weight: 700;
      color: #64748b;
      margin-top: 3px;
      text-transform: uppercase;
      letter-spacing: 0.04em;
    }

    /* Action Buttons */
    .btn-bar-primary {
      background: var(--bar-gradient);
      border: none;
      color: #ffffff;
      padding: 9px 20px;
      border-radius: 30px;
      font-size: 0.88rem;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      box-shadow: 0 4px 16px rgba(124, 58, 237, 0.32);
      transition: all 0.25s ease;
      cursor: pointer;
      text-decoration: none;
    }

    .btn-bar-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(124, 58, 237, 0.45);
      color: #ffffff;
    }

    .btn-bar-secondary {
      background: #ffffff;
      border: 1.5px solid #e2e8f0;
      color: #475569;
      padding: 9px 18px;
      border-radius: 30px;
      font-size: 0.86rem;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: all 0.2s ease;
      cursor: pointer;
      text-decoration: none;
    }

    /* Availability Switch & Status Indicators */
    .status-dot-pulse {
      display: inline-block;
      width: 10px;
      height: 10px;
      border-radius: 50%;
      flex-shrink: 0;
    }
    .pulse-green {
      background-color: #10b981;
      box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
      animation: pulseGreen 2s infinite;
    }
    .pulse-red {
      background-color: #ef4444;
      box-shadow: none;
    }
    @keyframes pulseGreen {
      0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
      70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
      100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
    .form-check-input.bar-avail-toggle {
      width: 2.8em !important;
      height: 1.5em !important;
      cursor: pointer;
      border-color: #cbd5e1;
      transition: background-color 0.2s ease, border-color 0.2s ease;
    }
    .form-check-input.bar-avail-toggle:checked {
      background-color: #10b981 !important;
      border-color: #10b981 !important;
    }
    .bar-item-card.is-unavailable {
      opacity: 0.82;
      background: #fafaf9;
      border-color: #f1f5f9;
    }
    .bar-item-card.is-unavailable .item-top-media img {
      filter: grayscale(45%);
    }
    .status-filter-chip {
      border: 1.5px solid #e2e8f0;
      background: #ffffff;
      color: #64748b;
      font-size: 0.8rem;
      font-weight: 700;
      padding: 6px 14px;
      border-radius: 20px;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.2s ease;
      text-decoration: none;
    }
    .status-filter-chip:hover {
      background: #f8fafc;
      color: #1e293b;
    }
    .status-filter-chip.active {
      background: #0f172a;
      color: #ffffff;
      border-color: #0f172a;
    }
    .status-filter-chip.active-avail {
      background: #10b981;
      color: #ffffff;
      border-color: #10b981;
    }
    .status-filter-chip.active-unavail {
      background: #ef4444;
      color: #ffffff;
      border-color: #ef4444;
    }

    /* Bar Item Cards Grid */
    .bar-items-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 22px;
    }

    .bar-item-card {
      background: #ffffff;
      border-radius: var(--radius-lg);
      border: 1px solid #f1f5f9;
      box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      transition: all 0.25s ease;
    }

    .bar-item-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 28px rgba(124, 58, 237, 0.12);
      border-color: #ddd6fe;
    }

    .item-top-media {
      position: relative;
      height: 140px;
      background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
      overflow: hidden;
    }

    .item-top-media img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .bar-item-card:hover .item-top-media img {
      transform: scale(1.05);
    }

    .item-initial-avatar {
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #2e1065 0%, #4c1d95 60%, #5b21b6 100%);
      color: #ffffff;
      font-size: 2rem;
      font-weight: 900;
      font-family: 'Outfit', sans-serif;
    }

    .item-category-tag {
      position: absolute;
      top: 12px;
      left: 12px;
      background: rgba(15, 23, 42, 0.7);
      backdrop-filter: blur(6px);
      color: #ffffff;
      font-size: 0.68rem;
      font-weight: 700;
      padding: 3px 8px;
      border-radius: 6px;
      border: 1px solid rgba(255, 255, 255, 0.15);
      z-index: 2;
    }

    .item-brand-tag {
      position: absolute;
      top: 12px;
      right: 12px;
      background: rgba(124, 58, 237, 0.85);
      backdrop-filter: blur(6px);
      color: #ffffff;
      font-size: 0.72rem;
      font-weight: 800;
      padding: 4px 10px;
      border-radius: 20px;
      border: 1px solid rgba(255, 255, 255, 0.25);
      z-index: 2;
    }

    .item-card-body {
      padding: 18px 20px;
      display: flex;
      flex-direction: column;
      flex: 1;
      justify-content: space-between;
      gap: 14px;
    }

    .item-name-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.15rem;
      font-weight: 800;
      color: var(--bar-dark);
      margin: 0 0 4px 0;
      line-height: 1.3;
    }

    /* Inventory Spec Box */
    .inventory-spec-box {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      padding: 10px 14px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 8px;
      margin-bottom: 12px;
    }

    .inv-spec-col {
      display: flex;
      flex-direction: column;
    }

    .inv-spec-label {
      font-size: 0.68rem;
      font-weight: 700;
      color: #64748b;
      text-transform: uppercase;
      letter-spacing: 0.04em;
    }

    .inv-spec-val {
      font-family: 'JetBrains Mono', monospace;
      font-size: 0.95rem;
      font-weight: 800;
      color: #0f172a;
    }

    .inv-spec-val.highlight {
      color: #7c3aed;
    }

    /* Variations Chip Strip */
    .variations-list-box {
      display: flex;
      flex-wrap: wrap;
      gap: 5px;
      min-height: 30px;
      margin-bottom: 8px;
    }

    .var-chip {
      background: #faf5ff;
      border: 1px solid #e9d5ff;
      color: #6d28d9;
      font-size: 0.72rem;
      font-weight: 700;
      padding: 3px 8px;
      border-radius: 6px;
      display: inline-flex;
      align-items: center;
      gap: 4px;
    }

    .btn-card-add-var {
      background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
      color: #ffffff;
      border: none;
      border-radius: 20px;
      padding: 8px 14px;
      font-size: 0.82rem;
      font-weight: 800;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      cursor: pointer;
      transition: all 0.2s ease;
      flex: 1;
    }

    .btn-card-add-var:hover {
      box-shadow: 0 4px 14px rgba(124, 58, 237, 0.35);
      color: #ffffff;
      transform: translateY(-1px);
    }

    /* Form Controls */
    .form-control-custom {
      background: #f8fafc;
      border: 1.5px solid #e2e8f0;
      border-radius: 12px;
      padding: 10px 16px;
      font-size: 0.9rem;
      font-weight: 500;
      color: var(--text-main);
      width: 100%;
    }

    .form-control-custom:focus {
      background: #ffffff;
      border-color: var(--bar-primary);
      box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.15);
      outline: none;
    }

    .btn-modal-cancel {
      background: #f1f5f9;
      border: 1px solid #e2e8f0;
      color: #475569;
      border-radius: 30px;
      padding: 9px 20px;
      font-size: 0.86rem;
      font-weight: 700;
      cursor: pointer;
    }
  </style>
</head>

<body data-pc-theme="light">
  <div class="loader-bg">
    <div class="loader-track"><div class="loader-fill"></div></div>
  </div>

  @include('includes.sidebar')

  <div class="pc-container">
    <div class="pc-content">

      <!-- Header Deck -->
      <div class="bar-header-card">
        <div class="header-left-cluster">
          <div class="bar-header-icon">
            <i class="fa-solid fa-wine-bottle"></i>
          </div>
          <div>
            <div style="font-size: 0.72rem; font-weight: 800; text-transform: uppercase; color: var(--bar-primary); letter-spacing: 0.08em; margin-bottom: 2px;">
              <i class="fa-solid fa-martini-glass me-1"></i> Bar Management &bull; Liquor &amp; Beverage Catalog
            </div>
            <h1 class="bar-main-title">Bar Item &amp; Inventory Management</h1>
            <p style="font-size: 0.82rem; color: #64748b; margin: 0;">Configure bottle sizes (ml), opening inventory stock, and serving variations (pegs &amp; pours).</p>
          </div>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
          <a href="{{ route('bar.brand.index') }}" class="btn-bar-secondary">
            <i class="fa-solid fa-certificate"></i> Brand Master
          </a>
          <a href="{{ route('bar.category.index') }}" class="btn-bar-secondary">
            <i class="fa-solid fa-layer-group"></i> Bar Categories
          </a>
          <button type="button" class="btn-bar-primary" data-bs-toggle="modal" data-bs-target="#addBarItemModal">
            <i class="fa-solid fa-plus"></i> Add Bar Item
          </button>
        </div>
      </div>

      <!-- Category Filter Pills Bar -->
      <div class="category-filter-bar">
        <a href="{{ route('bar.item.index') }}" class="cat-filter-btn {{ empty($selectedCategory) ? 'active' : '' }}">
          <i class="fa-solid fa-border-all"></i> All Bar Categories ({{ $items->count() }})
        </a>
        @foreach($barCategories as $bCat)
          <a href="{{ route('bar.item.index', $bCat->id) }}" class="cat-filter-btn {{ (!empty($selectedCategory) && $selectedCategory->id == $bCat->id) ? 'active' : '' }}">
            <i class="fa-solid fa-martini-glass"></i> {{ $bCat->name }}
          </a>
        @endforeach
      </div>

      <!-- KPI Summary Cards -->
      <div class="kpi-row" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
        <div class="kpi-card status-kpi-trigger" data-filter="all" style="cursor: pointer;" title="Show All Bar Items">
          <div class="kpi-icon purple"><i class="fa-solid fa-wine-bottle"></i></div>
          <div>
            <div class="kpi-val">{{ $stats['total_items'] }}</div>
            <div class="kpi-label">Total Bar Items</div>
          </div>
        </div>
        <div class="kpi-card status-kpi-trigger" data-filter="available" style="border-left: 3px solid #10b981; cursor: pointer;" title="Filter Available (Active) Items">
          <div class="kpi-icon green"><i class="fa-solid fa-circle-check"></i></div>
          <div>
            <div class="kpi-val text-success" id="kpiAvailCount">{{ $stats['available_items'] ?? $items->where('status', 'A')->count() }}</div>
            <div class="kpi-label">Available (Active)</div>
          </div>
        </div>
        <div class="kpi-card status-kpi-trigger" data-filter="unavailable" style="border-left: 3px solid #ef4444; cursor: pointer;" title="Filter Unavailable (Inactive) Items">
          <div class="kpi-icon red" style="background:#fef2f2; color:#ef4444;"><i class="fa-solid fa-circle-xmark"></i></div>
          <div>
            <div class="kpi-val text-danger" id="kpiUnavailCount">{{ $stats['unavailable_items'] ?? $items->where('status', '!=', 'A')->count() }}</div>
            <div class="kpi-label">Unavailable (Inactive)</div>
          </div>
        </div>
        <div class="kpi-card">
          <div class="kpi-icon amber"><i class="fa-solid fa-flask"></i></div>
          <div>
            <div class="kpi-val">{{ number_format($stats['total_stock_ml']) }} ml</div>
            <div class="kpi-label">Total Stock in ml</div>
          </div>
        </div>
        <div class="kpi-card">
          <div class="kpi-icon blue"><i class="fa-solid fa-sliders"></i></div>
          <div>
            <div class="kpi-val">{{ $stats['items_with_variations'] }}</div>
            <div class="kpi-label">Items with Variations</div>
          </div>
        </div>
      </div>

      <!-- Quick Availability & Search Toolbar -->
      <div class="d-flex align-items-center justify-content-between gap-2 mb-3 flex-wrap">
        <div class="d-flex align-items-center gap-2 flex-wrap">
          <span class="small fw-bold text-muted me-1"><i class="fa-solid fa-filter me-1"></i> Quick Filter:</span>
          <button type="button" class="status-filter-chip active" data-filter="all">
            All Items (<span id="filterAllCount">{{ $items->count() }}</span>)
          </button>
          <button type="button" class="status-filter-chip" data-filter="available">
            <span class="status-dot-pulse pulse-green me-1" style="width:7px; height:7px;"></span> Active / Available (<span id="filterAvailCount">{{ $items->where('status', 'A')->count() }}</span>)
          </button>
          <button type="button" class="status-filter-chip" data-filter="unavailable">
            <span class="status-dot-pulse pulse-red me-1" style="width:7px; height:7px;"></span> Inactive / Unavailable (<span id="filterUnavailCount">{{ $items->where('status', '!=', 'A')->count() }}</span>)
          </button>
        </div>
        <div style="min-width: 220px; max-width: 320px;" class="flex-grow-1">
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-white border-end-0" style="border-radius: 20px 0 0 20px; border-color: #cbd5e1;">
              <i class="fa-solid fa-magnifying-glass text-muted"></i>
            </span>
            <input type="text" id="barItemSearchInput" class="form-control border-start-0" placeholder="Search liquor name or brand..." style="border-radius: 0 20px 20px 0; border-color: #cbd5e1; font-size: 0.82rem;">
          </div>
        </div>
      </div>

      <!-- Flash Messages -->
      @include('includes.message')

      <!-- Items Grid -->
      <div class="bar-items-grid">
        @forelse($items as $item)
          @php
            $bottleSize = floatval($item->bottle_size_ml ?? 750);
            $openingQty = floatval($item->opening_qty ?? 0);
            $currentStock = floatval($item->current_stock_ml ?? ($bottleSize * $openingQty));
            $brandName = $item->brand ? $item->brand->name : 'General Brand';
            $categoryName = $item->category ? $item->category->name : 'Bar Category';
            $isAvail = ($item->status === 'A');
            $normalizedStatus = $isAvail ? 'A' : 'I';
          @endphp
          <div class="bar-item-card {{ $isAvail ? '' : 'is-unavailable' }}" 
               id="barItemCard-{{ $item->id }}"
               data-item-id="{{ $item->id }}"
               data-status="{{ $normalizedStatus }}"
               data-category-id="{{ $item->category_id }}"
               data-name="{{ strtolower($item->name . ' ' . $brandName . ' ' . $categoryName) }}">
            
            <div class="item-top-media">
              <span class="item-category-tag"><i class="fa-solid fa-tag me-1"></i> {{ $categoryName }}</span>
              <span class="item-brand-tag"><i class="fa-solid fa-certificate me-1"></i> {{ $brandName }}</span>
              
              <!-- Floating Availability Pill Badge on Media -->
              <span class="badge {{ $isAvail ? 'bg-success' : 'bg-danger' }} status-pill-badge" 
                    id="badgeStatus-{{ $item->id }}" 
                    style="position: absolute; bottom: 10px; left: 12px; font-size: 0.72rem; font-weight: 800; padding: 4px 10px; border-radius: 20px; backdrop-filter: blur(4px); box-shadow: 0 2px 6px rgba(0,0,0,0.25); border: 1px solid rgba(255,255,255,0.2);">
                <i class="fa-solid {{ $isAvail ? 'fa-circle-check' : 'fa-circle-xmark' }} me-1" id="badgeIcon-{{ $item->id }}"></i>
                <span id="badgeText-{{ $item->id }}">{{ $isAvail ? 'Available' : 'Unavailable' }}</span>
              </span>

              <span style="position: absolute; bottom: 10px; right: 12px; background: rgba(15, 23, 42, 0.78); color: #ede9fe; font-size: 0.72rem; font-weight: 700; padding: 3px 8px; border-radius: 6px; backdrop-filter: blur(4px); border: 1px solid rgba(255,255,255,0.15);">
                <i class="fa-solid fa-percent me-1 text-warning"></i> {{ floatval($item->vat_rate ?? 0) }}% State VAT
              </span>

              @if($item->image)
                <img src="{{ URL::to('storage/category/' . $item->image) }}" alt="{{ $item->name }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="item-initial-avatar" style="display: none;">
                  {{ strtoupper(substr($item->name, 0, 1)) }}
                </div>
              @else
                <div class="item-initial-avatar">
                  {{ strtoupper(substr($item->name, 0, 1)) }}
                </div>
              @endif
            </div>

            <div class="item-card-body">
              <div>
                <!-- Availability On/Off Switch Control Panel -->
                <div class="avail-switch-panel d-flex align-items-center justify-content-between p-2 mb-3 rounded-3"
                     id="availPanel-{{ $item->id }}"
                     style="background: {{ $isAvail ? '#f0fdf4' : '#fef2f2' }}; border: 1px solid {{ $isAvail ? '#bbf7d0' : '#fecaca' }}; transition: all 0.3s ease;">
                  <div class="d-flex align-items-center gap-2">
                    <span class="status-dot-pulse {{ $isAvail ? 'pulse-green' : 'pulse-red' }}" id="pulseDot-{{ $item->id }}"></span>
                    <div>
                      <div class="fw-bold" style="font-size: 0.8rem; color: {{ $isAvail ? '#15803d' : '#b91c1c' }}; line-height: 1.2;" id="availHeading-{{ $item->id }}">
                        {{ $isAvail ? 'Available (Active)' : 'Unavailable (Off)' }}
                      </div>
                      <small class="text-muted" style="font-size: 0.68rem; display: block;" id="availSubtext-{{ $item->id }}">
                        {{ $isAvail ? 'Visible in Rapid Bill, POS & QR' : 'Hidden from Rapid Bill, POS & QR' }}
                      </small>
                    </div>
                  </div>
                  <div class="form-check form-switch m-0" style="padding-left: 2.8em;" title="Switch availability on/off for order pages">
                    <input class="form-check-input bar-avail-toggle" 
                           type="checkbox" 
                           role="switch" 
                           id="switch-{{ $item->id }}"
                           data-item-id="{{ $item->id }}"
                           data-item-name="{{ $item->name }}"
                           {{ $isAvail ? 'checked' : '' }}>
                  </div>
                </div>

                <h3 class="item-name-title" title="{{ $item->name }}">{{ $item->name }}</h3>
                <p style="font-size: 0.8rem; color: #64748b; margin-bottom: 10px; min-height: 1.3em;">
                  {{ $item->description ? Str::limit($item->description, 55) : 'No description provided' }}
                </p>

                <!-- Inventory Metrics Box -->
                <div class="inventory-spec-box">
                  <div class="inv-spec-col">
                    <span class="inv-spec-label">Bottle Size</span>
                    <span class="inv-spec-val">{{ number_format($bottleSize) }} ml</span>
                  </div>
                  <div class="inv-spec-col">
                    <span class="inv-spec-label">Opening ML</span>
                    <span class="inv-spec-val">{{ number_format($openingQty) }} ml</span>
                  </div>
                  <div class="inv-spec-col" style="grid-column: span 2; border-top: 1px dashed #e2e8f0; padding-top: 6px;">
                    <span class="inv-spec-label">Total Inventory Volume</span>
                    <div class="d-flex align-items-center justify-content-between">
                      <span class="inv-spec-val highlight">{{ number_format($currentStock) }} ml</span>
                      <small class="text-muted fw-bold">({{ $bottleSize > 0 ? number_format($currentStock / $bottleSize, 1) : 0 }} Bottles)</small>
                    </div>
                  </div>
                </div>

                <!-- Variations List -->
                <div style="font-size: 0.72rem; font-weight: 800; color: #475569; text-transform: uppercase; margin-bottom: 4px;">
                  Serving Variations ({{ $item->variations->count() }}):
                </div>
                <div class="variations-list-box">
                  @forelse($item->variations as $var)
                    <span class="var-chip" title="{{ $var->name }}: {{ $var->ml }}ml at ₹{{ number_format($var->price, 2) }}">
                      <i class="fa-solid fa-martini-glass"></i> {{ $var->name }}: ₹{{ number_format($var->price, 0) }}
                    </span>
                  @empty
                    <span class="text-muted small" style="font-size: 0.76rem; font-style: italic;">No variations added yet. Click below to add pegs/pours.</span>
                  @endforelse
                </div>
              </div>

              <!-- Card Actions -->
              <div class="d-flex align-items-center gap-2 pt-2 border-top">
                <a href="{{ route('bar.item.variations', $item->id) }}" class="btn-card-add-var">
                  <i class="fa-solid fa-sliders"></i> Variations ({{ $item->variations->count() }})
                </a>
                <button type="button" 
                        class="btn btn-sm btn-outline-secondary editBarItemBtn" 
                        data-id="{{ $item->id }}"
                        data-name="{{ $item->name }}"
                        data-category-id="{{ $item->category_id }}"
                        data-brand-id="{{ $item->brand_id }}"
                        data-bottle-size="{{ $bottleSize }}"
                        data-opening-qty="{{ $openingQty }}"
                        data-price="{{ $item->price }}"
                        data-vat="{{ floatval($item->vat_rate ?? 0) }}"
                        data-description="{{ $item->description }}"
                        data-status="{{ $normalizedStatus }}"
                        style="border-radius: 8px; padding: 7px 11px;" title="Edit Item">
                  <i class="fa-solid fa-pen-to-square"></i>
                </button>
                <a href="{{ route('bar.item.delete', $item->id) }}" 
                   class="btn btn-sm btn-outline-danger" 
                   onclick="return confirm('Are you sure you want to delete \'{{ addslashes($item->name) }}\'?')"
                   style="border-radius: 8px; padding: 7px 11px;" title="Delete Item">
                  <i class="fa-solid fa-trash"></i>
                </a>
              </div>

            </div>

          </div>
        @empty
          <div style="grid-column: 1 / -1; background: #ffffff; border-radius: var(--radius-lg); padding: 48px 24px; text-align: center; border: 1.5px dashed #cbd5e1;">
            <i class="fa-solid fa-wine-bottle fs-1 text-secondary opacity-50 mb-3 d-block"></i>
            <h5 class="fw-bold">No Bar Items Found</h5>
            <p class="text-muted small mb-3">Add your first liquor product with bottle size and opening stock.</p>
            <button type="button" class="btn-bar-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addBarItemModal">
              <i class="fa-solid fa-plus"></i> Add Bar Item Now
            </button>
          </div>
        @endforelse

        @if($items->count() > 0)
          <div id="noFilteredBarItems" style="display: none; grid-column: 1 / -1; background: #ffffff; border-radius: var(--radius-lg); padding: 40px 24px; text-align: center; border: 1.5px dashed #cbd5e1;">
            <i class="fa-solid fa-filter-circle-xmark fs-1 text-secondary opacity-50 mb-3 d-block"></i>
            <h5 class="fw-bold mb-1">No Matching Bar Items</h5>
            <p class="text-muted small mb-3" id="noFilteredBarItemsDesc">No bar items match your current availability filter or search query.</p>
            <button type="button" class="btn-bar-secondary btn-sm" id="resetBarFilterBtn">
              <i class="fa-solid fa-rotate-left"></i> Show All Items
            </button>
          </div>
        @endif
      </div>

    </div>
  </div>


  <!-- ===================================================
       MODAL: ADD BAR ITEM
       =================================================== -->
  <div class="modal fade" id="addBarItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <form method="POST" action="{{ route('bar.item.store') }}" enctype="multipart/form-data" style="width: 100%;">
        @csrf
        <div class="modal-content" style="border-radius: var(--radius-lg); overflow: hidden; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
          
          <div class="modal-header" style="background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 18px 22px;">
            <h5 class="modal-title font-weight-bold" style="font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 1.15rem;">
              <i class="fa-solid fa-plus-circle text-primary me-2"></i> Add New Bar Product
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body" style="padding: 20px 22px;">
            <div class="row g-3">
              
              <div class="col-md-7">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">
                  Product Name <span class="text-danger">*</span>
                </label>
                <input type="text" name="name" class="form-control-custom" required placeholder="e.g. Johnnie Walker Black Label 12 Yrs" autocomplete="off">
              </div>

              <div class="col-md-5">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">
                  Bar Category <span class="text-danger">*</span>
                </label>
                <select name="category_id" class="form-control-custom" required>
                  <option value="">Select Category</option>
                  @foreach($barCategories as $bCat)
                    <option value="{{ $bCat->id }}" {{ (!empty($selectedCategory) && $selectedCategory->id == $bCat->id) ? 'selected' : '' }}>
                      {{ $bCat->name }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">
                  Brand <small class="text-muted fw-normal">(Optional)</small>
                </label>
                <select name="brand_id" class="form-control-custom">
                  <option value="">Select Brand</option>
                  @foreach($brands as $brand)
                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">
                  Base Bottle Price (₹) <small class="text-muted fw-normal">(Optional)</small>
                </label>
                <input type="number" step="0.01" name="price" class="form-control-custom" placeholder="e.g. 4500.00">
              </div>

              <div class="col-md-6">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">
                  State VAT Rate (%) <small class="text-muted fw-normal">(GST-Exempt in India)</small>
                </label>
                <div class="input-group">
                  <input type="number" step="0.01" min="0" max="100" name="vat_rate" id="addVatRate" class="form-control-custom" value="{{ floatval($defaultVatRate ?: 20) }}" placeholder="e.g. 20">
                  <span class="input-group-text" style="border: 1.5px solid #cbd5e1; border-left: none; background: #f8fafc; font-weight: 700; font-size: 0.85rem; border-radius: 0 8px 8px 0;">%</span>
                </div>
                <div class="d-flex gap-1 mt-1 flex-wrap">
                  <span class="small text-muted" style="font-size: 0.7rem;">Presets:</span>
                  <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 setVatPresetBtn" data-vat="0" style="font-size: 0.7rem; border-radius: 4px;">0% (Exempt)</button>
                  <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 setVatPresetBtn" data-vat="10" style="font-size: 0.7rem; border-radius: 4px;">10%</button>
                  <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 setVatPresetBtn" data-vat="14.5" style="font-size: 0.7rem; border-radius: 4px;">14.5%</button>
                  <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2 setVatPresetBtn" data-vat="20" style="font-size: 0.7rem; border-radius: 4px; font-weight: 700;">20% (Std)</button>
                  <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 setVatPresetBtn" data-vat="25" style="font-size: 0.7rem; border-radius: 4px;">25%</button>
                </div>
              </div>

              <div class="col-md-6">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">
                  Bottle Size in ml <span class="text-danger">*</span>
                </label>
                <input type="number" step="any" name="bottle_size_ml" id="addBottleSize" class="form-control-custom" required value="750" placeholder="e.g. 750, 1000, 330, 650">
              </div>

              <div class="col-md-6">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">
                  Opening ML <span class="text-danger">*</span>
                </label>
                <input type="number" step="any" name="opening_qty" id="addOpeningQty" class="form-control-custom" required value="750" placeholder="e.g. 750, 1500">
              </div>

              <div class="col-md-6">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">
                  Availability Status <span class="text-danger">*</span>
                </label>
                <select name="status" class="form-control-custom" required>
                  <option value="A" selected>Available (Active - Visible in POS &amp; QR)</option>
                  <option value="I">Unavailable (Inactive - Hidden from POS &amp; QR)</option>
                </select>
              </div>

              <!-- Live Inventory Calculation Preview Banner -->
              <div class="col-12">
                <div class="p-3 rounded-3" style="background: #faf5ff; border: 1.5px dashed #d8b4fe;">
                  <div class="d-flex align-items-center justify-content-between">
                    <div>
                      <div style="font-size: 0.74rem; font-weight: 800; text-transform: uppercase; color: #7c3aed;">
                        <i class="fa-solid fa-calculator me-1"></i> Stock &amp; Bottle Equivalent
                      </div>
                      <div class="fw-bold" style="font-size: 0.95rem; color: #4c1d95;" id="addInvCalcText">
                        750 ml = 1.0 Bottle (750 ml each)
                      </div>
                    </div>
                    <span class="badge" style="background: #7c3aed; color: #fff; font-size: 0.85rem; padding: 6px 12px; border-radius: 8px;" id="addInvCalcBadge">
                      750 ml
                    </span>
                  </div>
                </div>
              </div>

              <div class="col-12">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">
                  Description <small class="text-muted fw-normal">(Optional)</small>
                </label>
                <textarea name="description" class="form-control-custom" rows="2" placeholder="Taste notes, distillery info, aging..."></textarea>
              </div>

              <div class="col-12">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">
                  Cover Image <small class="text-muted fw-normal">(Optional)</small>
                </label>
                <input type="file" name="image" class="form-control-custom" accept="image/*">
              </div>

            </div>
          </div>

          <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 14px 22px;">
            <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn-bar-primary">Save Bar Product</button>
          </div>

        </div>
      </form>
    </div>
  </div>


  <!-- ===================================================
       MODAL: EDIT BAR ITEM
       =================================================== -->
  <div class="modal fade" id="editBarItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <form method="POST" action="{{ route('bar.item.update') }}" enctype="multipart/form-data" style="width: 100%;">
        @csrf
        <input type="hidden" name="id" id="editItemId">
        <div class="modal-content" style="border-radius: var(--radius-lg); overflow: hidden; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
          
          <div class="modal-header" style="background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 18px 22px;">
            <h5 class="modal-title font-weight-bold" style="font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 1.15rem;">
              <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Bar Product
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body" style="padding: 20px 22px;">
            <div class="row g-3">
              <div class="col-md-7">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">Product Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="editItemName" class="form-control-custom" required autocomplete="off">
              </div>

              <div class="col-md-5">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">Bar Category <span class="text-danger">*</span></label>
                <select name="category_id" id="editItemCategory" class="form-control-custom" required>
                  @foreach($barCategories as $bCat)
                    <option value="{{ $bCat->id }}">{{ $bCat->name }}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">Brand</label>
                <select name="brand_id" id="editItemBrand" class="form-control-custom">
                  <option value="">Select Brand</option>
                  @foreach($brands as $brand)
                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">Base Price (₹)</label>
                <input type="number" step="0.01" name="price" id="editItemPrice" class="form-control-custom">
              </div>

              <div class="col-md-6">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">
                  State VAT Rate (%) <small class="text-muted fw-normal">(GST-Exempt in India)</small>
                </label>
                <div class="input-group">
                  <input type="number" step="0.01" min="0" max="100" name="vat_rate" id="editItemVat" class="form-control-custom" placeholder="e.g. 20">
                  <span class="input-group-text" style="border: 1.5px solid #cbd5e1; border-left: none; background: #f8fafc; font-weight: 700; font-size: 0.85rem; border-radius: 0 8px 8px 0;">%</span>
                </div>
                <div class="d-flex gap-1 mt-1 flex-wrap">
                  <span class="small text-muted" style="font-size: 0.7rem;">Presets:</span>
                  <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 setEditVatPresetBtn" data-vat="0" style="font-size: 0.7rem; border-radius: 4px;">0%</button>
                  <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 setEditVatPresetBtn" data-vat="10" style="font-size: 0.7rem; border-radius: 4px;">10%</button>
                  <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 setEditVatPresetBtn" data-vat="14.5" style="font-size: 0.7rem; border-radius: 4px;">14.5%</button>
                  <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2 setEditVatPresetBtn" data-vat="20" style="font-size: 0.7rem; border-radius: 4px; font-weight: 700;">20%</button>
                  <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-2 setEditVatPresetBtn" data-vat="25" style="font-size: 0.7rem; border-radius: 4px;">25%</button>
                </div>
              </div>

              <div class="col-md-6">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">Bottle Size (ml) <span class="text-danger">*</span></label>
                <input type="number" step="any" name="bottle_size_ml" id="editBottleSize" class="form-control-custom" required>
              </div>

              <div class="col-md-6">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">Opening ML <span class="text-danger">*</span></label>
                <input type="number" step="any" name="opening_qty" id="editOpeningQty" class="form-control-custom" required placeholder="e.g. 750, 1500">
              </div>

              <div class="col-md-6">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">Availability Status <span class="text-danger">*</span></label>
                <select name="status" id="editItemStatus" class="form-control-custom" required>
                  <option value="A">Available (Visible in POS &amp; QR)</option>
                  <option value="I">Unavailable (Hidden from POS &amp; QR)</option>
                </select>
              </div>

              <div class="col-12">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">Description</label>
                <textarea name="description" id="editItemDesc" class="form-control-custom" rows="2"></textarea>
              </div>

              <div class="col-12">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">Change Image <small class="text-muted fw-normal">(Optional)</small></label>
                <input type="file" name="image" class="form-control-custom" accept="image/*">
              </div>
            </div>
          </div>

          <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 14px 22px;">
            <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn-bar-primary">Update Bar Product</button>
          </div>

        </div>
      </form>
    </div>
  </div>


  <!-- ===================================================
       MODAL: MANAGE ITEM VARIATIONS (Pegs / Pours / Bottles)
       =================================================== -->
  <div class="modal fade" id="variationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content" style="border-radius: var(--radius-lg); overflow: hidden; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.18);">
        
        <div class="modal-header" style="background: #0f172a; color: #ffffff; padding: 18px 24px;">
          <div>
            <h5 class="modal-title font-weight-bold text-white" style="font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 1.2rem;">
              <i class="fa-solid fa-sliders text-warning me-2"></i> <span id="varModalItemTitle">Bar Item Variations</span>
            </h5>
            <div style="font-size: 0.78rem; color: #94a3b8;" id="varModalItemMeta">
              Bottle Size: -- ml &bull; In Stock: -- ml
            </div>
          </div>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body" style="padding: 22px 24px; background: #ffffff;">
          
          <!-- Inline Notification Box -->
          <div id="varModalAlert" style="display: none; border-radius: 8px; font-weight: 600;" class="alert py-2 px-3 small mb-3"></div>

          <!-- Add Variation Sub-form -->
          <div class="p-3 mb-4 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
            <div style="font-size: 0.78rem; font-weight: 800; text-transform: uppercase; color: var(--bar-primary); margin-bottom: 8px;">
              <i class="fa-solid fa-plus-circle me-1"></i> Add New Variation / Peg
            </div>

            <!-- Quick Presets -->
            <div class="d-flex align-items-center gap-1 flex-wrap mb-3">
              <span class="small text-muted fw-bold me-1" style="font-size: 0.72rem;">Presets:</span>
              <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 presetBtn" data-name="30ml Peg" data-ml="30" style="font-size: 0.72rem; border-radius: 6px;">+ 30ml Peg</button>
              <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 presetBtn" data-name="60ml Large" data-ml="60" style="font-size: 0.72rem; border-radius: 6px;">+ 60ml Large</button>
              <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 presetBtn" data-name="90ml Patiala" data-ml="90" style="font-size: 0.72rem; border-radius: 6px;">+ 90ml Patiala</button>
              <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 presetBtn" data-name="180ml Quarter" data-ml="180" style="font-size: 0.72rem; border-radius: 6px;">+ 180ml</button>
              <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 presetBtn" data-name="375ml Half" data-ml="375" style="font-size: 0.72rem; border-radius: 6px;">+ 375ml</button>
              <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 presetBtn" id="presetBottleBtn" data-name="Full Bottle" data-ml="750" style="font-size: 0.72rem; border-radius: 6px;">+ Bottle</button>
            </div>

            <form id="addVariationForm" class="row g-2 align-items-end">
              <input type="hidden" id="varFormItemId">
              <div class="col-md-5">
                <label class="form-label small fw-bold text-muted mb-1">Variation Name <span class="text-danger">*</span></label>
                <input type="text" id="varNameInput" class="form-control form-control-sm" required placeholder="e.g. 30ml Peg, 60ml Large, Bottle" style="border-radius: 8px;">
              </div>
              <div class="col-md-3">
                <label class="form-label small fw-bold text-muted mb-1">Volume (ml) <span class="text-danger">*</span></label>
                <input type="number" step="any" id="varMlInput" class="form-control form-control-sm" required placeholder="30" style="border-radius: 8px;">
              </div>
              <div class="col-md-2">
                <label class="form-label small fw-bold text-muted mb-1">Price (₹) <span class="text-danger">*</span></label>
                <input type="number" step="any" id="varPriceInput" class="form-control form-control-sm" required placeholder="250" style="border-radius: 8px;">
              </div>
              <div class="col-md-2">
                <button type="submit" class="btn btn-sm w-100" id="saveVarBtn" style="background: var(--bar-gradient); color: #fff; font-weight: 800; border-radius: 8px; padding: 6px;">
                  <i class="fa-solid fa-check"></i> Add
                </button>
              </div>
            </form>
          </div>

          <!-- Existing Variations Table -->
          <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: #1e293b; margin-bottom: 10px;">
            Configured Serving Formats:
          </div>
          <div class="table-responsive border rounded-3">
            <table class="table table-sm table-hover align-middle mb-0" id="variationsTable">
              <thead class="table-light" style="font-size: 0.74rem; text-transform: uppercase;">
                <tr>
                  <th class="ps-3">Variation Name</th>
                  <th>Serving Size (ml)</th>
                  <th>Depletion Ratio</th>
                  <th>Selling Price (₹)</th>
                  <th class="text-end pe-3">Action</th>
                </tr>
              </thead>
              <tbody id="variationsTableBody">
                <!-- Injected via AJAX -->
              </tbody>
            </table>
          </div>

        </div>

        <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 12px 24px;">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal" style="border-radius: 20px; font-weight: 700; padding: 6px 18px;">
            Close &amp; Refresh
          </button>
        </div>

      </div>
    </div>
  </div>


  <!-- Core Scripts -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  @include('includes.script')

  <script>
    $(document).ready(function() {
      
      // Auto Inventory Calculation in Add Modal
      function updateAddInventoryCalc() {
        const bottleSize = parseFloat($('#addBottleSize').val()) || 0;
        const openingMl = parseFloat($('#addOpeningQty').val()) || 0;
        const bottles = bottleSize > 0 ? (openingMl / bottleSize).toFixed(1) : 0;

        $('#addInvCalcText').text(`${openingMl.toLocaleString()} ml = ${bottles} ${bottles == 1 ? 'Bottle' : 'Bottles'} eq. (${bottleSize} ml per bottle)`);
        $('#addInvCalcBadge').text(`${openingMl.toLocaleString()} ml`);
      }

      $('#addBottleSize, #addOpeningQty').on('input', updateAddInventoryCalc);

      // Edit Item Populator
      $(document).on('click', '.editBarItemBtn', function() {
        const btn = $(this);
        const id = btn.attr('data-id');
        const name = btn.attr('data-name');
        const catId = btn.attr('data-category-id');
        const brandId = btn.attr('data-brand-id') || '';
        const size = btn.attr('data-bottle-size');
        const qty = btn.attr('data-opening-qty');
        const price = btn.attr('data-price');
        const vat = btn.attr('data-vat') !== undefined ? btn.attr('data-vat') : 20;
        const desc = btn.attr('data-description') || '';
        const rawStatus = btn.attr('data-status') || 'A';
        const status = (rawStatus === 'A') ? 'A' : 'I';

        $('#editItemId').val(id);
        $('#editItemName').val(name);
        $('#editItemCategory').val(catId);
        $('#editItemBrand').val(brandId);
        $('#editBottleSize').val(size);
        $('#editOpeningQty').val(qty);
        $('#editItemPrice').val(price);
        $('#editItemVat').val(vat);
        $('#editItemDesc').val(desc);
        $('#editItemStatus').val(status);

        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editBarItemModal'));
        modal.show();
      });

      // Bar Item Availability Switch Toggle Handler
      const toggleStatusBaseUrl = "{{ route('bar.item.toggle-status', ['id' => '__ITEM_ID__']) }}";

      $(document).on('change', '.bar-avail-toggle', function() {
        const toggleEl = $(this);
        const itemId = toggleEl.attr('data-item-id') || toggleEl.data('item-id');
        const itemName = toggleEl.attr('data-item-name') || toggleEl.data('item-name') || 'Item';
        const isChecked = toggleEl.is(':checked');
        const targetStatus = isChecked ? 'A' : 'I';

        // Optimistically disable during request
        toggleEl.prop('disabled', true);

        const url = toggleStatusBaseUrl.replace('__ITEM_ID__', itemId);

        $.ajax({
          url: url,
          method: 'POST',
          dataType: 'json',
          data: {
            _token: '{{ csrf_token() }}',
            status: targetStatus
          },
          success: function(res) {
            toggleEl.prop('disabled', false);
            if (res && res.success) {
              const isAvailable = (res.status === 'A' || res.is_available === true);
              updateItemCardAvailabilityUI(itemId, isAvailable);
              showFloatingToast(res.message || `${itemName} is now ${isAvailable ? 'Available' : 'Unavailable'}`, isAvailable ? 'success' : 'warning');
            } else {
              // Rollback
              toggleEl.prop('checked', !isChecked);
              showFloatingToast((res && res.message) ? res.message : 'Failed to update availability status', 'danger');
            }
          },
          error: function(xhr) {
            toggleEl.prop('disabled', false);
            // Rollback
            toggleEl.prop('checked', !isChecked);
            const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error changing availability status.';
            showFloatingToast(msg, 'danger');
          }
        });
      });

      function updateItemCardAvailabilityUI(itemId, isAvailable) {
        const card = $('#barItemCard-' + itemId);
        const panel = $('#availPanel-' + itemId);
        const heading = $('#availHeading-' + itemId);
        const subtext = $('#availSubtext-' + itemId);
        const dot = $('#pulseDot-' + itemId);
        const badge = $('#badgeStatus-' + itemId);
        const badgeIcon = $('#badgeIcon-' + itemId);
        const badgeText = $('#badgeText-' + itemId);
        const switchEl = $('#switch-' + itemId);
        const editBtn = card.find('.editBarItemBtn');

        const newStatus = isAvailable ? 'A' : 'I';
        card.attr('data-status', newStatus).data('status', newStatus);
        editBtn.attr('data-status', newStatus).data('status', newStatus);
        switchEl.prop('checked', isAvailable);

        if (isAvailable) {
          card.removeClass('is-unavailable');
          panel.css({
            'background': '#f0fdf4',
            'border-color': '#bbf7d0'
          });
          heading.css('color', '#15803d').text('Available (Active)');
          subtext.text('Visible in Rapid Bill, POS & QR');
          dot.removeClass('pulse-red').addClass('pulse-green');
          badge.removeClass('bg-danger').addClass('bg-success');
          badgeIcon.removeClass('fa-circle-xmark').addClass('fa-circle-check');
          badgeText.text('Available');
        } else {
          card.addClass('is-unavailable');
          panel.css({
            'background': '#fef2f2',
            'border-color': '#fecaca'
          });
          heading.css('color', '#b91c1c').text('Unavailable (Off)');
          subtext.text('Hidden from Rapid Bill, POS & QR');
          dot.removeClass('pulse-green').addClass('pulse-red');
          badge.removeClass('bg-success').addClass('bg-danger');
          badgeIcon.removeClass('fa-circle-check').addClass('fa-circle-xmark');
          badgeText.text('Unavailable');
        }

        recalcKpiCounters();
        applyQuickFilters();
      }

      function recalcKpiCounters() {
        let avail = 0;
        let unavail = 0;
        $('.bar-item-card').each(function() {
          const st = $(this).attr('data-status');
          if (st === 'A') avail++;
          else unavail++;
        });
        $('#kpiAvailCount').text(avail);
        $('#kpiUnavailCount').text(unavail);
        $('#filterAvailCount').text(avail);
        $('#filterUnavailCount').text(unavail);
        $('#filterAllCount').text(avail + unavail);
      }

      // Quick Filter & Search Handler
      let currentFilterStatus = 'all';

      function setFilterChipState(filterVal) {
        currentFilterStatus = filterVal || 'all';
        $('.status-filter-chip').removeClass('active active-avail active-unavail');
        const activeChip = $(`.status-filter-chip[data-filter="${currentFilterStatus}"]`);
        if (currentFilterStatus === 'available') {
          activeChip.addClass('active active-avail');
        } else if (currentFilterStatus === 'unavailable') {
          activeChip.addClass('active active-unavail');
        } else {
          activeChip.addClass('active');
        }
        applyQuickFilters();
      }

      $('.status-filter-chip').on('click', function() {
        setFilterChipState($(this).attr('data-filter') || $(this).data('filter'));
      });

      $('.status-kpi-trigger').on('click', function() {
        setFilterChipState($(this).attr('data-filter') || $(this).data('filter'));
      });

      $(document).on('click', '#resetBarFilterBtn', function() {
        $('#barItemSearchInput').val('');
        setFilterChipState('all');
      });

      $('#barItemSearchInput').on('input', function() {
        applyQuickFilters();
      });

      function applyQuickFilters() {
        const query = ($('#barItemSearchInput').val() || '').toLowerCase().trim();
        let visibleCount = 0;
        const totalCards = $('.bar-item-card').length;

        $('.bar-item-card').each(function() {
          const card = $(this);
          const status = card.attr('data-status');
          const name = (card.attr('data-name') || '').toLowerCase();

          let matchesStatus = true;
          if (currentFilterStatus === 'available') {
            matchesStatus = (status === 'A');
          } else if (currentFilterStatus === 'unavailable') {
            matchesStatus = (status !== 'A');
          }

          let matchesQuery = true;
          if (query.length > 0) {
            matchesQuery = name.indexOf(query) !== -1;
          }

          if (matchesStatus && matchesQuery) {
            card.css('display', 'flex');
            visibleCount++;
          } else {
            card.css('display', 'none');
          }
        });

        if (totalCards > 0) {
          if (visibleCount === 0) {
            let reason = 'No bar items match your current availability filter or search query.';
            if (currentFilterStatus === 'available' && !query) {
              reason = 'No Active (Available) bar items found. Toggle an item switch on to make it available.';
            } else if (currentFilterStatus === 'unavailable' && !query) {
              reason = 'No Inactive (Unavailable) bar items found. All your bar items are currently active!';
            }
            $('#noFilteredBarItemsDesc').text(reason);
            $('#noFilteredBarItems').show();
          } else {
            $('#noFilteredBarItems').hide();
          }
        }
      }

      function showFloatingToast(msg, type) {
        let toast = $('#floatingBarToast');
        if (!toast.length) {
          $('body').append(`
            <div id="floatingBarToast" style="position: fixed; top: 24px; right: 24px; z-index: 99999; min-width: 280px; max-width: 420px; box-shadow: 0 10px 30px rgba(0,0,0,0.18); border-radius: 12px; padding: 14px 18px; font-weight: 700; font-size: 0.88rem; display: none; transition: all 0.3s ease;">
              <div class="d-flex align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2" id="floatingToastContent"></div>
                <button type="button" class="btn-close btn-close-white" style="font-size: 0.7rem;" onclick="$('#floatingBarToast').fadeOut(200);"></button>
              </div>
            </div>
          `);
          toast = $('#floatingBarToast');
        }

        let bg = '#0f172a';
        let icon = '<i class="fa-solid fa-circle-info text-info"></i>';
        if (type === 'success') {
          bg = '#065f46';
          icon = '<i class="fa-solid fa-circle-check text-success"></i>';
        } else if (type === 'warning') {
          bg = '#92400e';
          icon = '<i class="fa-solid fa-triangle-exclamation text-warning"></i>';
        } else if (type === 'danger') {
          bg = '#991b1b';
          icon = '<i class="fa-solid fa-circle-exclamation text-danger"></i>';
        }

        toast.css({
          'background': bg,
          'color': '#ffffff'
        });
        $('#floatingToastContent').html(`${icon} <span>${msg}</span>`);
        toast.stop(true, true).fadeIn(250);
        clearTimeout(window.floatingToastTimer);
        window.floatingToastTimer = setTimeout(function() {
          toast.fadeOut(300);
        }, 4000);
      }

      // State VAT Presets Click Handlers
      $(document).on('click', '.setVatPresetBtn', function() {
        $('#addVatRate').val($(this).data('vat'));
      });
      $(document).on('click', '.setEditVatPresetBtn', function() {
        $('#editItemVat').val($(this).data('vat'));
      });

      // Manage Variations Modal
      let currentModalBottleSize = 750;
      let variationsChanged = false;
      const varGetUrl = "{{ url('admin/bar/items') }}";
      const varDeleteUrl = "{{ url('admin/bar/items/variations/delete') }}";
      const varStoreUrl = "{{ route('bar.item.variations.store') }}";

      $(document).on('click', '.openVariationModalBtn', function() {
        const itemId = $(this).data('item-id');
        const itemName = $(this).data('item-name');
        currentModalBottleSize = parseFloat($(this).data('bottle-size')) || 750;
        const currentStock = parseFloat($(this).data('current-stock')) || 0;

        variationsChanged = false;
        $('#varModalAlert').hide().removeClass('alert-success alert-danger alert-info').text('');
        $('#varFormItemId').val(itemId);
        $('#varModalItemTitle').text(itemName);
        $('#varModalItemMeta').html(`Bottle Size: <strong>${currentModalBottleSize} ml</strong> &bull; Total Stock: <strong class="text-warning">${currentStock.toLocaleString()} ml</strong>`);
        $('#presetBottleBtn').data('ml', currentModalBottleSize).attr('data-ml', currentModalBottleSize);

        loadVariations(itemId);

        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('variationModal'));
        modal.show();
      });

      // Preset click helper
      $('.presetBtn').on('click', function() {
        const name = $(this).data('name') || $(this).attr('data-name');
        const ml = $(this).attr('data-ml') || $(this).data('ml');
        $('#varNameInput').val(name);
        $('#varMlInput').val(ml);
        $('#varPriceInput').focus();
      });

      // Load Variations via AJAX
      function loadVariations(itemId) {
        const tbody = $('#variationsTableBody');
        tbody.html('<tr><td colspan="5" class="text-center py-3 text-muted"><i class="fa-solid fa-spinner fa-spin me-1"></i> Loading variations...</td></tr>');

        $.ajax({
          url: varGetUrl + '/' + itemId + '/variations-data',
          method: 'GET',
          cache: false,
          success: function(res) {
            tbody.empty();
            if (res.variations && res.variations.length > 0) {
              res.variations.forEach(function(v) {
                const mlVal = parseFloat(v.ml);
                const ratio = currentModalBottleSize > 0 ? (mlVal / currentModalBottleSize).toFixed(2) : '—';
                const row = `
                  <tr>
                    <td class="ps-3 fw-bold text-dark">
                      <i class="fa-solid fa-martini-glass text-primary me-2"></i> ${v.name}
                    </td>
                    <td class="font-monospace fw-bold">${mlVal} ml</td>
                    <td class="text-muted small">${ratio} of Bottle</td>
                    <td class="fw-bold text-success font-monospace">₹${parseFloat(v.price).toFixed(2)}</td>
                    <td class="text-end pe-3">
                      <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 deleteVarBtn" data-var-id="${v.id}" title="Delete variation" style="border-radius: 6px;">
                        <i class="fa-solid fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                `;
                tbody.append(row);
              });
            } else {
              tbody.html('<tr><td colspan="5" class="text-center py-4 text-muted">No variations added yet. Use the form above to add your first peg or serving format.</td></tr>');
            }
          },
          error: function(xhr) {
            const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Failed to load variations.';
            tbody.html(`<tr><td colspan="5" class="text-center py-3 text-danger"><i class="fa-solid fa-triangle-exclamation me-1"></i> ${msg}</td></tr>`);
          }
        });
      }

      // Add Variation Form Submit via AJAX
      $('#addVariationForm').on('submit', function(e) {
        e.preventDefault();

        const itemId = $('#varFormItemId').val();
        const name = $('#varNameInput').val().trim();
        const ml = $('#varMlInput').val().trim();
        const price = $('#varPriceInput').val().trim();

        if (!itemId || !name || !ml || !price) {
          showVarAlert('Please fill out all variation fields.', 'danger');
          return;
        }

        const btn = $('#saveVarBtn');
        btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');

        $.ajax({
          url: varStoreUrl,
          method: 'POST',
          data: {
            _token: '{{ csrf_token() }}',
            sub_category_id: itemId,
            name: name,
            ml: ml,
            price: price
          },
          success: function(res) {
            btn.prop('disabled', false).html('<i class="fa-solid fa-check"></i> Add');
            $('#varNameInput').val('');
            $('#varMlInput').val('');
            $('#varPriceInput').val('');
            variationsChanged = true;
            showVarAlert(res.message || 'Variation added successfully.', 'success');
            loadVariations(itemId);
          },
          error: function(xhr) {
            btn.prop('disabled', false).html('<i class="fa-solid fa-check"></i> Add');
            const err = xhr.responseJSON ? (xhr.responseJSON.message || JSON.stringify(xhr.responseJSON.errors)) : 'Error adding variation';
            showVarAlert(err, 'danger');
          }
        });
      });

      // Delete Variation via AJAX
      $(document).on('click', '.deleteVarBtn', function() {
        if (!confirm('Are you sure you want to remove this variation?')) return;
        const varId = $(this).data('var-id');
        const itemId = $('#varFormItemId').val();

        $.ajax({
          url: varDeleteUrl + '/' + varId,
          method: 'POST',
          data: { _token: '{{ csrf_token() }}' },
          success: function(res) {
            variationsChanged = true;
            showVarAlert('Variation deleted successfully.', 'info');
            loadVariations(itemId);
          },
          error: function(xhr) {
            const err = xhr.responseJSON ? xhr.responseJSON.message : 'Failed to delete variation.';
            showVarAlert(err, 'danger');
          }
        });
      });

      function showVarAlert(msg, type) {
        const al = $('#varModalAlert');
        al.removeClass('alert-success alert-danger alert-info alert-warning')
          .addClass('alert-' + type)
          .html(msg)
          .slideDown(200);
        setTimeout(function() { al.slideUp(300); }, 3500);
      }

      // Reload page only if variations were added/deleted when modal is closed
      $('#variationModal').on('hidden.bs.modal', function() {
        if (variationsChanged) {
          location.reload();
        }
      });

    });
  </script>
</body>
</html>
