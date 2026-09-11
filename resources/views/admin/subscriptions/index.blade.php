<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Subscriptions</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        /* Premium custom switch toggle */
        .switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 22px;
            vertical-align: middle;
        }

        .switch input { 
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
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked + .slider {
            background-color: #ff6a00;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #ff6a00;
        }

        input:checked + .slider:before {
            -webkit-transform: translateX(22px);
            -ms-transform: translateX(22px);
            transform: translateX(22px);
        }

        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }

        /* Modal specific styling */
        .detail-section-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 16px;
            transition: all 0.2s ease;
        }
        .detail-section-card:hover {
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
        }

        .detail-plan-hero {
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
            border: 1px solid #fed7aa;
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 18px;
            position: relative;
            overflow: hidden;
        }

        .detail-plan-hero::after {
            content: '\f219';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            right: 15px;
            bottom: -15px;
            font-size: 80px;
            color: rgba(255, 106, 0, 0.08);
            pointer-events: none;
        }

        .detail-label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .detail-value {
            font-size: 0.92rem;
            color: #1e293b;
            font-weight: 600;
        }

        .limit-pill {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 14px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .limit-pill i {
            font-size: 1.3rem;
            color: #ff6a00;
            background: #fff7ed;
            padding: 8px;
            border-radius: 8px;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 10px;
        }

        .feature-item-box {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 8px;
            font-size: 0.84rem;
            background: #f8fafc;
            border: 1px solid #edf2f7;
            font-weight: 500;
        }

        .feature-item-box.active-feature {
            color: #0f766e;
            background: #f0fdfa;
            border-color: #ccfbf1;
        }

        .feature-item-box.inactive-feature {
            color: #991b1b;
            background: #fef2f2;
            border-color: #fee2e2;
        }

        .feature-item-box i {
            font-size: 1rem;
            flex-shrink: 0;
        }
    </style>
</head>
<body data-pc-theme="light">
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>

    @include('includes.sidebar')

    <div class="pc-container">
        <div class="pc-content">
            <!-- Breadcrumb -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">My Subscriptions</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="">Home</a></li>
                                <li class="breadcrumb-item" aria-current="page">Subscriptions</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Breadcrumb end -->

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <a href="{{ route('restaurant.plans') }}" class="btn btn-primary" style="float: right;">
                                <i class="fa fa-plus"></i> Upgrade Plan
                            </a>
                        </div>
                        <div class="card-body">
                            @include('includes.message')
                            
                            <div class="dt-responsive table-responsive">
                                <table id="subscriptionsTable" class="table table-striped table-bordered nowrap">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Plan</th>
                                            <th>Price</th>
                                            <th>Status</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Renewal Date</th>
                                            <th>Auto Renew</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subscriptions as $subscription)
                                        <tr>
                                            <td>{{ $subscription->id }}</td>
                                            <td>
                                                <strong>{{ $subscription->plan->name ?? 'N/A' }}</strong>
                                                @if(isset($subscription->plan->label_name) && $subscription->plan->label_name)
                                                    <span class="badge badge-warning text-dark ml-1" style="font-size: 0.65rem;">{{ $subscription->plan->label_name }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(($subscription->plan->price ?? 0) == 0)
                                                    <span class="badge badge-success">FREE</span>
                                                @else
                                                    ₹{{ number_format($subscription->plan->price ?? 0, 2) }}
                                                @endif
                                            </td>
                                            <td>
                                                <span class="btn btn-{{ 
                                                    $subscription->status == 'active' ? 'success' : 
                                                     ($subscription->status == 'cancelled' ? 'danger' : 
                                                     ($subscription->status == 'expired' ? 'warning' : 'secondary')) 
                                                }} btn-sm" style="padding: 2px 8px; font-size: 0.78rem;">
                                                    {{ ucfirst($subscription->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $subscription->start_date ? $subscription->start_date->format('Y-m-d') : 'N/A' }}</td>
                                            <td>{{ $subscription->end_date ? $subscription->end_date->format('Y-m-d') : 'N/A' }}</td>
                                            <td>{{ $subscription->renewal_date ? $subscription->renewal_date->format('Y-m-d') : 'N/A' }}</td>
                                            <td>
                                                @if($subscription->status == 'active')
                                                    <div style="display: flex; align-items: center; gap: 8px;">
                                                        <label class="switch">
                                                            <input type="checkbox" class="toggle-auto-renew" data-id="{{ $subscription->id }}" {{ $subscription->auto_renew ? 'checked' : '' }}>
                                                            <span class="slider round"></span>
                                                        </label>
                                                        <span class="auto-renew-status badge badge-{{ $subscription->auto_renew ? 'success' : 'secondary' }}">
                                                            {{ $subscription->auto_renew ? 'ON' : 'OFF' }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <span class="badge badge-secondary">
                                                        {{ $subscription->auto_renew ? 'Yes' : 'No' }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-info btn-sm view-btn" 
                                                        data-id="{{ $subscription->id }}" 
                                                        title="View Details" 
                                                        style="background: linear-gradient(135deg, #0284c7, #0ea5e9); border: none; color: white;">
                                                    <i class="fas fa-eye"></i> View
                                                </button>
                                                <a href="{{ route('admin.subscriptions.invoice', $subscription->id) }}" class="btn btn-primary btn-sm" title="Download Invoice">
                                                    <i class="fas fa-file-download"></i> Invoice
                                                </a>
                                                @if($subscription->status == 'active')
                                                <button class="btn btn-danger btn-sm cancel-btn" 
                                                        data-id="{{ $subscription->id }}" 
                                                        data-plan="{{ $subscription->plan->name ?? 'N/A' }}">
                                                    <i class="fa fa-ban"></i> Cancel
                                                </button>
                                                @endif
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
        </div>
    </div>

    <!-- Subscription Details Modal -->
    <div class="modal fade" id="viewSubscriptionModal" tabindex="-1" aria-labelledby="viewSubscriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 1.25rem 1.75rem;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="background: rgba(255, 106, 0, 0.2); width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #ff6a00; font-size: 1.25rem; border: 1px solid rgba(255, 106, 0, 0.3);">
                            <i class="fas fa-gem"></i>
                        </div>
                        <div>
                            <h5 class="modal-title text-white mb-0" id="viewSubscriptionModalLabel" style="font-weight: 700; font-size: 1.15rem; color: #ffffff !important;">
                                Subscription Details
                            </h5>
                            <small class="text-white-50" id="modalSubRef">Loading subscription...</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span id="modalStatusBadge" class="badge badge-success px-3 py-2" style="font-size: 0.8rem; border-radius: 20px;">Active</span>
                        <button type="button" class="btn-close-custom text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                
                <div class="modal-body" id="modalSubscriptionBody" style="padding: 1.5rem !important; background: #f8fafc;">
                    <!-- Loading state -->
                    <div id="modalLoadingState" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem; color: #ff6a00 !important;">
                            <span class="sr-only">Loading details...</span>
                        </div>
                        <p class="mt-3 text-muted font-weight-500">Fetching complete subscription & plan details...</p>
                    </div>

                    <!-- Content (hidden initially) -->
                    <div id="modalContentState" style="display: none;">
                        
                        <!-- 1. Plan Hero Card -->
                        <div class="detail-plan-hero">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <h4 class="mb-0 font-weight-bold text-dark" id="modalPlanName">Plan Name</h4>
                                        <span id="modalPlanLabel" class="badge badge-warning text-dark font-weight-bold" style="display: none;">Popular</span>
                                    </div>
                                    <p class="text-muted mb-0 small" id="modalPlanDescription" style="max-width: 480px;">Plan description goes here.</p>
                                </div>
                                <div class="text-right">
                                    <div class="d-flex align-items-baseline justify-content-end gap-1">
                                        <span id="modalPlanCrossPrice" class="text-muted text-decoration-line-through small mr-1" style="text-decoration: line-through; display: none;">₹999</span>
                                        <h3 class="mb-0 font-weight-bold" style="color: #ff6a00;" id="modalPlanPrice">₹499.00</h3>
                                    </div>
                                    <span class="badge badge-light border text-muted" id="modalPlanBillingCycle">Monthly Plan</span>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Subscription Timeline & Schedule -->
                        <div class="detail-section-card">
                            <h6 class="detail-label mb-3"><i class="fas fa-clock text-primary mr-1"></i> Subscription Timeline & Validity</h6>
                            <div class="row">
                                <div class="col-sm-6 col-md-3 mb-3 mb-md-0">
                                    <div class="detail-label">Start Date</div>
                                    <div class="detail-value" id="modalStartDate"><i class="far fa-calendar-check text-success mr-1"></i> --</div>
                                </div>
                                <div class="col-sm-6 col-md-3 mb-3 mb-md-0">
                                    <div class="detail-label">End / Expiry Date</div>
                                    <div class="detail-value" id="modalEndDate"><i class="far fa-calendar-times text-danger mr-1"></i> --</div>
                                </div>
                                <div class="col-sm-6 col-md-3 mb-3 mb-md-0">
                                    <div class="detail-label">Next Renewal</div>
                                    <div class="detail-value" id="modalRenewalDate"><i class="fas fa-redo-alt text-info mr-1"></i> --</div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="detail-label">Auto Renewal</div>
                                    <div class="detail-value" id="modalAutoRenewStatus">
                                        <span class="badge badge-success">ON</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <small class="text-muted">
                                    <i class="fas fa-fingerprint mr-1"></i> <strong>Gateway Sub ID:</strong> 
                                    <code id="modalRazorpaySubId" style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px; color: #0f172a;">--</code>
                                </small>
                                <small class="text-muted">
                                    <i class="fas fa-calendar-day mr-1"></i> <strong>Duration:</strong> 
                                    <span id="modalDurationDays">30</span> Days Validity
                                </small>
                            </div>
                        </div>

                        <!-- 3. Payment Details -->
                        <div class="detail-section-card">
                            <h6 class="detail-label mb-3"><i class="fas fa-receipt text-primary mr-1"></i> Payment Information</h6>
                            <div class="row">
                                <div class="col-sm-6 col-md-3 mb-3 mb-md-0">
                                    <div class="detail-label">Amount Paid</div>
                                    <div class="detail-value" style="color: #10b981; font-size: 1.05rem;" id="modalPaymentAmount">₹0.00</div>
                                </div>
                                <div class="col-sm-6 col-md-3 mb-3 mb-md-0">
                                    <div class="detail-label">Payment Status</div>
                                    <div class="detail-value" id="modalPaymentStatus"><span class="badge badge-success">Success</span></div>
                                </div>
                                <div class="col-sm-6 col-md-3 mb-3 mb-md-0">
                                    <div class="detail-label">Payment Method</div>
                                    <div class="detail-value" id="modalPaymentMethod">Online / Gateway</div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="detail-label">Payment Date</div>
                                    <div class="detail-value" id="modalPaymentDate">--</div>
                                </div>
                            </div>

                            <div class="mt-3 pt-3 border-top row">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <small class="text-muted">
                                        <i class="fas fa-hashtag mr-1"></i> <strong>Razorpay Payment ID:</strong> 
                                        <code id="modalRazorpayPaymentId" style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px; color: #0f172a;">--</code>
                                    </small>
                                </div>
                                <div class="col-md-6 text-md-right" id="modalRefundRow" style="display: none;">
                                    <small class="text-info font-weight-bold">
                                        <i class="fas fa-undo-alt mr-1"></i> Refund / Upgrade Credit: <span id="modalRefundAmount">₹0.00</span>
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Plan Capacity & Limits -->
                        <div class="detail-section-card">
                            <h6 class="detail-label mb-3"><i class="fas fa-sliders-h text-primary mr-1"></i> Plan Capacity & Resource Limits</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="limit-pill">
                                        <i class="fas fa-folder-open"></i>
                                        <div>
                                            <div class="detail-label">Menu Categories</div>
                                            <div class="detail-value font-weight-bold" id="modalLimitCategories">Unlimited</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="limit-pill">
                                        <i class="fas fa-utensils"></i>
                                        <div>
                                            <div class="detail-label">Total Dishes</div>
                                            <div class="detail-value font-weight-bold" id="modalLimitDishes">Unlimited</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="limit-pill">
                                        <i class="fas fa-chair"></i>
                                        <div>
                                            <div class="detail-label">Dining Tables</div>
                                            <div class="detail-value font-weight-bold" id="modalLimitTables">Unlimited</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 5. Included Features & Modules -->
                        <div class="detail-section-card">
                            <h6 class="detail-label mb-3"><i class="fas fa-check-double text-primary mr-1"></i> Features Included in this Plan</h6>
                            
                            <div class="feature-grid mb-3">
                                <div class="feature-item-box active-feature">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Menu Availability Management</span>
                                </div>
                                <div class="feature-item-box active-feature">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Order Management</span>
                                </div>
                                <div class="feature-item-box active-feature">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Kitchen Panel / KDS</span>
                                </div>
                                <div class="feature-item-box active-feature">
                                    <i class="fas fa-check-circle"></i>
                                    <span>QR Code Digital Ordering</span>
                                </div>
                                <div class="feature-item-box active-feature">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Customer Support</span>
                                </div>
                                <div class="feature-item-box active-feature">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Staff & Role Management</span>
                                </div>
                                <div class="feature-item-box active-feature">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Reports & Analytics</span>
                                </div>
                                <div class="feature-item-box active-feature">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Invoice & Bill Generation</span>
                                </div>
                            </div>

                            <div class="detail-label mb-2"><i class="fas fa-boxes mr-1"></i> Inventory Management Suite:</div>
                            <div class="feature-grid" id="modalInventorySuiteGrid">
                                <div class="feature-item-box" id="featProduct">
                                    <i class="fas fa-circle"></i>
                                    <span>Manage Products</span>
                                </div>
                                <div class="feature-item-box" id="featPurchase">
                                    <i class="fas fa-circle"></i>
                                    <span>Manage Purchases</span>
                                </div>
                                <div class="feature-item-box" id="featStockout">
                                    <i class="fas fa-circle"></i>
                                    <span>Manage Stockout</span>
                                </div>
                                <div class="feature-item-box" id="featDebitNote">
                                    <i class="fas fa-circle"></i>
                                    <span>Debit Note</span>
                                </div>
                                <div class="feature-item-box" id="featInventory">
                                    <i class="fas fa-circle"></i>
                                    <span>Real-Time Inventory</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                
                <div class="modal-footer d-flex justify-content-between align-items-center" style="background: #ffffff; padding: 1rem 1.75rem;">
                    <a href="#" id="modalInvoiceDownloadBtn" class="btn btn-primary btn-sm" target="_blank" style="padding: 0.6rem 1.4rem; border-radius: 30px;">
                        <i class="fas fa-file-download mr-1"></i> Download Invoice
                    </a>
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" data-bs-dismiss="modal" style="padding: 0.6rem 1.4rem; border-radius: 30px;">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Confirmation Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelModalLabel">Confirm Cancellation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to cancel your subscription to "<span id="planName"></span>"?
                    <div class="alert alert-warning mt-2">
                        <i class="fa fa-exclamation-triangle"></i> 
                        This action cannot be undone. Any remaining days will not be refunded.
                    </div>
                </div>
                <div class="modal-footer">
                    <form id="cancelForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Cancel Subscription</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Keep Subscription</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#subscriptionsTable').DataTable({
                order: [[0, 'desc']]
            });

            // View Details Modal Handler
            $(document).on('click', '.view-btn', function() {
                let id = $(this).data('id');
                
                // Show modal & set loading state
                $('#modalLoadingState').show();
                $('#modalContentState').hide();
                $('#modalSubRef').text('Loading subscription #' + id + '...');
                
                let modalEl = document.getElementById('viewSubscriptionModal');
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(modalEl).show();
                } else {
                    $('#viewSubscriptionModal').modal('show');
                }

                $.ajax({
                    url: '{{ url("admin/subscriptions") }}/' + id + '/details',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success && response.data) {
                            let data = response.data;
                            let plan = data.plan || {};
                            let payment = data.payment || {};

                            // Header meta
                            $('#modalSubRef').text('Subscription ID: #SUB-' + String(data.id).padStart(6, '0'));
                            
                            // Status badge
                            let statusClass = 'badge-secondary';
                            if (data.status === 'active') statusClass = 'badge-success';
                            else if (data.status === 'cancelled') statusClass = 'badge-danger';
                            else if (data.status === 'expired') statusClass = 'badge-warning';
                            
                            $('#modalStatusBadge')
                                .removeClass('badge-success badge-danger badge-warning badge-secondary')
                                .addClass(statusClass)
                                .text(data.status_badge || data.status);

                            // Plan hero
                            $('#modalPlanName').text(plan.name || 'N/A');
                            if (plan.label_name) {
                                $('#modalPlanLabel').text(plan.label_name).show();
                            } else {
                                $('#modalPlanLabel').hide();
                            }
                            $('#modalPlanDescription').text(plan.description || 'All-in-one restaurant POS plan.');
                            $('#modalPlanPrice').text(plan.formatted_price || '₹0.00');
                            
                            if (plan.cross_price) {
                                $('#modalPlanCrossPrice').text(plan.cross_price).show();
                            } else {
                                $('#modalPlanCrossPrice').hide();
                            }
                            $('#modalPlanBillingCycle').text((plan.billing_cycle || 'Monthly') + ' Plan');

                            // Timeline
                            $('#modalStartDate').html('<i class="far fa-calendar-check text-success mr-1"></i> ' + (data.start_date || 'N/A'));
                            $('#modalEndDate').html('<i class="far fa-calendar-times text-danger mr-1"></i> ' + (data.end_date || 'N/A'));
                            $('#modalRenewalDate').html('<i class="fas fa-redo-alt text-info mr-1"></i> ' + (data.renewal_date || 'N/A'));
                            
                            if (data.auto_renew) {
                                $('#modalAutoRenewStatus').html('<span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> Enabled</span>');
                            } else {
                                $('#modalAutoRenewStatus').html('<span class="badge badge-secondary"><i class="fas fa-times-circle mr-1"></i> Disabled</span>');
                            }

                            $('#modalRazorpaySubId').text(data.razorpay_subscription_id || 'N/A');
                            $('#modalDurationDays').text(plan.duration_days || '30');

                            // Payment details
                            $('#modalPaymentAmount').text(payment.formatted_amount || '₹0.00');
                            
                            let payStatusClass = 'badge-secondary';
                            if (payment.status && payment.status.toLowerCase() === 'success') payStatusClass = 'badge-success';
                            else if (payment.status && payment.status.toLowerCase() === 'pending') payStatusClass = 'badge-warning';
                            else if (payment.status && payment.status.toLowerCase() === 'failed') payStatusClass = 'badge-danger';

                            $('#modalPaymentStatus').html('<span class="badge ' + payStatusClass + '">' + (payment.status || 'N/A') + '</span>');
                            $('#modalPaymentMethod').text(payment.payment_method || 'Online');
                            $('#modalPaymentDate').text(payment.payment_date || 'N/A');
                            $('#modalRazorpayPaymentId').text(payment.razorpay_payment_id || 'N/A');

                            if (payment.refund_amount && payment.refund_amount > 0) {
                                $('#modalRefundAmount').text('₹' + parseFloat(payment.refund_amount).toFixed(2));
                                $('#modalRefundRow').show();
                            } else {
                                $('#modalRefundRow').hide();
                            }

                            // Resource limits
                            $('#modalLimitCategories').text(plan.category_display || 'Unlimited');
                            $('#modalLimitDishes').text(plan.dish_display || 'Unlimited');
                            $('#modalLimitTables').text(plan.table_display || 'Unlimited');

                            // Inventory suite items
                            let isInventory = !!plan.inventory_enabled;
                            let invIcon = isInventory ? '<i class="fas fa-check-circle mr-1 text-success"></i>' : '<i class="fas fa-times-circle mr-1 text-danger"></i>';
                            let invClass = isInventory ? 'active-feature' : 'inactive-feature';

                            ['featProduct', 'featPurchase', 'featStockout', 'featDebitNote', 'featInventory'].forEach(function(featId) {
                                let box = $('#' + featId);
                                box.removeClass('active-feature inactive-feature').addClass(invClass);
                                let title = box.find('span').text();
                                box.html(invIcon + '<span>' + title + '</span>');
                            });

                            // Invoice download button
                            $('#modalInvoiceDownloadBtn').attr('href', data.invoice_url || '#');

                            // Hide loader & display content
                            $('#modalLoadingState').hide();
                            $('#modalContentState').fadeIn(200);
                        } else {
                            $('#modalLoadingState').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle mr-1"></i> Failed to retrieve subscription details.</div>');
                        }
                    },
                    error: function(xhr) {
                        $('#modalLoadingState').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle mr-1"></i> Error loading subscription details. Please try again.</div>');
                    }
                });
            });

            // Cancel button
            $('.cancel-btn').on('click', function() {
                let id = $(this).data('id');
                let planName = $(this).data('plan');
                
                $('#planName').text(planName);
                $('#cancelForm').attr('action', '{{ url("admin/subscriptions") }}/' + id + '/cancel');
                $('#cancelModal').modal('show');
            });

            // Toggle Auto-Renew AJAX
            $('.toggle-auto-renew').on('change', function() {
                let checkbox = $(this);
                let id = checkbox.data('id');
                let statusBadge = checkbox.closest('div').find('.auto-renew-status');
                
                statusBadge.text('Updating...').removeClass('badge-success badge-secondary').addClass('badge-info');
                
                $.ajax({
                    url: '{{ url("admin/subscriptions") }}/' + id + '/toggle-auto-renew',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            if (response.auto_renew) {
                                checkbox.prop('checked', true);
                                statusBadge.text('ON').removeClass('badge-info badge-secondary').addClass('badge-success');
                            } else {
                                checkbox.prop('checked', false);
                                statusBadge.text('OFF').removeClass('badge-info badge-success').addClass('badge-secondary');
                            }
                        } else {
                            // Revert on error
                            checkbox.prop('checked', !checkbox.prop('checked'));
                            statusBadge.text(checkbox.prop('checked') ? 'ON' : 'OFF')
                                .removeClass('badge-info')
                                .addClass(checkbox.prop('checked') ? 'badge-success' : 'badge-secondary');
                            alert(response.message || 'Failed to toggle auto-renew.');
                        }
                    },
                    error: function(xhr) {
                        // Revert on error
                        checkbox.prop('checked', !checkbox.prop('checked'));
                        statusBadge.text(checkbox.prop('checked') ? 'ON' : 'OFF')
                            .removeClass('badge-info')
                            .addClass(checkbox.prop('checked') ? 'badge-success' : 'badge-secondary');
                        alert('Error connecting to server. Please try again.');
                    }
                });
            });
        });
    </script>
    
    @include('includes.script')
</body>
</html>