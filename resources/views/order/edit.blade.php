<!DOCTYPE html>
<html lang="en">
<head>
  <title>Edit Order #{{ $order->order_id ?? $order->id }} • Bill&Bite POS</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">

  @include('includes.style')

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Public+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Font Awesome 6 CDN Loaded after includes.style -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  
  <!-- External Order Terminal CSS -->
  <link rel="stylesheet" href="{{ asset('admin_template/css/order-terminal.css') }}?v={{ time() }}">
</head>
<body>
@include('includes.sidebar')

@php
  $totalDishesCount = $categories->sum(function($c) { return $c->subcategories->count(); });
@endphp

<div class="pc-container">
  <div class="pc-content">
    
    <!-- Hero Terminal Header -->
    <div class="terminal-header-card">
      <div class="terminal-header-left">
        <a href="{{ route('order.management.dashboard') }}" class="btn-back-floor" title="Back to Floor Dashboard">
          <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div class="terminal-header-meta">
          <span class="terminal-breadcrumb-tag">
            <i class="fa-solid fa-pen-to-square"></i> Edit Order Management
          </span>
          <h1 class="terminal-main-title">
            Order #{{ $order->order_id ?? $order->id }}
            @if($table)
              • {{ $table->name }}
            @endif
          </h1>
        </div>
      </div>

      <div class="d-flex align-items-center gap-2">
        @if($table)
          <span class="terminal-sub-badge terminal-badge-table">
            <i class="fa-solid fa-chair"></i> {{ $table->name }}
          </span>
        @else
          <span class="terminal-sub-badge terminal-badge-takeaway">
            <i class="fa-solid fa-bag-shopping"></i> Takeaway / Parcel
          </span>
        @endif

        <span class="badge bg-warning text-dark font-weight-bold px-3 py-2 rounded-pill">
          <i class="fa-solid fa-circle-dot me-1"></i> {{ strtoupper($order->order_status ?? 'PENDING') }}
        </span>
      </div>
    </div>

    <!-- Alert Messages -->
    @include('includes.message')

    <div class="row g-4">
      
      <!-- Left Main Column -->
      <div class="col-lg-8 col-xl-8">
        
        <!-- Customer & Seating Panel -->
        <div class="pos-panel-card">
          <div class="pos-panel-header">
            <h3 class="pos-panel-title">
              <i class="fa-solid fa-user-check text-primary"></i> Customer &amp; Order Details
            </h3>
            <span class="text-muted small">Bill ID: #{{ $order->order_id ?? $order->id }}</span>
          </div>
          <div class="pos-panel-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="pos-form-label">Customer Name</label>
                <input type="text" class="pos-form-control" id="customer_name" value="{{ $order->customer_name }}" readonly style="background:#f1f5f9; cursor:not-allowed;">
              </div>
              <div class="col-md-6">
                <label class="pos-form-label">Customer Mobile</label>
                <input type="text" class="pos-form-control" id="customer_phone" value="{{ $order->customer_phone ?? '' }}" placeholder="Enter phone number">
              </div>
              <input type="hidden" id="table_id" value="{{ $table ? $table->id : '' }}">
            </div>
          </div>
        </div>

        <!-- Menu Browser Toolbar -->
        <div class="menu-toolbar-panel">
          <div class="menu-search-wrapper">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="nameFilter" class="menu-search-input" placeholder="Search dishes &amp; add more items...">
          </div>
          <select id="vegFilter" class="menu-type-select">
            <option value="">All Dietary Types</option>
            <option value="veg">🌱 Pure Vegetarian</option>
            <option value="non-veg">🍗 Non-Vegetarian</option>
          </select>
        </div>

        <!-- Category Horizontal Tabs & Food Items -->
        <div class="pos-panel-card">
          <div class="pos-panel-body">
            
            <!-- Category Scroll Tabs -->
            <div class="menu-category-tabs">
              <button type="button" class="cat-pill-tab active" data-category-id="all">
                <i class="fa-solid fa-layer-group me-1"></i> All Menu
                <span class="badge bg-light text-dark ms-1" style="font-size: 0.72rem;">{{ $totalDishesCount }}</span>
              </button>
              @foreach($categories as $category)
                <button type="button" class="cat-pill-tab" data-category-id="{{ $category->id }}">
                  <i class="fa-solid fa-utensils me-1"></i> {{ $category->name }}
                  <span class="badge bg-light text-dark ms-1" style="font-size: 0.72rem;">{{ $category->subcategories->count() }}</span>
                </button>
              @endforeach
            </div>

            <!-- Categories Container -->
            <div id="dishesCategoryContainer">
              @foreach($categories as $key => $category)
                <div class="category-pane mb-3" id="category-pane-{{ $category->id }}" data-category-id="{{ $category->id }}">
                  
                  <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem;">
                      <i class="fa-solid fa-bookmark text-primary me-1"></i> {{ $category->name }}
                    </h5>
                    <span class="text-muted small">{{ $category->subcategories->count() }} {{ Str::plural('item', $category->subcategories->count()) }}</span>
                  </div>

                  <div class="food-items-grid">
                    @forelse($category->subcategories as $item)
                      @php
                        $isNonVeg = (strtolower($item->food_type ?? 'veg') == 'non-veg');
                        $discount = (float)($item->discount_percentage ?? 0);
                        $finalPrice = $discount > 0 ? ($item->price - ($item->price * $discount / 100)) : $item->price;
                      @endphp
                      <div class="food-menu-card food-card" 
                           data-category-id="{{ $category->id }}"
                           data-type="{{ $isNonVeg ? 'non-veg' : 'veg' }}" 
                           data-name="{{ strtolower($item->name) }}">
                        
                        <div>
                          <div class="food-badge-strip">
                            <span class="badge-food-type {{ $isNonVeg ? 'badge-food-nonveg' : 'badge-food-veg' }}">
                              <i class="fa-solid {{ $isNonVeg ? 'fa-drumstick-bite' : 'fa-leaf' }}"></i>
                              {{ $item->food_type ?? 'Veg' }}
                            </span>
                            @if($discount > 0)
                              <span class="badge-food-discount">
                                <i class="fa-solid fa-tag"></i> {{ $discount }}% OFF
                              </span>
                            @endif
                          </div>

                          <h4 class="food-item-name">{{ $item->name }}</h4>
                        </div>

                        <div>
                          <div class="food-pricing-row">
                            <span class="price-final">₹{{ number_format($finalPrice, 2) }}</span>
                            @if($discount > 0)
                              <span class="price-original-del">₹{{ number_format($item->price, 2) }}</span>
                            @endif
                          </div>

                          <button type="button" class="btn-add-food add-item-btn"
                                  data-id="{{ $item->id }}"
                                  data-name="{{ $item->name }}"
                                  data-price="{{ $item->price }}"
                                  data-discount="{{ $discount }}">
                            <i class="fa-solid fa-plus"></i> Add Item
                          </button>
                        </div>
                      </div>
                    @empty
                      <div class="text-center py-4 text-muted w-100">
                        <i class="fa-solid fa-utensils fa-2x mb-2 d-block"></i>
                        No food items available under this category.
                      </div>
                    @endforelse
                  </div>

                  <div class="no-dishes-notice text-center py-3 text-muted" style="display: none;">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> No matching dishes found in {{ $category->name }}.
                  </div>
                </div>
              @endforeach
            </div>

          </div>
        </div>

        <!-- Existing Order Items Table -->
        <div class="pos-panel-card">
          <div class="pos-panel-header">
            <h3 class="pos-panel-title">
              <i class="fa-solid fa-fire text-warning"></i> Existing Order Items (Active KOTs)
            </h3>
            <span class="badge bg-secondary text-white">{{ count($order->orderItems) }} Fired Items</span>
          </div>

          <div class="pos-panel-body p-0">
            <div class="pos-table-wrap">
              <table class="pos-order-table">
                <thead>
                  <tr>
                    <th>Item &amp; Dish</th>
                    <th class="text-end">Price</th>
                    <th class="text-center">Disc %</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Disc Price</th>
                    <th class="text-end">Taxable</th>
                    @if(isset($restaurant_gstin) && $restaurant_gstin)
                      <th class="text-center">GST %</th>
                      <th class="text-end">GST Amt</th>
                    @endif
                    <th class="text-end">Total</th>
                    @if($order->order_status == 'PENDING')
                      <th class="text-center" style="width: 50px;">Action</th>
                    @endif
                  </tr>
                </thead>
                <tbody id="existingItems">
                  @forelse($order->orderItems as $item)
                    @php
                      $itemDiscount = $item->item_discount_percentage ?? 0;
                      $discountedPrice = $item->price - ($item->price * $itemDiscount / 100);
                      $taxableAmount = $discountedPrice * $item->quantity;
                      $gstAmount = ($taxableAmount * ($item->gst_rate ?? 0)) / 100;
                      $itemTotal = $taxableAmount + $gstAmount;
                    @endphp
                    <tr data-item-id="{{ $item->id }}">
                      <td>
                        <strong class="text-dark">{{ $item->subcategory->name ?? 'Dish Item' }}</strong>
                        @if(!empty($item->kot_no))
                          <span class="badge bg-dark text-white ms-1" style="font-size: 0.72rem;">{{ $item->kot_no }}</span>
                        @endif
                      </td>
                      <td class="text-end">₹{{ number_format($item->price, 2) }}</td>
                      <td class="text-center">{{ $itemDiscount }}%</td>
                      <td class="text-center fw-bold">{{ $item->quantity }}</td>
                      <td class="text-end">₹{{ number_format($discountedPrice, 2) }}</td>
                      <td class="text-end">₹{{ number_format($taxableAmount, 2) }}</td>
                      @if(isset($restaurant_gstin) && $restaurant_gstin)
                        <td class="text-center">{{ $item->gst_rate ?? 0 }}%</td>
                        <td class="text-end">₹{{ number_format($gstAmount, 2) }}</td>
                      @endif
                      <td class="text-end fw-bold text-dark">₹{{ number_format($itemTotal, 2) }}</td>
                      @if($order->order_status == 'PENDING')
                        <td class="text-center">
                          <button type="button" class="btn-remove-row delete-existing" data-id="{{ $item->id }}" title="Delete item from bill">
                            <i class="fa-solid fa-trash"></i>
                          </button>
                        </td>
                      @endif
                    </tr>
                  @empty
                    <tr>
                      <td colspan="10" class="text-center py-4 text-muted">
                        No active items found in this order.
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Newly Added Items Section -->
        <div class="pos-panel-card" id="newItemsSection">
          <div class="pos-panel-header">
            <h3 class="pos-panel-title">
              <i class="fa-solid fa-plus-circle text-success"></i> Newly Added Items to Fire
            </h3>
            <span id="newItemsCountBadge" class="badge bg-success text-white">0 New Items</span>
          </div>

          <div class="pos-panel-body p-0">
            <div class="pos-table-wrap">
              <table class="pos-order-table" id="newItemsTable">
                <thead>
                  <tr>
                    <th>Item &amp; Dish</th>
                    <th class="text-end">Price</th>
                    <th class="text-center">Disc %</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Disc Price</th>
                    <th class="text-end">Taxable</th>
                    @if(isset($restaurant_gstin) && $restaurant_gstin)
                      <th class="text-center">GST %</th>
                      <th class="text-end">GST Amt</th>
                    @endif
                    <th class="text-end">Total</th>
                    <th class="text-center" style="width: 50px;">Action</th>
                  </tr>
                </thead>
                <tbody id="newItemsBody">
                  <!-- Dynamic content injected via JS -->
                </tbody>
              </table>
            </div>

            <div id="emptyNewItems" class="terminal-empty-items">
              <i class="fa-solid fa-circle-plus"></i>
              <h5>No New Items Added Yet</h5>
              <p>Click "+ Add Item" from the food catalog above to append more items to this order.</p>
            </div>
          </div>
        </div>

      </div>

      <!-- Right Column (Sticky Summary) -->
      <div class="col-lg-4 col-xl-4">
        <div class="sticky-summary-dock">
          
          <!-- GST Status Callout -->
          @if(isset($restaurant_gstin) && $restaurant_gstin)
            <div class="gst-status-callout">
              <div>
                <i class="fa-solid fa-file-invoice text-success me-1"></i>
                <strong class="text-success">GST Tax Invoice</strong>
                <div class="small text-muted">GSTIN: {{ $restaurant_gstin }}</div>
              </div>
              <span class="badge bg-success text-white px-2 py-1">Rate: {{ $restaurant_gst_percentage ?? 0 }}%</span>
            </div>
          @else
            <div class="gst-status-callout non-gst">
              <div>
                <i class="fa-solid fa-receipt text-warning me-1"></i>
                <strong style="color: #92400e;">Non-GST Bill</strong>
                <div class="small text-muted">Standard Composite Bill</div>
              </div>
              <span class="badge bg-secondary text-white px-2 py-1">No GST</span>
            </div>
          @endif

          <!-- Order Summary Card -->
          <div class="summary-details-card">
            <h4 class="pos-panel-title mb-3">
              <i class="fa-solid fa-receipt text-primary"></i> Order Summary
            </h4>

            <div class="summary-row-item">
              <span class="summary-row-label">Original Subtotal</span>
              <span class="summary-row-val">₹<span id="original_subtotal">{{ number_format($original_subtotal, 2) }}</span></span>
            </div>

            <div class="summary-row-item">
              <span class="summary-row-label">Item Discounts</span>
              <span class="summary-row-val text-success">- ₹<span id="item_discount_total">0.00</span></span>
            </div>

            <div class="summary-row-item">
              <span class="summary-row-label">Taxable Subtotal</span>
              <span class="summary-row-val">₹<span id="total_taxable">{{ number_format($total_taxable, 2) }}</span></span>
            </div>

            @if(isset($restaurant_gstin) && $restaurant_gstin)
              <div class="summary-row-item">
                <span class="summary-row-label">GST Total ({{ $restaurant_gst_percentage ?? 0 }}%)</span>
                <span class="summary-row-val">₹<span id="total_gst">{{ number_format($total_gst, 2) }}</span></span>
              </div>
            @endif

            <div class="summary-row-item">
              <span class="summary-row-label">Order Discount</span>
              <span class="summary-row-val text-success">- ₹<span id="order_discount_amount">0.00</span></span>
            </div>

            <div class="summary-row-item" id="round_off_item" style="display: none;">
              <span class="summary-row-label">Round Off</span>
              <span class="summary-row-val" id="round_off">₹0.00</span>
            </div>

            <!-- Grand Final Total Box -->
            <div class="summary-grand-box">
              <span class="grand-label-title">Final Total</span>
              <span class="grand-amount-total">₹<span id="final_total">{{ number_format($final_total, 2) }}</span></span>
            </div>

            <!-- Checkout / Order Status -->
            @if(in_array(auth()->user()->role_type, ["Manager", "Cashier", "ADMIN"]))
              <div class="mt-4">
                <label class="pos-form-label">Order Status &amp; Complete</label>
                <select class="pos-form-control" id="order_complete">
                  <option value="PENDING" {{ $order->order_complete == 'PENDING' ? 'selected' : '' }}>⏳ Active / Running in Service</option>
                  <option value="DONE" {{ $order->order_complete == 'DONE' ? 'selected' : '' }}>✅ Done &amp; Checkout Bill</option>
                </select>
              </div>
            @endif

            <div class="mt-4">
              <button type="button" class="btn-terminal-save" id="saveOrderBtn">
                <i class="fa-solid fa-floppy-disk"></i> Save &amp; Update Order
              </button>
            </div>
          </div>

        </div>
      </div>

    </div>

  </div>
