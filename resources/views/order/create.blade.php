<!DOCTYPE html>
<html lang="en">
<head>
  <title>Create Order • Bill&Bite POS</title>
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
            <i class="fa-solid fa-cash-register"></i> POS Order Terminal
          </span>
          <h1 class="terminal-main-title">
            @if(isset($table) && $table)
              New Table Order • {{ $table->name }}
            @else
              New Express Takeaway Order
            @endif
          </h1>
        </div>
      </div>

      <div>
        @if(isset($table) && $table)
          <span class="terminal-sub-badge terminal-badge-table">
            <i class="fa-solid fa-chair"></i> Dining Table: {{ $table->name }}
          </span>
        @else
          <span class="terminal-sub-badge terminal-badge-takeaway">
            <i class="fa-solid fa-bag-shopping"></i> Takeaway / Parcel
          </span>
        @endif
      </div>
    </div>

    <!-- Alert Messages -->
    @include('includes.message')

    <div class="row g-4">
      
      <!-- Left Main Terminal Column -->
      <div class="col-lg-8 col-xl-8">
        
        <!-- Customer & Seating Panel -->
        <div class="pos-panel-card">
          <div class="pos-panel-header">
            <h3 class="pos-panel-title">
              <i class="fa-solid fa-user-tag text-primary"></i> Customer &amp; Table Info
            </h3>
            <span class="text-muted small">Required for bill dispatch</span>
          </div>
          <div class="pos-panel-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="pos-form-label">Customer Name <span class="text-danger">*</span></label>
                <input type="text" class="pos-form-control" id="customer_name" placeholder="Enter guest or customer name" required autofocus>
              </div>
              <div class="col-md-6">
                <label class="pos-form-label">Customer Mobile <span class="text-muted fw-normal">(Optional)</span></label>
                <input type="tel" class="pos-form-control" id="customer_phone" placeholder="e.g. 9876543210">
              </div>
              <input type="hidden" id="table_id" value="{{ isset($table) && $table ? $table->id : '' }}">
            </div>
          </div>
        </div>

        <!-- Menu Browser Toolbar -->
        <div class="menu-toolbar-panel">
          <div class="menu-search-wrapper">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="nameFilter" class="menu-search-input" placeholder="Search dishes &amp; food items by name...">
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

        <!-- Selected Order Items Cart Section -->
        <div class="pos-panel-card">
          <div class="pos-panel-header">
            <h3 class="pos-panel-title">
              <i class="fa-solid fa-cart-shopping text-success"></i> Selected Items Cart
            </h3>
            <span id="cartCountBadge" class="badge bg-dark text-white font-weight-bold">0 Items</span>
          </div>

          <div class="pos-panel-body p-0">
            <div class="pos-table-wrap">
              <table id="orderListTable" class="pos-order-table">
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
                <tbody id="orderItemsBody">
                  <!-- Injected via JavaScript -->
                </tbody>
              </table>
            </div>

            <div id="emptyOrderState" class="terminal-empty-items">
              <i class="fa-solid fa-cart-arrow-down"></i>
              <h5>Your Order Cart is Empty</h5>
              <p>Browse the menu above and click "+ Add Item" to populate the bill.</p>
            </div>
          </div>
        </div>

      </div>

      <!-- Right Sticky Order Summary Column -->
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
              <i class="fa-solid fa-receipt text-primary"></i> Bill Calculation
            </h4>

            <div class="summary-row-item">
              <span class="summary-row-label">Original Subtotal</span>
              <span class="summary-row-val">₹<span id="original_subtotal">0.00</span></span>
            </div>

            <div class="summary-row-item">
              <span class="summary-row-label">Item Discounts</span>
              <span class="summary-row-val text-success">- ₹<span id="item_discount_total">0.00</span></span>
            </div>

            <div class="summary-row-item">
              <span class="summary-row-label">Taxable Subtotal</span>
              <span class="summary-row-val">₹<span id="total_taxable">0.00</span></span>
            </div>

            @if(isset($restaurant_gstin) && $restaurant_gstin)
              <div class="summary-row-item">
                <span class="summary-row-label">GST Total ({{ $restaurant_gst_percentage ?? 0 }}%)</span>
                <span class="summary-row-val">₹<span id="total_gst">0.00</span></span>
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
              <span class="grand-amount-total">₹<span id="final_total">0.00</span></span>
            </div>

            <div class="mt-4">
              <button type="button" class="btn-terminal-save" id="saveOrderBtn">
                <i class="fa-solid fa-circle-check"></i>
                @if(!isset($table) || !$table) Checkout &amp; Pay @else Save &amp; Fire KOT @endif
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
  <span id="toastMessage">Action completed</span>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
