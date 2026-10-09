@extends('layouts.app')

@section('title')
<title>Admin - Manage Stock Outs</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('admin_template/css/inventory-modules.css') }}">
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
                <div class="inv-header-icon icon-stockout">
                    <i class="fas fa-box-open"></i>
                </div>
                <div class="inv-header-title-meta">
                    <span class="inv-header-eyebrow">Outward Inventory</span>
                    <h1 class="inv-header-title">Stock Out & Wastage</h1>
                    <p class="inv-header-sub">Track kitchen consumption, raw material usage, spoilage, and stock deductions</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if(auth()->user()->hasPermission('inventory_setting', 'add'))
                <a href="{{ route('stock-outs.create') }}" class="btn-inv-primary">
                    <i class="fas fa-plus"></i>
                    <span>Add Stock Out</span>
                </a>
                @endif
            </div>
        </div>

        {{-- 2. Stats Grid --}}
        @php
            $totalRecords = $stockOuts->count();
            $totalQtyDeducted = $stockOuts->sum('total_quantity');
            $totalItemsConsumed = $stockOuts->sum('total_items');
        @endphp
        <div class="inv-stats-grid">
            <div class="inv-stat-card stat-danger">
                <div class="inv-stat-info">
                    <span class="inv-stat-label">Total Outward Records</span>
                    <span class="inv-stat-val">{{ $totalRecords }}</span>
                </div>
                <div class="inv-stat-icon">
                    <i class="fas fa-dolly"></i>
                </div>
            </div>
            <div class="inv-stat-card stat-warning">
                <div class="inv-stat-info">
                    <span class="inv-stat-label">Total Qty Consumed</span>
                    <span class="inv-stat-val">{{ number_format($totalQtyDeducted, 2) }}</span>
                </div>
                <div class="inv-stat-icon">
                    <i class="fas fa-cubes"></i>
                </div>
            </div>
            <div class="inv-stat-card stat-purple">
                <div class="inv-stat-info">
                    <span class="inv-stat-label">Line Items</span>
                    <span class="inv-stat-val">{{ $totalItemsConsumed }}</span>
                </div>
                <div class="inv-stat-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
            </div>
            <div class="inv-stat-card stat-dark">
                <div class="inv-stat-info">
                    <span class="inv-stat-label">Stock Status</span>
                    <span class="inv-stat-val text-danger" style="font-size: 1.3rem;">Deducted</span>
                </div>
                <div class="inv-stat-icon">
                    <i class="fas fa-minus-circle"></i>
                </div>
            </div>
        </div>

        {{-- 3. Filter & Search Toolbar --}}
        <div class="inv-toolbar">
            <div class="inv-search-box">
                <i class="fas fa-search inv-search-icon"></i>
                <input type="text" id="stockOutSearchInput" class="inv-search-input" placeholder="Search by stockout no, date, user...">
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small fw-bold">Showing {{ $totalRecords }} Records</span>
            </div>
        </div>

        {{-- 4. Desktop Table View --}}
        <div class="inv-card">
            <div class="table-responsive">
                <table class="inv-table" id="stockOutDesktopTable">
                    <thead>
                        <tr>
                            <th style="width: 70px;">#</th>
                            <th>Stock Out No</th>
                            <th>Date</th>
                            <th class="text-center">Items</th>
                            <th class="text-end">Total Quantity</th>
                            <th>Processed By</th>
                            <th class="text-end" style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="stockOutTableBody">
                        @forelse($stockOuts as $key => $stockOut)
                        <tr class="stockout-row"
                            data-no="{{ strtolower($stockOut->stockout_no) }}"
                            data-date="{{ date('d-m-Y', strtotime($stockOut->stockout_date)) }}"
                            data-user="{{ strtolower($stockOut->user ? $stockOut->user->name : '') }}">
                            <td class="text-muted fw-bold">{{ $key + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width: 32px; height: 32px; border-radius: 8px; background: var(--inv-danger-bg); color: var(--inv-danger-text); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem;">
                                        <i class="fas fa-arrow-down"></i>
                                    </div>
                                    <div>
                                        <strong class="text-dark">{{ $stockOut->stockout_no }}</strong>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted small">
                                    <i class="far fa-calendar-alt me-1 text-secondary"></i> {{ date('d M, Y', strtotime($stockOut->stockout_date)) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="inv-badge badge-purple">
                                    {{ $stockOut->total_items }} Items
                                </span>
                            </td>
                            <td class="text-end">
                                <strong class="text-danger" style="font-family: 'Outfit', sans-serif; font-size: 1rem;">
                                    -{{ number_format($stockOut->total_quantity, 2) }}
                                </strong>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-user-circle text-muted"></i>
                                    <span class="text-dark fw-semibold small">{{ $stockOut->user ? $stockOut->user->name : 'N/A' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="inv-actions justify-content-end">
                                    <a href="{{ route('stock-outs.show', $stockOut->id) }}" 
                                       class="inv-btn-action btn-view" 
                                       title="View Stock Out Record">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if(auth()->user()->hasPermission('inventory_setting', 'edit'))
                                    <a href="{{ route('stock-outs.edit', $stockOut->id) }}" 
                                       class="inv-btn-action btn-edit" 
                                       title="Edit Stock Out">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    @endif

                                    @if(auth()->user()->hasPermission('inventory_setting', 'delete'))
                                    <a href="{{ route('stock-outs.delete', $stockOut->id) }}" 
                                       class="inv-btn-action btn-del" 
                                       title="Delete Stock Out"
                                       onclick="return confirm('Are you sure you want to delete stockout entry {{ $stockOut->stockout_no }}?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-box-open fa-3x mb-3 text-secondary opacity-50"></i>
                                <h6 class="fw-bold">No Stock Out Entries Recorded</h6>
                                <p class="small text-muted mb-0">Click "+ Add Stock Out" to record kitchen consumption or wastage.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 5. Mobile Cards View --}}
        <div class="inv-mobile-cards" id="stockOutMobileCards">
            @forelse($stockOuts as $key => $stockOut)
            <div class="inv-mobile-item stockout-row"
                 data-no="{{ strtolower($stockOut->stockout_no) }}"
                 data-date="{{ date('d-m-Y', strtotime($stockOut->stockout_date)) }}"
                 data-user="{{ strtolower($stockOut->user ? $stockOut->user->name : '') }}">
                <div class="inv-mobile-top">
                    <div>
                        <strong class="text-dark">{{ $stockOut->stockout_no }}</strong>
                        <div class="text-muted small">
                            <i class="far fa-calendar-alt me-1"></i> {{ date('d M, Y', strtotime($stockOut->stockout_date)) }}
                        </div>
                    </div>
                    <strong class="text-danger" style="font-family: 'Outfit', sans-serif; font-size: 1.1rem;">
                        -{{ number_format($stockOut->total_quantity, 2) }}
                    </strong>
                </div>

                <div class="inv-mobile-meta-row">
                    <div>
                        <i class="fas fa-user-circle text-muted me-1"></i>
                        <span>{{ $stockOut->user ? $stockOut->user->name : 'N/A' }}</span>
                    </div>
                    <span class="inv-badge badge-purple">
                        {{ $stockOut->total_items }} Items
                    </span>
                </div>

                <div class="inv-mobile-actions">
                    <a href="{{ route('stock-outs.show', $stockOut->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-3">
                        <i class="fas fa-eye me-1"></i> View
                    </a>

                    @if(auth()->user()->hasPermission('inventory_setting', 'edit'))
                    <a href="{{ route('stock-outs.edit', $stockOut->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        <i class="fas fa-pen me-1"></i> Edit
                    </a>
                    @endif

                    @if(auth()->user()->hasPermission('inventory_setting', 'delete'))
                    <a href="{{ route('stock-outs.delete', $stockOut->id) }}"
                       class="btn btn-sm btn-outline-danger rounded-pill px-3"
                       onclick="return confirm('Delete this record?')">
                        <i class="fas fa-trash-alt me-1"></i> Delete
                    </a>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-4 text-muted">
                <p class="mb-0">No stock out records found.</p>
            </div>
            @endforelse
        </div>

    </div>

</div>
</div>

@endsection

@section('script')
@include('includes.script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('stockOutSearchInput');
        const rows = document.querySelectorAll('.stockout-row');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.trim().toLowerCase();
                rows.forEach(row => {
                    const no = (row.dataset.no || '').toLowerCase();
                    const date = (row.dataset.date || '').toLowerCase();
                    const user = (row.dataset.user || '').toLowerCase();
                    if (!query || no.includes(query) || date.includes(query) || user.includes(query)) {
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