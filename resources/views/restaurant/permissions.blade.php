@extends('layouts.app')

@section('title')
<title>Admin || Manage Staff Permissions</title>
@endsection

@section('style')
@include('includes.style')
<style>
    .permission-card {
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
    }
    .permission-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 157, 26, 0.08);
        border-color: rgba(0, 157, 26, 0.25);
    }
    .permission-card.active {
        border-color: #009d1a;
        background-color: rgba(0, 157, 26, 0.01);
    }
    .permission-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #009d1a;
        background: rgba(0, 157, 26, 0.08);
        transition: all 0.3s ease;
    }
    .permission-card:hover .permission-icon {
        background: #009d1a;
        color: #ffffff;
    }
    .switch-toggle {
        position: relative;
        display: inline-block;
        width: 42px;
        height: 24px;
    }
    .switch-toggle input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 34px;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    input:checked + .slider {
        background-color: #009d1a;
    }
    input:checked + .slider:before {
        transform: translateX(18px);
    }
    .btn-gradient-success {
        background: linear-gradient(135deg, #009d1a 0%, #00bc20 100%);
        color: #ffffff;
        border: none;
        box-shadow: 0 4px 15px rgba(0, 157, 26, 0.2);
        transition: all 0.3s ease;
    }
    .btn-gradient-success:hover {
        background: linear-gradient(135deg, #00bc20 0%, #009d1a 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(0, 157, 26, 0.3);
        color: #ffffff;
    }
</style>
@endsection

@section('body')
@include('includes.sidebar')

<div class="pc-container">
<div class="pc-content">

    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="m-b-10 text-dark font-weight-bold">Staff Permissions</h5>
            <p class="text-muted mb-0">Configure access permissions for <strong>{{ $staff->name }}</strong> ({{ $staff->email }} | Role: <span class="badge bg-secondary">{{ $staff->role_type }}</span>)</p>
        </div>
        <a href="{{ route('restaurant.staff.index') }}" class="btn btn-outline-secondary btn-sm px-3 rounded-pill">
            <i class="fas fa-arrow-left me-2"></i> Back to Staff
        </a>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
        @include('includes.message')

        <form action="{{ route('restaurant.staff.update-permissions', $staff->id) }}" method="POST" id="permissionsForm">
            @csrf

            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center py-3">
                <h6 class="mb-0 text-secondary font-weight-bold"><i class="fas fa-shield-alt me-2 text-primary"></i> Module Permissions</h6>
                <button type="button" class="btn btn-light-primary btn-sm px-3 rounded-pill" id="selectAllBtn">
                    Select All
                </button>
            </div>

            <div class="card-body pt-0">
                <div class="row g-4">
                    @foreach($menus as $menu)
                    @php
                        $hasPerm = in_array($menu['key'], $selectedPermissions);
                    @endphp
                    <div class="col-xl-4 col-md-6">
                        <div class="permission-card p-4 d-flex align-items-start {{ $hasPerm ? 'active' : '' }}" onclick="toggleCard(this)">
                            <div class="permission-icon me-3">
                                <i class="{{ $menu['icon'] }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0 text-dark font-weight-bold" style="font-size: 1rem;">{{ $menu['title'] }}</h6>
                                    <label class="switch-toggle mb-0" onclick="event.stopPropagation();">
                                        <input type="checkbox" name="permissions[]" value="{{ $menu['key'] }}" class="permission-checkbox" {{ $hasPerm ? 'checked' : '' }} onchange="onCheckboxChange(this)">
                                        <span class="slider"></span>
                                    </label>
                                </div>
                                <p class="text-muted mb-0" style="font-size: 0.85rem; line-height: 1.4;">{{ $menu['description'] }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="text-end mt-5 pt-3 border-top">
                    <button type="submit" class="btn btn-gradient-success px-5 py-2.5 rounded-pill font-weight-bold">
                        <i class="fas fa-save me-2"></i> Save Permissions
                      </button>
                </div>
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
        const checkbox = card.querySelector('.permission-checkbox');
        checkbox.checked = !checkbox.checked;
        if (checkbox.checked) {
            card.classList.add('active');
        } else {
            card.classList.remove('active');
        }
    }

    function onCheckboxChange(checkbox) {
        const card = checkbox.closest('.permission-card');
        if (checkbox.checked) {
            card.classList.add('active');
        } else {
            card.classList.remove('active');
        }
    }

    document.getElementById('selectAllBtn').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.permission-checkbox');
        const cards = document.querySelectorAll('.permission-card');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        
        checkboxes.forEach((cb, idx) => {
            cb.checked = !allChecked;
            if (cb.checked) {
                cards[idx].classList.add('active');
            } else {
                cards[idx].classList.remove('active');
            }
        });

        this.textContent = allChecked ? 'Select All' : 'Deselect All';
        if (allChecked) {
            this.classList.remove('btn-light-danger');
            this.classList.add('btn-light-primary');
        } else {
            this.classList.remove('btn-light-primary');
            this.classList.add('btn-light-danger');
        }
    });

    // Initialize the button text based on start state
    window.addEventListener('DOMContentLoaded', () => {
        const checkboxes = document.querySelectorAll('.permission-checkbox');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        const btn = document.getElementById('selectAllBtn');
        if (allChecked && checkboxes.length > 0) {
            btn.textContent = 'Deselect All';
            btn.classList.remove('btn-light-primary');
            btn.classList.add('btn-light-danger');
        }
    });
</script>
@endsection
