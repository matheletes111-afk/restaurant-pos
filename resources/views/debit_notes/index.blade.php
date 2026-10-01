@extends('layouts.app')

@section('title')
<title>Admin - Supplier Debit Notes</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('admin_template/css/inventory-modules.css') }}">
<style>
    .preset-chip {
        font-size: 0.76rem;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 20px;
        background: #f1f5f9;
        color: var(--inv-slate);
        border: 1px solid var(--inv-border);
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        cursor: pointer;
    }
    .preset-chip:hover {
        background: var(--inv-primary-light);
        border-color: var(--inv-primary);
        color: var(--inv-primary);
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
                <div class="inv-header-icon icon-debit">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div class="inv-header-title-meta">
                    <span class="inv-header-eyebrow">Returns & Adjustments</span>
                    <h1 class="inv-header-title">Supplier Debit Notes</h1>
                    <p class="inv-header-sub">Manage vendor purchase returns, damage claims, and outward supplier credit deductions</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge bg-light text-dark border px-3 py-2 fw-bold" style="font-size: 0.8rem;">
                    <i class="far fa-calendar-alt text-primary me-1"></i> Period: {{ $fromDate->format('d M Y') }} - {{ $toDate->format('d M Y') }}
                </span>
                @if(auth()->user()->hasPermission('inventory_setting', 'add'))
                <a href="{{ route('debit-notes.create') }}" class="btn-inv-primary">
                    <i class="fas fa-plus"></i>
                    <span>Create Debit Note</span>
                </a>
                @endif
            </div>
        </div>

        {{-- 2. Date Range, Vendor & Keyword Filter Form --}}
        <div class="inv-filter-card">
            <form method="GET" action="{{ route('debit-notes.index') }}" id="debitFilterForm" class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <label class="inv-filter-label"><i class="far fa-calendar-alt me-1 text-primary"></i> From Date</label>
                    <input type="date" name="from_date" id="filterFromDate" value="{{ $fromDate->format('Y-m-d') }}" class="inv-filter-control">
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6">
                    <label class="inv-filter-label"><i class="far fa-calendar-alt me-1 text-primary"></i> To Date</label>
                    <input type="date" name="to_date" id="filterToDate" value="{{ $toDate->format('Y-m-d') }}" class="inv-filter-control">
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6">
                    <label class="inv-filter-label"><i class="fas fa-truck me-1 text-secondary"></i> Vendor / Supplier</label>
                    <select name="supplier_id" class="inv-filter-control">
                        <option value="all">All Vendors / Suppliers</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ (string)$supplierId === (string)$sup->id ? 'selected' : '' }}>
                                {{ $sup->supplier_name }} @if(!empty($sup->company_name)) ({{ $sup->company_name }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6">
                    <label class="inv-filter-label"><i class="fas fa-magnifying-glass me-1 text-muted"></i> Keyword Search</label>
                    <input type="text" name="keyword" value="{{ $keyword }}" placeholder="Debit Note No, product, vendor..." class="inv-filter-control">
                </div>

                {{-- Quick Presets & Form Actions --}}
                <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2 border-top">
                    <div class="d-flex align-items-center gap-1 flex-wrap">
                        <span class="text-muted small fw-bold me-1">Quick Presets:</span>
                        <button type="button" class="preset-chip" onclick="applyDebitPreset('current_month')">
                            <i class="fas fa-calendar-day"></i> Current Month
                        </button>
                        <button type="button" class="preset-chip" onclick="applyDebitPreset('last_30_days')">
                            <i class="fas fa-calendar-week"></i> Last 30 Days
                        </button>
                        <button type="button" class="preset-chip" onclick="applyDebitPreset('this_year')">
                            <i class="fas fa-calendar"></i> This Year
                        </button>
                        <a href="{{ route('debit-notes.index', ['from_date' => '2000-01-01', 'to_date' => date('Y-m-d')]) }}" class="preset-chip">
                            <i class="fas fa-infinity"></i> Show All
                        </a>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('debit-notes.index') }}" class="btn btn-outline-secondary rounded-pill px-3 fw-bold" style="font-size: 0.84rem;">
                            <i class="fas fa-redo me-1"></i> Reset
                        </a>
                        <button type="submit" class="btn-inv-primary px-4">
                            <i class="fas fa-filter me-1"></i> Apply Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- 3. Stats Grid --}}
        @php
            $totalDebitNotes = $debitNotes->count();
            $totalItemsReturned = $debitNotes->sum(function($n) { return $n->items ? $n->items->count() : 0; });
            $uniqueSuppliers = $debitNotes->pluck('supplier_id')->unique()->count();
        @endphp
        <div class="inv-stats-grid">
            <div class="inv-stat-card stat-purple">
                <div class="inv-stat-info">
                    <span class="inv-stat-label">Total Debit Notes</span>
                    <span class="inv-stat-val">{{ $totalDebitNotes }}</span>
                </div>
                <div class="inv-stat-icon">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>
            <div class="inv-stat-card stat-danger">
                <div class="inv-stat-info">
                    <span class="inv-stat-label">Returned Items</span>
                    <span class="inv-stat-val text-danger">{{ $totalItemsReturned }}</span>
                </div>
                <div class="inv-stat-icon">
                    <i class="fas fa-undo-alt"></i>
                </div>
            </div>
            <div class="inv-stat-card stat-info">
                <div class="inv-stat-info">
                    <span class="inv-stat-label">Suppliers Impacted</span>
                    <span class="inv-stat-val">{{ $uniqueSuppliers }}</span>
                </div>
                <div class="inv-stat-icon">
                    <i class="fas fa-truck"></i>
                </div>
            </div>
            <div class="inv-stat-card stat-dark">
                <div class="inv-stat-info">
                    <span class="inv-stat-label">Stock Status</span>
                    <span class="inv-stat-val text-success" style="font-size: 1.3rem;">Adjusted</span>
                </div>
                <div class="inv-stat-icon">
                    <i class="fas fa-balance-scale"></i>
                </div>
            </div>
        </div>

        {{-- 4. Instant Search Toolbar --}}
        <div class="inv-toolbar">
            <div class="inv-search-box">
                <i class="fas fa-search inv-search-icon"></i>
                <input type="text" id="debitNoteSearchInput" class="inv-search-input" placeholder="Instant table search (debit note no, supplier, date)...">
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small fw-bold" id="debitCountLabel">Showing {{ $totalDebitNotes }} Debit Notes</span>
            </div>
        </div>

        {{-- 5. Desktop Table View --}}
        <div class="inv-card">
            <div class="table-responsive">
                <table class="inv-table" id="debitNotesDesktopTable">
                    <thead>
                        <tr>
                            <th style="width: 70px;">#</th>
                            <th>Debit Note No</th>
                            <th>Date</th>
                            <th>Supplier</th>
                            <th class="text-center">Items</th>
                            <th>Issued By</th>
                            <th>Remarks</th>
                            <th class="text-end" style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="debitNotesTableBody">
                        @forelse($debitNotes as $key => $note)
                        <tr class="debit-row"
                            data-no="{{ strtolower($note->debit_note_no) }}"
                            data-supplier="{{ strtolower($note->supplier ? $note->supplier->supplier_name : '') }}"
                            data-date="{{ $note->debit_date ? $note->debit_date->format('d-m-Y') : '' }}">
                            <td class="text-muted fw-bold">{{ $key + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width: 32px; height: 32px; border-radius: 8px; background: var(--inv-purple-bg); color: var(--inv-purple-text); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem;">
                                        <i class="fas fa-file-invoice"></i>
                                    </div>
                                    <div>
                                        <strong class="text-dark">{{ $note->debit_note_no }}</strong>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-muted small">
                                    <i class="far fa-calendar-alt me-1 text-secondary"></i> {{ $note->debit_date ? $note->debit_date->format('d M, Y') : '-' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-store text-muted small"></i>
                                    <span class="fw-bold text-dark">{{ $note->supplier->supplier_name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="inv-badge badge-purple">
                                    {{ $note->items ? $note->items->count() : 0 }} Items
                                </span>
                            </td>
                            <td>
                                <span class="small text-dark fw-semibold">{{ $note->user->name ?? 'System' }}</span>
                            </td>
                            <td>
                                <span class="text-muted small">{{ Str::limit($note->remarks ?? 'N/A', 30) }}</span>
                            </td>
                            <td>
                                <div class="inv-actions justify-content-end">
                                    <a href="{{ route('debit-notes.show', $note->id) }}" 
                                       class="inv-btn-action btn-view" 
                                       title="View Debit Note">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if(auth()->user()->hasPermission('inventory_setting', 'delete'))
                                    <form action="{{ route('debit-notes.destroy', $note->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this debit note? Stock will be restored.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inv-btn-action btn-del" title="Delete Debit Note">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-file-invoice fa-3x mb-3 text-secondary opacity-50"></i>
                                <h6 class="fw-bold">No Debit Notes Found</h6>
                                <p class="small text-muted mb-3">No debit notes matching your date range and vendor filter criteria.</p>
                                <a href="{{ route('debit-notes.index', ['from_date' => '2000-01-01', 'to_date' => date('Y-m-d')]) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    <i class="fas fa-infinity me-1"></i> View All Debit Notes
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 6. Mobile Cards View --}}
        <div class="inv-mobile-cards" id="debitNoteMobileCards">
            @forelse($debitNotes as $key => $note)
            <div class="inv-mobile-item debit-row"
                 data-no="{{ strtolower($note->debit_note_no) }}"
                 data-supplier="{{ strtolower($note->supplier ? $note->supplier->supplier_name : '') }}"
                 data-date="{{ $note->debit_date ? $note->debit_date->format('d-m-Y') : '' }}">
                <div class="inv-mobile-top">
                    <div>
                        <strong class="text-dark">{{ $note->debit_note_no }}</strong>
                        <div class="text-muted small">
                            <i class="far fa-calendar-alt me-1"></i> {{ $note->debit_date ? $note->debit_date->format('d M, Y') : '-' }}
                        </div>
                    </div>
                    <span class="inv-badge badge-purple">
                        {{ $note->items ? $note->items->count() : 0 }} Items
                    </span>
                </div>

                <div class="inv-mobile-meta-row">
                    <div>
                        <i class="fas fa-store text-muted me-1"></i>
                        <strong>{{ $note->supplier->supplier_name ?? 'N/A' }}</strong>
                    </div>
                    <div class="text-muted small">
                        <span>{{ Str::limit($note->remarks ?? 'No remarks', 25) }}</span>
                    </div>
                </div>

                <div class="inv-mobile-actions">
                    <a href="{{ route('debit-notes.show', $note->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-3">
                        <i class="fas fa-eye me-1"></i> View
                    </a>

                    @if(auth()->user()->hasPermission('inventory_setting', 'delete'))
                    <form action="{{ route('debit-notes.destroy', $note->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this debit note? Stock will be restored.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                            <i class="fas fa-trash-alt me-1"></i> Delete
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-4 text-muted">
                <p class="mb-0">No debit notes found.</p>
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
    function applyDebitPreset(type) {
        const now = new Date();
        const fromInput = document.getElementById('filterFromDate');
        const toInput = document.getElementById('filterToDate');
        const form = document.getElementById('debitFilterForm');

        function formatDate(d) {
            let month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();
            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;
            return [year, month, day].join('-');
        }

        if (type === 'current_month') {
            const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
            const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
            fromInput.value = formatDate(firstDay);
            toInput.value = formatDate(lastDay);
        } else if (type === 'last_30_days') {
            const past30 = new Date();
            past30.setDate(now.getDate() - 30);
            fromInput.value = formatDate(past30);
            toInput.value = formatDate(now);
        } else if (type === 'this_year') {
            const firstDayYear = new Date(now.getFullYear(), 0, 1);
            const lastDayYear = new Date(now.getFullYear(), 11, 31);
            fromInput.value = formatDate(firstDayYear);
            toInput.value = formatDate(lastDayYear);
        }

        if (form) form.submit();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('debitNoteSearchInput');
        const rows = document.querySelectorAll('.debit-row');
        const countLabel = document.getElementById('debitCountLabel');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.trim().toLowerCase();
                let visibleCount = 0;

                rows.forEach(row => {
                    const no = (row.dataset.no || '').toLowerCase();
                    const supplier = (row.dataset.supplier || '').toLowerCase();
                    const date = (row.dataset.date || '').toLowerCase();
                    if (!query || no.includes(query) || supplier.includes(query) || date.includes(query)) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (countLabel) {
                    countLabel.textContent = `Showing ${visibleCount} Debit Notes`;
                }
            });
        }
    });
</script>
@endsection