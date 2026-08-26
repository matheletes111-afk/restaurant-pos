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
      --primary: #0f172a;
      --secondary: #009d1a;
      --success: #10b981;
      --danger: #ef4444;
      --warning: #f59e0b;
      --light: #f8fafc;
      --dark: #0f172a;
      --gray: #64748b;
      --light-gray: #f1f5f9;
    }

    body {
      background-color: #f8fafc;
      font-family: 'Public Sans', 'Segoe UI', sans-serif;
      color: #1e293b;
    }

    .page-header {
      background: white;
      padding: 24px;
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(15, 23, 42, 0.02);
      margin-bottom: 25px;
      border: 1px solid rgba(0, 0, 0, 0.03);
    }

    .page-header h5 {
      color: var(--primary);
      font-weight: 800;
      font-size: 1.3rem;
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
      font-weight: 600;
    }

    .breadcrumb-item.active {
      color: var(--gray);
    }

    .card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 8px 30px rgba(15, 23, 42, 0.03);
      margin-bottom: 25px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      overflow: hidden;
      background: white;
      border: 1px solid rgba(0, 0, 0, 0.03);
    }

    .card:hover {
      transform: translateY(-2px);
      box-shadow: 0 12px 35px rgba(15, 23, 42, 0.06);
    }

    .card-header {
      background: #f8fafc;
      border-bottom: 1px solid rgba(0, 0, 0, 0.05);
      padding: 18px 24px;
    }

    .card-header h5 {
      margin: 0;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 10px;
      color: #0f172a !important;
      font-size: 1.05rem;
    }

    .table-badge {
      background: rgba(0, 157, 26, 0.1);
      color: var(--secondary);
      padding: 6px 14px;
      border-radius: 30px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-weight: 700;
      font-size: 0.85rem;
      border: 1px solid rgba(0, 157, 26, 0.2);
    }

    .takeaway-badge {
      background: rgba(16, 185, 129, 0.1);
      color: var(--success);
      padding: 6px 14px;
      border-radius: 30px;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-weight: 700;
      font-size: 0.85rem;
      border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .filter-section {
      background: white;
      padding: 20px;
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(15, 23, 42, 0.02);
      margin: 20px 0;
      border: 1px solid rgba(0, 0, 0, 0.03);
    }

    .filter-section select, .filter-section input {
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      padding: 10px 16px;
      font-size: 0.9rem;
      background-color: #f8fafc;
      transition: all 0.2s ease;
    }

    .filter-section select:focus, .filter-section input:focus {
      background-color: white;
      border-color: var(--secondary);
      outline: none;
      box-shadow: 0 0 0 3px rgba(0, 157, 26, 0.1);
    }

    .nav-tabs {
      border: none;
      background: #f1f5f9;
      padding: 6px;
      border-radius: 12px;
      display: flex;
      flex-wrap: nowrap;
      overflow-x: auto;
      gap: 6px;
      margin: 20px 20px 0;
    }

    .nav-tabs::-webkit-scrollbar {
      display: none;
    }

    .nav-tabs .nav-link {
      border: none !important;
      color: var(--gray);
      font-weight: 700;
      font-size: 0.85rem;
      padding: 10px 20px;
      border-radius: 8px !important;
      transition: all 0.2s ease;
      background: transparent;
      white-space: nowrap;
    }

    .nav-tabs .nav-link:hover {
      color: var(--secondary);
      background: rgba(0, 157, 26, 0.05);
    }

    .nav-tabs .nav-link.active {
      background: white !important;
      color: var(--secondary) !important;
      box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05) !important;
    }

    .food-card {
      margin-bottom: 20px;
    }

    .food-item {
      border: 1px solid #e2e8f0;
      border-radius: 14px;
      padding: 20px;
      text-align: center;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      background: white;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .food-item:hover {
      border-color: rgba(0, 157, 26, 0.2);
      box-shadow: 0 10px 25px rgba(0, 157, 26, 0.08);
      transform: translateY(-3px);
    }

    .food-item h6 {
      color: var(--primary);
      font-weight: 800;
      font-size: 0.95rem;
      margin-bottom: 12px;
      line-height: 1.4;
    }

    .food-item .price {
      color: var(--secondary);
      font-size: 1.25rem;
      font-weight: 800;
      margin: 10px 0 15px;
    }

    .food-badge {
      font-size: 0.65rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.02em;
      padding: 4px 10px;
      border-radius: 20px;
      display: inline-block;
      margin-bottom: 8px;
    }

    .veg-badge {
      background: #d1fae5;
      color: #065f46;
    }

    .nonveg-badge {
      background: #fee2e2;
      color: #991b1b;
    }

    .gst-badge {
      background: #eff6ff;
      color: #1d4ed8;
    }

    .discount-badge {
      background: #fef3c7;
      color: #92400e;
    }

    .add-item-btn {
      background: var(--light-gray);
      color: var(--primary);
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 10px 20px;
      font-weight: 700;
      font-size: 0.85rem;
      transition: all 0.2s ease;
      width: 100%;
    }

    .add-item-btn:hover {
      background: linear-gradient(135deg, #009d1a 0%, #00bc20 100%);
      color: white;
      border-color: transparent;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(0, 157, 26, 0.2);
    }

    .order-items-section {
      background: white;
      border-radius: 16px;
      padding: 24px;
      box-shadow: 0 4px 20px rgba(15, 23, 42, 0.02);
      margin-top: 30px;
      border: 1px solid rgba(0, 0, 0, 0.03);
    }

    .order-items-section h5 {
      font-weight: 800;
      color: var(--primary);
    }

    .table-responsive {
      overflow-x: auto;
      border-radius: 12px;
      border: 1px solid #e2e8f0;
    }

    #orderListTable {
      width: 100% !important;
      min-width: 1000px;
      border-collapse: collapse;
    }

    #orderListTable thead {
      background: #0f172a;
    }

    #orderListTable th {
      border: none;
      color: white;
      font-weight: 700;
      padding: 14px 16px;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      white-space: nowrap;
    }

    #orderListTable td {
      padding: 14px 16px;
      vertical-align: middle;
      border-bottom: 1px solid #e2e8f0;
      font-size: 0.85rem;
    }

    #orderListTable tbody tr:hover {
      background: #f8fafc;
    }

    .qty-input {
      width: 60px;
      text-align: center;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 6px;
      font-weight: 700;
      background-color: #f8fafc;
    }

    .qty-input:focus {
      outline: none;
      border-color: var(--secondary);
      background-color: white;
    }

    .remove-btn {
      background: rgba(239, 68, 68, 0.1);
      color: var(--danger);
      border: 1px solid rgba(239, 68, 68, 0.2);
      border-radius: 8px;
      width: 32px;
      height: 32px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s ease;
      cursor: pointer;
    }

    .remove-btn:hover {
      background: var(--danger);
      color: white;
      transform: scale(1.05);
      box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2);
    }

    .item-discount-input {
      width: 70px;
      text-align: center;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 6px;
      font-size: 0.85rem;
      font-weight: 600;
      background-color: #f8fafc;
    }

    .item-discount-input:focus {
      outline: none;
      border-color: var(--secondary);
      background-color: white;
    }

    .summary-box {
      background: white;
      border-radius: 16px;
      padding: 24px;
      box-shadow: 0 8px 30px rgba(15, 23, 42, 0.04);
      margin-top: 25px;
      position: relative;
      border: 1px solid rgba(0, 0, 0, 0.03);
    }

    .summary-box::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #009d1a 0%, #00bc20 100%);
    }

    .summary-box h5 {
      font-weight: 800;
      color: var(--primary);
    }

    .summary-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 12px 0;
      border-bottom: 1px dashed #e2e8f0;
    }

    .summary-item:last-of-type {
      border-bottom: none;
    }

    .summary-label {
      color: var(--gray);
      font-size: 0.85rem;
      font-weight: 600;
    }

    .summary-value {
      color: var(--primary);
      font-weight: 700;
      font-size: 0.95rem;
    }

    .summary-total {
      background: #0f172a;
      color: white;
      padding: 16px 20px;
      border-radius: 12px;
      margin-top: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 4px 15px rgba(15, 23, 42, 0.1);
    }

    .total-label {
      font-size: 0.9rem;
      font-weight: 700;
    }

    .total-value {
      font-size: 1.4rem;
      font-weight: 800;
    }

    .discount-input {
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      padding: 10px 14px;
      text-align: center;
      width: 100%;
      background-color: #f8fafc;
      font-weight: 700;
    }

    .discount-input:focus {
      outline: none;
      border-color: var(--secondary);
      background-color: white;
    }

    .gst-info-box {
      background: #ecfdf5;
      border: 1px solid #a7f3d0;
      border-radius: 12px;
      padding: 16px;
      margin-bottom: 20px;
    }

    .gst-info-box strong {
      color: #065f46;
      font-weight: 700;
    }

    .non-gst-info-box {
      background: #fffbeb;
      border: 1px solid #fde68a;
      border-radius: 12px;
      padding: 16px;
      margin-bottom: 20px;
    }

    .non-gst-info-box strong {
      color: #92400e;
      font-weight: 700;
    }

    .save-btn {
      background: linear-gradient(135deg, #009d1a 0%, #00bc20 100%) !important;
      color: white !important;
      border: none !important;
      border-radius: 30px !important;
      padding: 14px 40px !important;
      font-size: 1rem !important;
      font-weight: 700 !important;
      transition: all 0.3s ease !important;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      width: 100%;
      box-shadow: 0 4px 15px rgba(0, 157, 26, 0.2) !important;
    }

    .save-btn:hover {
      background: linear-gradient(135deg, #00bc20 0%, #009d1a 100%) !important;
      transform: translateY(-2px) !important;
      box-shadow: 0 8px 25px rgba(0, 157, 26, 0.3) !important;
    }

    .empty-state {
      text-align: center;
      padding: 40px 20px;
      color: var(--gray);
    }

    .empty-state i {
      font-size: 3rem;
      color: #cbd5e1;
      margin-bottom: 15px;
    }

    .empty-state h5 {
      font-weight: 800;
      color: var(--primary);
    }

    .toast-notification {
      position: fixed;
      bottom: 30px;
      right: 30px;
      background: white;
      border-radius: 12px;
      padding: 16px 24px;
      box-shadow: 0 8px 30px rgba(15, 23, 42, 0.15);
      z-index: 9999;
      display: flex;
      align-items: center;
      gap: 12px;
      transform: translateX(400px);
      transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      border-left: 4px solid var(--success);
      font-weight: 700;
    }

    .toast-notification.show {
      transform: translateX(0);
    }

    .toast-notification.error {
      border-left-color: var(--danger);
    }

    .discount-note {
      font-size: 0.75rem;
      color: var(--gray);
      text-align: center;
      margin-top: 8px;
    }

    .badge-info {
      background-color: #2563eb;
      color: white;
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 0.7rem;
      font-weight: 700;
    }

    del {
      font-size: 0.75rem;
    }

    @media (max-width: 768px) {
      .card-header h5 { font-size: 1rem; }
      .nav-tabs { margin: 15px 15px 0; }
      .nav-tabs .nav-link { padding: 8px 12px; font-size: 0.8rem; }
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
            <h5 class="text-white"><i class="fas fa-user me-2"></i>Customer & Table Details</h5>
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
                  @if(isset($restaurant_gstin) && $restaurant_gstin)
                  <th>GST (%)</th>
                  <th>GST Amt (₹)</th>
                  @endif
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
          
          @if(isset($restaurant_gstin) && $restaurant_gstin)
          <div class="summary-item">
            <span class="summary-label">GST Total ({{ $restaurant_gst_percentage ?? 0 }}%):</span>
            <span class="summary-value">₹<span id="total_gst">0.00</span></span>
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
            <span class="total-value">₹<span id="final_total">0.00</span></span>
          </div>

          {{-- <div class="mt-3">
            <label class="form-label">Order Discount (%)</label>
            <input type="number" class="form-control discount-input" id="order_discount" value="0" min="0" max="100" step="1">
            <div class="discount-note">
              <i class="fas fa-info-circle"></i> Discount applies to (Taxable Value + GST)
            </div>
          </div> --}}
        </div>

        {{-- @if(!isset($table) || !$table)
        <div class="payment-section">
          <h5 class="mb-3"><i class="fas fa-credit-card me-2"></i>Payment Details</h5>
          
          <div class="mb-3">
            <label class="form-label">Order Complete ?</label>
            <select class="form-control" id="order_complete">
              <option value="PENDING">Pending</option>
              <option value="DONE">Done & Checkout</option>
            </select>
          </div>
          </div>
        @endif --}}

        <div class="d-grid gap-2 mt-4">
          <button class="btn save-btn" id="saveOrderBtn">
            <i class="fas fa-check-circle"></i>
            @if(!isset($table) || !$table) Checkout @else Save Order @endif
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
let orderItems = [];
let isGstRegistered = {{ isset($restaurant_gstin) && $restaurant_gstin ? 'true' : 'false' }};
let restaurantGstPercentage = {{ $restaurant_gst_percentage ?? 0 }};

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
    // Calculate discounted price per item
    let discountedPricePerItem = originalPrice - (originalPrice * discountPercent / 100);
    
    // Calculate taxable amount (after discount)
    let taxableAmount = discountedPricePerItem * qty;
    
    // Calculate GST on discounted price (only if GST registered)
    let gstAmount = 0;
    let gstRate = 0;
    if (isGstRegistered) {
        gstRate = restaurantGstPercentage;
        gstAmount = (taxableAmount * gstRate) / 100;
    }
    
    // Calculate total amount
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
    
    orderItems.forEach(item => {
        let originalAmount = item.price * item.qty;
        let details = calculateItemDetails(item.price, item.qty, item.itemDiscount || 0);
        
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
}

function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    str = String(str);
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
    
    // Build HTML for each item based on GST status
    orderItems.forEach((item, index) => {
        let details = calculateItemDetails(item.price, item.qty, item.itemDiscount || 0);
        
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
                <td class="text-end">₹${details.taxableAmount.toFixed(2)}`;
        
        if (isGstRegistered) {
            row += `<td class="text-center">${details.gstRate}%
                    <td class="text-end">₹${details.gstAmount.toFixed(2)}`;
        }
        
        row += `<td class="text-end fw-bold">₹${details.totalAmount.toFixed(2)}
                <td class="text-center">
                    <button class="btn btn-sm btn-danger remove-btn" data-index="${index}">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
              </tr>`;
        tbody.append(row);
    });
    
    updateSummary();
}

$(document).ready(function() {
    console.log('Document ready');
    console.log('GST Registered:', isGstRegistered);
    console.log('GST Percentage:', restaurantGstPercentage);
    
    // Add item button click
    $(document).on('click', '.add-item-btn', function(e) {
        e.preventDefault();
        
        let itemId = $(this).data('id');
        let itemName = $(this).data('name');
        let itemPrice = parseFloat($(this).data('price'));
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
                        setTimeout(() => {
                            window.location.href = response.redirect_url;
                        }, 1000);
                    } else if (response.invoice_url) {
                        setTimeout(() => {
                            window.location.href = response.invoice_url;
                        }, 1000);
                    } else {
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