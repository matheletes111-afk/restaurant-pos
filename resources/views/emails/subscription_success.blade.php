<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Activated Successfully</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 0;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .greeting strong {
            color: #10b981;
            font-size: 20px;
        }
        .plan-details {
            background: linear-gradient(135deg, #e8f4f8 0%, #d1fae5 100%);
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .plan-details h3 {
            margin-top: 0;
            color: #065f46;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #10b981;
            padding: 20px;
            margin: 25px 0;
            border-radius: 5px;
        }
        .info-item {
            margin-bottom: 12px;
            padding: 8px;
            background: white;
            border-radius: 5px;
        }
        .info-label {
            font-weight: bold;
            color: #10b981;
            display: inline-block;
            width: 140px;
        }
        .info-value {
            color: #333;
        }
        .payment-details {
            background-color: #fef3c7;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .payment-details h3 {
            margin-top: 0;
            color: #92400e;
        }
        .gst-breakdown {
            background-color: #f0fdf4;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            border: 1px solid #bbf7d0;
        }
        .gst-breakdown .row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        .gst-breakdown .label {
            font-weight: 500;
            color: #166534;
        }
        .gst-breakdown .value {
            font-weight: 600;
            color: #14532d;
        }
        .gst-breakdown .total {
            border-top: 1px solid #bbf7d0;
            margin-top: 5px;
            padding-top: 8px;
            font-weight: bold;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #e0e0e0;
        }
        @media only screen and (max-width: 600px) {
            .content {
                padding: 20px;
            }
            .info-label {
                display: block;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Subscription Activated Successfully!</h1>
            <p>Your plan is now active</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                Dear <strong>{{ $user->name ?? $restaurant->owner->name ?? 'Valued Customer' }}</strong>,
            </div>
            
            <p>Congratulations! Your subscription has been successfully activated. You now have access to all the features included in your chosen plan.</p>
            
            <div class="plan-details">
                <h3>🎯 Plan Details</h3>
                <div class="info-item">
                    <span class="info-label">Plan Name:</span>
                    <span class="info-value"><strong>{{ $plan->name }}</strong></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Billing Cycle:</span>
                    <span class="info-value">{{ ucfirst($plan->billing_cycle) }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Duration:</span>
                    <span class="info-value">{{ $plan->duration_days }} days</span>
                </div>
            </div>
            
            <div class="info-box">
                <h3>📅 Subscription Period</h3>
                <div class="info-item">
                    <span class="info-label">Start Date:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($subscription->start_date)->format('d M Y') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">End Date:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($subscription->end_date)->format('d M Y') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Auto Renew:</span>
                    <span class="info-value">{{ $subscription->auto_renew ? 'Yes' : 'No' }}</span>
                </div>
            </div>
            
            <div class="payment-details">
                <h3>💰 Payment Information</h3>
                
                @php
                    $amountPaid = $payment->amount ?? $plan->price;
                    $gstPercentage = $plan->gst_percentage ?? 18;
                    $taxableAmount = $plan->taxable_amount ?? ($amountPaid / (1 + ($gstPercentage / 100)));
                    $gstAmount = $amountPaid - $taxableAmount;
                @endphp
                
                <div class="info-item">
                    <span class="info-label">Payment ID:</span>
                    <span class="info-value">{{ $payment->razorpay_payment_id ?? 'N/A' }}</span>
                </div>
                
                <div class="gst-breakdown">
                    <div class="row">
                        <span class="label">Taxable Amount:</span>
                        <span class="value">₹{{ number_format($taxableAmount, 2) }}</span>
                    </div>
                    <div class="row">
                        <span class="label">GST ({{ $gstPercentage }}%):</span>
                        <span class="value">₹{{ number_format($gstAmount, 2) }}</span>
                    </div>
                    <div class="row total">
                        <span class="label">Total Amount Paid:</span>
                        <span class="value">₹{{ number_format($amountPaid, 2) }}</span>
                    </div>
                </div>
            </div>
            
            <center>
                <a href="{{ url('/restaurant/plans') }}" class="button">View Your Plans</a>
            </center>
            
            <p>If you have any questions or need assistance with your subscription, please don't hesitate to contact our support team.</p>
            
            <p>Best regards,<br>
            <strong>{{ config('app.name') }} Team</strong></p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>This is an automated message, please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>