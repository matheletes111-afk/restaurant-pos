<!DOCTYPE html>
<html lang="en">
<head>
  <title>Admin - Create Order</title>
  @include('includes.style')
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* Your existing styles */
    :root {
      --primary: #2c3e50;
      --secondary: #3498db;
      --success: #27ae60;
      --danger: #e74c3c;
      --warning: #f39c12;
      --light: #ecf0f1;
      --dark: #2c3e50;
      --gray: #95a5a6;
      --light-gray: #f8f9fa;
    }

    body {
      background-color: #f5f7fb;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .page-header {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.08);
      margin-bottom: 25px;
    }

    .page-header h5 {
      color: var(--primary);
      font-weight: 600;
      margin-bottom: 5px;
    }

    .breadcrumb {
      background: transparent;
      padding: 0;
      margin: 0;
    }

    .breadcrumb-item a {
      color: var(--secondary);
      text-decoration: none;
    }

    .breadcrumb-item.active {
      color: var(--gray);
    }

    .card {
      border: none;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      margin-bottom: 25px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      overflow: hidden;
    }

    .card:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 18px rgba(0,0,0,0.12);
    }

    .card-header {
      background: linear-gradient(135deg, var(--primary), #34495e);
      color: white;
      border-bottom: none;
      padding: 15px 20px;
      border-radius: 12px 12px 0 0 !important;
    }

    .card-header h5 {
      margin: 0;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .table-badge {
      background: var(--secondary);
      color: white;
      padding: 5px 12px;
      border-radius: 6px;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }

    .takeaway-badge {
      background: var(--success);
      color: white;
      padding: 5px 12px;
      border-radius: 6px;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }

    .filter-section {
      background: white;
      padding: 15px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
      margin: 20px 0;
    }

    .filter-section select, .filter-section input {
      border: 2px solid #e0e6ed;
      border-radius: 8px;
      padding: 8px 15px;
      transition: all 0.3s;
    }

    .nav-tabs {
      border: none;
      background: white;
      padding: 10px 10px 0;
      border-radius: 10px 10px 0 0;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }

    .nav-tabs .nav-link {
      border: none;
      color: var(--gray);
      font-weight: 500;
      padding: 10px 20px;
      border-radius: 8px 8px 0 0;
      margin-right: 5px;
      transition: all 0.3s;
      background: transparent;
    }

    .nav-tabs .nav-link:hover {
      color: var(--secondary);
      background: rgba(52, 152, 219, 0.05);
    }

    .nav-tabs .nav-link.active {
      background: white;
      color: var(--primary);
      box-shadow: 0 -2px 8px rgba(0,0,0,0.04);
      border-bottom: 3px solid var(--secondary);
    }

    .food-card {
      margin-bottom: 20px;
    }

    .food-item {
      border: 1px solid #e0e6ed;
      border-radius: 10px;
      padding: 15px;
      text-align: center;
      transition: all 0.3s ease;
      background: white;
      height: 100%;
    }

    .food-item:hover {
      border-color: var(--secondary);
      box-shadow: 0 4px 12px rgba(52, 152, 219, 0.15);
      transform: translateY(-3px);
    }

    .food-item h6 {
      color: var(--primary);
      font-weight: 600;
      margin-bottom: 8px;
    }

    .food-item .price {
      color: var(--success);
      font-size: 1.3rem;
      font-weight: 700;
      margin: 10px 0;
    }

    .food-badge {
      font-size: 0.75rem;
      padding: 4px 10px;
      border-radius: 20px;
      display: inline-block;
      margin-bottom: 8px;
    }

    .veg-badge {
      background: rgba(39, 174, 96, 0.1);
      color: var(--success);
      border: 1px solid rgba(39, 174, 96, 0.2);
    }

    .nonveg-badge {
      background: rgba(231, 76, 60, 0.1);
      color: var(--danger);
      border: 1px solid rgba(231, 76, 60, 0.2);
    }

    .gst-badge {
      background: rgba(243, 156, 18, 0.1);
      color: var(--warning);
      border: 1px solid rgba(243, 156, 18, 0.2);
    }

    .discount-badge {
      background: rgba(46, 158, 79, 0.1);
      color: var(--success);
      border: 1px solid rgba(46, 158, 79, 0.2);
    }

    .add-item-btn {
      background: var(--secondary);
      color: white;
      border: none;
      border-radius: 6px;
      padding: 8px 20px;
      font-weight: 500;
      transition: all 0.3s;
      width: 100%;
      margin-top: 10px;
    }

    .add-item-btn:hover {
      background: #2980b9;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(52, 152, 219, 0.2);
    }

    .order-items-section {
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
      margin-top: 30px;
    }

    /* Full width table styles */
    .table-responsive {
      overflow-x: auto;
    }

    #orderListTable {
      width: 100% !important;
      min-width: 1000px;
    }

    #orderListTable thead {
      background: linear-gradient(135deg, var(--primary), #34495e);
    }

    #orderListTable th {
      border: none;
      color: white;
      font-weight: 500;
      padding: 12px 8px;
      font-size: 0.8rem;
      white-space: nowrap;
    }

    #orderListTable td {
      padding: 10px 8px;
      vertical-align: middle;
    }

    #orderListTable tbody tr:hover {
      background: #f8f9fa;
    }

    .qty-input {
      width: 60px;
      text-align: center;
      border: 2px solid #e0e6ed;
      border-radius: 6px;
      padding: 5px;
    }

    .remove-btn {
      background: var(--danger);
      color: white;
      border: none;
      border-radius: 6px;
      padding: 5px 12px;
      transition: all 0.3s;
    }

    .remove-btn:hover {
      background: #c0392b;
      transform: scale(1.05);
    }

    .item-discount-input {
      width: 70px;
      text-align: center;
      border: 1px solid #e0e6ed;
      border-radius: 4px;
      padding: 5px;
      font-size: 0.85rem;
    }

    .summary-box {
      background: white;
      border-radius: 10px;
      padding: 25px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      margin-top: 25px;
      border-top: 4px solid var(--secondary);
    }

    .summary-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 0;
      border-bottom: 1px solid #e0e6ed;
    }

    .summary-item:last-child {
      border-bottom: none;
    }

    .summary-label {
      color: var(--gray);
      font-size: 0.9rem;
    }

    .summary-value {
      color: var(--primary);
      font-weight: 600;
      font-size: 1rem;
    }

    .summary-total {
      background: linear-gradient(135deg, var(--primary), #34495e);
      color: white;
      padding: 15px;
      border-radius: 8px;
      margin-top: 15px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .total-label {
      font-size: 1rem;
    }

    .total-value {
      font-size: 1.3rem;
      font-weight: 700;
    }

    .discount-input {
      border: 2px solid #e0e6ed;
      border-radius: 6px;
      padding: 8px 12px;
      text-align: center;
      width: 100%;
    }

    .payment-section {
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
      margin-top: 25px;
      border-left: 4px solid var(--success);
    }

    .save-btn {
      background: linear-gradient(135deg, var(--success), #219653);
      color: white;
      border: none;
      border-radius: 8px;
      padding: 12px 40px;
      font-size: 1rem;
      font-weight: 600;
      transition: all 0.3s;
      display: flex;
      align-items: center;
      gap: 10px;
      margin-left: auto;
      box-shadow: 0 4px 12px rgba(39, 174, 96, 0.2);
    }

    .save-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(39, 174, 96, 0.3);
    }

    .empty-state {
      text-align: center;
      padding: 40px 20px;
      color: var(--gray);
    }

    .empty-state i {
      font-size: 3rem;
      opacity: 0.5;
      margin-bottom: 15px;
    }

    .hidden {
      display: none !important;
    }

    .toast-notification {
      position: fixed;
      bottom: 30px;
      right: 30px;
      background: white;
      border-radius: 12px;
      padding: 12px 20px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.15);
      z-index: 9999;
      display: flex;
      align-items: center;
      gap: 10px;
      transform: translateX(400px);
      transition: transform 0.3s ease;
      border-left: 4px solid var(--success);
    }

    .toast-notification.show {
      transform: translateX(0);
    }

    .toast-notification.error {
      border-left-color: var(--danger);
    }

    .discount-note {
      font-size: 0.7rem;
      color: var(--gray);
      text-align: center;
      margin-top: 8px;
    }

    .badge-info {
      background-color: #17a2b8;
      color: white;
      padding: 3px 8px;
      border-radius: 20px;
      font-size: 0.7rem;
    }

    .text-success {
      color: #27ae60 !important;
    }

    del {
      font-size: 0.7rem;
    }

    @media (max-width: 768px) {
      .card-header h5 { font-size: 1rem; }
      .nav-tabs .nav-link { padding: 8px 12px; font-size: 0.9rem; }
      .summary-box { padding: 20px; }
      .save-btn { width: 100%; justify-content: center; }
    }
  </style>
</head>

<body data-pc-theme="light">
@include('includes.sidebar')

<div class="pc-container">
  <div class="pc-content">
    <div class="page-header">
      <div class="page-block">
        <div class="row align-items-center">
          <div class="col-md-12">
            <h5>Create Order</h5>
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ route('order.management.dashboard') }}">Order Management</a></li>
              <li class="breadcrumb-item active" aria-current="page">Create Order</li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-8">
        <!-- Customer & Table -->
        <div class="card">
          <div class="card-header">
            <h5><i class="fas fa-user me-2"></i>Customer & Table Details</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label>Customer Name *</label>
                <input type="text" class="form-control" id="customer_name" placeholder="Enter customer name" required>
              </div>
              <div class="col-md-6 mb-3">
                <label>Customer Phone</label>
                <input type="tel" class="form-control" id="customer_phone" placeholder="Enter phone number (optional)">
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label>Table</label>
                <div>
                  @if(isset($table) && $table)
                    <div class="table-badge">
                      <i class="fas fa-table"></i>
                      {{ $table->name }}
                    </div>
                    <input type="hidden" id="table_id" value="{{ $table->id }}">
                  @else
                    <div class="takeaway-badge">
                      <i class="fas fa-utensils"></i>
                      Takeaway
                    </div>
                    <input type="hidden" id="table_id" value="">
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="filter-section">
          <div class="row align-items-center">
            <div class="col-md-6 mb-2">
              <select id="vegFilter" class="form-control">
                <option value="">All Items</option>
                <option value="veg">Vegetarian</option>
                <option value="non-veg">Non-Vegetarian</option>
              </select>
            </div>
            <div class="col-md-6 mb-2">
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" id="nameFilter" class="form-control" placeholder="Search items...">
              </div>
            </div>
          </div>
        </div>

        <!-- Category Tabs -->
        <div class="card">
          <div class="card-body p-0">
            <ul class="nav nav-tabs" id="categoryTabs" role="tablist">
              @foreach($categories as $key => $category)
              <li class="nav-item" role="presentation">
                <a class="nav-link {{ $key == 0 ? 'active' : '' }}" 
                   id="cat-{{ $category->id }}" 
                   data-toggle="tab" 
                   href="#category-{{ $category->id }}" 
                   role="tab">
                  {{ $category->name }}
                </a>
              </li>
              @endforeach
            </ul>

            <div class="tab-content p-3">
              @foreach($categories as $key => $category)
              <div class="tab-pane fade {{ $key == 0 ? 'show active' : '' }}" 
                   id="category-{{ $category->id }}" 
                   role="tabpanel">
                <div class="row">
                  @foreach($category->subcategories as $item)
                  <div class="col-lg-4 col-md-6 mb-3 food-card" 
                       data-type="{{ strtolower($item->food_type ?? 'veg') }}" 
                       data-name="{{ strtolower($item->name) }}">
                    <div class="food-item">
                      <span class="food-badge {{ strtolower($item->food_type ?? 'veg') == 'non-veg' ? 'nonveg-badge' : 'veg-badge' }}">
                        {{ $item->food_type ?? 'Veg' }}
                      </span>
                      @if(($item->discount_percentage ?? 0) > 0)
                      <span class="food-badge discount-badge">
                        {{ $item->discount_percentage }}% OFF
                      </span>
                      @endif
                      <span class="food-badge gst-badge">
                        GST: {{ $item->gst_rate }}%
                      </span>
                      <h6>{{ $item->name }}</h6>
                      <div class="price">
                        @if(($item->discount_percentage ?? 0) > 0)
                          <del class="text-muted">₹{{ number_format($item->price, 2) }}</del>
                          <span class="text-success">₹{{ number_format($item->price - ($item->price * $item->discount_percentage / 100), 2) }}</span>
                        @else
                          ₹{{ number_format($item->price, 2) }}
                        @endif
                      </div>
                      <button class="add-item-btn"
                            data-id="{{ $item->id }}"
                            data-name="{{ $item->name }}"
                            data-price="{{ $item->price }}"
                            data-gst="{{ $item->gst_rate }}"
                            data-discount="{{ $item->discount_percentage ?? 0 }}">
                        <i class="fas fa-plus me-2"></i>Add to Order
                      </button>
                    </div>
                  </div>
                  @endforeach
                </div>
              </div>
              @endforeach
            </div>
          </div>
        </div>

        <!-- Order Items - Full Width Table -->
        <div class="order-items-section">
          <h5 class="mb-3"><i class="fas fa-shopping-cart me-2"></i>Selected Items</h5>
          <div class="table-responsive">
            <table id="orderListTable" class="table table-bordered" style="width:100%">
              <thead>
                <tr>
                  <th>Item</th>
                  <th>Price (₹)</th>
                  <th>Disc %</th>
                  <th>Qty</th>
                  <th>Disc Price (₹)</th>
                  <th>Taxable (₹)</th>
                  <th>GST (%)</th>
                  <th>GST Amt (₹)</th>
                  <th>Total (₹)</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="orderItemsBody">
                <!-- Dynamic content will appear here -->
              </tbody>
            </table>
          </div>
          <div id="emptyOrderState" class="empty-state">
            <i class="fas fa-shopping-cart"></i>
            <h5>No Items Added</h5>
            <p>Click on "Add to Order" button from menu items above</p>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <!-- Order Summary -->
        <div class="summary-box">
          <h5 class="mb-3"><i class="fas fa-receipt me-2"></i>Order Summary</h5>
          
          <div class="summary-item">
            <span class="summary-label">Original Subtotal:</span>
            <span class="summary-value">₹<span id="original_subtotal">0.00</span></span>
          </div>
          
          <div class="summary-item">
            <span class="summary-label">Item Discount:</span>
            <span class="summary-value text-success">- ₹<span id="item_discount_total">0.00</span></span>
          </div>
          
          <div class="summary-item">
            <span class="summary-label">Taxable Value:</span>
            <span class="summary-value">₹<span id="total_taxable">0.00</span></span>
          </div>
          
          <div class="summary-item">
            <span class="summary-label">GST Total:</span>
            <span class="summary-value">₹<span id="total_gst">0.00</span></span>
          </div>
          
          
          
          <div class="summary-item hidden" id="round_off_item">
            <span class="summary-label">Round Off:</span>
            <span class="summary-value" id="round_off">₹0.00</span>
          </div>
          
          <div class="summary-total">
            <span class="total-label">Final Total:</span>
            <span class="total-value">₹<span id="final_total">0.00</span></span>
          </div>

          
        </div>

        @if(!isset($table) || !$table)
        <div class="payment-section">
          <h5 class="mb-3"><i class="fas fa-credit-card me-2"></i>Payment Details</h5>
          
          <div class="mb-3">
            <label class="form-label">Payment Status</label>
            <select class="form-control" id="payment_status">
              <option value="PENDING">Pending</option>
              <option value="PAID">Paid</option>
            </select>
          </div>

          <div class="mb-3 hidden" id="paymentMethodDiv">
            <label class="form-label">Payment Method</label>
            <select class="form-control" id="payment_method">
              <option value="">-- Select Method --</option>
              @if(isset($payment_methods))
                @foreach($payment_methods as $method)
                  <option value="{{ $method }}">{{ $method }}</option>
                @endforeach
              @else
                <option value="Cash">Cash</option>
                <option value="UPI">UPI</option>
                <option value="Card">Card</option>
              @endif
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Remarks</label>
            <textarea class="form-control" id="remarks" rows="2" placeholder="Any special instructions..."></textarea>
          </div>
        </div>
        @endif

        <div class="d-grid gap-2 mt-4">
          <button class="btn save-btn" id="saveOrderBtn">
            <i class="fas fa-check-circle"></i>
            Save Order
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="toast-notification" id="toastNotification">
  <i class="fas fa-check-circle" style="color: #27ae60;"></i>
  <span id="toastMessage">Success!</span>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
