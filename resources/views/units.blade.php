@extends('layouts.app')

@section('title')
<title>Admin - Manage Units</title>
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
                <div class="inv-header-icon icon-units">
                    <i class="fas fa-ruler-combined"></i>
                </div>
                <div class="inv-header-title-meta">
                    <span class="inv-header-eyebrow">Inventory Settings</span>
                    <h1 class="inv-header-title">Measurement Units</h1>
                    <p class="inv-header-sub">Configure measurement units for raw materials and inventory (e.g. Kg, Liter, Pcs, Grams)</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if(auth()->user()->hasPermission('inventory_setting', 'add'))
                <button type="button" class="btn-inv-primary" data-bs-toggle="modal" data-bs-target="#addUnitModal">
                    <i class="fas fa-plus"></i>
                    <span>Add Unit</span>
                </button>
                @endif
            </div>
        </div>

        {{-- 2. Stats Grid --}}
        @php
            $totalUnits = $data->count();
        @endphp
        <div class="inv-stats-grid">
            <div class="inv-stat-card stat-info">
                <div class="inv-stat-info">
                    <span class="inv-stat-label">Total Units</span>
                    <span class="inv-stat-val">{{ $totalUnits }}</span>
                </div>
                <div class="inv-stat-icon">
                    <i class="fas fa-ruler"></i>
                </div>
            </div>
            <div class="inv-stat-card stat-success">
                <div class="inv-stat-info">
                    <span class="inv-stat-label">Status</span>
                    <span class="inv-stat-val text-success">Active</span>
                </div>
                <div class="inv-stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        {{-- 3. Filter & Search Toolbar --}}
        <div class="inv-toolbar">
            <div class="inv-search-box">
                <i class="fas fa-search inv-search-icon"></i>
                <input type="text" id="unitSearchInput" class="inv-search-input" placeholder="Search units by name...">
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small fw-bold">Total: {{ $totalUnits }} Units</span>
            </div>
        </div>

        {{-- 4. Desktop Table View --}}
        <div class="inv-card">
            <div class="table-responsive">
                <table class="inv-table" id="unitsDesktopTable">
                    <thead>
                        <tr>
                            <th style="width: 80px;">#</th>
                            <th>Unit Name</th>
                            <th>Type / Code</th>
                            <th class="text-end" style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="unitsTableBody">
                        @forelse($data as $key => $value)
                        <tr class="unit-row" data-name="{{ strtolower($value->name) }}">
                            <td class="text-muted fw-bold">{{ $key + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width: 38px; height: 38px; border-radius: 10px; background: var(--inv-info-bg); color: var(--inv-info-text); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.9rem;">
                                        <i class="fas fa-weight-hanging"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold" style="color: var(--inv-dark); font-size: 0.95rem;">{{ $value->name }}</h6>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="inv-badge badge-unit">
                                    <i class="fas fa-tag"></i> {{ strtoupper($value->name) }}
                                </span>
                            </td>
                            <td>
                                <div class="inv-actions justify-content-end">
                                    @if(auth()->user()->hasPermission('inventory_setting', 'edit'))
                                    <button type="button" 
                                            class="inv-btn-action btn-edit edit-btn"
                                            title="Edit Unit"
                                            data-id="{{ $value->id }}"
                                            data-name="{{ $value->name }}">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    @endif

                                    @if(auth()->user()->hasPermission('inventory_setting', 'delete'))
                                    <a href="{{ route('manage.units.delete', $value->id) }}"
                                       class="inv-btn-action btn-del"
                                       title="Delete Unit"
                                       onclick="return confirm('Are you sure you want to delete unit {{ addslashes($value->name) }}?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-ruler-combined fa-3x mb-3 text-secondary opacity-50"></i>
                                <h6 class="fw-bold">No Measurement Units Found</h6>
                                <p class="small text-muted mb-0">Click "+ Add Unit" to define your first measurement unit.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 5. Mobile Cards View --}}
        <div class="inv-mobile-cards" id="unitsMobileCards">
            @forelse($data as $key => $value)
            <div class="inv-mobile-item unit-row" data-name="{{ strtolower($value->name) }}">
                <div class="inv-mobile-top">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: var(--inv-info-bg); color: var(--inv-info-text); display: flex; align-items: center; justify-content: center; font-weight: 800;">
                            <i class="fas fa-weight-hanging"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold" style="color: var(--inv-dark);">{{ $value->name }}</h6>
                            <small class="text-muted">Unit #{{ $key + 1 }}</small>
                        </div>
                    </div>
                    <span class="inv-badge badge-unit">
                        {{ strtoupper($value->name) }}
                    </span>
                </div>
                <div class="inv-mobile-actions">
                    @if(auth()->user()->hasPermission('inventory_setting', 'edit'))
                    <button type="button" 
                            class="btn btn-sm btn-outline-primary rounded-pill px-3 edit-btn"
                            data-id="{{ $value->id }}"
                            data-name="{{ $value->name }}">
                        <i class="fas fa-pen me-1"></i> Edit
                    </button>
                    @endif

                    @if(auth()->user()->hasPermission('inventory_setting', 'delete'))
                    <a href="{{ route('manage.units.delete', $value->id) }}"
                       class="btn btn-sm btn-outline-danger rounded-pill px-3"
                       onclick="return confirm('Delete this unit?')">
                        <i class="fas fa-trash-alt me-1"></i> Delete
                    </a>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-4 text-muted">
                <p class="mb-0">No measurement units found.</p>
            </div>
            @endforelse
        </div>

    </div>

