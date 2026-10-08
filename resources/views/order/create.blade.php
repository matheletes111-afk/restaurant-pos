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
                <input type="tel" class="pos-form-control" id="customer_phone" placeholder="e.g. 9876543210" inputmode="numeric" onkeydown="if(['e','E','+','-','.'].includes(event.key)) event.preventDefault();" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
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
              @if(isset($restaurant_addons) && $restaurant_addons->count() > 0)
                <button type="button" class="cat-pill-tab tab-addons" data-category-id="addons">
                  <i class="fa-solid fa-puzzle-piece me-1 text-warning"></i> Add-ons
                  <span class="badge bg-warning text-dark ms-1" style="font-size: 0.72rem;">{{ $restaurant_addons->count() }}</span>
                </button>
              @endif
            </div>

            <!-- All Dishes Unified Grid -->
            <div id="dishesContainer">
              <div class="food-items-grid" id="foodItemsGrid">
                @php $hasAnyDishes = false; @endphp
                
                @foreach($categories as $category)
                  @foreach($category->subcategories as $item)
                    @php
                      $hasAnyDishes = true;
                      $isNonVeg = (strtolower($item->food_type ?? 'veg') == 'non-veg');
                      $discount = (float)($item->discount_percentage ?? 0);
                      $finalPrice = $discount > 0 ? ($item->price - ($item->price * $discount / 100)) : $item->price;
                      
                      $mappedAddons = ($item->addons) ? $item->addons->where('status', '!=', 'D')->where('status', '!=', 'I')->values() : collect([]);
                      $availableDishAddons = $mappedAddons->isNotEmpty() ? $mappedAddons : ($restaurant_addons ?? collect([]));
                      $addonsCount = $availableDishAddons->count();
                    @endphp
                    <div class="food-menu-card food-card" 
                         data-category-id="{{ $category->id }}"
                         data-category-name="{{ strtolower($category->name) }}"
                         data-type="{{ $isNonVeg ? 'non-veg' : 'veg' }}" 
                         data-name="{{ strtolower($item->name) }}">
                      
                      <div>
                        <div class="food-badge-strip">
                          <span class="badge-food-type {{ $isNonVeg ? 'badge-food-nonveg' : 'badge-food-veg' }}">
                            <i class="fa-solid {{ $isNonVeg ? 'fa-drumstick-bite' : 'fa-leaf' }}"></i>
                            {{ $item->food_type ?? 'Veg' }}
                          </span>
                          @if($addonsCount > 0)
                            <span class="badge-addon-pill" title="{{ $addonsCount }} Add-ons available">
                              <i class="fa-solid fa-plus-circle"></i> {{ $addonsCount }} Add-on{{ $addonsCount > 1 ? 's' : '' }}
                            </span>
                          @endif
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

                        @if($addonsCount > 0)
                          <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn-add-food add-item-btn flex-grow-1"
                                    data-id="{{ $item->id }}"
                                    data-name="{{ $item->name }}"
                                    data-price="{{ $item->price }}"
                                    data-discount="{{ $discount }}"
                                    data-addons='@json($availableDishAddons)'>
                              <i class="fa-solid fa-plus"></i> Add
                            </button>
                            <button type="button" class="btn-customize-addons-pos open-addon-modal-btn"
                                    data-id="{{ $item->id }}"
                                    data-name="{{ $item->name }}"
                                    data-price="{{ $item->price }}"
                                    data-discount="{{ $discount }}"
                                    data-addons='@json($availableDishAddons)'
                                    title="Customize with Add-ons">
                              <i class="fa-solid fa-sliders"></i> Add-ons
                            </button>
                          </div>
                        @else
                          <button type="button" class="btn-add-food add-item-btn"
                                  data-id="{{ $item->id }}"
                                  data-name="{{ $item->name }}"
                                  data-price="{{ $item->price }}"
                                  data-discount="{{ $discount }}"
                                  data-addons='@json($availableDishAddons)'>
                            <i class="fa-solid fa-plus"></i> Add Item
                          </button>
                        @endif
                      </div>
                    </div>
                  @endforeach
                @endforeach

                @if(isset($restaurant_addons) && $restaurant_addons->count() > 0)
                  @foreach($restaurant_addons as $addon)
                    @php
                      $hasAnyDishes = true;
                      $addonPrice = floatval($addon->price ?? 0);
                      $addonFoodType = strtolower($addon->food_type ?? 'veg');
                      $isNonVeg = ($addonFoodType === 'non-veg' || $addonFoodType === 'nonveg');
                    @endphp
                    <div class="food-menu-card food-card addon-card"
                         data-category-id="addons"
                         data-category-name="addons"
                         data-type="{{ $isNonVeg ? 'non-veg' : 'veg' }}"
                         data-name="{{ strtolower($addon->name) }}"
                         data-is-addon="1">
                      <div>
                        <div class="food-badge-strip">
                          <span class="badge-food-type {{ $isNonVeg ? 'badge-food-nonveg' : 'badge-food-veg' }}">
                            <i class="fa-solid {{ $isNonVeg ? 'fa-drumstick-bite' : 'fa-leaf' }}"></i>
                            {{ $addon->food_type ?? 'Veg' }}
                          </span>
                          <span class="badge-addon-pill" style="background: #fff7ed; color: #ea580c; border: 1px solid #fed7aa;">
                            <i class="fa-solid fa-puzzle-piece"></i> Standalone Add-on
                          </span>
                        </div>
                        <h4 class="food-item-name">{{ $addon->name }}</h4>
                      </div>
                      <div>
                        <div class="food-pricing-row">
                          <span class="price-final">₹{{ number_format($addonPrice, 2) }}</span>
                          <span class="small text-muted">/ portion</span>
                        </div>
                        <button type="button" class="btn-add-food btn-add-addon-direct add-addon-direct-btn"
                                data-id="{{ $addon->id }}"
                                data-name="{{ $addon->name }}"
                                data-price="{{ $addonPrice }}"
                                data-food-type="{{ $addon->food_type ?? 'VEG' }}">
                          <i class="fa-solid fa-plus"></i> Add Add-on
                        </button>
                      </div>
                    </div>
                  @endforeach
                @endif
              </div>

              <!-- No Matching Dishes Notice -->
              <div id="noDishesNotice" class="text-center py-5 text-muted" style="display: {{ $hasAnyDishes ? 'none' : 'block' }};">
                <i class="fa-solid fa-utensils fa-3x mb-3 text-muted opacity-50"></i>
                <h5 class="fw-bold text-dark">No Dishes Found</h5>
                <p class="small text-muted mb-0">No menu items match your search or filter selection.</p>
              </div>
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

            <!-- Order Status & Action Mode -->
            @if(!isset($table) || !$table)
              <input type="hidden" id="order_complete" value="DONE">
            @else
              <div class="mt-4">
                <label class="pos-form-label d-flex align-items-center justify-content-between">
                  <span><i class="fa-solid fa-list-check me-1 text-primary"></i> Order Status &amp; Action</span>
                  <span class="badge bg-light text-dark font-weight-bold" id="statusBadgeTag">Running Service</span>
                </label>
                <select class="pos-form-control" id="order_complete">
                  <option value="PENDING" selected>⏳ Active / Running in Service</option>
                  <option value="DONE">✅ Done &amp; Checkout Bill</option>
                </select>
              </div>
            @endif

            <!-- Instant Checkout Split Payment Box -->
            <div class="checkout-payment-box active-mode" id="checkoutPaymentSection" style="display: {{ (!isset($table) || !$table) ? 'block' : 'none' }};">
              <div class="payment-box-header">
                <div class="payment-box-title">
                  <i class="fa-solid fa-cash-register text-success"></i> Settlement Mode
                </div>
                <span class="payment-badge-status" id="paymentStatusBadge">Full Payment</span>
              </div>

              <!-- Quick Split Helper Pills -->
              <div class="payment-quick-pills">
                <button type="button" class="btn-quick-split active" id="btnAllCash">
                  <i class="fa-solid fa-money-bill-wave text-success"></i> All Cash
                </button>
                <button type="button" class="btn-quick-split" id="btnAllUpi">
                  <i class="fa-solid fa-qrcode text-primary"></i> All UPI
                </button>
                <button type="button" class="btn-quick-split" id="btnSplitFifty">
                  <i class="fa-solid fa-arrows-split-up-and-left text-warning"></i> 50/50 Split
                </button>
              </div>

              <!-- Payment Inputs: Cash & UPI -->
              <div class="pos-pay-grid">
                <div class="pos-pay-field">
                  <label class="pos-pay-label" for="cash_payment_amount">
                    <span class="method-tag text-success"><i class="fa-solid fa-money-bill-wave"></i> Cash (₹)</span>
                  </label>
                  <div class="pos-pay-input-wrap">
                    <span class="pay-currency-prefix">₹</span>
                    <input type="number" step="any" min="0" class="pos-pay-input cash-focus" id="cash_payment_amount" placeholder="0.00" value="0.00" inputmode="decimal" onkeydown="if(['e','E','+','-'].includes(event.key)) event.preventDefault();">
                  </div>
                </div>

                <div class="pos-pay-field">
                  <label class="pos-pay-label" for="upi_payment_amount">
                    <span class="method-tag text-primary"><i class="fa-solid fa-qrcode"></i> UPI (₹)</span>
                  </label>
                  <div class="pos-pay-input-wrap">
                    <span class="pay-currency-prefix">₹</span>
                    <input type="number" step="any" min="0" class="pos-pay-input upi-focus" id="upi_payment_amount" placeholder="0.00" value="0.00" inputmode="decimal" onkeydown="if(['e','E','+','-'].includes(event.key)) event.preventDefault();">
                  </div>
                </div>
              </div>

              <div class="payment-split-summary">
                <span>Paying: <strong id="totalPayingText">₹0.00</strong></span>
                <span id="balanceDueText">Due: <strong>₹0.00</strong></span>
              </div>
            </div>

            <div class="mt-4">
              <button type="button" class="btn-terminal-save" id="saveOrderBtn">
                @if(!isset($table) || !$table)
                  <i class="fa-solid fa-circle-check"></i> Done &amp; Checkout Bill
                @else
                  <i class="fa-solid fa-fire"></i> Save &amp; Fire KOT
                @endif
              </button>
            </div>
          </div>

        </div>
      </div>

    </div>

  </div>
