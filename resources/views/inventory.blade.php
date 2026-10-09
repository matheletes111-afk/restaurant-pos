@extends('layouts.app')

@section('title')
<title>Admin - Live Inventory & Stock Status</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<!-- FontAwesome 6.5 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="{{ asset('admin_template/css/inventory-modules.css') }}">
<style>
    .live-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.76rem;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 20px;
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }
    .live-status-pill .pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #10b981;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: livePulse 1.8s infinite;
    }
    @keyframes livePulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 7px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
    .stock-meter-wrap {
        display: flex;
        flex-direction: column;
        gap: 4px;
        min-width: 140px;
    }
    .stock-meter-bar {
        height: 6px;
        width: 100%;
        background-color: #e2e8f0;
        border-radius: 4px;
        overflow: hidden;
    }
    .stock-meter-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.4s ease;
    }
    .stock-meter-fill.fill-good {
        background: linear-gradient(90deg, #10b981, #059669);
    }
    .stock-meter-fill.fill-low {
        background: linear-gradient(90deg, #f59e0b, #d97706);
    }
    .stock-meter-fill.fill-out {
        background: linear-gradient(90deg, #ef4444, #dc2626);
    }
    .quick-status-tab {
        font-size: 0.8rem;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 20px;
        background: #f1f5f9;
        color: var(--inv-slate);
        border: 1px solid var(--inv-border);
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
    }
    .quick-status-tab:hover {
        background: #e2e8f0;
        color: var(--inv-dark);
    }
    .quick-status-tab.active {
        background: var(--inv-primary);
        color: #ffffff;
        border-color: var(--inv-primary);
        box-shadow: 0 2px 8px rgba(255, 94, 20, 0.25);
    }
    .quick-status-tab.tab-good.active {
        background: #10b981;
        border-color: #10b981;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);
    }
    .quick-status-tab.tab-low.active {
        background: #f59e0b;
        border-color: #f59e0b;
        box-shadow: 0 2px 8px rgba(245, 158, 11, 0.25);
    }
    .quick-status-tab.tab-out.active {
        background: #ef4444;
        border-color: #ef4444;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.25);
    }
    .stock-badge-pill {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.74rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .badge-pill-success {
        background-color: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }
    .badge-pill-warning {
        background-color: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .badge-pill-danger {
        background-color: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
    .badge-pill-info {
        background-color: #f0f9ff;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }
    .btn-action-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        transition: all 0.2s ease;
        text-decoration: none !important;
        border: none;
    }
    .btn-action-icon:hover {
        transform: translateY(-2px);
    }
    .btn-action-restock {
        background: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .btn-action-restock:hover {
        background: #f59e0b;
        color: #ffffff;
    }
    .btn-action-purchase {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }
    .btn-action-purchase:hover {
        background: #10b981;
        color: #ffffff;
    }
    .btn-action-stockout {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
    .btn-action-stockout:hover {
        background: #ef4444;
        color: #ffffff;
    }
    .product-avatar-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #f1f5f9;
        color: #475569;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        flex-shrink: 0;
    }
    .stock-qty-display {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 1.05rem;
        line-height: 1.2;
    }
    .qty-good { color: #047857; }
    .qty-low { color: #b45309; }
    .qty-out { color: #b91c1c; }

    /* Live Stock Table Section Padding & Refinements */
    .inv-card {
        background: #ffffff !important;
        border-radius: 16px !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04) !important;
        margin-bottom: 24px !important;
        overflow: hidden !important;
    }
    .inv-card-header {
        padding: 22px 28px !important;
        background: #ffffff !important;
        border-bottom: 1px solid #edf2f7 !important;
    }
    .inv-card-title {
        font-family: 'Outfit', sans-serif !important;
        font-size: 1.22rem !important;
        font-weight: 800 !important;
        color: #0f172a !important;
        letter-spacing: -0.01em !important;
        margin: 0 !important;
    }
    .inv-card-subtitle {
        font-size: 0.84rem !important;
        color: #64748b !important;
        margin: 0 !important;
    }
    .dataTables-toolbar-deck {
        padding: 16px 28px !important;
        background: #fbfcfe !important;
        border-bottom: 1px solid #edf2f7 !important;
    }
    .dataTables-footer-deck {
        padding: 16px 28px !important;
        background: #ffffff !important;
        border-top: 1px solid #edf2f7 !important;
    }
    #liveInventoryTable {
        width: 100% !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
        margin: 0 !important;
    }
    #liveInventoryTable thead th {
        background: #f8fafc !important;
        padding: 16px 24px !important;
        font-family: 'Outfit', sans-serif !important;
        font-size: 0.78rem !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.06em !important;
        color: #475569 !important;
        border-bottom: 2px solid #e2e8f0 !important;
        border-top: none !important;
        white-space: nowrap !important;
    }
    #liveInventoryTable tbody td {
        padding: 18px 24px !important;
        border-bottom: 1px solid #f1f5f9 !important;
        vertical-align: middle !important;
        font-size: 0.88rem !important;
        color: #1e293b !important;
    }
    #liveInventoryTable tbody tr:last-child td {
        border-bottom: none !important;
    }
    #liveInventoryTable tbody tr:hover td {
        background-color: #fafcff !important;
    }
    .dataTables_filter input {
        border-radius: 30px !important;
        border: 1px solid #cbd5e1 !important;
        padding: 7px 16px !important;
        font-size: 0.85rem !important;
        outline: none !important;
        margin-left: 8px !important;
        background: #ffffff !important;
        transition: all 0.2s ease !important;
    }
    .dataTables_filter input:focus {
        border-color: #ff5e14 !important;
        box-shadow: 0 0 0 3px rgba(255, 94, 20, 0.12) !important;
    }
    .dataTables_length select {
        border-radius: 20px !important;
        border: 1px solid #cbd5e1 !important;
        padding: 5px 10px !important;
        font-size: 0.84rem !important;
        margin: 0 6px !important;
    }
    @media (max-width: 768px) {
        .inv-card-header,
        .dataTables-toolbar-deck,
        .dataTables-footer-deck {
            padding: 16px 18px !important;
        }
        #liveInventoryTable thead th,
        #liveInventoryTable tbody td {
            padding: 14px 16px !important;
        }
    }
</style>
@endsection

@section('body')
@include('includes.sidebar')

<div class="pc-container">
<div class="pc-content">

    <div class="inv-page-wrap">
        {{-- Flash / Error Messages --}}
        @include('includes.message')

        {{-- 1. Header Deck --}}
        <div class="inv-header-deck">
            <div class="inv-header-left">
                <div class="inv-header-icon icon-live">
                    <i class="fas fa-boxes-stacked"></i>
                </div>
                <div class="inv-header-title-meta">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="inv-header-eyebrow">Real-Time Inventory</span>
                        <div class="live-status-pill">
                            <span class="pulse-dot"></span> Live Sync
                        </div>
                    </div>
                    <h1 class="inv-header-title">Live Stocks & Inventory</h1>
                    <p class="inv-header-sub">Instant tracking of on-hand raw materials, stock thresholds, and rapid reordering.</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <button type="button" onclick="refreshInventory()" class="btn-inv-secondary refresh-btn" id="refreshBtn">
                    <i class="fas fa-arrows-rotate"></i> Refresh Stocks
                </button>
                <a href="{{ route('purchases.create') }}" class="btn-inv-primary">
                    <i class="fas fa-plus"></i> Add Purchase
                </a>
                <a href="{{ route('stock-outs.create') }}" class="btn-inv-secondary">
                    <i class="fas fa-minus"></i> Stock Out
                </a>
            </div>
        </div>

        {{-- 2. Stats Grid --}}
        <div class="inv-stats-grid">
            <div class="inv-stat-card stat-teal">
                <div class="inv-stat-info">
                    <span class="inv-stat-label">Total Monitored Items</span>
                    <span class="inv-stat-val">{{ number_format($totalProducts) }}</span>
                    <span class="text-muted small mt-1">Total Qty: {{ number_format($totalStockQuantity, 2) }}</span>
                </div>
                <div class="inv-stat-icon">
                    <i class="fas fa-cubes"></i>
                </div>
            </div>

            <div class="inv-stat-card stat-success">
                <div class="inv-stat-info">
                    <span class="inv-stat-label">Adequate Stock</span>
                    <span class="inv-stat-val text-success">{{ number_format($goodStockItems) }}</span>
                    <span class="text-success small mt-1">&gt; 10 units available</span>
                </div>
                <div class="inv-stat-icon">
                    <i class="fas fa-circle-check"></i>
                </div>
            </div>

            <div class="inv-stat-card stat-warning">
                <div class="inv-stat-info">
                    <span class="inv-stat-label">Low Stock Alerts</span>
                    <span class="inv-stat-val text-warning">{{ number_format($lowStockItems) }}</span>
                    <span class="text-warning small mt-1">1 - 10 units remaining</span>
                </div>
                <div class="inv-stat-icon">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
            </div>

            <div class="inv-stat-card stat-danger">
                <div class="inv-stat-info">
                    <span class="inv-stat-label">Out of Stock</span>
                    <span class="inv-stat-val text-danger">{{ number_format($outOfStockItems) }}</span>
                    <span class="text-danger small mt-1">Immediate action needed</span>
                </div>
                <div class="inv-stat-icon">
                    <i class="fas fa-circle-xmark"></i>
                </div>
            </div>
        </div>

        {{-- 3. Filter & Keyword Search Deck --}}
        <div class="inv-card mb-4">
            <div class="inv-card-body p-3">
                <form method="GET" action="{{ route('inventory.live') }}" id="liveInventoryFilterForm">
                    <div class="row g-2 align-items-center">
                        {{-- Search Input --}}
                        <div class="col-lg-4 col-md-5 col-12">
                            <div class="inv-search-box w-100" style="max-width: 100%;">
                                <i class="fas fa-search inv-search-icon"></i>
                                <input type="text" 
                                       name="search" 
                                       id="customKeywordSearch"
                                       class="inv-search-input" 
                                       placeholder="Search product name, unit, author..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>

                        {{-- Unit Filter Dropdown --}}
                        <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                            <select name="unit_id" class="form-select inv-select-pill" onchange="this.form.submit()">
                                <option value="">-- All Units --</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="col-lg-5 col-md-3 col-sm-6 col-12 text-md-end text-start">
                            <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 py-2 fw-bold me-1">
                                <i class="fas fa-filter me-1"></i> Apply Filter
                            </button>
                            @if(request()->hasAny(['search', 'unit_id', 'stock_status', 'low_stock', 'out_of_stock']))
                                <a href="{{ route('inventory.live') }}" class="btn btn-sm btn-light border rounded-pill px-3 py-2 fw-bold text-muted">
                                    <i class="fas fa-rotate-left me-1"></i> Reset
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Stock Status Filter Chips / Tabs --}}
                    <div class="d-flex align-items-center gap-2 flex-wrap mt-3 pt-3 border-top">
                        <span class="text-muted small fw-bold text-uppercase me-1"><i class="fas fa-sliders me-1"></i> Stock Status:</span>
                        
                        <a href="{{ route('inventory.live', array_merge(request()->except(['stock_status', 'low_stock', 'out_of_stock', 'page']), ['stock_status' => ''])) }}" 
                           class="quick-status-tab {{ empty($stockStatus) ? 'active' : '' }}">
                            <span>All Items</span>
                            <span class="badge rounded-pill bg-white text-dark">{{ $totalProducts }}</span>
                        </a>

                        <a href="{{ route('inventory.live', array_merge(request()->except(['stock_status', 'low_stock', 'out_of_stock', 'page']), ['stock_status' => 'good'])) }}" 
                           class="quick-status-tab tab-good {{ $stockStatus === 'good' ? 'active' : '' }}">
                            <i class="fas fa-circle-check"></i>
                            <span>Good Stock (>10)</span>
                            <span class="badge rounded-pill bg-white text-dark">{{ $goodStockItems }}</span>
                        </a>

                        <a href="{{ route('inventory.live', array_merge(request()->except(['stock_status', 'low_stock', 'out_of_stock', 'page']), ['stock_status' => 'low'])) }}" 
                           class="quick-status-tab tab-low {{ $stockStatus === 'low' ? 'active' : '' }}">
                            <i class="fas fa-triangle-exclamation"></i>
                            <span>Low Stock (≤10)</span>
                            <span class="badge rounded-pill bg-white text-dark">{{ $lowStockItems }}</span>
                        </a>

                        <a href="{{ route('inventory.live', array_merge(request()->except(['stock_status', 'low_stock', 'out_of_stock', 'page']), ['stock_status' => 'out'])) }}" 
                           class="quick-status-tab tab-out {{ $stockStatus === 'out' ? 'active' : '' }}">
                            <i class="fas fa-circle-xmark"></i>
                            <span>Out of Stock (0)</span>
                            <span class="badge rounded-pill bg-white text-dark">{{ $outOfStockItems }}</span>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- 4. Main Inventory Data Table Card --}}
        <div class="inv-card">
            <div class="inv-card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h3 class="inv-card-title mb-1">Live Stock Table</h3>
                    <p class="inv-card-subtitle mb-0">Showing real-time on-hand quantities for active products.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill">
                        Total Items: <strong>{{ $inventories->count() }}</strong>
                    </span>
                </div>
            </div>

            <div class="inv-card-body p-0">
                <div class="table-responsive">
                    <table id="liveInventoryTable" class="table inv-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Product Details</th>
                                <th>Unit</th>
                                <th class="text-end">Opening Stock</th>
                                <th class="text-end">Current Stock</th>
                                <th>Stock Status & Level</th>
                                <th>Last Updated</th>
                                <th class="text-center" style="width: 140px;">Quick Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventories as $key => $inventory)
                                @php
                                    $product = $inventory->product;
                                    $currentStock = (float) $inventory->total_qty;
                                    $openingStock = (float) $inventory->opening_qty;
                                    $unitName = $product && $product->unit ? $product->unit->name : 'Units';
                                    $lastUpdated = $inventory->updated_at ? $inventory->updated_at->format('d M Y, h:i A') : 'N/A';
                                    
                                    // Stock Level & Percentage calculation (relative to 50 base scale or opening qty)
                                    $baseMax = max(50, $openingStock, $currentStock);
                                    $percentage = $baseMax > 0 ? min(100, max(0, ($currentStock / $baseMax) * 100)) : 0;

                                    if ($currentStock <= 0) {
                                        $rowBg = 'background-color: rgba(239, 68, 68, 0.02);';
                                        $qtyClass = 'qty-out';
                                        $fillClass = 'fill-out';
                                        $statusBadge = '<span class="stock-badge-pill badge-pill-danger"><i class="fas fa-circle-xmark"></i> Out of Stock</span>';
                                    } elseif ($currentStock <= 10) {
                                        $rowBg = 'background-color: rgba(245, 158, 11, 0.02);';
                                        $qtyClass = 'qty-low';
                                        $fillClass = 'fill-low';
                                        $statusBadge = '<span class="stock-badge-pill badge-pill-warning"><i class="fas fa-triangle-exclamation"></i> Low Stock</span>';
                                    } else {
                                        $rowBg = '';
                                        $qtyClass = 'qty-good';
                                        $fillClass = 'fill-good';
                                        $statusBadge = '<span class="stock-badge-pill badge-pill-success"><i class="fas fa-circle-check"></i> Good Stock</span>';
                                    }
                                @endphp
                                <tr style="{{ $rowBg }}">
                                    <td class="text-muted fw-bold">{{ $key + 1 }}</td>
                                    
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="product-avatar-icon">
                                                <i class="fas fa-box-open"></i>
                                            </div>
                                            <div>
                                                <span class="fw-bold text-dark d-block" style="font-size: 0.95rem;">
                                                    {{ $product ? $product->product_name : 'Unnamed Product' }}
                                                </span>
                                                @if($currentStock <= 0)
                                                    <small class="text-danger fw-semibold"><i class="fas fa-triangle-exclamation me-1"></i> Stock exhausted - Restock immediately</small>
                                                @elseif($currentStock <= 10)
                                                    <small class="text-warning fw-semibold"><i class="fas fa-bell me-1"></i> Running low on stock</small>
                                                @else
                                                    <small class="text-muted">ID #{{ $product ? $product->id : 'N/A' }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-1 rounded-pill font-monospace">
                                            {{ $unitName }}
                                        </span>
                                    </td>

                                    <td class="text-end font-monospace text-muted">
                                        {{ number_format($openingStock, 2) }}
                                    </td>

                                    <td class="text-end">
                                        <span class="stock-qty-display {{ $qtyClass }}">
                                            {{ number_format($currentStock, 2) }}
                                        </span>
                                        <small class="d-block text-muted" style="font-size: 0.72rem;">{{ $unitName }}</small>
                                    </td>

                                    <td>
                                        <div class="stock-meter-wrap">
                                            <div>{!! $statusBadge !!}</div>
                                            <div class="stock-meter-bar" title="{{ number_format($currentStock, 2) }} {{ $unitName }}">
                                                <div class="stock-meter-fill {{ $fillClass }}" style="width: {{ $percentage }}%;"></div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="d-block text-dark small fw-semibold">{{ $lastUpdated }}</span>
                                        @if($inventory->created_by)
                                            <small class="text-muted">By: {{ $inventory->created_by }}</small>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        @if($product)
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                @if($currentStock <= 10)
                                                    <a href="{{ route('purchases.create') }}?product_id={{ $product->id }}&quantity={{ max(50, $openingStock > 0 ? $openingStock : 20) }}" 
                                                       class="btn-action-icon btn-action-restock" 
                                                       data-bs-toggle="tooltip" 
                                                       title="Quick Restock">
                                                        <i class="fas fa-bolt"></i>
                                                    </a>
                                                @endif
                                                <a href="{{ route('purchases.create') }}?product_id={{ $product->id }}" 
                                                   class="btn-action-icon btn-action-purchase" 
                                                   data-bs-toggle="tooltip" 
                                                   title="Add Purchase (+)">
                                                    <i class="fas fa-plus"></i>
                                                </a>
                                                <a href="{{ route('stock-outs.create') }}?product_id={{ $product->id }}" 
                                                   class="btn-action-icon btn-action-stockout" 
                                                   data-bs-toggle="tooltip" 
                                                   title="Record Stock Out (-)">
                                                    <i class="fas fa-minus"></i>
                                                </a>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>