</div>
</div>

{{-- Add Modal --}}
<div class="modal fade" id="addUnitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content inv-modal-content">
            <form method="POST" action="{{ route('manage.units.insert') }}">
                @csrf
                <div class="inv-modal-header">
                    <h5 class="inv-modal-title">
                        <span class="inv-stat-icon" style="width: 36px; height: 36px; font-size: 1rem; background: var(--inv-info-bg); color: var(--inv-info-text);">
                            <i class="fas fa-plus"></i>
                        </span>
                        <span>Add Measurement Unit</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="inv-modal-body">
                    <div class="mb-3">
                        <label class="inv-form-label">Unit Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="inv-form-control" required placeholder="e.g. Kilogram, Liter, Pcs, Box, Gram">
                        <small class="text-muted mt-1 d-block">Specify unit name or standard abbreviation</small>
                    </div>
                </div>

                <div class="inv-modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-inv-primary">
                        <i class="fas fa-check-circle"></i> Save Unit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editUnitModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content inv-modal-content">
            <form method="POST" action="{{ route('manage.units.update') }}">
                @csrf
                <input type="hidden" name="id" id="edit_id">

                <div class="inv-modal-header">
                    <h5 class="inv-modal-title">
                        <span class="inv-stat-icon" style="width: 36px; height: 36px; font-size: 1rem; background: var(--inv-info-bg); color: var(--inv-info-text);">
                            <i class="fas fa-edit"></i>
                        </span>
                        <span>Edit Measurement Unit</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="inv-modal-body">
                    <div class="mb-3">
                        <label class="inv-form-label">Unit Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name" class="inv-form-control" required>
                    </div>
                </div>

                <div class="inv-modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-inv-primary">
                        <i class="fas fa-save"></i> Update Unit
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
        const editButtons = document.querySelectorAll('.edit-btn');
        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                document.getElementById('edit_id').value = this.dataset.id || '';
                document.getElementById('edit_name').value = this.dataset.name || '';
                const editModal = new bootstrap.Modal(document.getElementById('editUnitModal'));
                editModal.show();
            });
        });

        // Live Search
        const searchInput = document.getElementById('unitSearchInput');
        const rows = document.querySelectorAll('.unit-row');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.trim().toLowerCase();
                rows.forEach(row => {
                    const name = (row.dataset.name || '').toLowerCase();
                    if (!query || name.includes(query)) {
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