</div>

<!-- Luxury Toast Notification -->
<div class="pos-toast" id="toastNotification">
  <i class="fa-solid fa-circle-check text-success"></i>
  <span id="toastMessage">Order updated successfully</span>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
@include('includes.script')

<script>
let newOrderItems = [];
let isGstRegistered = {{ isset($restaurant_gstin) && $restaurant_gstin ? 'true' : 'false' }};
let restaurantGstPercentage = {{ $restaurant_gst_percentage ?? 0 }};
let existingSubtotal = {{ $original_subtotal ?? 0 }};
let existingTaxable = {{ $total_taxable ?? 0 }};
let existingGst = {{ $total_gst ?? 0 }};

function showToast(message, isError = false) {
    let toast = $('#toastNotification');
    toast.find('#toastMessage').text(message);
    
    if (isError) {
        toast.addClass('error');
        toast.find('i').attr('class', 'fa-solid fa-circle-exclamation text-danger');
    } else {
        toast.removeClass('error');
        toast.find('i').attr('class', 'fa-solid fa-circle-check text-success');
    }
    
    toast.addClass('show');
    setTimeout(() => {
        toast.removeClass('show');
    }, 2800);
}

function calculateItemDetails(originalPrice, qty, discountPercent = 0) {
    let discountedPricePerItem = originalPrice - (originalPrice * discountPercent / 100);
    let taxableAmount = discountedPricePerItem * qty;
    let gstAmount = 0;
    let gstRate = 0;
    
    if (isGstRegistered) {
        gstRate = restaurantGstPercentage;
        gstAmount = (taxableAmount * gstRate) / 100;
    }
    
    let totalAmount = taxableAmount + gstAmount;
    
    return {
        discountedPricePerItem: discountedPricePerItem,
        taxableAmount: taxableAmount,
        gstAmount: gstAmount,
        gstRate: gstRate,
        totalAmount: totalAmount,
        itemDiscountAmount: (originalPrice * qty) - taxableAmount
    };
}