</div>

<!-- Dish Add-on Customization Modal -->
<div class="pos-addon-modal-backdrop" id="addonModalBackdrop" style="display: none;">
  <div class="pos-addon-modal-box">
    <div class="pos-addon-modal-header">
      <div>
        <h4 class="pos-addon-modal-title" id="addonModalDishName">Customize Dish</h4>
        <div class="pos-addon-modal-subtitle">Base Dish Price: ₹<span id="addonModalBasePrice">0.00</span></div>
      </div>
      <button type="button" class="btn-close-addon-modal" id="closeAddonModalBtn"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="pos-addon-modal-body" id="addonModalBody">
      <!-- Dynamically rendered add-on options -->
    </div>
    <div class="pos-addon-modal-footer">
      <div class="addon-modal-total-preview">
        <span>Unit Price (Dish + Add-ons):</span>
        <strong id="addonModalFinalItemTotal">₹0.00</strong>
      </div>
      <button type="button" class="btn-confirm-addons" id="btnConfirmAddons">
        <i class="fa-solid fa-check"></i> Add to Order Cart
      </button>
    </div>
  </div>
</div>

<!-- Map Standalone Add-on to Dish in Cart Modal -->
<div class="pos-addon-modal-backdrop" id="mapAddonModalBackdrop" style="display: none;">
  <div class="pos-addon-modal-box" style="max-width: 480px;">
    <div class="pos-addon-modal-header" style="background: #0f172a; color: #fff;">
      <div>
        <h4 class="pos-addon-modal-title text-white" id="mapAddonModalTitle">Map Add-on to Dish</h4>
        <div class="pos-addon-modal-subtitle text-white-50" id="mapAddonModalSubtitle">Select a dish from your current cart</div>
      </div>
      <button type="button" class="btn-close-addon-modal text-white" id="closeMapAddonModalBtn"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="pos-addon-modal-body" style="padding: 16px;">
      <div class="p-3 mb-3 rounded-3" style="background: #fff8f5; border: 1px solid #fed7aa;">
        <div class="d-flex align-items-center justify-content-between">
          <strong class="text-dark" id="mapAddonName" style="font-size: 1rem;">Add-on Name</strong>
          <span class="badge bg-warning text-dark font-monospace fw-bold fs-6" id="mapAddonRate">+₹0.00</span>
        </div>
      </div>
      <label class="fw-bold small text-muted text-uppercase mb-2" style="letter-spacing: 0.05em;">Attach to which dish in cart?</label>
      <div class="d-flex flex-column gap-2" id="mapAddonDishesList">
        <!-- Injected via JS -->
      </div>
    </div>
    <div class="pos-addon-modal-footer">
      <button type="button" class="btn btn-secondary w-100 fw-bold" id="cancelMapAddonBtn" style="border-radius: 8px;">
        Cancel
      </button>
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
let currentCustomizingDish = null;
window.POS_RESTAURANT_ADDONS = @json($restaurant_addons ?? []);

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

