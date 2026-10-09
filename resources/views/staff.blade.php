@extends('layouts.app')

@section('title')
<title>Admin || Manage Restaurant Staff</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('admin_template/css/restaurant-staff.css') }}">
@endsection

@section('body')
@include('includes.sidebar')

<div class="pc-container">
<div class="pc-content">

    <div class="staff-page-wrap">
        {{-- Flash / Error Messages --}}
        @include('includes.message')

        {{-- 1. Header Deck --}}
        <div class="staff-header-deck">
            <div class="staff-header-left">
                <div class="staff-header-icon">
                    <i class="fas fa-users-cog"></i>
                </div>
                <div class="staff-header-title-meta">
                    <span class="staff-header-eyebrow">Team & Access Control</span>
                    <h1 class="staff-header-title">Staff Management</h1>
                    <p class="staff-header-sub">Manage restaurant staff, assign roles, branches, and granular permissions</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if(auth()->user()->hasPermission('staff', 'add'))
                <button type="button" class="btn-staff-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-user-plus"></i>
                    <span>Add Staff Member</span>
                </button>
                @endif
            </div>
        </div>

        {{-- Permissions Info Alert --}}
        <div class="alert alert-info border-0 shadow-sm d-flex align-items-center justify-content-between p-3 mb-4" style="border-radius: 14px; background: #f0f9ff; border-left: 4px solid #0284c7 !important;">
            <div class="d-flex align-items-center gap-3">
                <div style="width: 36px; height: 36px; border-radius: 10px; background: #e0f2fe; color: #0369a1; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0;">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div>
                    <strong style="color: #0369a1; font-size: 0.9rem;">Granular Module Permissions</strong>
                    <p class="mb-0 text-muted" style="font-size: 0.82rem;">Click the <span class="badge bg-warning text-dark"><i class="fas fa-user-shield me-1"></i> Permissions</span> button on any staff member to fine-tune their module access rights.</p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        {{-- 2. Stats Deck --}}
        @php
            $totalStaff = $data->count();
            $activeStaff = $data->where('status', 'A')->count();
            $inactiveStaff = $data->where('status', 'I')->count();
            $outletCount = isset($availableOutlets) ? $availableOutlets->count() : 1;
        @endphp
        <div class="staff-stats-grid">
            <div class="staff-stat-card stat-total">
                <div class="staff-stat-info">
                    <span class="staff-stat-label">Total Staff</span>
                    <span class="staff-stat-val">{{ $totalStaff }}</span>
                </div>
                <div class="staff-stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="staff-stat-card stat-active">
                <div class="staff-stat-info">
                    <span class="staff-stat-label">Active Staff</span>
                    <span class="staff-stat-val text-success">{{ $activeStaff }}</span>
                </div>
                <div class="staff-stat-icon">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
            <div class="staff-stat-card stat-inactive">
                <div class="staff-stat-info">
                    <span class="staff-stat-label">Inactive Staff</span>
                    <span class="staff-stat-val text-danger">{{ $inactiveStaff }}</span>
                </div>
                <div class="staff-stat-icon">
                    <i class="fas fa-user-slash"></i>
                </div>
            </div>
            <div class="staff-stat-card stat-branches">
                <div class="staff-stat-info">
                    <span class="staff-stat-label">{{ $outletCount > 1 ? 'Active Outlets' : 'Total Roles' }}</span>
                    <span class="staff-stat-val" style="color: var(--staff-primary);">{{ $outletCount > 1 ? $outletCount : $data->pluck('role_type')->unique()->count() }}</span>
                </div>
                <div class="staff-stat-icon">
                    <i class="fas {{ $outletCount > 1 ? 'fa-store' : 'fa-id-badge' }}"></i>
                </div>
            </div>
        </div>

        {{-- 3. Filter & Search Toolbar --}}
        <div class="staff-toolbar">
            <div class="staff-search-box">
                <i class="fas fa-search staff-search-icon"></i>
                <input type="text" id="staffSearchInput" class="staff-search-input" placeholder="Search staff by name, email, phone, role...">
            </div>
            <div class="staff-filter-pills">
                <span class="text-muted small fw-bold me-1"><i class="fas fa-filter me-1"></i>Role:</span>
                <button type="button" class="staff-pill-btn active" data-role="all">All</button>
                <button type="button" class="staff-pill-btn" data-role="Manager">Manager</button>
                <button type="button" class="staff-pill-btn" data-role="Cashier">Cashier</button>
                <button type="button" class="staff-pill-btn" data-role="Waiter">Waiter</button>
                <button type="button" class="staff-pill-btn" data-role="Kitchen Staff">Kitchen Staff</button>
            </div>
            <div class="staff-filter-pills">
                <span class="text-muted small fw-bold me-1">Status:</span>
                <button type="button" class="staff-pill-btn active" data-status="all">All</button>
                <button type="button" class="staff-pill-btn" data-status="A">Active</button>
                <button type="button" class="staff-pill-btn" data-status="I">Inactive</button>
            </div>
        </div>

        {{-- 4. Desktop Table View --}}
        <div class="staff-table-card">
            <div class="table-responsive">
                <table class="staff-table" id="staffDesktopTable">
                    <thead>
                        <tr>
                            <th>Staff Member</th>
                            <th>Contact</th>
                            @if(isset($availableOutlets) && $availableOutlets->count() > 1)
                            <th>Assigned Branch</th>
                            @endif
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="staffTableBody">
                        @forelse($data as $value)
                        @php
                            $roleClean = str_replace(' ', '', $value->role_type);
                            $initials = '';
                            $nameParts = explode(' ', trim($value->name));
                            foreach(array_slice($nameParts, 0, 2) as $part) {
                                if (!empty($part)) $initials .= strtoupper($part[0]);
                            }
                            if (empty($initials)) $initials = 'ST';
                            
                            $roleClass = 'role-Default';
                            if (stripos($value->role_type, 'Manager') !== false) $roleClass = 'role-Manager';
                            elseif (stripos($value->role_type, 'Cashier') !== false) $roleClass = 'role-Cashier';
                            elseif (stripos($value->role_type, 'Waiter') !== false) $roleClass = 'role-Waiter';
                            elseif (stripos($value->role_type, 'Kitchen') !== false) $roleClass = 'role-Kitchen';
                        @endphp
                        <tr class="staff-row" 
                            data-name="{{ strtolower($value->name) }}" 
                            data-email="{{ strtolower($value->email) }}" 
                            data-phone="{{ strtolower($value->phone) }}" 
                            data-role="{{ $value->role_type }}" 
                            data-status="{{ $value->status }}"
                            data-outlet="{{ strtolower($value->restaurant ? $value->restaurant->name : '') }}">
                            <td>
                                <div class="staff-profile-cell">
                                    <div class="staff-avatar {{ $roleClass }}">
                                        {{ $initials }}
                                    </div>
                                    <div class="staff-profile-info">
                                        <span class="staff-profile-name">{{ $value->name }}</span>
                                        <span class="staff-profile-email">{{ $value->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="tel:{{ $value->phone }}" class="staff-phone-link">
                                    <i class="fas fa-phone-alt text-primary small"></i>
                                    <span>{{ $value->phone }}</span>
                                </a>
                            </td>
                            @if(isset($availableOutlets) && $availableOutlets->count() > 1)
                            <td>
                                <span class="staff-outlet-badge">
                                    <i class="fas fa-store text-primary"></i>
                                    <span>{{ $value->restaurant ? $value->restaurant->name : 'N/A' }}</span>
                                </span>
                            </td>
                            @endif
                            <td>
                                <span class="staff-role-badge {{ $roleClass }}">
                                    @if($roleClass == 'role-Manager') <i class="fas fa-user-tie"></i>
                                    @elseif($roleClass == 'role-Cashier') <i class="fas fa-cash-register"></i>
                                    @elseif($roleClass == 'role-Waiter') <i class="fas fa-concierge-bell"></i>
                                    @elseif($roleClass == 'role-Kitchen') <i class="fas fa-utensils"></i>
                                    @else <i class="fas fa-user"></i>
                                    @endif
                                    <span>{{ $value->role_type }}</span>
                                </span>
                            </td>
                            <td>
                                @if(auth()->user()->hasPermission('staff', 'edit'))
                                    <a href="{{ route('restaurant.staff.status', $value->id) }}" 
                                       class="staff-status-badge {{ $value->status == 'A' ? 'active' : 'inactive' }}"
                                       title="Click to toggle status">
                                        <span class="staff-status-dot"></span>
                                        <span>{{ $value->status == 'A' ? 'Active' : 'Inactive' }}</span>
                                    </a>
                                @else
                                    <span class="staff-status-badge {{ $value->status == 'A' ? 'active' : 'inactive' }}">
                                        <span class="staff-status-dot"></span>
                                        <span>{{ $value->status == 'A' ? 'Active' : 'Inactive' }}</span>
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="staff-actions justify-content-end">
                                    @if(auth()->user()->hasPermission('staff', 'edit'))
                                    <button type="button" 
                                            class="staff-btn-action btn-edit editBtn"
                                            title="Edit Staff Member"
                                            data-id="{{ $value->id }}"
                                            data-name="{{ $value->name }}"
                                            data-email="{{ $value->email }}"
                                            data-phone="{{ $value->phone }}"
                                            data-role="{{ $value->role_type }}"
                                            data-restaurant_id="{{ $value->restaurant_id }}"
                                            data-address="{{ $value->address }}"
                                            data-pincode="{{ $value->pincode }}"
                                            data-status="{{ $value->status }}">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    @endif

                                    @if(auth()->user()->role_type === 'ADMIN')
                                    <a href="{{ route('restaurant.staff.permissions', $value->id) }}"
                                       class="staff-btn-action btn-shield"
                                       title="Manage Module Permissions">
                                        <i class="fas fa-shield-alt"></i>
                                    </a>
                                    @endif

                                    @if(auth()->user()->hasPermission('staff', 'delete'))
                                    <a href="{{ route('restaurant.staff.delete', $value->id) }}"
                                       onclick="return confirm('Are you sure you want to remove {{ addslashes($value->name) }} from staff?')"
                                       class="staff-btn-action btn-del"
                                       title="Delete Staff">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr id="emptyTableRow">
                            <td colspan="{{ (isset($availableOutlets) && $availableOutlets->count() > 1) ? '6' : '5' }}" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-user-friends fa-3x mb-3 text-secondary opacity-50"></i>
                                    <h6 class="fw-bold">No Staff Members Found</h6>
                                    <p class="small text-muted mb-0">Click "+ Add Staff Member" to add your first team member.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 5. Mobile Cards View (Touch-friendly & Stacked) --}}
        <div class="staff-mobile-cards" id="staffMobileContainer">
            @forelse($data as $value)
            @php
                $initials = '';
                $nameParts = explode(' ', trim($value->name));
                foreach(array_slice($nameParts, 0, 2) as $part) {
                    if (!empty($part)) $initials .= strtoupper($part[0]);
                }
                if (empty($initials)) $initials = 'ST';
                
                $roleClass = 'role-Default';
                if (stripos($value->role_type, 'Manager') !== false) $roleClass = 'role-Manager';
                elseif (stripos($value->role_type, 'Cashier') !== false) $roleClass = 'role-Cashier';
                elseif (stripos($value->role_type, 'Waiter') !== false) $roleClass = 'role-Waiter';
                elseif (stripos($value->role_type, 'Kitchen') !== false) $roleClass = 'role-Kitchen';
            @endphp
            <div class="staff-card-item staff-row"
                 data-name="{{ strtolower($value->name) }}" 
                 data-email="{{ strtolower($value->email) }}" 
                 data-phone="{{ strtolower($value->phone) }}" 
                 data-role="{{ $value->role_type }}" 
                 data-status="{{ $value->status }}"
                 data-outlet="{{ strtolower($value->restaurant ? $value->restaurant->name : '') }}">
                <div class="staff-card-top">
                    <div class="staff-profile-cell">
                        <div class="staff-avatar {{ $roleClass }}">
                            {{ $initials }}
                        </div>
                        <div class="staff-profile-info">
                            <span class="staff-profile-name">{{ $value->name }}</span>
                            <span class="staff-profile-email">{{ $value->email }}</span>
                        </div>
                    </div>
                    <div>
                        @if(auth()->user()->hasPermission('staff', 'edit'))
                            <a href="{{ route('restaurant.staff.status', $value->id) }}" 
                               class="staff-status-badge {{ $value->status == 'A' ? 'active' : 'inactive' }}">
                                <span class="staff-status-dot"></span>
                                <span>{{ $value->status == 'A' ? 'Active' : 'Inactive' }}</span>
                            </a>
                        @else
                            <span class="staff-status-badge {{ $value->status == 'A' ? 'active' : 'inactive' }}">
                                <span class="staff-status-dot"></span>
                                <span>{{ $value->status == 'A' ? 'Active' : 'Inactive' }}</span>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="staff-card-meta-row">
                    <div>
                        <a href="tel:{{ $value->phone }}" class="staff-phone-link">
                            <i class="fas fa-phone-alt text-primary small"></i>
                            <span>{{ $value->phone }}</span>
                        </a>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="staff-role-badge {{ $roleClass }}">
                            <span>{{ $value->role_type }}</span>
                        </span>
                        @if(isset($availableOutlets) && $availableOutlets->count() > 1 && $value->restaurant)
                        <span class="staff-outlet-badge">
                            <i class="fas fa-store text-primary"></i>
                            <span>{{ $value->restaurant->name }}</span>
                        </span>
                        @endif
                    </div>
                </div>

                <div class="staff-card-actions">
                    @if(auth()->user()->hasPermission('staff', 'edit'))
                    <button type="button" 
                            class="btn btn-sm btn-outline-primary rounded-pill px-3 editBtn"
                            data-id="{{ $value->id }}"
                            data-name="{{ $value->name }}"
                            data-email="{{ $value->email }}"
                            data-phone="{{ $value->phone }}"
                            data-role="{{ $value->role_type }}"
                            data-restaurant_id="{{ $value->restaurant_id }}"
                            data-address="{{ $value->address }}"
                            data-pincode="{{ $value->pincode }}"
                            data-status="{{ $value->status }}">
                        <i class="fas fa-pen me-1"></i> Edit
                    </button>
                    @endif

                    @if(auth()->user()->role_type === 'ADMIN')
                    <a href="{{ route('restaurant.staff.permissions', $value->id) }}"
                       class="btn btn-sm btn-outline-warning rounded-pill px-3">
                        <i class="fas fa-shield-alt me-1"></i> Permissions
                    </a>
                    @endif

                    @if(auth()->user()->hasPermission('staff', 'delete'))
                    <a href="{{ route('restaurant.staff.delete', $value->id) }}"
                       onclick="return confirm('Delete this staff?')"
                       class="btn btn-sm btn-outline-danger rounded-pill px-3">
                        <i class="fas fa-trash-alt me-1"></i> Delete
                    </a>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-4 text-muted">
                <p class="mb-0">No staff records found.</p>
            </div>
            @endforelse
        </div>

    </div>

