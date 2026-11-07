<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt #{{ $order->order_id }}</title>
    <link rel="shortcut icon" href="{{ asset('fav_web.png') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            width: 100%;
            margin: 0;
            padding: 5px;
            background: #fff;
        }
        .receipt {
            width: 100%;
            max-width: 280px;
            margin: 0 auto;
            background: white;
        }
        .center { 
            text-align: center; 
        }
        .left { 
            text-align: left; 
        }
        .right { 
            text-align: right; 
        }
        .bold {
            font-weight: bold;
        }
        .line {
            border-bottom: 1px dashed #aaa;
            margin: 5px 0;
        }
        .line-dotted {
            border-bottom: 1px dotted #ccc;
            margin: 4px 0;
        }
        
        /* Header Section */
        .restaurant-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .restaurant-details {
            font-size: 9px;
            color: #555;
            margin-bottom: 5px;
            line-height: 1.3;
        }
        
        /* Order Info */
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
            font-size: 10px;
        }
        .info-label {
            font-weight: bold;
        }
        
        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
        }
        .items-table th {
            text-align: left;
            font-size: 10px;
            padding: 4px 0;
            border-bottom: 1px solid #000;
        }
        .items-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .item-name {
            font-size: 10px;
            word-break: break-word;
            max-width: 130px;
        }
        .item-discount {
            font-size: 8px;
            color: #27ae60;
        }
        .gst-note {
            font-size: 8px;
            color: #777;
            padding-top: 0;
        }
        
        /* Totals Section */
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
        }
        .totals-table td {
            padding: 3px 0;
        }
        .totals-table td:last-child {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            font-size: 12px;
            border-top: 1px dashed #aaa;
        }
        .total-row td {
            padding-top: 6px;
        }
        
        /* GST Info Box */
        .gst-info {
            background: #f0fdf4;
            padding: 5px;
            margin: 5px 0;
            text-align: center;
            font-size: 8px;
            border: 1px solid #bbf7d0;
        }
        .non-gst-info {
            background: #fef3c7;
            padding: 5px;
            margin: 5px 0;
            text-align: center;
            font-size: 8px;
            border: 1px solid #fde68a;
        }
        
        /* Payment Section */
        .payment-info {
            margin: 6px 0;
            padding: 5px;
            background: #f9f9f9;
        }
        
        /* Footer */
        .footer {
            margin-top: 8px;
            padding-top: 5px;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
        
        /* Print Optimization */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .receipt {
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <!-- Restaurant Header -->
        <div class="center">
            <div class="restaurant-name">{{ strtoupper($restaurant_details->name ?? 'RESTAURANT') }}</div>
            <div class="restaurant-details">
                {{ $restaurant_details->address ?? '' }}<br>
                @if(!empty($restaurant_details->phone))
                Tel: {{ $restaurant_details->phone }}<br>
                @endif
                @if(!empty($restaurant_details->gstin))
                <strong>GSTIN:</strong> {{ $restaurant_details->gstin }}
                @endif
                @if(!empty($restaurant_details->vatin))
                @if(!empty($restaurant_details->gstin)) | @endif
                <strong>VATIN:</strong> {{ $restaurant_details->vatin }}
                @endif
            </div>
        </div>
        
        <div class="line"></div>
        
        <!-- Order Information -->
        <div class="info-row">
            <span class="info-label">Order #:</span>
            <span>{{ $order->order_id ?? $order->id }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Date:</span>
            <span>{{ $order->created_at->format('d/m/Y h:i A') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Customer:</span>
            <span>{{ $order->customer_name ?: 'Walk-in Guest' }}</span>
        </div>
        @if($order->customer_phone)
        <div class="info-row">
            <span class="info-label">Phone:</span>
            <span>{{ $order->customer_phone }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Type:</span>
            <span>{{ $order->order_type == 'DINE_IN' ? 'Dine In' : 'Takeaway' }}</span>
        </div>
        @if($order->table_id && $order->table)
        <div class="info-row">
            <span class="info-label">Table:</span>
            <span>{{ $order->table->name ?? 'Table ' . $order->table_id }}</span>
        </div>
        @endif
        
        <div class="line-dotted"></div>

        @php
            $isGstBill = isset($order->is_gst_bill) && $order->is_gst_bill == 'YES';
            $restaurantGstPercentage = (float)($order->restaurant_gst_percentage ?? 0);

            $foodList = isset($food_order_items) ? $food_order_items : $order->orderItems->filter(function($i) {
                return empty($i->bar_variation_id) && floatval($i->vat_rate ?? 0) <= 0 && (!isset($i->subcategory) || strtoupper($i->subcategory->food_type ?? '') !== 'BAR');
            });

            $barList = isset($bar_order_items) ? $bar_order_items : $order->orderItems->filter(function($i) {
                return !empty($i->bar_variation_id) || floatval($i->vat_rate ?? 0) > 0 || (isset($i->subcategory) && strtoupper($i->subcategory->food_type ?? '') === 'BAR');
            });
        @endphp
        
        <!-- Items Header -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="right">Qty</th>
                    <th class="right">Price</th>
                    <th class="right">Total</th>
                </tr>
            </thead>
            <tbody>
                {{-- 1. Food Items --}}
                @if($foodList && $foodList->count() > 0)
                    @if($barList && $barList->count() > 0)
                    <tr>
                        <td colspan="4" style="font-weight: bold; font-size: 9px; padding-top: 4px; padding-bottom: 2px; color: #333; text-decoration: underline;">
                            -- FOOD &amp; KITCHEN ITEMS --
                        </td>
                    </tr>
                    @endif
                    @foreach($foodList as $item)
                    @php
                        $itemDiscount = (float)($item->item_discount_percentage ?? 0);
                        $originalPrice = (float)$item->price;
                        $discountedPrice = (float)($item->discounted_price ?? ($originalPrice - ($originalPrice * $itemDiscount / 100)));
                        $quantity = (int)$item->quantity;
                        $addons = $item->addons_list;
                        $addonsCost = 0;
                        if (!empty($addons) && is_array($addons)) {
                            foreach ($addons as $a) {
                                $addonsCost += (floatval($a['price'] ?? 0) * intval($a['qty'] ?? $a['quantity'] ?? 1));
                            }
                        }
                        $isAddonItem = empty($item->subcategory_id);
                        $taxableAmount = $item->taxable_amount ?? ($isAddonItem ? ($discountedPrice * $quantity) : (($discountedPrice * $quantity) + $addonsCost));
                        
                        $gstRate = $isGstBill ? (float)($item->gst_rate ?? $restaurantGstPercentage) : 0;
                        $gstAmount = $item->gst_amount ?? (($taxableAmount * $gstRate) / 100);
                        $itemTotal = $taxableAmount + $gstAmount;
                    @endphp
                    <tr>
                        <td class="item-name">
                            {{ \Illuminate\Support\Str::limit($item->subcategory->name ?? 'Item', 24) }}
                            @if(!empty($addons) && !$isAddonItem)
                                @foreach($addons as $a)
                                    @php
                                        $aQty = $a['qty'] ?? $a['quantity'] ?? 1;
                                        $aPrice = floatval($a['price'] ?? 0);
                                    @endphp
                                    <div style="font-size: 8px; color: #333; padding-left: 4px; font-weight: 600;">
                                        + {{ $a['name'] ?? 'Addon' }} x{{ $aQty }} ({{ number_format($aPrice * $aQty, 2) }})
                                    </div>
                                @endforeach
                            @endif
                            @if($itemDiscount > 0)
                                <div class="item-discount">-{{ $itemDiscount }}% off</div>
                            @endif
                        </td>
                        <td class="right">{{ $quantity }}</td>
                        <td class="right">
                            @if($itemDiscount > 0)
                                <del>{{ number_format($originalPrice, 2) }}</del><br>
                                {{ number_format($discountedPrice, 2) }}
                            @else
                                {{ number_format($originalPrice, 2) }}
                            @endif
                        </td>
                        <td class="right">{{ number_format($itemTotal, 2) }}</td>
                    </tr>
                    @if($isGstBill && $gstRate > 0)
                    <tr class="gst-note">
                        <td colspan="4" class="right">
                            (GST {{ number_format($gstRate, 2) }}%: {{ number_format($gstAmount, 2) }})
                        </td>
                    </tr>
                    @endif
                    @endforeach
                @endif

                {{-- 2. Bar & Liquor Items (with ML and Peg size separate) --}}
                @if($barList && $barList->count() > 0)
                    <tr>
                        <td colspan="4" style="font-weight: bold; font-size: 9px; padding-top: 6px; padding-bottom: 2px; color: #854d0e; text-decoration: underline;">
                            -- BAR &amp; LIQUOR ITEMS --
                        </td>
                    </tr>
                    @foreach($barList as $bItem)
                    @php
                        $bDiscount = (float)($bItem->item_discount_percentage ?? 0);
                        $bPrice = (float)$bItem->price;
                        $bDiscounted = (float)($bItem->discounted_price ?? ($bPrice - ($bPrice * $bDiscount / 100)));
                        $bQty = (int)$bItem->quantity;
                        $bTaxable = $bItem->taxable_amount ?? ($bDiscounted * $bQty);
                        $bVatRate = floatval($bItem->vat_rate ?? 0);
                        $bVatAmount = $bItem->vat_amount ?? (($bTaxable * $bVatRate) / 100);
                        $bTotal = $bItem->total_amount ?? ($bTaxable + $bVatAmount);

                        $varTitle = $bItem->bar_variation_name;
                        $varMl = !empty($bItem->bar_variation_ml) ? floatval($bItem->bar_variation_ml) : null;
                    @endphp
                    <tr>
                        <td class="item-name">
                            {{ \Illuminate\Support\Str::limit($bItem->subcategory->name ?? 'Liquor Item', 24) }}
                            <div style="font-size: 8px; color: #1e293b; font-weight: bold; padding-left: 2px;">
                                [Peg: {{ $varTitle ?: 'Standard' }}@if($varMl) • {{ $varMl }}ml @endif]
                            </div>
                            @if($bDiscount > 0)
                                <div class="item-discount">-{{ $bDiscount }}% off</div>
                            @endif
                        </td>
                        <td class="right">{{ $bQty }}</td>
                        <td class="right">
                            @if($bDiscount > 0)
                                <del>{{ number_format($bPrice, 2) }}</del><br>
                                {{ number_format($bDiscounted, 2) }}
                            @else
                                {{ number_format($bPrice, 2) }}
                            @endif
                        </td>
                        <td class="right">{{ number_format($bTotal, 2) }}</td>
                    </tr>
                    @if($bVatRate > 0)
                    <tr class="gst-note">
                        <td colspan="4" class="right" style="color: #047857;">
                            (State VAT {{ number_format($bVatRate, 2) }}%: {{ number_format($bVatAmount, 2) }})
                        </td>
                    </tr>
                    @endif
                    @endforeach
                @endif
            </tbody>
        </table>
        
        <div class="line-dotted"></div>
        
        <!-- Totals Section -->
        @php
            $displaySubtotalVal = $original_subtotal ?? ($order->total_amount ?? 0);
            $displayTaxableVal = $total_taxable ?? ($order->taxable_amount ?? 0);
            $displayGstVal = $total_gst ?? ($order->gst_amount ?? 0);
            $displayVatVal = $total_vat ?? ($order->vat_amount ?? 0);
            $orderDiscountPercent = (float)($order->discount_percentage ?? 0);
            $orderDiscountAmount = (float)($order->discount ?? (($displayTaxableVal + $displayGstVal + $displayVatVal) * $orderDiscountPercent / 100));
            $finalTotal = (float)($order->grand_total ?? ($displayTaxableVal + $displayGstVal + $displayVatVal - $orderDiscountAmount));
            $roundOff = (float)($order->round_off ?? 0);
            $totalItemDiscount = max(0, $displaySubtotalVal - $displayTaxableVal);
        @endphp
        
        <table class="totals-table">
            @if($displaySubtotalVal > 0)
            <tr>
                <td>Subtotal:</td>
                <td>{{ number_format($displaySubtotalVal, 2) }}</td>
            </tr>
            @endif
            
            @if($totalItemDiscount > 0)
            <tr>
                <td>Item Discount:</td>
                <td>- {{ number_format($totalItemDiscount, 2) }}</td>
            </tr>
            @endif
            
            <tr>
                <td>Taxable Amount:</td>
                <td>{{ number_format($displayTaxableVal, 2) }}</td>
            </tr>
            
            @if($isGstBill && $displayGstVal > 0)
            <tr>
                <td>GST ({{ $restaurantGstPercentage }}%):</td>
                <td>{{ number_format($displayGstVal, 2) }}</td>
            </tr>
            @endif

            @if($displayVatVal > 0)
            <tr>
                <td>State VAT (Liquor):</td>
                <td>{{ number_format($displayVatVal, 2) }}</td>
            </tr>
            @endif
            
            @if($orderDiscountPercent > 0)
            <tr>
                <td>Order Disc ({{ $orderDiscountPercent }}%):</td>
                <td>- {{ number_format($orderDiscountAmount, 2) }}</td>
            </tr>
            @endif
            
            @if(abs($roundOff) > 0.01)
            <tr>
                <td>Round Off:</td>
                <td>{{ number_format($roundOff, 2) }}</td>
            </tr>
            @endif
            
            <tr class="total-row">
                <td><strong>TOTAL:</strong></td>
                <td><strong>{{ number_format($finalTotal, 2) }}</strong></td>
            </tr>
        </table>
        

        
        <div class="line"></div>
        
        <!-- Payment Information -->
        @if($order->payment_status && $order->payment_status != 'PENDING')
        <div class="payment-info">
            <div class="info-row">
                <span class="info-label">Payment:</span>
                <span>{{ ucfirst($order->payment_status) }}</span>
            </div>
            @if($order->payment_method)
            <div class="info-row">
                <span class="info-label">Method:</span>
                <span>{{ $order->payment_method }}</span>
            </div>
            @endif
            @if($order->amount_paid > 0)
            <div class="info-row">
                <span class="info-label">Amount Paid:</span>
                <span>{{ number_format($order->amount_paid, 2) }}</span>
            </div>
            @php
                $balance = $finalTotal - ($order->amount_paid ?? 0);
            @endphp
            @if($balance > 0)
            <div class="info-row">
                <span class="info-label">Balance Due:</span>
                <span>{{ number_format($balance, 2) }}</span>
            </div>
            @endif
            @endif
        </div>
        <div class="line-dotted"></div>
        @endif
        
        <!-- GST Summary for Non-GST Bill -->
        <!-- @if(!$isGstBill)
        <div class="non-gst-info" style="margin-top: 5px;">
            <strong>Note:</strong> This is a Non-GST bill. No tax applicable.
        </div>
        <div class="line-dotted"></div>
        @endif -->
        
        <!-- Remarks -->
        @if($order->remarks && trim($order->remarks) !== '' && $order->remarks !== 'Rapid Bill Checkout')
        <div class="info-row">
            <span class="info-label">Remarks:</span>
            <span>{{ $order->remarks }}</span>
        </div>
        <div class="line-dotted"></div>
        @endif
        
        @if(!empty($restaurant_details->qr_code_image) || !empty($restaurant_details->upi_id))
        <div class="line-dotted"></div>
        <div class="center" style="margin: 8px 0;">
            @if(!empty($restaurant_details->qr_code_image))
            <div style="margin-bottom: 4px;">
                <img src="{{ public_path('storage/restaurant/' . $restaurant_details->qr_code_image) }}" style="width: 100px; height: 100px; object-fit: contain; display: inline-block;">
            </div>
            @endif
            @if(!empty($restaurant_details->upi_id))
            <div style="font-size: 9px; font-weight: bold;">
                UPI ID: {{ $restaurant_details->upi_id }}
            </div>
            @endif
        </div>
        @endif
        
        <!-- Footer -->
        <div class="footer">
            Thank you for dining with us!<br>
            We hope to serve you again.<br>
            <span style="font-size: 8px;">** Computer generated receipt **</span>
        </div>
    </div>
</body>
</html>