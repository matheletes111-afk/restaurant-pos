<!DOCTYPE html>
<html lang="en">
<head>
    <title>Waste Inventory - Restaurant POS</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('admin_template/css/inventory-modules.css') }}">
    
    <style>
        body {
            background-color: #f4f6fb;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .page-header {
            background: white;
            padding: 22px 28px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.03);
            margin-bottom: 22px;
            border: 1px solid rgba(0, 0, 0, 0.04);
        }
        .page-header h5 {
            color: #0f172a;
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 1.35rem;
            margin-bottom: 4px;
        }
        .breadcrumb-item a {
            color: #ff5e14;
            text-decoration: none;
            font-weight: 600;
        }

        /* KPI Stat Cards */
        .kpi-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .kpi-card {
            background: white;
            border-radius: 14px;
            padding: 18px 22px;
            border: 1px solid rgba(0, 0, 0, 0.04);
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.03);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .kpi-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
        }
        .kpi-card.kpi-primary::before { background: #ff5e14; }
        .kpi-card.kpi-danger::before { background: #ef4444; }
        .kpi-card.kpi-warning::before { background: #f59e0b; }
        .kpi-card.kpi-info::before { background: #0ea5e9; }

        .kpi-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            margin-bottom: 4px;
        }
        .kpi-value {
            font-family: 'Outfit', sans-serif;
            font-size: 1.55rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
        }
        .kpi-sub {
            font-size: 0.72rem;
            color: #94a3b8;
            margin-top: 3px;
        }
        .kpi-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        .kpi-primary .kpi-icon { background: rgba(255, 94, 20, 0.1); color: #ff5e14; }
        .kpi-danger .kpi-icon { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .kpi-warning .kpi-icon { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .kpi-info .kpi-icon { background: rgba(14, 165, 233, 0.1); color: #0ea5e9; }

        /* Filter Card */
        .filter-card {
            background: white;
            border-radius: 16px;
            border: 1px solid rgba(0, 0, 0, 0.04);
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03);
            margin-bottom: 24px;
            padding: 20px 24px;
        }

        .filter-card label {
            font-weight: 700;
            color: #475569;
            font-size: 0.74rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
        }

        .form-control, .form-select, select {
            background-color: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 8px 14px !important;
            font-size: 0.88rem !important;
            color: #1e293b !important;
            height: 42px !important;
        }
        .form-control:focus, select:focus {
            background-color: #ffffff !important;
            border-color: #ff5e14 !important;
            box-shadow: 0 0 0 3px rgba(255, 94, 20, 0.12) !important;
            outline: none !important;
        }

        /* Select2 */
        .select2-container--default .select2-selection--single {
            background-color: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            height: 42px !important;
            padding: 6px 12px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px !important;
            color: #1e293b !important;
        }

        /* Table Card */
        .main-card {
            background: white;
            border-radius: 16px;
            border: 1px solid rgba(0, 0, 0, 0.04);
            box-shadow: 0 6px 24px rgba(15, 23, 42, 0.03);
            overflow: hidden;
        }

        .waste-table {
            width: 100% !important;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 0;
        }
        .waste-table thead th {
            background: #0f172a;
            color: white;
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 0.74rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 14px 16px;
            border: none;
            white-space: nowrap;
        }
        .waste-table tbody td {
            padding: 13px 16px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.88rem;
            color: #1e293b;
        }
        .waste-table tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Badges */
        .badge-food {
            background: rgba(16, 185, 129, 0.12);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.25);
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.72rem;
            letter-spacing: 0.02em;
        }
        .badge-bar {
            background: rgba(245, 158, 11, 0.12);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, 0.25);
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.72rem;
            letter-spacing: 0.02em;
        }
        .badge-ref {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            font-family: monospace;
            font-size: 0.76rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
        }

        /* Buttons */
        .btn-theme {
            background: linear-gradient(135deg, #ff5e14 0%, #ff8c42 100%) !important;
            border: none !important;
            border-radius: 10px !important;
            padding: 9px 20px !important;
            font-weight: 700 !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(255, 94, 20, 0.2) !important;
            transition: all 0.2s ease !important;
        }
        .btn-theme:hover {
            background: linear-gradient(135deg, #ea580c 0%, #ff5e14 100%) !important;
            transform: translateY(-1px);
            color: white !important;
        }

        /* Quick Type Selector */
        .type-selector-pills {
            display: flex;
            gap: 12px;
            margin-bottom: 18px;
        }
        .type-pill {
            flex: 1;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #f8fafc;
        }
        .type-pill:hover {
            border-color: #cbd5e1;
            background: #ffffff;
        }
        .type-pill.active {
            border-color: #ff5e14;
            background: #fff7ed;
            color: #ea580c;
            box-shadow: 0 0 0 1px #ff5e14;
        }
        .type-pill input[type="radio"] {
            display: none;
        }
        .type-pill-title {
            font-weight: 800;
            font-size: 0.95rem;
            display: block;
        }
        .type-pill-sub {
            font-size: 0.72rem;
            color: #64748b;
        }
        .type-pill.active .type-pill-sub {
            color: #c2410c;
        }

        .stock-badge-box {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 7px 12px;
            font-size: 0.78rem;
            color: #475569;
            margin-top: 6px;
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

        <!-- Breadcrumb & Header Deck -->
        <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="m-0"><i class="fas fa-dumpster text-warning me-2"></i>Waste Inventory</h5>
                <ul class="breadcrumb m-0 mt-1 p-0 bg-transparent">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item">Inventory Setting</li>
                    <li class="breadcrumb-item active">Waste Inventory</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('waste-inventory.export-excel', request()->all()) }}" class="btn btn-outline-success" style="border-radius: 10px; font-weight: 600; padding: 9px 16px;">
                    <i class="fas fa-file-excel me-1 text-success"></i> Download Excel
                </a>
                <a href="{{ route('waste-inventory.create') }}" class="btn btn-theme">
                    <i class="fas fa-plus me-1"></i> Add Waste Entry
                </a>
                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#quickWasteModal" style="border-radius: 10px; font-weight: 600; padding: 9px 16px;">
                    <i class="fas fa-bolt me-1 text-warning"></i> Quick Add
                </button>
            </div>
        </div>

        @include('includes.message')

        <!-- KPI Summary Cards -->
        <div class="kpi-row">
            <div class="kpi-card kpi-primary">
                <div>
                    <div class="kpi-label">Total Waste Records</div>
                    <div class="kpi-value">{{ number_format($stats['total_entries']) }}</div>
                    <div class="kpi-sub">{{ $stats['food_records'] }} Food • {{ $stats['bar_records'] }} Bar</div>
                </div>
                <div class="kpi-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
            </div>

            <div class="kpi-card kpi-danger">
                <div>
                    <div class="kpi-label">Food Waste Qty</div>
                    <div class="kpi-value">{{ number_format($stats['food_waste_qty'], 2) }}</div>
                    <div class="kpi-sub">Kitchen / Store products deducted</div>
                </div>
                <div class="kpi-icon">
                    <i class="fas fa-utensils"></i>
                </div>
            </div>

            <div class="kpi-card kpi-warning">
                <div>
                    <div class="kpi-label">Bar Waste (ML)</div>
                    <div class="kpi-value">{{ number_format($stats['bar_waste_ml'], 0) }} <small style="font-size: 0.9rem;">ML</small></div>
                    <div class="kpi-sub">
                        @php
                            $approxBottles = $stats['bar_waste_ml'] > 0 ? round($stats['bar_waste_ml'] / 750, 1) : 0;
                        @endphp
                        ≈ {{ $approxBottles }} Bottles eq. (750ml)
                    </div>
                </div>
                <div class="kpi-icon">
                    <i class="fas fa-wine-bottle"></i>
                </div>
            </div>

            <div class="kpi-card kpi-info">
                <div>
                    <div class="kpi-label">Filter Period</div>
                    <div class="kpi-value" style="font-size: 1.15rem; font-weight: 700;">
                        {{ $fromDate->format('d M') }} - {{ $toDate->format('d M Y') }}
                    </div>
                    <div class="kpi-sub">Current view filter range</div>
                </div>
                <div class="kpi-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="filter-card">
            <form action="{{ route('waste-inventory.index') }}" method="GET" id="wasteFilterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3 col-sm-6">
                        <label>From Date</label>
                        <input type="date" name="from_date" class="form-control" value="{{ $fromDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label>To Date</label>
                        <input type="date" name="to_date" class="form-control" value="{{ $toDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <label>Item Type</label>
                        <select name="item_type" class="form-control">
                            <option value="">All Types</option>
                            <option value="FOOD" {{ $itemType === 'FOOD' ? 'selected' : '' }}>🍽️ Food Items</option>
                            <option value="BAR" {{ $itemType === 'BAR' ? 'selected' : '' }}>🍾 Bar Items</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <label>Keyword Search</label>
                        <input type="text" name="keyword" class="form-control" placeholder="Waste #, Product, Reason..." value="{{ $keyword ?? '' }}">
                    </div>
                    <div class="col-md-2 col-sm-12 d-flex gap-2">
                        <button type="submit" class="btn btn-theme flex-grow-1">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('waste-inventory.index') }}" class="btn btn-outline-secondary" title="Reset Filters" style="border-radius: 10px; height: 42px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Main Waste Inventory Table -->
        <div class="main-card">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-dark px-3 py-2 text-white" style="border-radius: 8px;">
                        {{ $wasteList->count() }} Waste Records
                    </span>
                    @if($hasExplicitFilter)
                        <span class="badge bg-warning text-dark px-2 py-1">Filters Active</span>
                    @else
                        <small class="text-muted"><i class="fas fa-calendar-day me-1"></i>Showing Current Month by Default</small>
                    @endif
                </div>
                <div>
                    <a href="{{ route('waste-inventory.export-excel', request()->all()) }}" class="btn btn-sm btn-outline-success" style="border-radius: 8px; font-weight: 600; padding: 6px 14px;">
                        <i class="fas fa-file-excel me-1 text-success"></i> Download Excel
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="waste-table">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="14%">Waste Ref #</th>
                            <th width="12%">Date</th>
                            <th width="12%">Item Type</th>
                            <th width="20%">Product Name</th>
                            <th width="8%">Unit</th>
                            <th width="11%" class="text-end">Waste Qty</th>
                            <th width="12%">Reason</th>
                            <th width="10%">Recorded By</th>
                            <th width="6%" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($wasteList as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <span class="badge-ref">{{ $item->waste_no }}</span>
                            </td>
                            <td>
                                <i class="far fa-calendar-alt text-muted me-1"></i>
                                {{ $item->waste_date ? $item->waste_date->format('d-m-Y') : 'N/A' }}
                            </td>
                            <td>
                                @if($item->item_type === 'BAR' || $item->sub_category_id)
                                    <span class="badge-bar">🍾 BAR ITEM</span>
                                @else
                                    <span class="badge-food">🍽️ FOOD ITEM</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $item->product->product_name ?? 'N/A' }}</strong>
                                @if($item->barItem && $item->barItem->brand)
                                    <br><small class="text-muted">{{ $item->barItem->brand->name }}</small>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $item->unit->name ?? ($item->item_type === 'BAR' ? 'ML' : 'N/A') }}</strong>
                            </td>
                            <td class="text-end">
                                <span class="text-danger fw-bold fs-6">
                                    -{{ number_format($item->waste_qty, 2) }}
                                </span>
                                @if(($item->item_type === 'BAR' || $item->sub_category_id) && $item->product && $item->product->barItem)
                                    @php
                                        $bSize = floatval($item->product->barItem->bottle_size_ml ?: 750);
                                        $bEq = $bSize > 0 ? round($item->waste_qty / $bSize, 2) : null;
                                    @endphp
                                    @if($bEq)
                                        <br><small class="text-muted">({{ $bEq }} Btl @ {{ number_format($bSize, 0) }}ml)</small>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if($item->reason)
                                    <span class="text-truncate d-inline-block" style="max-width: 180px;" title="{{ $item->reason }}">
                                        {{ $item->reason }}
                                    </span>
                                @else
                                    <span class="text-muted fst-italic">None specified</span>
                                @endif
                            </td>
                            <td>
                                <small class="fw-semibold text-secondary">
                                    <i class="far fa-user me-1"></i>{{ $item->user->name ?? 'System' }}
                                </small>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('waste-inventory.delete', $item->id) }}" 
                                   class="btn btn-sm btn-outline-danger" 
                                   title="Delete and Restore Inventory Stock"
                                   onclick="return confirm('Are you sure you want to delete this waste record ({{ $item->waste_no }})? This will restore {{ $item->waste_qty }} {{ $item->unit->name ?? 'units' }} back to the inventory.')"
                                   style="border-radius: 8px; padding: 4px 8px;">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="py-4">
                                    <i class="fas fa-trash-restore text-muted mb-3" style="font-size: 3rem; opacity: 0.4;"></i>
                                    <h6 class="text-muted fw-bold">No waste inventory records found</h6>
                                    <p class="text-muted small mb-3">No stock waste has been recorded for the selected period.</p>
                                    <button type="button" class="btn btn-theme btn-sm" data-bs-toggle="modal" data-bs-target="#quickWasteModal">
                                        <i class="fas fa-plus me-1"></i> Record New Waste
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