function calculateItemDetails(item) {
    let basePrice = parseFloat(item.price) || 0;
    let qty = parseInt(item.qty) || 1;
    let discountPercent = parseFloat(item.itemDiscount || 0) || 0;
    let isStandaloneAddon = !!item.is_addon;
    let addonsCost = 0;
    if (!isStandaloneAddon && item.addons && Array.isArray(item.addons)) {
        item.addons.forEach(a => {
            let p = parseFloat(a.price) || 0;
            let q = parseInt(a.qty || a.quantity) || 1;
            addonsCost += (p * q);
        });
    }
    
    let discountedPricePerItem = basePrice - (basePrice * discountPercent / 100);
    let taxableAmount = isStandaloneAddon ? (discountedPricePerItem * qty) : ((discountedPricePerItem * qty) + addonsCost);
    let originalAmount = isStandaloneAddon ? (basePrice * qty) : ((basePrice * qty) + addonsCost);
    let itemDiscountAmount = (basePrice * discountPercent / 100) * qty;

    let gstRate = isGstRegistered ? restaurantGstPercentage : 0;
    let gstAmount = (taxableAmount * gstRate) / 100;
    let totalAmount = taxableAmount + gstAmount;
    
    return {
        basePrice: basePrice,
        discountedPricePerItem: discountedPricePerItem,
        taxableAmount: taxableAmount,
        originalAmount: originalAmount,
        gstAmount: gstAmount,
        gstRate: gstRate,
        totalAmount: totalAmount,
        itemDiscountAmount: itemDiscountAmount,
        addonsCost: addonsCost
    };
}

function getItemEffectiveUnitPrice(item) {
    return parseFloat(item.price) || 0;
}

