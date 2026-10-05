<!DOCTYPE html>
<html lang="en">
<head>
  <title>Dish Addon Master • Bill&Bite POS</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @include('includes.style')
  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Font Awesome 6 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    :root {
      --addon-primary: #ff5e14;
      --addon-primary-dark: #e04a08;
      --addon-primary-light: #fff3ed;
      --addon-primary-gradient: linear-gradient(135deg, #ff5e14 0%, #ff8c42 100%);
      --addon-dark: #0f172a;
      --addon-slate: #1e293b;
      --addon-muted: #64748b;
      --addon-border: #e2e8f0;
      --addon-card-bg: #ffffff;
      --addon-veg: #10b981;
      --addon-nonveg: #ef4444;
    }

    body {
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
      background-color: #f8fafc;
      color: var(--addon-slate);
    }

    .addon-page-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 16px;
      margin-bottom: 24px;
    }

    .addon-title-box h3 {
      font-family: 'Outfit', sans-serif;
      font-size: 1.6rem;
      font-weight: 800;
      color: var(--addon-dark);
      margin-bottom: 4px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .addon-title-box p {
      font-size: 0.88rem;
      color: var(--addon-muted);
      margin-bottom: 0;
    }

    .addon-header-actions {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
    }

    .btn-addon-primary {
      background: var(--addon-primary-gradient);
      color: #ffffff !important;
      border: none;
      padding: 10px 22px;
      border-radius: 30px;
      font-weight: 700;
      font-size: 0.88rem;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      box-shadow: 0 4px 14px rgba(255, 94, 20, 0.28);
      transition: all 0.2s ease;
      text-decoration: none;
      cursor: pointer;
    }

    .btn-addon-primary,
    .btn-addon-primary:hover,
    .btn-addon-primary:focus,
    .btn-addon-primary:active,
    .btn-addon-primary * {
      color: #ffffff !important;
    }

    .btn-addon-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(255, 94, 20, 0.38);
      color: #ffffff !important;
    }

    .btn-addon-outline {
      background: #ffffff;
      color: var(--addon-slate) !important;
      border: 1.5px solid var(--addon-border);
      padding: 9px 18px;
      border-radius: 30px;
      font-weight: 700;
      font-size: 0.88rem;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      transition: all 0.2s ease;
      text-decoration: none;
      cursor: pointer;
    }

    .btn-addon-outline:hover {
      background: #f1f5f9;
      border-color: var(--addon-primary);
      color: var(--addon-primary) !important;
      transform: translateY(-1px);
    }

    /* Stat Cards */
    .addon-stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 16px;
      margin-bottom: 24px;
    }

    .addon-stat-card {
      background: #ffffff;
      border: 1.5px solid var(--addon-border);
      border-radius: 16px;
      padding: 18px 22px;
      display: flex;
      align-items: center;
      gap: 16px;
      box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
      transition: all 0.2s ease;
    }

    .addon-stat-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
    }

    .stat-icon-wrap {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.3rem;
      flex-shrink: 0;
    }

    .icon-total { background: #f1f5f9; color: var(--addon-dark); }
    .icon-veg { background: #ecfdf5; color: #059669; }
    .icon-nonveg { background: #fef2f2; color: #dc2626; }
    .icon-active { background: #eff6ff; color: #2563eb; }

    .stat-val-num {
      font-family: 'Outfit', sans-serif;
      font-size: 1.45rem;
      font-weight: 800;
      color: var(--addon-dark);
      line-height: 1.1;
    }

    .stat-val-label {
      font-size: 0.78rem;
      font-weight: 600;
      color: var(--addon-muted);
      text-transform: uppercase;
      letter-spacing: 0.04em;
    }

    /* Main Table Card */
    .addon-main-card {
      background: #ffffff;
      border-radius: 20px;
      border: 1.5px solid var(--addon-border);
      box-shadow: 0 6px 24px rgba(15, 23, 42, 0.04);
      overflow: hidden;
    }

    .addon-filter-toolbar {
      padding: 18px 24px;
      border-bottom: 1.5px solid var(--addon-border);
      background: #ffffff;
    }

    .addon-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 0;
    }

    .addon-table th {
      background: #f8fafc;
      color: var(--addon-muted);
      font-size: 0.78rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      padding: 14px 20px;
      border-bottom: 2px solid var(--addon-border);
    }

    .addon-table td {
      padding: 16px 20px;
      border-bottom: 1px solid #f1f5f9;
      font-size: 0.92rem;
      vertical-align: middle;
    }

    .addon-table tbody tr:hover {
      background: #fafbfc;
    }

    .food-type-pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 0.76rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.03em;
    }

    .pill-veg { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
    .pill-nonveg { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

    .food-type-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
    }
    .food-type-dot.veg { background: #10b981; }
    .food-type-dot.nonveg { background: #ef4444; }

    .price-tag {
      font-family: 'Outfit', sans-serif;
      font-size: 1.05rem;
      font-weight: 800;
      color: var(--addon-dark);
    }

    /* Action Buttons in Table */
    .btn-table-action {
      width: 34px;
      height: 34px;
      border-radius: 10px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border: 1px solid var(--addon-border);
      background: #ffffff;
      color: var(--addon-slate);
      font-size: 0.85rem;
      transition: all 0.15s ease;
      cursor: pointer;
    }

    .btn-table-action:hover {
      transform: translateY(-1px);
    }

    .btn-table-action.btn-edit:hover {
      background: #eff6ff;
      border-color: #3b82f6;
      color: #2563eb;
    }

    .btn-table-action.btn-delete:hover {
      background: #fef2f2;
      border-color: #ef4444;
      color: #dc2626;
    }

    /* Toggle Status Switch */
    .status-toggle-wrap {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
    }

    /* Modal Styling */
    .modal-content-premium {
      border: none;
      border-radius: 20px;
      box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25);
      overflow: hidden;
    }

    .modal-header-premium {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
      color: #ffffff !important;
      padding: 20px 24px;
      border-bottom: 3px solid var(--addon-primary);
    }

    .modal-header-premium .modal-title,
    .modal-header-premium .modal-title * {
      font-family: 'Outfit', sans-serif;
      font-size: 1.2rem;
      font-weight: 800;
      color: #ffffff !important;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .modal-body-premium {
      padding: 24px;
      background: #ffffff;
    }

    .modal-footer-premium {
      padding: 16px 24px;
      background: #f8fafc;
      border-top: 1.5px solid var(--addon-border);
    }

    .form-label-custom {
      font-size: 0.78rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      color: var(--addon-dark);
      margin-bottom: 6px;
    }

    .form-control-custom {
      border: 1.5px solid var(--addon-border);
      border-radius: 12px;
      padding: 10px 14px;
      font-size: 0.95rem;
      font-weight: 600;
      color: var(--addon-dark);
      background: #f8fafc;
      transition: all 0.2s ease;
    }

    .form-control-custom:focus {
      background: #ffffff;
      border-color: var(--addon-primary);
      box-shadow: 0 0 0 4px rgba(255, 94, 20, 0.12);
      outline: none;
    }

    /* Food Type Radio Cards in Modal */
    .foodtype-radio-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 12px;
    }

    .foodtype-card-label {
      border: 1.5px solid var(--addon-border);
      border-radius: 12px;
      padding: 12px 10px;
      text-align: center;
      cursor: pointer;
      background: #f8fafc;
      transition: all 0.2s ease;
      display: flex;
      flex-direction: row;
      align-items: center;
      justify-content: center;
      gap: 8px;
      margin-bottom: 0;
    }

    .foodtype-card-label input {
      display: none;
    }

    .foodtype-card-label:hover {
      border-color: #cbd5e1;
      background: #ffffff;
    }

    .foodtype-card-label.selected.veg {
      border-color: #10b981;
      background: #ecfdf5;
      color: #047857;
      font-weight: 800;
    }

    .foodtype-card-label.selected.nonveg {
      border-color: #ef4444;
      background: #fef2f2;
      color: #b91c1c;
      font-weight: 800;
    }

    /* Drag & Drop Upload Zone */
    .excel-upload-zone {
      border: 2px dashed #cbd5e1;
      border-radius: 16px;
      padding: 32px 20px;
      text-align: center;
      background: #f8fafc;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .excel-upload-zone:hover {
      border-color: var(--addon-primary);
      background: var(--addon-primary-light);
    }
  </style>
</head>

<body>
@include('includes.sidebar')

<div class="pc-container">
  <div class="pc-content">
    
    {{-- Flash Notifications --}}
    @include('includes.message')

    {{-- Page Header --}}
    <div class="addon-page-header">
      <div class="addon-title-box">
        <h3>
          <i class="fa-solid fa-puzzle-piece text-primary"></i> Dish Addon Master
        </h3>
        <p>Configure extra toppings, dips, sides, and customizations for your dishes</p>
      </div>

      <div class="addon-header-actions">
        <a href="{{ route('addon.template.download') }}" class="btn-addon-outline">
          <i class="fa-solid fa-file-excel text-success"></i> Download Template
        </a>
        @if(auth()->user()->hasPermission('dish_addon_master', 'add') || auth()->user()->hasPermission('menu_master', 'add'))
        <button type="button" class="btn-addon-outline" data-bs-toggle="modal" data-bs-target="#bulkUploadModal">
          <i class="fa-solid fa-cloud-arrow-up text-primary"></i> Bulk Upload Excel
        </button>
        <button type="button" class="btn-addon-primary text-white" data-bs-toggle="modal" data-bs-target="#addAddonModal" style="color: #ffffff !important;">
          <i class="fa-solid fa-circle-plus text-white"></i> <span style="color: #ffffff !important;">+ Add New Dish Addon</span>
        </button>
        @endif
      </div>
    </div>

    {{-- Summary Stats Deck --}}
    <div class="addon-stats-grid">
      <div class="addon-stat-card">
        <div class="stat-icon-wrap icon-total">
          <i class="fa-solid fa-list-check"></i>
        </div>
        <div>
          <div class="stat-val-num">{{ $totalCount }}</div>
          <div class="stat-val-label">Total Addons</div>
        </div>
      </div>

      <div class="addon-stat-card">
        <div class="stat-icon-wrap icon-veg">
          <i class="fa-solid fa-seedling"></i>
        </div>
        <div>
          <div class="stat-val-num">{{ $vegCount }}</div>
          <div class="stat-val-label">Veg Items</div>
        </div>
      </div>

      <div class="addon-stat-card">
        <div class="stat-icon-wrap icon-nonveg">
          <i class="fa-solid fa-drumstick-bite"></i>
        </div>
        <div>
          <div class="stat-val-num">{{ $nonVegCount }}</div>
          <div class="stat-val-label">Non-Veg Items</div>
        </div>
      </div>

      <div class="addon-stat-card">
        <div class="stat-icon-wrap icon-active">
          <i class="fa-solid fa-circle-check"></i>
        </div>
        <div>
          <div class="stat-val-num">{{ $activeCount }}</div>
          <div class="stat-val-label">Active Addons</div>
        </div>
      </div>
    </div>

    {{-- Main Content Table Card --}}
    <div class="addon-main-card">
      
      {{-- Filter Toolbar --}}
      <div class="addon-filter-toolbar">
        <form method="GET" action="{{ route('addon.index') }}" class="row g-2 align-items-center">
          <div class="col-md-5">
            <div class="input-group">
              <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px;">
                <i class="fa-solid fa-magnifying-glass text-muted"></i>
              </span>
              <input type="text" name="search" class="form-control form-control-custom border-start-0" 
                     placeholder="Search addon by name or description..." value="{{ request('search') }}"
                     style="border-radius: 0 12px 12px 0;">
            </div>
          </div>

          <div class="col-md-3">
            <select name="food_type" class="form-select form-control-custom" onchange="this.form.submit()">
              <option value="">All Food Types</option>
              <option value="VEG" {{ request('food_type') == 'VEG' ? 'selected' : '' }}>🟢 Veg</option>
              <option value="NON-VEG" {{ request('food_type') == 'NON-VEG' ? 'selected' : '' }}>🔴 Non-Veg</option>
            </select>
          </div>

          <div class="col-md-2">
            <select name="status" class="form-select form-control-custom" onchange="this.form.submit()">
              <option value="">All Statuses</option>
              <option value="A" {{ request('status') == 'A' ? 'selected' : '' }}>Active</option>
              <option value="I" {{ request('status') == 'I' ? 'selected' : '' }}>Inactive</option>
            </select>
          </div>

          <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-dark w-100 rounded-pill fw-bold">
              Filter
            </button>
            @if(request()->hasAny(['search', 'food_type', 'status']))
              <a href="{{ route('addon.index') }}" class="btn btn-outline-secondary rounded-pill px-3" title="Clear Filters">
                <i class="fa-solid fa-rotate-left"></i>
              </a>
            @endif
          </div>
        </form>
      </div>

      {{-- Table --}}
      <div class="table-responsive">
        <table class="addon-table">
          <thead>
            <tr>
              <th width="60">#</th>
              <th>Addon Name</th>
              <th>Description</th>
              <th>Food Type</th>
              <th class="text-end">Base Price</th>
              <th class="text-center" width="120">Status</th>
              <th class="text-center" width="120">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($addons as $idx => $addon)
            <tr id="addonRow_{{ $addon->id }}">
              <td class="text-muted font-monospace">{{ $addons->firstItem() + $idx }}</td>
              <td>
                <strong class="text-dark d-block fs-6">{{ $addon->name }}</strong>
              </td>
              <td>
                @if($addon->description)
                  <span class="text-muted small">{{ $addon->description }}</span>
                @else
                  <span class="text-muted small fst-italic">—</span>
                @endif
              </td>
              <td>
                @if($addon->food_type == 'NON-VEG')
                  <span class="food-type-pill pill-nonveg">
                    <span class="food-type-dot nonveg"></span> Non-Veg
                  </span>
                @else
                  <span class="food-type-pill pill-veg">
                    <span class="food-type-dot veg"></span> Veg
                  </span>
                @endif
              </td>
              <td class="text-end">
                <span class="price-tag">₹{{ number_format($addon->price, 2) }}</span>
              </td>
              <td class="text-center">
                <div class="form-check form-switch d-inline-block">
                  <input class="form-check-input toggle-status-switch" type="checkbox" role="switch"
                         data-id="{{ $addon->id }}" {{ $addon->status == 'A' ? 'checked' : '' }}
                         @if(!auth()->user()->hasPermission('dish_addon_master', 'edit') && !auth()->user()->hasPermission('menu_master', 'edit')) disabled @endif
                         style="cursor: pointer; width: 38px; height: 20px;">
                </div>
              </td>
              <td class="text-center">
                <div class="d-inline-flex gap-1">
                  @if(auth()->user()->hasPermission('dish_addon_master', 'edit') || auth()->user()->hasPermission('menu_master', 'edit'))
                  <button type="button" class="btn-table-action btn-edit btnEditAddon" data-id="{{ $addon->id }}" title="Edit Addon">
                    <i class="fa-solid fa-pen-to-square"></i>
                  </button>
                  @endif
                  @if(auth()->user()->hasPermission('dish_addon_master', 'delete') || auth()->user()->hasPermission('menu_master', 'delete'))
                  <button type="button" class="btn-table-action btn-delete btnDeleteAddon" data-id="{{ $addon->id }}" data-name="{{ $addon->name }}" title="Delete Addon">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                  @endif
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="7" class="text-center py-5">
                <div class="text-muted">
                  <i class="fa-solid fa-puzzle-piece fa-3x mb-3 text-muted opacity-50"></i>
                  <h5 class="fw-bold text-dark">No Dish Addons Found</h5>
                  <p class="small text-muted mb-3">Add customizations like extra cheese, dips, patties, or sides to enhance your menu orders.</p>
                  <button type="button" class="btn-addon-primary text-white" data-bs-toggle="modal" data-bs-target="#addAddonModal" style="color: #ffffff !important;">
                    <i class="fa-solid fa-circle-plus text-white"></i> <span style="color: #ffffff !important;">+ Add New Dish Addon</span>
                  </button>
                </div>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if($addons->hasPages())
        <div class="p-3 border-top d-flex justify-content-end">
          {{ $addons->links() }}
        </div>
      @endif

    </div>

  </div>
</div>

<!-- Modal 1: Add Addon Modal -->
<div class="modal fade" id="addAddonModal" tabindex="-1" aria-labelledby="addAddonModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-content-premium">
      <div class="modal-header modal-header-premium" style="color: #ffffff !important;">
        <h5 class="modal-title text-white" id="addAddonModalLabel" style="color: #ffffff !important;">
          <i class="fa-solid fa-circle-plus text-primary"></i> <span style="color: #ffffff !important;">Add New Dish Addon</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('addon.store') }}" method="POST" id="addAddonForm">
        @csrf
        <div class="modal-body modal-body-premium">
          
          {{-- Name --}}
          <div class="mb-3">
            <label class="form-label-custom">Addon Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control form-control-custom" required placeholder="e.g. Extra Cheese Slice / Peri Peri Mayo" autofocus>
          </div>

          {{-- Base Price --}}
          <div class="mb-3">
            <label class="form-label-custom">Base Price (₹) <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text bg-light fw-bold" style="border: 1.5px solid var(--addon-border); border-right: none; border-radius: 12px 0 0 12px;">₹</span>
              <input type="number" step="0.01" min="0" name="price" class="form-control form-control-custom font-monospace" required placeholder="0.00" style="border-radius: 0 12px 12px 0;">
            </div>
          </div>

          {{-- Food Type --}}
          <div class="mb-3">
            <label class="form-label-custom">Food Type <span class="text-danger">*</span></label>
            <div class="foodtype-radio-grid">
              <label class="foodtype-card-label selected veg" id="addTypeVegLabel">
                <input type="radio" name="food_type" value="VEG" checked onchange="updateFoodTypeSelection('add', 'VEG')">
                <span class="food-type-dot veg"></span>
                <span class="fw-bold">Veg</span>
              </label>
              <label class="foodtype-card-label" id="addTypeNonVegLabel">
                <input type="radio" name="food_type" value="NON-VEG" onchange="updateFoodTypeSelection('add', 'NON-VEG')">
                <span class="food-type-dot nonveg"></span>
                <span class="fw-bold">Non-Veg</span>
              </label>
            </div>
          </div>

          {{-- Description --}}
          <div class="mb-3">
            <label class="form-label-custom">Description <span class="text-muted fw-normal">(Optional)</span></label>
            <textarea name="description" class="form-control form-control-custom" rows="3" placeholder="Brief note about this addon or topping..."></textarea>
          </div>

          {{-- Status --}}
          <div>
            <label class="form-label-custom">Initial Status</label>
            <select name="status" class="form-select form-control-custom">
              <option value="A" selected>Active (Available for orders)</option>
              <option value="I">Inactive (Hidden)</option>
            </select>
          </div>

        </div>
        <div class="modal-footer modal-footer-premium">
          <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn-addon-primary">
            <i class="fa-solid fa-check"></i> Save Addon
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal 2: Edit Addon Modal -->
<div class="modal fade" id="editAddonModal" tabindex="-1" aria-labelledby="editAddonModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-content-premium">
      <div class="modal-header modal-header-premium" style="color: #ffffff !important;">
        <h5 class="modal-title text-white" id="editAddonModalLabel" style="color: #ffffff !important;">
          <i class="fa-solid fa-pen-to-square text-primary"></i> <span style="color: #ffffff !important;">Edit Dish Addon</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editAddonForm" method="POST">
        @csrf
        <div class="modal-body modal-body-premium">
          
          {{-- Name --}}
          <div class="mb-3">
            <label class="form-label-custom">Addon Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="edit_addon_name" class="form-control form-control-custom" required>
          </div>

          {{-- Base Price --}}
          <div class="mb-3">
            <label class="form-label-custom">Base Price (₹) <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text bg-light fw-bold" style="border: 1.5px solid var(--addon-border); border-right: none; border-radius: 12px 0 0 12px;">₹</span>
              <input type="number" step="0.01" min="0" name="price" id="edit_addon_price" class="form-control form-control-custom font-monospace" required style="border-radius: 0 12px 12px 0;">
            </div>
          </div>

          {{-- Food Type --}}
          <div class="mb-3">
            <label class="form-label-custom">Food Type <span class="text-danger">*</span></label>
            <div class="foodtype-radio-grid">
              <label class="foodtype-card-label" id="editTypeVegLabel">
                <input type="radio" name="food_type" id="edit_type_veg" value="VEG" onchange="updateFoodTypeSelection('edit', 'VEG')">
                <span class="food-type-dot veg"></span>
                <span class="fw-bold">Veg</span>
              </label>
              <label class="foodtype-card-label" id="editTypeNonVegLabel">
                <input type="radio" name="food_type" id="edit_type_nonveg" value="NON-VEG" onchange="updateFoodTypeSelection('edit', 'NON-VEG')">
                <span class="food-type-dot nonveg"></span>
                <span class="fw-bold">Non-Veg</span>
              </label>
            </div>
          </div>

          {{-- Description --}}
          <div class="mb-3">
            <label class="form-label-custom">Description <span class="text-muted fw-normal">(Optional)</span></label>
            <textarea name="description" id="edit_addon_description" class="form-control form-control-custom" rows="3"></textarea>
          </div>

          {{-- Status --}}
          <div>
            <label class="form-label-custom">Status</label>
            <select name="status" id="edit_addon_status" class="form-select form-control-custom">
              <option value="A">Active (Available)</option>
              <option value="I">Inactive (Hidden)</option>
            </select>
          </div>

        </div>
        <div class="modal-footer modal-footer-premium">
          <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn-addon-primary">
            <i class="fa-solid fa-check"></i> Update Addon
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal 3: Bulk Upload Excel Modal -->
<div class="modal fade" id="bulkUploadModal" tabindex="-1" aria-labelledby="bulkUploadModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-content-premium">
      <div class="modal-header modal-header-premium" style="color: #ffffff !important;">
        <h5 class="modal-title text-white" id="bulkUploadModalLabel" style="color: #ffffff !important;">
          <i class="fa-solid fa-cloud-arrow-up text-primary"></i> <span style="color: #ffffff !important;">Bulk Upload Dish Addons</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('addon.bulk.upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body modal-body-premium">
          
          <div class="mb-3 p-3 rounded" style="background:#f8fafc; border: 1px solid var(--addon-border);">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span class="fw-bold small text-dark"><i class="fa-solid fa-circle-info text-primary me-1"></i> Excel Format Guidelines:</span>
              <a href="{{ route('addon.template.download') }}" class="btn btn-sm btn-outline-success rounded-pill fw-bold">
                <i class="fa-solid fa-download me-1"></i> Sample Template
              </a>
            </div>
            <ul class="text-muted small mb-0 ps-3">
              <li>Column 1: <strong>Addon Name</strong> (Required)</li>
              <li>Column 2: <strong>Base Price (₹)</strong> (Required, e.g. <code>30</code> or <code>45.50</code>)</li>
              <li>Column 3: <strong>Food Type</strong> (Required: <code>VEG</code> or <code>NON-VEG</code>)</li>
              <li>Column 4: <strong>Description</strong> (Optional)</li>
            </ul>
          </div>

          <div class="mb-3">
            <label class="form-label-custom" for="bulkFileInput">Select Excel / CSV File <span class="text-danger">*</span></label>
            
            <label for="bulkFileInput" class="excel-upload-zone d-block mb-2" id="excelDropZone" style="cursor: pointer;">
              <i class="fa-solid fa-cloud-arrow-up fa-2x text-primary mb-2"></i>
              <div class="fw-bold text-dark" id="fileNameDisplay">Click to Browse or Drag &amp; Drop Excel File Here</div>
              <div class="text-muted small">Supports .xlsx, .xls, .csv files up to 10MB</div>
            </label>

            <input type="file" name="bulk_file" id="bulkFileInput" class="form-control form-control-custom" accept=".xlsx,.xls,.csv" required onchange="handleFileSelected(this)">
            <small class="text-muted d-block mt-1" style="font-size: 0.76rem;">Supported file formats: .xlsx, .xls, .csv (Maximum file size: 10MB)</small>
          </div>

        </div>
        <div class="modal-footer modal-footer-premium">
          <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn-addon-primary">
            <i class="fa-solid fa-upload"></i> Upload &amp; Import
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@include('includes.script')

<script>
function updateFoodTypeSelection(modalPrefix, type) {
    $(`#${modalPrefix}TypeVegLabel, #${modalPrefix}TypeNonVegLabel`).removeClass('selected veg nonveg');
    
    if (type === 'NON-VEG') {
        $(`#${modalPrefix}TypeNonVegLabel`).addClass('selected nonveg');
    } else {
        $(`#${modalPrefix}TypeVegLabel`).addClass('selected veg');
    }
}

function handleFileSelected(input) {
    if (input.files && input.files[0]) {
        $('#fileNameDisplay').html(`<span class="text-success fw-bold"><i class="fa-solid fa-file-circle-check me-1"></i> Selected: ${input.files[0].name}</span>`);
    } else {
        $('#fileNameDisplay').html('Click to Browse or Drag &amp; Drop Excel File Here');
    }
}

$(document).ready(function() {
    // Edit Addon Modal Load
    $('.btnEditAddon').on('click', function() {
        let addonId = $(this).data('id');
        let editUrl = "{{ route('addon.edit', ':id') }}".replace(':id', addonId);
        let updateUrl = "{{ route('addon.update', ':id') }}".replace(':id', addonId);

        $('#editAddonForm').attr('action', updateUrl);

        $.ajax({
            url: editUrl,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success && res.addon) {
                    let a = res.addon;
                    $('#edit_addon_name').val(a.name);
                    $('#edit_addon_price').val(parseFloat(a.price).toFixed(2));
                    $('#edit_addon_description').val(a.description || '');
                    $('#edit_addon_status').val(a.status);

                    let foodType = (a.food_type || 'VEG').toUpperCase();
                    if (foodType === 'NON-VEG') {
                        $('#edit_type_nonveg').prop('checked', true);
                    } else {
                        $('#edit_type_veg').prop('checked', true);
                    }
                    updateFoodTypeSelection('edit', foodType);

                    let modal = new bootstrap.Modal(document.getElementById('editAddonModal'));
                    modal.show();
                }
            },
            error: function() {
                alert('Error loading addon details');
            }
        });
    });

    // Toggle Status Switch
    $('.toggle-status-switch').on('change', function() {
        let addonId = $(this).data('id');
        let toggleUrl = "{{ route('addon.toggle.status', ':id') }}".replace(':id', addonId);
        let checkbox = $(this);

        $.ajax({
            url: toggleUrl,
            type: 'POST',
            data: { _token: "{{ csrf_token() }}" },
            dataType: 'json',
            success: function(res) {
                if (!res.success) {
                    checkbox.prop('checked', !checkbox.prop('checked'));
                }
            },
            error: function() {
                checkbox.prop('checked', !checkbox.prop('checked'));
                alert('Error updating status');
            }
        });
    });

    // Delete Addon with confirmation
    $('.btnDeleteAddon').on('click', function() {
        let addonId = $(this).data('id');
        let addonName = $(this).data('name');
        let deleteUrl = "{{ route('addon.destroy', ':id') }}".replace(':id', addonId);

        if (confirm(`Are you sure you want to delete addon "${addonName}"?`)) {
            $.ajax({
                url: deleteUrl,
                type: 'DELETE',
                data: { _token: "{{ csrf_token() }}" },
                dataType: 'json',
                success: function(res) {
                    if (res.success) {
                        $(`#addonRow_${addonId}`).fadeOut(300, function() { $(this).remove(); });
                    } else {
                        alert(res.message || 'Error deleting addon');
                    }
                },
                error: function() {
                    alert('Error deleting addon');
                }
            });
        }
    });

    // Drag and Drop Zone support
    let dropZone = document.getElementById('excelDropZone');
    let fileInput = document.getElementById('bulkFileInput');
    if (dropZone && fileInput) {
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                $(dropZone).css('border-color', '#ff5e14').css('background', '#fff3ed');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                $(dropZone).css('border-color', '#cbd5e1').css('background', '#f8fafc');
            }, false);
        });

        dropZone.addEventListener('drop', (e) => {
            let dt = e.dataTransfer;
            let files = dt.files;
            if (files && files.length) {
                fileInput.files = files;
                handleFileSelected(fileInput);
            }
        }, false);
    }
});
</script>
</body>
</html>
