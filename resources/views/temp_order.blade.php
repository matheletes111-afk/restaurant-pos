<!DOCTYPE html>
<html lang="en">
<head>
  <title>Customer Order | Premium Dining</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    :root {
      --primary: #2563eb;
      --primary-dark: #1d4ed8;
      --secondary: #64748b;
      --success: #10b981;
      --light-bg: #f8fafc;
      --card-bg: #ffffff;
      --border: #e2e8f0;
      --text-primary: #1e293b;
      --text-secondary: #64748b;
      --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
      --shadow-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
      --radius-lg: 20px;
      --radius-md: 16px;
      --radius-sm: 10px;
      --radius-xs: 6px;
    }

    body {
      background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
      font-family: 'Inter', 'Poppins', sans-serif;
      color: var(--text-primary);
      min-height: 100vh;
      padding-bottom: 40px;
    }

    .container {
      max-width: 1400px;
      padding-top: 30px;
    }

    /* Header */
    .restaurant-header {
      text-align: center;
      margin-bottom: 40px;
      padding: 30px;
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      border-radius: var(--radius-lg);
      color: white;
      box-shadow: var(--shadow);
      position: relative;
      overflow: hidden;
    }

    .restaurant-header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
      opacity: 0.3;
    }

    .restaurant-header h1 {
      font-weight: 700;
      font-size: 2.8rem;
      margin-bottom: 10px;
      position: relative;
      z-index: 1;
    }

    .restaurant-header p {
      font-size: 1.1rem;
      opacity: 0.9;
      position: relative;
      z-index: 1;
    }

    /* Customer Info Card */
    .customer-card {
      background: var(--card-bg);
      border-radius: var(--radius-lg);
      padding: 30px;
      margin-bottom: 40px;
      box-shadow: var(--shadow);
      border: 1px solid var(--border);
    }

    .customer-card h5 {
      font-weight: 600;
      color: var(--primary);
      margin-bottom: 25px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .customer-card h5 i {
      font-size: 1.3rem;
    }

    .form-control {
      border: 2px solid #e2e8f0;
      border-radius: var(--radius-sm);
      padding: 12px 16px;
      font-size: 15px;
      transition: all 0.3s;
    }

    .form-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    /* Search & Filter */
    .search-container {
      position: relative;
      max-width: 400px;
    }

    .search-container i {
      position: absolute;
      left: 16px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--secondary);
      z-index: 10;
    }

    .search-container input {
      padding-left: 46px;
      border-radius: 50px;
      height: 48px;
      border: 2px solid var(--border);
    }

    .filter-container {
      display: flex;
      gap: 12px;
    }

    .filter-btn {
      padding: 10px 24px;
      border-radius: 50px;
      border: 2px solid var(--border);
      background: white;
      color: var(--text-secondary);
      font-weight: 500;
      transition: all 0.3s;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .filter-btn.active {
      background: var(--primary);
      color: white;
      border-color: var(--primary);
    }

    .filter-btn.veg.active {
      background: #10b981;
      border-color: #10b981;
    }

    .filter-btn.nonveg.active {
      background: #ef4444;
      border-color: #ef4444;
    }

    /* Category Tabs */
    .category-tabs {
      border-bottom: 2px solid var(--border);
      margin-bottom: 40px;
    }

    .category-tabs .nav-link {
      border: none;
      background: transparent;
      color: var(--text-secondary);
      font-weight: 500;
      padding: 14px 24px;
      margin-right: 5px;
      border-radius: var(--radius-md) var(--radius-md) 0 0;
      transition: all 0.3s;
      position: relative;
    }

    .category-tabs .nav-link:hover {
      color: var(--primary);
      background: rgba(37, 99, 235, 0.05);
    }

    .category-tabs .nav-link.active {
      color: var(--primary);
      font-weight: 600;
      background: rgba(37, 99, 235, 0.08);
    }

    .category-tabs .nav-link.active::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      right: 0;
      height: 3px;
      background: var(--primary);
      border-radius: 3px 3px 0 0;
    }

    /* Food Cards - Updated for mobile 2-column layout */
    .food-card-wrapper {
      margin-bottom: 25px;
    }

    .food-card {
      background: var(--card-bg);
      border-radius: var(--radius-md);
      overflow: hidden;
      border: 1px solid var(--border);
      transition: all 0.3s ease;
      height: 100%;
      display: flex;
      flex-direction: column;
      box-shadow: var(--shadow);
    }

    .food-card:hover {
      transform: translateY(-8px);
      box-shadow: var(--shadow-hover);
      border-color: var(--primary);
    }

    /* Updated Image Container */
    .food-image-container {
      height: 220px;
      overflow: hidden;
      position: relative;
      width: 100%;
    }

    /* Enhanced Image Styling */
    .food-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s ease;
      border-radius: var(--radius-md) var(--radius-md) 0 0;
    }

    .food-card:hover .food-image {
      transform: scale(1.08);
    }

    .food-badge {
      position: absolute;
      top: 15px;
      right: 15px;
      width: 28px;
      height: 28px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 12px;
      box-shadow: 0 3px 6px rgba(0,0,0,0.15);
      z-index: 2;
    }

    .veg-badge {
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .nonveg-badge {
      background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .food-details {
      padding: 22px;
      flex-grow: 1;
      display: flex;
      flex-direction: column;
    }

    .food-title {
      font-weight: 600;
      font-size: 1.15rem;
      margin-bottom: 10px;
      color: var(--text-primary);
      line-height: 1.3;
    }

    .food-description {
      color: var(--text-secondary);
      font-size: 0.9rem;
      margin-bottom: 18px;
      flex-grow: 1;
      line-height: 1.5;
    }

    .food-price {
      font-weight: 700;
      font-size: 1.35rem;
      color: var(--primary);
      margin-bottom: 4px;
    }

    .food-gst {
      color: var(--text-secondary);
      font-size: 0.85rem;
      margin-bottom: 20px;
    }

    .add-to-cart-btn {
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      color: white;
      border: none;
      border-radius: var(--radius-sm);
      padding: 13px;
      font-weight: 600;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      transition: all 0.3s;
      width: 100%;
      font-size: 0.95rem;
    }

    .add-to-cart-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 12px rgba(37, 99, 235, 0.2);
    }

    .add-to-cart-btn.added {
      background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
    }

    /* Order Summary */
    .order-summary-card {
      background: var(--card-bg);
      border-radius: var(--radius-lg);
      padding: 30px;
      margin-top: 40px;
      box-shadow: var(--shadow);
      border: 1px solid var(--border);
    }

    .order-summary-card h5 {
      font-weight: 600;
      color: var(--primary);
      margin-bottom: 25px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .order-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
    }

    .order-table thead th {
      background: #f8fafc;
      color: var(--text-secondary);
      font-weight: 600;
      padding: 16px 20px;
      border-bottom: 2px solid var(--border);
    }

    .order-table tbody td {
      padding: 20px;
      border-bottom: 1px solid var(--border);
      vertical-align: middle;
    }

    .order-table tbody tr:last-child td {
      border-bottom: none;
    }

    .item-name {
      font-weight: 500;
      color: var(--text-primary);
    }

    .quantity-controls {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .qty-btn {
      width: 36px;
      height: 36px;
      border-radius: var(--radius-sm);
      border: 2px solid var(--border);
      background: white;
      color: var(--text-primary);
      font-weight: 600;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s;
    }

    .qty-btn:hover {
      background: var(--light-bg);
      border-color: var(--primary);
    }

    .qty-value {
      min-width: 40px;
      text-align: center;
      font-weight: 600;
      font-size: 1.1rem;
    }

    .remove-btn {
      background: #fee2e2;
      color: #dc2626;
      border: none;
      border-radius: var(--radius-sm);
      width: 36px;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s;
    }

    .remove-btn:hover {
      background: #fecaca;
    }

    .totals-section {
      background: #f8fafc;
      border-radius: var(--radius-md);
      padding: 24px;
      margin-top: 30px;
    }

    .total-row {
      display: flex;
      justify-content: space-between;
      padding: 12px 0;
      border-bottom: 1px solid var(--border);
    }

    .total-row:last-child {
      border-bottom: none;
    }

    .total-row.final {
      font-size: 1.3rem;
      font-weight: 700;
      color: var(--primary);
      padding-top: 16px;
    }

    /* Place Order Button */
    .place-order-btn {
      background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
      color: white;
      border: none;
      border-radius: 50px;
      padding: 18px 50px;
      font-size: 1.2rem;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 12px;
      transition: all 0.3s;
      box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
      margin-top: 40px;
    }

    .place-order-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 15px 30px rgba(16, 185, 129, 0.4);
    }

    .place-order-btn:disabled {
      background: #94a3b8;
      transform: none;
      box-shadow: none;
      cursor: not-allowed;
    }

    /* Empty State */
    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: var(--text-secondary);
    }

    .empty-state i {
      font-size: 4rem;
      margin-bottom: 20px;
      color: #cbd5e1;
    }

    .empty-state h4 {
      font-weight: 600;
      margin-bottom: 10px;
    }

    /* Responsive Grid System */
    /* Desktop: 4 columns, Tablet: 3 columns, Mobile: 2 columns */
    @media (min-width: 992px) {
      .food-card-wrapper {
        flex: 0 0 25%;
        max-width: 25%;
      }
    }

    @media (max-width: 991px) and (min-width: 768px) {
      .food-card-wrapper {
        flex: 0 0 33.333%;
        max-width: 33.333%;
      }
    }

    @media (max-width: 767px) and (min-width: 576px) {
      .food-card-wrapper {
        flex: 0 0 50%;
        max-width: 50%;
      }
      
      .food-image-container {
        height: 180px;
      }
      
      .food-details {
        padding: 18px;
      }
    }

    @media (max-width: 575px) {
      .food-card-wrapper {
        flex: 0 0 50%;
        max-width: 50%;
      }
      
      .food-image-container {
        height: 160px;
      }
      
      .food-details {
        padding: 16px;
      }
      
      .food-title {
        font-size: 1.05rem;
      }
      
      .food-price {
        font-size: 1.2rem;
      }
      
      .add-to-cart-btn {
        padding: 10px;
        font-size: 0.9rem;
      }
    }

    /* Extra small mobile optimization */
    @media (max-width: 380px) {
      .food-card-wrapper {
        flex: 0 0 100%;
        max-width: 100%;
      }
      
      .food-image-container {
        height: 180px;
      }
    }

    /* Other Responsive Styles */
    @media (max-width: 992px) {
      .restaurant-header h1 {
        font-size: 2.2rem;
      }
      
      .search-container {
        max-width: 100%;
        margin-bottom: 20px;
      }
      
      .filter-container {
        justify-content: center;
      }
    }

    @media (max-width: 768px) {
      .container {
        padding-top: 15px;
      }
      
      .restaurant-header {
        padding: 20px;
        margin-bottom: 30px;
      }
      
      .customer-card, .order-summary-card {
        padding: 20px;
      }
      
      .category-tabs .nav-link {
        padding: 10px 16px;
        font-size: 0.9rem;
      }
      
      .order-table {
        display: block;
        overflow-x: auto;
      }
      
      .place-order-btn {
        width: 100%;
        justify-content: center;
        padding: 16px 20px;
      }
    }

    @media (max-width: 576px) {
      .filter-container {
        flex-wrap: wrap;
        justify-content: center;
      }
      
      .filter-btn {
        padding: 8px 16px;
        font-size: 0.9rem;
      }
    }
  </style>