function updateSummary() {
    let newOriginalSubtotal = 0;
    let newTaxable = 0;
    let newGst = 0;
    let newItemDiscount = 0;
    let newCount = 0;
    
    newOrderItems.forEach(item => {
        let details = calculateItemDetails(item.price, item.qty, item.itemDiscount || 0);
        newOriginalSubtotal += item.price * item.qty;
        newTaxable += details.taxableAmount;
        newGst += details.gstAmount;
        newItemDiscount += details.itemDiscountAmount;
        newCount += item.qty;
    });
    
    let totalOriginalSubtotal = existingSubtotal + newOriginalSubtotal;
    let totalTaxable = existingTaxable + newTaxable;
    let totalGst = existingGst + newGst;
    let totalItemDiscount = newItemDiscount;
    
    let orderDiscountPercent = parseFloat($('#order_discount').val()) || 0;
    let totalBeforeOrderDiscount = totalTaxable + totalGst;
    let orderDiscountAmount = (totalBeforeOrderDiscount * orderDiscountPercent) / 100;
    let grandTotal = totalBeforeOrderDiscount - orderDiscountAmount;
    let finalTotal = Math.round(grandTotal);
    let roundOff = finalTotal - grandTotal;
    
    $('#original_subtotal').text(totalOriginalSubtotal.toFixed(2));
    $('#item_discount_total').text(totalItemDiscount.toFixed(2));
    $('#total_taxable').text(totalTaxable.toFixed(2));
    if (isGstRegistered) {
        $('#total_gst').text(totalGst.toFixed(2));
    }
    $('#order_discount_amount').text(orderDiscountAmount.toFixed(2));
    
    if (Math.abs(roundOff) > 0.01) {
        $('#round_off_item').show();
        $('#round_off').text(`₹${roundOff.toFixed(2)}`);
    } else {
        $('#round_off_item').hide();
    }
    
    $('#final_total').text(finalTotal.toFixed(2));
    $('#newItemsCountBadge').text(newCount + (newCount === 1 ? ' New Item' : ' New Items'));
}

