{{-- Master Report Header & Navigation Bar --}}
@php
    $currentRoute = Route::currentRouteName();
    $outletParam = $context['selectedOutletId'] ? ['outlet_id' => $context['selectedOutletId']] : [];
    $targetRest = $context['targetRestaurant'];
    $availableOutlets = $context['availableOutlets'];
    $isMulti = $context['isMultiBranch'] || (auth()->user()->role === 'SA');
@endphp

<style>
/* ================================================================
   MASTER REPORT ULTRA-PREMIUM DESIGN SYSTEM
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

.mr-header-deck::before {
    content: '';
    position: absolute;
    bottom: -50%;
    left: 20%;
    width: 260px;
    height: 260px;
    background: radial-gradient(circle, rgba(79, 70, 229, 0.25) 0%, transparent 70%);
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
    background: linear-gradient(135deg, #ff6a00 0%, #ff8533 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    box-shadow: 0 6px 20px rgba(255, 106, 0, 0.45);
    flex-shrink: 0;
}

.mr-header-eyebrow {
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: #cbd5e1;
    font-weight: 700;
    margin-bottom: 4px;
    display: block;
}

.mr-header-title {
    font-size: 1.65rem;
    font-weight: 800;
    margin: 0;
    color: #ffffff;
    letter-spacing: -0.5px;
    line-height: 1.2;
}

.mr-header-sub {
    font-size: 0.85rem;
    color: #94a3b8;
    margin: 4px 0 0 0;
}

/* Branch Switcher Dropdown */
.mr-outlet-badge {
    background: rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    padding: 8px 16px;
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.88rem;
    font-weight: 600;
}

.mr-outlet-select-btn {
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.25);
    color: #ffffff;
    border-radius: 12px;
    padding: 9px 18px;
    font-weight: 600;
    font-size: 0.88rem;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 10px;
}

.mr-outlet-select-btn:hover, .mr-outlet-select-btn:focus {
    background: rgba(255, 255, 255, 0.22);
    color: #ffffff;
    border-color: rgba(255, 255, 255, 0.4);
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
}

.mr-outlet-dropdown-menu {
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
    padding: 8px;
    min-width: 240px;
}

.mr-outlet-item {
    padding: 10px 14px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.88rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.15s;
    color: var(--mr-slate-700);
}

.mr-outlet-item:hover {
    background: #f1f5f9;
    color: var(--mr-primary);
}

.mr-outlet-item.active {
    background: var(--mr-primary-light);
    color: var(--mr-primary);
    font-weight: 700;
}

/* Modern Tab Pill Bar */
.mr-tabs-bar {
    background: #ffffff;
    border-radius: 16px;
    padding: 8px;
    margin-bottom: 24px;
    box-shadow: var(--mr-shadow-sm);
    border: 1px solid var(--mr-slate-200);
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.mr-tab-pill {
    flex: 1 1 auto;
    min-width: 130px;
    text-align: center;
    padding: 11px 16px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.86rem;
    color: var(--mr-slate-600);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.mr-tab-pill i {
    font-size: 0.95rem;
    transition: transform 0.2s ease;
}

.mr-tab-pill:hover {
    background: #f8fafc;
    color: var(--mr-primary);
    border-color: #e2e8f0;
}

.mr-tab-pill:hover i {
    transform: translateY(-2px);
}

.mr-tab-pill.active {
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(79, 70, 229, 0.35);
    border-color: rgba(255, 255, 255, 0.2);
}

.mr-tab-pill.active i {
    color: #ffffff;
}

/* KPI Card Styles */
.mr-kpi-card {
    background: #ffffff;
    border-radius: var(--mr-radius);
    padding: 22px 24px;
    box-shadow: var(--mr-shadow-sm);
    border: 1px solid var(--mr-slate-200);
    transition: all 0.25s ease;
    position: relative;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.mr-kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--mr-shadow-lg);
    border-color: #cbd5e1;
}

.mr-kpi-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 12px;
}

.mr-kpi-label {
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: var(--mr-slate-500);
    margin: 0;
}

.mr-kpi-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
}

.mr-kpi-value {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--mr-slate-900);
    letter-spacing: -0.5px;
    margin-bottom: 4px;
    line-height: 1.2;
}

.mr-kpi-footer {
    font-size: 0.78rem;
    color: var(--mr-slate-500);
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 6px;
}

/* Filter Card */
.mr-filter-card {
    background: #ffffff;
    border-radius: var(--mr-radius);
    padding: 18px 22px;
    box-shadow: var(--mr-shadow-sm);
    border: 1px solid var(--mr-slate-200);
    margin-bottom: 22px;
}

.mr-form-label {
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    color: var(--mr-slate-600);
    margin-bottom: 5px;
}

.mr-input {
    border-radius: 10px;
    border: 1px solid var(--mr-slate-200);
    padding: 8px 14px;
    font-size: 0.88rem;
    font-weight: 500;
    color: var(--mr-slate-800);
    transition: all 0.2s ease;
    height: 42px;
}

.mr-input:focus {
    border-color: var(--mr-primary);
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
}

/* Table Card */
.mr-table-card {
    background: #ffffff;
    border-radius: var(--mr-radius);
    box-shadow: var(--mr-shadow-sm);
    border: 1px solid var(--mr-slate-200);
    overflow: hidden;
    margin-bottom: 24px;
}

.mr-table-header {
    padding: 18px 24px;
    border-bottom: 1px solid var(--mr-slate-200);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    background: #fafafa;
}

