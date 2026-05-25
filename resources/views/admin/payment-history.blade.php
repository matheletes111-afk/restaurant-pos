<!DOCTYPE html>
<html lang="en">
<head>
    <title>Payment History | Admin</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s;
            border-bottom: 3px solid;
            margin-bottom: 20px;
        }
        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .stats-number {
            font-size: 1.8rem;
            font-weight: 700;
        }
        .stats-label {
            color: #64748b;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 5px;
        }
        .filter-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .payment-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            display: inline-block;
        }
        .status-captured { background: #d1fae5; color: #065f46; }
        .status-success { background: #d1fae5; color: #065f46; }
        .status-pending { background: #dbeafe; color: #1e40af; }
        .status-failed { background: #fee2e2; color: #991b1b; }
        .status-refunded { background: #fef3c7; color: #92400e; }
        .modal-dialog {
            max-width: 650px;
        }
        .payment-detail-row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .payment-detail-row:last-child {
            border-bottom: none;
        }
        .payment-detail-label {
            width: 40%;
            font-weight: 600;
            color: #475569;
        }
        .payment-detail-value {
            width: 60%;
            color: #1e293b;
            word-break: break-word;
        }
        .payment-id {
            font-family: 'Courier New', monospace;
            font-size: 0.8rem;
        }
        .text-purple {
            color: #8b5cf6;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
        }
        .table td {
            vertical-align: middle;
        }
        .modal-header .close {
            color: white;
            opacity: 0.8;
        }
        .modal-header .close:hover {
            opacity: 1;
        }
        .badge-auto-renew-yes {
            background: #10b981;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
        }
        .badge-auto-renew-no {
            background: #64748b;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
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
                                <h5 class="m-b-10">Payment History</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="">Home</a></li>
                                <li class="breadcrumb-item" aria-current="page">Payment History</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Breadcrumb end -->

            <!-- Statistics Cards -->


            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Payment Transactions</h5>
                        </div>
                        <div class="card-body">
                            <!-- Filter Form -->
                            <div class="filter-card">
                                <form method="GET" action="{{ route('admin.payment.history') }}" class="row g-3 align-items-end">
                                    <div class="col-md-2">
                                        <label class="form-label">From Date</label>
                                        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">To Date</label>
                                        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Restaurant</label>
                                        <select name="restaurant_id" class="form-control">
                                            <option value="">All Restaurants</option>
                                            @foreach($restaurants as $rest)
                                                <option value="{{ $rest->id }}" {{ request('restaurant_id') == $rest->id ? 'selected' : '' }}>
                                                    {{ $rest->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Plan</label>
                                        <select name="plan_id" class="form-control">
                                            <option value="">All Plans</option>
                                            @foreach($plans as $plan)
                                                <option value="{{ $plan->id }}" {{ request('plan_id') == $plan->id ? 'selected' : '' }}>
                                                    {{ $plan->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-search me-1"></i> Filter
                                        </button>
                                    </div>
                                </form>
                            </div>

                            @include('includes.message')
                            
                            <div class="dt-responsive table-responsive">
                                <table id="paymentTable" class="table table-striped table-bordered nowrap">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Restaurant</th>
                                            <th>Plan</th>
                                            <th>Amount</th>
                                            <th>Payment ID</th>
                                            <th>Status</th>
                                            <th>Payment Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($payments as $payment)
                                        @php
                                            $restaurantName = 'N/A';
                                            $restaurantOwner = 'N/A';
                                            if ($payment->subscription && $payment->subscription->restaurant_details) {
                                                $restaurantName = $payment->subscription->restaurant_details->name;
                                                if ($payment->subscription->restaurant_details->owner) {
                                                    $restaurantOwner = $payment->subscription->restaurant_details->owner->name;
                                                }
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $payment->id }}</div>
                                            <td>
                                                <strong>{{ $restaurantName }}</strong>
                                                <br>
                                                <small class="text-muted">Owner: {{ $restaurantOwner }}</small>
                                             </div>
                                            <td>{{ $payment->plan->name ?? 'N/A' }}</div>
                                            <td>
                                                <strong>₹{{ number_format($payment->amount ?? 0, 2) }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $payment->currency ?? 'INR' }}</small>
                                             </div>
                                            <td>
                                                <span class="payment-id">{{ $payment->razorpay_payment_id ?? 'N/A' }}</span>
                                             </div>
                                            <td>
                                                <span class="payment-status status-{{ $payment->status }}">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                             </div>
                                            <td>{{ $payment->created_at->format('d M Y, h:i A') }}</div>
                                            <td>
                                                <button class="btn btn-sm btn-info view-btn" 
                                                        data-id="{{ $payment->id }}"
                                                        title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                             </div>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <div class="empty-state">
                                                    <i class="fas fa-inbox" style="font-size: 48px; color: #cbd5e1;"></i>
                                                    <h5 class="mt-3">No Payment Records Found</h5>
                                                    <p class="text-muted">No payment transactions match your criteria.</p>
                                                </div>
                                            </div>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if($payments->hasPages())
                                <div class="mt-4 d-flex justify-content-end">
                                    {{ $payments->appends(request()->query())->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Details Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #1e293b, #334155);">
                    <h5 class="modal-title text-white" id="viewModalLabel">
                        <i class="fas fa-receipt me-2"></i> Payment Details
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $('#paymentTable').DataTable({
            order: [[0, 'desc']],
            responsive: true,
            pageLength: 15,
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries"
            }
        });

        // View Details
        $('.view-btn').on('click', function() {
            let id = $(this).data('id');
            
            $('#modalContent').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div></div>');
            $('#viewModal').modal('show');
            
            $.ajax({
                url: "{{ url('admin/payment-history') }}/" + id,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        let data = response.data;
                        let statusClass = '';
                        switch(data.status) {
                            case 'captured': statusClass = 'status-captured'; break;
                            case 'success': statusClass = 'status-captured'; break;
                            case 'pending': statusClass = 'status-pending'; break;
                            case 'failed': statusClass = 'status-failed'; break;
                            case 'refunded': statusClass = 'status-refunded'; break;
                            default: statusClass = 'status-pending';
                        }
                        
                        let amount = parseFloat(data.amount) || 0;
                        let refundAmount = parseFloat(data.refund_amount) || 0;
                        
                        let html = `
                            <div class="payment-detail-row">
                                <div class="payment-detail-label">Payment ID:</div>
                                <div class="payment-detail-value"><strong class="payment-id">#${data.id}</strong></div>
                            </div>
                            <div class="payment-detail-row">
                                <div class="payment-detail-label">Razorpay Payment ID:</div>
                                <div class="payment-detail-value payment-id">${data.razorpay_payment_id || 'N/A'}</div>
                            </div>`;
                        
                        if (data.restaurant) {
                            html += `
                            <div class="payment-detail-row">
                                <div class="payment-detail-label">Restaurant Name:</div>
                                <div class="payment-detail-value"><strong>${data.restaurant.name || 'N/A'}</strong></div>
                            </div>
                            <div class="payment-detail-row">
                                <div class="payment-detail-label">Restaurant Address:</div>
                                <div class="payment-detail-value">${data.restaurant.address || 'N/A'}</div>
                            </div>
                            <div class="payment-detail-row">
                                <div class="payment-detail-label">Pincode:</div>
                                <div class="payment-detail-value">${data.restaurant.pincode || 'N/A'}</div>
                            </div>`;
                            
                            if (data.restaurant.gstin) {
                                html += `
                                <div class="payment-detail-row">
                                    <div class="payment-detail-label">GSTIN:</div>
                                    <div class="payment-detail-value">${data.restaurant.gstin}</div>
                                </div>`;
                            }
                        } else {
                            html += `
                            <div class="payment-detail-row">
                                <div class="payment-detail-label">Restaurant Name:</div>
                                <div class="payment-detail-value">N/A</div>
                            </div>`;
                        }
                        
                        if (data.plan) {
                            html += `
                            <div class="payment-detail-row">
                                <div class="payment-detail-label">Plan Name:</div>
                                <div class="payment-detail-value"><strong>${data.plan.name || 'N/A'}</strong></div>
                            </div>
                            <div class="payment-detail-row">
                                <div class="payment-detail-label">Plan Price:</div>
                                <div class="payment-detail-value">₹${amount.toFixed(2)} (${data.currency || 'INR'})</div>
                            </div>
                            <div class="payment-detail-row">
                                <div class="payment-detail-label">Billing Cycle:</div>
                                <div class="payment-detail-value">${data.plan.billing_cycle || 'N/A'}</div>
                            </div>`;
                        }
                        
                        if (data.subscription) {
                            html += `
                            <div class="payment-detail-row">
                                <div class="payment-detail-label">Subscription ID:</div>
                                <div class="payment-detail-value payment-id">#${data.subscription.id}</div>
                            </div>
                            <div class="payment-detail-row">
                                <div class="payment-detail-label">Subscription Status:</div>
                                <div class="payment-detail-value"><span class="payment-status ${data.subscription.status === 'active' ? 'status-captured' : 'status-pending'}">${data.subscription.status ? data.subscription.status.charAt(0).toUpperCase() + data.subscription.status.slice(1) : 'N/A'}</span></div>
                            </div>`;
                            
                            if (data.subscription.start_date) {
                                html += `
                                <div class="payment-detail-row">
                                    <div class="payment-detail-label">Start Date:</div>
                                    <div class="payment-detail-value">${new Date(data.subscription.start_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</div>
                                </div>`;
                            }
                            
                            if (data.subscription.end_date) {
                                html += `
                                <div class="payment-detail-row">
                                    <div class="payment-detail-label">End Date:</div>
                                    <div class="payment-detail-value">${new Date(data.subscription.end_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</div>
                                </div>`;
                            }
                            
                            html += `
                            <div class="payment-detail-row">
                                <div class="payment-detail-label">Auto Renew:</div>
                                <div class="payment-detail-value">${data.subscription.auto_renew ? '<span class="badge-auto-renew-yes">Yes</span>' : '<span class="badge-auto-renew-no">No</span>'}</div>
                            </div>`;
                        }
                        
                        html += `
                            <div class="payment-detail-row">
                                <div class="payment-detail-label">Payment Status:</div>
                                <div class="payment-detail-value"><span class="payment-status ${statusClass}">${data.status ? data.status.charAt(0).toUpperCase() + data.status.slice(1) : 'N/A'}</span></div>
                            </div>
                            <div class="payment-detail-row">
                                <div class="payment-detail-label">Payment Method:</div>
                                <div class="payment-detail-value">${data.payment_method || 'N/A'}</div>
                            </div>
                            <div class="payment-detail-row">
                                <div class="payment-detail-label">Payment Date:</div>
                                <div class="payment-detail-value">${new Date(data.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</div>
                            </div>`;
                        
                        if (data.description) {
                            html += `
                            <div class="payment-detail-row">
                                <div class="payment-detail-label">Description:</div>
                                <div class="payment-detail-value">${data.description}</div>
                            </div>`;
                        }
                        
                        if (refundAmount > 0) {
                            html += `
                            <div class="payment-detail-row">
                                <div class="payment-detail-label">Refund Amount:</div>
                                <div class="payment-detail-value">₹${refundAmount.toFixed(2)}</div>
                            </div>`;
                        }
                        
                        if (data.razorpay_order_id) {
                            html += `
                            <div class="payment-detail-row">
                                <div class="payment-detail-label">Razorpay Order ID:</div>
                                <div class="payment-detail-value payment-id">${data.razorpay_order_id}</div>
                            </div>`;
                        }
                        
                        if (data.razorpay_signature) {
                            html += `
                            <div class="payment-detail-row">
                                <div class="payment-detail-label">Signature:</div>
                                <div class="payment-detail-value payment-id" style="font-size: 0.7rem; word-break: break-all;">${data.razorpay_signature}</div>
                            </div>`;
                        }
                        
                        html += `</div>`;
                        $('#modalContent').html(html);
                    } else {
                        $('#modalContent').html('<div class="alert alert-danger">Failed to load payment details.</div>');
                    }
                },
                error: function(xhr) {
                    console.error(xhr);
                    $('#modalContent').html('<div class="alert alert-danger">Error loading payment details. Please try again.</div>');
                }
            });
        });
        
        // Close modal button - using Bootstrap's modal methods
        // Method 1: Close via data-dismiss (already works with proper Bootstrap JS)
        // Method 2: Manual close if needed
        $('.close, [data-dismiss="modal"]').on('click', function() {
            $('#viewModal').modal('hide');
        });
        
        // Optional: Close modal when clicking outside (default Bootstrap behavior)
        $('#viewModal').on('click', function(e) {
            if (e.target === this) {
                $(this).modal('hide');
            }
        });
    });
</script>
    
    @include('includes.script')
</body>
</html>