@include('includes.script')

<script>
let orderItems = [];
let isGstRegistered = {{ isset($restaurant_gstin) && $restaurant_gstin ? 'true' : 'false' }};
let restaurantGstPercentage = {{ $restaurant_gst_percentage ?? 0 }};

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
    let originalSubtotal = 0;
    let totalTaxable = 0;
    let totalGst = 0;
    let totalItemDiscount = 0;
    let totalCount = 0;
    
    orderItems.forEach(item => {
        let originalAmount = item.price * item.qty;
        let details = calculateItemDetails(item.price, item.qty, item.itemDiscount || 0);
        
        originalSubtotal += originalAmount;
        totalTaxable += details.taxableAmount;
        totalGst += details.gstAmount;
        totalItemDiscount += details.itemDiscountAmount;
        totalCount += item.qty;
    });
    
    let orderDiscountPercent = parseFloat($('#order_discount').val()) || 0;
    let totalBeforeOrderDiscount = totalTaxable + totalGst;
    let orderDiscountAmount = (totalBeforeOrderDiscount * orderDiscountPercent) / 100;
    let grandTotal = totalBeforeOrderDiscount - orderDiscountAmount;
    let finalTotal = Math.round(grandTotal);
    let roundOff = finalTotal - grandTotal;
    
    $('#original_subtotal').text(originalSubtotal.toFixed(2));
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
    $('#cartCountBadge').text(totalCount + (totalCount === 1 ? ' Item' : ' Items'));
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

