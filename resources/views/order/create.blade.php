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

    /* Card Styling */
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

    .card-header h5 i {
      font-size: 1.1rem;
    }

    /* Customer Info Section */
    .customer-section {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
      margin-bottom: 25px;
    }

    .customer-section label {
      color: var(--primary);
      font-weight: 500;
      margin-bottom: 8px;
      font-size: 0.9rem;
    }

    .customer-section input {
      border: 2px solid #e0e6ed;
      border-radius: 8px;
      padding: 10px 15px;
      transition: all 0.3s;
    }

    .customer-section input:focus {
      border-color: var(--secondary);
      box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
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

    /* Filter Section */
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

    .filter-section select:focus, .filter-section input:focus {
      border-color: var(--secondary);
      box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }

    /* Category Tabs */
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

    /* Food Items */
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

    /* Order Items Table */
    .order-items-section {
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
      margin-top: 30px;
    }

    #orderListTable {
      border-radius: 8px;
      overflow: hidden;
    }

    #orderListTable thead {
      background: linear-gradient(135deg, var(--primary), #34495e);
    }

    #orderListTable th {
      border: none;
      color: white;
      font-weight: 500;
      padding: 12px 15px;
      font-size: 0.9rem;
    }

    #orderListTable tbody tr {
      transition: background 0.3s;
    }

    #orderListTable tbody tr:hover {
      background: #f8f9fa;
    }

    .qty-input {
      width: 70px;
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

    /* Summary Box */
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
      padding: 12px 0;
      border-bottom: 1px solid #e0e6ed;
    }

    .summary-item:last-child {
      border-bottom: none;
    }

    .summary-label {
      color: var(--gray);
      font-size: 0.95rem;
    }

    .summary-value {
      color: var(--primary);
      font-weight: 600;
      font-size: 1.05rem;
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
      font-size: 1.1rem;
    }

    .total-value {
      font-size: 1.4rem;
      font-weight: 700;
    }

    .discount-input {
      border: 2px solid #e0e6ed;
      border-radius: 6px;
      padding: 8px 12px;
      text-align: center;
    }

    /* Payment Section */
    .payment-section {
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
      margin-top: 25px;
      border-left: 4px solid var(--success);
    }

    /* Save Button */
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
      background: linear-gradient(135deg, #219653, #1e8449);
    }

    /* Empty State */
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

    /* Responsive */
    @media (max-width: 768px) {
      .card-header h5 {
        font-size: 1rem;
      }
      
      .nav-tabs .nav-link {
        padding: 8px 12px;
        font-size: 0.9rem;
      }
      
      .summary-box {
        padding: 20px;
      }
      
      .save-btn {
        width: 100%;
        justify-content: center;
      }
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
          @if($table)
            <div class="table-badge">
              <i class="fas fa-table"></i>
              {{ $table->name }}
            </div>
            <input type="hidden" id="table_id" value="{{ $table->id }}">
          @else
            <div class="takeaway-badge">
              <i class="fas fa-table"></i>
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
                <option value="Veg">Vegetarian</option>
                <option value="Non-Veg">Non-Vegetarian</option>
              </select>
            </div>
            <div class="col-md-6 mb-2">
              <div class="input-group">
                <span class="input-group-text">
                  <i class="fas fa-search"></i>
                </span>
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
                       data-type="{{ $item->food_type ?? 'Veg' }}" 
                       data-name="{{ strtolower($item->name) }}">
                    <div class="food-item">
                      <span class="food-badge {{ strtolower($item->food_type ?? 'Veg') == 'non-veg' ? 'nonveg-badge' : 'veg-badge' }}">
                        {{ $item->food_type ?? 'Veg' }}
                      </span>
                      <span class="food-badge gst-badge">
                        GST: {{ $item->gst_rate }}%
                      </span>
                      <h6>{{ $item->name }}</h6>
                      <div class="price">₹{{ $item->price }}</div>
                      <button class="add-item-btn"
                              data-id="{{ $item->id }}"
                              data-name="{{ $item->name }}"
                              data-price="{{ $item->price }}"
                              data-gst="{{ $item->gst_rate }}">
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
        <div id="toastNotification" class="hidden"></div>
        <!-- Order Items -->
        <div class="order-items-section">
          <h5 class="mb-3"><i class="fas fa-shopping-cart me-2"></i>Selected Items</h5>
          <div class="table-responsive">
            <table id="orderListTable" class="table table-hover">
              <thead>
                <tr>
                  <th>Item</th>
                  <th>Price</th>
                  <th>Qty</th>
                  <th>Total</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <!-- Dynamic content -->
              </tbody>
            </table>
          </div>
          <div id="emptyOrderState" class="empty-state">
            <i class="fas fa-shopping-cart"></i>
            <h5>No Items Added</h5>
            <p>Add items from the menu above</p>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <!-- Order Summary -->
        <!-- Order Summary Section -->
<div class="summary-box">
  <h5 class="mb-3"><i class="fas fa-receipt me-2"></i>Order Summary</h5>
  
  <div class="summary-item">
    <span class="summary-label">Subtotal:</span>
    <span class="summary-value">₹<span id="total_amount">0.00</span></span>
  </div>
  
  <div class="summary-item">
    <span class="summary-label">GST Total:</span>
    <span class="summary-value">₹<span id="total_gst">0.00</span></span>
  </div>
  
  <div class="summary-item">
    <span class="summary-label">Discount:</span>
    <span class="summary-value text-success" id="discount_amount">- ₹0.00</span>
  </div>
  
  <!-- Round Off Row (will be shown/hidden dynamically) -->
  <div class="summary-item hidden" id="round_off_item">
    <span class="summary-label">Round Off:</span>
    <span class="summary-value" id="round_off">₹0.00</span>
  </div>
  
  <div class="summary-total">
    <span class="total-label">Final Total:</span>
    <span class="total-value">₹<span id="final_total">0.00</span></span>
  </div>

  <div class="mt-4">
    <label class="form-label mb-2">Discount Percentage</label>
    <div class="input-group">
      <input type="number" class="form-control discount-input" id="discount" value="0" min="0" max="100">
      <span class="input-group-text">%</span>
    </div>
  </div>
</div>

        <!-- Payment Section -->
        @if(!$table)
        <div class="payment-section">
          <h5 class="mb-3"><i class="fas fa-credit-card me-2"></i>Payment Details</h5>
          
          <div class="mb-3">
            <label class="form-label">Payment Status</label>
            <select class="form-control" id="payment_status">
              <option value="UNPAID">Unpaid</option>
              <option value="PAID">Paid</option>
            </select>
          </div>

          <div class="mb-3 hidden" id="paymentMethodDiv">
            <label class="form-label">Payment Method</label>
            <select class="form-control" id="payment_method">
              <option value="">-- Select Method --</option>
              @foreach($payment_methods as $method)
                <option value="{{ $method }}">{{ $method }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Remarks</label>
            <textarea class="form-control" id="remarks" rows="2" placeholder="Any special instructions..."></textarea>
          </div>
        </div>
        @endif

        <!-- Save Button -->
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

<!-- Toast Notification -->


<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
@include('includes.script')

<script>
let orderItems = [];

function showToast(message, type = 'success') {
  const toast = $('#toastNotification');
  toast.removeClass().addClass('alert alert-dismissible fade show');
  toast.addClass(type === 'success' ? 'alert-success' : 'alert-danger');
  toast.html(`
    ${message}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  `);
  toast.removeClass('hidden');
  
  setTimeout(() => {
    toast.addClass('hidden');
  }, 3000);
}

function updateOrderTable() {
  let tbody = $('#orderListTable tbody');
  let emptyState = $('#emptyOrderState');
  
  if (orderItems.length === 0) {
    tbody.html('');
    emptyState.removeClass('hidden');
    return;
  }
  
  emptyState.addClass('hidden');
  tbody.html('');
  
  let subtotal = 0, gstTotal = 0;

  orderItems.forEach((item, index) => {
    let gstAmount = (item.price * item.qty * item.gst) / 100;
    let itemTotal = (item.price * item.qty) + gstAmount;
    subtotal += item.price * item.qty;
    gstTotal += gstAmount;

    tbody.append(`
      <tr>
        <td>
          <strong>${item.name}</strong>
          <div><small class="text-muted">GST: ${item.gst}%</small></div>
        </td>
        <td>₹${item.price.toFixed(2)}</td>
        <td>
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-outline-secondary decrease-qty" 
                    data-index="${index}">-</button>
            <input type="number" class="form-control qty-input" 
                   data-index="${index}" value="${item.qty}" min="1" style="width: 60px;">
            <button class="btn btn-sm btn-outline-secondary increase-qty" 
                    data-index="${index}">+</button>
          </div>
        </td>
        <td>₹${itemTotal.toFixed(2)}</td>
        <td>
          <button class="btn btn-sm remove-btn" data-index="${index}">
            <i class="fas fa-trash"></i>
          </button>
        </td>
      </tr>
    `);
  });

  updateSummary(subtotal, gstTotal);
}

function updateSummary(subtotal, gstTotal) {
  let discount = parseFloat($('#discount').val()) || 0;
  let totalBeforeDiscount = subtotal + gstTotal;
  let discountAmount = (totalBeforeDiscount * discount) / 100;
  let grandTotal = totalBeforeDiscount - discountAmount;
  
  // Round off logic
  let finalTotal = Math.round(grandTotal);
  let roundOff = finalTotal - grandTotal;
  
  $('#total_amount').text(subtotal.toFixed(2));
  $('#total_gst').text(gstTotal.toFixed(2));
  $('#discount_amount').text(`- ₹${discountAmount.toFixed(2)}`);
  
  // Handle round off display
  if (Math.abs(roundOff) > 0) {
    $('#round_off_item').removeClass('hidden');
    $('#round_off').text(`₹${roundOff.toFixed(2)}`);
  } else {
    $('#round_off_item').addClass('hidden');
  }
  
  $('#final_total').text(finalTotal.toFixed(2));
}

// Add item
$('.add-item-btn').click(function() {
  let item = {
    id: $(this).data('id'),
    name: $(this).data('name'),
    price: parseFloat($(this).data('price')),
    gst: parseFloat($(this).data('gst')),
    qty: 1
  };
  
  let exists = false;
  orderItems.forEach(i => { 
    if (i.id == item.id) { 
      i.qty += 1; 
      exists = true; 
    } 
  });
  
  if (!exists) orderItems.push(item);
  
  updateOrderTable();
  showToast(`${item.name} added to order`, 'success');
});

// Quantity controls
$(document).on('click', '.increase-qty', function() {
  let index = $(this).data('index');
  orderItems[index].qty += 1;
  updateOrderTable();
});

$(document).on('click', '.decrease-qty', function() {
  let index = $(this).data('index');
  if (orderItems[index].qty > 1) {
    orderItems[index].qty -= 1;
    updateOrderTable();
  }
});

$(document).on('change', '.qty-input', function() {
  let index = $(this).data('index');
  let newQty = parseInt($(this).val());
  if (newQty > 0) {
    orderItems[index].qty = newQty;
    updateOrderTable();
  }
});

// Remove item
$(document).on('click', '.remove-btn', function() {
  let index = $(this).data('index');
  orderItems.splice(index, 1);
  updateOrderTable();
  showToast('Item removed from order', 'error');
});

// Discount update
$('#discount').on('change', function() {
  let discount = parseFloat($(this).val()) || 0;
  if (discount < 0) $(this).val(0);
  if (discount > 100) $(this).val(100);
  
  let subtotal = orderItems.reduce((sum, item) => sum + (item.price * item.qty), 0);
  let gstTotal = orderItems.reduce((sum, item) => sum + (item.price * item.qty * item.gst) / 100, 0);
  updateSummary(subtotal, gstTotal);
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
  let customer_phone = $('#customer_phone').val().trim(); // Add this line
  let table_id = $('#table_id').val();
  let discount = $('#discount').val();
  let payment_status = $('#payment_status').val() || null;
  let payment_method = $('#payment_method').val() || null;
  let remarks = $('#remarks').val() || null;

  // Validation
  if (orderItems.length == 0) {
    showToast('Please add items to the order first', 'error');
    return;
  }
  
  if (customer_name === '') {
    showToast('Please enter customer name', 'error');
    $('#customer_name').focus();
    return;
  }

  if (payment_status === 'PAID' && !payment_method) {
    showToast('Please select payment method', 'error');
    return;
  }

  // Optional: Validate phone number format
  if (customer_phone && !/^[0-9]{10}$/.test(customer_phone)) {
    showToast('Please enter a valid 10-digit phone number', 'error');
    $('#customer_phone').focus();
    return;
  }

  // Disable button and show loading
  $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...');

  $.ajax({
    url: "{{ route('order.save') }}",
    method: 'POST',
    data: {
      _token: "{{ csrf_token() }}",
      customer_name,
      customer_phone, // Add this line
      table_id,
      discount,
      order_items: orderItems,
      payment_status,
      payment_method,
      remarks
    },
    success: function(response) {
      if (response.success) {
        showToast('Order saved successfully!', 'success');
        
        // Redirect to invoice URL in same window
        if (response.invoice_url) {
          setTimeout(() => {
            window.location.href = response.invoice_url;
          }, 1000);
        } else {
          setTimeout(() => {
            window.location.href = "{{ route('order.management.dashboard') }}";
          }, 1000);
        }
      } else {
        showToast(response.message || 'Error saving order', 'error');
        $('#saveOrderBtn').prop('disabled', false).html('<i class="fas fa-check-circle"></i> Save Order');
      }
    },
    error: function(xhr) {
      showToast(xhr.responseJSON?.message || 'An error occurred', 'error');
      $('#saveOrderBtn').prop('disabled', false).html('<i class="fas fa-check-circle"></i> Save Order');
    }
  });
});

// Filters
$('#vegFilter, #nameFilter').on('input change', function() {
  let type = $('#vegFilter').val().toLowerCase();
  let name = $('#nameFilter').val().toLowerCase();

  $('.food-card').each(function() {
    let itemType = $(this).data('type').toLowerCase();
    let itemName = $(this).data('name').toLowerCase();
    let matchType = !type || itemType === type;
    let matchName = !name || itemName.includes(name);
    $(this).toggle(matchType && matchName);
  });
});

// Initialize
$(document).ready(function() {
  $('#orderListTable').DataTable({
    searching: false,
    paging: false,
    info: false,
    ordering: false,
    responsive: true
  });
  
  updateOrderTable();
});
</script>
</body>
</html>