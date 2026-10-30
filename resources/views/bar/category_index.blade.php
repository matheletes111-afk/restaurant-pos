<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bar Category Management • Bill&amp;Bite POS</title>
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
      --bar-primary: #8b5cf6;
      --bar-primary-hover: #7c3aed;
      --bar-gradient: linear-gradient(135deg, #7c3aed 0%, #a855f7 50%, #ec4899 100%);
      --bar-accent: #f59e0b;
      --bar-dark: #0f172a;
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
      background-color: #f6f7fb;
      color: var(--text-main);
      overflow-x: hidden;
    }

    .pc-content {
      padding: 24px 28px;
    }

    @media (max-width: 768px) {
      .pc-content {
        padding: 16px 14px;
      }
    }

    /* ===================================================
       HEADER & CONTROLS DECK
       =================================================== */
    .bar-header-card {
      background: #ffffff;
      border-radius: var(--radius-lg);
      padding: 22px 26px;
      margin-bottom: 24px;
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

    .header-title-meta {
      display: flex;
      flex-direction: column;
      min-width: 0;
    }

    .bar-eyebrow-tag {
      font-size: 0.72rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--bar-primary);
      margin-bottom: 3px;
      display: flex;
      align-items: center;
      gap: 6px;
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
      background: #f8fafc;
      border: 1.5px solid #e2e8f0;
      border-radius: 30px;
      padding: 9px 16px 9px 38px;
      font-size: 0.86rem;
      width: 100%;
      outline: none;
      transition: all 0.2s ease;
      font-weight: 500;
    }

    .search-box-input:focus {
      background: #ffffff;
      border-color: var(--bar-primary);
      box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.15);
    }

    .plan-usage-pill {
      background: #faf5ff;
      border: 1px solid #e9d5ff;
      border-radius: 30px;
      padding: 8px 16px;
      font-size: 0.8rem;
      font-weight: 700;
      color: #7c3aed;
      display: inline-flex;
      align-items: center;
      gap: 8px;
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
      text-decoration: none;
      cursor: pointer;
      white-space: nowrap;
    }

    .btn-bar-secondary:hover {
      border-color: var(--bar-primary);
      color: var(--bar-primary);
      background: #faf5ff;
      box-shadow: 0 4px 12px rgba(124, 58, 237, 0.1);
    }

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
      transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
      text-decoration: none;
      cursor: pointer;
      white-space: nowrap;
    }

    .btn-bar-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(124, 58, 237, 0.45);
      color: #ffffff;
    }

    /* ===================================================
       CATEGORY CARDS GRID
       =================================================== */
    .category-grid-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 22px;
    }

    .bar-category-card {
      background: #ffffff;
      border-radius: var(--radius-lg);
      border: 1px solid #f1f5f9;
      box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04);
      overflow: hidden;
      display: flex;
      flex-direction: column;
      transition: all 0.25s ease;
      position: relative;
    }

    .bar-category-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 28px rgba(124, 58, 237, 0.12);
      border-color: #ddd6fe;
    }

    .card-media-wrap {
      position: relative;
      width: 100%;
      height: 155px;
      background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
      overflow: hidden;
    }

    .card-media-img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.4s ease;
    }

    .bar-category-card:hover .card-media-img {
      transform: scale(1.05);
    }

    .card-media-placeholder {
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #2e1065 0%, #4c1d95 60%, #5b21b6 100%);
    }

    .placeholder-initial-circle {
      width: 56px;
      height: 56px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(8px);
      border: 2px solid rgba(255, 255, 255, 0.3);
      color: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      font-weight: 800;
      font-family: 'Outfit', sans-serif;
    }

    .card-media-overlay {
      position: absolute;
      inset: 0;
      background: linear-gradient(180deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.65) 100%);
    }

    .category-id-badge {
      position: absolute;
      top: 12px;
      left: 12px;
      background: rgba(15, 23, 42, 0.65);
      backdrop-filter: blur(6px);
      color: #ffffff;
      font-size: 0.68rem;
      font-weight: 700;
      padding: 3px 8px;
      border-radius: 6px;
      border: 1px solid rgba(255, 255, 255, 0.15);
      z-index: 2;
    }

    .category-dishes-badge {
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
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }

    .card-content-area {
      padding: 16px 18px 18px 18px;
      display: flex;
      flex-direction: column;
      flex: 1;
      justify-content: space-between;
      gap: 14px;
    }

    .category-title-text {
      font-family: 'Outfit', sans-serif;
      font-size: 1.15rem;
      font-weight: 800;
      color: var(--bar-dark);
      margin: 0 0 6px 0;
      line-height: 1.3;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .category-meta-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      flex-wrap: wrap;
    }

    .category-type-pill {
      font-size: 0.72rem;
      font-weight: 800;
      padding: 3px 8px;
      border-radius: 6px;
      background: #faf5ff;
      color: #7c3aed;
      border: 1px solid #e9d5ff;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }

    .category-quick-info {
      font-size: 0.78rem;
      color: #64748b;
      font-weight: 600;
    }

    .card-actions-dock {
      display: flex;
      align-items: center;
      gap: 8px;
      padding-top: 10px;
      border-top: 1px solid #f1f5f9;
    }

    .btn-card-dishes {
      flex: 1;
      background: #faf5ff;
      border: 1px solid #ddd6fe;
      color: #6d28d9;
      border-radius: 20px;
      padding: 7px 14px;
      font-size: 0.8rem;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      text-decoration: none;
      transition: all 0.2s ease;
    }

    .btn-card-dishes:hover {
      background: var(--bar-gradient);
      border-color: transparent;
      color: #ffffff;
      box-shadow: 0 4px 12px rgba(124, 58, 237, 0.28);
    }

    .card-icon-actions-group {
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .btn-card-icon {
      width: 34px;
      height: 34px;
      border-radius: 50%;
      border: 1px solid #e2e8f0;
      background: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.82rem;
      cursor: pointer;
      transition: all 0.2s ease;
      text-decoration: none;
    }

    .btn-card-icon.edit-icon {
      color: #475569;
    }

    .btn-card-icon.edit-icon:hover {
      background: #f1f5f9;
      color: var(--bar-primary);
      border-color: var(--bar-primary);
    }

    .btn-card-icon.delete-icon {
      color: #94a3b8;
    }

    .btn-card-icon.delete-icon:hover {
      background: #fef2f2;
      color: #ef4444;
      border-color: #fca5a5;
    }

    /* Empty state */
    .empty-category-deck {
      grid-column: 1 / -1;
      background: #ffffff;
      border-radius: var(--radius-lg);
      padding: 48px 24px;
      text-align: center;
      border: 1.5px dashed #cbd5e1;
    }

    .empty-icon-wrap {
      width: 72px;
      height: 72px;
      border-radius: 20px;
      background: #faf5ff;
      color: #7c3aed;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      margin-bottom: 16px;
    }

    /* Modal Styling */
    .modal-pos-content {
      border: none;
      border-radius: var(--radius-lg);
      overflow: hidden;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .modal-pos-header {
      background: #ffffff;
      padding: 18px 22px;
      border-bottom: 1px solid #e2e8f0;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .modal-pos-title {
      font-family: 'Outfit', sans-serif;
      font-weight: 800;
      font-size: 1.18rem;
      margin: 0;
      color: var(--bar-dark);
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .modal-btn-close {
      background: none;
      border: none;
      font-size: 1.1rem;
      color: #94a3b8;
      cursor: pointer;
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
           PAGE HEADER & ACTIONS CLUSTER
           =================================================== -->
      <div class="bar-header-card">
        <div class="header-left-cluster">
          <div class="bar-header-icon">
            <i class="fa-solid fa-martini-glass-citrus"></i>
          </div>
          <div class="header-title-meta">
            <span class="bar-eyebrow-tag">
              <i class="fa-solid fa-wine-bottle"></i> Bar Management &bull; Category Architecture
            </span>
            <h1 class="bar-main-title">Bar Category Management</h1>
            <p class="header-sub-text">Organize craft cocktails, draught beers, spirits, wines, and bar beverages.</p>
          </div>
        </div>

        <div class="header-right-actions">
          <!-- Real-Time Search Filter -->
          <div class="search-box-wrap">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="categorySearchInput" class="search-box-input" placeholder="Search bar categories...">
          </div>

          <!-- Subscription Plan Quota Pill -->
          @if(isset($plan_details) && isset($plan_details->category_number))
          <div class="plan-usage-pill" title="Establishment Category Limit">
            <i class="fa-solid fa-chart-pie"></i>
            <span>{{ $total_all_categories ?? count($data ?? []) }} / {{ $plan_details->category_number }} Categories</span>
          </div>
          @endif

          <!-- Bulk Upload Button -->
          <button type="button" class="btn-bar-secondary" data-bs-toggle="modal" data-bs-target="#bulkUploadCategoryModal" title="Bulk Upload Bar Categories">
            <i class="fa-solid fa-file-excel text-success"></i>
            <span>Bulk Categories</span>
          </button>

          <!-- Add Bar Category Action Button -->
          <button type="button" class="btn-bar-primary" data-bs-toggle="modal" data-bs-target="#addBarCategoryModal">
            <i class="fa-solid fa-plus"></i>
            <span>Add Bar Category</span>
          </button>
        </div>
      </div>

      <!-- Flash Messages -->
      @include('includes.message')

      <!-- ===================================================
           BAR CATEGORY CARDS GRID
           =================================================== -->
      <div class="category-grid-container" id="categoryGrid">
        @forelse(@$data as $value)
        <div class="bar-category-card category-item-card" data-name="{{ strtolower($value->name) }}">
          
          <!-- Media Thumbnail Banner -->
          <div class="card-media-wrap">
            <span class="category-id-badge">
              <i class="fa-solid fa-wine-glass"></i> #BAR-{{ $value->id }}
            </span>

            <span class="category-dishes-badge">
              <i class="fa-solid fa-cocktail"></i>
              <span>{{ $value->subcategories_count ?? count($value->subcategories ?? []) }} {{ ($value->subcategories_count ?? count($value->subcategories ?? [])) == 1 ? 'Item' : 'Items' }}</span>
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
                <span class="category-type-pill">
                  <i class="fa-solid fa-martini-glass"></i> Role: BAR
                </span>
                <span class="category-quick-info">
                  {{ ($value->subcategories_count ?? count($value->subcategories ?? [])) > 0 ? (($value->subcategories_count ?? count($value->subcategories ?? [])) . ' drinks/items') : '0 drinks' }}
                </span>
              </div>
            </div>

            <!-- Action Buttons Dock -->
            <div class="card-actions-dock">
              <!-- Manage Drinks/Dishes Button -->
              <a href="{{ route('bar.item.index', @$value->id) }}" class="btn-card-dishes" title="Manage Bar Items in {{ $value->name }}">
                <i class="fa-solid fa-wine-bottle"></i>
                <span>Bar Items</span>
                <i class="fa-solid fa-arrow-right"></i>
              </a>

              <div class="card-icon-actions-group">
                <!-- Edit Category -->
                <button type="button"
                        class="btn-card-icon edit-icon edit-bar-btn"
                        data-id="{{ $value->id }}"
                        data-name="{{ $value->name }}"
                        data-image="{{ $value->image }}"
                        title="Edit Bar Category">
                  <i class="fa-solid fa-pen-to-square"></i>
                </button>

                <!-- Delete Category -->
                <a href="{{ route('bar.category.delete', @$value->id) }}"
                    class="btn-card-icon delete-icon"
                    title="Delete Bar Category"
                    onclick="return confirm('Are you sure you want to delete \'{{ addslashes($value->name) }}\'? All linked drinks & items will also be archived.')">
                  <i class="fa-solid fa-trash"></i>
                </a>
              </div>
            </div>

          </div><!-- /.card-content-area -->

        </div><!-- /.bar-category-card -->
        @empty
        <!-- Empty State -->
        <div class="empty-category-deck">
          <div class="empty-icon-wrap">
            <i class="fa-solid fa-cocktail"></i>
          </div>
          <h3 style="font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 1.3rem; margin-bottom: 6px;">No Bar Categories Found</h3>
          <p style="color: #64748b; font-size: 0.88rem; max-width: 480px; margin: 0 auto 20px auto;">Get started by creating your first Bar category (e.g. Cocktails, Draught Beers, Single Malts, Mocktails) or upload via Excel.</p>
          
          <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
            <button type="button" class="btn-bar-primary" data-bs-toggle="modal" data-bs-target="#addBarCategoryModal">
              <i class="fa-solid fa-plus"></i>
              <span>Create Bar Category</span>
            </button>
            <button type="button" class="btn-bar-secondary" data-bs-toggle="modal" data-bs-target="#bulkUploadCategoryModal">
              <i class="fa-solid fa-file-excel text-success"></i>
              <span>Bulk Upload Categories</span>
            </button>
          </div>
        </div>
        @endforelse
      </div>

    </div><!-- /.pc-content -->
  </div><!-- /.pc-container -->


  <!-- ===================================================
       MODAL: ADD BAR CATEGORY (category_type = BAR)
       =================================================== -->
  <div class="modal fade" id="addBarCategoryModal" tabindex="-1" aria-labelledby="addBarCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form method="POST" action="{{ route('bar.category.insert') }}" enctype="multipart/form-data" style="width: 100%;">
        @csrf
        <input type="hidden" name="category_type" value="BAR">

        <div class="modal-content modal-pos-content">
          
          <div class="modal-pos-header">
            <h5 class="modal-pos-title" id="addBarCategoryModalLabel">
              <i class="fa-solid fa-circle-plus text-primary"></i>
              <span>Add New Bar Category</span>
            </h5>
            <button type="button" class="modal-btn-close" data-bs-dismiss="modal" aria-label="Close">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>

          <div class="modal-pos-body">
            <!-- Role / Category Type Indicator -->
            <div class="alert alert-light border d-flex align-items-center mb-3" style="border-radius: 12px; background: #faf5ff; border-color: #e9d5ff !important;">
              <i class="fa-solid fa-martini-glass text-primary me-2 fs-5"></i>
              <div style="font-size: 0.8rem; color: #581c87;">
                <strong>Category Type: BAR</strong> &bull; This category will be categorized under Bar Management.
              </div>
            </div>

            <div class="form-group mb-3">
              <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700; color: #1e293b;">
                Bar Category Name <span class="text-danger">*</span>
              </label>
              <input type="text" name="name" class="form-control-custom" required placeholder="e.g. Craft Cocktails, Draught Beers, Whiskey &amp; Spirits" autocomplete="off">
            </div>

            <div class="form-group mb-2">
              <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700; color: #1e293b;">
                Cover Image Thumbnail <small class="text-muted font-weight-normal">(Optional)</small>
              </label>
              <input type="file" name="image" id="add_bar_image_input" class="form-control-custom" accept="image/jpeg,image/png,image/jpg,image/webp,image/gif">
              <small class="form-text text-muted" style="font-size: 0.74rem;">Formats: JPG, PNG, WEBP, GIF (Max: 5MB)</small>

              <div class="upload-preview-box" id="add_bar_preview_wrap" style="display: none;">
                <img id="add_bar_image_preview" src="" alt="Selected Preview">
              </div>
            </div>
          </div>

          <div class="modal-pos-footer">
            <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn-bar-primary">
              <i class="fa-solid fa-check"></i>
              <span>Save Bar Category</span>
            </button>
          </div>

        </div>
      </form>
    </div>
  </div>


  <!-- ===================================================
       MODAL: EDIT BAR CATEGORY
       =================================================== -->
  <div class="modal fade" id="editBarCategoryModal" tabindex="-1" aria-labelledby="editBarCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form method="POST" action="{{ route('bar.category.update') }}" enctype="multipart/form-data" style="width: 100%;">
        @csrf
        <input type="hidden" name="id" id="edit_bar_id">
        <input type="hidden" name="category_type" value="BAR">

        <div class="modal-content modal-pos-content">
          
          <div class="modal-pos-header">
            <h5 class="modal-pos-title" id="editBarCategoryModalLabel">
              <i class="fa-solid fa-pen-to-square text-primary"></i>
              <span>Edit Bar Category</span>
            </h5>
            <button type="button" class="modal-btn-close" data-bs-dismiss="modal" aria-label="Close">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>

          <div class="modal-pos-body">
            <div class="form-group mb-3">
              <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700; color: #1e293b;">
                Bar Category Name <span class="text-danger">*</span>
              </label>
              <input type="text" name="name" id="edit_bar_name" class="form-control-custom" required autocomplete="off">
            </div>

            <div class="form-group mb-2">
              <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700; color: #1e293b;">
                Change Cover Image <small class="text-muted font-weight-normal">(Optional)</small>
              </label>
              <input type="file" name="image" id="edit_bar_image_input" class="form-control-custom" accept="image/jpeg,image/png,image/jpg,image/webp,image/gif">
              <small class="form-text text-muted" style="font-size: 0.74rem;">Leave blank to preserve existing image</small>

              <div class="upload-preview-box" id="edit_bar_preview_wrap" style="display: none;">
                <img id="edit_bar_image_preview" src="" alt="Selected Preview">
              </div>
            </div>
          </div>

          <div class="modal-pos-footer">
            <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn-bar-primary">
              <i class="fa-solid fa-check"></i>
              <span>Update Category</span>
            </button>
          </div>

        </div>
      </form>
    </div>
  </div>


  <!-- ===================================================
       MODAL: BULK UPLOAD BAR CATEGORIES
       =================================================== -->
  <div class="modal fade" id="bulkUploadCategoryModal" tabindex="-1" aria-labelledby="bulkUploadCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form method="POST" action="{{ route('bar.category.bulk.upload') }}" enctype="multipart/form-data" style="width: 100%;">
        @csrf
        <div class="modal-content modal-pos-content">
          
          <div class="modal-pos-header">
            <h5 class="modal-pos-title" id="bulkUploadCategoryModalLabel">
              <i class="fa-solid fa-file-excel text-success"></i>
              <span>Bulk Upload Bar Categories</span>
            </h5>
            <button type="button" class="modal-btn-close" data-bs-dismiss="modal" aria-label="Close">
              <i class="fa-solid fa-xmark"></i>
            </button>
          </div>

          <div class="modal-pos-body">
            <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 14px;">
              Upload an Excel (.xlsx, .xls) or CSV spreadsheet containing bar category names. All uploaded rows will be automatically marked with <strong>category_type = BAR</strong>.
            </p>

            <div class="alert alert-light border mb-3" style="border-radius: 12px; background: #faf5ff; border-color: #ddd6fe !important;">
              <div class="d-flex justify-content-between align-items-center">
                <span style="font-size: 0.8rem; font-weight: 700; color: #6d28d9;">
                  <i class="fa-solid fa-download me-1"></i> Download Pre-Formatted Template
                </span>
                <a href="{{ route('bar.category.template') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 20px; font-weight: 700; font-size: 0.75rem;">
                  Download .xlsx
                </a>
              </div>
            </div>

            <div class="form-group mb-2">
              <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700; color: #1e293b;">
                Select Spreadsheet <span class="text-danger">*</span>
              </label>
              <input type="file" name="bulk_file" class="form-control-custom" required accept=".xlsx,.xls,.csv">
            </div>
          </div>

          <div class="modal-pos-footer">
            <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn-bar-primary">
              <i class="fa-solid fa-cloud-arrow-up"></i>
              <span>Upload &amp; Import</span>
            </button>
          </div>

        </div>
      </form>
    </div>
  </div>


  <!-- Core Scripts -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  @include('includes.script')

  <script>
    $(document).ready(function() {
      // Live search filter
      $('#categorySearchInput').on('keyup', function() {
        const val = $(this).val().toLowerCase().trim();
        $('.category-item-card').each(function() {
          const name = $(this).data('name') || '';
          if (name.includes(val)) {
            $(this).show();
          } else {
            $(this).hide();
          }
        });
      });

      // Handle Edit Modal Populator
      $('.edit-bar-btn').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const image = $(this).data('image');

        $('#edit_bar_id').val(id);
        $('#edit_bar_name').val(name);

        if (image) {
          $('#edit_bar_image_preview').attr('src', '{{ URL::to("storage/category") }}/' + image);
          $('#edit_bar_preview_wrap').show();
        } else {
          $('#edit_bar_preview_wrap').hide();
        }

        const modal = new bootstrap.Modal(document.getElementById('editBarCategoryModal'));
        modal.show();
      });

      // Image Preview Handlers
      $('#add_bar_image_input').on('change', function() {
        if (this.files && this.files[0]) {
          const reader = new FileReader();
          reader.onload = function(e) {
            $('#add_bar_image_preview').attr('src', e.target.result);
            $('#add_bar_preview_wrap').show();
          };
          reader.readAsDataURL(this.files[0]);
        }
      });

      $('#edit_bar_image_input').on('change', function() {
        if (this.files && this.files[0]) {
          const reader = new FileReader();
          reader.onload = function(e) {
            $('#edit_bar_image_preview').attr('src', e.target.result);
            $('#edit_bar_preview_wrap').show();
          };
          reader.readAsDataURL(this.files[0]);
        }
      });
    });
  </script>
</body>
</html>