</head>

<body>
<div class="container">

  <!-- Restaurant Header -->
  <div class="restaurant-header">
    <h1><i class="fas fa-utensils"></i> {{@$restaurant_details->name}}</h1>
    <p class="mb-0">Select your favorite dishes from our premium menu</p>
  </div>

  <!-- Customer Info -->
  <div class="customer-card">
    <h5><i class="fas fa-user-circle"></i> Customer Details</h5>
    <div class="row">
      <div class="col-lg-6 mb-3">
        <label class="form-label">Your Name</label>
        <input type="text" id="customer_name" name="customer_name" class="form-control" placeholder="Enter your full name">
      </div>
      <div class="col-lg-6 mb-3">
        <label class="form-label">Phone Number</label>
        <input type="text" id="phone" name="customer_phone" class="form-control" placeholder="Enter your contact number">
      </div>
    </div>
    <input type="hidden" id="table_id" value="{{ $table_id }}">
    <input type="hidden" id="restaurant_id" value="{{ $restaurant_id }}">
  </div>

  <!-- Search & Filter -->
  <div class="d-lg-flex justify-content-between align-items-center mb-4">
    <div class="search-container mb-3 mb-lg-0">
      <i class="fas fa-search"></i>
      <input type="text" id="searchBox" class="form-control" placeholder="Search for dishes...">
    </div>
    
    <div class="filter-container">
      <button class="filter-btn active" data-type="">All Items</button>
      <button class="filter-btn veg" data-type="veg">
        <i class="fas fa-leaf"></i> Veg
      </button>
      <button class="filter-btn nonveg" data-type="non-veg">
        <i class="fas fa-drumstick-bite"></i> Non-Veg
      </button>
    </div>
  </div>

  <!-- Category Tabs -->
  <ul class="nav category-tabs" role="tablist">
    @foreach($categories as $key => $cat)
    <li class="nav-item">
      <a class="nav-link {{ $key==0?'active':'' }}" data-toggle="tab" href="#cat{{ $cat->id }}">
        {{ $cat->name }}
      </a>
    </li>
    @endforeach
  </ul>

  <!-- Category Content -->
  <div class="tab-content mt-1">
    @foreach($categories as $key => $cat)
    <div class="tab-pane fade {{ $key==0?'show active':'' }}" id="cat{{ $cat->id }}">
      <div class="row">
        @foreach($cat->subcategories as $item)
        <div class="col-xl-3 col-lg-4 col-md-6 food-card-wrapper"
             data-name="{{ strtolower($item->name) }}"
             data-type="{{ strtolower($item->food_type) }}">
          
          <div class="food-card">
            <div class="food-image-container">
              @if($item->image)
                <img src="{{ URL::to('storage/app/public/category') }}/{{ @$item->image }}" 
                     alt="{{ $item->name }}" 
                     class="food-image"
                     onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80'">
              @else
                <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" 
                     alt="{{ $item->name }}" 
                     class="food-image">
              @endif
              
              <div class="food-badge {{ strtolower($item->food_type) == 'veg' ? 'veg-badge' : 'nonveg-badge' }}">
                <i class="fas {{ strtolower($item->food_type) == 'veg' ? 'fa-leaf' : 'fa-drumstick-bite' }}"></i>
              </div>
            </div>
            
            <div class="food-details">
              <h5 class="food-title">{{ $item->name }}</h5>
              <p class="food-description">
                {{ $item->description ?? 'Delicious dish prepared with premium ingredients' }}
              </p>
              
              <div class="d-flex justify-content-between align-items-end">
                <div>
                  <div class="food-price">₹{{ $item->price }}</div>
                  <div class="food-gst">Includes {{ $item->gst_rate }}% GST</div>
                </div>
                
                <button class="add-to-cart-btn addItemBtn"
                        data-id="{{ $item->id }}"
                        data-name="{{ $item->name }}"
                        data-price="{{ $item->price }}"
                        data-gst="{{ $item->gst_rate }}"
                        data-type="{{ strtolower($item->food_type) }}">
                  <i class="fas fa-plus"></i> Add
                </button>
              </div>
            </div>
          </div>
          
        </div>
        @endforeach
      </div>
    </div>
    @endforeach
  </div>

  <!-- Order Summary -->
  <div class="order-summary-card">
    <h5><i class="fas fa-receipt"></i> Order Summary</h5>
    
    <div id="orderItemsContainer">
      <div class="table-responsive">
        <table class="order-table">
          <thead>
            <tr>
              <th>Item</th>
              <th>Price</th>
              <th>Quantity</th>
              <th>GST</th>
              <th>Total</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody id="orderItemsBody">
            <!-- Items will be inserted here -->
          </tbody>
        </table>
      </div>
      
      <!-- Empty State -->
      <div id="emptyOrderState" class="empty-state">
        <i class="fas fa-shopping-cart"></i>
        <h4>Your cart is empty</h4>
        <p>Add delicious items from the menu to get started</p>
      </div>
    </div>
    
    <!-- Totals -->
    <div class="totals-section">
      <div class="total-row">
        <span>Subtotal</span>
        <span>₹<span id="subtotal">0.00</span></span>
      </div>
      <div class="total-row">
        <span>GST Total</span>
        <span>₹<span id="gst_total">0.00</span></span>
      </div>
      <div class="total-row final">
        <span>Final Total</span>
        <span>₹<span id="final_total">0.00</span></span>
      </div>
    </div>
  </div>

  <!-- Place Order -->
  <div class="text-center">
    <button class="place-order-btn" id="placeOrderBtn">
      <i class="fas fa-paper-plane"></i> Place Your Order
    </button>
  </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
