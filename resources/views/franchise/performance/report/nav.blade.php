{{-- Franchise Performance Master Report Navigation Bar --}}
@php
    $currentRoute = Route::currentRouteName();
    $targetRest = $context['targetRestaurant'];
    $mainRest = $context['mainRestaurant'];
    $availableOutlets = $context['availableOutlets'];
    $isMulti = $context['isMultiBranch'] || (auth()->user()->role === 'SA');
    $restaurantId = $targetRest->id;
@endphp

<style>
/* ================================================================
   FRANCHISE PERFORMANCE MASTER REPORT ULTRA-PREMIUM DESIGN SYSTEM
   ================================================================ */
:root {
    --mr-primary: #4f46e5;
    --mr-primary-dark: #3730a3;
    --mr-primary-light: #eef2ff;
    --mr-accent: #ff6a00;
    --mr-success: #10b981;
    --mr-success-light: #ecfdf5;
    --mr-warning: #f59e0b;
    --mr-warning-light: #fffbeb;
    --mr-danger: #ef4444;
    --mr-danger-light: #fef2f2;
    --mr-info: #06b6d4;
    --mr-info-light: #ecfeff;
    --mr-slate-900: #0f172a;
    --mr-slate-800: #1e293b;
    --mr-slate-700: #334155;
    --mr-slate-600: #475569;
    --mr-slate-500: #64748b;
    --mr-slate-400: #94a3b8;
    --mr-slate-200: #e2e8f0;
    --mr-slate-100: #f1f5f9;
    --mr-slate-50: #f8fafc;
    --mr-radius: 16px;
    --mr-shadow-sm: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
    --mr-shadow-md: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -1px rgba(0,0,0,0.04);
    --mr-shadow-lg: 0 10px 25px -5px rgba(0,0,0,0.08), 0 8px 10px -6px rgba(0,0,0,0.04);
    --mr-shadow-glow: 0 12px 30px -8px rgba(79, 70, 229, 0.28);
}

.mr-wrap {
    font-family: 'Plus Jakarta Sans', 'Outfit', system-ui, -apple-system, sans-serif;
    color: var(--mr-slate-800);
}

/* Header Deck */
.mr-header-deck {
    background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 60%, #311042 100%);
    border-radius: 20px;
    padding: 26px 30px;
    color: #ffffff;
    box-shadow: 0 12px 32px rgba(15, 23, 42, 0.25);
    margin-bottom: 22px;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.mr-header-deck::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 320px;
    height: 320px;
    background: radial-gradient(circle, rgba(255, 106, 0, 0.2) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}

.mr-header-left {
    display: flex;
    align-items: center;
    gap: 18px;
}

.mr-header-icon {
    width: 58px;
    height: 58px;
    border-radius: 16px;
    background: linear-gradient(135deg, #ff6a00 0%, #ee0979 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    color: #ffffff;
    box-shadow: 0 8px 20px rgba(255, 106, 0, 0.35);
    flex-shrink: 0;
}

.mr-header-eyebrow {
    font-size: 0.72rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    color: #ff6a00;
    display: block;
    margin-bottom: 3px;
}

.mr-header-title {
    font-size: 1.55rem;
    font-weight: 800;
    margin: 0;
    line-height: 1.2;
    letter-spacing: -0.5px;
    color: #ffffff;
}

.mr-header-sub {
    font-size: 0.86rem;
    color: rgba(255, 255, 255, 0.7);
    margin: 4px 0 0;
}

.mr-outlet-badge {
    background: rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    padding: 8px 14px;
    color: #ffffff;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.mr-outlet-select-btn {
    background: rgba(255, 255, 255, 0.15) !important;
    border: 1px solid rgba(255, 255, 255, 0.25) !important;
    color: #ffffff !important;
    border-radius: 12px;
    padding: 9px 16px;
    font-weight: 600;
    font-size: 0.85rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.mr-outlet-dropdown-menu {
    border-radius: 14px;
    box-shadow: 0 16px 36px rgba(0, 0, 0, 0.16);
    border: 1px solid var(--mr-slate-200);
    padding: 8px;
    min-width: 250px;
}

.mr-outlet-item {
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 0.86rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

/* Tabs Bar */
.mr-tabs-bar {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: var(--mr-shadow-sm);
    border: 1px solid var(--mr-slate-200);
    margin-bottom: 24px;
    overflow-x: auto;
    white-space: nowrap;
}

.mr-tab-pill {
    padding: 10px 18px;
    border-radius: 12px;
    font-size: 0.88rem;
    font-weight: 600;
    color: var(--mr-slate-600);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    border: 1px solid transparent;
}

.mr-tab-pill:hover {
    color: var(--mr-primary);
    background: var(--mr-primary-light);
}

.mr-tab-pill.active {
    background: linear-gradient(135deg, var(--mr-primary) 0%, var(--mr-primary-dark) 100%);
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}

/* KPI Cards */
.mr-kpi-card {
    background: #ffffff;
    border-radius: var(--mr-radius);
    padding: 22px;
    border: 1px solid var(--mr-slate-200);
    box-shadow: var(--mr-shadow-sm);
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
    position: relative;
    overflow: hidden;
}

.mr-kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--mr-shadow-md);
    border-color: #cbd5e1;
}

.mr-kpi-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}

.mr-kpi-label {
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: var(--mr-slate-500);
}

.mr-kpi-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.mr-kpi-value {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--mr-slate-900);
    line-height: 1.15;
    margin-bottom: 8px;
    letter-spacing: -0.5px;
}

