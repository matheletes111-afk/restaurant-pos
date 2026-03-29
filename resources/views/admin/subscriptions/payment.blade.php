<!DOCTYPE html>
<html lang="en">
<head>
    <title>Complete Payment</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <style>
        .loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .payment-status {
            display: none;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .payment-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .payment-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body data-pc-theme="light">
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>

    <!-- Loading overlay -->
    <div class="loader-overlay" id="loadingOverlay">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Processing...</span>
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
                                <h5 class="m-b-10">Complete Payment</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('plans.index') }}">Plans</a></li>
                                <li class="breadcrumb-item" aria-current="page">Payment</li>
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
                            <h5>Payment Information</h5>
                        </div>
                        <div class="card-body">
                            @include('includes.message')
                            
                            <!-- Payment Status Messages -->
                            <div id="paymentSuccess" class="payment-status payment-success" style="display: none;">
                                <h4><i class="fa fa-check-circle"></i> Payment Successful!</h4>
                                <p>Redirecting to subscriptions page...</p>
                            </div>
                            
                            <div id="paymentError" class="payment-status payment-error" style="display: none;">
                                <h4><i class="fa fa-exclamation-circle"></i> Payment Failed</h4>
                                <p id="errorMessage"></p>
                                <a href="{{ route('plans.index') }}" class="btn btn-secondary">Go Back to Plans</a>
                            </div>
                            
                            <div id="paymentForm">
                                <div class="row">
                                    <div class="col-md-8 offset-md-2">
                                        <div class="payment-summary">
                                            <h4 class="text-center mb-4">Payment Summary</h4>
                                            
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th>Plan Name:</th>
                                                    <td>{{ $plan->name }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Duration:</th>
                                                    <td>{{ $plan->duration_days }} days</td>
                                                </tr>
                                                <tr>
                                                    <th>Billing Cycle:</th>
                                                    <td>{{ ucfirst($plan->billing_cycle) }}</td>
                                                </tr>
                                                @if($is_upgrade && $existing_subscription)
                                                <tr>
                                                    <th>Previous Plan:</th>
                                                    <td>{{ $existing_subscription->plan->name ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Refund Amount:</th>
                                                    <td class="text-success">₹{{ number_format($refund_amount ?? 0, 2) }}</td>
                                                </tr>
                                                @endif
                                                <tr>
                                                    <th>Amount Payable:</th>
                                                    <td class="font-weight-bold">₹{{ number_format($payable_amount, 2) }}</td>
                                                </tr>
                                            </table>
                                            
                                            <div class="text-center mt-4">
                                                <button id="rzp-button" class="btn btn-primary btn-lg">
                                                    <i class="fa fa-credit-card"></i> Pay Now (₹{{ number_format($payable_amount, 2) }})
                                                </button>
                                                
                                                <a href="{{ route('plans.index') }}" class="btn btn-secondary">
                                                    <i class="fa fa-times"></i> Cancel
                                                </a>
                                            </div>
                                            
                                            <div class="alert alert-info mt-3">
                                                <i class="fa fa-info-circle"></i> 
                                                You will be redirected to Razorpay's secure payment gateway.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    $(document).ready(function() {
        var subscriptionId = "{{ $subscription_id }}";
        var planId = "{{ $plan->id }}";
        var userId = "{{ $user->id }}";
        var csrfToken = "{{ csrf_token() }}";
        var appName = "{{ config('app.name', 'App') }}";
        var razorpayKey = "{{ env('RAZORPAY_KEY_ID') }}";
        var existingSubscriptionId = "{{ $existing_subscription_id ?? '' }}";
        var creditAmount = "{{ $credit_amount ?? 0 }}";
        
        console.log('Subscription ID:', subscriptionId);
        console.log('Plan ID:', planId);
        console.log('User ID:', userId);
        
        // Validate required data
        if (!subscriptionId || !razorpayKey) {
            showError('Payment configuration error. Please contact support.');
            return;
        }
        
        var options = {
            "key": razorpayKey,
            "subscription_id": subscriptionId,
            "name": appName,
            "description": "Subscription for {{ $plan->name }}",
            "prefill": {
                "name": "{{ $user->name }}",
                "email": "{{ $user->email }}",
                "contact": "{{ $user->phone ?? '9999999999' }}"
            },
            "theme": {
                "color": "#F37254"
            },
            "handler": function(response) {
                console.log('Payment successful response:', response);
                
                // Show loading
                showLoading();
                
                // Prepare form data
                var formData = new FormData();
                formData.append('_token', csrfToken);
                formData.append('razorpay_payment_id', response.razorpay_payment_id);
                formData.append('razorpay_subscription_id', subscriptionId);
                formData.append('razorpay_signature', response.razorpay_signature);
                formData.append('plan_id', planId);
                formData.append('user_id', userId);
                
                // Add upgrade info if applicable
                if (existingSubscriptionId) {
                    formData.append('existing_subscription_id', existingSubscriptionId);
                    formData.append('credit_amount', creditAmount);
                }
                
                formData.append('all_response', JSON.stringify(response));
                
                // Send to server via AJAX
                $.ajax({
                    url: "{{ route('admin.subscriptions.payment.success') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        console.log('Server response:', data);
                        
                        if (data.success) {
                            // Show success message
                            $('#paymentForm').hide();
                            $('#paymentSuccess').show();
                            
                            // Redirect after 2 seconds
                            setTimeout(function() {
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                } else {
                                    window.location.href = "{{ route('admin.subscriptions.index') }}";
                                }
                            }, 2000);
                        } else {
                            showError(data.error || 'Payment processing failed on server.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Payment verification failed:', xhr.responseText);
                        
                        var errorMsg = 'Payment verification failed. ';
                        
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.error) {
                                errorMsg += response.error;
                            } else if (response.message) {
                                errorMsg += response.message;
                            }
                        } catch (e) {
                            errorMsg += 'Please try again or contact support.';
                        }
                        
                        showError(errorMsg);
                    },
                    complete: function() {
                        hideLoading();
                    }
                });
            },
            "modal": {
                "ondismiss": function() {
                    console.log('Payment modal dismissed');
                    
                    // Don't immediately redirect - let user decide
                    if (confirm('Are you sure you want to cancel this payment?')) {
                        // Send failure to server
                        $.ajax({
                            url: "{{ route('admin.subscriptions.payment.failed') }}",
                            type: "POST",
                            data: {
                                _token: csrfToken,
                                razorpay_subscription_id: subscriptionId,
                                reason: 'user_cancelled'
                            },
                            success: function() {
                                window.location.href = "{{ route('plans.index') }}";
                            },
                            error: function() {
                                window.location.href = "{{ route('plans.index') }}";
                            }
                        });
                    }
                }
            },
            "notes": {
                "plan_id": planId,
                "user_id": userId
            }
        };

        var rzp = new Razorpay(options);
        
        // Open payment modal automatically
        rzp.open();
        
        // Also attach to button click
        $('#rzp-button').on('click', function(e) {
            e.preventDefault();
            rzp.open();
        });

        // Helper functions
        function showLoading() {
            $('#loadingOverlay').show();
        }
        
        function hideLoading() {
            $('#loadingOverlay').hide();
        }
        
        function showError(message) {
            $('#paymentForm').hide();
            $('#errorMessage').text(message);
            $('#paymentError').show();
        }
    });
    
    // Handle page visibility change (user might switch tabs during payment)
    document.addEventListener('visibilitychange', function() {
        if (document.visibilityState === 'visible') {
            // Check if payment was completed while tab was inactive
            console.log('Page became visible again');
        }
    });
    
    // Handle beforeunload to prevent accidental navigation
    window.addEventListener('beforeunload', function(e) {
        // Only show warning if payment might be in progress
        return null; // You can add a warning message here if needed
    });
    </script>
    
    @include('includes.script')
</body>
</html>