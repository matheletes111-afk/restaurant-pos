@extends('layouts.app')

@section('title')
<title>Admin || Franchise Sponsorship Owner Management</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<style>
  :root {
    --fso-primary: #ff5e14;
    --fso-primary-hover: #ea580c;
    --fso-dark: #0f172a;
    --fso-slate: #1e293b;
    --fso-border: #e2e8f0;
    --fso-border-light: #f1f5f9;
  }

  body {
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    color: var(--fso-slate);
  }

  .fso-page-wrap {
    padding: 24px 28px;
  }
  @media (max-width: 768px) {
    .fso-page-wrap {
      padding: 16px 14px;
    }
  }

  /* Header Deck */
  .fso-header-deck {
    background: #ffffff;
    border-radius: 16px;
    padding: 20px 24px;
    margin-bottom: 22px;
    box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
    border: 1px solid var(--fso-border-light);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
  }
  .fso-header-left {
    display: flex;
    align-items: center;
    gap: 16px;
  }
  .fso-header-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    background: linear-gradient(135deg, #ff5e14 0%, #ff8c42 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    box-shadow: 0 6px 18px rgba(255, 94, 20, 0.28);
    flex-shrink: 0;
  }
  .fso-header-eyebrow {
    font-size: 0.72rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--fso-primary);
    margin-bottom: 2px;
  }
  .fso-header-title {
    font-family: 'Outfit', sans-serif;
    font-size: 1.45rem;
    font-weight: 800;
    color: var(--fso-dark);
    margin: 0;
    line-height: 1.25;
  }
  .fso-header-sub {
    font-size: 0.82rem;
    color: #64748b;
    margin: 0;
  }

  /* Action Button */
  .btn-fso-add {
    background: linear-gradient(135deg, #ff5e14 0%, #ff8c42 100%);
    color: #ffffff;
    border: none;
    border-radius: 30px;
    padding: 10px 22px;
    font-weight: 700;
    font-size: 0.88rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 16px rgba(255, 94, 20, 0.25);
    transition: all 0.2s ease;
    text-decoration: none;
    cursor: pointer;
  }
  .btn-fso-add:hover {
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 8px 22px rgba(255, 94, 20, 0.35);
  }

  /* Metric KPI Cards */
  .fso-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
    margin-bottom: 22px;
  }
  .fso-stat-card {
    background: #ffffff;
    border-radius: 14px;
    padding: 18px 20px;
    border: 1px solid var(--fso-border-light);
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.03);
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    overflow: hidden;
  }
  .fso-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    width: 4px;
  }
  .fso-stat-card.stat-primary::before { background: #ff5e14; }
  .fso-stat-card.stat-success::before { background: #10b981; }
  .fso-stat-card.stat-warning::before { background: #f59e0b; }
  .fso-stat-card.stat-indigo::before { background: #6366f1; }

  .fso-stat-label {
    font-size: 0.74rem;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 4px;
    display: block;
  }
  .fso-stat-val {
    font-family: 'Outfit', sans-serif;
    font-size: 1.65rem;
    font-weight: 800;
    color: var(--fso-dark);
    line-height: 1.1;
  }
  .fso-stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
  }

  /* Main Card & Table */
  .fso-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid var(--fso-border-light);
    box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
    overflow: hidden;
  }
  .fso-card-header {
    padding: 18px 24px;
    border-bottom: 1px solid var(--fso-border-light);
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .fso-card-title {
    font-family: 'Outfit', sans-serif;
    font-size: 1.15rem;
    font-weight: 800;
    color: var(--fso-dark);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .fso-table {
    width: 100%;
    margin-bottom: 0;
  }
  .fso-table thead th {
    background: #f8fafc;
    padding: 14px 18px;
    font-family: 'Outfit', sans-serif;
    font-size: 0.78rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #64748b;
    border-bottom: 1px solid var(--fso-border);
    white-space: nowrap;
  }
  .fso-table tbody td {
    padding: 14px 18px;
    border-bottom: 1px solid var(--fso-border-light);
    vertical-align: middle;
    font-size: 0.88rem;
  }
  .fso-table tbody tr:hover td {
    background-color: #fafbfc;
  }

  /* Avatar Initials */
  .owner-avatar {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
    color: #4f46e5;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    margin-right: 12px;
    flex-shrink: 0;
    border: 1px solid #c7d2fe;
  }

  /* Role & Status Badges */
  .badge-role-fso {
    background: #eef2ff;
    color: #4338ca;
    border: 1px solid #c7d2fe;
    font-size: 0.75rem;
    font-weight: 800;
    padding: 4px 10px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    letter-spacing: 0.04em;
  }
  .badge-status-active {
    background: #ecfdf5;
    color: #047857;
    border: 1px solid #a7f3d0;
    font-size: 0.74rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
  }
  .badge-status-inactive {
    background: #fffbeb;
    color: #b45309;
    border: 1px solid #fde68a;
    font-size: 0.74rem;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
  }

  /* Form Modal Styling */
  .modal-content {
    border-radius: 18px;
    border: none;
    box-shadow: 0 20px 40px rgba(15, 23, 42, 0.18);
  }
  .modal-header {
    background: #f8fafc;
    border-bottom: 1px solid var(--fso-border);
    padding: 18px 24px;
    border-top-left-radius: 18px;
    border-top-right-radius: 18px;
  }
  .modal-title {
    font-family: 'Outfit', sans-serif;
    font-weight: 800;
    color: var(--fso-dark);
  }
  .modal-body {
    padding: 24px;
  }
  .fso-form-label {
    font-size: 0.76rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--fso-slate);
    margin-bottom: 6px;
    display: block;
  }
  .fso-form-control {
    width: 100%;
    background: #f8fafc;
    border: 1px solid var(--fso-border);
    border-radius: 10px;
    padding: 9px 14px;
    font-size: 0.88rem;
    color: var(--fso-slate);
    outline: none;
    transition: all 0.2s ease;
  }
  .fso-form-control:focus {
    background: #ffffff;
    border-color: var(--fso-primary);
    box-shadow: 0 0 0 3px rgba(255, 94, 20, 0.12);
  }
  .input-group-pass {
    position: relative;
  }
  .btn-pass-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    z-index: 5;
  }
  .btn-pass-toggle:hover {
    color: var(--fso-primary);
  }

  /* DataTables layout refinement */
  .dataTables_wrapper {
    padding: 16px 0;
  }
  .dataTables_wrapper .dataTables_filter {
    padding: 0 20px 14px 20px;
  }
  .dataTables_wrapper .dataTables_filter input {
    border: 1px solid var(--fso-border);
    border-radius: 8px;
    padding: 6px 12px;
    font-size: 0.88rem;
    background: #f8fafc;
  }
  .dataTables_wrapper .dataTables_length {
    padding: 0 20px 14px 20px;
    font-size: 0.85rem;
  }
  .dataTables_wrapper .dataTables_info,
  .dataTables_wrapper .dataTables_paginate {
    padding: 14px 20px;
  }
</style>
@endsection

@section('body')
@include('includes.sidebar')

<div class="pc-container">
<div class="pc-content">

    <div class="fso-page-wrap">
        {{-- Flash / Error Messages --}}
        @include('includes.message')

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-1"></i> Please check form inputs:</h6>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        {{-- 1. Header Deck --}}
        <div class="fso-header-deck">
            <div class="fso-header-left">
                <div class="fso-header-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <div>
                    <span class="fso-header-eyebrow">Enterprise & Sponsorship Portal</span>
                    <h1 class="fso-header-title">Franchise Sponsorship Owner Management</h1>
                    <p class="fso-header-sub">Super admin management of Franchise Sponsorship Owners (Role: FSO), credentials, and contact details</p>
                </div>
            </div>
            <div>
                <button type="button" class="btn-fso-add" data-bs-toggle="modal" data-bs-target="#addFsoModal">
                    <i class="fas fa-user-plus"></i> + Add Franchise Owner
                </button>
            </div>
        </div>

        {{-- 2. Metric KPI Cards --}}
        <div class="fso-stats-grid">
            <div class="fso-stat-card stat-primary">
                <div>
                    <span class="fso-stat-label">Total Franchise Owners</span>
                    <span class="fso-stat-val">{{ number_format($summary['total']) }}</span>
                    <small class="text-muted d-block mt-1">Role: FSO Accounts</small>
                </div>
                <div class="fso-stat-icon" style="background: rgba(255, 94, 20, 0.1); color: #ff5e14;">
                    <i class="fas fa-users"></i>
                </div>
            </div>

            <div class="fso-stat-card stat-success">
                <div>
                    <span class="fso-stat-label">Active Accounts</span>
                    <span class="fso-stat-val">{{ number_format($summary['active']) }}</span>
                    <small class="text-success d-block mt-1 font-semibold"><i class="fas fa-check-circle me-1"></i>Ready for login</small>
                </div>
                <div class="fso-stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>

            <div class="fso-stat-card stat-warning">
                <div>
                    <span class="fso-stat-label">Inactive Accounts</span>
                    <span class="fso-stat-val">{{ number_format($summary['inactive']) }}</span>
                    <small class="text-warning d-block mt-1 font-semibold"><i class="fas fa-user-clock me-1"></i>Suspended / Inactive</small>
                </div>
                <div class="fso-stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                    <i class="fas fa-user-slash"></i>
                </div>
            </div>

            <div class="fso-stat-card stat-indigo">
                <div>
                    <span class="fso-stat-label">New This Month</span>
                    <span class="fso-stat-val">{{ number_format($summary['new_this_month']) }}</span>
                    <small class="text-indigo d-block mt-1 font-semibold" style="color: #6366f1;"><i class="fas fa-calendar-plus me-1"></i>Joined in {{ now()->format('M Y') }}</small>
                </div>
                <div class="fso-stat-icon" style="background: rgba(99, 102, 241, 0.1); color: #6366f1;">
                    <i class="fas fa-user-tie"></i>
                </div>
            </div>
        </div>

        {{-- 3. Data Table Card --}}
        <div class="fso-card">
            <div class="fso-card-header">
                <h3 class="fso-card-title">
                    <i class="fas fa-list text-primary"></i> Registered Franchise Sponsorship Owners
                </h3>
                <span class="text-muted small">Showing {{ $owners->count() }} total FSO profiles</span>
            </div>

            <div class="table-responsive">
                <table class="table fso-table align-middle" id="fsoTable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th>Owner Details</th>
                            <th>Email Address</th>
                            <th>Contact Phone</th>
                            <th>Address</th>
                            <th class="text-center">Role</th>
                            <th class="text-center">Status</th>
                            <th>Created At</th>
                            <th class="text-end" style="width: 130px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($owners as $key => $owner)
                        @php
                            $isActive = in_array(strtoupper($owner->status ?? 'A'), ['A', 'ACTIVE', '1']);
                            $initials = collect(explode(' ', $owner->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
                        @endphp
                        <tr>
                            <td class="text-muted fw-bold">#{{ $key + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="owner-avatar">
                                        {{ $initials ?: 'FS' }}
                                    </div>
                                    <div>
                                        <strong class="text-dark d-block fs-6">{{ $owner->name }}</strong>
                                        <small class="text-muted">ID: {{ $owner->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $owner->email }}</span>
                            </td>
                            <td>
                                <span class="text-muted font-monospace"><i class="fas fa-phone-alt me-1 text-secondary opacity-75"></i>{{ $owner->phone ?: 'N/A' }}</span>
                            </td>
                            <td>
                                @if($owner->address)
                                    <span class="text-slate-700" title="{{ $owner->address }}">
                                        {{ \Illuminate\Support\Str::limit($owner->address, 35) }}
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge-role-fso" title="Franchise Sponsorship Owner">
                                    <i class="fas fa-id-badge"></i> {{ $owner->role }}
                                </span>
                            </td>
                            <td class="text-center">
                                <form action="{{ route('admin.fso.toggle-status', $owner->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="border-0 bg-transparent p-0" title="Click to Toggle Status">
                                        @if($isActive)
                                            <span class="badge-status-active"><i class="fas fa-check-circle"></i> Active</span>
                                        @else
                                            <span class="badge-status-inactive"><i class="fas fa-ban"></i> Inactive</span>
                                        @endif
                                    </button>
                                </form>
                            </td>
                            <td class="text-muted small">
                                {{ $owner->created_at ? $owner->created_at->format('d M Y') : 'N/A' }}
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-primary editFsoBtn"
                                            style="border-radius: 8px;"
                                            data-id="{{ $owner->id }}"
                                            data-name="{{ $owner->name }}"
                                            data-email="{{ $owner->email }}"
                                            data-phone="{{ $owner->phone }}"
                                            data-address="{{ $owner->address }}"
                                            data-status="{{ $isActive ? 'A' : 'I' }}"
                                            title="Edit Owner">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="{{ route('admin.fso.delete', $owner->id) }}"
                                       class="btn btn-sm btn-outline-danger"
                                       style="border-radius: 8px;"
                                       onclick="return confirm('Are you sure you want to delete Franchise Sponsorship Owner \'{{ addslashes($owner->name) }}\'? This action cannot be undone.')"
                                       title="Delete Owner">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-handshake fa-3x mb-3 text-secondary opacity-50"></i>
                                <h6 class="fw-bold">No Franchise Sponsorship Owners Found</h6>
                                <p class="mb-3 text-secondary">Click the button below to register your first Franchise Sponsorship Owner (Role: FSO).</p>
                                <button type="button" class="btn-fso-add" data-bs-toggle="modal" data-bs-target="#addFsoModal">
                                    <i class="fas fa-user-plus"></i> Add Franchise Owner
                                </button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
</div>

{{-- ==================== MODAL: ADD FRANCHISE OWNER ==================== --}}
<div class="modal fade" id="addFsoModal" tabindex="-1" aria-labelledby="addFsoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="addFsoModalLabel">
                        <i class="fas fa-handshake text-primary me-2"></i> Add Franchise Sponsorship Owner
                    </h5>
                    <small class="text-muted">Account will be created with Role: <strong>FSO</strong></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.fso.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fso-form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="fso-form-control" placeholder="e.g. John Doe" value="{{ old('name') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="fso-form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="fso-form-control" placeholder="e.g. owner@franchise.com" value="{{ old('email') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="fso-form-label">Contact Phone <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="fso-form-control" placeholder="e.g. +91 9876543210" value="{{ old('phone') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="fso-form-label">Account Status <span class="text-danger">*</span></label>
                            <select name="status" class="fso-form-control">
                                <option value="A" {{ old('status', 'A') == 'A' ? 'selected' : '' }}>Active (Allow Login)</option>
                                <option value="I" {{ old('status') == 'I' ? 'selected' : '' }}>Inactive (Suspended)</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="fso-form-label">Physical Address</label>
                            <textarea name="address" rows="3" class="fso-form-control" placeholder="Complete address, street, city, state, pin code...">{{ old('address') }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="fso-form-label">Password <span class="text-danger">*</span></label>
                            <div class="input-group-pass">
                                <input type="password" name="password" id="addPasswordInput" class="fso-form-control" placeholder="Minimum 6 characters" minlength="6" required>
                                <button type="button" class="btn-pass-toggle" onclick="togglePassVisibility('addPasswordInput', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <small class="text-muted">Must be at least 6 characters</small>
                        </div>

                        <div class="col-md-6">
                            <label class="fso-form-label">Assigned User Role</label>
                            <input type="text" class="fso-form-control" value="FSO (Franchise Sponsorship Owner)" readonly style="background: #e2e8f0; font-weight: 700; color: #4338ca;">
                            <small class="text-muted">Fixed system role: <code>FSO</code></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-bottom-left-radius: 18px; border-bottom-right-radius: 18px;">
                    <button type="button" class="btn btn-secondary" style="border-radius: 20px; padding: 8px 20px;" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-fso-add">
                        <i class="fas fa-check-circle"></i> Save Franchise Owner
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ==================== MODAL: EDIT FRANCHISE OWNER ==================== --}}
<div class="modal fade" id="editFsoModal" tabindex="-1" aria-labelledby="editFsoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="editFsoModalLabel">
                        <i class="fas fa-user-edit text-primary me-2"></i> Edit Franchise Sponsorship Owner
                    </h5>
                    <small class="text-muted">Update details for <strong id="editModalOwnerTitle">Owner</strong> (Role: FSO)</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.fso.update') }}" method="POST">
                @csrf
                <input type="hidden" name="id" id="editOwnerId">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="fso-form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editOwnerName" class="fso-form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="fso-form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="editOwnerEmail" class="fso-form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="fso-form-label">Contact Phone <span class="text-danger">*</span></label>
                            <input type="text" name="phone" id="editOwnerPhone" class="fso-form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="fso-form-label">Account Status <span class="text-danger">*</span></label>
                            <select name="status" id="editOwnerStatus" class="fso-form-control">
                                <option value="A">Active (Allow Login)</option>
                                <option value="I">Inactive (Suspended)</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="fso-form-label">Physical Address</label>
                            <textarea name="address" id="editOwnerAddress" rows="3" class="fso-form-control"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="fso-form-label">New Password (Optional)</label>
                            <div class="input-group-pass">
                                <input type="password" name="password" id="editPasswordInput" class="fso-form-control" placeholder="Leave blank to keep existing password" minlength="6">
                                <button type="button" class="btn-pass-toggle" onclick="togglePassVisibility('editPasswordInput', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <small class="text-muted">Enter only if you want to reset password</small>
                        </div>

                        <div class="col-md-6">
                            <label class="fso-form-label">User Role</label>
                            <input type="text" class="fso-form-control" value="FSO (Franchise Sponsorship Owner)" readonly style="background: #e2e8f0; font-weight: 700; color: #4338ca;">
                            <small class="text-muted">Immutable system role</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-bottom-left-radius: 18px; border-bottom-right-radius: 18px;">
                    <button type="button" class="btn btn-secondary" style="border-radius: 20px; padding: 8px 20px;" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-fso-add">
                        <i class="fas fa-save"></i> Update Franchise Owner
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@include('includes.script')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
  $(document).ready(function() {
      if ($('#fsoTable tbody tr').length > 0 && !$('#fsoTable tbody tr td[colspan]').length) {
          $('#fsoTable').DataTable({
              paging: true,
              pageLength: 25,
              lengthMenu: [10, 25, 50, 100],
              ordering: true,
              order: [[0, 'desc']],
              info: true,
              language: {
                  search: "_INPUT_",
                  searchPlaceholder: "Search by name, email, phone..."
              }
          });
      }

      // Handle Edit Button Modal Populator
      $('.editFsoBtn').on('click', function() {
          const id = $(this).data('id');
          const name = $(this).data('name');
          const email = $(this).data('email');
          const phone = $(this).data('phone');
          const address = $(this).data('address') || '';
          const status = $(this).data('status');

          $('#editOwnerId').val(id);
          $('#editOwnerName').val(name);
          $('#editOwnerEmail').val(email);
          $('#editOwnerPhone').val(phone);
          $('#editOwnerAddress').val(address);
          $('#editOwnerStatus').val(status);
          $('#editPasswordInput').val('');
          $('#editModalOwnerTitle').text(name);

          $('#editFsoModal').modal('show');
      });
  });

  function togglePassVisibility(inputId, btn) {
      const input = document.getElementById(inputId);
      const icon = btn.querySelector('i');
      if (input.type === 'password') {
          input.type = 'text';
          icon.classList.remove('fa-eye');
          icon.classList.add('fa-eye-slash');
      } else {
          input.type = 'password';
          icon.classList.remove('fa-eye-slash');
          icon.classList.add('fa-eye');
      }
  }
</script>
@endsection