.mr-kpi-footer {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.78rem;
    color: var(--mr-slate-500);
    font-weight: 500;
}

.mr-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.3px;
    text-transform: uppercase;
}

.mr-badge-paid { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
.mr-badge-unpaid { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
.mr-badge-partial { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }

.mr-badge-in-stock { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
.mr-badge-low-stock { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
.mr-badge-out-stock { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }

/* Filter Card */
.mr-filter-card {
    background: #ffffff;
    border-radius: var(--mr-radius);
    padding: 20px 24px;
    border: 1px solid var(--mr-slate-200);
    box-shadow: var(--mr-shadow-sm);
    margin-bottom: 24px;
}

.mr-form-label {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: var(--mr-slate-600);
    margin-bottom: 6px;
}

.mr-input {
    border-radius: 10px;
    border: 1.5px solid var(--mr-slate-200);
    padding: 9px 14px;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.mr-input:focus {
    border-color: var(--mr-primary);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
}

/* Action Buttons */
.mr-btn {
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.85rem;
    padding: 9px 16px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    text-decoration: none;
    border: none;
}

.mr-btn-primary {
    background: var(--mr-primary);
    color: #ffffff;
}

.mr-btn-primary:hover {
    background: var(--mr-primary-dark);
    color: #ffffff;
}

.mr-btn-outline {
    background: #ffffff;
    border: 1px solid var(--mr-slate-300);
    color: var(--mr-slate-700);
}

.mr-btn-outline:hover {
    background: var(--mr-slate-50);
    border-color: var(--mr-slate-400);
    color: var(--mr-slate-900);
}

.mr-btn-success {
    background: #10b981;
    color: #ffffff;
}

.mr-btn-success:hover {
    background: #059669;
    color: #ffffff;
}

/* Tables */
.mr-table-card {
    background: #ffffff;
    border-radius: var(--mr-radius);
    border: 1px solid var(--mr-slate-200);
    box-shadow: var(--mr-shadow-sm);
    overflow: hidden;
    margin-bottom: 24px;
}

.mr-table-header {
    padding: 18px 24px;
    border-bottom: 1px solid var(--mr-slate-200);
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--mr-slate-50);
}

.mr-table-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--mr-slate-900);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.mr-table-responsive {
    overflow-x: auto;
}

.mr-table {
    width: 100%;
    margin-bottom: 0;
    font-size: 0.875rem;
}

.mr-table thead th {
    background: #f8fafc;
    color: var(--mr-slate-600);
    font-weight: 700;
    font-size: 0.74rem;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    padding: 13px 18px;
    border-bottom: 1px solid var(--mr-slate-200);
}

.mr-table tbody td {
    padding: 14px 18px;
    vertical-align: middle;
    border-bottom: 1px solid var(--mr-slate-100);
    color: var(--mr-slate-700);
}

.mr-table tbody tr:hover td {
    background: #f8fafc;
}
</style>

{{-- 1. HEADER DECK --}}
<div class="mr-header-deck">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 position-relative" style="z-index: 2;">
        <div class="mr-header-left">
            <div class="mr-header-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="mr-header-eyebrow">Franchise Performance Tracking</span>
                    @if(empty($targetRest->parent_id))
                        <span class="badge bg-primary text-white" style="font-size: 10px;">HQ / Parent</span>
                    @else
                        <span class="badge bg-info text-white" style="font-size: 10px;">Sub-Outlet</span>
                    @endif
                </div>
                <h1 class="mr-header-title">{{ $targetRest->name }} - Master Report</h1>
                <p class="mr-header-sub">
                    <i class="fas fa-map-marker-alt me-1 text-danger"></i> {{ $targetRest->address ?: 'Address not set' }}
                    @if($targetRest->pincode) (PIN: {{ $targetRest->pincode }}) @endif
                    @if($targetRest->gstin) &bull; GSTIN: <strong>{{ $targetRest->gstin }}</strong> @endif
                </p>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- Navigation Back buttons --}}
            @if($mainRest && $mainRest->outlets->count() > 0)
                <a href="{{ route('franchise.performance.parent', ['parentId' => $mainRest->id]) }}" class="mr-btn mr-btn-outline" title="Back to Associated Outlets">
                    <i class="fas fa-network-wired"></i> Outlets List
                </a>
            @endif
            <a href="{{ route('franchise.performance.index') }}" class="mr-btn mr-btn-outline" title="Back to Performance Dashboard">
                <i class="fas fa-th-large"></i> All Sponsors
            </a>

            {{-- Multi-Branch Switcher --}}
            @if($isMulti && $availableOutlets && $availableOutlets->count() > 1)
                <div class="dropdown">
                    <button class="btn mr-outlet-select-btn dropdown-toggle" type="button" id="branchSwitcherDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-store-alt text-warning"></i>
                        <span>Switch Outlet: <strong>{{ $targetRest->name }}</strong></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end mr-outlet-dropdown-menu" aria-labelledby="branchSwitcherDropdown">
                        <li class="dropdown-header text-uppercase font-weight-bold text-muted small px-3 py-1">
                            Associated Outlets
                        </li>
                        @foreach($availableOutlets as $outlet)
                            @php
                                $routePrefix = str_replace(['admin.reports.master.', 'franchise.performance.report.'], '', $currentRoute);
                                if (!in_array($routePrefix, ['overview', 'orders', 'order-items', 'purchases', 'stock', 'expenses', 'analytics'])) {
                                    $routePrefix = 'overview';
                                }
                                $switchUrl = route('franchise.performance.report.' . $routePrefix, array_merge(['restaurantId' => $outlet->id], request()->except('restaurantId')));
                            @endphp
                            <li>
                                <a class="dropdown-item mr-outlet-item {{ $outlet->id == $targetRest->id ? 'active' : '' }}" href="{{ $switchUrl }}">
                                    <span>
                                        <i class="fas {{ $outlet->parent_id ? 'fa-code-branch' : 'fa-building' }} me-2 text-muted"></i>
                                        {{ $outlet->name }}
                                        @if(!$outlet->parent_id) <span class="badge bg-primary ms-1" style="font-size: 10px;">HQ</span> @endif
                                    </span>
                                    @if($outlet->id == $targetRest->id)
                                        <i class="fas fa-check-circle text-primary"></i>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Excel Export Quick Trigger --}}
            <button type="button" class="mr-btn mr-btn-success" onclick="if(typeof exportActiveReportToExcel === 'function') { exportActiveReportToExcel(); } else { window.print(); }">
                <i class="fas fa-file-excel"></i> Export Excel
            </button>
            <button type="button" class="mr-btn mr-btn-outline" onclick="window.print();">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>
