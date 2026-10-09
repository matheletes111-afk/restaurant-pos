@extends('layouts.app')

@section('title')
<title>Admin - Manage Products</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="{{ asset('admin_template/css/inventory-modules.css') }}">
@endsection

@section('body')
@include('includes.sidebar')

<div class="pc-container">
<div class="pc-content">

    <div class="inv-page-wrap">
        {{-- Flash / Error Messages --}}
        @include('includes.message')

        @if(session('import_errors'))
        <div class="alert alert-danger border-0 shadow-sm p-3 mb-4" style="border-radius: 14px;">
            <h6 class="fw-bold mb-2"><i class="fas fa-exclamation-triangle me-1"></i> Import Errors:</h6>
            <ul class="mb-0 ps-3">
                @foreach(session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- 1. Header Deck --}}
        <div class="inv-header-deck">
            <div class="inv-header-left">
                <div class="inv-header-icon icon-products">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="inv-header-title-meta">
                    <span class="inv-header-eyebrow">Inventory Master</span>
                    <h1 class="inv-header-title">Raw Materials & Products</h1>
                    <p class="inv-header-sub">Manage raw ingredients, stock items, units of measurement, and opening stock</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('products.export') }}" class="btn-inv-secondary" title="Export all products to Excel">
                    <i class="fas fa-file-export text-primary"></i>
                    <span>Export</span>
                </a>
                @if(auth()->user()->hasPermission('inventory_setting', 'add'))
                <button type="button" class="btn-inv-secondary" data-bs-toggle="modal" data-bs-target="#bulkUploadModal">
                    <i class="fas fa-file-arrow-up text-success"></i>
                    <span>Bulk Upload</span>
                </button>
                <button type="button" class="btn-inv-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-plus"></i>
                    <span>Add Product</span>
                </button>
                @endif
            </div>
        </div>

        {{-- 2. Stats Grid --}}
        @php
            $totalProducts = $products->count();
            $totalUnitsCount = $units->count();
            $totalOpeningQty = $products->sum('opening_qty');
        @endphp
        <div class="inv-stats-grid">
            <div class="inv-stat-card stat-purple">
                <div class="inv-stat-info">
                    <span class="inv-stat-label">Total Items</span>
                    <span class="inv-stat-val">{{ $totalProducts }}</span>
                </div>
                <div class="inv-stat-icon">
                    <i class="fas fa-cubes"></i>
                </div>
            </div>
            <div class="inv-stat-card stat-info">
                <div class="inv-stat-info">
                    <span class="inv-stat-label">Units Configured</span>
                    <span class="inv-stat-val">{{ $totalUnitsCount }}</span>
                </div>
                <div class="inv-stat-icon">
                    <i class="fas fa-ruler-combined"></i>
                </div>
            </div>
            <div class="inv-stat-card stat-success">
                <div class="inv-stat-info">
                    <span class="inv-stat-label">Total Opening Qty</span>
                    <span class="inv-stat-val text-success">{{ number_format($totalOpeningQty, 2) }}</span>
                </div>
                <div class="inv-stat-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
            </div>
            <div class="inv-stat-card stat-dark">
                <div class="inv-stat-info">
                    <span class="inv-stat-label">Stock Status</span>
                    <span class="inv-stat-val text-primary" style="font-size: 1.3rem;">Tracked</span>
                </div>
                <div class="inv-stat-icon">
                    <i class="fas fa-warehouse"></i>
                </div>
            </div>
        </div>

        {{-- 3. Filter & Search Toolbar --}}
        <div class="inv-toolbar">
            <div class="inv-search-box">
                <i class="fas fa-search inv-search-icon"></i>
                <input type="text" id="productSearchInput" class="inv-search-input" placeholder="Search product by name or unit...">
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small fw-bold">Showing {{ $totalProducts }} Products</span>
            </div>
        </div>

        {{-- 4. Desktop Table View --}}
        <div class="inv-card">
            <div class="table-responsive">
                <table class="inv-table" id="productDesktopTable">
                    <thead>
                        <tr>
                            <th style="width: 70px;">#</th>
                            <th>Product / Raw Material</th>
                            <th>Unit of Measure</th>
                            <th>Opening Qty</th>
                            <th class="text-end" style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productTableBody">
                        @forelse($products as $key => $product)
                        <tr class="product-row" 
                            data-name="{{ strtolower($product->product_name) }}" 
                            data-unit="{{ strtolower($product->unit ? $product->unit->name : '') }}">
                            <td class="text-muted fw-bold">{{ $key + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width: 38px; height: 38px; border-radius: 10px; background: var(--inv-purple-bg); color: var(--inv-purple-text); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.95rem;">
                                        <i class="fas fa-cube"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold" style="color: var(--inv-dark); font-size: 0.95rem;">{{ $product->product_name }}</h6>
                                        <small class="text-muted">Item ID: #PRD-{{ $product->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($product->unit)
                                <span class="inv-badge badge-unit">
                                    <i class="fas fa-balance-scale me-1"></i> {{ $product->unit->name }}
                                </span>
                                @else
                                <span class="badge bg-light text-muted border">N/A</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold" style="color: var(--inv-slate);">{{ number_format($product->opening_qty ?? 0, 2) }}</span>
                                <small class="text-muted ms-1">{{ $product->unit ? $product->unit->name : '' }}</small>
                            </td>
                            <td>
                                <div class="inv-actions justify-content-end">
                                    @if(auth()->user()->hasPermission('inventory_setting', 'edit'))
                                    <button type="button" 
                                            class="inv-btn-action btn-edit editBtn"
                                            title="Edit Product"
                                            data-id="{{ $product->id }}"
                                            data-name="{{ $product->product_name }}"
                                            data-unit="{{ $product->unit_id }}"
                                            data-qty="{{ $product->opening_qty }}">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    @endif

                                    @if(auth()->user()->hasPermission('inventory_setting', 'delete'))
                                    <a href="{{ route('products.delete', $product->id) }}"
                                       class="inv-btn-action btn-del"
                                       title="Delete Product"
                                       onclick="return confirm('Are you sure you want to delete {{ addslashes($product->product_name) }}?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-boxes fa-3x mb-3 text-secondary opacity-50"></i>
                                <h6 class="fw-bold">No Raw Materials Found</h6>
                                <p class="small text-muted mb-0">Click "+ Add Product" to create your raw ingredients & supplies.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 5. Mobile Cards View --}}
        <div class="inv-mobile-cards" id="productMobileCards">
            @forelse($products as $key => $product)
            <div class="inv-mobile-item product-row" 
                 data-name="{{ strtolower($product->product_name) }}" 
                 data-unit="{{ strtolower($product->unit ? $product->unit->name : '') }}">
                <div class="inv-mobile-top">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: var(--inv-purple-bg); color: var(--inv-purple-text); display: flex; align-items: center; justify-content: center; font-weight: 800;">
                            <i class="fas fa-cube"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold" style="color: var(--inv-dark);">{{ $product->product_name }}</h6>
                            <small class="text-muted">#PRD-{{ $product->id }}</small>
                        </div>
                    </div>
                    @if($product->unit)
                    <span class="inv-badge badge-unit">
                        {{ $product->unit->name }}
                    </span>
                    @endif
                </div>

                <div class="inv-mobile-meta-row">
                    <div>
                        <span class="text-muted small">Opening Stock:</span>
                        <strong class="ms-1">{{ number_format($product->opening_qty ?? 0, 2) }} {{ $product->unit ? $product->unit->name : '' }}</strong>
                    </div>
                </div>

                <div class="inv-mobile-actions">
                    @if(auth()->user()->hasPermission('inventory_setting', 'edit'))
                    <button type="button" 
                            class="btn btn-sm btn-outline-primary rounded-pill px-3 editBtn"
                            data-id="{{ $product->id }}"
                            data-name="{{ $product->product_name }}"
                            data-unit="{{ $product->unit_id }}"
                            data-qty="{{ $product->opening_qty }}">
                        <i class="fas fa-pen me-1"></i> Edit
                    </button>
                    @endif

                    @if(auth()->user()->hasPermission('inventory_setting', 'delete'))
                    <a href="{{ route('products.delete', $product->id) }}"
                       class="btn btn-sm btn-outline-danger rounded-pill px-3"
                       onclick="return confirm('Delete this product?')">
                        <i class="fas fa-trash-alt me-1"></i> Delete
                    </a>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-4 text-muted">
                <p class="mb-0">No raw material items found.</p>
            </div>
            @endforelse
        </div>

    </div>

