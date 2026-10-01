@extends('layouts.app')

@section('title')
<title>Admin - Manage Suppliers</title>
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
                <div class="inv-header-icon icon-suppliers">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="inv-header-title-meta">
                    <span class="inv-header-eyebrow">Supplier Directory</span>
                    <h1 class="inv-header-title">Vendor & Supplier Management</h1>
                    <p class="inv-header-sub">Manage vendor contacts, purchase histories, shop details, and financial ledgers</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if(auth()->user()->hasPermission('inventory_setting', 'add'))
                <button type="button" class="btn-inv-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-user-plus"></i>
                    <span>Add Supplier</span>
                </button>
                @endif
            </div>
        </div>

        {{-- 2. Stats Grid --}}
        @php
            $totalSuppliers = $suppliers->count();
            $totalOutstanding = $suppliers->sum('opening_outstanding');
        @endphp
        <div class="inv-stats-grid">
            <div class="inv-stat-card stat-success">
                <div class="inv-stat-info">
                    <span class="inv-stat-label">Total Suppliers</span>
                    <span class="inv-stat-val">{{ $totalSuppliers }}</span>
                </div>
                <div class="inv-stat-icon">
                    <i class="fas fa-handshake"></i>
                </div>
            </div>
            <div class="inv-stat-card stat-primary">
                <div class="inv-stat-info">
                    <span class="inv-stat-label">Opening Outstanding</span>
                    <span class="inv-stat-val">₹{{ number_format($totalOutstanding, 2) }}</span>
                </div>
                <div class="inv-stat-icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
            </div>
            <div class="inv-stat-card stat-info">
                <div class="inv-stat-info">
                    <span class="inv-stat-label">Active Vendors</span>
                    <span class="inv-stat-val text-info">{{ $totalSuppliers }}</span>
                </div>
                <div class="inv-stat-icon">
                    <i class="fas fa-store"></i>
                </div>
            </div>
            <div class="inv-stat-card stat-dark">
                <div class="inv-stat-info">
                    <span class="inv-stat-label">Ledger Tracking</span>
                    <span class="inv-stat-val text-success" style="font-size: 1.3rem;">Active</span>
                </div>
                <div class="inv-stat-icon">
                    <i class="fas fa-book"></i>
                </div>
            </div>
        </div>

        {{-- 3. Filter & Search Toolbar --}}
        <div class="inv-toolbar">
            <div class="inv-search-box">
                <i class="fas fa-search inv-search-icon"></i>
                <input type="text" id="supplierSearchInput" class="inv-search-input" placeholder="Search supplier by name, shop, phone, email...">
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small fw-bold">Total: {{ $totalSuppliers }} Vendors</span>
            </div>
        </div>

        {{-- 4. Desktop Table View --}}
        <div class="inv-card">
            <div class="table-responsive">
                <table class="inv-table" id="supplierDesktopTable">
                    <thead>
                        <tr>
                            <th style="width: 70px;">#</th>
                            <th>Supplier Details</th>
                            <th>Shop / Business</th>
                            <th>Contact Info</th>
                            <th>Opening Due</th>
                            <th class="text-end" style="width: 170px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="supplierTableBody">
                        @forelse($suppliers as $key => $supplier)
                        <tr class="supplier-row"
                            data-name="{{ strtolower($supplier->supplier_name) }}"
                            data-shop="{{ strtolower($supplier->shop_name ?? '') }}"
                            data-phone="{{ strtolower($supplier->phone) }}"
                            data-email="{{ strtolower($supplier->email ?? '') }}">
                            <td class="text-muted fw-bold">{{ $key + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width: 38px; height: 38px; border-radius: 10px; background: var(--inv-success-bg); color: var(--inv-success-text); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.95rem;">
                                        <i class="fas fa-user-tag"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold" style="color: var(--inv-dark); font-size: 0.95rem;">{{ $supplier->supplier_name }}</h6>
                                        <small class="text-muted">{{ Str::limit($supplier->address, 35) ?: 'No address added' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($supplier->shop_name)
                                <span class="inv-badge badge-dark">
                                    <i class="fas fa-store me-1 text-primary"></i> {{ $supplier->shop_name }}
                                </span>
                                @else
                                <span class="text-muted small">N/A</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <a href="tel:{{ $supplier->phone }}" class="text-dark fw-bold text-decoration-none small">
                                        <i class="fas fa-phone-alt text-primary me-1"></i> {{ $supplier->phone }}
                                    </a>
                                    @if($supplier->email)
                                    <span class="text-muted small">
                                        <i class="fas fa-envelope text-muted me-1"></i> {{ $supplier->email }}
                                    </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold {{ ($supplier->opening_outstanding > 0) ? 'text-danger' : 'text-success' }}">
                                    ₹{{ number_format($supplier->opening_outstanding ?? 0, 2) }}
                                </span>
                            </td>
                            <td>
                                <div class="inv-actions justify-content-end">
                                    <a href="{{ route('suppliers.ledger', $supplier->id) }}" 
                                       class="inv-btn-action btn-ledger"
                                       title="View Supplier Ledger">
                                        <i class="fas fa-book"></i>
                                    </a>

                                    @if(auth()->user()->hasPermission('inventory_setting', 'edit'))
                                    <button type="button" 
                                            class="inv-btn-action btn-edit edit-btn"
                                            title="Edit Supplier"
                                            data-id="{{ $supplier->id }}"
                                            data-name="{{ $supplier->supplier_name }}"
                                            data-shop="{{ $supplier->shop_name }}"
                                            data-phone="{{ $supplier->phone }}"
                                            data-email="{{ $supplier->email }}"
                                            data-address="{{ $supplier->address }}"
                                            data-outstanding="{{ $supplier->opening_outstanding }}">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    @endif

                                    @if(auth()->user()->hasPermission('inventory_setting', 'delete'))
                                    <a href="{{ route('suppliers.delete', $supplier->id) }}"
                                       class="inv-btn-action btn-del"
                                       title="Delete Supplier"
                                       onclick="return confirm('Are you sure you want to delete supplier {{ addslashes($supplier->supplier_name) }}?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-truck fa-3x mb-3 text-secondary opacity-50"></i>
                                <h6 class="fw-bold">No Suppliers Found</h6>
                                <p class="small text-muted mb-0">Click "+ Add Supplier" to register your vendor contacts.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 5. Mobile Cards View --}}
        <div class="inv-mobile-cards" id="supplierMobileCards">
            @forelse($suppliers as $key => $supplier)
            <div class="inv-mobile-item supplier-row"
                 data-name="{{ strtolower($supplier->supplier_name) }}"
                 data-shop="{{ strtolower($supplier->shop_name ?? '') }}"
                 data-phone="{{ strtolower($supplier->phone) }}"
                 data-email="{{ strtolower($supplier->email ?? '') }}">
                <div class="inv-mobile-top">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: var(--inv-success-bg); color: var(--inv-success-text); display: flex; align-items: center; justify-content: center; font-weight: 800;">
                            <i class="fas fa-user-tag"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold" style="color: var(--inv-dark);">{{ $supplier->supplier_name }}</h6>
                            <small class="text-muted">{{ $supplier->shop_name ?: 'Vendor #'.($key+1) }}</small>
                        </div>
                    </div>
                    <span class="fw-bold {{ ($supplier->opening_outstanding > 0) ? 'text-danger' : 'text-success' }}">
                        ₹{{ number_format($supplier->opening_outstanding ?? 0, 2) }}
                    </span>
                </div>

                <div class="inv-mobile-meta-row">
                    <div>
                        <a href="tel:{{ $supplier->phone }}" class="text-dark fw-bold text-decoration-none small">
                            <i class="fas fa-phone-alt text-primary me-1"></i> {{ $supplier->phone }}
                        </a>
                    </div>
                    @if($supplier->email)
                    <div class="text-muted small text-truncate" style="max-width: 180px;">
                        <i class="fas fa-envelope text-muted me-1"></i> {{ $supplier->email }}
                    </div>
                    @endif
                </div>

                <div class="inv-mobile-actions">
                    <a href="{{ route('suppliers.ledger', $supplier->id) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3">
                        <i class="fas fa-book me-1"></i> Ledger
                    </a>

                    @if(auth()->user()->hasPermission('inventory_setting', 'edit'))
                    <button type="button" 
                            class="btn btn-sm btn-outline-primary rounded-pill px-3 edit-btn"
                            data-id="{{ $supplier->id }}"
                            data-name="{{ $supplier->supplier_name }}"
                            data-shop="{{ $supplier->shop_name }}"
                            data-phone="{{ $supplier->phone }}"
                            data-email="{{ $supplier->email }}"
                            data-address="{{ $supplier->address }}"
                            data-outstanding="{{ $supplier->opening_outstanding }}">
                        <i class="fas fa-pen me-1"></i> Edit
                    </button>
                    @endif

                    @if(auth()->user()->hasPermission('inventory_setting', 'delete'))
                    <a href="{{ route('suppliers.delete', $supplier->id) }}"
                       class="btn btn-sm btn-outline-danger rounded-pill px-3"
                       onclick="return confirm('Delete this supplier?')">
                        <i class="fas fa-trash-alt me-1"></i> Delete
                    </a>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-4 text-muted">
                <p class="mb-0">No suppliers registered.</p>
            </div>
            @endforelse
        </div>

    </div>

</div>
</div>

{{-- Add Supplier Modal --}}
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content inv-modal-content">
            <form method="POST" action="{{ route('suppliers.store') }}">
                @csrf
                <div class="inv-modal-header">
                    <h5 class="inv-modal-title">
                        <span class="inv-stat-icon" style="width: 36px; height: 36px; font-size: 1rem; background: var(--inv-success-bg); color: var(--inv-success-text);">
                            <i class="fas fa-user-plus"></i>
                        </span>
                        <span>Add New Supplier</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="inv-modal-body">
                    <div class="mb-3">
                        <label class="inv-form-label">Supplier Name <span class="text-danger">*</span></label>
                        <input type="text" name="supplier_name" class="inv-form-control" required placeholder="e.g. Ramesh Kumar">
                    </div>

                    <div class="mb-3">
                        <label class="inv-form-label">Shop / Company Name</label>
                        <input type="text" name="shop_name" class="inv-form-control" placeholder="e.g. Shree Krishna Dairy & Agro">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="inv-form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="inv-form-control" required placeholder="e.g. 9876543210" maxlength="20">
                        </div>
                        <div class="col-md-6">
                            <label class="inv-form-label">Email Address</label>
                            <input type="email" name="email" class="inv-form-control" placeholder="vendor@example.com">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="inv-form-label">Opening Outstanding (₹)</label>
                        <input type="number" name="opening_outstanding" class="inv-form-control" step="0.01" min="0" value="0">
                        <small class="text-muted mt-1 d-block">Starting balance due to this supplier</small>
                    </div>

                    <div class="mb-3">
                        <label class="inv-form-label">Address</label>
                        <textarea name="address" class="inv-form-control" rows="2" placeholder="Street, market area, city..."></textarea>
                    </div>
                </div>

                <div class="inv-modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-inv-primary">
                        <i class="fas fa-check-circle"></i> Save Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Supplier Modal --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content inv-modal-content">
            <form method="POST" action="{{ route('suppliers.update') }}">
                @csrf
                <input type="hidden" name="id" id="edit_id">

                <div class="inv-modal-header">
                    <h5 class="inv-modal-title">
                        <span class="inv-stat-icon" style="width: 36px; height: 36px; font-size: 1rem; background: var(--inv-success-bg); color: var(--inv-success-text);">
                            <i class="fas fa-user-edit"></i>
                        </span>
                        <span>Edit Supplier</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="inv-modal-body">
                    <div class="mb-3">
                        <label class="inv-form-label">Supplier Name <span class="text-danger">*</span></label>
                        <input type="text" name="supplier_name" id="edit_name" class="inv-form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="inv-form-label">Shop / Company Name</label>
                        <input type="text" name="shop_name" id="edit_shop" class="inv-form-control">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="inv-form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" id="edit_phone" class="inv-form-control" required maxlength="20">
                        </div>
                        <div class="col-md-6">
                            <label class="inv-form-label">Email Address</label>
                            <input type="email" name="email" id="edit_email" class="inv-form-control">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="inv-form-label">Opening Outstanding (₹)</label>
                        <input type="number" name="opening_outstanding" id="edit_outstanding" class="inv-form-control" step="0.01" min="0">
                    </div>

                    <div class="mb-3">
                        <label class="inv-form-label">Address</label>
                        <textarea name="address" id="edit_address" class="inv-form-control" rows="2"></textarea>
                    </div>
                </div>

                <div class="inv-modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-inv-primary">
                        <i class="fas fa-save"></i> Update Supplier
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
                document.getElementById('edit_shop').value = this.dataset.shop || '';
                document.getElementById('edit_phone').value = this.dataset.phone || '';
                document.getElementById('edit_email').value = this.dataset.email || '';
                document.getElementById('edit_address').value = this.dataset.address || '';
                document.getElementById('edit_outstanding').value = this.dataset.outstanding || '0';

                const editModal = new bootstrap.Modal(document.getElementById('editModal'));
                editModal.show();
            });
        });

        // Live Search
        const searchInput = document.getElementById('supplierSearchInput');
        const rows = document.querySelectorAll('.supplier-row');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.trim().toLowerCase();
                rows.forEach(row => {
                    const name = (row.dataset.name || '').toLowerCase();
                    const shop = (row.dataset.shop || '').toLowerCase();
                    const phone = (row.dataset.phone || '').toLowerCase();
                    const email = (row.dataset.email || '').toLowerCase();
                    if (!query || name.includes(query) || shop.includes(query) || phone.includes(query) || email.includes(query)) {
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