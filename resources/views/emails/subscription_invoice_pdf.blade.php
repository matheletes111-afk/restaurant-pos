<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $invoice_no }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #000000;
        }
        table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }
        table td {
            padding: 8px;
            vertical-align: top;
        }
        .header-table td {
            padding: 0;
        }
        .title {
            font-size: 28px;
            color: #ff6a00;
            font-weight: bold;
            text-transform: uppercase;
        }
        .company-info {
            text-align: right;
            font-size: 13px;
            color: #555;
        }
        .divider {
            border-bottom: 2px solid #ff6a00;
            margin: 20px 0;
        }
        .details-table {
            margin-bottom: 30px;
        }
        .details-table td {
            width: 50%;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #ff6a00;
            text-transform: uppercase;
            margin-bottom: 8px;
            border-bottom: 1px solid #eee;
            padding-bottom: 4px;
        }
        .info-block p {
            margin: 4px 0;
            font-size: 13px;
        }
        .items-table {
            width: 100%;
            margin-bottom: 30px;
        }
        .items-table th {
            background-color: #ff6a00;
            color: #ffffff;
            font-weight: bold;
            text-align: left;
            padding: 10px 8px;
            font-size: 13px;
            text-transform: uppercase;
        }
        .items-table td {
            border-bottom: 1px solid #eee;
            padding: 12px 8px;
            font-size: 13px;
        }
        .items-table tr.total-row td {
            border-bottom: none;
            font-weight: bold;
            font-size: 14px;
            background-color: #fcfcfc;
        }
        .text-right {
            text-align: right !important;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <!-- Logo and Title Header -->
        <table class="header-table">
            <tr>
                <td>
                    @if(file_exists(public_path('logo.png')))
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('logo.png'))) }}" style="max-height: 55px;" alt="Bill & Bite">
                    @else
                        <span class="title">Bill & Bite</span>
                    @endif
                </td>
                <td class="company-info">
                    <span class="title" style="font-size: 24px; display: block; margin-bottom: 5px;">INVOICE</span>
                    <strong>SRV Technology</strong><br>
                    B.T Ranadeep Colony, Matigara<br>
                    Siliguri - 734010, West Bengal, India.<br>
                    Contact : info@billnbite.com<br>
                    GSTIN: 19AEKFS8887F1Z4
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- Billing details and Invoice Meta details -->
        <table class="details-table">
            <tr>
                <td>
                    <div class="section-title">Bill To:</div>
                    <div class="info-block">
                        <p><strong>{{ $restaurant->name }}</strong></p>
                        <p>Owner: {{ $restaurant->owner->name ?? 'N/A' }}</p>
                        <p>Email: {{ $restaurant->owner->email ?? 'N/A' }}</p>
                        <p>Phone: {{ $restaurant->owner->phone ?? 'N/A' }}</p>
                        <p>Address: {{ $restaurant->address }}, {{ $restaurant->pincode }}</p>
                        @if($restaurant->gstin)
                            <p>GSTIN: {{ $restaurant->gstin }}</p>
                        @endif
                    </div>
                </td>
                <td style="padding-left: 40px;">
                    <div class="section-title">Invoice Details:</div>
                    <div class="info-block">
                        <p><strong>Invoice No:</strong> {{ $invoice_no }}</p>
                        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($invoice_date)->format('d M Y') }}</p>
                        <p><strong>Subscription ID:</strong> {{ $subscription->razorpay_subscription_id ?? 'SUB-' . str_pad($subscription->id, 6, '0', STR_PAD_LEFT) }}</p>
                        <p><strong>Payment Method:</strong> {{ strtoupper($payment->payment_method ?? 'N/A') }}</p>
                        <p><strong>Payment Status:</strong> {{ $payment ? strtoupper($payment->status) : 'PENDING' }}</p>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Item list table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right">Billing Cycle</th>
                    <th class="text-right">Period</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $amountPaid = $payment->amount ?? $plan->price;
                    $gstPercentage = $plan->gst_percentage ?? 18;
                    $taxableAmount = $plan->taxable_amount ?? ($amountPaid / (1 + ($gstPercentage / 100)));
                    $gstAmount = $amountPaid - $taxableAmount;
                @endphp
                <tr>
                    <td>
                        <strong>{{ $plan->name }} Plan Subscription</strong><br>
                        <span style="font-size: 11px; color: #777;">Access to all standard features including tables, menu management, KOT & orders.</span>
                    </td>
                    <td class="text-right">{{ ucfirst($plan->billing_cycle) }}</td>
                    <td class="text-right">
                        {{ \Carbon\Carbon::parse($subscription->start_date)->format('d M Y') }} to {{ \Carbon\Carbon::parse($subscription->end_date)->format('d M Y') }}
                    </td>
                    <td class="text-right">{{ number_format($taxableAmount, 2) }}</td>
                </tr>

                <!-- Summary subtotal and tax -->
                <tr class="total-row">
                    <td colspan="2"></td>
                    <td class="text-right" style="color: #555;">Taxable Amount:</td>
                    <td class="text-right">{{ number_format($taxableAmount, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="2"></td>
                    <td class="text-right" style="color: #555;">GST ({{ $gstPercentage }}%):</td>
                    <td class="text-right">{{ number_format($gstAmount, 2) }}</td>
                </tr>
                <tr class="total-row" style="border-top: 1px solid #ddd;">
                    <td colspan="2"></td>
                    <td class="text-right" style="color: #ff6a00; font-size: 15px;">Total Paid:</td>
                    <td class="text-right" style="color: #ff6a00; font-size: 15px;">{{ number_format($amountPaid, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Razorpay payment reference block -->
        @if($payment && $payment->razorpay_payment_id)
            <div style="background-color: #f9f9f9; padding: 15px; border-radius: 6px; font-size: 12px; border: 1px solid #eee; margin-top: 20px;">
                <strong>Payment Reference Information:</strong><br>
                Razorpay Payment ID: {{ $payment->razorpay_payment_id }}<br>
                Transaction Date: {{ $payment->created_at->format('d M Y H:i:s') }}<br>
                Thank you for choosing Bill & Bite POS to empower your culinary business!
            </div>
        @endif

        <div class="footer">
            <p>This is a computer-generated document and does not require a physical signature.</p>
            <p>For support queries, write to info@billnbite.com or visit support portal.</p>
            <p>&copy; {{ date('Y') }} Bill & Bite. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