.mr-table-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--mr-slate-900);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.mr-table {
    width: 100%;
    margin-bottom: 0;
    vertical-align: middle;
}

.mr-table th {
    background: #f8fafc;
    color: var(--mr-slate-600);
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 14px 18px;
    border-bottom: 1px solid var(--mr-slate-200);
    white-space: nowrap;
}

.mr-table td {
    padding: 14px 18px;
    color: var(--mr-slate-800);
    font-size: 0.88rem;
    border-bottom: 1px solid var(--mr-slate-100);
    vertical-align: middle;
}

.mr-table tr:hover td {
    background: #fbfcfe;
}

/* Badges */
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

/* Action Buttons */
.mr-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 18px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.85rem;
    transition: all 0.2s ease;
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none;
}

.mr-btn-primary {
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
}
.mr-btn-primary:hover {
    background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
}

.mr-btn-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
}
.mr-btn-success:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(16, 185, 129, 0.35);
}

.mr-btn-outline {
    background: #ffffff;
    color: var(--mr-slate-700);
    border-color: var(--mr-slate-200);
}
.mr-btn-outline:hover {
    background: #f8fafc;
    color: var(--mr-primary);
    border-color: #cbd5e1;
}
</style>

{{-- 1. HEADER DECK --}}
<div class="mr-header-deck">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 position-relative" style="z-index: 2;">
        <div class="mr-header-left">
            <div class="mr-header-icon">
                <i class="fas fa-crown"></i>
            </div>
            <div>
                <span class="mr-header-eyebrow">Enterprise Business Intelligence</span>
                <h1 class="mr-header-title">Master Executive Report</h1>
                <p class="mr-header-sub">Unified analytics, sales revenue, live stock, purchases, expenses & branch performance</p>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- Multi-Branch Switcher --}}
            @if($isMulti && $availableOutlets && $availableOutlets->count() > 1)
                <div class="dropdown">
                    <button class="btn mr-outlet-select-btn dropdown-toggle" type="button" id="branchSwitcherDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-store-alt text-warning"></i>
                        <span>Branch: <strong>{{ $targetRest ? $targetRest->name : 'Select Outlet' }}</strong></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end mr-outlet-dropdown-menu" aria-labelledby="branchSwitcherDropdown">
                        <li class="dropdown-header text-uppercase font-weight-bold text-muted small px-3 py-1">
                            Available Outlets
                        </li>
                        @foreach($availableOutlets as $outlet)
                            <li>
                                <a class="dropdown-item mr-outlet-item {{ $outlet->id == ($targetRest ? $targetRest->id : null) ? 'active' : '' }}" 
                                   href="{{ route($currentRoute, array_merge(request()->except('outlet_id'), ['outlet_id' => $outlet->id])) }}">
                                    <span>
                                        <i class="fas {{ $outlet->parent_id ? 'fa-code-branch' : 'fa-building' }} me-2 text-muted"></i>
                                        {{ $outlet->name }}
                                        @if(!$outlet->parent_id) <span class="badge bg-primary ms-1" style="font-size: 10px;">Main</span> @endif
                                    </span>
                                    @if($outlet->id == ($targetRest ? $targetRest->id : null))
                                        <i class="fas fa-check-circle text-primary"></i>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @elseif($targetRest)
                <div class="mr-outlet-badge">
                    <i class="fas fa-building text-warning"></i>
                    <span>{{ $targetRest->name }}</span>
                    @if($targetRest->gstin)
                        <span class="badge bg-secondary ms-1" style="font-size: 10px;">GST: {{ $targetRest->gstin }}</span>
                    @endif
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
    <a href="{{ route('admin.reports.master.overview', $outletParam) }}" 
       class="mr-tab-pill {{ in_array($currentRoute, ['admin.reports.master.overview', 'admin.reports.master.index']) ? 'active' : '' }}">
        <i class="fas fa-chart-pie"></i> Executive Overview
    </a>

    <a href="{{ route('admin.reports.master.orders', $outletParam) }}" 
       class="mr-tab-pill {{ $currentRoute === 'admin.reports.master.orders' ? 'active' : '' }}">
        <i class="fas fa-receipt"></i> Orders List
    </a>

    <a href="{{ route('admin.reports.master.order-items', $outletParam) }}" 
       class="mr-tab-pill {{ $currentRoute === 'admin.reports.master.order-items' ? 'active' : '' }}">
        <i class="fas fa-utensils"></i> Order Items
    </a>

    <a href="{{ route('admin.reports.master.purchases', $outletParam) }}" 
       class="mr-tab-pill {{ $currentRoute === 'admin.reports.master.purchases' ? 'active' : '' }}">
        <i class="fas fa-truck-loading"></i> Purchases
    </a>

    <a href="{{ route('admin.reports.master.stock', $outletParam) }}" 
       class="mr-tab-pill {{ $currentRoute === 'admin.reports.master.stock' ? 'active' : '' }}">
        <i class="fas fa-warehouse"></i> Live Stock
    </a>

    <a href="{{ route('admin.reports.master.expenses', $outletParam) }}" 
       class="mr-tab-pill {{ $currentRoute === 'admin.reports.master.expenses' ? 'active' : '' }}">
        <i class="fas fa-wallet"></i> Expenses
    </a>

    <a href="{{ route('admin.reports.master.analytics', $outletParam) }}" 
       class="mr-tab-pill {{ $currentRoute === 'admin.reports.master.analytics' ? 'active' : '' }}">
        <i class="fas fa-brain"></i> BI Analytics
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
