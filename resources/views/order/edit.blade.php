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

    .order-status-badge {
      padding: 5px 15px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 500;
    }

    .status-pending { background: #fef3c7; color: #92400e; }
    .status-in_progress { background: #dbeafe; color: #1e40af; }
    .status-completed { background: #d1fae5; color: #065f46; }

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
      padding: 12px 10px;
      font-size: 0.8rem;
      white-space: nowrap;
    }

    .order-table td {
      padding: 10px 8px;
      vertical-align: middle;
    }

    .order-table tbody tr:hover {
      background: #f8f9fa;
    }

    .qty-controls {
      display: flex;
      align-items: center;
      gap: 5px;
      justify-content: center;
    }

    .qty-btn {
      width: 28px;
      height: 28px;
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
      padding: 5px 10px;
      transition: all 0.3s;
    }

    .remove-btn:hover {
      background: #c0392b;
      transform: scale(1.05);
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
      padding: 12px 0;
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

    .gst-info-box {
      background: #f0fdf4;
      border: 1px solid #bbf7d0;
      border-radius: 10px;
      padding: 12px 15px;
      margin-bottom: 15px;
    }

    .non-gst-info-box {
      background: #fef3c7;
      border: 1px solid #fde68a;
      border-radius: 10px;
      padding: 12px 15px;
      margin-bottom: 15px;
    }

    .badge-info {
      background: #17a2b8;
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
      .order-table th { font-size: 0.7rem; padding: 8px 5px; }
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
            <h5>Edit Order #{{ $order->order_id ?? $order->id }}</h5>
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
                  <span class="order-status-badge status-{{ strtolower(str_replace(' ', '_', $order->order_status)) }}">
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
                      @if(isset($restaurant_gstin) && $restaurant_gstin)
                      <span class="food-badge gst-badge">
                        GST: {{ $restaurant_gst_percentage ?? 0 }}%
                      </span>
                      @endif
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

        <!-- Existing Order Items -->
        <div class="order-items-section">
          <h5 class="mb-3"><i class="fas fa-list me-2"></i>Existing Order Items</h5>
          <div class="table-responsive">
            <table class="table order-table">
              <thead>
                <tr>
                  <th>Item</th>
                  <th>Price (₹)</th>
                  <th>Disc %</th>
                  <th>Qty</th>
                  <th>Disc Price (₹)</th>
                  <th>Taxable (₹)</th>
                  @if(isset($restaurant_gstin) && $restaurant_gstin)
                  <th>GST (%)</th>
                  <th>GST Amt (₹)</th>
                  @endif
                  <th>Total (₹)</th>
                  @if($order->order_status == 'PENDING')
                  <th>Action</th>
                  @endif
                </tr>
              </thead>
              <tbody id="existingItems">
                @foreach($order->orderItems as $item)
                @php
                  $itemDiscount = $item->item_discount_percentage ?? 0;
                  $discountedPrice = $item->price - ($item->price * $itemDiscount / 100);
                  $taxableAmount = $discountedPrice * $item->quantity;
                  $gstAmount = ($taxableAmount * ($item->gst_rate ?? 0)) / 100;
                  $itemTotal = $taxableAmount + $gstAmount;
                @endphp
                <tr data-item-id="{{ $item->id }}">
                  <td><strong>{{ $item->subcategory->name ?? 'Unknown' }}</strong></td>
                  <td class="text-end">₹{{ number_format($item->price, 2) }}</td>
                  <td class="text-center">{{ $itemDiscount }}%</td>
                  <td class="text-center">{{ $item->quantity }}</td>
                  <td class="text-end">₹{{ number_format($discountedPrice, 2) }}</td>
                  <td class="text-end">₹{{ number_format($taxableAmount, 2) }}</td>
                  @if(isset($restaurant_gstin) && $restaurant_gstin)
                  <td class="text-center">{{ $item->gst_rate ?? 0 }}%</td>
                  <td class="text-end">₹{{ number_format($gstAmount, 2) }}</td>
                  @endif
                  <td class="text-end fw-bold">₹{{ number_format($itemTotal, 2) }}</td>
                  @if($order->order_status == 'PENDING')
                  <td class="text-center">
                    <button class="btn btn-sm btn-danger remove-btn delete-existing" 
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
                  <th>Price (₹)</th>
                  <th>Disc %</th>
                  <th>Qty</th>
                  <th>Disc Price (₹)</th>
                  <th>Taxable (₹)</th>
                  @if(isset($restaurant_gstin) && $restaurant_gstin)
                  <th>GST (%)</th>
                  <th>GST Amt (₹)</th>
                  @endif
                  <th>Total (₹)</th>
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
        <!-- GST Info Box -->
        @if(isset($restaurant_gstin) && $restaurant_gstin)
        <div class="gst-info-box">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <i class="fas fa-file-invoice-dollar text-success fa-lg"></i>
              <strong class="ml-2">GST Bill</strong>
            </div>
            <div>
              <span class="badge badge-success">GSTIN: {{ $restaurant_gstin }}</span>
              <span class="badge badge-info ml-1">GST: {{ $restaurant_gst_percentage ?? 0 }}%</span>
            </div>
          </div>
        </div>
        @else
        <div class="non-gst-info-box">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <i class="fas fa-receipt text-muted fa-lg"></i>
              <strong class="ml-2">Non-GST Bill</strong>
            </div>
            <div>
              <span class="badge badge-secondary">No GST Applicable</span>
            </div>
          </div>
        </div>
        @endif

        <!-- Order Summary -->
        <div class="summary-box">
          <h5 class="mb-3"><i class="fas fa-receipt me-2"></i>Order Summary</h5>
          
          <div class="summary-item">
            <span class="summary-label">Original Subtotal:</span>
            <span class="summary-value">₹<span id="original_subtotal">{{ number_format($original_subtotal, 2) }}</span></span>
          </div>
          
          <div class="summary-item">
            <span class="summary-label">Item Discount:</span>
            <span class="summary-value text-success">- ₹<span id="item_discount_total">0.00</span></span>
          </div>
          
          <div class="summary-item">
            <span class="summary-label">Taxable Value:</span>
            <span class="summary-value">₹<span id="total_taxable">{{ number_format($total_taxable, 2) }}</span></span>
          </div>
          
          @if(isset($restaurant_gstin) && $restaurant_gstin)
          <div class="summary-item">
            <span class="summary-label">GST Total ({{ $restaurant_gst_percentage ?? 0 }}%):</span>
            <span class="summary-value">₹<span id="total_gst">{{ number_format($total_gst, 2) }}</span></span>
          </div>
          @endif
          
          <div class="summary-item">
            <span class="summary-label">Order Discount:</span>
            <span class="summary-value text-success">- ₹<span id="order_discount_amount">0.00</span></span>
          </div>
          
          <div class="summary-item hidden" id="round_off_item">
            <span class="summary-label">Round Off:</span>
            <span class="summary-value" id="round_off">₹0.00</span>
          </div>
          
          <div class="summary-total">
            <span class="total-label">Final Total:</span>
            <span class="total-value">₹<span id="final_total">{{ number_format($final_total, 2) }}</span></span>
          </div>

          {{-- <div class="mt-4">
            <label class="form-label mb-2">Order Discount (%)</label>
            <input type="number" class="form-control discount-input" id="order_discount" 
                   value="{{ $discount_percent }}" min="0" max="100" step="1">
            <div class="discount-note">
              <i class="fas fa-info-circle"></i> Discount applies to (Taxable Value + GST)
            </div>
          </div> --}}
        </div>

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
            <select class="form-control" id="payment_method">
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

<div class="toast-notification" id="toastNotification">
  <i class="fas fa-check-circle" style="color: #27ae60;"></i>
  <span id="toastMessage">Success!</span>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
@include('includes.script')

<script>
// Global variables
let newOrderItems = [];
let existingItemsData = [];
let isGstRegistered = {{ isset($restaurant_gstin) && $restaurant_gstin ? 'true' : 'false' }};
let restaurantGstPercentage = {{ $restaurant_gst_percentage ?? 0 }};

// Store existing items data from PHP
@php
$existingItemsArray = [];
foreach ($order->orderItems as $item) {
    $itemDiscount = $item->item_discount_percentage ?? 0;
    $discountedPrice = $item->price - ($item->price * $itemDiscount / 100);
    $taxableAmount = $discountedPrice * $item->quantity;
    $gstAmount = ($taxableAmount * ($item->gst_rate ?? 0)) / 100;
    
    $existingItemsArray[] = [
        'id' => $item->id,
        'subcategory_id' => $item->subcategory_id,
        'price' => floatval($item->price),
        'quantity' => intval($item->quantity),
        'gst_rate' => floatval($item->gst_rate ?? 0),
        'item_discount' => $itemDiscount,
        'taxable_amount' => $taxableAmount,
        'gst_amount' => $gstAmount,
        'total_amount' => $taxableAmount + $gstAmount
    ];
}
@endphp

let existingSubtotal = {{ $original_subtotal ?? 0 }};
let existingTaxable = {{ $total_taxable ?? 0 }};
let existingGst = {{ $total_gst ?? 0 }};

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
    // Calculate new items totals
    let newOriginalSubtotal = 0;
    let newTaxable = 0;
    let newGst = 0;
    let newItemDiscount = 0;
    
    newOrderItems.forEach(item => {
        let details = calculateItemDetails(item.price, item.qty, item.itemDiscount || 0);
        newOriginalSubtotal += item.price * item.qty;
        newTaxable += details.taxableAmount;
        newGst += details.gstAmount;
        newItemDiscount += details.itemDiscountAmount;
    });
    
    // Combine with existing
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
        $('#round_off_item').removeClass('hidden');
        $('#round_off').text(`₹${roundOff.toFixed(2)}`);
    } else {
        $('#round_off_item').addClass('hidden');
    }
    
    $('#final_total').text(finalTotal.toFixed(2));
    
    // Auto-fill amount paid when status is PAID
    if ($('#payment_status').val() === 'PAID' && !$('#amount_paid').val()) {
        $('#amount_paid').val(finalTotal.toFixed(2));
    }
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
                <td><strong>${escapeHtml(item.name)}</strong></td>
                <td class="text-end">₹${item.price.toFixed(2)}</td>
                <td class="text-center">
                    <input type="number" class="form-control form-control-sm item-discount-input" 
                           data-index="${index}" value="${item.itemDiscount || 0}" 
                           min="0" max="100" step="1" style="width: 65px;">
                    <span>%</span>
                </td>
                <td class="text-center">
                    <div class="qty-controls">
                        <button class="qty-btn decrease-qty" data-index="${index}">-</button>
                        <input type="number" class="qty-input" value="${item.qty}" min="1" data-index="${index}">
                        <button class="qty-btn increase-qty" data-index="${index}">+</button>
                    </div>
                </td>
                <td class="text-end">₹${details.discountedPricePerItem.toFixed(2)}</td>
                <td class="text-end">₹${details.taxableAmount.toFixed(2)}`;
        
        if (isGstRegistered) {
            row += `<td class="text-center">${details.gstRate}%
                    <td class="text-end">₹${details.gstAmount.toFixed(2)}`;
        }
        
        row += `<td class="text-end fw-bold">₹${details.totalAmount.toFixed(2)}
                <td class="text-center">
                    <button class="btn btn-sm btn-danger remove-btn delete-new" data-index="${index}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
              </tr>`;
        tbody.append(row);
    });
    
    updateSummary();
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

$(document).ready(function() {
    console.log('Edit Order Page Loaded');
    console.log('GST Registered:', isGstRegistered);
    console.log('GST Percentage:', restaurantGstPercentage);
    
    // Add new item button click
    $(document).on('click', '.add-item-btn', function(e) {
        e.preventDefault();
        
        let itemId = $(this).data('id');
        let itemName = $(this).data('name');
        let itemPrice = parseFloat($(this).data('price'));
        let itemDiscount = parseFloat($(this).data('discount')) || 0;
        
        let existingItem = newOrderItems.find(i => i.id === itemId);
        
        if (existingItem) {
            existingItem.qty += 1;
            showToast(`${itemName} quantity increased to ${existingItem.qty}`, false);
        } else {
            newOrderItems.push({
                id: itemId,
                name: itemName,
                price: itemPrice,
                qty: 1,
                itemDiscount: itemDiscount
            });
            
            if (itemDiscount > 0) {
                showToast(`${itemName} added with ${itemDiscount}% discount`, false);
            } else {
                showToast(`${itemName} added to order`, false);
            }
        }
        
        updateNewItemsTable();
    });
    
    // Item discount change for new items
    $(document).on('change', '.item-discount-input', function() {
        let index = $(this).data('index');
        let newDiscount = parseFloat($(this).val()) || 0;
        if (newDiscount < 0) newDiscount = 0;
        if (newDiscount > 100) newDiscount = 100;
        
        if (newOrderItems[index]) {
            newOrderItems[index].itemDiscount = newDiscount;
            $(this).val(newDiscount);
            updateNewItemsTable();
            showToast(`Discount updated to ${newDiscount}% for ${newOrderItems[index].name}`, false);
        }
    });
    
    // Increase quantity for new items
    $(document).on('click', '.increase-qty', function() {
        let index = $(this).closest('tr').data('index');
        if (newOrderItems[index]) {
            newOrderItems[index].qty += 1;
            updateNewItemsTable();
        }
    });
    
    // Decrease quantity for new items
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
                showToast(`${removedItem.name} removed from order`, true);
            }
        }
    });
    
    // Quantity input change for new items
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
        showToast(`${removedItem.name} removed from order`, true);
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
                    showToast('Item removed successfully', false);
                    location.reload();
                } else {
                    showToast(res.message || 'Error removing item', true);
                    button.prop('disabled', false).html('<i class="fas fa-trash"></i>');
                }
            },
            error: function() {
                showToast('Error removing item', true);
                button.prop('disabled', false).html('<i class="fas fa-trash"></i>');
            }
        });
    });
    
    // Order discount update
    $('#order_discount').on('input change', function() {
        let discount = parseFloat($(this).val()) || 0;
        if (discount < 0) $(this).val(0);
        if (discount > 100) $(this).val(100);
        updateSummary();
    });
    
    // Payment status change
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
            let itemType = $(this).data('type') || '';
            let itemName = $(this).data('name') || '';
            let matchType = !type || itemType === type;
            let matchName = !name || itemName.includes(name);
            $(this).toggle(matchType && matchName);
        });
    });
    
    // Save Order
    $('#saveOrderBtn').click(function() {
        let customer_phone = $('#customer_phone').val().trim();
        let orderDiscount = $('#order_discount').val() || 0;
        let payment_status = $('#payment_status').val();
        let payment_method = $('#payment_method').val();
        let amount_paid = $('#amount_paid').val();
        let remarks = $('#remarks').val();
        
        if (payment_status === 'PAID' && !payment_method) {
            showToast('Please select payment method for paid orders', true);
            return;
        }
        
        if (customer_phone && !/^[0-9]{10}$/.test(customer_phone)) {
            showToast('Please enter a valid 10-digit phone number', true);
            $('#customer_phone').focus();
            return;
        }
        
        let data = {
            _token: "{{ csrf_token() }}",
            discount: orderDiscount,
            payment_status: payment_status,
            payment_method: payment_method,
            amount_paid: amount_paid,
            customer_phone: customer_phone,
            remarks: remarks,
            is_gst_registered: isGstRegistered,
            gst_percentage: restaurantGstPercentage
        };
        
        if (newOrderItems.length > 0) {
            data.order_items = newOrderItems.map(item => ({
                id: item.id,
                name: item.name,
                price: item.price,
                qty: item.qty,
                item_discount: item.itemDiscount || 0
            }));
        }
        
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...');
        
        $.ajax({
            url: "{{ route('order.update', $order->id) }}",
            type: "POST",
            data: data,
            success: function(response) {
                if (response.success) {
                    showToast('Order updated successfully!', false);
                    
                    if (response.redirect_url && (payment_status === 'PAID' || payment_status === 'MISCORDER')) {
                        window.location.href = response.redirect_url;
                    } else {
                        location.reload();
                    }
                } else {
                    showToast(response.message || 'Error saving order', true);
                    $('#saveOrderBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Save Changes');
                }
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'An error occurred', true);
                $('#saveOrderBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Save Changes');
            }
        });
    });
    
    // Initial update
    updateSummary();
});
</script>
</body>
</html>