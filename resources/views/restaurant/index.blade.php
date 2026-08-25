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

        /* Table Styles */
        .table thead th {
            background: linear-gradient(135deg, var(--dark), #2C3E50);
            color: white;
            font-weight: 600;
            border: none;
        }

        .btn-sm {
            margin: 0 2px;
            border-radius: 8px;
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
                <button class="btn btn-primary" data-toggle="modal" data-target="#addRestaurantModal">
                    <i class="fa fa-plus"></i> Add Restaurant
                </button>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="restaurantTable" class="table table-striped table-bordered nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>Restaurant</th>
                                <th>Address</th>
                                <th>Pincode</th>
                                <th>Owner</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Active Plan</th>
                                <th>Status</th>
                                <th>Action</th>
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
                                <td>{{ $rest->name }}</td>
                                <td>{{ $rest->address }}</td>
                                <td>{{ $rest->pincode }}</td>
                                <td>{{ $rest->owner->name ?? '' }}</td>
                                <td>{{ $rest->owner->email ?? '' }}</td>
                                <td>{{ $rest->owner->phone ?? '' }}</td>
                                <td>
                                    @if($plan)
                                        @php
                                            $isExpired = $sub->end_date && $sub->end_date->isPast();
                                            $badgeClass = $sub->status == 'active' && !$isExpired ? 'bg-success' : 'bg-danger';
                                        @endphp
                                        <a href="javascript:void(0)" class="badge {{ $badgeClass }} text-white showPlanDetailsBtn" style="text-decoration:none;"
                                           data-plan-name="{{ $plan->name }}"
                                           data-plan-price="{{ $plan->price }}"
                                           data-sub-status="{{ $sub->status }}"
                                           data-start-date="{{ $sub->start_date ? $sub->start_date->format('Y-m-d H:i') : 'N/A' }}"
                                           data-end-date="{{ $sub->end_date ? $sub->end_date->format('Y-m-d H:i') : 'N/A' }}"
                                           data-payment-amount="{{ $payment ? $payment->amount : 'N/A' }}"
                                           data-payment-method="{{ $payment ? $payment->payment_method : 'N/A' }}"
                                           data-payment-status="{{ $payment ? $payment->status : 'N/A' }}"
                                           data-payment-id="{{ $payment ? $payment->razorpay_payment_id : 'N/A' }}"
                                           data-payment-date="{{ $payment ? $payment->created_at->format('Y-m-d H:i') : 'N/A' }}">
                                            {{ $plan->name }} @if($isExpired) (Expired) @endif
                                        </a>
                                    @else
                                        <span class="badge bg-secondary text-white">No Plan</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('manage.restaurant.status', $rest->owner_id) }}"
                                       onclick="return confirm('Are you sure?')"
                                       class="btn btn-sm {{ $rest->status == 'A' ? 'btn-success' : 'btn-warning' }}">
                                        {{ $rest->status == 'A' ? 'Active' : 'Inactive' }}
                                    </a>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-success editBtn"
                                            data-id="{{ $rest->id }}"
                                            data-owner_id="{{ $rest->owner_id }}"
                                            data-restaurant_name="{{ $rest->name }}"
                                            data-restaurant_address="{{ $rest->address }}"
                                            data-restaurant_pincode="{{ $rest->pincode }}"
                                            data-restaurant_gstin="{{ $rest->gstin }}"
                                            data-restaurant_fssai_number="{{ $rest->fssai_number }}"
                                            data-owner_name="{{ @$rest->owner->name }}"
                                            data-owner_email="{{ @$rest->owner->email }}"
                                            data-owner_phone="{{ @$rest->owner->phone }}">
                                        <i class="fa fa-edit"></i>
                                    </button>

                                    <a href="{{ route('restaurant.analytics', $rest->id) }}"
                                       class="btn btn-sm btn-primary">
                                        <i class="fa fa-handshake"></i>
                                    </a>

                                    <a href="{{ route('manage.restaurant.show.plans', $rest->id) }}" class="btn btn-sm btn-info" title="Assign Plans">
                                        <i class="fas fa-tags"></i> Plans
                                    </a>

                                    <a href="{{ route('manage.restaurant.delete', $rest->id) }}"
                                       onclick="return confirm('Delete this restaurant?')"
                                       class="btn btn-sm btn-danger">
                                        <i class="fa fa-trash"></i>
                                    </a>
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
            <div class="modal-footer">
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
        order: []
    });

    // Show plan details button handler
    $('.showPlanDetailsBtn').on('click', function () {
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

        $('#detail_plan_name').text(planName);
        $('#detail_plan_price').text('₹' + planPrice);
        
        // Status class mappings
        let statusBadge = $('#detail_sub_status');
        statusBadge.text(subStatus.toUpperCase());
        statusBadge.removeClass('bg-success bg-danger bg-warning');
        if (subStatus === 'active') {
            statusBadge.addClass('bg-success text-white');
        } else {
            statusBadge.addClass('bg-danger text-white');
        }

        $('#detail_start_date').text(startDate);
        $('#detail_end_date').text(endDate);
        $('#detail_payment_amount').text('₹' + amount);
        $('#detail_payment_method').text(method ? method.toUpperCase() : 'N/A');
        
        let payStatusBadge = $('#detail_payment_status');
        payStatusBadge.text(status ? status.toUpperCase() : 'N/A');
        payStatusBadge.removeClass('bg-success bg-danger bg-warning');
        if (status === 'captured' || status === 'success') {
            payStatusBadge.addClass('bg-success text-white');
        } else if (status === 'failed') {
            payStatusBadge.addClass('bg-danger text-white');
        } else {
            payStatusBadge.addClass('bg-warning text-dark');
        }

        $('#detail_payment_id').text(paymentId);
        $('#detail_payment_date').text(paymentDate);

        $('#planDetailsModal').modal('show');
    });

    // Dismiss plan details modal manually
    $('#planDetailsModal').on('click', '[data-dismiss="modal"]', function() {
        $('#planDetailsModal').modal('hide');
    });

    // Edit button handler
    $('.editBtn').on('click', function () {
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