</div>

{{-- 2. DEDICATED TAB PILLS BAR --}}
<div class="mr-tabs-bar">
    <a href="{{ route('franchise.performance.report.dashboard', ['restaurantId' => $restaurantId]) }}" 
       class="mr-tab-pill {{ in_array($currentRoute, ['franchise.performance.report.dashboard', 'franchise.performance.report']) ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </a>

    <a href="{{ route('franchise.performance.report.overview', ['restaurantId' => $restaurantId]) }}" 
       class="mr-tab-pill {{ $currentRoute === 'franchise.performance.report.overview' ? 'active' : '' }}">
        <i class="fas fa-chart-pie"></i> Executive Overview
    </a>

    <a href="{{ route('franchise.performance.report.orders', ['restaurantId' => $restaurantId]) }}" 
       class="mr-tab-pill {{ $currentRoute === 'franchise.performance.report.orders' ? 'active' : '' }}">
        <i class="fas fa-receipt"></i> Orders List
    </a>

    <a href="{{ route('franchise.performance.report.order-items', ['restaurantId' => $restaurantId]) }}" 
       class="mr-tab-pill {{ $currentRoute === 'franchise.performance.report.order-items' ? 'active' : '' }}">
        <i class="fas fa-utensils"></i> Order Items
    </a>

    <a href="{{ route('franchise.performance.report.purchases', ['restaurantId' => $restaurantId]) }}" 
       class="mr-tab-pill {{ $currentRoute === 'franchise.performance.report.purchases' ? 'active' : '' }}">
        <i class="fas fa-truck-loading"></i> Purchases
    </a>

    <a href="{{ route('franchise.performance.report.stock', ['restaurantId' => $restaurantId]) }}" 
       class="mr-tab-pill {{ $currentRoute === 'franchise.performance.report.stock' ? 'active' : '' }}">
        <i class="fas fa-warehouse"></i> Live Stock
    </a>

    <a href="{{ route('franchise.performance.report.expenses', ['restaurantId' => $restaurantId]) }}" 
       class="mr-tab-pill {{ $currentRoute === 'franchise.performance.report.expenses' ? 'active' : '' }}">
        <i class="fas fa-wallet"></i> Expenses
    </a>

    <a href="{{ route('franchise.performance.report.analytics', ['restaurantId' => $restaurantId]) }}" 
       class="mr-tab-pill {{ $currentRoute === 'franchise.performance.report.analytics' ? 'active' : '' }}">
        <i class="fas fa-brain"></i> BI Analytics
    </a>

    <a href="{{ route('franchise.performance.report.customers', ['restaurantId' => $restaurantId]) }}" 
       class="mr-tab-pill {{ $currentRoute === 'franchise.performance.report.customers' ? 'active' : '' }}">
        <i class="fas fa-users"></i> Customers
    </a>
</div>

{{-- SheetJS Library for client-side Excel XLSX generation --}}
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
function exportTableToExcel(tableId, filename) {
    var table = document.getElementById(tableId);
    if (!table) {
        alert('No data table found to export.');
        return;
    }
    var wb = XLSX.utils.table_to_book(table, { sheet: "Report Data" });
    var cleanFilename = (filename || 'Report') + '_' + new Date().toISOString().slice(0,10) + '.xlsx';
    XLSX.writeFile(wb, cleanFilename);
}
</script>
