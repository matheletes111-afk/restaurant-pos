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
                                <h5 class="m-b-10">{{ isset($is_payment_method_update) && $is_payment_method_update ? 'Update AutoPay Bank / Card' : 'Complete Payment' }}</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('plans.index') }}">Plans</a></li>
                                <li class="breadcrumb-item" aria-current="page">{{ isset($is_payment_method_update) && $is_payment_method_update ? 'Update Bank Details' : 'Payment' }}</li>
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
                            <h5>{{ isset($is_payment_method_update) && $is_payment_method_update ? 'Update AutoPay Payment Method' : 'Payment Information' }}</h5>
                        </div>
                        <div class="card-body">
                            @include('includes.message')
                            
                            <!-- Payment Status Messages -->
                            <div id="paymentSuccess" class="payment-status payment-success" style="display: none;">
                                <h4><i class="fa fa-check-circle"></i> {{ isset($is_payment_method_update) && $is_payment_method_update ? 'Bank / Card Updated Successfully!' : 'Payment Successful!' }}</h4>
                                <p>Redirecting to subscriptions page...</p>
                            </div>
                            
                            <div id="paymentError" class="payment-status payment-error" style="display: none;">
                                <h4><i class="fa fa-exclamation-circle"></i> {{ isset($is_payment_method_update) && $is_payment_method_update ? 'Bank Update Failed' : 'Payment Failed' }}</h4>
                                <p id="errorMessage"></p>
                                <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">Go Back to Subscriptions</a>
                            </div>
                            
                            <div id="paymentForm">
                                <div class="row">
                                    <div class="col-md-8 offset-md-2">
                                        @if(isset($is_payment_method_update) && $is_payment_method_update)
                                        <div class="alert alert-warning mb-4" style="border-radius: 10px;">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fas fa-university fa-2x text-warning me-2"></i>
                                                <div>
                                                    <strong>Changing AutoPay Bank Account / Card</strong>
                                                    <p class="mb-0 small text-dark">Your current active subscription validity and remaining days will not change. Authenticating with your new bank details will set them for all future renewals.</p>
                                                </div>
                                            </div>
                                        </div>
                                        @endif

                                        <div class="payment-summary">
                                            <h4 class="text-center mb-4">{{ isset($is_payment_method_update) && $is_payment_method_update ? 'Bank Update & Authorization Summary' : 'Payment Summary' }}</h4>
                                            
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th>Plan Name:</th>
                                                    <td><strong>{{ $plan->name }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <th>Billing Cycle:</th>
                                                    <td>{{ ucfirst($plan->billing_cycle) }}</td>
                                                </tr>
                                                @if(isset($is_upgrade) && $is_upgrade && isset($existing_subscription) && $existing_subscription)
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
                                                    <th>{{ isset($is_payment_method_update) && $is_payment_method_update ? 'Renewal Rate / Auth Amount:' : 'Amount Payable:' }}</th>
                                                    <td class="font-weight-bold text-success">₹{{ number_format($payable_amount, 2) }}</td>
                                                </tr>
                                            </table>
                                            
                                            <div class="text-center mt-4">
                                                <button id="rzp-button" class="btn btn-primary btn-lg" style="box-shadow: 0 4px 14px rgba(255, 106, 0, 0.35); font-weight: 700; padding: 14px 32px; border-radius: 50px;">
                                                    <i class="fa fa-credit-card me-2"></i> 
                                                    <span id="rzp-button-text">{{ isset($is_payment_method_update) && $is_payment_method_update ? 'Authorize New Bank / Card (₹' . number_format($payable_amount, 2) . ')' : 'Pay Now (₹' . number_format($payable_amount, 2) . ')' }}</span>
                                                </button>
                                                
                                                <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary btn-lg ms-2" style="padding: 14px 24px; border-radius: 50px;">
                                                    <i class="fa fa-times me-1"></i> Cancel
                                                </a>
                                            </div>

                                            <div class="alert alert-info mt-3" id="autoPopupNotice" style="border-radius: 10px;">
                                                <i class="fa fa-info-circle me-1"></i> 
                                                <span>Razorpay's secure payment window will open automatically. If not opened, please click <strong>Pay Now</strong> above.</span>
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
    (function() {
        // Safe configuration extraction
        var config = {
            subscriptionId: @json($subscription_id ?? ''),
            planId: @json($plan->id ?? ''),
            userId: @json($user->id ?? ''),
            csrfToken: @json(csrf_token()),
            appName: @json(config('app.name', 'Bill&Bite POS')),
            razorpayKey: @json(config('services.razorpay.key_id') ?? env('RAZORPAY_KEY_ID', '')),
            existingSubscriptionId: @json($existing_subscription_id ?? ''),
            creditAmount: @json($credit_amount ?? 0),
            isPaymentMethodUpdate: @json(isset($is_payment_method_update) && $is_payment_method_update ? 1 : 0),
            oldSubscriptionId: @json($old_subscription_id ?? ''),
            customerName: @json($user->name ?? 'Customer'),
            customerEmail: @json($user->email ?? auth()->user()->email ?? ''),
            customerPhone: @json(preg_replace('/[^0-9]/', '', $user->phone ?? auth()->user()->phone ?? '')),
            planName: @json($plan->name ?? 'Subscription Plan'),
            paymentSuccessUrl: @json(route('admin.subscriptions.payment.success')),
            paymentFailedUrl: @json(route('admin.subscriptions.payment.failed')),
            subscriptionsIndexUrl: @json(route('admin.subscriptions.index')),
            plansIndexUrl: @json(route('plans.index'))
        };

        console.log('Payment Config Initialized:', {
            subscriptionId: config.subscriptionId,
            planId: config.planId,
            userId: config.userId,
            hasKey: !!config.razorpayKey
        });

        // 1. Dynamic SDK Loader with fallback
        function ensureRazorpaySDK(callback, failureCallback) {
            if (typeof window.Razorpay === 'function') {
                callback();
                return;
            }

            console.log('Razorpay SDK not detected in global scope. Dynamically injecting SDK...');
            var existingScript = document.getElementById('rzp-sdk-script');
            if (existingScript) {
                existingScript.remove();
            }

            var script = document.createElement('script');
            script.id = 'rzp-sdk-script';
            script.src = 'https://checkout.razorpay.com/v1/checkout.js';
            script.async = true;
            script.onload = function() {
                if (typeof window.Razorpay === 'function') {
                    console.log('Razorpay SDK successfully loaded dynamically.');
                    callback();
                } else if (typeof failureCallback === 'function') {
                    failureCallback('Razorpay SDK failed to initialize.');
                }
            };
            script.onerror = function() {
                console.error('Failed to load Razorpay checkout.js script from CDN.');
                if (typeof failureCallback === 'function') {
                    failureCallback('Unable to connect to Razorpay payment gateway. Please check your internet connection or disable ad-blockers and try again.');
                }
            };
            document.head.appendChild(script);
        }

        // 2. Build Options & Open Razorpay
        var activeRzpInstance = null;
        var isSubmittingPayment = false;

        function launchRazorpayCheckout(isUserClick) {
            if (isSubmittingPayment) return;

            if (!config.subscriptionId || !config.razorpayKey) {
                showError('Payment configuration error (Missing key or subscription ID). Please contact support.');
                return;
            }

            var btn = $('#rzp-button');
            var originalBtnHtml = btn.html();

            if (isUserClick) {
                btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-1"></i> Opening Gateway...');
            }

            ensureRazorpaySDK(function() {
                btn.prop('disabled', false).html(originalBtnHtml);

                var options = {
                    "key": config.razorpayKey,
                    "subscription_id": config.subscriptionId,
                    "name": config.appName,
                    "description": config.isPaymentMethodUpdate ? ("Update AutoPay Bank for " + config.planName) : ("Subscription for " + config.planName),
                    "prefill": {
                        "name": config.customerName,
                        "email": config.customerEmail,
                        "contact": config.customerPhone
                    },
                    "theme": {
                        "color": "#ff6a00"
                    },
                    "handler": function(response) {
                        console.log('Payment successful response received from Razorpay:', response);
                        isSubmittingPayment = true;
                        showLoading();

                        var formData = new FormData();
                        formData.append('_token', config.csrfToken);
                        formData.append('razorpay_payment_id', response.razorpay_payment_id || '');
                        formData.append('razorpay_subscription_id', config.subscriptionId);
                        formData.append('razorpay_signature', response.razorpay_signature || '');
                        formData.append('plan_id', config.planId);
                        formData.append('user_id', config.userId);

                        if (config.existingSubscriptionId) {
                            formData.append('existing_subscription_id', config.existingSubscriptionId);
                            formData.append('credit_amount', config.creditAmount);
                        }

                        if (config.isPaymentMethodUpdate) {
                            formData.append('is_payment_method_update', '1');
                            formData.append('old_subscription_id', config.oldSubscriptionId);
                        }

                        formData.append('all_response', JSON.stringify(response));

                        $.ajax({
                            url: config.paymentSuccessUrl,
                            type: "POST",
                            data: formData,
                            processData: false,
                            contentType: false,
                            dataType: 'json',
                            success: function(data) {
                                console.log('Server verification response:', data);
                                if (data && data.success) {
                                    $('#paymentForm').hide();
                                    $('#paymentSuccess').show();
                                    setTimeout(function() {
                                        window.location.href = data.redirect || config.subscriptionsIndexUrl;
                                    }, 1500);
                                } else {
                                    isSubmittingPayment = false;
                                    showError((data && data.error) ? data.error : 'Payment verification failed on server.');
                                }
                            },
                            error: function(xhr) {
                                isSubmittingPayment = false;
                                console.error('Payment verification failed:', xhr.responseText);
                                var errorMsg = 'Payment verification failed. ';
                                try {
                                    var res = JSON.parse(xhr.responseText);
                                    if (res.error) errorMsg += res.error;
                                    else if (res.message) errorMsg += res.message;
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
                            console.log('Payment modal dismissed by user');
                            btn.prop('disabled', false).html(originalBtnHtml);
                            $('#autoPopupNotice').html('<i class="fa fa-info-circle text-warning me-1"></i> Payment window closed. Click <strong>Pay Now</strong> above whenever you are ready to complete payment.');
                        }
                    },
                    "notes": {
                        "plan_id": config.planId,
                        "user_id": config.userId
                    }
                };

                try {
                    activeRzpInstance = new Razorpay(options);
                    activeRzpInstance.on('payment.failed', function(resp) {
                        console.warn('Razorpay payment failed callback:', resp);
                        if (resp.error) {
                            alert('Payment Failed: ' + (resp.error.description || resp.error.reason || 'Transaction could not be completed'));
                        }
                    });
                    activeRzpInstance.open();
                } catch (err) {
                    console.error('Error invoking Razorpay instance:', err);
                    if (isUserClick) {
                        showError('Could not open Razorpay checkout: ' + (err.message || 'Unknown error'));
                    }
                }
            }, function(errorMsg) {
                btn.prop('disabled', false).html(originalBtnHtml);
                showError(errorMsg);
            });
        }

        // 3. Document Ready Initialization
        $(document).ready(function() {
            // Register button click handler FIRST to guarantee user interaction always works
            $('#rzp-button').on('click', function(e) {
                e.preventDefault();
                launchRazorpayCheckout(true);
            });

            // Trigger safe auto-open with small delay
            setTimeout(function() {
                try {
                    launchRazorpayCheckout(false);
                } catch (autoErr) {
                    console.warn('Auto-open was blocked by browser or failed:', autoErr);
                }
            }, 600);
        });

        // Helper functions
        function showLoading() {
            $('#loadingOverlay').css('display', 'flex');
        }

        function hideLoading() {
            $('#loadingOverlay').hide();
        }

        function showError(message) {
            $('#paymentForm').hide();
            $('#errorMessage').text(message);
            $('#paymentError').show();
        }

        window.retryPayment = function() {
            $('#paymentError').hide();
            $('#paymentForm').show();
            launchRazorpayCheckout(true);
        };
    })();
    </script>
    
    @include('includes.script')
</body>
</html>