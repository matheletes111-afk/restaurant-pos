@extends('layouts.app')

@section('title')
<title>Admin || Manage Staff Permissions</title>
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

        {{-- 1. Staff Profile Banner --}}
        @php
            $initials = '';
            $nameParts = explode(' ', trim($staff->name));
            foreach(array_slice($nameParts, 0, 2) as $part) {
                if (!empty($part)) $initials .= strtoupper($part[0]);
            }
            if (empty($initials)) $initials = 'ST';

            $roleClass = 'role-Default';
            if (stripos($staff->role_type, 'Manager') !== false) $roleClass = 'role-Manager';
            elseif (stripos($staff->role_type, 'Cashier') !== false) $roleClass = 'role-Cashier';
            elseif (stripos($staff->role_type, 'Waiter') !== false) $roleClass = 'role-Waiter';
            elseif (stripos($staff->role_type, 'Kitchen') !== false) $roleClass = 'role-Kitchen';
        @endphp
        <div class="perm-profile-banner">
            <div class="perm-profile-left">
                <div class="perm-profile-avatar">
                    {{ $initials }}
                </div>
                <div class="perm-profile-meta">
                    <h4>{{ $staff->name }}</h4>
                    <p>
                        <span><i class="fas fa-envelope text-muted me-1"></i> {{ $staff->email }}</span>
                        <span>•</span>
                        <span><i class="fas fa-phone text-muted me-1"></i> {{ $staff->phone }}</span>
                        <span>•</span>
                        <span class="staff-role-badge {{ $roleClass }} py-0 px-2" style="font-size: 0.72rem;">
                            {{ $staff->role_type }}
                        </span>
                        @if($staff->restaurant)
                        <span>•</span>
                        <span class="staff-outlet-badge py-0 px-2" style="font-size: 0.72rem;">
                            <i class="fas fa-store text-primary"></i> {{ $staff->restaurant->name }}
                        </span>
                        @endif
                    </p>
                </div>
            </div>
            <div>
                <a href="{{ route('restaurant.staff.index') }}" class="btn-staff-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Staff
                </a>
            </div>
        </div>

        {{-- 2. Form & Module Permissions Grid --}}
        <form action="{{ route('restaurant.staff.update-permissions', $staff->id) }}" method="POST" id="permissionsForm">
            @csrf

            <div class="staff-toolbar mb-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="staff-stat-icon" style="width: 38px; height: 38px; font-size: 1.1rem; background: var(--staff-primary-light); color: var(--staff-primary);">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold" style="font-family: 'Outfit', sans-serif; font-size: 1.1rem; color: var(--staff-dark);">Module Permissions Matrix</h6>
                        <small class="text-muted">Toggle specific feature access or individual CRUD privileges</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" id="selectAllBtn">
                        <i class="fas fa-check-double me-1"></i> Select All
                    </button>
                    <button type="submit" class="btn-staff-primary py-2 px-4" style="height: 38px;">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </div>

            <div class="row g-3">
                @foreach($menus as $menu)
                @php
                    $isGranular = in_array($menu['key'], ['menu_master', 'table_master', 'staff', 'inventory_setting']);
                    if ($isGranular) {
                        $hasView = in_array($menu['key'] . '.view', $selectedPermissions) || in_array($menu['key'], $selectedPermissions);
                        $hasAdd = in_array($menu['key'] . '.add', $selectedPermissions) || in_array($menu['key'], $selectedPermissions);
                        $hasEdit = in_array($menu['key'] . '.edit', $selectedPermissions) || in_array($menu['key'], $selectedPermissions);
                        $hasDelete = in_array($menu['key'] . '.delete', $selectedPermissions) || in_array($menu['key'], $selectedPermissions);
                        $hasAny = $hasView || $hasAdd || $hasEdit || $hasDelete;
                    } else {
                        $hasPerm = in_array($menu['key'] . '.view', $selectedPermissions) || in_array($menu['key'], $selectedPermissions);
                    }
                @endphp
                <div class="col-xl-4 col-md-6">
                    @if($isGranular)
                    <div class="perm-card {{ $hasAny ? 'active' : '' }}">
                        <div class="perm-card-top">
                            <div class="perm-icon">
                                <i class="{{ $menu['icon'] }}"></i>
                            </div>
                            <div class="perm-title-desc">
                                <h6 class="perm-title">{{ $menu['title'] }}</h6>
                                <p class="perm-desc">{{ $menu['description'] }}</p>
                            </div>
                        </div>

                        <div class="perm-granular-grid">
                            <div class="perm-granular-item">
                                <label>View</label>
                                <label class="staff-switch">
                                    <input type="checkbox" name="permissions[]" value="{{ $menu['key'] }}.view" class="permission-checkbox granular-checkbox" {{ $hasView ? 'checked' : '' }} onchange="onGranularChange(this)">
                                    <span class="staff-slider"></span>
                                </label>
                            </div>
                            <div class="perm-granular-item">
                                <label>Add</label>
                                <label class="staff-switch">
                                    <input type="checkbox" name="permissions[]" value="{{ $menu['key'] }}.add" class="permission-checkbox granular-checkbox" {{ $hasAdd ? 'checked' : '' }} onchange="onGranularChange(this)">
                                    <span class="staff-slider"></span>
                                </label>
                            </div>
                            <div class="perm-granular-item">
                                <label>Edit</label>
                                <label class="staff-switch">
                                    <input type="checkbox" name="permissions[]" value="{{ $menu['key'] }}.edit" class="permission-checkbox granular-checkbox" {{ $hasEdit ? 'checked' : '' }} onchange="onGranularChange(this)">
                                    <span class="staff-slider"></span>
                                </label>
                            </div>
                            <div class="perm-granular-item">
                                <label>Delete</label>
                                <label class="staff-switch">
                                    <input type="checkbox" name="permissions[]" value="{{ $menu['key'] }}.delete" class="permission-checkbox granular-checkbox" {{ $hasDelete ? 'checked' : '' }} onchange="onGranularChange(this)">
                                    <span class="staff-slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="perm-card {{ $hasPerm ? 'active' : '' }}" onclick="toggleCard(this)" style="cursor: pointer;">
                        <div class="perm-card-top">
                            <div class="perm-icon">
                                <i class="{{ $menu['icon'] }}"></i>
                            </div>
                            <div class="perm-title-desc">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <h6 class="perm-title mb-0">{{ $menu['title'] }}</h6>
                                    <label class="staff-switch" onclick="event.stopPropagation();">
                                        <input type="checkbox" name="permissions[]" value="{{ $menu['key'] }}.view" class="permission-checkbox" {{ $hasPerm ? 'checked' : '' }} onchange="onCheckboxChange(this)">
                                        <span class="staff-slider"></span>
                                    </label>
                                </div>
                                <p class="perm-desc">{{ $menu['description'] }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            {{-- Sticky/Bottom Save CTA --}}
            <div class="d-flex justify-content-end align-items-center gap-3 mt-4 pt-3 border-top">
                <a href="{{ route('restaurant.staff.index') }}" class="btn btn-light rounded-pill px-4">Cancel</a>
                <button type="submit" class="btn-staff-primary px-5 py-2.5">
                    <i class="fas fa-save me-2"></i> Save Permissions
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
    function toggleCard(card) {
        if (card.querySelector('.granular-checkbox')) {
            return;
        }
        const checkbox = card.querySelector('.permission-checkbox');
        checkbox.checked = !checkbox.checked;
        if (checkbox.checked) {
            card.classList.add('active');
        } else {
            card.classList.remove('active');
        }
        updateSelectAllButton();
    }

    function onCheckboxChange(checkbox) {
        const card = checkbox.closest('.perm-card');
        if (checkbox.checked) {
            card.classList.add('active');
        } else {
            card.classList.remove('active');
        }
        updateSelectAllButton();
    }

    function onGranularChange(checkbox) {
        const card = checkbox.closest('.perm-card');
        const viewCheckbox = card.querySelector('input[value$=".view"]');
        const isView = checkbox === viewCheckbox;

        if (isView) {
            if (!viewCheckbox.checked) {
                card.querySelectorAll('.granular-checkbox').forEach(cb => {
                    if (cb !== viewCheckbox) {
                        cb.checked = false;
                    }
                });
            }
        } else {
            if (checkbox.checked && viewCheckbox) {
                viewCheckbox.checked = true;
            }
        }

        const granulars = card.querySelectorAll('.granular-checkbox');
        let anyChecked = false;
        granulars.forEach(cb => {
            if (cb.checked) {
                anyChecked = true;
            }
        });

        if (anyChecked) {
            card.classList.add('active');
        } else {
            card.classList.remove('active');
        }
        updateSelectAllButton();
    }

    function updateSelectAllButton() {
        const checkboxes = document.querySelectorAll('.permission-checkbox');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        const btn = document.getElementById('selectAllBtn');
        if (btn) {
            if (allChecked && checkboxes.length > 0) {
                btn.innerHTML = '<i class="fas fa-times me-1"></i> Deselect All';
                btn.classList.remove('btn-outline-secondary');
                btn.classList.add('btn-outline-danger');
            } else {
                btn.innerHTML = '<i class="fas fa-check-double me-1"></i> Select All';
                btn.classList.remove('btn-outline-danger');
                btn.classList.add('btn-outline-secondary');
            }
        }
    }

    document.getElementById('selectAllBtn').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.permission-checkbox');
        const cards = document.querySelectorAll('.perm-card');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        
        checkboxes.forEach((cb) => {
            cb.checked = !allChecked;
        });

        cards.forEach((card) => {
            if (!allChecked) {
                card.classList.add('active');
            } else {
                card.classList.remove('active');
            }
        });

        updateSelectAllButton();
    });

    window.addEventListener('DOMContentLoaded', () => {
        updateSelectAllButton();
    });
</script>
@endsection