@include('includes.script')

<script>
// Global variable to store order items
let orderItems = [];

function showToast(message, isError = false) {
    let toast = $('#toastNotification');
    toast.find('#toastMessage').text(message);
    
    if (isError) {
        toast.addClass('error');
        toast.find('i').attr('class', 'fas fa-exclamation-circle').css('color', '#e74c3c');
    } else {
        toast.removeClass('error');
        toast.find('i').attr('class', 'fas fa-check-circle').css('color', '#27ae60');
    }
    
    toast.addClass('show');
    setTimeout(() => {
        toast.removeClass('show');
    }, 3000);
}

function calculateItemDetails(originalPrice, qty, gstRate, discountPercent = 0) {
    // Calculate discounted price per item
    let discountedPricePerItem = originalPrice - (originalPrice * discountPercent / 100);
    
    // Calculate taxable amount (after discount)
    let taxableAmount = discountedPricePerItem * qty;
    
    // Calculate GST on discounted price
    let gstAmount = (taxableAmount * gstRate) / 100;
    
    // Calculate total amount
    let totalAmount = taxableAmount + gstAmount;
    
    return {
        discountedPricePerItem: discountedPricePerItem,
        taxableAmount: taxableAmount,
        gstAmount: gstAmount,
        totalAmount: totalAmount,
        itemDiscountAmount: (originalPrice * qty) - taxableAmount
    };
}