function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str).replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

function updateNewItemsTable() {
    let tbody = $('#newItemsBody');
    let emptyState = $('#emptyNewItems');
    
    if (newOrderItems.length === 0) {
        tbody.empty();
        emptyState.show();
        updateSummary();
        return;
    }
    
    emptyState.hide();
    tbody.empty();
    
    newOrderItems.forEach((item, index) => {
        let details = calculateItemDetails(item.price, item.qty, item.itemDiscount || 0);
        
        let row = `
            <tr data-index="${index}">
                <td><strong class="text-dark">${escapeHtml(item.name)}</strong></td>
                <td class="text-end">₹${item.price.toFixed(2)}</td>
                <td class="text-center">
                    <input type="number" class="item-disc-input item-discount-input" 
                           data-index="${index}" value="${item.itemDiscount || 0}" 
                           min="0" max="100" step="1">
                    <span class="small text-muted">%</span>
                </td>
                <td class="text-center">
                    <div class="qty-stepper">
                        <button type="button" class="qty-step-btn decrease-qty" data-index="${index}">-</button>
                        <input type="number" class="qty-step-input qty-input" value="${item.qty}" min="1" data-index="${index}">
                        <button type="button" class="qty-step-btn increase-qty" data-index="${index}">+</button>
                    </div>
                </td>
                <td class="text-end">₹${details.discountedPricePerItem.toFixed(2)}</td>
                <td class="text-end">₹${details.taxableAmount.toFixed(2)}</td>`;
        
        if (isGstRegistered) {
            row += `<td class="text-center">${details.gstRate}%</td>
                    <td class="text-end">₹${details.gstAmount.toFixed(2)}</td>`;
        }
        
        row += `<td class="text-end fw-bold text-dark">₹${details.totalAmount.toFixed(2)}</td>
                <td class="text-center">
                    <button type="button" class="btn-remove-row delete-new" data-index="${index}" title="Remove dish">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
              </tr>`;
        tbody.append(row);
    });
    
    updateSummary();
}

