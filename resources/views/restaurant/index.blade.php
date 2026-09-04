<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Restaurant</title>
    @include('includes.style')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        :root {
            --primary: #FF6B35;
            --primary-dark: #E85D2C;
            --success: #2E9E4F;
            --danger: #E76F51;
            --gray: #6C7A8A;
            --dark: #1A2C3E;
        }

        /* Form Section Styles */
        .form-section-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-section-title i {
            color: var(--primary);
            font-size: 1.3rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
        }

        .form-label.required::after {
            content: '*';
            color: var(--danger);
            margin-left: 4px;
        }

        .form-control {
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            padding: 10px 14px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255,107,53,0.1);
            outline: none;
        }

        .action-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #E2E8F0;
        }

        .btn-gradient {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255,107,53,0.3);
            color: white;
        }

        .btn-outline-light {
            background: transparent;
            border: 1px solid #CBD5E1;
            padding: 10px 24px;
            border-radius: 30px;
            font-weight: 600;
            color: var(--gray);
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-outline-light:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .custom-check {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 1.5rem 0;
            padding: 12px;
            background: #F8FAFC;
            border-radius: 10px;
        }

        .custom-check input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--primary);
        }

        .custom-check label {
            margin: 0;
            font-size: 0.85rem;
            color: var(--gray);
        }

        .custom-check a {
            color: var(--primary);
            text-decoration: none;
        }

        .custom-check a:hover {
            text-decoration: underline;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 70%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--gray);
        }

        .position-relative {
            position: relative;
        }

        /* Modal Styles */
        .modal-content {
            border-radius: 20px;
            border: none;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--dark), #2C3E50);
            color: white;
            border-radius: 20px 20px 0 0;
        }

        .modal-header .close {
            color: white;
            opacity: 0.8;
        }

        .modal-header .close:hover {
            opacity: 1;
        }

        /* Modern Table & Card Styles */
        .card {
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
            overflow: hidden;
        }

        .card-header {
            background: #ffffff;
            border-bottom: 1px solid #E2E8F0;
            padding: 1.2rem 1.5rem;
        }

        #restaurantTable {
            width: 100% !important;
            margin-bottom: 0;
        }

        #restaurantTable thead th {
            background: linear-gradient(135deg, #1A2C3E, #2C3E50);
            color: #ffffff;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 14px;
            border: none;
            white-space: nowrap;
        }

        #restaurantTable tbody td {
            vertical-align: middle;
            padding: 12px 14px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.85rem;
        }

        #restaurantTable tbody tr:hover {
            background-color: rgba(255, 107, 53, 0.02);
        }

        /* Action Buttons Group */
        .action-btns-wrapper {
            display: inline-flex;
            gap: 6px;
            align-items: center;
            justify-content: center;
            flex-wrap: nowrap;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            padding: 0;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.82rem;
            transition: all 0.2s ease;
            border: 1px solid transparent;
            text-decoration: none;
            cursor: pointer;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
        }

        .action-btn-status-active {
            background: #fffbeb;
            color: #d97706;
            border-color: #fde68a;
        }
        .action-btn-status-active:hover {
            background: #d97706;
            color: #ffffff;
        }

        .action-btn-status-inactive {
            background: #ecfdf5;
            color: #059669;
            border-color: #a7f3d0;
        }
        .action-btn-status-inactive:hover {
            background: #059669;
            color: #ffffff;
        }

        .action-btn-edit {
            background: #eff6ff;
            color: #2563eb;
            border-color: #bfdbfe;
        }
        .action-btn-edit:hover {
            background: #2563eb;
            color: #ffffff;
        }

        .action-btn-analytics {
            background: #faf5ff;
            color: #7c3aed;
            border-color: #e9d5ff;
        }
        .action-btn-analytics:hover {
            background: #7c3aed;
            color: #ffffff;
        }

        .action-btn-plans {
            background: #f0f9ff;
            color: #0284c7;
            border-color: #bae6fd;
        }
        .action-btn-plans:hover {
            background: #0284c7;
            color: #ffffff;
        }

        .action-btn-delete {
            background: #fef2f2;
            color: #dc2626;
            border-color: #fecaca;
        }
        .action-btn-delete:hover {
            background: #dc2626;
            color: #ffffff;
        }

        /* Status Badge */
        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.76rem;
            font-weight: 600;
            white-space: nowrap;
        }
        .status-pill-active {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }
        .status-pill-inactive {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        /* Plan Badges */
        .plan-badge-active {
            background: linear-gradient(135deg, #10b981, #059669) !important;
            color: #ffffff !important;
            padding: 5px 12px !important;
            border-radius: 20px !important;
            font-weight: 600 !important;
            font-size: 0.76rem !important;
            display: inline-flex !important;
            align-items: center;
            gap: 5px;
            text-decoration: none !important;
            box-shadow: 0 2px 4px rgba(16, 185, 129, 0.25);
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .plan-badge-active:hover {
            background: linear-gradient(135deg, #059669, #047857) !important;
            color: #ffffff !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(16, 185, 129, 0.35);
        }
        .plan-badge-expired {
            background: linear-gradient(135deg, #ef4444, #dc2626) !important;
            color: #ffffff !important;
            padding: 5px 12px !important;
            border-radius: 20px !important;
            font-weight: 600 !important;
            font-size: 0.76rem !important;
            display: inline-flex !important;
            align-items: center;
            gap: 5px;
            text-decoration: none !important;
            box-shadow: 0 2px 4px rgba(239, 68, 68, 0.25);
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .plan-badge-expired:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c) !important;
            color: #ffffff !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(239, 68, 68, 0.35);
        }
        .plan-badge-none {
            background-color: #f1f5f9 !important;
            color: #64748b !important;
            border: 1px solid #cbd5e1;
            padding: 5px 12px !important;
            border-radius: 20px !important;
            font-weight: 600 !important;
            font-size: 0.76rem !important;
            display: inline-flex !important;
            align-items: center;
            gap: 5px;
        }

        /* Restaurant & Owner info helpers */
        .rest-title {
            font-weight: 700;
            color: #1e293b;
            font-size: 0.95rem;
            line-height: 1.3;
        }
        .rest-code-badge {
            font-size: 0.7rem;
            color: #4338ca;
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 4px;
            display: inline-block;
        }
        .rest-address-text {
            color: #64748b;
            font-size: 0.8rem;
            line-height: 1.35;
            margin-top: 3px;
        }
        .owner-name {
            font-weight: 600;
            color: #0f172a;
            font-size: 0.88rem;
        }
        .owner-contact-item {
            color: #64748b;
            font-size: 0.79rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 2px;
        }
        .owner-contact-item:hover {
            color: var(--primary);
        }
    </style>
</head>

<body>
@include('includes.sidebar')

<div class="pc-container">
    <div class="pc-content">

        <div class="page-header">
            <h5 class="m-b-10">Manage Restaurant</h5>
        </div>

        <div class="card">
            @include('includes.message')

            <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="mb-0">Restaurant List</h5>
                <div>
                    <a href="{{ route('manage.restaurant', array_merge(request()->all(), ['export' => 'excel'])) }}" class="btn btn-success me-2">
                        <i class="fa fa-file-excel"></i> Export Excel
                    </a>
                    <button class="btn btn-primary" data-toggle="modal" data-target="#addRestaurantModal">
                        <i class="fa fa-plus"></i> Add Restaurant
                    </button>
                </div>
            </div>

            <div class="card-body">
                <!-- Filters Section -->
                <div class="filter-card mb-4" style="background: #F8FAFC; border-radius: 12px; padding: 18px; border: 1px solid #E2E8F0;">
                    <form method="GET" action="{{ route('manage.restaurant') }}" id="filterForm" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label font-weight-bold" style="font-size: 0.85rem; color: var(--dark);">Keyword Search</label>
                            <input type="text" name="search" class="form-control shadow-none" placeholder="Search ID, name, owner, phone..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label font-weight-bold" style="font-size: 0.85rem; color: var(--dark);">Status</label>
                            <select name="status" class="form-control shadow-none">
                                <option value="all">All Statuses</option>
                                <option value="A" {{ request('status') === 'A' ? 'selected' : '' }}>Active</option>
                                <option value="I" {{ request('status') === 'I' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-weight-bold" style="font-size: 0.85rem; color: var(--dark);">Plan</label>
                            <select name="plan_id" class="form-control shadow-none">
                                <option value="all">All Plans</option>
                                <option value="none" {{ request('plan_id') === 'none' ? 'selected' : '' }}>No Plan</option>
                                @foreach($plans as $p)
                                    <option value="{{ $p->id }}" {{ request('plan_id') == $p->id ? 'selected' : '' }}>{{ $p->name }} (₹{{ number_format($p->price, 2) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2 justify-content-end" style="gap: 8px;">
                            <button type="submit" class="btn btn-primary w-50" title="Apply Filters" style="height: 42px; display: flex; align-items: center; justify-content: center; border-radius: 10px; gap: 6px;">
                                <i class="fa fa-filter"></i> <span>Filter</span>
                            </button>
                            <a href="{{ route('manage.restaurant') }}" class="btn btn-secondary w-50" title="Reset Filters" style="border-radius: 10px; background-color: #e2e8f0; color: #475569; border: none; display: flex; align-items: center; justify-content: center; height: 42px; gap: 6px;">
                                <i class="fa fa-sync-alt"></i> <span>Reset</span>
                            </a>
                        </div>

                        <!-- Date Range Filters -->
                        <div class="col-md-3">
                            <label class="form-label font-weight-bold" style="font-size: 0.85rem; color: var(--dark);">
                                <i class="far fa-clock text-muted me-1"></i> Reg. From Date
                            </label>
                            <input type="date" name="from_date" class="form-control shadow-none" value="{{ request('from_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-weight-bold" style="font-size: 0.85rem; color: var(--dark);">
                                <i class="far fa-clock text-muted me-1"></i> Reg. To Date
                            </label>
                            <input type="date" name="to_date" class="form-control shadow-none" value="{{ request('to_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-weight-bold" style="font-size: 0.85rem; color: var(--dark);">
                                <i class="far fa-calendar-alt text-muted me-1"></i> Plan Start From
                            </label>
                            <input type="date" name="sub_from_date" class="form-control shadow-none" value="{{ request('sub_from_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-weight-bold" style="font-size: 0.85rem; color: var(--dark);">
                                <i class="far fa-calendar-alt text-muted me-1"></i> Plan Start To
                            </label>
                            <input type="date" name="sub_to_date" class="form-control shadow-none" value="{{ request('sub_to_date') }}">
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table id="restaurantTable" class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="width: 32%;">Restaurant & Location</th>
                                <th style="width: 24%;">Owner & Contact</th>
                                <th style="width: 18%;">Plan & Billing</th>
                                <th style="width: 10%; text-align: center;">Status</th>
                                <th style="width: 16%; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($restaurants as $rest)
                            @php
                                $sub = $rest->active_subscription ?? $rest->latest_subscription;
                                $plan = $sub->plan ?? null;
                                $payment = $sub ? $sub->payments->first() : null;
                            @endphp
                            <tr>
                                <!-- Restaurant & Location -->
                                <td>
                                    <div class="d-flex flex-column">
                                        <div class="d-flex align-items-center flex-wrap gap-1 mb-1">
                                            <span class="rest-title">{{ $rest->name }}</span>
                                            @if($rest->restaurant_id_unique)
                                                <span class="rest-code-badge">
                                                    <i class="fas fa-hashtag text-muted" style="font-size: 0.65rem;"></i> {{ $rest->restaurant_id_unique }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="rest-address-text">
                                            <i class="fas fa-map-marker-alt text-danger me-1" style="font-size: 0.75rem;"></i>
                                            {{ $rest->address }}@if($rest->pincode)<span class="text-secondary font-monospace">, {{ $rest->pincode }}</span>@endif
                                        </div>
                                        @if($rest->gstin || $rest->fssai_number)
                                            <div class="d-flex flex-wrap gap-3 mt-1" style="font-size: 0.73rem; color: #64748b;">
                                                @if($rest->gstin)
                                                    <span><strong class="text-dark">GST:</strong> {{ $rest->gstin }}</span>
                                                @endif
                                                @if($rest->fssai_number)
                                                    <span><strong class="text-dark">FSSAI:</strong> {{ $rest->fssai_number }}</span>
                                                @endif
                                            </div>
                                        @endif
                                        @if($rest->created_at)
                                            <div class="mt-1" style="font-size: 0.74rem; color: #64748b;">
                                                <i class="far fa-clock text-secondary me-1"></i><strong>Created:</strong> {{ $rest->created_at->format('d M Y, h:i A') }}
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- Owner & Contact -->
                                <td>
                                    <div class="d-flex flex-column">
                                        <div class="owner-name mb-1">
                                            <i class="fas fa-user-circle text-primary me-1" style="font-size: 0.85rem;"></i> {{ $rest->owner->name ?? 'N/A' }}
                                        </div>
                                        @if(@$rest->owner->phone)
                                            <a href="tel:{{ $rest->owner->phone }}" class="owner-contact-item" title="Click to call">
                                                <i class="fas fa-phone-alt text-success" style="font-size: 0.72rem; width: 14px;"></i> {{ $rest->owner->phone }}
                                            </a>
                                        @endif
                                        @if(@$rest->owner->email)
                                            <a href="mailto:{{ $rest->owner->email }}" class="owner-contact-item" title="Click to email">
                                                <i class="fas fa-envelope text-info" style="font-size: 0.72rem; width: 14px;"></i> {{ $rest->owner->email }}
                                            </a>
                                        @endif
                                    </div>
                                </td>

                                <!-- Plan & Billing -->
                                <td>
                                    @if($plan)
                                        @php
                                            $isExpired = ($sub->end_date && \Carbon\Carbon::parse($sub->end_date)->isPast()) || in_array($sub->status, ['expired', 'cancelled']);
                                            $isActive = ($sub->status == 'active' || $sub->status == 'completed') && !$isExpired;
                                            $badgeClass = $isActive ? 'plan-badge-active' : 'plan-badge-expired';
                                        @endphp
                                        <div class="d-flex flex-column align-items-start">
                                            <a href="javascript:void(0)" class="badge {{ $badgeClass }} showPlanDetailsBtn mb-1"
                                               data-sub-id="{{ $sub->id }}"
                                               data-plan-name="{{ $plan->name }}"
                                               data-plan-price="{{ $plan->price }}"
                                               data-sub-status="{{ $sub->status }}"
                                               data-start-date="{{ $sub->start_date ? $sub->start_date->format('d M Y, h:i A') : 'N/A' }}"
                                               data-end-date="{{ $sub->end_date ? $sub->end_date->format('d M Y, h:i A') : 'N/A' }}"
                                               data-payment-amount="{{ $payment ? $payment->amount : 'N/A' }}"
                                               data-payment-method="{{ $payment ? $payment->payment_method : 'N/A' }}"
                                               data-payment-status="{{ $payment ? $payment->status : 'N/A' }}"
                                               data-payment-id="{{ $payment ? $payment->razorpay_payment_id : 'N/A' }}"
                                               data-payment-date="{{ $payment && $payment->created_at ? $payment->created_at->format('d M Y, h:i A') : 'N/A' }}"
                                               title="View Subscription & Payment Details">
                                                <i class="fas fa-crown" style="font-size: 0.68rem;"></i> {{ $plan->name }} @if(!$isActive) (Expired) @endif
                                            </a>
                                            <div class="small mt-1" style="font-size: 0.76rem; color: #475569; line-height: 1.45;">
                                                <div class="fw-semibold text-dark">₹{{ number_format($plan->price, 2) }}</div>
                                                @if($sub->start_date || $sub->end_date)
                                                    <div class="mt-1 d-flex flex-column gap-1" style="font-size: 0.74rem;">
                                                        @if($sub->start_date)
                                                            <span class="text-muted"><i class="far fa-calendar-check text-success me-1"></i><strong>Start:</strong> {{ \Carbon\Carbon::parse($sub->start_date)->format('d M Y') }}</span>
                                                        @endif
                                                        @if($sub->end_date)
                                                            <span class="text-muted"><i class="far fa-calendar-times {{ $isExpired ? 'text-danger' : 'text-primary' }} me-1"></i><strong>End:</strong> {{ \Carbon\Carbon::parse($sub->end_date)->format('d M Y') }}</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="badge plan-badge-none">
                                            <i class="fas fa-minus-circle" style="font-size: 0.7rem;"></i> No Plan
                                        </span>
                                    @endif
                                </td>

                                <!-- Status -->
                                <td style="text-align: center;">
                                    <span class="status-pill {{ $rest->status == 'A' ? 'status-pill-active' : 'status-pill-inactive' }}">
                                        <i class="fas {{ $rest->status == 'A' ? 'fa-check-circle' : 'fa-ban' }}"></i>
                                        {{ $rest->status == 'A' ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td style="text-align: center;">
                                    <div class="action-btns-wrapper">
                                        <a href="{{ route('manage.restaurant.status', $rest->owner_id) }}"
                                           onclick="return confirm('Are you sure you want to change status?')"
                                           class="action-btn {{ $rest->status == 'A' ? 'action-btn-status-active' : 'action-btn-status-inactive' }}"
                                           title="{{ $rest->status == 'A' ? 'Deactivate Restaurant' : 'Activate Restaurant' }}">
                                            <i class="fa {{ $rest->status == 'A' ? 'fa-ban' : 'fa-check' }}"></i>
                                        </a>

                                        <button class="action-btn action-btn-edit editBtn"
                                                data-id="{{ $rest->id }}"
                                                data-owner_id="{{ $rest->owner_id }}"
                                                data-restaurant_name="{{ $rest->name }}"
                                                data-restaurant_address="{{ $rest->address }}"
                                                data-restaurant_pincode="{{ $rest->pincode }}"
                                                data-restaurant_gstin="{{ $rest->gstin }}"
                                                data-restaurant_fssai_number="{{ $rest->fssai_number }}"
                                                data-owner_name="{{ @$rest->owner->name }}"
                                                data-owner_email="{{ @$rest->owner->email }}"
                                                data-owner_phone="{{ @$rest->owner->phone }}"
                                                title="Edit Restaurant">
                                            <i class="fa fa-edit"></i>
                                        </button>

                                        <a href="{{ route('restaurant.analytics', $rest->id) }}"
                                           class="action-btn action-btn-analytics"
                                           title="View Analytics">
                                            <i class="fas fa-chart-line"></i>
                                        </a>

                                        <a href="{{ route('manage.restaurant.show.plans', $rest->id) }}" 
                                           class="action-btn action-btn-plans" 
                                           title="Assign Plans">
                                            <i class="fas fa-tags"></i>
                                        </a>

                                        <a href="{{ route('manage.restaurant.delete', $rest->id) }}"
                                           onclick="return confirm('Delete this restaurant?')"
                                           class="action-btn action-btn-delete"
                                           title="Delete Restaurant">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- =======================================================
                     ADD MODAL (Multi-Step)
======================================================== -->
<div class="modal fade" id="addRestaurantModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" style="color:white !important"><i class="fas fa-store"></i> Add New Restaurant</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <form method="POST" action="{{ route('manage.restaurant.insert') }}" id="multiStepForm">
                    @csrf

                    <!-- SECTION 1: RESTAURANT INFO -->
                    <div id="section1">
                        <div class="form-section-title">
                            <i class="fas fa-store"></i> Restaurant Information
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label required">Restaurant Name</label>
                                    <input type="text" name="restaurant_name" class="form-control" 
                                           value="{{ old('restaurant_name') }}" required 
                                           placeholder="e.g., Spice Garden">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label required">Pincode</label>
                                    <input type="text" name="pincode" class="form-control" 
                                           value="{{ old('pincode') }}" required 
                                           placeholder="e.g., 110001">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Full Address</label>
                            <input type="text" name="address" class="form-control" 
                                   value="{{ old('address') }}" required 
                                   placeholder="Street, city, landmark">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">GSTIN</label>
                                    <input type="text" name="gstin" class="form-control" 
                                           value="{{ old('gstin') }}" 
                                           placeholder="e.g., 07AAAAA1111A1Z1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">FSSAI Number</label>
                                    <input type="text" name="fssai_number" class="form-control" 
                                           value="{{ old('fssai_number') }}" 
                                           placeholder="e.g., 12345678901234">
                                </div>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <div></div>
                            <button type="button" class="btn-gradient" onclick="showSection(2)">
                                Next Step <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- SECTION 2: OWNER DETAILS -->
                    <div id="section2" style="display: none;">
                        <div class="form-section-title">
                            <i class="fas fa-user-circle"></i> Owner & Account
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label required">Owner Name</label>
                                    <input type="text" name="name" class="form-control" 
                                           value="{{ old('name') }}" required 
                                           placeholder="Full name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label required">Email Address</label>
                                    <input type="email" name="email" class="form-control" 
                                           value="{{ old('email') }}" required 
                                           placeholder="hello@restaurant.com">
                                    @if($errors->has('email'))
                                        <small class="text-warning" style="font-size: 0.7rem;">{{ $errors->first('email') }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label required">Phone Number</label>
                                    <input type="text" name="phone" class="form-control" 
                                           value="{{ old('phone') }}" required 
                                           placeholder="+91 98765 43210">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group position-relative">
                                    <label class="form-label required">Password</label>
                                    <input type="password" name="password" id="password" class="form-control" 
                                           required placeholder="••••••••">
                                    <span class="password-toggle" onclick="togglePassword()">
                                        <i class="far fa-eye" id="toggleIcon"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <input type="checkbox" id="terms" checked style="display: none;" required>

                        <div class="action-buttons">
                            <button type="button" class="btn-outline-light" onclick="showSection(1)">
                                <i class="fas fa-arrow-left"></i> Back
                            </button>
                            <button type="submit" class="btn-gradient" id="submitBtn">
                                <i class="fas fa-user-plus"></i> Register Restaurant
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- =======================================================
                     EDIT MODAL
======================================================== -->
<div class="modal fade" id="editRestaurantModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" style="color:white !important;"><i class="fas fa-edit"></i> Edit Restaurant</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <form action="{{ route('manage.restaurant.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" id="edit_id">
                    <input type="hidden" name="owner_id" id="edit_owner_id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label required">Restaurant Name</label>
                                <input type="text" name="restaurant_name" id="edit_restaurant_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label required">Pincode</label>
                                <input type="text" name="pincode" id="edit_restaurant_pincode" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Address</label>
                        <input type="text" name="address" id="edit_restaurant_address" class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">GSTIN</label>
                                <input type="text" name="gstin" id="edit_restaurant_gstin" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">FSSAI Number</label>
                                <input type="text" name="fssai_number" id="edit_restaurant_fssai_number" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label required">Owner Name</label>
                                <input type="text" name="name" id="edit_owner_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label required">Email</label>
                                <input type="email" name="email" id="edit_owner_email" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Phone</label>
                        <input type="text" name="phone" id="edit_owner_phone" class="form-control" required>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Terms Modal -->
<div class="modal fade" id="termsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Terms of Service</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>By registering your restaurant, you agree to our terms and conditions...</p>
                <ul>
                    <li>You are responsible for all content posted</li>
                    <li>We reserve the right to suspend accounts that violate policies</li>
                    <li>Valid business license required</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Privacy Modal -->
<div class="modal fade" id="privacyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Privacy Policy</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>We value your privacy. Customer data is protected and never shared with third parties.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Plan Details Modal -->
<div class="modal fade" id="planDetailsModal" tabindex="-1" role="dialog" aria-labelledby="planDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" style="color: white !important;" id="planDetailsModalLabel"><i class="fas fa-file-invoice-dollar"></i> Subscription & Payment Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="border: none; background: transparent; font-size: 1.5rem; outline: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <h6 class="text-uppercase text-muted small font-weight-bold mb-3">Subscription Details</h6>
                <div class="table-responsive">
                    <table class="table table-borderless table-sm mb-4">
                        <tr>
                            <td class="pl-0 text-muted" style="width: 140px;">Plan Name:</td>
                            <td class="font-weight-bold" id="detail_plan_name">N/A</td>
                        </tr>
                        <tr>
                            <td class="pl-0 text-muted">Price:</td>
                            <td class="font-weight-bold" id="detail_plan_price">N/A</td>
                        </tr>
                        <tr>
                            <td class="pl-0 text-muted">Status:</td>
                            <td><span class="badge" id="detail_sub_status">N/A</span></td>
                        </tr>
                        <tr>
                            <td class="pl-0 text-muted">Start Date:</td>
                            <td id="detail_start_date">N/A</td>
                        </tr>
                        <tr>
                            <td class="pl-0 text-muted">Expiry Date:</td>
                            <td id="detail_end_date">N/A</td>
                        </tr>
                    </table>
                </div>

                <h6 class="text-uppercase text-muted small font-weight-bold mb-3">Payment details</h6>
                <div class="table-responsive">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="pl-0 text-muted" style="width: 140px;">Amount Paid:</td>
                            <td class="font-weight-bold" id="detail_payment_amount">N/A</td>
                        </tr>
                        <tr>
                            <td class="pl-0 text-muted">Method:</td>
                            <td id="detail_payment_method">N/A</td>
                        </tr>
                        <tr>
                            <td class="pl-0 text-muted">Payment Status:</td>
                            <td><span class="badge" id="detail_payment_status">N/A</span></td>
                        </tr>
                        <tr>
                            <td class="pl-0 text-muted">Razorpay ID:</td>
                            <td id="detail_payment_id">N/A</td>
                        </tr>
                        <tr>
                            <td class="pl-0 text-muted">Payment Date:</td>
                            <td id="detail_payment_date">N/A</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <a href="#" id="downloadInvoiceBtn" class="btn btn-primary" style="border-radius: 30px; padding: 8px 20px;">
                    <i class="fas fa-file-download"></i> Download Invoice
                </a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 30px; padding: 8px 20px;">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
@include('includes.script')

<script>
$(document).ready(function () {
    // Initialize DataTable
    $('#restaurantTable').DataTable({
        responsive: true,
        pageLength: 10,
        order: [],
        autoWidth: false
    });

    // Initialize Tooltips
    $('[title]').tooltip();

    // Show plan details button handler (delegated for DataTable pagination)
    $(document).on('click', '.showPlanDetailsBtn', function () {
        const subId = $(this).data('sub-id');
        const planName = $(this).data('plan-name');
        const planPrice = $(this).data('plan-price');
        const subStatus = $(this).data('sub-status');
        const startDate = $(this).data('start-date');
        const endDate = $(this).data('end-date');
        const amount = $(this).data('payment-amount');
        const method = $(this).data('payment-method');
        const status = $(this).data('payment-status');
        const paymentId = $(this).data('payment-id');
        const paymentDate = $(this).data('payment-date');

        // Set invoice download link
        if (subId) {
            let invoiceUrl = "{{ route('admin.subscriptions.invoice', ':id') }}".replace(':id', subId);
            $('#downloadInvoiceBtn').attr('href', invoiceUrl).show();
        } else {
            $('#downloadInvoiceBtn').hide();
        }

        $('#detail_plan_name').text(planName);
        $('#detail_plan_price').text('₹' + planPrice);
        
        // Status class mappings
        let statusBadge = $('#detail_sub_status');
        statusBadge.text(subStatus ? subStatus.toUpperCase() : 'N/A');
        statusBadge.removeClass('plan-badge-active plan-badge-expired plan-badge-none bg-success bg-danger bg-warning');
        if (subStatus === 'active' || subStatus === 'completed') {
            statusBadge.addClass('plan-badge-active text-white');
        } else {
            statusBadge.addClass('plan-badge-expired text-white');
        }

        $('#detail_start_date').text(startDate);
        $('#detail_end_date').text(endDate);
        $('#detail_payment_amount').text('₹' + amount);
        $('#detail_payment_method').text(method ? method.toUpperCase() : 'N/A');
        
        let payStatusBadge = $('#detail_payment_status');
        payStatusBadge.text(status ? status.toUpperCase() : 'N/A');
        payStatusBadge.removeClass('plan-badge-active plan-badge-expired plan-badge-none bg-success bg-danger bg-warning');
        if (status === 'captured' || status === 'success' || status === 'paid') {
            payStatusBadge.addClass('plan-badge-active text-white');
        } else if (status === 'failed') {
            payStatusBadge.addClass('plan-badge-expired text-white');
        } else {
            payStatusBadge.addClass('bg-warning text-dark');
        }

        $('#detail_payment_id').text(paymentId);
        $('#detail_payment_date').text(paymentDate);

        $('#planDetailsModal').modal('show');
    });

    // Dismiss plan details modal manually
    $(document).on('click', '#planDetailsModal [data-dismiss="modal"]', function() {
        $('#planDetailsModal').modal('hide');
    });

    // Edit button handler (delegated for DataTable pagination)
    $(document).on('click', '.editBtn', function () {
        $('#edit_id').val($(this).data('id'));
        $('#edit_owner_id').val($(this).data('owner_id'));
        $('#edit_restaurant_name').val($(this).data('restaurant_name'));
        $('#edit_restaurant_address').val($(this).data('restaurant_address'));
        $('#edit_restaurant_pincode').val($(this).data('restaurant_pincode'));
        $('#edit_restaurant_gstin').val($(this).data('restaurant_gstin'));
        $('#edit_restaurant_fssai_number').val($(this).data('restaurant_fssai_number'));
        $('#edit_owner_name').val($(this).data('owner_name'));
        $('#edit_owner_email').val($(this).data('owner_email'));
        $('#edit_owner_phone').val($(this).data('owner_phone'));
        $('#editRestaurantModal').modal('show');
    });

    // Reset form when modal is closed
    $('#addRestaurantModal').on('hidden.bs.modal', function () {
        $('#multiStepForm')[0].reset();
        showSection(1);
    });
});

// Multi-step form navigation
function showSection(section) {
    if (section === 1) {
        $('#section1').show();
        $('#section2').hide();
    } else {
        // Validate section 1 before proceeding
        let restaurantName = $('input[name="restaurant_name"]').val();
        let pincode = $('input[name="pincode"]').val();
        let address = $('input[name="address"]').val();
        
        if (!restaurantName) {
            showToast('Please enter restaurant name', 'error');
            $('input[name="restaurant_name"]').focus();
            return;
        }
        if (!pincode) {
            showToast('Please enter pincode', 'error');
            $('input[name="pincode"]').focus();
            return;
        }
        if (!address) {
            showToast('Please enter address', 'error');
            $('input[name="address"]').focus();
            return;
        }
        
        $('#section1').hide();
        $('#section2').show();
    }
}

// Password visibility toggle
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const icon = document.getElementById('toggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Toast notification
function showToast(message, type = 'success') {
    let toastHtml = `
        <div class="toast-notification show">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}" 
               style="color: ${type === 'success' ? '#27ae60' : '#e74c3c'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    $('body').append(toastHtml);
    setTimeout(() => {
        $('.toast-notification').remove();
    }, 3000);
}

// Terms checkbox validation before submit
$('#multiStepForm').on('submit', function(e) {
    if (!$('#terms').is(':checked')) {
        e.preventDefault();
        showToast('Please accept the Terms of Service and Privacy Policy', 'error');
        $('#terms').focus();
        return false;
    }
    return true;
});
</script>

<style>
    .toast-notification {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: white;
        border-radius: 12px;
        padding: 12px 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 10px;
        border-left: 4px solid #27ae60;
        animation: slideIn 0.3s ease;
    }
    
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .position-relative {
        position: relative;
    }
    
    .password-toggle {
        position: absolute;
        right: 12px;
        top: 72%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6C7A8A;
        z-index: 10;
    }
    
    .password-toggle:hover {
        color: #FF6B35;
    }
    
    .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }
    
    .btn-sm {
        padding: 5px 10px;
        margin: 2px;
    }
</style>

</body>
</html>