function renderOrderTable() {
    let tbody = $('#orderItemsBody');
    let emptyState = $('#emptyOrderState');
    tbody.empty();
    
    if (orderItems.length === 0) {
        emptyState.show();
        updateSummary();
        return;
    }
    
    emptyState.hide();
    
    orderItems.forEach((item, index) => {
        let details = calculateItemDetails(item.price, item.qty, item.itemDiscount || 0);
        
        let row = `
            <tr data-index="${index}">
                <td>
                    <strong class="text-dark">${escapeHtml(item.name)}</strong>
                </td>
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
                        <input type="number" class="qty-step-input qty-input" 
                               data-index="${index}" value="${item.qty}" min="1">
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
                    <button type="button" class="btn-remove-row remove-btn" data-index="${index}" title="Remove dish">
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

    // Add item button
    $(document).on('click', '.add-item-btn', function(e) {
        e.preventDefault();
        let itemId = $(this).data('id');
        let itemName = $(this).data('name');
        let itemPrice = parseFloat($(this).data('price'));
        let itemDiscount = parseFloat($(this).data('discount')) || 0;
        
        let existingItem = orderItems.find(i => i.id === itemId);
        
        if (existingItem) {
            existingItem.qty += 1;
            showToast(`${itemName} quantity updated to ${existingItem.qty}`);
        } else {
            orderItems.push({
                id: itemId,
                name: itemName,
                price: itemPrice,
                qty: 1,
                itemDiscount: itemDiscount
            });
            showToast(`${itemName} added to order`);
        }
        
        renderOrderTable();
    });
    
    // Item discount modification
    $(document).on('change', '.item-discount-input', function() {
        let index = $(this).data('index');
        let newDiscount = parseFloat($(this).val()) || 0;
        if (newDiscount < 0) newDiscount = 0;
        if (newDiscount > 100) newDiscount = 100;
        
        if (orderItems[index]) {
            orderItems[index].itemDiscount = newDiscount;
            $(this).val(newDiscount);
            renderOrderTable();
            showToast(`Discount set to ${newDiscount}% for ${orderItems[index].name}`);
        }
    });
    
    // Increase quantity
    $(document).on('click', '.increase-qty', function() {
        let index = $(this).data('index');
        if (orderItems[index]) {
            orderItems[index].qty += 1;
            renderOrderTable();
        }
    });
    
    // Decrease quantity
    $(document).on('click', '.decrease-qty', function() {
        let index = $(this).data('index');
        if (orderItems[index] && orderItems[index].qty > 1) {
            orderItems[index].qty -= 1;
            renderOrderTable();
        } else if (orderItems[index] && orderItems[index].qty === 1) {
            if (confirm(`Remove ${orderItems[index].name} from order?`)) {
                let removedItem = orderItems[index];
                orderItems.splice(index, 1);
                renderOrderTable();
                showToast(`${removedItem.name} removed`, true);
            }
        }
    });
    
    // Quantity direct input
    $(document).on('change', '.qty-input', function() {
        let index = $(this).data('index');
        let newQty = parseInt($(this).val());
        if (!isNaN(newQty) && newQty > 0 && orderItems[index]) {
            orderItems[index].qty = newQty;
            renderOrderTable();
        } else if (orderItems[index]) {
            $(this).val(orderItems[index].qty);
        }
    });
    
    // Remove item
    $(document).on('click', '.remove-btn', function() {
        let index = $(this).data('index');
        let removedItem = orderItems[index];
        orderItems.splice(index, 1);
        renderOrderTable();
        showToast(`${removedItem.name} removed from order`, true);
    });
    
    // Save Order
    $('#saveOrderBtn').click(function() {
        let customer_name = $('#customer_name').val().trim();
        let customer_phone = $('#customer_phone').val().trim();
        let table_id = $('#table_id').val();
        let orderDiscount = $('#order_discount').val() || 0;
        let order_complete = $('#order_complete').length ? $('#order_complete').val() : null;
        let payment_method = $('#payment_method').length ? $('#payment_method').val() : null;
        let remarks = $('#remarks').val() || null;
        
        if (orderItems.length === 0) {
            showToast('Please add items to the order first', true);
            return;
        }
        
        if (customer_name === '') {
            showToast('Please enter customer name', true);
            $('#customer_name').focus();
            return;
        }
        
        let orderItemsData = orderItems.map(item => ({
            id: item.id,
            name: item.name,
            price: item.price,
            qty: item.qty,
            item_discount: item.itemDiscount || 0
        }));
        
        $(this).prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-2"></i> Saving Order...');
        
        $.ajax({
            url: "{{ route('order.save') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                customer_name: customer_name,
                customer_phone: customer_phone,
                table_id: table_id,
                discount: orderDiscount,
                order_items: orderItemsData,
                order_complete: order_complete,
                payment_method: payment_method,
                remarks: remarks,
                is_gst_registered: isGstRegistered,
                gst_percentage: restaurantGstPercentage
            },
            success: function(response) {
                if (response.success) {
                    showToast('Order saved successfully!', false);
                    if (response.redirect_url) {
                        setTimeout(() => { window.location.href = response.redirect_url; }, 800);
                    } else if (response.invoice_url) {
                        setTimeout(() => { window.location.href = response.invoice_url; }, 800);
                    } else {
                        setTimeout(() => { window.location.href = "{{ route('order.management.dashboard') }}"; }, 800);
                    }
                } else {
                    showToast(response.message || 'Error saving order', true);
                    $('#saveOrderBtn').prop('disabled', false).html('<i class="fa-solid fa-circle-check"></i> Save Order');
                }
            },
            error: function(xhr) {
                let errorMsg = xhr.responseJSON?.message || 'An error occurred while saving';
                showToast(errorMsg, true);
                $('#saveOrderBtn').prop('disabled', false).html('<i class="fa-solid fa-circle-check"></i> Save Order');
            }
        });
    });
    
    // Initial setup
    renderOrderTable();
    applyFoodFilters();
});
</script>
</body>
</html>