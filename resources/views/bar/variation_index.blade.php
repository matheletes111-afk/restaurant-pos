<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bar Variation Management • Bill&amp;Bite POS</title>
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
    .var-header-card {
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

    .var-header-icon {
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

    .var-main-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.45rem;
      font-weight: 800;
      color: var(--bar-dark);
      margin: 0 0 2px 0;
      letter-spacing: -0.02em;
      line-height: 1.25;
    }

    /* Buttons */
    .btn-bar-primary {
      background: var(--bar-gradient);
      color: #ffffff;
      border: none;
      padding: 9px 20px;
      border-radius: 30px;
      font-weight: 700;
      font-size: 0.86rem;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 4px 15px rgba(124, 58, 237, 0.3);
      transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
      cursor: pointer;
      text-decoration: none;
    }
    .btn-bar-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 22px rgba(124, 58, 237, 0.45);
      color: #ffffff;
    }

    .btn-bar-secondary {
      background: #ffffff;
      color: #475569;
      border: 1px solid #cbd5e1;
      padding: 9px 18px;
      border-radius: 30px;
      font-weight: 700;
      font-size: 0.86rem;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all 0.2s ease;
      cursor: pointer;
      text-decoration: none;
    }
    .btn-bar-secondary:hover {
      background: #f8fafc;
      border-color: #94a3b8;
      color: #0f172a;
    }

    /* KPI Summary Deck */
    .kpi-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 16px;
      margin-bottom: 22px;
    }

    .kpi-card {
      background: #ffffff;
      border-radius: var(--radius-md);
      padding: 18px 20px;
      border: 1px solid var(--border-color);
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .kpi-icon {
      width: 44px;
      height: 44px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.15rem;
      flex-shrink: 0;
    }
    .kpi-icon.purple { background: #ede9fe; color: #7c3aed; }
    .kpi-icon.blue { background: #e0f2fe; color: #0284c7; }
    .kpi-icon.amber { background: #fef3c7; color: #d97706; }
    .kpi-icon.green { background: #dcfce7; color: #16a34a; }

    .kpi-val {
      font-family: 'Outfit', sans-serif;
      font-size: 1.35rem;
      font-weight: 800;
      color: var(--bar-dark);
      line-height: 1.1;
    }
    .kpi-label {
      font-size: 0.76rem;
      color: var(--text-muted);
      font-weight: 600;
      margin-top: 2px;
    }

    /* Filters Bar */
    .filter-card {
      background: #ffffff;
      border-radius: var(--radius-md);
      padding: 16px 20px;
      margin-bottom: 20px;
      border: 1px solid var(--border-color);
      box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }

    .form-control-custom {
      border: 1px solid #cbd5e1;
      border-radius: 10px;
      padding: 9px 14px;
      font-size: 0.88rem;
      color: #1e293b;
      width: 100%;
      background: #ffffff;
      transition: all 0.2s ease;
    }
    .form-control-custom:focus {
      outline: none;
      border-color: #8b5cf6;
      box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.15);
    }

    /* Table Design */
    .var-table-card {
      background: #ffffff;
      border-radius: var(--radius-lg);
      padding: 20px 22px;
      border: 1px solid var(--border-color);
      box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }

    .table thead th {
      background: #f8fafc;
      color: #475569;
      font-size: 0.75rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      border-bottom: 1.5px solid #e2e8f0;
      padding: 12px 16px;
    }

    .table tbody td {
      padding: 14px 16px;
      vertical-align: middle;
      font-size: 0.86rem;
      border-bottom: 1px solid #f1f5f9;
    }

    .table tbody tr:hover {
      background: #faf5ff;
    }

    .badge-bar-category {
      background: #ede9fe;
      color: #6d28d9;
      font-size: 0.72rem;
      font-weight: 700;
      padding: 4px 8px;
      border-radius: 6px;
      text-transform: uppercase;
      letter-spacing: 0.03em;
    }

    .badge-bar-brand {
      background: #fef3c7;
      color: #b45309;
      font-size: 0.72rem;
      font-weight: 700;
      padding: 4px 8px;
      border-radius: 6px;
    }

    .var-icon-pill {
      width: 34px;
      height: 34px;
      border-radius: 10px;
      background: #f3e8ff;
      color: #7c3aed;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 0.95rem;
      flex-shrink: 0;
    }

    .depletion-bar {
      height: 6px;
      background: #e2e8f0;
      border-radius: 3px;
      overflow: hidden;
      margin-top: 4px;
      width: 80px;
    }
    .depletion-fill {
      height: 100%;
      background: linear-gradient(90deg, #8b5cf6, #ec4899);
      border-radius: 3px;
    }

    /* Modal Presets */
    .preset-pill {
      border: 1px solid #cbd5e1;
      background: #ffffff;
      color: #334155;
      font-size: 0.74rem;
      font-weight: 700;
      padding: 4px 10px;
      border-radius: 16px;
      cursor: pointer;
      transition: all 0.15s ease;
    }
    .preset-pill:hover, .preset-pill.active {
      background: #ede9fe;
      border-color: #8b5cf6;
      color: #7c3aed;
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

      <!-- Navigation Breadcrumb -->
      <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <a href="{{ route('bar.item.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 20px; font-weight: 700; padding: 6px 16px;">
          <i class="fa-solid fa-arrow-left me-1"></i> Back to Bar Products
        </a>
        <div class="text-muted small fw-bold">
          <span>Bar Management</span> &rsaquo;
          <a href="{{ route('bar.item.index') }}" class="text-decoration-none text-muted">Bar Products</a> &rsaquo;
          @if($selectedItem)
            <span class="text-dark">{{ $selectedItem->name }}</span> &rsaquo;
          @endif
          <span class="text-primary">Serving Variations</span>
        </div>
      </div>

      <!-- Header Deck -->
      <div class="var-header-card">
        <div class="header-left-cluster">
          <div class="var-header-icon">
            <i class="fa-solid fa-sliders"></i>
          </div>
          <div>
            <div style="font-size: 0.72rem; font-weight: 800; text-transform: uppercase; color: var(--bar-primary); letter-spacing: 0.08em; margin-bottom: 2px;">
              <i class="fa-solid fa-martini-glass me-1"></i> Bar Management &bull; Product Serving Formats
            </div>
            <h1 class="var-main-title">
              @if($selectedItem)
                {{ $selectedItem->name }} &bull; Variations
              @else
                Bar Serving Variations
              @endif
            </h1>
            <p style="font-size: 0.82rem; color: #64748b; margin: 0;">
              @if($selectedItem)
                Configure custom pegs, pours, halves, and bottle prices for this liquor product.
              @else
                Manage pegs, pours, halves, and bottle serving formats with volume (ml), selling prices &amp; bottle depletion ratios.
              @endif
            </p>
          </div>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
          <a href="{{ route('bar.item.index') }}" class="btn-bar-secondary">
            <i class="fa-solid fa-wine-bottle"></i> Bar Products
          </a>
          <button type="button" class="btn-bar-primary" data-bs-toggle="modal" data-bs-target="#addVariationModal">
            <i class="fa-solid fa-plus"></i> Add Serving Variation
          </button>
        </div>
      </div>

      @if($selectedItem)
        @php
          $prodBottle = floatval($selectedItem->bottle_size_ml ?? 750);
          $prodStock = floatval($selectedItem->current_stock_ml ?? 0);
        @endphp
        <!-- Selected Product Highlight Card -->
        <div class="p-3 mb-4 rounded-3" style="background: #ffffff; border: 1.5px solid #ede9fe; box-shadow: 0 4px 15px rgba(124, 58, 237, 0.04);">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
              <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: #faf5ff; border: 1px solid #e9d5ff; color: #7c3aed; font-size: 1.3rem;">
                <i class="fa-solid fa-wine-bottle"></i>
              </div>
              <div>
                <div class="d-flex align-items-center gap-2">
                  <h3 class="fw-bold mb-0" style="font-size: 1.15rem; color: #1e293b;">{{ $selectedItem->name }}</h3>
                  @if($selectedItem->brand)
                    <span class="badge-bar-brand">{{ $selectedItem->brand->name }}</span>
                  @endif
                  @if($selectedItem->category)
                    <span class="badge-bar-category">{{ $selectedItem->category->name }}</span>
                  @endif
                </div>
                <div class="text-muted small mt-1">
                  Bottle Size: <strong class="text-dark">{{ number_format($prodBottle) }} ml</strong> &bull;
                  Total In Stock: <strong class="text-primary">{{ number_format($prodStock) }} ml</strong>
                  <span class="text-muted">({{ $prodBottle > 0 ? number_format($prodStock / $prodBottle, 1) : 0 }} Bottles eq.)</span> &bull;
                  State VAT: <strong style="color: #7c3aed;">{{ floatval($selectedItem->vat_rate ?? 0) }}%</strong> <span class="badge" style="background: #ede9fe; color: #6d28d9; font-size: 0.68rem; font-weight: 700;">GST-Exempt</span>
                </div>
              </div>
            </div>
            <div class="d-flex align-items-center gap-2">
              <a href="{{ route('bar.variation.index') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 20px; font-size: 0.78rem; font-weight: 700;">
                <i class="fa-solid fa-list me-1"></i> View All Variations
              </a>
              <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addVariationModal" style="border-radius: 20px; background: var(--bar-primary); border-color: var(--bar-primary); font-weight: 700; padding: 6px 14px;">
                <i class="fa-solid fa-plus me-1"></i> Add Variation
              </button>
            </div>
          </div>
        </div>
      @endif

      <!-- KPI Summary Cards -->
      <div class="kpi-row">
        <div class="kpi-card">
          <div class="kpi-icon purple"><i class="fa-solid fa-sliders"></i></div>
          <div>
            <div class="kpi-val">{{ number_format($stats['total_variations']) }}</div>
            <div class="kpi-label">Total Serving Formats</div>
          </div>
        </div>
        <div class="kpi-card">
          <div class="kpi-icon blue"><i class="fa-solid fa-wine-bottle"></i></div>
          <div>
            <div class="kpi-val">{{ number_format($stats['distinct_items']) }}</div>
            <div class="kpi-label">Configured Bar Products</div>
          </div>
        </div>
        <div class="kpi-card">
          <div class="kpi-icon amber"><i class="fa-solid fa-martini-glass-citrus"></i></div>
          <div>
            <div class="kpi-val">{{ number_format($stats['standard_pegs']) }}</div>
            <div class="kpi-label">Standard Pegs (30/60/90ml)</div>
          </div>
        </div>
        <div class="kpi-card">
          <div class="kpi-icon green"><i class="fa-solid fa-indian-rupee-sign"></i></div>
          <div>
            <div class="kpi-val">₹{{ number_format($stats['avg_price'], 0) }}</div>
            <div class="kpi-label">Average Serving Price</div>
          </div>
        </div>
      </div>

      <!-- Flash Messages -->
      @include('includes.message')

      <!-- Filter Bar -->
      <div class="filter-card">
        <form method="GET" action="{{ route('bar.variation.index') }}" class="row g-2 align-items-center">
          
          <div class="col-md-3">
            <label class="form-label small fw-bold text-muted mb-1">Filter by Product</label>
            <select name="item_id" class="form-control-custom form-select" onchange="this.form.submit()">
              <option value="">All Bar Products</option>
              @foreach($barItems as $bItem)
                <option value="{{ $bItem->id }}" {{ $itemId == $bItem->id ? 'selected' : '' }}>
                  {{ $bItem->name }} ({{ number_format($bItem->bottle_size_ml) }}ml)
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label small fw-bold text-muted mb-1">Filter by Category</label>
            <select name="category_id" class="form-control-custom form-select" onchange="this.form.submit()">
              <option value="">All Bar Categories</option>
              @foreach($barCategories as $bCat)
                <option value="{{ $bCat->id }}" {{ $categoryId == $bCat->id ? 'selected' : '' }}>
                  {{ $bCat->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label small fw-bold text-muted mb-1">Search Variations</label>
            <div class="input-group">
              <input type="text" name="search" value="{{ $search }}" class="form-control-custom" placeholder="Search variation or product name...">
              <button class="btn btn-primary" type="submit" style="border-radius: 0 10px 10px 0; background: var(--bar-primary); border-color: var(--bar-primary);">
                <i class="fa-solid fa-magnifying-glass"></i>
              </button>
            </div>
          </div>

          <div class="col-md-2 text-md-end pt-md-3">
            @if($itemId || $categoryId || $search)
              <a href="{{ route('bar.variation.index') }}" class="btn btn-sm btn-outline-secondary w-100" style="border-radius: 10px; padding: 8px;">
                <i class="fa-solid fa-rotate-left me-1"></i> Clear Filter
              </a>
            @else
              <span class="text-muted small fw-bold">Showing {{ $variations->count() }} Formats</span>
            @endif
          </div>

        </form>
      </div>

      <!-- Variations DataTable -->
      <div class="var-table-card">
        <div class="table-responsive">
          <table class="table align-middle mb-0" id="variationsTable">
            <thead>
              <tr>
                <th style="border-radius: 8px 0 0 8px; width: 60px;">#ID</th>
                <th>Liquor Product &amp; Brand</th>
                <th>Category</th>
                <th>Serving Variation Name</th>
                <th>Serving Volume</th>
                <th>Bottle Depletion</th>
                <th>Selling Price</th>
                <th class="text-center">Status</th>
                <th class="text-end" style="border-radius: 0 8px 8px 0; width: 120px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($variations as $var)
                @php
                  $product = $var->item;
                  $bottleSize = $product ? floatval($product->bottle_size_ml) : 750;
                  $mlVal = floatval($var->ml);
                  $ratio = $bottleSize > 0 ? ($mlVal / $bottleSize) : 0;
                  $percent = round($ratio * 100, 1);
                @endphp
                <tr>
                  <td class="font-monospace text-muted fw-bold" style="font-size: 0.8rem;">
                    #{{ $var->id }}
                  </td>
                  <td>
                    @if($product)
                      <div class="d-flex align-items-center gap-2">
                        <div class="fw-bold text-dark" style="font-size: 0.92rem;">
                          {{ $product->name }}
                        </div>
                        @if($product->brand)
                          <span class="badge-bar-brand">{{ $product->brand->name }}</span>
                        @endif
                      </div>
                      <small class="text-muted font-monospace" style="font-size: 0.75rem;">
                        Standard Bottle: {{ number_format($bottleSize) }} ml
                      </small>
                    @else
                      <span class="text-danger small">Product Deleted</span>
                    @endif
                  </td>
                  <td>
                    @if($product && $product->category)
                      <span class="badge-bar-category">{{ $product->category->name }}</span>
                    @else
                      <span class="text-muted small">—</span>
                    @endif
                  </td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <div class="var-icon-pill">
                        @if($mlVal >= $bottleSize)
                          <i class="fa-solid fa-wine-bottle text-success"></i>
                        @elseif($mlVal >= 180)
                          <i class="fa-solid fa-flask text-warning"></i>
                        @else
                          <i class="fa-solid fa-martini-glass text-primary"></i>
                        @endif
                      </div>
                      <span class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $var->name }}</span>
                    </div>
                  </td>
                  <td>
                    <span class="font-monospace fw-bold px-2 py-1 rounded bg-light" style="font-size: 0.86rem; border: 1px solid #e2e8f0;">
                      {{ number_format($mlVal) }} ml
                    </span>
                  </td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <span class="fw-bold font-monospace small" style="color: #6d28d9;">{{ $percent }}%</span>
                      <small class="text-muted" style="font-size: 0.72rem;">({{ number_format($ratio, 2) }} bottle)</small>
                    </div>
                    <div class="depletion-bar">
                      <div class="depletion-fill" style="width: {{ min(100, $percent) }}%;"></div>
                    </div>
                  </td>
                  <td>
                    <span class="font-monospace fw-bold text-success" style="font-size: 0.96rem;">
                      ₹{{ number_format($var->price, 2) }}
                    </span>
                    @if($product && floatval($product->vat_rate) > 0)
                      <div><span class="badge" style="background: #ede9fe; color: #6d28d9; font-size: 0.68rem; font-weight: 700;">+{{ floatval($product->vat_rate) }}% State VAT</span></div>
                    @endif
                  </td>
                  <td class="text-center">
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1" style="font-size: 0.72rem; font-weight: 700;">
                      <i class="fa-solid fa-circle-check me-1"></i> Active
                    </span>
                  </td>
                  <td class="text-end">
                    <div class="d-inline-flex gap-1">
                      <button type="button" 
                              class="btn btn-sm btn-outline-secondary editVarBtn" 
                              data-id="{{ $var->id }}"
                              data-item-id="{{ $var->sub_category_id }}"
                              data-name="{{ $var->name }}"
                              data-ml="{{ $var->ml }}"
                              data-price="{{ $var->price }}"
                              data-bottle-size="{{ $bottleSize }}"
                              style="border-radius: 8px; padding: 6px 10px;" title="Edit Variation">
                        <i class="fa-solid fa-pen-to-square"></i>
                      </button>
                      <a href="{{ route('bar.variation.delete', $var->id) }}" 
                         class="btn btn-sm btn-outline-danger" 
                         onclick="return confirm('Are you sure you want to soft delete \'{{ addslashes($var->name) }}\' for {{ addslashes($product ? $product->name : '') }}?')"
                         style="border-radius: 8px; padding: 6px 10px;" title="Delete Variation">
                        <i class="fa-solid fa-trash"></i>
                      </a>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="9" class="text-center py-5">
                    <div style="max-width: 380px; margin: 0 auto;">
                      <i class="fa-solid fa-sliders fs-1 text-secondary opacity-50 mb-3 d-block"></i>
                      <h5 class="fw-bold">No Serving Variations Found</h5>
                      <p class="text-muted small mb-3">Add standard pegs (30ml, 60ml) or bottle formats for your bar products.</p>
                      <button type="button" class="btn-bar-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addVariationModal">
                        <i class="fa-solid fa-plus"></i> Add First Variation
                      </button>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>


  <!-- ===================================================
       MODAL: ADD SERVING VARIATION
       =================================================== -->
  <div class="modal fade" id="addVariationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <form method="POST" action="{{ route('bar.variation.store') }}" style="width: 100%;">
        @csrf
        <div class="modal-content" style="border-radius: var(--radius-lg); overflow: hidden; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.18);">
          
          <div class="modal-header" style="background: #0f172a; color: #ffffff; padding: 18px 24px;">
            <div>
              <h5 class="modal-title font-weight-bold text-white" style="font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 1.2rem;">
                <i class="fa-solid fa-plus-circle text-warning me-2"></i> Add Serving Variation
              </h5>
              <div style="font-size: 0.78rem; color: #94a3b8;">
                Configure pegs, pours, halves, and full bottles with exact ml volume.
              </div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body" style="padding: 22px 24px;">
            <div class="row g-3">
              
              <div class="col-12">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">
                  Bar Liquor Product <span class="text-danger">*</span>
                </label>
                <select name="sub_category_id" id="addVarItemSelect" class="form-control-custom form-select" required>
                  <option value="">Select a Bar Product...</option>
                  @foreach($barItems as $item)
                    @php
                      $bName = $item->brand ? $item->brand->name : 'General';
                    @endphp
                    <option value="{{ $item->id }}" 
                            data-bottle-size="{{ $item->bottle_size_ml }}" 
                            data-price="{{ $item->price }}"
                            {{ $itemId == $item->id ? 'selected' : '' }}>
                      {{ $item->name }} ({{ $bName }} &bull; {{ number_format($item->bottle_size_ml) }}ml)
                    </option>
                  @endforeach
                </select>
              </div>

              <!-- Quick Presets -->
              <div class="col-12">
                <div class="p-2 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                  <div class="d-flex align-items-center gap-1 flex-wrap">
                    <span class="small text-muted fw-bold me-1" style="font-size: 0.72rem;">Quick Presets:</span>
                    <button type="button" class="preset-pill addPresetBtn" data-name="30ml Peg" data-ml="30">+ 30ml Peg</button>
                    <button type="button" class="preset-pill addPresetBtn" data-name="60ml Large" data-ml="60">+ 60ml Large</button>
                    <button type="button" class="preset-pill addPresetBtn" data-name="90ml Patiala" data-ml="90">+ 90ml Patiala</button>
                    <button type="button" class="preset-pill addPresetBtn" data-name="180ml Quarter" data-ml="180">+ 180ml Quarter</button>
                    <button type="button" class="preset-pill addPresetBtn" data-name="375ml Half" data-ml="375">+ 375ml Half</button>
                    <button type="button" class="preset-pill addPresetBtn" id="addPresetBottleBtn" data-name="Full Bottle" data-ml="750">+ Full Bottle</button>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">
                  Variation Name <span class="text-danger">*</span>
                </label>
                <input type="text" name="name" id="addVarNameInput" class="form-control-custom" required placeholder="e.g. 30ml Peg, 60ml Large, Bottle">
              </div>

              <div class="col-md-3">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">
                  Volume in ml <span class="text-danger">*</span>
                </label>
                <input type="number" step="any" name="ml" id="addVarMlInput" class="form-control-custom" required placeholder="30" value="30">
              </div>

              <div class="col-md-3">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">
                  Selling Price (₹) <span class="text-danger">*</span>
                </label>
                <input type="number" step="0.01" name="price" id="addVarPriceInput" class="form-control-custom" required placeholder="250.00">
              </div>

              <!-- Live Depletion Calculation Banner -->
              <div class="col-12">
                <div class="p-3 rounded-3" style="background: #faf5ff; border: 1.5px dashed #d8b4fe;">
                  <div class="d-flex align-items-center justify-content-between">
                    <div>
                      <div style="font-size: 0.74rem; font-weight: 800; text-transform: uppercase; color: #7c3aed;">
                        <i class="fa-solid fa-calculator me-1"></i> Bottle Depletion Ratio
                      </div>
                      <div class="fw-bold" style="font-size: 0.95rem; color: #4c1d95;" id="addDepletionText">
                        30 ml consumes 4.0% of a 750 ml bottle (0.040 bottle equivalent)
                      </div>
                    </div>
                    <span class="badge" style="background: #7c3aed; color: #fff; font-size: 0.85rem; padding: 6px 12px; border-radius: 8px;" id="addDepletionBadge">
                      4.0% Bottle
                    </span>
                  </div>
                </div>
              </div>

            </div>
          </div>

          <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 14px 24px;">
            <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn-bar-primary">Save Variation</button>
          </div>

        </div>
      </form>
    </div>
  </div>


  <!-- ===================================================
       MODAL: EDIT SERVING VARIATION
       =================================================== -->
  <div class="modal fade" id="editVariationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <form method="POST" action="{{ route('bar.variation.update') }}" style="width: 100%;">
        @csrf
        <input type="hidden" name="id" id="editVarId">
        <div class="modal-content" style="border-radius: var(--radius-lg); overflow: hidden; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.18);">
          
          <div class="modal-header" style="background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 18px 24px;">
            <div>
              <h5 class="modal-title font-weight-bold text-dark" style="font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 1.2rem;">
                <i class="fa-solid fa-pen-to-square text-primary me-2"></i> Edit Serving Variation
              </h5>
              <div style="font-size: 0.78rem; color: #64748b;">
                Modify format title, serving ml or price.
              </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body" style="padding: 22px 24px;">
            <div class="row g-3">
              
              <div class="col-12">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">
                  Bar Liquor Product <span class="text-danger">*</span>
                </label>
                <select name="sub_category_id" id="editVarItemSelect" class="form-control-custom form-select" required>
                  @foreach($barItems as $item)
                    @php
                      $bName = $item->brand ? $item->brand->name : 'General';
                    @endphp
                    <option value="{{ $item->id }}" data-bottle-size="{{ $item->bottle_size_ml }}">
                      {{ $item->name }} ({{ $bName }} &bull; {{ number_format($item->bottle_size_ml) }}ml)
                    </option>
                  @endforeach
                </select>
              </div>

              <!-- Quick Presets -->
              <div class="col-12">
                <div class="p-2 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                  <div class="d-flex align-items-center gap-1 flex-wrap">
                    <span class="small text-muted fw-bold me-1" style="font-size: 0.72rem;">Quick Presets:</span>
                    <button type="button" class="preset-pill editPresetBtn" data-name="30ml Peg" data-ml="30">+ 30ml</button>
                    <button type="button" class="preset-pill editPresetBtn" data-name="60ml Large" data-ml="60">+ 60ml</button>
                    <button type="button" class="preset-pill editPresetBtn" data-name="90ml Patiala" data-ml="90">+ 90ml</button>
                    <button type="button" class="preset-pill editPresetBtn" data-name="180ml Quarter" data-ml="180">+ 180ml</button>
                    <button type="button" class="preset-pill editPresetBtn" data-name="375ml Half" data-ml="375">+ 375ml</button>
                    <button type="button" class="preset-pill editPresetBtn" id="editPresetBottleBtn" data-name="Full Bottle" data-ml="750">+ Bottle</button>
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">
                  Variation Name <span class="text-danger">*</span>
                </label>
                <input type="text" name="name" id="editVarNameInput" class="form-control-custom" required>
              </div>

              <div class="col-md-3">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">
                  Volume in ml <span class="text-danger">*</span>
                </label>
                <input type="number" step="any" name="ml" id="editVarMlInput" class="form-control-custom" required>
              </div>

              <div class="col-md-3">
                <label class="form-label font-weight-bold" style="font-size: 0.82rem; font-weight: 700;">
                  Selling Price (₹) <span class="text-danger">*</span>
                </label>
                <input type="number" step="0.01" name="price" id="editVarPriceInput" class="form-control-custom" required>
              </div>

              <!-- Live Depletion Calculation Banner -->
              <div class="col-12">
                <div class="p-3 rounded-3" style="background: #f8fafc; border: 1.5px dashed #cbd5e1;">
                  <div class="d-flex align-items-center justify-content-between">
                    <div>
                      <div style="font-size: 0.74rem; font-weight: 800; text-transform: uppercase; color: #475569;">
                        <i class="fa-solid fa-calculator me-1"></i> Bottle Depletion Ratio
                      </div>
                      <div class="fw-bold" style="font-size: 0.95rem; color: #1e293b;" id="editDepletionText">
                        --
                      </div>
                    </div>
                    <span class="badge" style="background: #475569; color: #fff; font-size: 0.85rem; padding: 6px 12px; border-radius: 8px;" id="editDepletionBadge">
                      --%
                    </span>
                  </div>
                </div>
              </div>

            </div>
          </div>

          <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 14px 24px;">
            <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn-bar-primary">Update Variation</button>
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

      // Update Add Modal Depletion Calculation
      function updateAddDepletion() {
        const selectedOpt = $('#addVarItemSelect option:selected');
        const bottleSize = parseFloat(selectedOpt.data('bottle-size')) || 750;
        const ml = parseFloat($('#addVarMlInput').val()) || 0;
        const ratio = bottleSize > 0 ? (ml / bottleSize) : 0;
        const percent = (ratio * 100).toFixed(1);

        $('#addDepletionText').text(`${ml} ml consumes ${percent}% of a ${bottleSize} ml bottle (${ratio.toFixed(3)} bottle equivalent)`);
        $('#addDepletionBadge').text(`${percent}% Bottle`);
        $('#addPresetBottleBtn').attr('data-ml', bottleSize);
      }

      $('#addVarItemSelect, #addVarMlInput').on('input change', updateAddDepletion);
      updateAddDepletion();

      // Add Presets
      $('.addPresetBtn').on('click', function() {
        const name = $(this).attr('data-name');
        const ml = $(this).attr('data-ml');
        $('#addVarNameInput').val(name);
        $('#addVarMlInput').val(ml);
        updateAddDepletion();
        $('#addVarPriceInput').focus();
      });

      // Update Edit Modal Depletion Calculation
      function updateEditDepletion() {
        const selectedOpt = $('#editVarItemSelect option:selected');
        const bottleSize = parseFloat(selectedOpt.data('bottle-size')) || 750;
        const ml = parseFloat($('#editVarMlInput').val()) || 0;
        const ratio = bottleSize > 0 ? (ml / bottleSize) : 0;
        const percent = (ratio * 100).toFixed(1);

        $('#editDepletionText').text(`${ml} ml consumes ${percent}% of a ${bottleSize} ml bottle (${ratio.toFixed(3)} bottle equivalent)`);
        $('#editDepletionBadge').text(`${percent}% Bottle`);
        $('#editPresetBottleBtn').attr('data-ml', bottleSize);
      }

      $('#editVarItemSelect, #editVarMlInput').on('input change', updateEditDepletion);

      // Edit Presets
      $('.editPresetBtn').on('click', function() {
        const name = $(this).attr('data-name');
        const ml = $(this).attr('data-ml');
        $('#editVarNameInput').val(name);
        $('#editVarMlInput').val(ml);
        updateEditDepletion();
        $('#editVarPriceInput').focus();
      });

      // Open Edit Modal
      $('.editVarBtn').on('click', function() {
        const id = $(this).data('id');
        const itemId = $(this).data('item-id');
        const name = $(this).data('name');
        const ml = $(this).data('ml');
        const price = $(this).data('price');

        $('#editVarId').val(id);
        $('#editVarItemSelect').val(itemId);
        $('#editVarNameInput').val(name);
        $('#editVarMlInput').val(ml);
        $('#editVarPriceInput').val(price);

        updateEditDepletion();

        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('editVariationModal'));
        modal.show();
      });

    });
  </script>
</body>
</html>