</div>
</div>

{{-- Add Modal --}}
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content inv-modal-content">
            <form action="{{ route('products.store') }}" method="POST">
                @csrf
                <div class="inv-modal-header">
                    <h5 class="inv-modal-title">
                        <span class="inv-stat-icon" style="width: 36px; height: 36px; font-size: 1rem; background: var(--inv-purple-bg); color: var(--inv-purple-text);">
                            <i class="fas fa-cube"></i>
                        </span>
                        <span>Add Raw Material / Product</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="inv-modal-body">
                    <div class="mb-3">
                        <label class="inv-form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="product_name" class="inv-form-control" required placeholder="e.g. Basmati Rice, Olive Oil, Paneer">
                    </div>

                    <div class="mb-3">
                        <label class="inv-form-label">Measurement Unit <span class="text-danger">*</span></label>
                        <select name="unit_id" class="inv-form-control" required>
                            <option value="">-- Select Unit --</option>
                            @foreach($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="inv-form-label">Opening Quantity</label>
                        <input type="number" name="opening_qty" class="inv-form-control" step="0.01" min="0" value="0" placeholder="0.00">
                        <small class="text-muted mt-1 d-block">Initial stock count currently on hand</small>
                    </div>
                </div>

                <div class="inv-modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-inv-primary">
                        <i class="fas fa-check-circle"></i> Save Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content inv-modal-content">
            <form action="{{ route('products.update') }}" method="POST">
                @csrf
                <input type="hidden" name="id" id="edit_id">

                <div class="inv-modal-header">
                    <h5 class="inv-modal-title">
                        <span class="inv-stat-icon" style="width: 36px; height: 36px; font-size: 1rem; background: var(--inv-purple-bg); color: var(--inv-purple-text);">
                            <i class="fas fa-edit"></i>
                        </span>
                        <span>Edit Product</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="inv-modal-body">
                    <div class="mb-3">
                        <label class="inv-form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" id="edit_name" name="product_name" class="inv-form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="inv-form-label">Measurement Unit <span class="text-danger">*</span></label>
                        <select name="unit_id" id="edit_unit" class="inv-form-control" required>
                            <option value="">-- Select Unit --</option>
                            @foreach($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="inv-form-label">Opening Quantity</label>
                        <input type="number" name="opening_qty" id="edit_qty" class="inv-form-control" step="0.01" min="0">
                    </div>
                </div>

                <div class="inv-modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-inv-primary">
                        <i class="fas fa-save"></i> Update Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Bulk Upload Products Modal --}}
