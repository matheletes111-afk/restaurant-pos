<!DOCTYPE html>
<html lang="en">
<head>
  <title>Manage Food Items • {{ @$details->name }} • Bill&Bite POS</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">

  @include('includes.style')

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Font Awesome 6 CDN Loaded after includes.style -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    :root {
      --primary: #ff5e14;
      --primary-hover: #ea580c;
      --primary-light: rgba(255, 94, 20, 0.08);
      --primary-gradient: linear-gradient(135deg, #ff5e14 0%, #ff8c42 100%);
      --dark-slate: #0f172a;
      --border-color: #e2e8f0;
      --text-main: #1e293b;
      --text-muted: #64748b;
      --veg-green: #10b981;
      --nonveg-red: #ef4444;
      --radius-sm: 8px;
      --radius-md: 14px;
      --radius-lg: 18px;
    }

    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
      background-color: #f4f6fb;
      color: var(--text-main);
      overflow-x: hidden;
    }

    /* Mobile-optimized Container Padding */
    .pc-content {
      padding: 24px 28px;
    }

    @media (max-width: 768px) {
      .pc-content {
        padding: 16px 14px;
      }
    }

    /* ===================================================
       PAGE HEADER & CONTROLS DECK (100% RESPONSIVE)
       =================================================== */
    .menu-header-card {
      background: #ffffff;
      border-radius: var(--radius-lg);
      padding: 22px 26px;
      margin-bottom: 20px;
      box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
      border: 1px solid #eef2f6;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      flex-wrap: wrap;
    }

    .header-left-cluster {
      display: flex;
      align-items: center;
      gap: 14px;
      flex: 1 1 auto;
      min-width: 240px;
    }

    .btn-back-link {
      width: 42px;
      height: 42px;
      border-radius: 12px;
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      color: #64748b;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      transition: all 0.2s ease;
      font-size: 0.95rem;
      flex-shrink: 0;
    }

    .btn-back-link:hover {
      background: #ffffff;
      color: var(--primary);
      border-color: var(--primary);
      box-shadow: 0 4px 12px rgba(255, 94, 20, 0.12);
      transform: translateX(-2px);
    }

    .header-icon-badge {
      width: 52px;
      height: 52px;
      border-radius: 14px;
      background: var(--primary-gradient);
      color: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.35rem;
      box-shadow: 0 6px 18px rgba(255, 94, 20, 0.28);
      flex-shrink: 0;
    }

    .header-title-meta {
      display: flex;
      flex-direction: column;
      min-width: 0;
    }

    .header-eyebrow-tag {
      font-size: 0.72rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--primary);
      margin-bottom: 3px;
    }

    .header-main-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.45rem;
      font-weight: 800;
      color: var(--dark-slate);
      margin: 0 0 2px 0;
      letter-spacing: -0.02em;
      line-height: 1.25;
    }

    .header-sub-text {
      font-size: 0.82rem;
      color: var(--text-muted);
      margin: 0;
    }

    .header-right-actions {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
      justify-content: flex-end;
      flex: 0 0 auto;
    }

    .search-box-wrap {
      position: relative;
      min-width: 200px;
    }

    .search-box-wrap i {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: #94a3b8;
      font-size: 0.9rem;
      pointer-events: none;
    }

    .search-box-input {
      width: 100%;
      height: 42px;
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 30px;
      padding: 0 16px 0 38px;
      font-size: 0.85rem;
      color: var(--text-main);
      transition: all 0.2s ease;
      outline: none;
    }

    .search-box-input:focus {
      background: #ffffff;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(255, 94, 20, 0.12);
    }

    .plan-usage-pill {
      background: #f1f5f9;
      border: 1px solid #e2e8f0;
      padding: 8px 14px;
      border-radius: 30px;
      font-size: 0.8rem;
      font-weight: 700;
      color: #475569;
      display: inline-flex;
      align-items: center;
      gap: 7px;
      white-space: nowrap;
    }

    .plan-usage-pill i {
      color: var(--primary);
    }

    .plan-usage-pill.quota-exceeded {
      background: #fef2f2;
      border-color: #fecaca;
      color: #dc2626;
    }

    .plan-usage-pill.quota-exceeded i {
      color: #dc2626;
    }

    .btn-pos-secondary {
      background: #ffffff;
      border: 1.5px solid #e2e8f0;
      color: #475569;
      padding: 8px 18px;
      border-radius: 30px;
      font-size: 0.88rem;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: all 0.2s ease;
      text-decoration: none;
      cursor: pointer;
      white-space: nowrap;
    }

    .btn-pos-secondary:hover {
      border-color: var(--primary);
      color: var(--primary);
      background: #fff8f5;
      box-shadow: 0 4px 12px rgba(255, 94, 20, 0.1);
    }

    .btn-pos-primary {
      background: var(--primary-gradient);
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
      box-shadow: 0 4px 16px rgba(255, 94, 20, 0.28);
      transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
      text-decoration: none;
      cursor: pointer;
      white-space: nowrap;
    }

    .btn-pos-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 22px rgba(255, 94, 20, 0.38);
      color: #ffffff;
    }

    /* Swipeable Filter Pills Toolbar */
    .filter-toolbar-row {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 22px;
      overflow-x: auto;
      flex-wrap: nowrap;
      padding-bottom: 6px;
      -webkit-overflow-scrolling: touch;
      scrollbar-width: none; /* Firefox */
    }

    .filter-toolbar-row::-webkit-scrollbar {
      display: none; /* Chrome, Safari, Edge */
    }

    .filter-btn-pill {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      color: #64748b;
      font-size: 0.82rem;
      font-weight: 700;
      padding: 7px 16px;
      border-radius: 20px;
      cursor: pointer;
      transition: all 0.2s ease;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      white-space: nowrap;
      flex-shrink: 0;
    }

    .filter-btn-pill:hover,
    .filter-btn-pill.active {
      background: var(--primary-gradient);
      border-color: transparent;
      color: #ffffff;
      box-shadow: 0 3px 10px rgba(255, 94, 20, 0.22);
    }

    /* Header Mobile Adjustments */
    @media (max-width: 991px) {
      .menu-header-card {
        flex-direction: column;
        align-items: stretch;
        padding: 18px 20px;
        gap: 16px;
      }
      .header-left-cluster {
        width: 100%;
      }
      .header-right-actions {
        width: 100%;
        justify-content: flex-start;
      }
      .search-box-wrap {
        flex: 1 1 100%;
      }
    }

    @media (max-width: 576px) {
      .menu-header-card {
        padding: 16px 14px;
        gap: 14px;
      }
      .header-left-cluster {
        gap: 12px;
      }
      .btn-back-link {
        width: 38px;
        height: 38px;
        font-size: 0.88rem;
      }
      .header-icon-badge {
        width: 44px;
        height: 44px;
        font-size: 1.15rem;
        border-radius: 12px;
      }
      .header-main-title {
        font-size: 1.25rem;
      }
      .header-sub-text {
        font-size: 0.78rem;
      }
      .header-right-actions {
        flex-direction: column;
        align-items: stretch;
        gap: 10px;
      }
      .search-box-wrap {
        width: 100%;
        min-width: 100%;
      }
      .plan-usage-pill {
        justify-content: center;
        width: 100%;
      }
      .btn-pos-secondary,
      .btn-pos-primary {
        width: 100%;
        height: 44px;
      }
    }

    /* ===================================================
       FOOD ITEM CARDS GRID (100% RESPONSIVE)
       =================================================== */
    .product-grid-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    @media (max-width: 576px) {
      .product-grid-container {
        grid-template-columns: 1fr;
        gap: 16px;
      }
    }

    .luxury-product-card {
      background: #ffffff;
      border-radius: var(--radius-lg);
      border: 1px solid #eef2f6;
      box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
      position: relative;
    }

    .luxury-product-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 16px 32px rgba(15, 23, 42, 0.08);
      border-color: rgba(255, 94, 20, 0.3);
    }

    /* Media Header Area */
    .product-media-wrap {
      position: relative;
      height: 175px;
      background: #f8fafc;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    @media (max-width: 576px) {
      .product-media-wrap {
        height: 190px;
      }
    }

    .product-media-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.45s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .luxury-product-card:hover .product-media-img {
      transform: scale(1.06);
    }

    .product-media-placeholder {
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      color: #ffffff;
      position: relative;
    }

    .product-media-placeholder::before {
      content: '';
      position: absolute;
      inset: 0;
      background-image: radial-gradient(circle at 50% 50%, rgba(255, 94, 20, 0.22) 0%, transparent 70%);
    }

    .product-media-placeholder i {
      font-size: 2.2rem;
      color: #ff8c42;
      z-index: 1;
      margin-bottom: 6px;
    }

    .product-media-placeholder span {
      font-size: 0.76rem;
      color: #94a3b8;
      z-index: 1;
    }

    /* Food Type FSSAI Badges */
    .fssai-food-badge {
      position: absolute;
      top: 12px;
      left: 12px;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      border-radius: 8px;
      padding: 4px 8px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 0.72rem;
      font-weight: 800;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      z-index: 2;
    }

    .fssai-box {
      width: 14px;
      height: 14px;
      border: 2px solid;
      border-radius: 3px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .fssai-box.veg {
      border-color: #10b981;
    }

    .fssai-dot.veg {
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: #10b981;
    }

    .fssai-box.non-veg {
      border-color: #ef4444;
    }

    .fssai-dot.non-veg {
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: #ef4444;
    }

    /* Availability Pill on Card Top Right */
    .status-badge-chip {
      position: absolute;
      top: 12px;
      right: 12px;
      font-size: 0.7rem;
      font-weight: 700;
      padding: 4px 10px;
      border-radius: 20px;
      z-index: 2;
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
    }

    .status-badge-chip.active {
      background: rgba(16, 185, 129, 0.9);
      color: #ffffff;
      box-shadow: 0 3px 8px rgba(16, 185, 129, 0.3);
    }

    .status-badge-chip.inactive {
      background: rgba(245, 158, 11, 0.9);
      color: #ffffff;
      box-shadow: 0 3px 8px rgba(245, 158, 11, 0.3);
    }

    /* Content Area */
    .product-content-area {
      padding: 18px 20px;
      display: flex;
      flex-direction: column;
      flex: 1;
      justify-content: space-between;
    }

    .product-title-text {
      font-family: 'Outfit', sans-serif;
      font-size: 1.12rem;
      font-weight: 800;
      color: var(--dark-slate);
      margin: 0 0 8px 0;
      line-height: 1.35;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
      min-height: 2.7em;
    }

    .product-pricing-bar {
      display: flex;
      align-items: baseline;
      gap: 6px;
      margin-bottom: 14px;
    }

    .price-currency-tag {
      font-size: 0.95rem;
      font-weight: 700;
      color: var(--primary);
    }

    .price-amount-text {
      font-family: 'Outfit', sans-serif;
      font-size: 1.35rem;
      font-weight: 800;
      color: #0f172a;
      letter-spacing: -0.02em;
    }

    .gst-rate-tag {
      font-size: 0.7rem;
      font-weight: 600;
      color: #64748b;
      background: #f1f5f9;
      padding: 2px 7px;
      border-radius: 4px;
      margin-left: auto;
    }

    /* Action Buttons in Card */
    .product-actions-dock {
      display: flex;
      align-items: center;
      gap: 8px;
      padding-top: 14px;
      border-top: 1px solid #f1f5f9;
      margin-top: auto;
      position: relative;
      z-index: 10;
    }

    .btn-action-tile {
      height: 40px;
      border-radius: 10px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 0.84rem;
      font-weight: 700;
      border: 1px solid transparent;
      transition: all 0.2s ease;
      cursor: pointer;
      text-decoration: none;
      gap: 6px;
      position: relative;
      z-index: 10;
    }

    .btn-action-tile i {
      pointer-events: none;
      font-size: 0.92rem;
    }

    .btn-action-tile.edit-action {
      flex: 1;
      background: #f0f9ff;
      border-color: #e0f2fe;
      color: #0284c7;
    }

    .btn-action-tile.edit-action:hover {
      background: #0284c7;
      color: #ffffff;
      transform: translateY(-1px);
      box-shadow: 0 4px 10px rgba(2, 132, 199, 0.2);
    }

    .btn-action-tile.status-toggle {
      width: 40px;
      background: #f8fafc;
      border-color: #e2e8f0;
      color: #64748b;
    }

    .btn-action-tile.status-toggle:hover {
      background: #e2e8f0;
      color: var(--dark-slate);
      transform: translateY(-1px);
    }

    .btn-action-tile.delete-action {
      width: 40px;
      background: #fff1f2;
      border-color: #ffe4e6;
      color: #e11d48;
    }

    .btn-action-tile.delete-action:hover {
      background: #e11d48;
      color: #ffffff;
      transform: translateY(-1px);
      box-shadow: 0 4px 10px rgba(225, 29, 72, 0.25);
    }

    /* ===================================================
       EMPTY STATE
       =================================================== */
    .empty-product-deck {
      background: #ffffff;
      border-radius: var(--radius-lg);
      border: 1px dashed #cbd5e1;
      padding: 60px 20px;
      text-align: center;
      grid-column: 1 / -1;
    }

    .empty-icon-wrap {
      width: 74px;
      height: 74px;
      border-radius: 50%;
      background: var(--primary-light);
      color: var(--primary);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      margin: 0 auto 16px;
    }

    .empty-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.3rem;
      font-weight: 800;
      color: var(--dark-slate);
      margin-bottom: 6px;
    }

    .empty-desc {
      font-size: 0.88rem;
      color: var(--text-muted);
      max-width: 420px;
      margin: 0 auto 20px;
    }

    /* ===================================================
       MODAL STYLING (MOBILE-FIRST)
       =================================================== */
    .modal-dialog {
      margin: 16px auto;
      max-width: 500px;
    }

    .modal-dialog.modal-lg {
      max-width: 720px;
    }

    .modal-pos-content {
      border: none;
      border-radius: var(--radius-lg);
      overflow: hidden;
      box-shadow: 0 25px 50px rgba(15, 23, 42, 0.25);
    }

    .modal-pos-header {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
      color: #ffffff;
      padding: 18px 22px;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .modal-pos-header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: var(--primary-gradient);
    }

    .modal-pos-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.15rem;
      font-weight: 800;
      color: #ffffff;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .modal-btn-close {
      background: rgba(255, 255, 255, 0.1);
      border: none;
      color: #ffffff;
      width: 32px;
      height: 32px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.9rem;
      cursor: pointer;
      transition: all 0.2s;
    }

    .modal-btn-close:hover {
      background: rgba(255, 255, 255, 0.25);
      transform: rotate(90deg);
    }

    .modal-pos-body {
      padding: 20px 22px;
      background: #ffffff;
    }

    .modal-pos-footer {
      padding: 14px 22px;
      background: #f8fafc;
      border-top: 1px solid #e2e8f0;
      display: flex;
      justify-content: flex-end;
      gap: 10px;
    }

    @media (max-width: 576px) {
      .modal-pos-footer {
        flex-direction: column-reverse;
      }
      .modal-pos-footer button,
      .modal-pos-footer a {
        width: 100%;
        text-align: center;
        justify-content: center;
      }
    }

    .form-control-custom {
      background: #f8fafc;
      border: 1.5px solid #e2e8f0;
      border-radius: 12px;
      padding: 10px 16px;
      font-size: 0.9rem;
      font-weight: 500;
      color: var(--text-main);
      transition: all 0.2s ease;
      width: 100%;
    }

    .form-control-custom:focus {
      background: #ffffff;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(255, 94, 20, 0.12);
      outline: none;
    }

    .upload-preview-box {
      width: 100%;
      height: 130px;
      border: 2px dashed #cbd5e1;
      border-radius: 14px;
      background: #f8fafc;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      margin-top: 10px;
      position: relative;
    }

    .upload-preview-box img {
      width: 100%;
      height: 100%;
      object-fit: cover;
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

    .btn-modal-cancel:hover {
      background: #e2e8f0;
      color: #1e293b;
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

      <!-- ===================================================
           PAGE HEADER & CONTROLS CLUSTER (100% RESPONSIVE)
           =================================================== -->
      <div class="menu-header-card">
        <div class="header-left-cluster">
          <a href="{{ route('manage.category') }}" class="btn-back-link" title="Back to Categories">
            <i class="fa-solid fa-arrow-left"></i>
          </a>

          <div class="header-icon-badge">
            <i class="fa-solid fa-utensils"></i>
          </div>

          <div class="header-title-meta">
            <span class="header-eyebrow-tag">Category Menu Catalog</span>
            <h1 class="header-main-title">{{ @$details->name }}</h1>
            <p class="header-sub-text">Manage dishes, pricing, dietary indicators &amp; kitchen status.</p>
          </div>
        </div>

        <div class="header-right-actions">
          <!-- Real-Time Client Search Filter -->
          <div class="search-box-wrap">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="dishSearchInput" class="search-box-input" placeholder="Search dishes by name or price...">
          </div>

          @php
            $restaurantDishesCount = $total_dishes ?? count($data ?? []);
            $dishQuotaMax = (isset($plan_details) && isset($plan_details->total_number_of_dishes)) ? (int)$plan_details->total_number_of_dishes : 0;
            $isQuotaEnforced = ($dishQuotaMax > 0);
            $isDishLimitReached = ($isQuotaEnforced && $restaurantDishesCount >= $dishQuotaMax);
          @endphp

          <!-- Subscription Plan Quota Pill (Restaurant-Wide Total) -->
          @if(isset($plan_details) && isset($plan_details->total_number_of_dishes))
          <div class="plan-usage-pill {{ $isDishLimitReached ? 'quota-exceeded' : '' }}" title="Overall dishes across all categories for your restaurant">
            <i class="fa-solid fa-chart-pie"></i>
            <span>{{ $restaurantDishesCount }} / {{ $dishQuotaMax == 0 ? 'Unlimited' : $dishQuotaMax }} Used (Restaurant Total)</span>
          </div>
          @endif

          <!-- Bulk Upload Button -->
          @if($isDishLimitReached)
          <button type="button" class="btn-pos-secondary disabled" disabled style="opacity: 0.6; cursor: not-allowed;" title="Overall restaurant dish limit reached ({{ $dishQuotaMax }} max). Upgrade plan to upload more dishes.">
            <i class="fa-solid fa-file-excel text-muted" style="margin-right: 4px;"></i>
            <span>Bulk Upload</span>
          </button>
          @else
          <button type="button" class="btn-pos-secondary" data-toggle="modal" data-target="#bulkUploadModal" data-bs-toggle="modal" data-bs-target="#bulkUploadModal">
            <i class="fa-solid fa-file-excel text-success" style="margin-right: 4px;"></i>
            <span>Bulk Upload</span>
          </button>
          @endif

          <!-- Add Product Button -->
          @if(auth()->user()->hasPermission('menu_master', 'add'))
            @if($isDishLimitReached)
            <button type="button" class="btn-pos-primary disabled" disabled style="opacity:0.65; cursor:not-allowed; background:#64748b; border-color:#64748b; box-shadow:none;" title="Restaurant dish limit reached ({{ $restaurantDishesCount }}/{{ $dishQuotaMax }} dishes). Upgrade your plan to add more.">
              <i class="fa-solid fa-lock"></i>
              <span>Limit Reached</span>
            </button>
            @else
            <button type="button" class="btn-pos-primary" data-toggle="modal" data-target="#addProductModal" data-bs-toggle="modal" data-bs-target="#addProductModal">
              <i class="fa-solid fa-plus"></i>
              <span>Add Food Item</span>
            </button>
            @endif
          @endif
        </div>
      </div>

      <!-- Plan Quota Warning Alert (if overall restaurant limit reached) -->
      @if($isDishLimitReached)
      <div class="alert alert-warning d-flex align-items-center mb-3 mt-1" style="border-radius: 12px; background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.35); color: #92400e; padding: 12px 18px;" role="alert">
        <i class="fa-solid fa-triangle-exclamation fs-5 me-2 flex-shrink-0" style="color: #d97706;"></i>
        <div style="font-size: 0.88rem; line-height: 1.45;">
          <strong>Restaurant Dish Limit Reached:</strong> Your restaurant has used <strong>{{ $restaurantDishesCount }} of {{ $dishQuotaMax }}</strong> dishes allowed across all categories on your active plan. Upgrade your plan to add or bulk upload additional dishes.
        </div>
      </div>
      @endif

      <!-- Swipeable Filter Pills Toolbar (Touch Friendly) -->
      <div class="filter-toolbar-row">
        <button type="button" class="filter-btn-pill active" data-filter="all">
          <i class="fa-solid fa-utensils me-1"></i> All Dishes ({{ count($data ?? []) }})
        </button>
        <button type="button" class="filter-btn-pill" data-filter="veg">
          <span class="fssai-dot veg" style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#10b981;"></span> Veg Only
        </button>
        <button type="button" class="filter-btn-pill" data-filter="non-veg">
          <span class="fssai-dot non-veg" style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#ef4444;"></span> Non-Veg
        </button>
        <button type="button" class="filter-btn-pill" data-filter="active">
          <i class="fa-solid fa-circle-check text-success me-1"></i> Active
        </button>
        <button type="button" class="filter-btn-pill" data-filter="inactive">
          <i class="fa-solid fa-circle-pause text-warning me-1"></i> Inactive
        </button>
      </div>

      <!-- Flash Messages -->
      @include('includes.message')

      <!-- ===================================================
           FOOD ITEMS CARDS GRID (100% RESPONSIVE)
           =================================================== -->
      <div class="product-grid-container" id="productGrid">
        @forelse(@$data as $value)
        <div class="luxury-product-card product-item-card"
             data-name="{{ strtolower($value->name) }}"
             data-price="{{ $value->price }}"
             data-type="{{ strtolower($value->food_type) }}"
             data-status="{{ $value->status == 'A' ? 'active' : 'inactive' }}">
          
          <!-- Media Thumbnail -->
          <div class="product-media-wrap">
            <!-- FSSAI Veg / Non-Veg Badge -->
            <div class="fssai-food-badge" title="{{ $value->food_type }}">
              <div class="fssai-box {{ $value->food_type == 'VEG' ? 'veg' : 'non-veg' }}">
                <div class="fssai-dot {{ $value->food_type == 'VEG' ? 'veg' : 'non-veg' }}"></div>
              </div>
              <span>{{ $value->food_type }}</span>
            </div>

            <!-- Active / Inactive Chip -->
            <span class="status-badge-chip {{ $value->status == 'A' ? 'active' : 'inactive' }}">
              <i class="fa-solid {{ $value->status == 'A' ? 'fa-check' : 'fa-pause' }} me-1"></i>
              {{ $value->status == 'A' ? 'Active' : 'Paused' }}
            </span>

            @if($value->image)
              <img src="{{ URL::to('storage/category') }}/{{ @$value->image }}" alt="{{ @$value->name }}" class="product-media-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
              <div class="product-media-placeholder" style="display: none;">
                <i class="fa-solid fa-utensils"></i>
                <span>No Image</span>
              </div>
            @else
              <div class="product-media-placeholder">
                <i class="fa-solid fa-utensils"></i>
                <span>No Image</span>
              </div>
            @endif
          </div>

          <!-- Content Details -->
          <div class="product-content-area">
            <div>
              <h3 class="product-title-text" title="{{ @$value->name }}">
                {{ @$value->name }}
              </h3>

              <div class="product-pricing-bar">
                <span class="price-currency-tag">₹</span>
                <span class="price-amount-text">{{ number_format($value->price, 2) }}</span>
                
                @if(isset($value->gst_rate) && $value->gst_rate > 0)
                <span class="gst-rate-tag">{{ $value->gst_rate }}% GST</span>
                @endif
              </div>
            </div>

            <!-- Action Buttons Dock -->
            <div class="product-actions-dock">
              @if(auth()->user()->hasPermission('menu_master', 'edit'))
              <!-- Edit Button -->
              <button type="button"
                      class="btn-action-tile edit-action edit-btn"
                      data-id="{{ $value->id }}"
                      data-name="{{ $value->name }}"
                      data-price="{{ $value->price }}"
                      data-type="{{ $value->food_type }}"
                      data-image="{{ $value->image }}"
                      title="Edit Food Item">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Edit</span>
              </button>

              <!-- Status Toggle -->
              <a href="{{ route('manage.subcategory.category.status', $value->id) }}"
                 class="btn-action-tile status-toggle"
                 title="{{ $value->status == 'A' ? 'Pause Item Availability' : 'Activate Item' }}"
                 onclick="return confirm('Change status for \'{{ addslashes($value->name) }}\'?')">
                <i class="fa-solid {{ $value->status == 'A' ? 'fa-eye-slash text-warning' : 'fa-eye text-success' }}"></i>
              </a>
              @endif

              @if(auth()->user()->hasPermission('menu_master', 'delete'))
              <!-- Delete Button -->
              <a href="{{ route('manage.subcategory.category.delete', $value->id) }}"
                 class="btn-action-tile delete-action btn-delete-product"
                 title="Delete Food Item"
                 onclick="return confirm('Are you sure you want to permanently delete \'{{ addslashes($value->name) }}\'?')">
                <i class="fa-solid fa-trash text-danger"></i>
              </a>
              @endif
            </div>

          </div><!-- /.product-content-area -->

        </div><!-- /.luxury-product-card -->
        @empty
        <!-- Empty State -->
        <div class="empty-product-deck">
          <div class="empty-icon-wrap">
            <i class="fa-solid fa-utensils"></i>
          </div>
          <h3 class="empty-title">No Food Items Yet</h3>
          <p class="empty-desc">Add delicious dishes to "{{ @$details->name }}" individually or bulk upload from an Excel template.</p>
          
          <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: center; width: 100%;">
            @if($isDishLimitReached)
            <button type="button" class="btn-pos-secondary disabled" disabled style="opacity: 0.6; cursor: not-allowed;" title="Overall restaurant dish limit reached">
              <i class="fa-solid fa-file-excel text-muted" style="margin-right: 4px;"></i>
              <span>Bulk Upload</span>
            </button>
            @else
            <button type="button" class="btn-pos-secondary" data-toggle="modal" data-target="#bulkUploadModal" data-bs-toggle="modal" data-bs-target="#bulkUploadModal">
              <i class="fa-solid fa-file-excel text-success" style="margin-right: 4px;"></i>
              <span>Bulk Upload</span>
            </button>
            @endif

            @if(auth()->user()->hasPermission('menu_master', 'add'))
              @if($isDishLimitReached)
              <button type="button" class="btn-pos-primary disabled" disabled style="opacity:0.65; cursor:not-allowed; background:#64748b; border-color:#64748b; box-shadow:none;" title="Overall restaurant dish limit reached ({{ $dishQuotaMax }} max). Upgrade plan to add more.">
                <i class="fa-solid fa-lock"></i>
                <span>Limit Reached ({{ $restaurantDishesCount }}/{{ $dishQuotaMax }})</span>
              </button>
              @else
              <button type="button" class="btn-pos-primary" data-toggle="modal" data-target="#addProductModal" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="fa-solid fa-plus"></i>
                <span>Add First Dish</span>
              </button>
              @endif
            @endif
          </div>
        </div>
        @endforelse
      </div>

    </div><!-- /.pc-content -->
  </div><!-- /.pc-container -->


  <!-- ===================================================
       MODAL: ADD PRODUCT
       =================================================== -->
  <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form method="POST" action="{{ route('manage.subcategory.category.insert') }}" enctype="multipart/form-data" style="width: 100%;">
        @csrf
        <input type="hidden" name="category_id" value="{{ @$id }}">

        <div class="modal-content modal-pos-content">
          
          <div class="modal-pos-header">
            <h5 class="modal-pos-title" id="addProductModalLabel">
              <i class="fa-solid fa-circle-plus text-primary"></i>
              <span>Add Food Item</span>
            </h5>
            <button type="button" class="modal-btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>

          <div class="modal-pos-body">
            <div class="form-group mb-3">
              <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700; color: #1e293b;">
                Dish Name <span class="text-danger">*</span>
              </label>
              <input type="text" name="name" class="form-control-custom" required placeholder="e.g. Butter Chicken, Paneer Tikka" autocomplete="off">
            </div>

            <div class="row">
              <div class="col-md-6 form-group mb-3">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700; color: #1e293b;">
                  Base Price (₹) <span class="text-danger">*</span>
                </label>
                <input type="number" step="0.01" min="0" name="price" class="form-control-custom" required placeholder="0.00">
              </div>

              <div class="col-md-6 form-group mb-3">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700; color: #1e293b;">
                  Food Type <span class="text-danger">*</span>
                </label>
                <select name="food_type" class="form-control-custom" required>
                  <option value="VEG">🟢 VEG</option>
                  <option value="NON-VEG">🔴 NON-VEG</option>
                </select>
              </div>
            </div>

            <div class="form-group mb-2">
              <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700; color: #1e293b;">
                Dish Photo <small class="text-muted font-weight-normal">(Optional)</small>
              </label>
              <input type="file" name="image" id="add_dish_image_input" class="form-control-custom" accept="image/jpeg,image/png,image/jpg,image/webp,image/gif">
              <small class="form-text text-muted" style="font-size: 0.74rem;">Formats: JPG, PNG, WEBP, GIF (Max: 5MB)</small>

              <div class="upload-preview-box" id="add_dish_preview_wrap" style="display: none;">
                <img id="add_dish_image_preview" src="" alt="Selected Preview">
              </div>
            </div>
          </div>

          <div class="modal-pos-footer">
            <button type="button" class="btn-modal-cancel" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn-pos-primary">
              <i class="fa-solid fa-check"></i>
              <span>Save Food Item</span>
            </button>
          </div>

        </div>
      </form>
    </div>
  </div>


  <!-- ===================================================
       MODAL: EDIT PRODUCT
       =================================================== -->
  <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form method="POST" action="{{ route('manage.subcategory.category.update') }}" enctype="multipart/form-data" style="width: 100%;">
        @csrf
        <input type="hidden" name="id" id="edit_id">

        <div class="modal-content modal-pos-content">
          
          <div class="modal-pos-header">
            <h5 class="modal-pos-title" id="editProductModalLabel">
              <i class="fa-solid fa-pen-to-square text-primary"></i>
              <span>Edit Food Item</span>
            </h5>
            <button type="button" class="modal-btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>

          <div class="modal-pos-body">
            <div class="form-group mb-3">
              <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700; color: #1e293b;">
                Dish Name <span class="text-danger">*</span>
              </label>
              <input type="text" name="name" id="edit_name" class="form-control-custom" required placeholder="Food Item Name" autocomplete="off">
            </div>

            <div class="row">
              <div class="col-md-6 form-group mb-3">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700; color: #1e293b;">
                  Base Price (₹) <span class="text-danger">*</span>
                </label>
                <input type="number" step="0.01" min="0" name="price" id="edit_price" class="form-control-custom" required placeholder="0.00">
              </div>

              <div class="col-md-6 form-group mb-3">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700; color: #1e293b;">
                  Food Type <span class="text-danger">*</span>
                </label>
                <select name="food_type" id="edit_food_type" class="form-control-custom" required>
                  <option value="VEG">🟢 VEG</option>
                  <option value="NON-VEG">🔴 NON-VEG</option>
                </select>
              </div>
            </div>

            <div class="form-group mb-2">
              <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700; color: #1e293b;">
                Update Photo <small class="text-muted font-weight-normal">(Leave empty to retain current)</small>
              </label>
              <input type="file" name="image" id="edit_dish_image_input" class="form-control-custom" accept="image/jpeg,image/png,image/jpg,image/webp,image/gif">
              <small class="form-text text-muted" style="font-size: 0.74rem;">Formats: JPG, PNG, WEBP, GIF (Max: 5MB)</small>

              <div class="upload-preview-box" id="edit_dish_preview_wrap" style="display: none;">
                <img id="edit_dish_image_preview" src="" alt="Food Image Preview">
              </div>
            </div>
          </div>

          <div class="modal-pos-footer">
            <button type="button" class="btn-modal-cancel" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn-pos-primary">
              <i class="fa-solid fa-check"></i>
              <span>Update Dish</span>
            </button>
          </div>

        </div>
      </form>
    </div>
  </div>


  <!-- ===================================================
       MODAL: BULK UPLOAD
       =================================================== -->
  <div class="modal fade" id="bulkUploadModal" tabindex="-1" aria-labelledby="bulkUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <form method="POST" action="{{ route('manage.subcategory.category.bulk.upload') }}" enctype="multipart/form-data" style="width: 100%;">
        @csrf
        <input type="hidden" name="category_id" value="{{ @$id }}">

        <div class="modal-content modal-pos-content">
          
          <div class="modal-pos-header">
            <h5 class="modal-pos-title" id="bulkUploadModalLabel">
              <i class="fa-solid fa-file-excel text-success"></i>
              <span>Bulk Upload Dishes</span>
            </h5>
            <button type="button" class="modal-btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>

          <div class="modal-pos-body">
            <!-- Instruction Strip -->
            <div class="alert alert-info border-0 shadow-sm mb-4" style="border-radius: 12px; background: #f0f9ff; color: #0369a1;">
              <h6 class="font-weight-bold mb-2"><i class="fa-solid fa-circle-info me-1"></i> Quick Step-by-Step Instructions:</h6>
              <ol class="mb-0 ps-3" style="font-size: 0.84rem; line-height: 1.6;">
                <li>Download the sample Excel template by clicking the button below.</li>
                <li>Fill in your dishes: <strong>Product Name</strong>, <strong>Price (₹)</strong>, <strong>Food Type (VEG / NON-VEG)</strong>, and <strong>Category</strong>.</li>
                <li><strong>Strict Matching:</strong> Category name must match an existing active category. If any row has an unmatched category, the entire upload is cancelled to prevent data corruption.</li>
              </ol>
            </div>

            <!-- File Upload Field -->
            <div class="form-group mb-3">
              <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700; color: #1e293b;">
                Select Completed Excel/CSV File <span class="text-danger">*</span>
              </label>
              <input type="file" name="bulk_file" class="form-control-custom" accept=".xlsx,.xls,.csv" required>
              <small class="form-text text-muted" style="font-size: 0.74rem;">Accepted file extensions: .xlsx, .xls, .csv (Max 5MB)</small>
            </div>

            <!-- Sample Format Preview Table -->
            <div class="mt-4">
              <h6 style="font-size: 0.82rem; font-weight: 700; color: #1e293b; margin-bottom: 8px;">
                <i class="fa-solid fa-table me-1 text-primary"></i> Required Excel Format (4 Columns):
              </h6>
              <div class="table-responsive" style="border-radius: 10px; overflow-x: auto; border: 1px solid #e2e8f0; width: 100%;">
                <table class="table table-bordered table-sm mb-0" style="font-size: 0.82rem; min-width: 440px;">
                  <thead style="background: #f8fafc; color: #475569;">
                    <tr>
                      <th style="padding: 8px 12px;">Product Name</th>
                      <th style="padding: 8px 12px;">Price (₹)</th>
                      <th style="padding: 8px 12px;">Food Type</th>
                      <th style="padding: 8px 12px;">Category</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td style="padding: 8px 12px;">Paneer Butter Masala</td>
                      <td style="padding: 8px 12px;">250.00</td>
                      <td style="padding: 8px 12px;"><span class="badge bg-success text-white">VEG</span></td>
                      <td style="padding: 8px 12px;">{{ @$details->name ?? 'Main Course' }}</td>
                    </tr>
                    <tr>
                      <td style="padding: 8px 12px;">Chicken Biryani</td>
                      <td style="padding: 8px 12px;">320.00</td>
                      <td style="padding: 8px 12px;"><span class="badge bg-danger text-white">NON-VEG</span></td>
                      <td style="padding: 8px 12px;">{{ @$details->name ?? 'Main Course' }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

          </div>

          <div class="modal-pos-footer" style="justify-content: space-between;">
            <a href="{{ route('manage.subcategory.category.template', $id) }}" class="btn btn-outline-info" style="border-radius: 30px; font-weight: 700; font-size: 0.84rem;">
              <i class="fa-solid fa-download me-1"></i> Download Template
            </a>

            <div style="display: flex; gap: 8px;">
              <button type="button" class="btn-modal-cancel" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn-pos-primary">
                <i class="fa-solid fa-upload"></i>
                <span>Upload &amp; Import</span>
              </button>
            </div>
          </div>

        </div>
      </form>
    </div>
  </div>


  <!-- JS & Scripts -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  @include('includes.script')

  <script>
    $(document).ready(function() {
      // 1. Live Client-Side Dishes Search Filter
      $('#dishSearchInput').on('keyup', function() {
        applyFilters();
      });

      // 2. Filter Pills (All / Veg / Non-Veg / Active / Inactive)
      let activeFilter = 'all';
      $('.filter-btn-pill').on('click', function() {
        $('.filter-btn-pill').removeClass('active');
        $(this).addClass('active');
        activeFilter = $(this).data('filter');
        applyFilters();
      });

      function applyFilters() {
        const query = ($('#dishSearchInput').val() || '').toLowerCase().trim();

        $('.product-item-card').each(function() {
          const name = $(this).data('name') || '';
          const price = String($(this).data('price') || '');
          const type = $(this).data('type') || '';
          const status = $(this).data('status') || '';

          const matchesQuery = name.includes(query) || price.includes(query);
          let matchesFilter = true;

          if (activeFilter === 'veg') {
            matchesFilter = (type === 'veg');
          } else if (activeFilter === 'non-veg') {
            matchesFilter = (type === 'non-veg');
          } else if (activeFilter === 'active') {
            matchesFilter = (status === 'active');
          } else if (activeFilter === 'inactive') {
            matchesFilter = (status === 'inactive');
          }

          if (matchesQuery && matchesFilter) {
            $(this).show();
          } else {
            $(this).hide();
          }
        });
      }

      // 3. Edit Modal Population
      $('.edit-btn').on('click', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let name = $(this).data('name');
        let price = $(this).data('price');
        let type = $(this).data('type');
        let image = $(this).data('image');

        $('#edit_id').val(id);
        $('#edit_name').val(name);
        $('#edit_price').val(price);
        $('#edit_food_type').val(type);

        if (image) {
          $('#edit_dish_image_preview').attr('src', '{{ URL::to("storage/category") }}/' + image);
          $('#edit_dish_preview_wrap').show();
        } else {
          $('#edit_dish_preview_wrap').hide();
        }

        if (typeof $('#editProductModal').modal === 'function') {
          $('#editProductModal').modal('show');
        }
      });

      // 4. Live Image Preview for Add Modal
      $('#add_dish_image_input').on('change', function() {
        const file = this.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function(e) {
            $('#add_dish_image_preview').attr('src', e.target.result);
            $('#add_dish_preview_wrap').show();
          };
          reader.readAsDataURL(file);
        } else {
          $('#add_dish_preview_wrap').hide();
        }
      });

      // 5. Live Image Preview for Edit Modal
      $('#edit_dish_image_input').on('change', function() {
        const file = this.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function(e) {
            $('#edit_dish_image_preview').attr('src', e.target.result);
            $('#edit_dish_preview_wrap').show();
          };
          reader.readAsDataURL(file);
        }
      });
    });
  </script>

</body>
</html>