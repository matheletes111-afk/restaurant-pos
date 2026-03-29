<!DOCTYPE html>
<html lang="en">
<head>
  <title>Admin - Edit Order</title>
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

    .customer-section input, .customer-section select {
      border: 2px solid #e0e6ed;
      border-radius: 8px;
      padding: 10px 15px;
      transition: all 0.3s;
    }

    .customer-section input:focus, .customer-section select:focus {
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

    .order-status-badge {
      padding: 5px 15px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 500;
    }

    .status-pending { background: #fef3c7; color: #92400e; }
    .status-confirmed { background: #d1fae5; color: #065f46; }
    .status-paid { background: #dbeafe; color: #1e40af; }
    .status-miscorder { background: #fce7f3; color: #9d174d; }

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

    .order-table {
      border-radius: 8px;
      overflow: hidden;
      width: 100%;
    }

    .order-table thead {
      background: linear-gradient(135deg, var(--primary), #34495e);
    }

    .order-table th {
      border: none;
      color: white;
      font-weight: 500;
      padding: 12px 15px;
      font-size: 0.9rem;
    }

    .order-table tbody tr {
      transition: background 0.3s;
    }

    .order-table tbody tr:hover {
      background: #f8f9fa;
    }

    .qty-controls {
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .qty-btn {
      width: 30px;
      height: 30px;
      border: 1px solid #e0e6ed;
      background: white;
      border-radius: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s;
    }

    .qty-btn:hover {
      background: #f8f9fa;
      border-color: var(--secondary);
    }

    .qty-input {
      width: 50px;
      text-align: center;
      border: 1px solid #e0e6ed;
      border-radius: 4px;
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
      
      .qty-controls {
        flex-direction: column;
        gap: 3px;
      }
      
      .qty-input {
        width: 40px;
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
            <h5>Edit Order #{{ $order->id }}</h5>
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ route('order.management.dashboard') }}">Order Management</a></li>
              <li class="breadcrumb-item active" aria-current="page">Edit Order</li>
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
        <label>Customer Name</label>
        <input type="text" class="form-control" id="customer_name" 
               value="{{ $order->customer_name }}" readonly>
      </div>
      <div class="col-md-6 mb-3">
        <label>Customer Phone</label>
        <input type="text" class="form-control" id="customer_phone" 
               value="{{ $order->customer_phone ?? '' }}" placeholder="Phone number">
      </div>
    </div>
    <div class="row">
      <div class="col-md-6 mb-3">
        <label>Order Status</label>
        <div>
          <span class="order-status-badge status-{{ strtolower($order->order_status) }}">
            {{ strtoupper($order->order_status) }}
          </span>
        </div>
      </div>
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
              <i class="fas fa-takeout-box"></i>
              Takeaway
            </div>
            <input type="hidden" id="table_id" value="">
          @endif
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6 mb-3">
        <label>Order Date</label>
        <div class="form-control bg-light">
          {{ $order->created_at->format('d M Y, h:i A') }}
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
        <!-- Existing Items -->
        <div class="order-items-section">
          <h5 class="mb-3"><i class="fas fa-list me-2"></i>Existing Order Items</h5>
          <div class="table-responsive">
            <table class="table order-table">
              <thead>
                <tr>
                  <th>Item</th>
                  <th>Price</th>
                  <th>Qty</th>
                  <th>Total</th>
                  @if(strtoupper($order->order_status) == 'PENDING')
                  <th>Action</th>
                  @endif
                </tr>
              </thead>
              <tbody id="existingItems">
                @php $existingTotal = 0; $existingGst = 0; @endphp
                @foreach($order->orderItems as $item)
                @php
                  $gstAmt = ($item->price * $item->quantity * ($item->gst_rate ?? 0)) / 100;
                  $itemTotal = ($item->price * $item->quantity) + $gstAmt;
                  $existingTotal += $item->price * $item->quantity;
                  $existingGst += $gstAmt;
                @endphp
                <tr data-item-id="{{ $item->id }}">
                  <td>
                    <strong>{{ $item->subcategory->name ?? 'Unknown' }}</strong>
                    <div><small class="text-muted">GST: {{ $item->gst_rate ?? 0 }}%</small></div>
                  </td>
                  <td>₹{{ number_format($item->price, 2) }}</td>
                  <td>{{ $item->quantity }}</td>
                  <td>₹{{ number_format($itemTotal, 2) }}</td>
                  @if(strtoupper($order->order_status) == 'PENDING')
                  <td>
                    <button class="btn btn-sm remove-btn delete-existing" 
                            data-id="{{ $item->id }}">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                  @endif
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          @if(count($order->orderItems) === 0)
          <div class="empty-state">
            <i class="fas fa-shopping-cart"></i>
            <h5>No Items in Order</h5>
            <p>Add items from the menu above</p>
          </div>
          @endif
        </div>

        <!-- New Items (Dynamic) -->
        <div class="order-items-section" id="newItemsSection">
          <h5 class="mb-3"><i class="fas fa-plus-circle me-2"></i>Newly Added Items</h5>
          <div class="table-responsive">
            <table class="table order-table" id="newItemsTable">
              <thead>
                <tr>
                  <th>Item</th>
                  <th>Price</th>
                  <th>Qty</th>
                  <th>Total</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="newItemsBody">
                <!-- Dynamic content -->
              </tbody>
            </table>
          </div>
          <div id="emptyNewItems" class="empty-state">
            <i class="fas fa-plus-circle"></i>
            <h5>No New Items Added</h5>
            <p>Add items from the menu above</p>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <!-- Order Summary -->
        <div class="summary-box">
          <h5 class="mb-3"><i class="fas fa-receipt me-2"></i>Order Summary</h5>
          
          <div class="summary-item">
            <span class="summary-label">Subtotal:</span>
            <span class="summary-value">₹<span id="total_amount">{{ number_format($existingTotal, 2) }}</span></span>
          </div>
          
          <div class="summary-item">
            <span class="summary-label">GST Total:</span>
            <span class="summary-value">₹<span id="total_gst">{{ number_format($existingGst, 2) }}</span></span>
          </div>
          
          <div class="summary-item">
            <span class="summary-label">Discount:</span>
            <span class="summary-value text-success" id="discount_amount">- ₹0.00</span>
          </div>
          
          <div class="summary-total">
            <span class="total-label">Final Total:</span>
            <span class="total-value">₹<span id="final_total">{{ number_format($order->grand_total, 2) }}</span></span>
          </div>

          <div class="summary-item" id="round_off_item" style="display: none;">
            <span class="summary-label">Round Off:</span>
            <span class="summary-value" id="round_off">₹0.00</span>
          </div>

          <div class="mt-4">
            <label class="form-label mb-2">Discount Percentage</label>
            <div class="input-group">
              <input type="number" class="form-control discount-input" id="discount" 
                     value="{{ $order->discount ?? 0 }}" min="0" max="100">
              <span class="input-group-text">%</span>
            </div>
          </div>
        </div>

        <!-- Payment Section -->
        @if(in_array(auth()->user()->role_type, ["Manager", "Cashier", "ADMIN"]))
        <div class="payment-section">
          <h5 class="mb-3"><i class="fas fa-credit-card me-2"></i>Payment Details</h5>
          
          <div class="mb-3">
            <label class="form-label">Payment Status</label>
            <select class="form-control" id="payment_status">
              <option value="PENDING" {{ $order->payment_status == 'PENDING' ? 'selected' : '' }}>Pending</option>
              <option value="PAID" {{ $order->payment_status == 'PAID' ? 'selected' : '' }}>Paid</option>
              <option value="MISCORDER" {{ $order->payment_status == 'MISCORDER' ? 'selected' : '' }}>Miscorder (Ate but not paid)</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Payment Method</label>
            <select class="form-control" id="payment_method" {{ $order->payment_status == 'PAID' ? 'required' : '' }}>
              <option value="">-- Select Method --</option>
              @foreach($payment_methods as $method)
                <option value="{{ $method }}" {{ $order->payment_method == $method ? 'selected' : '' }}>
                  {{ $method }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Amount Paid</label>
            <input type="number" class="form-control" id="amount_paid" 
                   value="{{ number_format($order->amount_paid ?? 0, 2) }}" 
                   min="0" step="0.01">
          </div>

          <div class="mb-3">
            <label class="form-label">Remarks</label>
            <textarea class="form-control" id="remarks" rows="2" placeholder="Any special instructions...">{{ $order->remarks }}</textarea>
          </div>
        </div>
        @endif

        <!-- Save Button -->
        <div class="d-grid mt-4 gap-2">
          <button class="btn save-btn" id="saveOrderBtn">
            <i class="fas fa-save"></i>
            Save Changes
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
let orderItems = []; // Stores newly added items (not yet saved to DB)
let existingSubtotal = parseFloat('{{ $existingTotal }}') || 0;
let existingGst = parseFloat('{{ $existingGst }}') || 0;

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

function updateSummary() {
  // Calculate new items totals
  let newSubtotal = 0;
  let newGst = 0;
  
  orderItems.forEach(item => {
    let lineSubtotal = item.price * item.qty;
    let lineGst = (lineSubtotal * (item.gst || 0)) / 100;
    newSubtotal += lineSubtotal;
    newGst += lineGst;
  });
  
  // Combine with existing
  let totalSubtotal = existingSubtotal + newSubtotal;
  let totalGst = existingGst + newGst;
  
  let discount = parseFloat($('#discount').val()) || 0;
  let totalBeforeDiscount = totalSubtotal + totalGst;
  let discountAmount = (totalBeforeDiscount * discount) / 100;
  let grandTotal = totalBeforeDiscount - discountAmount;
  
  // Round off logic
  let finalAmount = Math.round(grandTotal);
  let roundOff = finalAmount - grandTotal;

  // Update UI
  $('#total_amount').text(totalSubtotal.toFixed(2));
  $('#total_gst').text(totalGst.toFixed(2));
  $('#discount_amount').text(`- ₹${discountAmount.toFixed(2)}`);
  
  // Add round off display
  let roundOffElement = $('#round_off');
  if (Math.abs(roundOff) > 0) {
    if (!roundOffElement.length) {
      // Add round off row if it doesn't exist
      $('.summary-item:last').before(`
        <div class="summary-item" id="round_off_item">
          <span class="summary-label">Round Off:</span>
          <span class="summary-value" id="round_off">₹${roundOff.toFixed(2)}</span>
        </div>
      `);
    } else {
      $('#round_off').text(`₹${roundOff.toFixed(2)}`);
      $('#round_off_item').show();
    }
  } else {
    $('#round_off_item').hide();
  }
  
  $('#final_total').text(finalAmount.toFixed(2));
  
  // Auto-fill amount paid when status is PAID
  if ($('#payment_status').val() === 'PAID' && !$('#amount_paid').val()) {
    $('#amount_paid').val(finalAmount.toFixed(2));
  }
  
  return {
    subtotal: totalSubtotal,
    gst: totalGst,
    grandTotal: grandTotal,
    finalAmount: finalAmount,
    roundOff: roundOff
  };
}

function updateNewItemsTable() {
  let tbody = $('#newItemsBody');
  let emptyState = $('#emptyNewItems');
  
  if (orderItems.length === 0) {
    tbody.empty();
    emptyState.show();
    return;
  }
  
  emptyState.hide();
  tbody.empty();
  
  orderItems.forEach((item, index) => {
    let lineSubtotal = item.price * item.qty;
    let lineGst = (lineSubtotal * (item.gst || 0)) / 100;
    let itemTotal = lineSubtotal + lineGst;
    
    tbody.append(`
      <tr data-index="${index}">
        <td>
          <strong>${item.name}</strong>
          <div><small class="text-muted">GST: ${item.gst}%</small></div>
        </td>
        <td>₹${item.price.toFixed(2)}</td>
        <td>
          <div class="qty-controls">
            <button class="qty-btn decrease-qty" data-index="${index}">
              <i class="fas fa-minus"></i>
            </button>
            <input type="number" class="qty-input" value="${item.qty}" min="1" data-index="${index}">
            <button class="qty-btn increase-qty" data-index="${index}">
              <i class="fas fa-plus"></i>
            </button>
          </div>
        </td>
        <td>₹${itemTotal.toFixed(2)}</td>
        <td>
          <button class="btn btn-sm remove-btn delete-new" data-index="${index}">
            <i class="fas fa-trash"></i>
          </button>
        </td>
      </tr>
    `);
  });
  
  updateSummary();
}

// Payment status change - auto-fill amount
$('#payment_status').on('change', function() {
  if ($(this).val() === 'PAID') {
    let finalTotal = parseFloat($('#final_total').text());
    $('#amount_paid').val(finalTotal.toFixed(2));
  }
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

// Add new item to local array (not saved to DB yet)
$('.add-item-btn').click(function() {
  let item = {
    id: $(this).data('id'),
    name: $(this).data('name'),
    price: parseFloat($(this).data('price')),
    gst: parseFloat($(this).data('gst')),
    qty: 1
  };
  
  // Check if item already exists in new items
  let existingIndex = orderItems.findIndex(i => i.id === item.id);
  if (existingIndex > -1) {
    orderItems[existingIndex].qty += 1;
  } else {
    orderItems.push(item);
  }
  
  updateNewItemsTable();
  showToast(`${item.name} added to order`, 'success');
});

// Quantity controls for new items
$(document).on('click', '.increase-qty', function() {
  let index = $(this).data('index');
  orderItems[index].qty += 1;
  updateNewItemsTable();
});

$(document).on('click', '.decrease-qty', function() {
  let index = $(this).data('index');
  if (orderItems[index].qty > 1) {
    orderItems[index].qty -= 1;
    updateNewItemsTable();
  }
});

$(document).on('change', '.qty-input', function() {
  let index = $(this).data('index');
  let newQty = parseInt($(this).val());
  if (newQty > 0) {
    orderItems[index].qty = newQty;
    updateNewItemsTable();
  }
});

// Delete new item from local array
$(document).on('click', '.delete-new', function() {
  let index = $(this).data('index');
  let itemName = orderItems[index].name;
  orderItems.splice(index, 1);
  updateNewItemsTable();
  showToast(`${itemName} removed`, 'error');
});

// Delete existing item from DB
$(document).on('click', '.delete-existing', function() {
  if (!confirm('Are you sure you want to remove this item?')) return;
  
  let itemId = $(this).data('id');
  let button = $(this);
  
  button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
  
  $.ajax({
    url: "{{ route('order.update', $order->id) }}",
    type: "POST",
    data: {
      _token: "{{ csrf_token() }}",
      delete_item_id: itemId
    },
    success: function(res) {
      if (res.success) {
        showToast('Item removed successfully', 'success');
        location.reload();
      } else {
        showToast(res.message || 'Error removing item', 'error');
        button.prop('disabled', false).html('<i class="fas fa-trash"></i>');
      }
    },
    error: function() {
      showToast('Error removing item', 'error');
      button.prop('disabled', false).html('<i class="fas fa-trash"></i>');
    }
  });
});

// Discount update
$('#discount').on('input', function() {
  let discount = parseFloat($(this).val()) || 0;
  if (discount < 0) $(this).val(0);
  if (discount > 100) $(this).val(100);
  
  updateSummary();
});

// Save Order Changes - Update this part
$('#saveOrderBtn').click(function() {
  let discount = $('#discount').val();
  let payment_status = $('#payment_status').val();
  let payment_method = $('#payment_method').val();
  let amount_paid = $('#amount_paid').val();
  let customer_phone = $('#customer_phone').val().trim(); // Add this line
  let remarks = $('#remarks').val();

  // Validation - only require payment method for PAID status
  if (payment_status === 'PAID' && !payment_method) {
    showToast('Please select payment method for paid orders', 'error');
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

  // Prepare data for new items
  let data = {
    _token: "{{ csrf_token() }}",
    discount: discount,
    payment_status: payment_status,
    payment_method: payment_method,
    amount_paid: amount_paid,
    customer_phone: customer_phone, // Add this line
    remarks: remarks
  };

  // Add new order items if any
  if (orderItems.length > 0) {
    data.order_items = orderItems;
  }

  $.ajax({
    url: "{{ route('order.update', $order->id) }}",
    type: "POST",
    data: data,
    success: function(response) {
      if (response.success) {
        showToast('Order updated successfully!', 'success');
        
        // Update totals on page if not redirecting
        if (response.final_total) {
          $('#final_total').text(response.final_total);
        }
        if (response.amount_paid) {
          $('#amount_paid').val(response.amount_paid);
        }
        
        // Redirect for both PAID and MISCORDER
        if (response.redirect_url && (payment_status === 'PAID' || payment_status === 'MISCORDER')) {
          // Open invoice in SAME window (not new tab)
          window.location.href = response.redirect_url;
        } else {
          // Reload page to reflect changes for other statuses
          location.reload();
        }
      } else {
        showToast(response.message || 'Error saving order', 'error');
        $('#saveOrderBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Save Changes');
      }
    },
    error: function(xhr) {
      showToast(xhr.responseJSON?.message || 'An error occurred', 'error');
      $('#saveOrderBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Save Changes');
    }
  });
});

// Initialize
$(document).ready(function() {
  updateSummary();
  updateNewItemsTable();
});
</script>
</body>
</html>