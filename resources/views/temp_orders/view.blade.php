<!DOCTYPE html>
<html lang="en">
<head>
  <title>Review Pending Order #{{ $order->order_id ?? $order->id }} • Bill&Bite POS</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">

  @include('includes.style')

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Font Awesome 6 CDN -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Pending Orders CSS -->
  <link rel="stylesheet" href="{{ asset('admin_template/css/pending-temp-orders.css') }}">
</head>
<body data-pc-theme="light">

@include('includes.sidebar')

<div class="pc-container">
  <div class="pc-content">
    
    <div class="pos-page-wrap">

      @php
        $isGstBill = ($order->is_gst_bill ?? 'NO') == 'YES';
        $orderNo = $order->order_id ?? ('#' . $order->id);
        $customerInitial = strtoupper(substr($order->customer_name ?? 'G', 0, 1));
      @endphp
      
      <!-- ===================================================
           1. TOP ORDER BANNER
           =================================================== -->
      <div class="pos-order-banner">
        <div class="pos-banner-top">
          <div class="pos-banner-title-area">
            <a href="{{ route('temp.orders') }}" class="btn-back-square" title="Back to Pending Orders">
              <i class="fa-solid fa-arrow-left"></i>
            </a>

            <div>
              <h1 class="pos-banner-order-no">
                <span>Pending Order {{ $orderNo }}</span>
                @if($isGstBill)
                  <span class="pos-bill-type-tag gst"><i class="fa-solid fa-percent me-1"></i> GST Bill</span>
                @else
                  <span class="pos-bill-type-tag nongst">Non-GST</span>
                @endif
              </h1>
              <p class="text-white-50 mb-0" style="font-size: 0.82rem;">
                <i class="fa-regular fa-clock me-1"></i> Placed on {{ $order->created_at ? $order->created_at->format('d M Y, h:i A') : 'Just now' }} • Contactless QR Order
              </p>
            </div>
          </div>

          <div class="d-flex align-items-center gap-2">
            <span class="pos-table-badge dine-in" style="background: rgba(255,255,255,0.15); color: #ffffff; border: 1px solid rgba(255,255,255,0.25);">
              <i class="fa-solid fa-spinner fa-spin me-1 text-warning"></i> Awaiting Approval
            </span>
          </div>
        </div>
      </div>

      <!-- Flash Messages -->
      @include('includes.message')

      <!-- ===================================================
           2. CUSTOMER & ORDER META INFO CARDS
           =================================================== -->
      <div class="pos-info-cards-grid">
        <!-- Customer Details -->
        <div class="pos-info-box">
          <div class="pos-info-box-icon">
            <i class="fa-solid fa-user"></i>
          </div>
          <div class="pos-info-box-meta">
            <span class="pos-info-box-label">Customer Name</span>
            <span class="pos-info-box-value">{{ $order->customer_name ?? 'Guest Customer' }}</span>
          </div>
        </div>

        <!-- Phone Number -->
        <div class="pos-info-box">
          <div class="pos-info-box-icon">
            <i class="fa-solid fa-phone"></i>
          </div>
          <div class="pos-info-box-meta">
            <span class="pos-info-box-label">Contact Phone</span>
            <span class="pos-info-box-value">
              @if($order->customer_phone)
                <a href="tel:{{ $order->customer_phone }}" class="text-dark text-decoration-none">{{ $order->customer_phone }}</a>
              @else
                <span class="text-muted">Not Provided</span>
              @endif
            </span>
          </div>
        </div>

        <!-- Dining Table -->
        <div class="pos-info-box">
          <div class="pos-info-box-icon">
            <i class="fa-solid fa-chair"></i>
          </div>
          <div class="pos-info-box-meta">
            <span class="pos-info-box-label">Table &amp; Dining Type</span>
            <span class="pos-info-box-value">
              @if($order->order_type == 'DINE_IN')
                {{ @$order->table_details->name ?? 'Dine In' }}
                @if($order->table_details)
                  @if($order->table_details->table_status == 'INACTIVE' || $order->table_details->status == 'I' || $order->table_details->status == 'D')
                    <span class="badge bg-danger ms-1" style="font-size: 0.65rem;">Inactive</span>
                  @elseif($order->table_details->table_status == 'OCCUPIED')
                    <span class="badge bg-warning text-dark ms-1" style="font-size: 0.65rem;">Occupied</span>
                  @else
                    <span class="badge bg-success ms-1" style="font-size: 0.65rem;">Available</span>
                  @endif
                @endif
              @else
                Takeaway Order
              @endif
            </span>
          </div>
        </div>

        <!-- Customer Remarks / Special Requests -->
        <div class="pos-info-box">
          <div class="pos-info-box-icon">
            <i class="fa-solid fa-note-sticky"></i>
          </div>
          <div class="pos-info-box-meta">
            <span class="pos-info-box-label">Customer Remarks</span>
            <span class="pos-info-box-value" style="font-size: 0.85rem; color: #92400e;">
              {{ $order->remarks ? $order->remarks : 'No special instructions' }}
            </span>
          </div>
        </div>
      </div>

      <!-- ===================================================
           3. MAIN LAYOUT: ITEMS LIST (LEFT) + BILL SUMMARY (RIGHT)
           =================================================== -->
      <div class="pos-view-layout-grid">
        
        <!-- LEFT: Items Review Table -->
        <div class="pos-card-container mb-0">
          <div class="pos-card-header">
            <h2 class="pos-card-title">
              <i class="fa-solid fa-utensils text-primary"></i>
              <span>Order Items</span>
              <span class="pos-badge-count">{{ count($order->items) }} Items</span>
            </h2>
          </div>

          <div class="table-responsive">
            <table class="pos-table-custom">
              <thead>
                <tr>
                  <th style="width: 40px;">#</th>
                  <th>Food Item</th>
                  <th class="text-center">Qty</th>
                  <th>Unit Price</th>
                  <th>Discount</th>
                  <th>Taxable</th>
                  <th>GST</th>
                  <th>Item Total</th>
                  <th class="text-end">Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($order->items as $key => $i)
                @php
                  $itemQty = max(1, intval($i->quantity ?? 1));
                  $basePrice = floatval($i->price ?? 0);
                  $itemDiscount = floatval($i->item_discount_percentage ?? 0);
                  $discountedPrice = floatval($i->discounted_price ?: ($basePrice - ($basePrice * $itemDiscount / 100)));
                  $addonsList = $i->addons_list ?? [];
                  $isAddon = empty($i->subcategory_id);
                  $hasAddons = !$isAddon && !empty($addonsList) && count($addonsList) > 0;
                  $addonsCost = 0;
                  if ($hasAddons) {
                      foreach ($addonsList as $a) {
                          $addonsCost += (floatval($a['price'] ?? 0) * intval($a['qty'] ?? $a['quantity'] ?? 1));
                      }
                  }
                  $lineTaxable = floatval($i->taxable_amount > 0 ? $i->taxable_amount : ($isAddon ? ($discountedPrice * $itemQty) : (($discountedPrice * $itemQty) + $addonsCost)));
                  $gstRate = floatval($i->gst_rate ?? 0);
                  $lineGst = floatval($i->gst_amount > 0 ? $i->gst_amount : (($lineTaxable * $gstRate) / 100));
                  $itemTotal = floatval($i->total_amount > 0 ? $i->total_amount : ($lineTaxable + $lineGst));
                  $foodType = strtoupper($i->menuItem->food_type ?? 'VEG');
                  $itemDiscountAmount = ($basePrice * $itemDiscount / 100) * $itemQty;
                @endphp
                <tr>
                  <td><span class="text-muted" style="font-size: 0.8rem;">{{ $key + 1 }}</span></td>
                  <td>
                    <div>
                      <strong style="color: #0f172a; font-size: 0.92rem;">{{ $i->menuItem->name ?? 'Dish Item' }}</strong>
                      @if($isAddon)
                        <span class="badge bg-warning text-dark ms-1" style="font-size: 0.68rem; font-weight: 700;">Add-on</span>
                      @endif
                      @if($hasAddons)
                        <div class="mt-1 d-flex flex-wrap gap-1">
                          @foreach($i->addons_list as $addon)
                            @php
                              $aQty = $addon['qty'] ?? $addon['quantity'] ?? 1;
                              $aPrice = floatval($addon['price'] ?? 0);
                            @endphp
                            <span class="badge" style="background: #fff3ed; color: #ff5e14; border: 1px solid #ffd8c7; font-size: 0.72rem; font-weight: 600; padding: 2px 7px; display: inline-flex; align-items: center; gap: 4px;">
                              <span>+ {{ $addon['name'] }}</span>
                              <span style="color: #9a3412;">(₹{{ number_format($aPrice, 2) }})</span>
                              <span class="badge" style="background: #ffedd5; color: #c2410c; font-weight: 800; font-size: 0.7rem; padding: 1px 5px; border-radius: 4px; border: 1px solid #fed7aa;">x{{ $aQty }}</span>
                            </span>
                          @endforeach
                        </div>
                      @endif
                      <div class="mt-1">
                        <span class="pos-bill-type-tag {{ $foodType == 'VEG' ? 'gst' : 'nongst' }}" style="font-size: 0.65rem; padding: 1px 6px;">
                          <i class="fa-solid fa-circle me-1" style="font-size: 0.45rem; color: {{ $foodType == 'VEG' ? '#10b981' : '#ef4444' }};"></i> {{ $foodType }}
                        </span>
                        @if($itemDiscount > 0)
                          <span class="badge bg-success text-white ms-1" style="font-size: 0.65rem;">{{ $itemDiscount }}% OFF</span>
                        @endif
                      </div>
                    </div>
                  </td>
                  <td class="text-center">
                    <span class="pos-items-count-pill" style="font-size: 0.85rem; font-weight: 800;">
                      x{{ $itemQty }}
                    </span>
                  </td>
                  <td>
                    @if($itemDiscount > 0)
                      <del class="text-muted" style="font-size: 0.78rem;">₹{{ number_format($basePrice, 2) }}</del><br>
                      <strong class="text-success">₹{{ number_format($discountedPrice, 2) }}</strong>
                    @else
                      <strong>₹{{ number_format($basePrice, 2) }}</strong>
                    @endif
                  </td>
                  <td>
                    @if($itemDiscount > 0)
                      <span class="text-danger font-weight-bold">- ₹{{ number_format($itemDiscountAmount, 2) }}</span>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td>₹{{ number_format($lineTaxable, 2) }}</td>
                  <td>
                    @if($isGstBill)
                      ₹{{ number_format($lineGst, 2) }} <small class="text-muted">({{ $gstRate }}%)</small>
                    @else
                      <span class="text-muted">-</span>
                    @endif
                  </td>
                  <td>
                    <strong class="text-primary font-weight-bold" style="font-family: 'Outfit', sans-serif; font-size: 0.95rem;">
                      ₹{{ number_format($itemTotal, 2) }}
                    </strong>
                  </td>
                  <td class="text-end">
                    <a href="{{ route('temp.orders.view.delete.item', $i->id) }}" 
                       onclick="return confirm('Remove \'{{ addslashes($i->menuItem->name ?? 'item') }}\' from this pending order?')"
                       class="btn btn-sm btn-outline-danger" 
                       style="border-radius: 8px; padding: 4px 10px;"
                       title="Remove Item">
                      <i class="fa-solid fa-trash"></i>
                    </a>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>

        <!-- RIGHT: Financial Summary & Totals -->
        <div>
          <div class="pos-summary-card">
            <h3 class="pos-summary-title">
              <i class="fa-solid fa-calculator text-primary"></i>
              <span>Bill Breakdown</span>
            </h3>

            @php
              $computedSubtotal = 0;
              $computedTaxable = 0;
              $computedGst = 0;
              $computedItemDiscount = 0;

              foreach($order->items as $item) {
                $qty = max(1, intval($item->quantity ?? 1));
                $bPrice = floatval($item->price ?? 0);
                $iDisc = floatval($item->item_discount_percentage ?? 0);
                $dPrice = floatval($item->discounted_price ?: ($bPrice - ($bPrice * $iDisc / 100)));
                $aList = $item->addons_list ?? [];
                $aCost = 0;
                if (!empty($aList) && is_array($aList)) {
                    foreach ($aList as $a) {
                        $aCost += (floatval($a['price'] ?? 0) * intval($a['qty'] ?? $a['quantity'] ?? 1));
                    }
                }
                $isAdd = empty($item->subcategory_id);
                $lTax = floatval($item->taxable_amount > 0 ? $item->taxable_amount : ($isAdd ? ($dPrice * $qty) : (($dPrice * $qty) + $aCost)));
                $lOrig = $isAdd ? ($bPrice * $qty) : (($bPrice * $qty) + $aCost);
                $gRate = floatval($item->gst_rate ?? 0);
                $lGst = floatval($item->gst_amount > 0 ? $item->gst_amount : (($lTax * $gRate) / 100));

                $computedSubtotal += $lOrig;
                $computedTaxable += $lTax;
                $computedGst += $lGst;
                $computedItemDiscount += ($bPrice * $iDisc / 100) * $qty;
              }

              $displaySubtotal = floatval($order->total_amount > 0 ? $order->total_amount : $computedSubtotal);
              $displayItemDiscount = floatval($computedItemDiscount > 0 ? $computedItemDiscount : $order->discount);
              $displayTaxable = floatval($order->taxable_amount > 0 ? $order->taxable_amount : $computedTaxable);
              $displayGst = floatval($order->gst_amount > 0 ? $order->gst_amount : $computedGst);
              
              $orderDiscPercent = floatval($order->discount_percentage ?? 0);
              $orderDiscAmt = $orderDiscPercent > 0 ? (($displayTaxable * $orderDiscPercent) / 100) : 0;
              
              $grandTotal = floatval($order->grand_total > 0 ? $order->grand_total : ($displayTaxable - $orderDiscAmt + $displayGst));
              $finalAmount = round($grandTotal);
              $roundOff = $finalAmount - $grandTotal;
            @endphp

            <div class="pos-summary-row">
              <span>Subtotal</span>
              <span>₹{{ number_format($displaySubtotal, 2) }}</span>
            </div>

            @if($displayItemDiscount > 0)
            <div class="pos-summary-row text-success">
              <span>Item Level Discounts</span>
              <span>- ₹{{ number_format($displayItemDiscount, 2) }}</span>
            </div>
            @endif

            <div class="pos-summary-row bold-total">
              <span>Taxable Subtotal</span>
              <span>₹{{ number_format($displayTaxable, 2) }}</span>
            </div>

            @if($isGstBill)
            <div class="pos-summary-row">
              <span>GST ({{ $order->restaurant_gst_percentage ?? 0 }}%)</span>
              <span>₹{{ number_format($displayGst, 2) }}</span>
            </div>
            @endif

            @if($orderDiscAmt > 0)
            <div class="pos-summary-row text-success">
              <span>Order Discount ({{ $orderDiscPercent }}%)</span>
              <span>- ₹{{ number_format($orderDiscAmt, 2) }}</span>
            </div>
            @endif

            @if(abs($roundOff) > 0.001)
            <div class="pos-summary-row">
              <span>Round Off</span>
              <span>{{ $roundOff >= 0 ? '+' : '−' }} ₹{{ number_format(abs($roundOff), 2) }}</span>
            </div>
            @endif

            <div class="pos-grand-total-box">
              <span style="font-weight: 700; font-size: 0.92rem;">Calculated Grand Total</span>
              <span style="font-family: 'Outfit', sans-serif; font-size: 1.25rem; font-weight: 800; color: #ff8c42;">₹{{ number_format($grandTotal, 2) }}</span>
            </div>

            <div class="pos-final-amount-box">
              <span style="font-weight: 800; font-size: 0.95rem;"><i class="fa-solid fa-check-circle me-1"></i> Final Payable Amount</span>
              <span style="font-family: 'Outfit', sans-serif; font-size: 1.45rem; font-weight: 800;">₹{{ number_format($finalAmount, 2) }}</span>
            </div>
          </div>
        </div>

      </div>

      <!-- ===================================================
           4. FLOATING APPROVAL & REJECTION ACTION BAR
           =================================================== -->
      @php
        $tableInactive = $order->table_details && ($order->table_details->table_status == 'INACTIVE' || $order->table_details->status == 'I' || $order->table_details->status == 'D');
      @endphp

      <div class="pos-approval-bar">
        @if(!$tableInactive)
          <a href="{{ route('admin.temporder.approve', $order->id) }}" 
             class="btn-approve-big" 
             onclick="return confirm('Approve this customer order and send tickets directly to kitchen?')">
            <i class="fa-solid fa-circle-check"></i>
            <span>Approve &amp; Send to Kitchen</span>
          </a>

          <a href="{{ route('admin.temporder.reject', $order->id) }}" 
             class="btn-reject-big" 
             onclick="return confirm('Reject this pending order? The customer will see the rejected notification.')">
            <i class="fa-solid fa-circle-xmark"></i>
            <span>Reject Order</span>
          </a>

          <a href="{{ route('temp.orders') }}" class="btn-pos-pill">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back to Queue</span>
          </a>
        @else
          <div class="alert alert-danger mb-0 d-inline-flex align-items-center gap-2" style="border-radius: 30px; font-weight: 700; font-size: 0.88rem;">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span>Table is currently inactive or under maintenance. Please update table status before approving.</span>
          </div>
          <a href="{{ route('temp.orders') }}" class="btn-pos-pill">
            <i class="fa-solid fa-arrow-left"></i> Back to Queue
          </a>
        @endif
      </div>

    </div><!-- /.pos-page-wrap -->

  </div><!-- /.pc-content -->
</div><!-- /.pc-container -->

<!-- JS & Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@include('includes.script')

</body>
</html>