function updateSummary() {
    let originalSubtotal = 0;
    let totalTaxable = 0;
    let totalGst = 0;
    let totalItemDiscount = 0;
    let totalCount = 0;
    
    orderItems.forEach(item => {
        let details = calculateItemDetails(item);
        
        originalSubtotal += details.originalAmount;
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

    // Automatically synchronize payment display without overwriting user custom input
    syncPaymentSplit('total_change');
}

let userEditedSplit = false;

function syncPaymentSplit(triggerSource = 'none') {
    let finalTotal = parseFloat($('#final_total').text()) || 0;
    let cashInput = $('#cash_payment_amount');
    let upiInput = $('#upi_payment_amount');
    
    let isCheckoutActive = $('#order_complete').val() === 'DONE';
    if (!isCheckoutActive) {
        return;
    }

    // On total change, if user hasn't typed custom amounts, default Cash to the new final total
    if (triggerSource === 'total_change') {
        if (!userEditedSplit) {
            cashInput.val(finalTotal.toFixed(2));
            upiInput.val('0.00');
        } else {
            let cashVal = parseFloat(cashInput.val()) || 0;
            let remaining = Math.max(0, finalTotal - cashVal);
            upiInput.val(remaining.toFixed(2));
        }
    }

    let cashVal = parseFloat(cashInput.val()) || 0;
    let upiVal = parseFloat(upiInput.val()) || 0;
    let totalPaying = cashVal + upiVal;
    let balanceDue = finalTotal - totalPaying;

    $('#totalPayingText').text(`₹${totalPaying.toFixed(2)}`);

    let badge = $('#paymentStatusBadge');
    if (Math.abs(balanceDue) < 0.01 && totalPaying > 0) {
        $('#balanceDueText').html('Due: <strong class="text-success">₹0.00</strong>');
        badge.attr('class', 'payment-badge-status').text('Settled Full');
    } else if (balanceDue > 0) {
        $('#balanceDueText').html(`Due: <strong class="text-warning">₹${balanceDue.toFixed(2)}</strong>`);
        badge.attr('class', 'payment-badge-status partial').text(`Partial (₹${balanceDue.toFixed(2)} due)`);
    } else if (totalPaying === 0) {
        $('#balanceDueText').html(`Due: <strong class="text-warning">₹${finalTotal.toFixed(2)}</strong>`);
        badge.attr('class', 'payment-badge-status partial').text(`Due ₹${finalTotal.toFixed(2)}`);
    } else {
        let change = Math.abs(balanceDue);
        $('#balanceDueText').html(`Change: <strong class="text-primary">₹${change.toFixed(2)}</strong>`);
        badge.attr('class', 'payment-badge-status').text(`Change ₹${change.toFixed(2)}`);
    }

    // Update quick pill highlights
    $('.btn-quick-split').removeClass('active');
    if (Math.abs(cashVal - finalTotal) < 0.01 && upiVal === 0 && finalTotal > 0) {
        $('#btnAllCash').addClass('active');
    } else if (Math.abs(upiVal - finalTotal) < 0.01 && cashVal === 0 && finalTotal > 0) {
        $('#btnAllUpi').addClass('active');
    } else if (Math.abs(cashVal - upiVal) < 1.0 && cashVal > 0 && Math.abs(totalPaying - finalTotal) < 0.01) {
        $('#btnSplitFifty').addClass('active');
    }
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
        let isStandaloneAddon = !!item.is_addon;
        let details = calculateItemDetails(item);
        let hasAddons = !isStandaloneAddon && item.addons && Array.isArray(item.addons) && item.addons.length > 0;
        
        let addonsHtml = '';
        if (hasAddons) {
            let addonRows = item.addons.map((addon, aIdx) => {
                let aPrice = parseFloat(addon.price) || 0;
                let aQty = parseInt(addon.qty || addon.quantity) || 1;
                let aTotal = aPrice * aQty;
                let isNonVeg = String(addon.food_type || '').toLowerCase() === 'non-veg';
                
                return `
                    <div class="pos-cart-addon-row">
                        <div class="addon-info">
                            <span class="addon-dot ${isNonVeg ? 'nonveg' : 'veg'}"></span>
                            <span class="addon-name" title="${escapeHtml(addon.name)}">${escapeHtml(addon.name)}</span>
                            <span class="addon-unit-rate">₹${aPrice.toFixed(2)}</span>
                        </div>
                        <div class="addon-controls">
                            <div class="addon-stepper">
                                <button type="button" class="addon-step-btn dec-cart-addon-qty" data-item-idx="${index}" data-addon-idx="${aIdx}" title="Decrease">-</button>
                                <span class="addon-step-qty">${aQty}</span>
                                <button type="button" class="addon-step-btn inc-cart-addon-qty" data-item-idx="${index}" data-addon-idx="${aIdx}" title="Increase">+</button>
                            </div>
                            <span class="addon-subtotal">+₹${aTotal.toFixed(2)}</span>
                            <button type="button" class="btn-delete-addon remove-cart-addon" data-item-idx="${index}" data-addon-idx="${aIdx}" title="Remove">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </div>
                `;
            }).join('');

            addonsHtml = `
                <div class="pos-cart-addons-box">
                    <div class="pos-cart-addons-title">
                        <span><i class="fa-solid fa-puzzle-piece text-primary"></i> Add-ons (${item.addons.length})</span>
                        <button type="button" class="btn-clear-cart-addons clear-all-addons-btn" data-item-idx="${index}" title="Remove all add-ons">Clear all</button>
                    </div>
                    <div class="pos-cart-addons-list">
                        ${addonRows}
                    </div>
                </div>
            `;
        }
        
        let itemTitleHtml = isStandaloneAddon
            ? `<div class="d-flex align-items-center gap-2">
                 <span class="addon-dot ${String(item.food_type || '').toLowerCase() === 'non-veg' ? 'nonveg' : 'veg'}"></span>
                 <strong class="text-dark" style="font-size: 0.95rem;">${escapeHtml(item.name)}</strong>
                 <span class="badge bg-warning text-dark border ms-1" style="font-size: 0.68rem; font-weight: 800;">Add-on</span>
               </div>`
            : `<div class="d-flex align-items-center justify-content-between">
                 <strong class="text-dark" style="font-size: 0.95rem;">${escapeHtml(item.name)}</strong>
                 <button type="button" class="btn-cart-customize-item cart-customize-btn" data-index="${index}" title="Edit add-ons">
                     <i class="fa-solid fa-sliders"></i> ${hasAddons ? 'Edit Add-ons' : '+ Add-ons'}
                 </button>
               </div>`;

        let row = `
            <tr data-index="${index}">
                <td>
                    ${itemTitleHtml}
                    ${addonsHtml}
                </td>
                <td class="text-end">
                    <div class="fw-bold text-dark">₹${details.basePrice.toFixed(2)}</div>
                    ${(hasAddons && !isStandaloneAddon) ? `<div class="small text-muted" style="font-size: 0.72rem;">+₹${details.addonsCost.toFixed(2)} Add-ons</div>` : ''}
                </td>
                <td class="text-center">
                    <input type="number" class="item-disc-input item-discount-input" 
                           data-index="${index}" value="${item.itemDiscount || 0}" 
                           min="0" max="100" step="any" inputmode="decimal" onkeydown="if(['e','E','+','-'].includes(event.key)) event.preventDefault();">
                    <span class="small text-muted">%</span>
                </td>
                <td class="text-center">
                    <div class="qty-stepper">
                        <button type="button" class="qty-step-btn decrease-qty" data-index="${index}">-</button>
                        <input type="number" class="qty-step-input qty-input" 
                               data-index="${index}" value="${item.qty}" min="1" step="1" inputmode="numeric" onkeydown="if(['e','E','+','-','.'].includes(event.key)) event.preventDefault();" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
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
    let activeTab = $('.cat-pill-tab.active');
    let selectedCatId = String(activeTab.attr('data-category-id') || 'all').trim();
    let vegType = String($('#vegFilter').val() || '').toLowerCase().trim();
    let searchKeyword = String($('#nameFilter').val() || '').toLowerCase().trim();

    let visibleCount = 0;

    $('.food-card').each(function() {
        let cardCatId = String($(this).attr('data-category-id') || '').trim();
        let cardType = String($(this).attr('data-type') || '').toLowerCase().trim();
        let cardName = String($(this).attr('data-name') || '').toLowerCase().trim();

        let matchesCat = (selectedCatId === 'all') || (cardCatId === selectedCatId);
        let matchesType = (!vegType) || (cardType === vegType);
        let matchesName = (!searchKeyword) || (cardName.indexOf(searchKeyword) !== -1);

        if (matchesCat && matchesType && matchesName) {
            $(this).css('display', 'flex');
            visibleCount++;
        } else {
            $(this).css('display', 'none');
        }
    });

    if (visibleCount === 0) {
        $('#noDishesNotice').show();
    } else {
        $('#noDishesNotice').hide();
    }
}

// Update Modal Total Calculation
function updateAddonModalTotal() {
    if (!currentCustomizingDish) return;
    let basePrice = parseFloat(currentCustomizingDish.price) || 0;
    let addonsTotal = 0;
    
    currentCustomizingDish.availableAddons.forEach(addon => {
        let aId = String(addon.id);
        let count = currentCustomizingDish.selectedCounts[aId] || 0;
        let price = parseFloat(addon.price) || 0;
        addonsTotal += (price * count);
    });
    
    let finalItemTotal = basePrice + addonsTotal;
    $('#addonModalFinalItemTotal').text(`₹${finalItemTotal.toFixed(2)}`);
}

// Open Addon Customization Modal
function openAddonModal(dishData) {
    let availableAddons = [];
    let rawAddons = dishData.addons;
    if (typeof rawAddons === 'string') {
        try { 
            let decoded = rawAddons;
            if (decoded.includes('&quot;') || decoded.includes('&#039;') || decoded.includes('&amp;')) {
                let txt = document.createElement('textarea');
                txt.innerHTML = decoded;
                decoded = txt.value;
            }
            availableAddons = JSON.parse(decoded); 
        } catch(e) { 
            availableAddons = []; 
        }
    } else if (Array.isArray(rawAddons)) {
        availableAddons = rawAddons;
    } else if (typeof rawAddons === 'object' && rawAddons !== null) {
        availableAddons = Object.values(rawAddons);
    }

    // Fallback to global restaurant addons if dish-specific list is empty
    if ((!availableAddons || availableAddons.length === 0) && window.POS_RESTAURANT_ADDONS && window.POS_RESTAURANT_ADDONS.length > 0) {
        availableAddons = window.POS_RESTAURANT_ADDONS;
    }

    // Filter only active addons (status !== 'D' and status !== 'I')
    if (Array.isArray(availableAddons)) {
        availableAddons = availableAddons.filter(a => a && a.status !== 'D' && a.status !== 'I');
    } else {
        availableAddons = [];
    }

    if (availableAddons.length === 0) {
        showToast('No active add-ons found. Please create or activate add-ons in Add-on Master.', true);
        return;
    }

    let prefillCounts = {};
    if (dishData.prefillAddons && Array.isArray(dishData.prefillAddons)) {
        dishData.prefillAddons.forEach(pa => {
            let paId = String(pa.id);
            prefillCounts[paId] = parseInt(pa.qty || pa.quantity) || 1;
        });
    }

    currentCustomizingDish = {
        id: dishData.id,
        name: dishData.name,
        price: parseFloat(dishData.price) || 0,
        discount: parseFloat(dishData.discount) || 0,
        cartItemIndex: (dishData.cartItemIndex !== undefined) ? dishData.cartItemIndex : null,
        availableAddons: availableAddons,
        selectedCounts: {}
    };

    availableAddons.forEach(a => {
        let aId = String(a.id);
        currentCustomizingDish.selectedCounts[aId] = prefillCounts[aId] || 0;
    });

    $('#addonModalDishName').text(dishData.name);
    $('#addonModalBasePrice').text(currentCustomizingDish.price.toFixed(2));

    let modalBody = $('#addonModalBody');
    modalBody.empty();

    availableAddons.forEach(addon => {
        let aId = String(addon.id);
        let count = currentCustomizingDish.selectedCounts[aId] || 0;
        let isNonVeg = String(addon.food_type || '').toLowerCase() === 'non-veg';
        let price = parseFloat(addon.price) || 0;
        let lineTotal = price * count;
        
        let addonRow = `
            <div class="pos-addon-selection-row ${count > 0 ? 'is-selected' : ''}" data-addon-id="${addon.id}">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge ${isNonVeg ? 'bg-danger' : 'bg-success'} rounded-circle" style="width: 10px; height: 10px; padding: 0;"></span>
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">${escapeHtml(addon.name)}</div>
                        <div class="small text-muted">+₹${price.toFixed(2)} / portion</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="fw-bold text-primary modal-addon-line-total" style="font-size: 0.95rem;">₹${lineTotal.toFixed(2)}</span>
                    <div class="pos-addon-ctrl-wrap" style="padding: 2px 4px;">
                        <button type="button" class="btn-addon-step modal-addon-dec" data-addon-id="${addon.id}">-</button>
                        <span class="addon-qty-num modal-addon-qty-val" data-addon-id="${addon.id}" style="min-width: 24px;">${count}</span>
                        <button type="button" class="btn-addon-step modal-addon-inc" data-addon-id="${addon.id}">+</button>
                    </div>
                </div>
            </div>
        `;
        modalBody.append(addonRow);
    });

    updateAddonModalTotal();
    $('#addonModalBackdrop').addClass('is-open').css('display', 'flex').hide().fadeIn(180);
}

function closeAddonModal() {
    $('#addonModalBackdrop').fadeOut(150, function() {
        $(this).removeClass('is-open');
    });
    currentCustomizingDish = null;
}

let pendingStandaloneAddon = null;

function attachAddonToCartItem(itemIndex, addonData) {
    if (!orderItems[itemIndex]) return;
    if (!orderItems[itemIndex].addons) orderItems[itemIndex].addons = [];

    let existing = orderItems[itemIndex].addons.find(a => String(a.id) === String(addonData.id));
    if (existing) {
        existing.qty = (parseInt(existing.qty || existing.quantity) || 1) + 1;
        existing.quantity = existing.qty;
    } else {
        orderItems[itemIndex].addons.push({
            id: addonData.id,
            name: addonData.name,
            price: parseFloat(addonData.price) || 0,
            qty: 1,
            quantity: 1,
            food_type: addonData.food_type || 'VEG'
        });
    }

    $('#mapAddonModalBackdrop').fadeOut(150);
    pendingStandaloneAddon = null;
    renderOrderTable();
    showToast(`Mapped ${addonData.name} to ${orderItems[itemIndex].name}`);
}

$(document).ready(function() {
    // Category pill tab click
    $(document).on('click', '.cat-pill-tab', function(e) {
        e.preventDefault();
        $('.cat-pill-tab').removeClass('active');
        $(this).closest('.cat-pill-tab').addClass('active');
        applyFoodFilters();
    });

    // Search and Veg Filter inputs
    $('#vegFilter, #nameFilter').on('input change keyup', function() {
        applyFoodFilters();
    });

    // Add standalone add-on directly button
    $(document).on('click', '.add-addon-direct-btn', function(e) {
        e.preventDefault();
        let addonId = $(this).data('id');
        let addonName = $(this).data('name');
        let addonPrice = parseFloat($(this).data('price')) || 0;
        let foodType = $(this).data('food-type') || 'VEG';

        let cartKey = 'addon_' + addonId;
        let existing = orderItems.find(i => i.id === cartKey || (i.is_addon && i.addon_id == addonId));

        if (existing) {
            existing.qty += 1;
            showToast(`${addonName} quantity increased to ${existing.qty}`);
        } else {
            orderItems.push({
                id: cartKey,
                name: addonName,
                price: addonPrice,
                qty: 1,
                itemDiscount: 0,
                is_addon: true,
                addon_id: addonId,
                food_type: foodType,
                addons: [{
                    id: addonId,
                    name: addonName,
                    price: addonPrice,
                    qty: 1,
                    quantity: 1,
                    food_type: foodType
                }]
            });
            showToast(`${addonName} added to order`);
        }
        renderOrderTable();
    });

    $(document).on('click', '.btn-map-dish-target', function(e) {
        e.preventDefault();
        let itemIdx = $(this).data('item-index');
        if (pendingStandaloneAddon && itemIdx !== undefined) {
            attachAddonToCartItem(itemIdx, pendingStandaloneAddon);
        }
    });

    $(document).on('click', '#closeMapAddonModalBtn, #cancelMapAddonBtn', function(e) {
        e.preventDefault();
        $('#mapAddonModalBackdrop').fadeOut(150);
        pendingStandaloneAddon = null;
    });

    // Add item directly button (without addons or base dish)
    $(document).on('click', '.add-item-btn', function(e) {
        e.preventDefault();
        let itemId = $(this).data('id');
        let itemName = $(this).data('name');
        let itemPrice = parseFloat($(this).data('price'));
        let itemDiscount = parseFloat($(this).data('discount')) || 0;
        
        // Check for an existing item without addons
        let existingItem = orderItems.find(i => i.id == itemId && (!i.addons || i.addons.length === 0));
        
        if (existingItem) {
            existingItem.qty += 1;
            showToast(`${itemName} quantity updated to ${existingItem.qty}`);
        } else {
            orderItems.push({
                id: itemId,
                name: itemName,
                price: itemPrice,
                qty: 1,
                itemDiscount: itemDiscount,
                addons: []
            });
            showToast(`${itemName} added to order`);
        }
        
        renderOrderTable();
    });

    // Open Add-on Customization modal button from Dish Card
    $(document).on('click', '.open-addon-modal-btn', function(e) {
        e.preventDefault();
        let dishData = {
            id: $(this).data('id'),
            name: $(this).data('name'),
            price: $(this).data('price'),
            discount: $(this).data('discount'),
            addons: $(this).attr('data-addons') || $(this).data('addons')
        };
        openAddonModal(dishData);
    });

    // Open Add-on Customization modal from Cart Row
    $(document).on('click', '.cart-customize-btn', function(e) {
        e.preventDefault();
        let index = $(this).data('index');
        let item = orderItems[index];
        if (!item) return;
        
        let dishCardBtn = $(`.open-addon-modal-btn[data-id="${item.id}"]`);
        let rawAddons = dishCardBtn.length ? (dishCardBtn.attr('data-addons') || dishCardBtn.data('addons')) : window.POS_RESTAURANT_ADDONS;
        
        openAddonModal({
            id: item.id,
            name: item.name,
            price: item.price,
            discount: item.itemDiscount || 0,
            cartItemIndex: index,
            prefillAddons: item.addons || [],
            addons: rawAddons
        });
    });

    // Close Addon Modal
    $(document).on('click', '#closeAddonModalBtn', function(e) {
        e.preventDefault();
        closeAddonModal();
    });

    $(document).on('click', '#addonModalBackdrop', function(e) {
        if (e.target === this) {
            closeAddonModal();
        }
    });

    // Modal Addon Increment
    $(document).on('click', '.modal-addon-inc', function(e) {
        e.preventDefault();
        let addonId = String($(this).attr('data-addon-id') || $(this).data('addon-id'));
        if (!currentCustomizingDish) return;
        let count = (parseInt(currentCustomizingDish.selectedCounts[addonId]) || 0) + 1;
        currentCustomizingDish.selectedCounts[addonId] = count;
        
        let row = $(`.pos-addon-selection-row[data-addon-id="${addonId}"]`);
        row.find('.modal-addon-qty-val').text(count);
        
        let addon = currentCustomizingDish.availableAddons.find(a => String(a.id) === addonId);
        let lineTotal = addon ? (parseFloat(addon.price) * count) : 0;
        row.find('.modal-addon-line-total').text(`₹${lineTotal.toFixed(2)}`);
        
        if (count > 0) {
            row.addClass('is-selected');
        }
        
        updateAddonModalTotal();
    });

    // Modal Addon Decrement
    $(document).on('click', '.modal-addon-dec', function(e) {
        e.preventDefault();
        let addonId = String($(this).attr('data-addon-id') || $(this).data('addon-id'));
        if (!currentCustomizingDish) return;
        let count = Math.max(0, (parseInt(currentCustomizingDish.selectedCounts[addonId]) || 0) - 1);
        currentCustomizingDish.selectedCounts[addonId] = count;
        
        let row = $(`.pos-addon-selection-row[data-addon-id="${addonId}"]`);
        row.find('.modal-addon-qty-val').text(count);
        
        let addon = currentCustomizingDish.availableAddons.find(a => String(a.id) === addonId);
        let lineTotal = addon ? (parseFloat(addon.price) * count) : 0;
        row.find('.modal-addon-line-total').text(`₹${lineTotal.toFixed(2)}`);
        
        if (count === 0) {
            row.removeClass('is-selected');
        }
        
        updateAddonModalTotal();
    });

    // Toggle selection on row click
    $(document).on('click', '.pos-addon-selection-row', function(e) {
        if ($(e.target).closest('.pos-addon-ctrl-wrap, .btn-addon-step').length) {
            return;
        }
        let addonId = String($(this).attr('data-addon-id') || $(this).data('addon-id'));
        if (!currentCustomizingDish) return;
        let currentCount = parseInt(currentCustomizingDish.selectedCounts[addonId]) || 0;
        let count = currentCount === 0 ? 1 : 0;
        currentCustomizingDish.selectedCounts[addonId] = count;
        
        $(this).find('.modal-addon-qty-val').text(count);
        let addon = currentCustomizingDish.availableAddons.find(a => String(a.id) === addonId);
        let lineTotal = addon ? (parseFloat(addon.price) * count) : 0;
        $(this).find('.modal-addon-line-total').text(`₹${lineTotal.toFixed(2)}`);
        
        if (count > 0) {
            $(this).addClass('is-selected');
        } else {
            $(this).removeClass('is-selected');
        }
        updateAddonModalTotal();
    });

    // Confirm Addons & Add to Cart
    $('#btnConfirmAddons').on('click', function() {
        if (!currentCustomizingDish) return;
        
        let selectedAddonsList = [];
        currentCustomizingDish.availableAddons.forEach(addon => {
            let aId = String(addon.id);
            let count = currentCustomizingDish.selectedCounts[aId] || 0;
            if (count > 0) {
                let price = parseFloat(addon.price) || 0;
                selectedAddonsList.push({
                    id: addon.id,
                    name: addon.name,
                    price: price,
                    qty: count,
                    quantity: count,
                    total: price * count,
                    food_type: addon.food_type || 'VEG'
                });
            }
        });

        // Case 1: Editing existing cart item directly
        if (currentCustomizingDish.cartItemIndex !== null && orderItems[currentCustomizingDish.cartItemIndex]) {
            orderItems[currentCustomizingDish.cartItemIndex].addons = selectedAddonsList;
            showToast(`Updated add-ons for ${currentCustomizingDish.name}`);
        } else {
            // Case 2: Opened from dish card
            // Check if there is an uncustomized dish in cart with 0 addons: update that item!
            let uncustomizedIndex = orderItems.findIndex(i => i.id == currentCustomizingDish.id && (!i.addons || i.addons.length === 0));
            
            if (uncustomizedIndex !== -1 && selectedAddonsList.length > 0) {
                orderItems[uncustomizedIndex].addons = selectedAddonsList;
                showToast(`${currentCustomizingDish.name} updated with add-ons`);
            } else {
                // Check if exact same dish + same addons already exists
                let exactMatch = orderItems.find(i => {
                    if (i.id != currentCustomizingDish.id) return false;
                    let iAddons = i.addons || [];
                    if (iAddons.length !== selectedAddonsList.length) return false;
                    return selectedAddonsList.every(sa => {
                        let match = iAddons.find(ia => String(ia.id) === String(sa.id));
                        return match && (parseInt(match.qty || match.quantity) === parseInt(sa.qty));
                    });
                });

                if (exactMatch) {
                    exactMatch.qty += 1;
                    showToast(`${currentCustomizingDish.name} quantity increased to ${exactMatch.qty}`);
                } else {
                    orderItems.push({
                        id: currentCustomizingDish.id,
                        name: currentCustomizingDish.name,
                        price: currentCustomizingDish.price,
                        qty: 1,
                        itemDiscount: currentCustomizingDish.discount,
                        addons: selectedAddonsList
                    });
                    showToast(`${currentCustomizingDish.name} added to cart`);
                }
            }
        }

        closeAddonModal();
        renderOrderTable();
    });

    // Cart Addon Quantity Step Controls
    $(document).on('click', '.inc-cart-addon-qty', function() {
        let itemIdx = $(this).data('item-idx');
        let addonIdx = $(this).data('addon-idx');
        if (orderItems[itemIdx] && orderItems[itemIdx].addons && orderItems[itemIdx].addons[addonIdx]) {
            orderItems[itemIdx].addons[addonIdx].qty = (parseInt(orderItems[itemIdx].addons[addonIdx].qty) || 1) + 1;
            orderItems[itemIdx].addons[addonIdx].quantity = orderItems[itemIdx].addons[addonIdx].qty;
            renderOrderTable();
        }
    });

    $(document).on('click', '.dec-cart-addon-qty', function() {
        let itemIdx = $(this).data('item-idx');
        let addonIdx = $(this).data('addon-idx');
        if (orderItems[itemIdx] && orderItems[itemIdx].addons && orderItems[itemIdx].addons[addonIdx]) {
            let currentQty = parseInt(orderItems[itemIdx].addons[addonIdx].qty) || 1;
            if (currentQty > 1) {
                orderItems[itemIdx].addons[addonIdx].qty = currentQty - 1;
                orderItems[itemIdx].addons[addonIdx].quantity = orderItems[itemIdx].addons[addonIdx].qty;
            } else {
                orderItems[itemIdx].addons.splice(addonIdx, 1);
            }
            renderOrderTable();
        }
    });

    $(document).on('click', '.remove-cart-addon', function() {
        let itemIdx = $(this).data('item-idx');
        let addonIdx = $(this).data('addon-idx');
        if (orderItems[itemIdx] && orderItems[itemIdx].addons && orderItems[itemIdx].addons[addonIdx]) {
            let removedName = orderItems[itemIdx].addons[addonIdx].name;
            orderItems[itemIdx].addons.splice(addonIdx, 1);
            renderOrderTable();
            showToast(`Removed add-on "${removedName}"`, false);
        }
    });

    $(document).on('click', '.clear-all-addons-btn', function() {
        let itemIdx = $(this).data('item-idx');
        if (orderItems[itemIdx]) {
            orderItems[itemIdx].addons = [];
            renderOrderTable();
            showToast(`Cleared add-ons for ${orderItems[itemIdx].name}`);
        }
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
    
    // Increase main item quantity
    $(document).on('click', '.increase-qty', function() {
        let index = $(this).data('index');
        if (orderItems[index]) {
            orderItems[index].qty += 1;
            renderOrderTable();
        }
    });
    
    // Decrease main item quantity
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

    // Order status dropdown change (Done & Checkout Bill vs Running Service)
    $('#order_complete').on('change', function() {
        let val = $(this).val();
        if (val === 'DONE') {
            $('#checkoutPaymentSection').slideDown(250);
            $('#statusBadgeTag').text('Instant Checkout');
            $('#saveOrderBtn').html('<i class="fa-solid fa-circle-check"></i> Done &amp; Checkout Bill');
            syncPaymentSplit('total_change');
        } else {
            $('#checkoutPaymentSection').slideUp(250);
            $('#statusBadgeTag').text('Running Service');
            $('#saveOrderBtn').html('<i class="fa-solid fa-fire"></i> Save &amp; Fire KOT');
        }
    });

    // Quick Split Buttons
    $('#btnAllCash').on('click', function(e) {
        e.preventDefault();
        userEditedSplit = false;
        let finalTotal = parseFloat($('#final_total').text()) || 0;
        $('#cash_payment_amount').val(finalTotal.toFixed(2));
        $('#upi_payment_amount').val('0.00');
        syncPaymentSplit('button');
    });

    $('#btnAllUpi').on('click', function(e) {
        e.preventDefault();
        userEditedSplit = true;
        let finalTotal = parseFloat($('#final_total').text()) || 0;
        $('#cash_payment_amount').val('0.00');
        $('#upi_payment_amount').val(finalTotal.toFixed(2));
        syncPaymentSplit('button');
    });

    $('#btnSplitFifty').on('click', function(e) {
        e.preventDefault();
        userEditedSplit = true;
        let finalTotal = parseFloat($('#final_total').text()) || 0;
        let half = Math.round((finalTotal / 2) * 100) / 100;
        let otherHalf = Math.round((finalTotal - half) * 100) / 100;
        $('#cash_payment_amount').val(half.toFixed(2));
        $('#upi_payment_amount').val(otherHalf.toFixed(2));
        syncPaymentSplit('button');
    });

    // Auto-calculate remaining amount when manually editing Cash or UPI
    $('#cash_payment_amount').on('input keyup change', function() {
        let finalTotal = parseFloat($('#final_total').text()) || 0;
        let cashRaw = $(this).val();
        let cashVal = parseFloat(cashRaw);
        if (isNaN(cashVal) || cashVal < 0) {
            cashVal = 0;
            $(this).val('0.00');
        }
        if (cashVal > finalTotal) {
            cashVal = finalTotal;
            $(this).val(finalTotal.toFixed(2));
        }
        let remaining = Math.max(0, finalTotal - cashVal);
        $('#upi_payment_amount').val(remaining.toFixed(2));
        userEditedSplit = true;
        syncPaymentSplit('cash_input');
    });

    $('#upi_payment_amount').on('input keyup change', function() {
        let finalTotal = parseFloat($('#final_total').text()) || 0;
        let upiRaw = $(this).val();
        let upiVal = parseFloat(upiRaw);
        if (isNaN(upiVal) || upiVal < 0) {
            upiVal = 0;
            $(this).val('0.00');
        }
        if (upiVal > finalTotal) {
            upiVal = finalTotal;
            $(this).val(finalTotal.toFixed(2));
        }
        let remaining = Math.max(0, finalTotal - upiVal);
        $('#cash_payment_amount').val(remaining.toFixed(2));
        userEditedSplit = true;
        syncPaymentSplit('upi_input');
    });
    
    // Save Order
    $('#saveOrderBtn').click(function() {
        let customer_name = $('#customer_name').val().trim();
        let customer_phone = $('#customer_phone').val().trim();
        let table_id = $('#table_id').val();
        let orderDiscount = $('#order_discount').val() || 0;
        let order_complete = $('#order_complete').length ? $('#order_complete').val() : 'DONE';
        let remarks = $('#remarks').val() || null;
        let finalTotal = parseFloat($('#final_total').text()) || 0;

        let cashAmount = 0;
        let upiAmount = 0;

        if (order_complete === 'DONE') {
            cashAmount = parseFloat($('#cash_payment_amount').val()) || 0;
            upiAmount = parseFloat($('#upi_payment_amount').val()) || 0;

            if ((cashAmount + upiAmount) > (finalTotal + 0.01)) {
                showToast(`Total payment (₹${(cashAmount + upiAmount).toFixed(2)}) cannot exceed Grand Total (₹${finalTotal.toFixed(2)})`, true);
                return;
            }
        }
        
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
            item_discount: item.itemDiscount || 0,
            addons: item.addons || []
        }));
        
        let btnText = (order_complete === 'DONE') ? 'Processing Checkout & Printing...' : 'Saving Order & Firing KOT...';
        $(this).prop('disabled', true).html(`<i class="fa-solid fa-spinner fa-spin me-2"></i> ${btnText}`);
        
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
                cash_amount: cashAmount,
                upi_amount: upiAmount,
                remarks: remarks,
                is_gst_registered: isGstRegistered,
                gst_percentage: restaurantGstPercentage
            },
            success: function(response) {
                if (response.success) {
                    showToast('Order and Payment processed successfully!', false);
                    let targetUrl = response.redirect_url || response.invoice_url || "{{ route('order.management.dashboard') }}";
                    setTimeout(() => { 
                        window.location.href = targetUrl; 
                    }, 600);
                } else {
                    showToast(response.message || 'Error saving order', true);
                    $('#saveOrderBtn').prop('disabled', false).html('<i class="fa-solid fa-circle-check"></i> Done &amp; Checkout Bill');
                }
            },
            error: function(xhr) {
                let errorMsg = xhr.responseJSON?.message || 'An error occurred while saving';
                showToast(errorMsg, true);
                $('#saveOrderBtn').prop('disabled', false).html('<i class="fa-solid fa-circle-check"></i> Done &amp; Checkout Bill');
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