function updateSummary() {
    let originalSubtotal = 0;
    let totalTaxable = 0;
    let totalGst = 0;
    let totalItemDiscount = 0;
    
    orderItems.forEach(item => {
        let originalAmount = item.price * item.qty;
        let details = calculateItemDetails(item.price, item.qty, item.gst, item.itemDiscount || 0);
        
        originalSubtotal += originalAmount;
        totalTaxable += details.taxableAmount;
        totalGst += details.gstAmount;
        totalItemDiscount += details.itemDiscountAmount;
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
    $('#total_gst').text(totalGst.toFixed(2));
    $('#order_discount_amount').text(orderDiscountAmount.toFixed(2));
    
    if (Math.abs(roundOff) > 0.01) {
        $('#round_off_item').removeClass('hidden');
        $('#round_off').text(`₹${roundOff.toFixed(2)}`);
    } else {
        $('#round_off_item').addClass('hidden');
    }
    
    $('#final_total').text(finalTotal.toFixed(2));
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

function renderOrderTable() {
    let tbody = $('#orderItemsBody');
    let emptyState = $('#emptyOrderState');
    
    // Clear the tbody first
    tbody.empty();
    
    if (orderItems.length === 0) {
        emptyState.removeClass('hidden');
        updateSummary();
        return;
    }
    
    emptyState.addClass('hidden');
    
    // Build HTML for each item
    orderItems.forEach((item, index) => {
        let details = calculateItemDetails(item.price, item.qty, item.gst, item.itemDiscount || 0);
        
        let row = `
            <tr data-index="${index}">
                <td>
                    <strong>${escapeHtml(item.name)}</strong>
                 </td>
                <td class="text-end">₹${item.price.toFixed(2)}</td>
                <td class="text-center">
                    <input type="number" class="item-discount-input form-control-sm" 
                           data-index="${index}" value="${item.itemDiscount || 0}" 
                           min="0" max="100" step="1" style="width: 70px;">
                    <span>%</span>
                </td>
                <td class="text-center">
                    <div class="d-flex align-items-center justify-content-center gap-1">
                        <button class="btn btn-sm btn-outline-secondary decrease-qty" data-index="${index}">-</button>
                        <input type="number" class="form-control qty-input text-center" 
                               data-index="${index}" value="${item.qty}" min="1" style="width: 55px;">
                        <button class="btn btn-sm btn-outline-secondary increase-qty" data-index="${index}">+</button>
                    </div>
                </td>
                <td class="text-end">₹${details.discountedPricePerItem.toFixed(2)}</td>
                <td class="text-end">₹${details.taxableAmount.toFixed(2)}</td>
                <td class="text-center">${item.gst}%</td>
                <td class="text-end">₹${details.gstAmount.toFixed(2)}</td>
                <td class="text-end fw-bold">₹${details.totalAmount.toFixed(2)}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-danger remove-btn" data-index="${index}">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
             </tr>
        `;
        tbody.append(row);
    });
    
    updateSummary();
}

$(document).ready(function() {
    console.log('Document ready');
    
    // Add item button click
    $(document).on('click', '.add-item-btn', function(e) {
        e.preventDefault();
        
        let itemId = $(this).data('id');
        let itemName = $(this).data('name');
        let itemPrice = parseFloat($(this).data('price'));
        let itemGst = parseFloat($(this).data('gst'));
        let itemDiscount = parseFloat($(this).data('discount')) || 0;
        
        let existingItem = orderItems.find(i => i.id === itemId);
        
        if (existingItem) {
            existingItem.qty += 1;
            showToast(`${itemName} quantity increased to ${existingItem.qty}`, false);
        } else {
            orderItems.push({
                id: itemId,
                name: itemName,
                price: itemPrice,
                gst: itemGst,
                qty: 1,
                itemDiscount: itemDiscount
            });
            
            if (itemDiscount > 0) {
                showToast(`${itemName} added with ${itemDiscount}% discount`, false);
            } else {
                showToast(`${itemName} added to order`, false);
            }
        }
        
        renderOrderTable();
    });
    
    // Item discount change - User can modify discount
    $(document).on('change', '.item-discount-input', function() {
        let index = $(this).data('index');
        let newDiscount = parseFloat($(this).val()) || 0;
        if (newDiscount < 0) newDiscount = 0;
        if (newDiscount > 100) newDiscount = 100;
        
        if (orderItems[index]) {
            orderItems[index].itemDiscount = newDiscount;
            $(this).val(newDiscount);
            renderOrderTable();
            showToast(`Discount updated to ${newDiscount}% for ${orderItems[index].name}`, false);
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
                showToast(`${removedItem.name} removed from order`, true);
            }
        }
    });
    
    // Quantity input change
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
    
    // Order discount update
    $('#order_discount').on('input change', function() {
        let discount = parseFloat($(this).val()) || 0;
        if (discount < 0) $(this).val(0);
        if (discount > 100) $(this).val(100);
        updateSummary();
    });
    
    // Payment fields toggle
    $('#payment_status').on('change', function() {
        if ($(this).val() === 'PAID') {
            $('#paymentMethodDiv').removeClass('hidden');
        } else {
            $('#paymentMethodDiv').addClass('hidden');
            $('#payment_method').val('');
        }
    });
    
// Save Order
$('#saveOrderBtn').click(function() {
    let customer_name = $('#customer_name').val().trim();
    let customer_phone = $('#customer_phone').val().trim();
    let table_id = $('#table_id').val();
    let orderDiscount = $('#order_discount').val() || 0;
    let payment_status = $('#payment_status').length ? $('#payment_status').val() : null;
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
    
    if (payment_status === 'PAID' && (!payment_method || payment_method === '')) {
        showToast('Please select payment method', true);
        return;
    }
    
    let orderItemsData = orderItems.map(item => ({
        id: item.id,
        name: item.name,
        price: item.price,
        qty: item.qty,
        gst: item.gst,
        item_discount: item.itemDiscount || 0
    }));
    
    $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...');
    
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
            payment_status: payment_status,
            payment_method: payment_method,
            remarks: remarks
        },
        success: function(response) {
            if (response.success) {
                showToast('Order saved successfully!', false);
                
                // Use the redirect_url from response
                if (response.redirect_url) {
                    setTimeout(() => {
                        window.location.href = response.redirect_url;
                    }, 1000);
                } else if (response.invoice_url) {
                    // Fallback for TAKEAWAY orders
                    setTimeout(() => {
                        window.location.href = response.invoice_url;
                    }, 1000);
                } else {
                    // Fallback for DINE_IN orders
                    setTimeout(() => {
                        window.location.href = "{{ route('order.management.dashboard') }}";
                    }, 1000);
                }
            } else {
                showToast(response.message || 'Error saving order', true);
                $('#saveOrderBtn').prop('disabled', false).html('<i class="fas fa-check-circle"></i> Save Order');
            }
        },
        error: function(xhr) {
            let errorMsg = xhr.responseJSON?.message || 'An error occurred while saving';
            showToast(errorMsg, true);
            $('#saveOrderBtn').prop('disabled', false).html('<i class="fas fa-check-circle"></i> Save Order');
        }
    });
});
    
    // Filters
    $('#vegFilter, #nameFilter').on('input change', function() {
        let type = $('#vegFilter').val().toLowerCase();
        let name = $('#nameFilter').val().toLowerCase();
        
        $('.food-card').each(function() {
            let itemType = $(this).data('type') || '';
            let itemName = $(this).data('name') || '';
            let matchType = !type || itemType === type;
            let matchName = !name || itemName.includes(name);
            if (matchType && matchName) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
    
    // Initial render
    renderOrderTable();
    console.log('Initialization complete');
});
</script>
</body>
</html>