<div class="modal fade" id="bulkUploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content inv-modal-content">
            <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data" id="bulkUploadProductForm">
                @csrf
                <div class="inv-modal-header">
                    <h5 class="inv-modal-title">
                        <span class="inv-stat-icon" style="width: 38px; height: 38px; font-size: 1.05rem; background: #ecfdf5; color: #047857;">
                            <i class="fas fa-file-excel"></i>
                        </span>
                        <span>Bulk Upload Raw Materials & Products</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="inv-modal-body">
                    {{-- Step 1: Download Template --}}
                    <div class="p-3 mb-3 border rounded-3 bg-light d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div>
                            <h6 class="mb-1 fw-bold text-dark"><i class="fas fa-download me-1 text-primary"></i> Step 1: Download Sample Template</h6>
                            <p class="mb-0 text-muted small">Download the pre-formatted Excel template containing example products & your active unit list.</p>
                        </div>
                        <a href="{{ route('products.download-sample') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                            <i class="fas fa-file-excel me-1"></i> Download Template (.xlsx)
                        </a>
                    </div>

                    {{-- Format Explanation Cards --}}
                    <div class="mb-3">
                        <label class="inv-form-label mb-2"><i class="fas fa-table-columns me-1 text-secondary"></i> Expected Column Structure:</label>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm small mb-2 text-center align-middle bg-white">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 35%;">Column A: Product Name <span class="text-danger">*</span></th>
                                        <th style="width: 35%;">Column B: Unit Name / ID</th>
                                        <th style="width: 30%;">Column C: Opening Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-start"><code>Basmati Rice</code></td>
                                        <td><code>Kg</code> <small class="text-muted">(or Unit ID 1)</small></td>
                                        <td><code>50.00</code></td>
                                    </tr>
                                    <tr>
                                        <td class="text-start"><code>Cooking Oil</code></td>
                                        <td><code>Liter</code> <small class="text-muted">(or Unit ID 2)</small></td>
                                        <td><code>25.00</code></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex align-items-start gap-2 text-muted small bg-light p-2 rounded-2 border">
                            <i class="fas fa-info-circle text-info mt-1"></i>
                            <div>
                                <span><strong>Smart Unit Matching:</strong> Enter unit name (e.g. <em>Kg, Pcs, Liter, Gram</em>) or the Unit ID number. If a unit name is new, it will be automatically registered for your restaurant.</span>
                            </div>
                        </div>
                    </div>

                    {{-- Step 2: Upload File --}}
                    <div class="mb-2">
                        <label class="inv-form-label"><i class="fas fa-cloud-arrow-up me-1 text-primary"></i> Step 2: Select Completed File <span class="text-danger">*</span></label>
                        <div class="border rounded-3 p-3 text-center bg-white" style="border: 2px dashed #cbd5e1 !important; cursor: pointer;" onclick="document.getElementById('bulkExcelFileInput').click();">
                            <div class="mb-2 text-success" style="font-size: 2rem;">
                                <i class="fas fa-file-excel"></i>
                            </div>
                            <h6 class="fw-bold mb-1" id="fileChosenLabel">Click here to browse or drag & drop file</h6>
                            <p class="text-muted small mb-2">Supported formats: <strong>.xlsx, .xls, .csv</strong> (Max size: 5 MB)</p>
                            <input type="file" 
                                   name="excel_file" 
                                   id="bulkExcelFileInput" 
                                   class="d-none" 
                                   accept=".xlsx,.xls,.csv" 
                                   required 
                                   onchange="if(this.files[0]) { document.getElementById('fileChosenLabel').innerHTML = '<span class=\'text-primary fw-bold\'><i class=\'fas fa-check-circle me-1\'></i> Selected: ' + this.files[0].name + '</span>'; }">
                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-3" onclick="document.getElementById('bulkExcelFileInput').click(); event.stopPropagation();">
                                Choose File
                            </button>
                        </div>
                    </div>
                </div>

                <div class="inv-modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-inv-primary" id="btnSubmitBulkProduct">
                        <i class="fas fa-cloud-arrow-up"></i> Upload & Import Products
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
@include('includes.script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Edit modal population
        const editButtons = document.querySelectorAll('.editBtn');
        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('edit_id').value = this.dataset.id || '';
                document.getElementById('edit_name').value = this.dataset.name || '';
                document.getElementById('edit_unit').value = this.dataset.unit || '';
                document.getElementById('edit_qty').value = this.dataset.qty || '0';
                const editModal = new bootstrap.Modal(document.getElementById('editModal'));
                editModal.show();
            });
        });

        // Live Search
        const searchInput = document.getElementById('productSearchInput');
        const rows = document.querySelectorAll('.product-row');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.trim().toLowerCase();
                rows.forEach(row => {
                    const name = (row.dataset.name || '').toLowerCase();
                    const unit = (row.dataset.unit || '').toLowerCase();
                    if (!query || name.includes(query) || unit.includes(query)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    });
</script>
@endsection