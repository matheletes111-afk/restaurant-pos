<!DOCTYPE html>
<html lang="en">
<head>
  <title>Manage Categories • Bill&Bite POS</title>
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
      --card-bg: #ffffff;
      --border-color: #e2e8f0;
      --text-main: #1e293b;
      --text-muted: #64748b;
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
      margin-bottom: 24px;
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
      gap: 16px;
      flex: 1 1 auto;
      min-width: 240px;
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
      min-width: 220px;
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
        justify-content: space-between;
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
      .btn-pos-primary {
        width: 100%;
        height: 44px;
      }
    }

    /* ===================================================
       CATEGORY CARDS GRID (RESPONSIVE 100%)
       =================================================== */
    .category-grid-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    @media (max-width: 576px) {
      .category-grid-container {
        grid-template-columns: 1fr;
        gap: 16px;
      }
    }

    .luxury-category-card {
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

    /* Luxury Category Card */
    .luxury-category-card {
      background: #ffffff;
      border-radius: var(--radius-lg);
      border: 1px solid #e2e8f0;
      box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
      position: relative;
    }

    .luxury-category-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 20px 40px -10px rgba(255, 94, 20, 0.14), 0 6px 16px rgba(15, 23, 42, 0.04);
      border-color: rgba(255, 94, 20, 0.35);
    }

    .card-media-wrap {
      position: relative;
      height: 185px;
      background: #f8fafc;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    @media (max-width: 576px) {
      .card-media-wrap {
        height: 200px;
      }
    }

    .card-media-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .luxury-category-card:hover .card-media-img {
      transform: scale(1.08);
    }

    .card-media-overlay {
      position: absolute;
      inset: 0;
      background: linear-gradient(180deg, rgba(15, 23, 42, 0.05) 0%, rgba(15, 23, 42, 0.38) 100%);
      pointer-events: none;
      z-index: 1;
    }

    .card-media-placeholder {
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

    .card-media-placeholder::before {
      content: '';
      position: absolute;
      inset: 0;
      background-image: radial-gradient(circle at 50% 50%, rgba(255, 94, 20, 0.25) 0%, transparent 70%);
    }

    .placeholder-initial-circle {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: var(--primary-gradient);
      color: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Outfit', sans-serif;
      font-size: 1.65rem;
      font-weight: 800;
      box-shadow: 0 8px 20px rgba(255, 94, 20, 0.4);
      z-index: 2;
    }

    .category-id-badge {
      position: absolute;
      top: 12px;
      left: 12px;
      background: rgba(15, 23, 42, 0.75);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      color: #ffffff;
      font-size: 0.72rem;
      font-weight: 700;
      padding: 4px 10px;
      border-radius: 20px;
      border: 1px solid rgba(255, 255, 255, 0.18);
      z-index: 2;
      display: inline-flex;
      align-items: center;
      gap: 4px;
    }

    .category-dishes-badge {
      position: absolute;
      top: 12px;
      right: 12px;
      background: rgba(255, 255, 255, 0.94);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      color: #0f172a;
      font-size: 0.74rem;
      font-weight: 800;
      padding: 4px 11px;
      border-radius: 20px;
      box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
      z-index: 2;
      display: inline-flex;
      align-items: center;
      border: 1px solid rgba(255, 255, 255, 0.8);
    }

    .category-dishes-badge i {
      color: var(--primary);
    }

    .card-content-area {
      padding: 20px 22px;
      display: flex;
      flex-direction: column;
      flex: 1;
      justify-content: space-between;
    }

    .category-title-text {
      font-family: 'Outfit', sans-serif;
      font-size: 1.25rem;
      font-weight: 800;
      color: var(--dark-slate);
      margin: 0 0 8px 0;
      line-height: 1.3;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      letter-spacing: -0.01em;
    }

    .category-meta-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      margin-bottom: 4px;
      font-size: 0.78rem;
    }

    .category-catalog-tag {
      color: #10b981;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      background: rgba(16, 185, 129, 0.08);
      padding: 3px 8px;
      border-radius: 6px;
    }

    .category-quick-info {
      color: #64748b;
      font-weight: 600;
    }

    /* Action Buttons in Card */
    .card-actions-dock {
      display: flex;
      align-items: center;
      gap: 8px;
      padding-top: 16px;
      border-top: 1px solid #f1f5f9;
      margin-top: 14px;
      position: relative;
      z-index: 10;
    }

    /* Premium Button-Type Design for Manage Dishes */
    .btn-card-dishes {
      flex: 1;
      min-width: 0;
      height: 42px;
      padding: 0 14px;
      background: var(--primary-gradient);
      color: #ffffff !important;
      font-size: clamp(0.78rem, 1.5vw, 0.88rem);
      font-weight: 700;
      letter-spacing: 0.01em;
      border: none;
      border-radius: 12px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      text-decoration: none;
      box-shadow: 0 4px 14px rgba(255, 94, 20, 0.28);
      transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
      position: relative;
      cursor: pointer;
    }

    .btn-card-dishes .dishes-btn-text {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      display: inline-block;
    }

    .btn-card-dishes i {
      font-size: 0.82rem;
      flex-shrink: 0;
    }

    .btn-card-dishes i.fa-arrow-right {
      font-size: 0.74rem;
      transition: transform 0.2s ease;
      opacity: 0.9;
    }

    .btn-card-dishes:hover {
      background: linear-gradient(135deg, #e04a08 0%, #ff6b2b 100%);
      transform: translateY(-2px);
      box-shadow: 0 8px 22px rgba(255, 94, 20, 0.4);
      color: #ffffff !important;
    }

    .btn-card-dishes:hover i.fa-arrow-right {
      transform: translateX(4px);
    }

    .btn-card-dishes:active {
      transform: translateY(0);
      box-shadow: 0 2px 8px rgba(255, 94, 20, 0.25);
    }

    .card-icon-actions-group {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      flex-shrink: 0;
    }

    .btn-card-icon {
      width: 40px;
      height: 40px;
      border-radius: 12px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 0.92rem;
      border: 1.5px solid transparent;
      transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
      cursor: pointer;
      text-decoration: none;
      position: relative;
      z-index: 10;
    }

    @media (max-width: 400px) {
      .card-actions-dock {
        flex-wrap: wrap;
      }
      .btn-card-dishes {
        width: 100%;
        flex: auto;
      }
      .card-icon-actions-group {
        width: 100%;
        justify-content: flex-end;
      }
      .btn-card-icon {
        flex: 1;
      }
    }

    .btn-card-icon i {
      pointer-events: none;
    }

    .btn-card-icon.edit-icon {
      color: #0284c7;
      border-color: #e0f2fe;
      background: #f0f9ff;
    }

    .btn-card-icon.edit-icon:hover {
      background: #0284c7;
      color: #ffffff;
      border-color: #0284c7;
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(2, 132, 199, 0.25);
    }

    .btn-card-icon.delete-icon {
      color: #e11d48;
      border-color: #ffe4e6;
      background: #fff1f2;
    }

    .btn-card-icon.delete-icon:hover {
      background: #e11d48;
      color: #ffffff;
      border-color: #e11d48;
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(225, 29, 72, 0.25);
    }

    /* ===================================================
       EMPTY STATE
       =================================================== */
    .empty-category-deck {
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
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>

  @include('includes.sidebar')

  <div class="pc-container">
    <div class="pc-content">

      <!-- ===================================================
           PAGE HEADER & ACTIONS CLUSTER (100% RESPONSIVE)
           =================================================== -->
      <div class="menu-header-card">
        <div class="header-left-cluster">
          <div class="header-icon-badge">
            <i class="fa-solid fa-layer-group"></i>
          </div>
          <div class="header-title-meta">
            <span class="header-eyebrow-tag">Menu &amp; Category Architecture</span>
            <h1 class="header-main-title">Manage Menu Categories</h1>
            <p class="header-sub-text">Create and structure your digital menu, manage visual thumbnails &amp; dishes.</p>
          </div>
        </div>

        <div class="header-right-actions">
          <!-- Real-Time Client Search Filter -->
          <div class="search-box-wrap">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="categorySearchInput" class="search-box-input" placeholder="Search categories...">
          </div>

          <!-- Subscription Plan Quota Pill -->
          @if(isset($plan_details) && isset($plan_details->category_number))
          <div class="plan-usage-pill" title="Category Quota">
            <i class="fa-solid fa-chart-pie"></i>
            <span>{{ count($data ?? []) }} / {{ $plan_details->category_number }} Used</span>
          </div>
          @endif

          @if(auth()->user()->hasPermission('menu_master', 'add'))
          <!-- Bulk Upload Categories Button -->
          <button type="button" class="btn-pos-secondary" data-toggle="modal" data-target="#bulkUploadCategoryModal" data-bs-toggle="modal" data-bs-target="#bulkUploadCategoryModal" title="Bulk Upload Categories from Excel">
            <i class="fa-solid fa-file-excel text-success"></i>
            <span>Bulk Categories</span>
          </button>

          <!-- Bulk Upload Dishes Button -->
          <button type="button" class="btn-pos-secondary" data-toggle="modal" data-target="#bulkUploadDishesModal" data-bs-toggle="modal" data-bs-target="#bulkUploadDishesModal" title="Bulk Upload Dishes across Categories">
            <i class="fa-solid fa-cloud-arrow-up text-primary"></i>
            <span>Bulk Dishes</span>
          </button>
          @endif

          <!-- Add Category Action Button -->
          @if(
              auth()->user()->hasPermission('menu_master', 'add')
              && isset($plan_details)
              && isset($plan_details->category_number)
              && count($data ?? []) < $plan_details->category_number
          )
          <button type="button" class="btn-pos-primary" data-toggle="modal" data-target="#addCategoryModal" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="fa-solid fa-plus"></i>
            <span>Add Category</span>
          </button>
          @endif
        </div>
      </div>

      <!-- Flash Messages -->
      @include('includes.message')

      <!-- ===================================================
           CATEGORY CARDS GRID
           =================================================== -->
      <div class="category-grid-container" id="categoryGrid">
        @forelse(@$data as $value)
        <div class="luxury-category-card category-item-card" data-name="{{ strtolower($value->name) }}">
          
          <!-- Media Thumbnail Banner -->
          <div class="card-media-wrap">
            <span class="category-id-badge">
              <i class="fa-solid fa-tag"></i> #CAT-{{ $value->id }}
            </span>

            <span class="category-dishes-badge">
              <i class="fa-solid fa-utensils"></i>
              <span>{{ $value->subcategories_count ?? count($value->subcategories ?? []) }} {{ ($value->subcategories_count ?? count($value->subcategories ?? [])) == 1 ? 'Dish' : 'Dishes' }}</span>
            </span>
            
            @if($value->image)
              <img src="{{ URL::to('storage/category') }}/{{ @$value->image }}" alt="{{ @$value->name }}" class="card-media-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
              <div class="card-media-placeholder" style="display: none;">
                <div class="placeholder-initial-circle">{{ strtoupper(substr($value->name, 0, 1)) }}</div>
              </div>
            @else
              <div class="card-media-placeholder">
                <div class="placeholder-initial-circle">{{ strtoupper(substr($value->name, 0, 1)) }}</div>
              </div>
            @endif
            <div class="card-media-overlay"></div>
          </div>

          <!-- Content Details -->
          <div class="card-content-area">
            <div>
              <h3 class="category-title-text" title="{{ @$value->name }}">
                {{ @$value->name }}
              </h3>
              <div class="category-meta-row">
                <span class="category-catalog-tag">
                  <i class="fa-solid fa-circle-check me-1"></i> Active Category
                </span>
                <span class="category-quick-info">
                  {{ ($value->subcategories_count ?? count($value->subcategories ?? [])) > 0 ? (($value->subcategories_count ?? count($value->subcategories ?? [])) . ' items') : '0 items' }}
                </span>
              </div>
            </div>

            <!-- Action Buttons Dock -->
            <div class="card-actions-dock">
              <!-- View / Manage Dishes Button -->
              <a href="{{ route('manage.subcategory.category', @$value->id) }}" class="btn-card-dishes" title="Manage Dishes in {{ $value->name }}">
                <i class="fa-solid fa-utensils"></i>
                <span class="dishes-btn-text">Dishes</span>
                <i class="fa-solid fa-arrow-right"></i>
              </a>

              <div class="card-icon-actions-group">
                <!-- Edit Category -->
                @if(auth()->user()->hasPermission('menu_master', 'edit'))
                <button type="button"
                        class="btn-card-icon edit-icon edit-btn"
                        data-id="{{ $value->id }}"
                        data-name="{{ $value->name }}"
                        data-image="{{ $value->image }}"
                        title="Edit Category">
                  <i class="fa-solid fa-pen-to-square"></i>
                </button>
                @endif

                <!-- Delete Category -->
                @if(auth()->user()->hasPermission('menu_master', 'delete'))
                <a href="{{ route('manage.category.delete', @$value->id) }}"
                    class="btn-card-icon delete-icon btn-delete-category"
                    title="Delete Category"
                    onclick="return confirm('Are you sure you want to delete \'{{ addslashes($value->name) }}\'? All linked sub-dishes will also be deleted.')">
                  <i class="fa-solid fa-trash"></i>
                </a>
                @endif
              </div>
            </div>

          </div><!-- /.card-content-area -->

        </div><!-- /.luxury-category-card -->
        @empty
        <!-- Empty State -->
        <div class="empty-category-deck">
          <div class="empty-icon-wrap">
            <i class="fa-solid fa-layer-group"></i>
          </div>
          <h3 class="empty-title">No Categories Found</h3>
          <p class="empty-desc">Get started by creating your first food &amp; beverage menu category or bulk upload categories via Excel.</p>
          
          <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
            @if(
                auth()->user()->hasPermission('menu_master', 'add')
                && isset($plan_details)
                && isset($plan_details->category_number)
                && count($data ?? []) < $plan_details->category_number
            )
            <button type="button" class="btn-pos-primary" data-toggle="modal" data-target="#addCategoryModal" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
              <i class="fa-solid fa-plus"></i>
              <span>Create Category</span>
            </button>
            @endif

            @if(auth()->user()->hasPermission('menu_master', 'add'))
            <button type="button" class="btn-pos-secondary" data-toggle="modal" data-target="#bulkUploadCategoryModal" data-bs-toggle="modal" data-bs-target="#bulkUploadCategoryModal">
              <i class="fa-solid fa-file-excel text-success"></i>
              <span>Bulk Upload Categories</span>
            </button>
            @endif
          </div>
        </div>
        @endforelse
      </div>

    </div><!-- /.pc-content -->
  </div><!-- /.pc-container -->


  <!-- ===================================================
       MODAL: ADD CATEGORY
       =================================================== -->
  <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form method="POST" action="{{ route('manage.category.insert') }}" enctype="multipart/form-data" style="width: 100%;">
        @csrf
        <div class="modal-content modal-pos-content">
          
          <div class="modal-pos-header">
            <h5 class="modal-pos-title" id="addCategoryModalLabel">
              <i class="fa-solid fa-circle-plus text-primary"></i>
              <span>Add New Menu Category</span>
            </h5>
            <button type="button" class="modal-btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>

          <div class="modal-pos-body">
            <div class="form-group mb-3">
              <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700; color: #1e293b;">
                Category Name <span class="text-danger">*</span>
              </label>
              <input type="text" name="name" class="form-control-custom" required placeholder="e.g. Starters, Main Course, Beverages" autocomplete="off">
            </div>

            <div class="form-group mb-2">
              <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700; color: #1e293b;">
                Cover Image Thumbnail <small class="text-muted font-weight-normal">(Optional)</small>
              </label>
              <input type="file" name="image" id="add_image_input" class="form-control-custom" accept="image/jpeg,image/png,image/jpg,image/webp,image/gif">
              <small class="form-text text-muted" style="font-size: 0.74rem;">Formats: JPG, PNG, WEBP, GIF (Max: 5MB)</small>

              <div class="upload-preview-box" id="add_preview_wrap" style="display: none;">
                <img id="add_image_preview" src="" alt="Selected Preview">
              </div>
            </div>
          </div>

          <div class="modal-pos-footer">
            <button type="button" class="btn-modal-cancel" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn-pos-primary">
              <i class="fa-solid fa-check"></i>
              <span>Save Category</span>
            </button>
          </div>

        </div>
      </form>
    </div>
  </div>


  <!-- ===================================================
       MODAL: EDIT CATEGORY
       =================================================== -->
  <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form method="POST" action="{{ route('manage.category.update') }}" enctype="multipart/form-data" style="width: 100%;">
        @csrf
        <input type="hidden" name="id" id="edit_id">

        <div class="modal-content modal-pos-content">
          
          <div class="modal-pos-header">
            <h5 class="modal-pos-title" id="editCategoryModalLabel">
              <i class="fa-solid fa-pen-to-square text-primary"></i>
              <span>Edit Menu Category</span>
            </h5>
            <button type="button" class="modal-btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>

          <div class="modal-pos-body">
            <div class="form-group mb-3">
              <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700; color: #1e293b;">
                Category Name <span class="text-danger">*</span>
              </label>
              <input type="text" name="name" id="edit_name" class="form-control-custom" required placeholder="Category name" autocomplete="off">
            </div>

            <div class="form-group mb-2">
              <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700; color: #1e293b;">
                Update Image Thumbnail <small class="text-muted font-weight-normal">(Leave empty to retain current)</small>
              </label>
              <input type="file" name="image" id="edit_image_input" class="form-control-custom" accept="image/jpeg,image/png,image/jpg,image/webp,image/gif">
              <small class="form-text text-muted" style="font-size: 0.74rem;">Formats: JPG, PNG, WEBP, GIF (Max: 5MB)</small>

              <div class="upload-preview-box" id="edit_preview_wrap" style="display: none;">
                <img id="edit_image_preview" src="" alt="Category Image Preview">
              </div>
            </div>
          </div>

          <div class="modal-pos-footer">
            <button type="button" class="btn-modal-cancel" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn-pos-primary">
              <i class="fa-solid fa-check"></i>
              <span>Update Category</span>
            </button>
          </div>

        </div>
      </form>
    </div>
  </div>


  <!-- ===================================================
       MODAL: BULK UPLOAD CATEGORIES
       =================================================== -->
  <div class="modal fade" id="bulkUploadCategoryModal" tabindex="-1" aria-labelledby="bulkUploadCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form method="POST" action="{{ route('manage.category.bulk.upload') }}" enctype="multipart/form-data" style="width: 100%;">
        @csrf
        <div class="modal-content modal-pos-content">
          
          <div class="modal-pos-header">
            <h5 class="modal-pos-title" id="bulkUploadCategoryModalLabel">
              <i class="fa-solid fa-file-excel text-success"></i>
              <span>Bulk Upload Categories</span>
            </h5>
            <button type="button" class="modal-btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>

          <div class="modal-pos-body">
            <div class="alert alert-info py-2 px-3 mb-3" style="font-size: 0.82rem; border-radius: 10px; background: #f0f9ff; border: 1px solid #bae6fd; color: #0369a1;">
              <i class="fa-solid fa-circle-info me-1"></i>
              Upload an Excel (.xlsx, .xls) or CSV file with <strong>Category Name</strong> only. Categories will strictly be inserted within your subscription plan limit.
            </div>

            <div class="form-group mb-3">
              <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700; color: #1e293b;">
                Select Excel / CSV File <span class="text-danger">*</span>
              </label>
              <input type="file" name="bulk_file" class="form-control-custom" accept=".xlsx, .xls, .csv" required>
              <small class="form-text text-muted" style="font-size: 0.74rem;">Supported formats: .xlsx, .xls, .csv (Max: 5MB)</small>
            </div>

            <div class="mt-3">
              <h6 style="font-size: 0.82rem; font-weight: 700; color: #1e293b; margin-bottom: 8px;">
                <i class="fa-solid fa-table me-1 text-primary"></i> Required Excel Format (1 Column Only):
              </h6>
              <div class="table-responsive" style="border-radius: 10px; overflow-x: auto; border: 1px solid #e2e8f0; width: 100%;">
                <table class="table table-bordered table-sm mb-0" style="font-size: 0.82rem; width: 100%;">
                  <thead style="background: #f8fafc; color: #475569;">
                    <tr>
                      <th style="padding: 8px 12px;">Category Name</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td style="padding: 8px 12px;">Starters</td>
                    </tr>
                    <tr>
                      <td style="padding: 8px 12px;">Main Course</td>
                    </tr>
                    <tr>
                      <td style="padding: 8px 12px;">Beverages</td>
                    </tr>
                    <tr>
                      <td style="padding: 8px 12px;">Desserts</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="modal-pos-footer" style="justify-content: space-between;">
            <a href="{{ route('manage.category.template') }}" class="btn btn-outline-info" style="border-radius: 30px; font-weight: 700; font-size: 0.84rem;">
              <i class="fa-solid fa-download me-1"></i> Download Template
            </a>

            <div style="display: flex; gap: 8px;">
              <button type="button" class="btn-modal-cancel" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn-pos-primary">
                <i class="fa-solid fa-upload"></i>
                <span>Upload Categories</span>
              </button>
            </div>
          </div>

        </div>
      </form>
    </div>
  </div>


  <!-- ===================================================
       MODAL: BULK UPLOAD DISHES
       =================================================== -->
  <div class="modal fade" id="bulkUploadDishesModal" tabindex="-1" aria-labelledby="bulkUploadDishesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 580px;">
      <form method="POST" action="{{ route('manage.subcategory.category.bulk.upload') }}" enctype="multipart/form-data" style="width: 100%;">
        @csrf
        <div class="modal-content modal-pos-content">
          
          <div class="modal-pos-header">
            <h5 class="modal-pos-title" id="bulkUploadDishesModalLabel">
              <i class="fa-solid fa-cloud-arrow-up text-primary"></i>
              <span>Bulk Upload Dishes</span>
            </h5>
            <button type="button" class="modal-btn-close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>

          <div class="modal-pos-body">
            <div class="alert alert-warning py-2 px-3 mb-3" style="font-size: 0.82rem; border-radius: 10px; background: #fffbeb; border: 1px solid #fde68a; color: #92400e;">
              <i class="fa-solid fa-triangle-exclamation me-1"></i>
              <strong>Category Match Rule:</strong> The <strong>Category</strong> column must match an existing category in your restaurant. If any category does not match, the entire upload will be aborted without inserting any dishes.
            </div>

            <div class="form-group mb-3">
              <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700; color: #1e293b;">
                Select Excel / CSV File <span class="text-danger">*</span>
              </label>
              <input type="file" name="bulk_file" class="form-control-custom" accept=".xlsx, .xls, .csv" required>
              <small class="form-text text-muted" style="font-size: 0.74rem;">Supported formats: .xlsx, .xls, .csv (Max: 5MB)</small>
            </div>

            <div class="mt-3">
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
                      <td style="padding: 8px 12px;">Main Course</td>
                    </tr>
                    <tr>
                      <td style="padding: 8px 12px;">Chicken Biryani</td>
                      <td style="padding: 8px 12px;">320.00</td>
                      <td style="padding: 8px 12px;"><span class="badge bg-danger text-white">NON-VEG</span></td>
                      <td style="padding: 8px 12px;">Main Course</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="modal-pos-footer" style="justify-content: space-between;">
            <a href="{{ route('manage.subcategory.category.template') }}" class="btn btn-outline-info" style="border-radius: 30px; font-weight: 700; font-size: 0.84rem;">
              <i class="fa-solid fa-download me-1"></i> Download Template
            </a>

            <div style="display: flex; gap: 8px;">
              <button type="button" class="btn-modal-cancel" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn-pos-primary">
                <i class="fa-solid fa-upload"></i>
                <span>Upload Dishes</span>
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
      // 1. Live Client-Side Category Search
      $('#categorySearchInput').on('keyup', function() {
        const query = $(this).val().toLowerCase().trim();
        $('.category-item-card').each(function() {
          const name = $(this).data('name') || '';
          if (name.includes(query)) {
            $(this).show();
          } else {
            $(this).hide();
          }
        });
      });

      // 2. Edit Modal Population
      $('.edit-btn').on('click', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let name = $(this).data('name');
        let image = $(this).data('image');

        $('#edit_id').val(id);
        $('#edit_name').val(name);

        if (image) {
          $('#edit_image_preview').attr('src', '{{ URL::to("storage/category") }}/' + image);
          $('#edit_preview_wrap').show();
        } else {
          $('#edit_preview_wrap').hide();
        }

        // Support both Bootstrap 4 & 5 modal triggers
        if (typeof $('#editCategoryModal').modal === 'function') {
          $('#editCategoryModal').modal('show');
        }
      });

      // 3. Live Image Preview for Add Modal
      $('#add_image_input').on('change', function() {
        const file = this.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function(e) {
            $('#add_image_preview').attr('src', e.target.result);
            $('#add_preview_wrap').show();
          };
          reader.readAsDataURL(file);
        } else {
          $('#add_preview_wrap').hide();
        }
      });

      // 4. Live Image Preview for Edit Modal
      $('#edit_image_input').on('change', function() {
        const file = this.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function(e) {
            $('#edit_image_preview').attr('src', e.target.result);
            $('#edit_preview_wrap').show();
          };
          reader.readAsDataURL(file);
        }
      });
    });
  </script>

</body>
</html>