</div>
</div>

{{-- ==========================================
     ADD STAFF MODAL
     ========================================== --}}
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content staff-modal-content">
            <form action="{{ route('restaurant.staff.insert') }}" method="POST">
                @csrf
                <div class="staff-modal-header">
                    <h5 class="staff-modal-title">
                        <span class="staff-stat-icon" style="width: 36px; height: 36px; font-size: 1rem; background: var(--staff-primary-light); color: var(--staff-primary);">
                            <i class="fas fa-user-plus"></i>
                        </span>
                        <span>Add New Staff Member</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="staff-modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="staff-form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="staff-form-control" placeholder="e.g. John Doe" required>
                        </div>

                        <div class="col-md-6">
                            <label class="staff-form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="staff-form-control" placeholder="e.g. john@restaurant.com" required>
                        </div>

                        <div class="col-md-6">
                            <label class="staff-form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="staff-form-control" placeholder="e.g. 9876543210" required>
                        </div>

                        <div class="col-md-6">
                            <label class="staff-form-label">Role Type <span class="text-danger">*</span></label>
                            <select name="role_type" class="staff-form-control" required>
                                <option value="">-- Select Staff Role --</option>
                                <option value="Manager">Manager</option>
                                <option value="Cashier">Cashier</option>
                                <option value="Waiter">Waiter</option>
                                <option value="Kitchen Staff">Kitchen Staff</option>
                            </select>
                        </div>

                        @if(isset($availableOutlets) && $availableOutlets->count() > 1)
                        <div class="col-md-6">
                            <label class="staff-form-label">Assign Outlet Branch <span class="text-danger">*</span></label>
                            <select name="restaurant_id" class="staff-form-control" required>
                                @foreach($availableOutlets as $outlet)
                                    <option value="{{ $outlet->id }}" {{ $outlet->id == auth()->user()->restaurant_id ? 'selected' : '' }}>
                                        {{ $outlet->name }} {{ $outlet->isMainRestaurant() ? '(Main Branch)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="col-md-6">
                            <label class="staff-form-label">Initial Status <span class="text-danger">*</span></label>
                            <select name="status" class="staff-form-control">
                                <option value="A">Active (Can Login)</option>
                                <option value="I">Inactive (Login Disabled)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="staff-form-label">Account Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="staff-form-control" placeholder="Create login password" required>
                        </div>

                        <div class="col-md-6">
                            <label class="staff-form-label">Postal / Zip Code</label>
                            <input type="text" name="pincode" class="staff-form-control" placeholder="e.g. 110001">
                        </div>

                        <div class="col-md-12">
                            <label class="staff-form-label">Residential Address</label>
                            <textarea class="staff-form-control" name="address" rows="2" placeholder="Street address, city, state..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="staff-modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-staff-primary">
                        <i class="fas fa-check-circle"></i> Save Staff Member
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ==========================================
     EDIT STAFF MODAL
     ========================================== --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content staff-modal-content">
            <form action="{{ route('restaurant.staff.update') }}" method="POST">
                @csrf
                <input type="hidden" name="id" id="edit_id">

                <div class="staff-modal-header">
                    <h5 class="staff-modal-title">
                        <span class="staff-stat-icon" style="width: 36px; height: 36px; font-size: 1rem; background: var(--staff-primary-light); color: var(--staff-primary);">
                            <i class="fas fa-user-edit"></i>
                        </span>
                        <span>Edit Staff Member</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="staff-modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="staff-form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="staff-form-control" id="edit_name" name="name" required>
                        </div>

                        <div class="col-md-6">
                            <label class="staff-form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="staff-form-control" id="edit_email" name="email" required>
                        </div>

                        <div class="col-md-6">
                            <label class="staff-form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" class="staff-form-control" id="edit_phone" name="phone" required>
                        </div>

                        <div class="col-md-6">
                            <label class="staff-form-label">Role Type <span class="text-danger">*</span></label>
                            <select class="staff-form-control" id="edit_role" name="role_type" required>
                                <option value="Manager">Manager</option>
                                <option value="Cashier">Cashier</option>
                                <option value="Waiter">Waiter</option>
                                <option value="Kitchen Staff">Kitchen Staff</option>
                            </select>
                        </div>

                        @if(isset($availableOutlets) && $availableOutlets->count() > 1)
                        <div class="col-md-6">
                            <label class="staff-form-label">Assign Outlet Branch <span class="text-danger">*</span></label>
                            <select class="staff-form-control" id="edit_restaurant_id" name="restaurant_id" required>
                                @foreach($availableOutlets as $outlet)
                                    <option value="{{ $outlet->id }}">
                                        {{ $outlet->name }} {{ $outlet->isMainRestaurant() ? '(Main Branch)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="col-md-6">
                            <label class="staff-form-label">Status</label>
                            <select class="staff-form-control" id="edit_status" name="status">
                                <option value="A">Active</option>
                                <option value="I">Inactive</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="staff-form-label">Postal / Zip Code</label>
                            <input type="text" class="staff-form-control" id="edit_pincode" name="pincode">
                        </div>

                        <div class="col-md-12">
                            <label class="staff-form-label">Residential Address</label>
                            <textarea class="staff-form-control" id="edit_address" name="address" rows="2"></textarea>
                        </div>
                    </div>
                </div>

                <div class="staff-modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-staff-primary">
                        <i class="fas fa-save"></i> Update Staff Member
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
                document.getElementById('edit_email').value = this.dataset.email || '';
                document.getElementById('edit_phone').value = this.dataset.phone || '';
                document.getElementById('edit_role').value = this.dataset.role || '';
                
                const outletSelect = document.getElementById('edit_restaurant_id');
                if (outletSelect && this.dataset.restaurant_id) {
                    outletSelect.value = this.dataset.restaurant_id;
                }
                
                document.getElementById('edit_address').value = this.dataset.address || '';
                document.getElementById('edit_pincode').value = this.dataset.pincode || '';
                document.getElementById('edit_status').value = this.dataset.status || 'A';

                const editModal = new bootstrap.Modal(document.getElementById('editModal'));
                editModal.show();
            });
        });

        // Filter & Search Logic
        const searchInput = document.getElementById('staffSearchInput');
        const roleButtons = document.querySelectorAll('.staff-filter-pills [data-role]');
        const statusButtons = document.querySelectorAll('.staff-filter-pills [data-status]');
        const rows = document.querySelectorAll('.staff-row');

        let activeRole = 'all';
        let activeStatus = 'all';
        let searchQuery = '';

        function applyFilters() {
            let visibleCount = 0;
            rows.forEach(row => {
                const name = (row.dataset.name || '').toLowerCase();
                const email = (row.dataset.email || '').toLowerCase();
                const phone = (row.dataset.phone || '').toLowerCase();
                const role = (row.dataset.role || '');
                const status = (row.dataset.status || '');
                const outlet = (row.dataset.outlet || '').toLowerCase();

                const matchesSearch = !searchQuery || 
                    name.includes(searchQuery) || 
                    email.includes(searchQuery) || 
                    phone.includes(searchQuery) || 
                    role.toLowerCase().includes(searchQuery) || 
                    outlet.includes(searchQuery);

                const matchesRole = (activeRole === 'all') || (role === activeRole);
                const matchesStatus = (activeStatus === 'all') || (status === activeStatus);

                if (matchesSearch && matchesRole && matchesStatus) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                searchQuery = this.value.trim().toLowerCase();
                applyFilters();
            });
        }

        roleButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                roleButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                activeRole = this.dataset.role;
                applyFilters();
            });
        });

        statusButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                statusButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                activeStatus = this.dataset.status;
                applyFilters();
            });
        });
    });
</script>
@endsection