</div>

@endsection

@section('script')
<!-- JS Libraries -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
@include('includes.script')

<script>
    function refreshInventory() {
        const btn = $('#refreshBtn');
        btn.html('<i class="fas fa-arrows-rotate fa-spin"></i> Refreshing...');
        btn.prop('disabled', true);
        setTimeout(function() {
            location.reload();
        }, 400);
    }

    $(document).ready(function() {
        // Initialize tooltips
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        // Initialize DataTable
        const table = $('#liveInventoryTable').DataTable({
            "order": [[4, "asc"]], // Sort by current stock ascending by default
            "pageLength": 25,
            "dom": '<"dataTables-toolbar-deck d-flex flex-wrap justify-content-between align-items-center gap-2"Bf>rt<"dataTables-footer-deck d-flex flex-wrap justify-content-between align-items-center gap-2"lip>',
            "buttons": [
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel me-1 text-success"></i> Export Excel',
                    className: 'btn btn-sm btn-light border rounded-pill px-3 shadow-none',
                    title: 'Live_Inventory_Report_' + new Date().toISOString().split('T')[0],
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print me-1 text-info"></i> Print List',
                    className: 'btn btn-sm btn-light border rounded-pill px-3 shadow-none',
                    title: 'Live Inventory Report - ' + new Date().toLocaleDateString(),
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    },
                    customize: function (win) {
                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', '10pt');
                        $(win.document.body)
                            .css('font-size', '10pt')
                            .prepend('<div style="margin-bottom: 20px;"><h3>Live Stock & Inventory Report</h3><p>Generated on: ' + new Date().toLocaleString() + '</p></div>');
                    }
                }
            ],
            "language": {
                "emptyTable": '<div class="py-4 text-center text-muted"><div class="mb-2" style="font-size: 2rem;"><i class="fas fa-boxes-packing opacity-50"></i></div><h6 class="fw-bold text-dark">No inventory records found</h6><p class="small text-muted mb-0">No stock items match your search keyword or selected filter criteria.</p></div>',
                "search": "_INPUT_",
                "searchPlaceholder": "Live filter in table...",
                "paginate": {
                    "first": "<i class='fas fa-angle-double-left'></i>",
                    "last": "<i class='fas fa-angle-double-right'></i>",
                    "next": "<i class='fas fa-angle-right'></i>",
                    "previous": "<i class='fas fa-angle-left'></i>"
                },
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ items",
                "infoEmpty": "Showing 0 to 0 of 0 items"
            }
        });

        // Auto-refresh every 2.5 minutes (150000 ms) unless user is interacting
        let autoRefreshTimer = setInterval(refreshInventory, 150000);
        $('#customKeywordSearch, input, select').on('focus', function() {
            clearInterval(autoRefreshTimer);
        });
    });

    // Keyboard shortcut (Ctrl + R) to refresh
    $(document).keydown(function(e) {
        if (e.ctrlKey && e.keyCode === 82) {
            e.preventDefault();
            refreshInventory();
        }
    });
</script>
@endsection