let cart = [];

function updateEmptyState() {
  if (cart.length === 0) {
    $('#emptyOrderState').show();
    $('#orderItemsBody').closest('.table-responsive').hide();
    $('#placeOrderBtn').prop('disabled', true);
  } else {
    $('#emptyOrderState').hide();
    $('#orderItemsBody').closest('.table-responsive').show();
    $('#placeOrderBtn').prop('disabled', false);
  }
}

function refreshTable() {
  let tbody = $('#orderItemsBody');
  tbody.html('');

  let subtotal = 0, gstTotal = 0;

  cart.forEach((item, i) => {
    let gstAmt = (item.price * item.qty * item.gst) / 100;
    let total = (item.price * item.qty) + gstAmt;

    subtotal += item.price * item.qty;
    gstTotal += gstAmt;

    tbody.append(`
      <tr>
        <td class="item-name">${item.name}</td>
        <td>₹${item.price.toFixed(2)}</td>
        <td>
          <div class="quantity-controls">
            <button class="qty-btn decreaseQty" data-index="${i}">
              <i class="fas fa-minus"></i>
            </button>
            <span class="qty-value">${item.qty}</span>
            <button class="qty-btn increaseQty" data-index="${i}">
              <i class="fas fa-plus"></i>
            </button>
          </div>
        </td>
        <td>${item.gst}%</td>
        <td><strong>₹${total.toFixed(2)}</strong></td>
        <td>
          <button class="remove-btn removeItem" data-index="${i}" title="Remove item">
            <i class="fas fa-trash"></i>
          </button>
        </td>
      </tr>
    `);
  });

  $('#subtotal').text(subtotal.toFixed(2));
  $('#gst_total').text(gstTotal.toFixed(2));
  $('#final_total').text((subtotal + gstTotal).toFixed(2));
  
  updateEmptyState();
}