<!-- ================= QUICK ADD WASTE MODAL ================= -->
<div class="modal fade" id="quickWasteModal" tabindex="-1" aria-labelledby="quickWasteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 18px; border: none; box-shadow: 0 15px 40px rgba(0,0,0,0.15); overflow: hidden;">
            <div class="modal-header bg-dark text-white py-3 px-4">
                <h5 class="modal-title text-white fw-bold" id="quickWasteModalLabel">
                    <i class="fas fa-dumpster text-warning me-2"></i>Record Waste Inventory
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('waste-inventory.store') }}" method="POST" id="quickWasteForm">
                @csrf
                <div class="modal-body p-4">
                    
                    <!-- Item Type Selector Pills -->
                    <label class="form-label fw-bold text-uppercase small text-muted mb-2">1. Select Item Type *</label>
                    <div class="type-selector-pills">
                        <label class="type-pill active" id="modalPillFood">
                            <input type="radio" name="item_type" value="FOOD" checked onchange="onModalTypeChange('FOOD')">
                            <span class="type-pill-title">🍽️ Food Item</span>
                            <span class="type-pill-sub">Kitchen, groceries, raw material products</span>
                        </label>
                        <label class="type-pill" id="modalPillBar">
                            <input type="radio" name="item_type" value="BAR" onchange="onModalTypeChange('BAR')">
                            <span class="type-pill-title">🍾 Bar Item</span>
                            <span class="type-pill-sub">Liquor, bottles, bar stock (Auto Unit: ML)</span>
                        </label>
                    </div>

                    <div class="row g-3">
                        <!-- Product Dropdown -->
                        <div class="col-md-8">
                            <label class="form-label fw-bold text-uppercase small text-muted">2. Select Product *</label>
                            <select name="product_id" id="modalProductSelect" class="form-control select2" required style="width: 100%;">
                                <option value="">-- Choose Food Product --</option>
                                @foreach($foodProducts as $product)
                                    <option value="{{ $product->id }}" 
                                            data-type="FOOD"
                                            data-unit="{{ $product->unit ? $product->unit->name : 'N/A' }}"
                                            data-unit-id="{{ $product->unit_id }}">
                                        {{ $product->product_name }} ({{ $product->unit ? $product->unit->name : 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Unit -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-uppercase small text-muted">Unit</label>
                            <input type="text" id="modalUnitDisplay" class="form-control" placeholder="Select product" readonly>
                        </div>

                        <!-- Current Stock Display -->
                        <div class="col-md-12">
                            <div class="stock-badge-box d-flex justify-content-between align-items-center" id="modalStockBox">
                                <span><i class="fas fa-boxes text-secondary me-1"></i> Current In-Stock:</span>
                                <strong id="modalCurrentStockVal" class="text-dark">Select a product to view stock</strong>
                            </div>
                        </div>

                        <!-- Waste Quantity -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-uppercase small text-muted">3. Waste Quantity *</label>
                            <input type="number" name="waste_qty" id="modalWasteQty" class="form-control" step="0.01" min="0.01" placeholder="Enter quantity" required>
                        </div>

                        <!-- Date -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-uppercase small text-muted">4. Waste Date *</label>
                            <input type="date" name="waste_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <!-- Reason -->
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-uppercase small text-muted">5. Reason / Remarks</label>
                            <textarea name="reason" id="modalReason" class="form-control" rows="2" placeholder="e.g. Expired, spoiled, damaged container, spillage, quality issue..."></textarea>
                            <div class="mt-2 d-flex flex-wrap gap-1">
                                <span class="badge bg-light text-secondary border cursor-pointer" onclick="quickFillReason('Expired / Past shelf life')">Expired</span>
                                <span class="badge bg-light text-secondary border cursor-pointer" onclick="quickFillReason('Spoiled / Quality issue')">Spoiled</span>
                                <span class="badge bg-light text-secondary border cursor-pointer" onclick="quickFillReason('Spillage / Accidental loss')">Spillage</span>
                                <span class="badge bg-light text-secondary border cursor-pointer" onclick="quickFillReason('Broken bottle / Damaged pack')">Broken Bottle</span>
                                <span class="badge bg-light text-secondary border cursor-pointer" onclick="quickFillReason('Kitchen preparation error')">Kitchen Error</span>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-theme px-4">
                        <i class="fas fa-minus-circle me-1"></i> Deduct & Save Waste
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Products JSON for dynamic switching between Food and Bar Items -->
<script>
    const foodProductsData = @json($foodProducts);
    const barProductsData = @json($barProducts);
</script>

<!-- JS Dependencies -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@include('includes.script')

<script>
    $(document).ready(function() {
        // Initialize modal Select2
        $('#modalProductSelect').select2({
            dropdownParent: $('#quickWasteModal'),
            placeholder: '-- Select Product --',
            allowClear: true
        });

        $('#modalProductSelect').on('change', function() {
            const productId = $(this).val();
            fetchProductStockInfo(productId);
        });
    });

    function onModalTypeChange(type) {
        if (type === 'FOOD') {
            $('#modalPillFood').addClass('active');
            $('#modalPillBar').removeClass('active');
            populateProductsDropdown(foodProductsData, 'FOOD');
        } else {
            $('#modalPillBar').addClass('active');
            $('#modalPillFood').removeClass('active');
            populateProductsDropdown(barProductsData, 'BAR');
        }
    }

    function populateProductsDropdown(products, type) {
        const select = $('#modalProductSelect');
        select.empty();
        select.append(new Option(`-- Select ${type === 'BAR' ? 'Bar Item' : 'Food Product'} --`, ''));

        products.forEach(p => {
            const unitName = type === 'BAR' ? 'ML' : (p.unit ? p.unit.name : 'N/A');
            const label = type === 'BAR' 
                ? `🍾 ${p.product_name} (${unitName})` 
                : `${p.product_name} (${unitName})`;
            const option = new Option(label, p.id, false, false);
            $(option).attr('data-unit', unitName);
            select.append(option);
        });

        select.val('').trigger('change');
        $('#modalUnitDisplay').val('');
        $('#modalCurrentStockVal').text('Select a product to view stock').removeClass('text-success text-danger text-warning').addClass('text-dark');
    }

    function fetchProductStockInfo(productId) {
        if (!productId) {
            $('#modalUnitDisplay').val('');
            $('#modalCurrentStockVal').text('Select a product to view stock').removeClass('text-success text-danger text-warning').addClass('text-dark');
            return;
        }

        $.ajax({
            url: '{{ route("waste-inventory.get-product", "") }}/' + productId,
            type: 'GET',
            success: function(res) {
                if (res.success) {
                    $('#modalUnitDisplay').val(res.unit_name);
                    const stock = parseFloat(res.current_stock) || 0;

                    let stockText = `${stock.toFixed(2)} ${res.unit_name}`;
                    if (res.is_bar && res.bottle_count !== null) {
                        stockText += ` (${res.bottle_count} Bottles @ ${res.bottle_size_ml}ml)`;
                    }

                    const stockEl = $('#modalCurrentStockVal');
                    stockEl.text(stockText);
                    stockEl.removeClass('text-dark text-success text-danger text-warning');

                    if (stock <= 0) {
                        stockEl.addClass('text-danger').append(' (Out of stock)');
                    } else {
                        stockEl.addClass('text-success');
                    }
                }
            }
        });
    }

    function quickFillReason(text) {
        $('#modalReason').val(text);
    }
</script>

</body>
</html>