// Category and Food Item Filtering Logic
function applyFoodFilters() {
    let selectedCatId = $('.cat-pill-tab.active').data('category-id');
    let vegType = $('#vegFilter').val().toLowerCase().trim();
    let searchKeyword = $('#nameFilter').val().toLowerCase().trim();

    $('.category-pane').each(function() {
        let paneCatId = $(this).data('category-id');
        let showThisCategory = (selectedCatId === 'all') || (String(paneCatId) === String(selectedCatId));

        if (!showThisCategory) {
            $(this).hide();
            return;
        }

        $(this).show();
        let visibleCount = 0;

        $(this).find('.food-card').each(function() {
            let cardType = ($(this).data('type') || '').toLowerCase();
            let cardName = ($(this).data('name') || '').toLowerCase();

            let matchesType = (!vegType) || (cardType === vegType);
            let matchesName = (!searchKeyword) || (cardName.indexOf(searchKeyword) !== -1);

            if (matchesType && matchesName) {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });

        if (visibleCount === 0) {
            $(this).find('.no-dishes-notice').show();
        } else {
            $(this).find('.no-dishes-notice').hide();
        }
    });
}

$(document).ready(function() {
    // Category pill tab click
    $(document).on('click', '.cat-pill-tab', function(e) {
        e.preventDefault();
        $('.cat-pill-tab').removeClass('active');
        $(this).addClass('active');
        applyFoodFilters();
    });

    // Search and Veg Filter inputs
    $('#vegFilter, #nameFilter').on('input change', function() {
        applyFoodFilters();
    });

    // Add new item button
    $(document).on('click', '.add-item-btn', function(e) {
        e.preventDefault();
        let itemId = $(this).data('id');
        let itemName = $(this).data('name');
        let itemPrice = parseFloat($(this).data('price'));
        let itemDiscount = parseFloat($(this).data('discount')) || 0;
        
        let existingItem = newOrderItems.find(i => i.id === itemId);
        
        if (existingItem) {
            existingItem.qty += 1;
            showToast(`${itemName} quantity updated to ${existingItem.qty}`);
        } else {
            newOrderItems.push({
                id: itemId,
                name: itemName,
                price: itemPrice,
                qty: 1,
                itemDiscount: itemDiscount
            });
            showToast(`${itemName} added to order`);
        }
        
        updateNewItemsTable();
    });
    
    // Discount change
    $(document).on('change', '.item-discount-input', function() {
        let index = $(this).data('index');
        let newDiscount = parseFloat($(this).val()) || 0;
        if (newDiscount < 0) newDiscount = 0;
        if (newDiscount > 100) newDiscount = 100;
        
        if (newOrderItems[index]) {
            newOrderItems[index].itemDiscount = newDiscount;
            $(this).val(newDiscount);
            updateNewItemsTable();
            showToast(`Discount set to ${newDiscount}% for ${newOrderItems[index].name}`);
        }
    });
    
    // Increase quantity
    $(document).on('click', '.increase-qty', function() {
        let index = $(this).closest('tr').data('index');
        if (newOrderItems[index]) {
            newOrderItems[index].qty += 1;
            updateNewItemsTable();
        }
    });
    
    // Decrease quantity
    $(document).on('click', '.decrease-qty', function() {
        let index = $(this).closest('tr').data('index');
        if (newOrderItems[index] && newOrderItems[index].qty > 1) {
            newOrderItems[index].qty -= 1;
            updateNewItemsTable();
        } else if (newOrderItems[index] && newOrderItems[index].qty === 1) {
            if (confirm(`Remove ${newOrderItems[index].name} from order?`)) {
                let removedItem = newOrderItems[index];
                newOrderItems.splice(index, 1);
                updateNewItemsTable();
                showToast(`${removedItem.name} removed`, true);
            }
        }
    });
    
    // Quantity input change
    $(document).on('change', '.qty-input', function() {
        let index = $(this).closest('tr').data('index');
        let newQty = parseInt($(this).val());
        if (!isNaN(newQty) && newQty > 0 && newOrderItems[index]) {
            newOrderItems[index].qty = newQty;
            updateNewItemsTable();
        } else if (newOrderItems[index]) {
            $(this).val(newOrderItems[index].qty);
        }
    });
    
    // Delete new item
    $(document).on('click', '.delete-new', function() {
        let index = $(this).closest('tr').data('index');
        let removedItem = newOrderItems[index];
        newOrderItems.splice(index, 1);
        updateNewItemsTable();
        showToast(`${removedItem.name} removed`, true);
    });
    
    // Delete existing item from DB
    $(document).on('click', '.delete-existing', function() {
        if (!confirm('Are you sure you want to remove this active item from order?')) return;
        
        let itemId = $(this).data('id');
        let button = $(this);
        button.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: "{{ route('order.update', $order->id) }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                delete_item_id: itemId
            },
            success: function(res) {
                if (res.success) {
                    showToast('Item removed successfully', false);
                    location.reload();
                } else {
                    showToast(res.message || 'Error removing item', true);
                    button.prop('disabled', false).html('<i class="fa-solid fa-trash"></i>');
                }
            },
            error: function() {
                showToast('Error removing item', true);
                button.prop('disabled', false).html('<i class="fa-solid fa-trash"></i>');
            }
        });
    });
    
    // Save Order Changes
    $('#saveOrderBtn').click(function() {
        let customer_phone = $('#customer_phone').val().trim();
        let order_complete = $('#order_complete').val();
        let payment_method = $('#payment_method').val();
        let amount_paid = $('#amount_paid').val();
        let remarks = $('#remarks').val();
        
        if (customer_phone && !/^[0-9]{10}$/.test(customer_phone)) {
            showToast('Please enter a valid 10-digit phone number', true);
            $('#customer_phone').focus();
            return;
        }
        
        let data = {
            _token: "{{ csrf_token() }}",
            order_complete: order_complete,
            payment_method: payment_method,
            amount_paid: amount_paid,
            customer_phone: customer_phone,
            remarks: remarks,
            is_gst_registered: isGstRegistered,
            gst_percentage: restaurantGstPercentage
        };
        
        if ($('#order_discount').length) {
            data.discount = $('#order_discount').val() || 0;
        }
        
        if (newOrderItems.length > 0) {
            data.order_items = newOrderItems.map(item => ({
                id: item.id,
                name: item.name,
                price: item.price,
                qty: item.qty,
                item_discount: item.itemDiscount || 0
            }));
        }
        
        $(this).prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-2"></i> Saving Changes...');
        
        $.ajax({
            url: "{{ route('order.update', $order->id) }}",
            type: "POST",
            data: data,
            success: function(response) {
                if (response.success) {
                    showToast('Order updated successfully!', false);
                    if (response.redirect_url) {
                        setTimeout(() => { window.location.href = response.redirect_url; }, 800);
                    } else {
                        setTimeout(() => { location.reload(); }, 800);
                    }
                } else {
                    showToast(response.message || 'Error saving order', true);
                    $('#saveOrderBtn').prop('disabled', false).html('<i class="fa-solid fa-floppy-disk"></i> Save &amp; Update Order');
                }
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'An error occurred while saving', true);
                $('#saveOrderBtn').prop('disabled', false).html('<i class="fa-solid fa-floppy-disk"></i> Save &amp; Update Order');
            }
        });
    });
    
    updateSummary();
    applyFoodFilters();
});
</script>
</body>
</html>