// Add item to cart
$(document).on('click', '.addItemBtn', function() {
  let item = {
    id: $(this).data('id'),
    name: $(this).data('name'),
    price: parseFloat($(this).data('price')),
    gst: parseFloat($(this).data('gst')),
    qty: 1
  };

  let exists = cart.find(i => i.id == item.id);
  if (exists) {
    exists.qty++;
  } else {
    cart.push(item);
  }

  // Visual feedback
  $(this).addClass('added');
  $(this).html('<i class="fas fa-check"></i> Added');
  
  setTimeout(() => {
    $(this).removeClass('added');
    $(this).html('<i class="fas fa-plus"></i> Add');
  }, 1000);

  refreshTable();
});

// Quantity controls
$(document).on('click', '.increaseQty', function() {
  cart[$(this).data('index')].qty++;
  refreshTable();
});

$(document).on('click', '.decreaseQty', function() {
  let index = $(this).data('index');
  if (cart[index].qty > 1) {
    cart[index].qty--;
    refreshTable();
  }
});

$(document).on('click', '.removeItem', function() {
  cart.splice($(this).data('index'), 1);
  refreshTable();
});

// Search functionality
$('#searchBox').on('input', function() {
  let val = $(this).val().toLowerCase();
  $('.food-card-wrapper').each(function() {
    $(this).toggle($(this).data('name').includes(val));
  });
});

