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
       FOOD ITEM CARDS GRID (6 DISHES PER ROW ON DESKTOP)
       =================================================== */
    .product-grid-container {
      display: grid;
      grid-template-columns: repeat(6, 1fr);
      gap: 14px;
      margin-bottom: 30px;
    }

    @media (max-width: 1599px) {
      .product-grid-container {
        grid-template-columns: repeat(5, 1fr);
        gap: 12px;
      }
    }

    @media (max-width: 1280px) {
      .product-grid-container {
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
      }
    }

    @media (max-width: 991px) {
      .product-grid-container {
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
      }
    }

    @media (max-width: 768px) {
      .product-grid-container {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
      }
    }

    @media (max-width: 480px) {
      .product-grid-container {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
      }
    }

    .luxury-product-card {
      background: #ffffff;
      border-radius: var(--radius-md);
      border: 1px solid #eef2f6;
      box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
      position: relative;
    }

    .luxury-product-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
      border-color: rgba(255, 94, 20, 0.3);
    }

    /* Media Header Area */
    .product-media-wrap {
      position: relative;
      height: 115px;
      background: #f8fafc;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    @media (max-width: 576px) {
      .product-media-wrap {
        height: 105px;
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
      font-size: 1.6rem;
      color: #ff8c42;
      z-index: 1;
      margin-bottom: 2px;
    }

    .product-media-placeholder span {
      font-size: 0.68rem;
      color: #94a3b8;
      z-index: 1;
    }

    /* Food Type FSSAI Badges */
    .fssai-food-badge {
      position: absolute;
      top: 6px;
      left: 6px;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      border-radius: 6px;
      padding: 2px 6px;
      display: inline-flex;
      align-items: center;
      gap: 4px;
      font-size: 0.65rem;
      font-weight: 800;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
      z-index: 2;
    }

    .fssai-box {
      width: 12px;
      height: 12px;
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
      width: 5px;
      height: 5px;
      border-radius: 50%;
      background: #10b981;
    }

    .fssai-box.non-veg {
      border-color: #ef4444;
    }

    .fssai-dot.non-veg {
      width: 5px;
      height: 5px;
      border-radius: 50%;
      background: #ef4444;
    }

    /* Availability Pill on Card Top Right */
    .status-badge-chip {
      position: absolute;
      top: 6px;
      right: 6px;
      font-size: 0.62rem;
      font-weight: 700;
      padding: 2px 7px;
      border-radius: 20px;
      z-index: 2;
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
    }

    .status-badge-chip.active {
      background: rgba(16, 185, 129, 0.9);
      color: #ffffff;
      box-shadow: 0 2px 6px rgba(16, 185, 129, 0.3);
    }

    .status-badge-chip.inactive {
      background: rgba(245, 158, 11, 0.9);
      color: #ffffff;
      box-shadow: 0 2px 6px rgba(245, 158, 11, 0.3);
    }

    /* Content Area */
    .product-content-area {
      padding: 10px 12px;
      display: flex;
      flex-direction: column;
      flex: 1;
      justify-content: space-between;
    }

    .product-title-text {
      font-family: 'Outfit', sans-serif;
      font-size: 0.9rem;
      font-weight: 700;
      color: var(--dark-slate);
      margin: 0 0 2px 0;
      line-height: 1.25;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
      min-height: auto;
      max-height: 2.5em;
    }

    .product-pricing-bar {
      display: flex;
      align-items: baseline;
      gap: 4px;
      margin-bottom: 6px;
    }

    .price-currency-tag {
      font-size: 0.82rem;
      font-weight: 700;
      color: var(--primary);
    }

    .price-amount-text {
      font-family: 'Outfit', sans-serif;
      font-size: 1.05rem;
      font-weight: 800;
      color: #0f172a;
      letter-spacing: -0.02em;
    }

    .gst-rate-tag {
      font-size: 0.65rem;
      font-weight: 600;
      color: #64748b;
      background: #f1f5f9;
      padding: 1px 5px;
      border-radius: 4px;
      margin-left: auto;
    }

    /* Action Buttons in Card */
    .product-actions-dock {
      display: flex;
      align-items: center;
      gap: 6px;
      padding-top: 8px;
      border-top: 1px solid #f1f5f9;
      margin-top: auto;
      position: relative;
      z-index: 10;
    }

    .btn-action-tile {
      height: 32px;
      border-radius: 8px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 0.78rem;
      font-weight: 700;
      border: 1px solid transparent;
      transition: all 0.2s ease;
      cursor: pointer;
      text-decoration: none;
      gap: 4px;
      position: relative;
      z-index: 10;
    }

    .btn-action-tile i {
      pointer-events: none;
      font-size: 0.82rem;
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
      width: 32px;
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
      width: 32px;
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

    /* Addon Mapping in Modals */
    .addon-item-row {
      display: flex !important;
      align-items: center;
      justify-content: space-between;
      transition: all 0.15s ease;
      cursor: pointer;
    }
    .addon-item-row:hover {
      border-color: var(--primary) !important;
      background: #fff8f5 !important;
    }
    .addon-item-row.is-checked {
      border-color: rgba(255, 94, 20, 0.45) !important;
      background: #fff9f6 !important;
    }
    .addon-item-row.is-hidden-addon {
      display: none !important;
    }

    .product-item-card.is-hidden-dish {
      display: none !important;
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

              @if($value->addons && $value->addons->count() > 0)
              <div class="mb-2">
                <span class="badge" style="background:#fff3ed; color:#ff5e14; border:1px solid #ffdecb; font-size:0.65rem; padding: 2px 6px;">
                  <i class="fa-solid fa-puzzle-piece me-1"></i>{{ $value->addons->count() }} Addon{{ $value->addons->count() > 1 ? 's' : '' }}
                </span>
              </div>
              @endif
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
                      data-addons="{{ $value->addons ? $value->addons->pluck('id')->join(',') : '' }}"
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
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 600px;">
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

            <div class="form-group mb-3">
              <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700; color: #1e293b;">
                Dish Photo <small class="text-muted font-weight-normal">(Optional)</small>
              </label>
              <input type="file" name="image" id="add_dish_image_input" class="form-control-custom" accept="image/jpeg,image/png,image/jpg,image/webp,image/gif">
              <small class="form-text text-muted" style="font-size: 0.74rem;">Formats: JPG, PNG, WEBP, GIF (Max: 5MB)</small>

              <div class="upload-preview-box" id="add_dish_preview_wrap" style="display: none;">
                <img id="add_dish_image_preview" src="" alt="Selected Preview">
              </div>
            </div>

            <!-- Dish Addon Mapping Section -->
            <div class="form-group mb-2">
              <div class="d-flex align-items-center justify-content-between mb-1">
                <label class="form-label font-weight-bold mb-0" style="font-size: 0.82rem; font-weight: 700; color: #1e293b;">
                  <i class="fa-solid fa-puzzle-piece text-primary me-1"></i> Map Dish Addons <small class="text-muted font-weight-normal">(Optional)</small>
                </label>
                <span class="badge font-weight-bold" id="add_addon_count_badge" style="background: #fff3ed; color: #ff5e14; border: 1px solid #ffdecb; font-size: 0.72rem; padding: 3px 8px; border-radius: 10px;">
                  0 Selected
                </span>
              </div>

              @if(isset($addons) && count($addons) > 0)
                <!-- Search Filter for Addons -->
                <div class="input-group input-group-sm mb-2">
                  <span class="input-group-text bg-light border-end-0" style="border-radius: 8px 0 0 8px;">
                    <i class="fa-solid fa-magnifying-glass text-muted" style="font-size: 0.8rem;"></i>
                  </span>
                  <input type="text" class="form-control form-control-sm border-start-0 addon-search-filter" 
                         data-target="#add_addon_list" 
                         placeholder="Search addons by name or price..." 
                         style="border-radius: 0 8px 8px 0; font-size: 0.82rem;">
                </div>

                <!-- Quick Action Select/Clear -->
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <small class="text-muted" style="font-size: 0.72rem;">Check addons to map them with this dish:</small>
                  <div>
                    <a href="javascript:void(0)" class="text-primary small fw-bold me-2 select-all-addons" data-target="#add_addon_list" style="font-size: 0.72rem; text-decoration: none;">Select All</a>
                    <a href="javascript:void(0)" class="text-muted small fw-bold deselect-all-addons" data-target="#add_addon_list" style="font-size: 0.72rem; text-decoration: none;">Clear</a>
                  </div>
                </div>

                <!-- Scrollable Addons Checkbox List -->
                <div class="addon-mapping-list border rounded p-2" id="add_addon_list" style="max-height: 180px; overflow-y: auto; background: #f8fafc;">
                  @foreach($addons as $addon)
                  <label class="addon-item-row p-2 mb-1 rounded bg-white border" 
                         for="add_addon_{{ $addon->id }}" 
                         data-name="{{ strtolower($addon->name) }}"
                         data-price="{{ $addon->price }}">
                    <div class="d-flex align-items-center gap-2">
                      <input type="checkbox" name="addon_ids[]" value="{{ $addon->id }}" 
                             class="form-check-input addon-checkbox mt-0" 
                             id="add_addon_{{ $addon->id }}"
                             data-badge="#add_addon_count_badge"
                             data-container="#add_addon_list"
                             style="cursor: pointer; width: 16px; height: 16px;">
                      
                      @if($addon->food_type == 'NON-VEG')
                        <span class="badge" style="background:#fef2f2; color:#dc2626; border:1px solid #fecaca; font-size:0.65rem; padding: 2px 5px;">🔴 Non-Veg</span>
                      @else
                        <span class="badge" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; font-size:0.65rem; padding: 2px 5px;">🟢 Veg</span>
                      @endif
                      
                      <span class="addon-name-text fw-bold text-dark" style="font-size: 0.82rem;">{{ $addon->name }}</span>
                    </div>
                    <span class="badge bg-light text-dark font-monospace fw-bold" style="font-size: 0.8rem; border: 1px solid #e2e8f0;">
                      +₹{{ number_format($addon->price, 2) }}
                    </span>
                  </label>
                  @endforeach
                  
                  <div class="no-addon-search-match text-center py-2 text-muted small" style="display: none;">
                    <i class="fa-solid fa-magnifying-glass me-1 opacity-50"></i> No matching addons found
                  </div>
                </div>
              @else
                <div class="alert alert-light border text-center py-3 mb-0" style="border-radius: 10px; background: #f8fafc;">
                  <i class="fa-solid fa-puzzle-piece text-muted fa-2x mb-2 opacity-50"></i>
                  <p class="mb-1 text-dark fw-bold" style="font-size: 0.82rem;">No Dish Addons Available</p>
                  <p class="small text-muted mb-2" style="font-size: 0.74rem;">Create toppings, dips, cheese, or sides in Addon Master.</p>
                  <a href="{{ route('addon.index') }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill fw-bold" style="font-size: 0.75rem;">
                    <i class="fa-solid fa-plus me-1"></i> Open Addon Master
                  </a>
                </div>
              @endif
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
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 600px;">
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

            <div class="form-group mb-3">
              <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700; color: #1e293b;">
                Update Photo <small class="text-muted font-weight-normal">(Leave empty to retain current)</small>
              </label>
              <input type="file" name="image" id="edit_dish_image_input" class="form-control-custom" accept="image/jpeg,image/png,image/jpg,image/webp,image/gif">
              <small class="form-text text-muted" style="font-size: 0.74rem;">Formats: JPG, PNG, WEBP, GIF (Max: 5MB)</small>

              <div class="upload-preview-box" id="edit_dish_preview_wrap" style="display: none;">
                <img id="edit_dish_image_preview" src="" alt="Food Image Preview">
              </div>
            </div>

            <!-- Dish Addon Mapping Section -->
            <div class="form-group mb-2">
              <div class="d-flex align-items-center justify-content-between mb-1">
                <label class="form-label font-weight-bold mb-0" style="font-size: 0.82rem; font-weight: 700; color: #1e293b;">
                  <i class="fa-solid fa-puzzle-piece text-primary me-1"></i> Map Dish Addons <small class="text-muted font-weight-normal">(Optional)</small>
                </label>
                <span class="badge font-weight-bold" id="edit_addon_count_badge" style="background: #fff3ed; color: #ff5e14; border: 1px solid #ffdecb; font-size: 0.72rem; padding: 3px 8px; border-radius: 10px;">
                  0 Selected
                </span>
              </div>

              @if(isset($addons) && count($addons) > 0)
                <!-- Search Filter for Addons -->
                <div class="input-group input-group-sm mb-2">
                  <span class="input-group-text bg-light border-end-0" style="border-radius: 8px 0 0 8px;">
                    <i class="fa-solid fa-magnifying-glass text-muted" style="font-size: 0.8rem;"></i>
                  </span>
                  <input type="text" class="form-control form-control-sm border-start-0 addon-search-filter" 
                         data-target="#edit_addon_list" 
                         placeholder="Search addons by name or price..." 
                         style="border-radius: 0 8px 8px 0; font-size: 0.82rem;">
                </div>

                <!-- Quick Action Select/Clear -->
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <small class="text-muted" style="font-size: 0.72rem;">Check addons to map them with this dish:</small>
                  <div>
                    <a href="javascript:void(0)" class="text-primary small fw-bold me-2 select-all-addons" data-target="#edit_addon_list" style="font-size: 0.72rem; text-decoration: none;">Select All</a>
                    <a href="javascript:void(0)" class="text-muted small fw-bold deselect-all-addons" data-target="#edit_addon_list" style="font-size: 0.72rem; text-decoration: none;">Clear</a>
                  </div>
                </div>

                <!-- Scrollable Addons Checkbox List -->
                <div class="addon-mapping-list border rounded p-2" id="edit_addon_list" style="max-height: 180px; overflow-y: auto; background: #f8fafc;">
                  @foreach($addons as $addon)
                  <label class="addon-item-row p-2 mb-1 rounded bg-white border" 
                         for="edit_addon_{{ $addon->id }}" 
                         data-name="{{ strtolower($addon->name) }}"
                         data-price="{{ $addon->price }}">
                    <div class="d-flex align-items-center gap-2">
                      <input type="checkbox" name="addon_ids[]" value="{{ $addon->id }}" 
                             class="form-check-input addon-checkbox mt-0" 
                             id="edit_addon_{{ $addon->id }}"
                             data-badge="#edit_addon_count_badge"
                             data-container="#edit_addon_list"
                             style="cursor: pointer; width: 16px; height: 16px;">
                      
                      @if($addon->food_type == 'NON-VEG')
                        <span class="badge" style="background:#fef2f2; color:#dc2626; border:1px solid #fecaca; font-size:0.65rem; padding: 2px 5px;">🔴 Non-Veg</span>
                      @else
                        <span class="badge" style="background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; font-size:0.65rem; padding: 2px 5px;">🟢 Veg</span>
                      @endif
                      
                      <span class="addon-name-text fw-bold text-dark" style="font-size: 0.82rem;">{{ $addon->name }}</span>
                    </div>
                    <span class="badge bg-light text-dark font-monospace fw-bold" style="font-size: 0.8rem; border: 1px solid #e2e8f0;">
                      +₹{{ number_format($addon->price, 2) }}
                    </span>
                  </label>
                  @endforeach
                  
                  <div class="no-addon-search-match text-center py-2 text-muted small" style="display: none;">
                    <i class="fa-solid fa-magnifying-glass me-1 opacity-50"></i> No matching addons found
                  </div>
                </div>
              @else
                <div class="alert alert-light border text-center py-3 mb-0" style="border-radius: 10px; background: #f8fafc;">
                  <i class="fa-solid fa-puzzle-piece text-muted fa-2x mb-2 opacity-50"></i>
                  <p class="mb-1 text-dark fw-bold" style="font-size: 0.82rem;">No Dish Addons Available</p>
                  <p class="small text-muted mb-2" style="font-size: 0.74rem;">Create toppings, dips, cheese, or sides in Addon Master.</p>
                  <a href="{{ route('addon.index') }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill fw-bold" style="font-size: 0.75rem;">
                    <i class="fa-solid fa-plus me-1"></i> Open Addon Master
                  </a>
                </div>
              @endif
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
      // Helper function to update count badge
      function updateAddonCount(containerSelector, badgeSelector) {
        let count = $(containerSelector).find('.addon-checkbox:checked').length;
        $(badgeSelector).text(count + ' Selected');
      }

      // Live Checkbox change event
      $(document).on('change', '.addon-checkbox', function() {
        let badgeSelector = $(this).attr('data-badge') || $(this).data('badge');
        let containerSelector = $(this).attr('data-container') || $(this).data('container');
        if (badgeSelector && containerSelector) {
          updateAddonCount(containerSelector, badgeSelector);
        }
      });

      // Live Addon Search filtering (instant on type, paste, clear, keyup)
      $(document).on('input keyup search change paste', '.addon-search-filter', function() {
        let targetSelector = $(this).attr('data-target') || $(this).data('target');
        let query = ($(this).val() || '').toLowerCase().trim();
        let matchedCount = 0;

        $(targetSelector).find('.addon-item-row').each(function() {
          let name = String($(this).attr('data-name') || $(this).find('.addon-name-text').text() || '').toLowerCase();
          let price = String($(this).attr('data-price') || '').toLowerCase();
          let rowText = $(this).text().toLowerCase();

          if (!query || name.includes(query) || price.includes(query) || rowText.includes(query)) {
            $(this).removeClass('is-hidden-addon');
            matchedCount++;
          } else {
            $(this).addClass('is-hidden-addon');
          }
        });

        if (matchedCount === 0 && query.length > 0) {
          $(targetSelector).find('.no-addon-search-match').removeClass('is-hidden-addon').show();
        } else {
          $(targetSelector).find('.no-addon-search-match').addClass('is-hidden-addon').hide();
        }
      });

      // Select All Addons (only visible ones)
      $(document).on('click', '.select-all-addons', function(e) {
        e.preventDefault();
        let targetSelector = $(this).attr('data-target') || $(this).data('target');
        $(targetSelector).find('.addon-item-row:not(.is-hidden-addon) .addon-checkbox').prop('checked', true);
        
        let badgeSelector = targetSelector === '#add_addon_list' ? '#add_addon_count_badge' : '#edit_addon_count_badge';
        updateAddonCount(targetSelector, badgeSelector);
      });

      // Deselect All / Clear Addons
      $(document).on('click', '.deselect-all-addons', function(e) {
        e.preventDefault();
        let targetSelector = $(this).attr('data-target') || $(this).data('target');
        $(targetSelector).find('.addon-checkbox').prop('checked', false);
        
        let badgeSelector = targetSelector === '#add_addon_list' ? '#add_addon_count_badge' : '#edit_addon_count_badge';
        updateAddonCount(targetSelector, badgeSelector);
      });

      // Reset Add Product Modal when opened
      $('#addProductModal').on('show.bs.modal', function() {
        $('#add_addon_list .addon-checkbox').prop('checked', false);
        $('#add_addon_list .addon-item-row').removeClass('is-hidden-addon');
        $('#add_addon_list .no-addon-search-match').addClass('is-hidden-addon').hide();
        $('.addon-search-filter[data-target="#add_addon_list"]').val('');
        updateAddonCount('#add_addon_list', '#add_addon_count_badge');
        $('#add_dish_preview_wrap').hide();
      });

      // 1. Live Client-Side Dishes Search Filter
      $(document).on('input keyup search change paste', '#dishSearchInput', function() {
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
          const name = String($(this).attr('data-name') || $(this).find('.product-title-text').text() || '').toLowerCase();
          const price = String($(this).attr('data-price') || '').toLowerCase();
          const type = String($(this).attr('data-type') || '').toLowerCase();
          const status = String($(this).attr('data-status') || '').toLowerCase();
          const cardText = $(this).text().toLowerCase();

          const matchesQuery = !query || name.includes(query) || price.includes(query) || cardText.includes(query);
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
            $(this).removeClass('is-hidden-dish');
          } else {
            $(this).addClass('is-hidden-dish');
          }
        });
      }

      // 3. Edit Modal Population with Addon Mapping
      $('.edit-btn').on('click', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let name = $(this).data('name');
        let price = $(this).data('price');
        let type = $(this).data('type');
        let image = $(this).data('image');
        let addonIdsRaw = String($(this).data('addons') || '');
        let mappedAddonIds = addonIdsRaw ? addonIdsRaw.split(',').map(function(s) { return s.trim(); }) : [];

        $('#edit_id').val(id);
        $('#edit_name').val(name);
        $('#edit_price').val(price);
        $('#edit_food_type').val(type);

        // Pre-check mapped addons for this dish
        $('#edit_addon_list .addon-checkbox').each(function() {
          let val = String($(this).val());
          $(this).prop('checked', mappedAddonIds.includes(val));
        });

        // Reset search filter and item visibility
        $('#edit_addon_list .addon-item-row').removeClass('is-hidden-addon');
        $('#edit_addon_list .no-addon-search-match').addClass('is-hidden-addon').hide();
        $('.addon-search-filter[data-target="#edit_addon_list"]').val('');
        updateAddonCount('#edit_addon_list', '#edit_addon_count_badge');

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