// Filter functionality
$('.filter-btn').click(function() {
  $('.filter-btn').removeClass('active');
  $(this).addClass('active');

  let type = $(this).data('type');
  $('.food-card-wrapper').each(function() {
    $(this).toggle(type === '' || $(this).data('type') === type);
  });
});

// Submit Order
$('#placeOrderBtn').click(function() {
  if (cart.length === 0) {
    alert('Please add items to your order');
    return;
  }
  
  if (!$('#customer_name').val()) {
    alert('Please enter your name');
    $('#customer_name').focus();
    return;
  }
  
  if (!$('#phone').val()) {
    alert('Please enter your phone number');
    $('#phone').focus();
    return;
  }

  // Show loading state
  $(this).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
  $(this).prop('disabled', true);

  $.post("{{ route('temp.order.store') }}", {
    _token: "{{ csrf_token() }}",
    customer_name: $('#customer_name').val(),
    customer_phone: $('#phone').val(),
    table_id: $('#table_id').val(),
    restaurant_id: $('#restaurant_id').val(),
    order_items: cart
  }, function(res) {
    if (res.status) {
      window.location.href = res.redirect;
    } else {
      alert('Something went wrong. Please try again.');
      $('#placeOrderBtn').html('<i class="fas fa-paper-plane"></i> Place Your Order');
      $('#placeOrderBtn').prop('disabled', false);
    }
  }).fail(function() {
    alert('Network error. Please check your connection and try again.');
    $('#placeOrderBtn').html('<i class="fas fa-paper-plane"></i> Place Your Order');
    $('#placeOrderBtn').prop('disabled', false);
  });
});

// Initialize empty state on load
$(document).ready(function() {
  updateEmptyState();
});
</script>

</body>
</html>