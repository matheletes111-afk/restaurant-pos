<!DOCTYPE html>
<html lang="en">
<head>
  <title>Customer Order | {{ $restaurant_details->name ?? 'Premium Dining' }}</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    :root {
      --primary: #C9A84C;
      --primary-light: rgba(201,168,76, 0.1);
      --primary-hover: #E8C97A;
      --secondary: #E8C97A;
      --surface: #17171C;
      --surface-2: #1E1E25;
      --surface-3: #26262F;
      --text-main: #F2EEE6;
      --text-muted: rgba(242,238,230,0.55);
      --border: rgba(255,255,255,0.07);
      --shadow-sm: 0 2px 8px rgba(0,0,0,0.3);
      --shadow-md: 0 8px 24px rgba(0,0,0,0.4);
      --shadow-lg: 0 20px 40px rgba(0,0,0,0.5);
      --radius-sm: 8px;
      --radius-md: 16px;
      --radius-lg: 24px;
      --success: #3DD68C;
      --danger: #FF6B6B;
    }

    body {
      background-color: #0A0A0B;
      background-image:
          radial-gradient(ellipse 80% 50% at 50% -10%, rgba(201,168,76,0.08) 0%, transparent 60%),
          radial-gradient(ellipse 60% 40% at 80% 100%, rgba(201,168,76,0.05) 0%, transparent 50%);
      font-family: 'Inter', sans-serif;
      color: var(--text-main);
      min-height: 100vh;
      padding-bottom: 120px;
      -webkit-font-smoothing: antialiased;
    }

    /* Noise texture overlay */
    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.03'/%3E%3C/svg%3E");
      pointer-events: none;
      z-index: 0;
      opacity: 0.6;
    }
    
    .container { max-width: 1200px; padding-top: 40px; position: relative; z-index: 1; }
    
    .serif-font { font-family: 'Playfair Display', serif; }

    /* Restaurant Header */
    .restaurant-header {
      background: var(--surface);
      border-radius: var(--radius-lg);
      padding: 60px 40px;
      text-align: center;
      margin-bottom: 40px;
      box-shadow: var(--shadow-md);
      position: relative;
      overflow: hidden;
    }
    
    .restaurant-header::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 8px;
      background: linear-gradient(90deg, var(--primary), var(--secondary));
    }

    .header-icon-ring {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 72px;
      height: 72px;
      border-radius: 50%;
      background: var(--primary-light);
      color: var(--primary);
      margin-bottom: 20px;
      font-size: 1.8rem;
    }

    .restaurant-header h1 {
      font-family: 'Playfair Display', serif;
      font-weight: 700;
      font-size: 3rem;
      color: var(--text-main);
      margin-bottom: 12px;
    }

    .header-tagline {
      font-size: 1rem;
      color: var(--text-muted);
      font-weight: 400;
      letter-spacing: 0.05em;
      margin-bottom: 24px;
    }

    .gst-info-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: var(--surface-3);
      padding: 8px 16px;
      border-radius: 50px;
      font-size: 0.85rem;
      font-weight: 500;
      color: var(--text-muted);
    }

    /* Customer Card */
    .customer-card {
      background: var(--surface);
      border-radius: var(--radius-md);
      padding: 32px;
      margin-bottom: 40px;
      box-shadow: var(--shadow-sm);
      border: 1px solid var(--border);
    }

    .section-label {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 24px;
    }

    .section-label h5 {
      font-weight: 600;
      font-size: 1.2rem;
      color: var(--text-main);
      margin: 0;
    }

    .form-label {
      font-size: 0.85rem;
      font-weight: 500;
      color: var(--text-muted);
      margin-bottom: 8px;
    }

    .form-control {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-sm);
      padding: 14px 16px;
      font-size: 1rem;
      color: var(--text-main);
      box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);
      transition: all 0.2s;
      height: auto;
    }

    .form-control::placeholder {
      color: var(--text-muted);
    }

    .form-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 4px var(--primary-light);
      background: var(--surface);
      color: var(--text-main);
      outline: none;
    }

    /* Search & Filter */
    .controls-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 16px;
      margin-bottom: 32px;
      flex-wrap: wrap;
    }

    .search-container {
      position: relative;
      flex: 1;
      max-width: 400px;
    }

    .search-container i {
      position: absolute;
      left: 16px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted);
    }

    .search-container input {
      padding-left: 44px;
      border-radius: 50px;
      height: 48px;
      background: var(--surface);
      border: 1px solid var(--border);
      box-shadow: var(--shadow-sm);
    }

    .filter-container {
      display: flex;
      gap: 12px;
    }

    .filter-btn {
      padding: 10px 24px;
      border-radius: 50px;
      border: 1px solid var(--border);
      background: var(--surface);
      color: var(--text-main);
      font-weight: 500;
      font-size: 0.95rem;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      gap: 8px;
      box-shadow: var(--shadow-sm);
      cursor: pointer;
    }

    .filter-btn:hover {
      border-color: var(--text-muted);
    }

    .filter-btn.active {
      background: var(--primary);
      color: #0A0A0B;
      border-color: var(--primary);
    }

    /* Category Tabs */
    .category-tabs-wrapper {
      margin-bottom: 32px;
      position: sticky;
      top: 0;
      z-index: 100;
      background: rgba(10, 10, 11, 0.85);
      backdrop-filter: blur(12px);
      padding: 16px 0;
    }

    .category-tabs {
      border: none;
      display: flex;
      gap: 8px;
      overflow-x: auto;
      padding-bottom: 4px;
      -ms-overflow-style: none;
      scrollbar-width: none;
    }

    .category-tabs::-webkit-scrollbar { display: none; }

    .category-tabs .nav-link {
      background: transparent;
      color: var(--text-muted);
      font-weight: 600;
      font-size: 1rem;
      padding: 8px 16px;
      border-radius: 50px;
      border: none;
      transition: all 0.2s;
      white-space: nowrap;
    }

    .category-tabs .nav-link:hover {
      color: var(--text-main);
      background: var(--surface-3);
    }

    .category-tabs .nav-link.active {
      color: var(--text-main);
      background: var(--surface);
      box-shadow: var(--shadow-sm);
      border: 1px solid var(--border);
    }

    /* Food Cards */
    .food-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 24px;
    }
    
    @media (max-width: 992px) { .food-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 576px) { .food-grid { grid-template-columns: 1fr; } }

    .food-card-wrapper { display: contents; }

    .food-card {
      background: var(--surface);
      border-radius: var(--radius-md);
      overflow: hidden;
      border: 1px solid var(--border);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      display: flex;
      flex-direction: column;
      height: 100%;
      box-shadow: var(--shadow-sm);
    }

    .food-card:hover {
      transform: translateY(-4px);
      box-shadow: var(--shadow-md);
      border-color: rgba(255, 90, 95, 0.3);
    }

    .food-image-container {
      height: 220px;
      position: relative;
      background: var(--surface-3);
      overflow: hidden;
    }

    .food-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.4s ease;
    }

    .food-card:hover .food-image {
      transform: scale(1.05);
    }

    .food-badge {
      position: absolute;
      top: 12px;
      right: 12px;
      width: 28px;
      height: 28px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 10px;
      background: white;
      box-shadow: var(--shadow-sm);
      z-index: 2;
    }

    .veg-badge { color: #008A05; border: 1px solid rgba(0, 138, 5, 0.2); }
    .nonveg-badge { color: #E12C2C; border: 1px solid rgba(225, 44, 44, 0.2); }

    .discount-badge {
      position: absolute;
      top: 12px;
      left: 12px;
      background: var(--primary);
      color: white;
      padding: 4px 10px;
      border-radius: 50px;
      font-size: 0.75rem;
      font-weight: 600;
      z-index: 2;
      box-shadow: var(--shadow-sm);
    }

    .food-details {
      padding: 20px;
      display: flex;
      flex-direction: column;
      flex-grow: 1;
    }

    .food-title {
      font-weight: 600;
      font-size: 1.1rem;
      color: var(--text-main);
      margin-bottom: 8px;
    }

    .food-description {
      color: var(--text-muted);
      font-size: 0.9rem;
      line-height: 1.5;
      margin-bottom: 20px;
      flex-grow: 1;
    }

    .food-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .food-price {
      font-weight: 700;
      font-size: 1.25rem;
      color: var(--text-main);
    }

    .food-price del {
      font-size: 0.85rem;
      color: var(--text-muted);
      font-weight: 400;
      display: block;
      margin-bottom: 2px;
    }

    .gst-hint {
      font-size: 0.75rem;
      color: var(--text-muted);
      margin-top: 4px;
    }

    .add-to-cart-btn {
      background: var(--surface-3);
      color: var(--text-main);
      border: 1px solid var(--border);
      border-radius: 50px;
      padding: 8px 20px;
      font-weight: 600;
      font-size: 0.9rem;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      gap: 6px;
      cursor: pointer;
    }

    .add-to-cart-btn:hover {
      background: var(--primary);
      color: #0A0A0B;
      border-color: var(--primary);
    }

    .add-to-cart-btn.added {
      background: var(--success);
      color: #0A0A0B;
      border-color: var(--success);
    }

    /* Order Summary */
    .order-summary-card {
      background: var(--surface);
      border-radius: var(--radius-md);
      padding: 32px;
      margin-top: 48px;
      box-shadow: var(--shadow-sm);
      border: 1px solid var(--border);
    }
    
    .order-table-wrapper {
      border-radius: var(--radius-sm);
      border: 1px solid var(--border);
      overflow-x: auto;
    }

    .order-table {
      width: 100%;
      min-width: 650px;
      border-collapse: collapse;
      text-align: left;
    }

    .order-table th {
      background: var(--surface-2);
      color: var(--text-muted);
      font-weight: 500;
      font-size: 0.8rem;
      padding: 16px;
      border-bottom: 1px solid var(--border);
      text-transform: uppercase;
    }

    .order-table td {
      padding: 16px;
      border-bottom: 1px solid var(--border);
      vertical-align: middle;
      font-size: 0.95rem;
    }

    .item-name-cell strong {
      display: block;
      font-weight: 600;
      color: var(--text-main);
      margin-bottom: 4px;
    }

    .quantity-controls {
      display: inline-flex;
      align-items: center;
      background: var(--surface-2);
      border-radius: 50px;
      padding: 4px;
      border: 1px solid var(--border);
    }

    .qty-btn {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      border: none;
      background: var(--surface-3);
      color: var(--text-main);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      transition: all 0.2s;
    }
    
    .qty-btn:hover { background: var(--primary); color: #0A0A0B; }
    .qty-value { min-width: 32px; text-align: center; font-weight: 600; }

    .remove-btn {
      background: transparent;
      color: var(--text-muted);
      border: none;
      cursor: pointer;
      font-size: 1.1rem;
      transition: color 0.2s;
    }
    .remove-btn:hover { color: var(--danger); }

    .totals-section {
      background: var(--surface-2);
      border-radius: var(--radius-md);
      padding: 24px;
      margin-top: 32px;
    }

    .total-row {
      display: flex;
      justify-content: space-between;
      padding: 8px 0;
      font-size: 1rem;
      color: var(--text-muted);
    }

    .total-row.final {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--text-main);
      border-top: 1px solid var(--border);
      padding-top: 16px;
      margin-top: 8px;
    }

    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: var(--text-muted);
    }

    .empty-state i {
      font-size: 3rem;
      color: var(--border);
      margin-bottom: 24px;
    }

    .empty-state h5 {
      font-weight: 600;
      color: var(--text-main);
      font-size: 1.4rem;
    }

    .order-action-area {
      position: fixed;
      bottom: 0; left: 0; right: 0;
      background: rgba(23, 23, 28, 0.9);
      backdrop-filter: blur(16px);
      padding: 20px;
      text-align: center;
      border-top: 1px solid var(--border);
      z-index: 200;
      box-shadow: 0 -4px 16px rgba(0,0,0,0.5);
    }

    .place-order-btn {
      background: var(--primary);
      color: white;
      border: none;
      border-radius: 50px;
      padding: 16px 48px;
      font-size: 1.1rem;
      font-weight: 600;
      box-shadow: 0 4px 12px rgba(255, 90, 95, 0.3);
      transition: all 0.2s;
      cursor: pointer;
    }

    .place-order-btn:hover:not(:disabled) {
      background: var(--primary-hover);
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(255, 90, 95, 0.4);
    }

    .place-order-btn:disabled {
      background: var(--surface-3);
      color: var(--text-muted);
      box-shadow: none;
      cursor: not-allowed;
    }
    
    @media (max-width: 768px) {
      .restaurant-header { padding: 40px 20px; }
      .restaurant-header h1 { font-size: 2.2rem; }
      .customer-card, .order-summary-card { padding: 24px; }
      .controls-bar { flex-direction: column; align-items: stretch; }
      .search-container { max-width: 100%; }
      .order-table th, .order-table td { padding: 12px; }
    }
  </style>
</head>
<body>
<div class="container">

  <!-- Restaurant Header -->
  <div class="restaurant-header">
    <div class="header-icon-ring"><i class="fas fa-utensils"></i></div>
    <h1>{{ $restaurant_details->name ?? 'Premium Dining' }}</h1>
    <p class="header-tagline">Exquisite flavors crafted with passion.</p>
    @if($restaurant_details->gstin)
      <div class="gst-info-badge">
        <i class="fas fa-file-invoice-dollar"></i> GSTIN: {{ $restaurant_details->gstin }} &nbsp;|&nbsp; GST: {{ $restaurant_details->gst_percentage ?? 0 }}%
      </div>
    @else
      <div class="gst-info-badge"><i class="fas fa-receipt"></i> Non-GST Bill</div>
    @endif
  </div>

  <!-- Customer Details -->
  <div class="customer-card">
    <div class="section-label">
      <i class="fas fa-user-circle" style="font-size: 1.5rem; color: var(--primary);"></i>
      <h5>Guest Details</h5>
    </div>
    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">Full Name <span class="text-danger">*</span></label>
        <input type="text" id="customer_name" class="form-control" placeholder="Enter your full name">
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">Phone Number <span class="text-danger">*</span></label>
        <input type="text" id="phone" class="form-control" placeholder="Enter your contact number">
      </div>
    </div>
    <input type="hidden" id="table_id" value="{{ $table_id }}">
    <input type="hidden" id="restaurant_id" value="{{ $restaurant_id }}">
    <input type="hidden" id="is_gst_registered" value="{{ $restaurant_details->gstin ? 'true' : 'false' }}">
    <input type="hidden" id="gst_percentage" value="{{ $restaurant_details->gst_percentage ?? 0 }}">
  </div>

  <!-- Controls -->
  <div class="controls-bar">
    <div class="search-container">
      <i class="fas fa-search"></i>
      <input type="text" id="searchBox" class="form-control" placeholder="Search for dishes...">
    </div>
    <div class="filter-container">
      <button class="filter-btn active" data-type="">All Items</button>
      <button class="filter-btn" data-type="veg"><i class="fas fa-leaf" style="color:var(--success)"></i> Veg</button>
      <button class="filter-btn" data-type="non-veg"><i class="fas fa-drumstick-bite" style="color:var(--danger)"></i> Non-Veg</button>
    </div>
  </div>

  <!-- Tabs -->
  <div class="category-tabs-wrapper">
    <ul class="nav category-tabs" role="tablist">
      @foreach($categories as $key => $cat)
        <li class="nav-item">
          <a class="nav-link {{ $key==0?'active':'' }}" data-toggle="tab" href="#cat{{ $cat->id }}">{{ $cat->name }}</a>
        </li>
      @endforeach
    </ul>
  </div>

  <!-- Tab Content -->
  <div class="tab-content">
    @foreach($categories as $key => $cat)
    <div class="tab-pane fade {{ $key==0?'show active':'' }}" id="cat{{ $cat->id }}">
      <div class="food-grid">
        @foreach($cat->subcategories as $item)
        <div class="food-card-wrapper" data-name="{{ strtolower($item->name) }}" data-type="{{ strtolower($item->food_type) }}">
          <div class="food-card">
            <div class="food-image-container">
              @if($item->image)
                <img src="{{ URL::to('storage/app/public/category') }}/{{ $item->image }}" alt="{{ $item->name }}" class="food-image" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&h=300&fit=crop'">
              @else
                <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&h=300&fit=crop" alt="{{ $item->name }}" class="food-image">
              @endif
              <div class="food-badge {{ strtolower($item->food_type) == 'veg' ? 'veg-badge' : 'nonveg-badge' }}">
                <i class="fas {{ strtolower($item->food_type) == 'veg' ? 'fa-circle' : 'fa-play fa-rotate-270' }}"></i>
              </div>
              @if(($item->discount_percentage ?? 0) > 0)
                <div class="discount-badge">{{ $item->discount_percentage }}% OFF</div>
              @endif
            </div>
            <div class="food-details">
              <h5 class="food-title">{{ $item->name }}</h5>
              <p class="food-description">{{ $item->description ?? 'Expertly crafted dish using the finest ingredients.' }}</p>
              <div class="food-footer">
                <div>
                  <div class="food-price">
                    @if(($item->discount_percentage ?? 0) > 0)
                      <del>₹{{ number_format($item->price, 2) }}</del>
                      ₹{{ number_format($item->price - ($item->price * $item->discount_percentage / 100), 2) }}
                    @else
                      ₹{{ number_format($item->price, 2) }}
                    @endif
                  </div>
                  @if($restaurant_details->gstin)
                    <div class="gst-hint">+ {{ $restaurant_details->gst_percentage ?? 0 }}% GST</div>
                  @endif
                </div>
                <button class="add-to-cart-btn addItemBtn" data-id="{{ $item->id }}" data-name="{{ $item->name }}" data-price="{{ $item->price }}" data-discount="{{ $item->discount_percentage ?? 0 }}">
                  Add <i class="fas fa-plus"></i>
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
    <div class="section-label">
      <i class="fas fa-receipt" style="font-size: 1.5rem; color: var(--primary);"></i>
      <h5>Your Order Summary</h5>
    </div>

    <div id="orderItemsContainer">
      <div class="order-table-wrapper table-responsive" style="display:none;">
        <table class="order-table">
          <thead>
            <tr>
              <th>Item</th>
              <th style="text-align: center;">Qty</th>
              <th>Unit Price</th>
              <th>Discount</th>
              <th>Taxable</th>
              @if($restaurant_details->gstin) <th>GST</th> @endif
              <th>Total</th>
              <th></th>
            </tr>
          </thead>
          <tbody id="orderItemsBody"></tbody>
        </table>
      </div>
      
      <div id="emptyOrderState" class="empty-state" style="display: block;">
        <i class="fas fa-shopping-basket"></i>
        <h5>Your tray is empty</h5>
        <p>Explore our menu and add your favorite dishes.</p>
      </div>
    </div>

    <div class="totals-section">
      <div class="total-row">
        <span>Subtotal</span>
        <span>₹<span id="original_subtotal">0.00</span></span>
      </div>
      <div class="total-row" style="color: var(--success);">
        <span>Discounts</span>
        <span>− ₹<span id="item_discount">0.00</span></span>
      </div>
      <div class="total-row">
        <span>Taxable Amount</span>
        <span>₹<span id="taxable_amount">0.00</span></span>
      </div>
      @if($restaurant_details->gstin)
      <div class="total-row">
        <span>GST ({{ $restaurant_details->gst_percentage ?? 0 }}%)</span>
        <span>₹<span id="gst_amount">0.00</span></span>
      </div>
      @endif
      <div class="total-row final">
        <span>Grand Total</span>
        <span>₹<span id="final_total">0.00</span></span>
      </div>
    </div>
  </div>

</div><!-- /container -->

<!-- Fixed Action Bar -->
<div class="order-action-area">
  <button class="place-order-btn" id="placeOrderBtn" disabled>
    Review & Confirm Order <i class="fas fa-arrow-right ms-2"></i>
  </button>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
let cart = [];
let isGstRegistered = $('#is_gst_registered').val() === 'true';
let gstPercentage = parseFloat($('#gst_percentage').val());

function calculateItemDetails(originalPrice, qty, discountPercent = 0) {
    let discountedPrice = originalPrice - (originalPrice * discountPercent / 100);
    let taxableAmount = discountedPrice * qty;
    let gstAmount = isGstRegistered ? (taxableAmount * gstPercentage) / 100 : 0;
    let totalAmount = taxableAmount + gstAmount;
    return {
        discountedPrice: discountedPrice,
        taxableAmount: taxableAmount,
        gstAmount: gstAmount,
        totalAmount: totalAmount,
        discountAmount: (originalPrice * qty) - taxableAmount
    };
}

function updateEmptyState() {
    if (cart.length === 0) {
        $('#emptyOrderState').show();
        $('.order-table-wrapper').hide();
        $('#placeOrderBtn').prop('disabled', true);
    } else {
        $('#emptyOrderState').hide();
        $('.order-table-wrapper').show();
        $('#placeOrderBtn').prop('disabled', false);
    }
}

function refreshTable() {
    let tbody = $('#orderItemsBody');
    tbody.html('');
    let originalSubtotal = 0, totalTaxable = 0, totalGst = 0, totalDiscount = 0;

    cart.forEach((item, i) => {
        let details = calculateItemDetails(item.price, item.qty, item.discount);
        originalSubtotal += item.price * item.qty;
        totalTaxable    += details.taxableAmount;
        totalGst        += details.gstAmount;
        totalDiscount   += details.discountAmount;

        let row = `
            <tr>
                <td class="item-name-cell">
                    <strong>${item.name}</strong>
                </td>
                <td>
                    <div class="quantity-controls">
                        <button class="qty-btn decreaseQty" data-index="${i}">−</button>
                        <span class="qty-value">${item.qty}</span>
                        <button class="qty-btn increaseQty" data-index="${i}">+</button>
                    </div>
                </td>
                <td>
                    ${item.discount > 0 ? `<del style="display:block;color:var(--text-muted);font-size:0.78rem;">₹${item.price.toFixed(2)}</del>` : ''}
                    ₹${details.discountedPrice.toFixed(2)}
                </td>
                <td style="color:var(--success)">
                    ${item.discount > 0 ? `− ₹${details.discountAmount.toFixed(2)}` : '<span style="color:var(--text-muted)">—</span>'}
                </td>
                <td>₹${details.taxableAmount.toFixed(2)}</td>`;

        if (isGstRegistered) {
            row += `<td>₹${details.gstAmount.toFixed(2)}</td>`;
        }

        row += `<td style="color:var(--gold-light);font-weight:600;">₹${details.totalAmount.toFixed(2)}</td>
                <td>
                    <button class="remove-btn removeItem" data-index="${i}" title="Remove">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>`;
        tbody.append(row);
    });

    $('#original_subtotal').text(originalSubtotal.toFixed(2));
    $('#item_discount').text(totalDiscount.toFixed(2));
    $('#taxable_amount').text(totalTaxable.toFixed(2));
    if (isGstRegistered) $('#gst_amount').text(totalGst.toFixed(2));
    $('#final_total').text((totalTaxable + totalGst).toFixed(2));
    updateEmptyState();
}

/* Add to cart */
$(document).on('click', '.addItemBtn', function() {
    let itemId   = $(this).data('id');
    let existing = cart.find(i => i.id === itemId);
    if (existing) {
        existing.qty++;
    } else {
        cart.push({
            id:       itemId,
            name:     $(this).data('name'),
            price:    parseFloat($(this).data('price')),
            qty:      1,
            discount: parseFloat($(this).data('discount')) || 0
        });
    }
    let btn = $(this);
    btn.addClass('added').html('<i class="fas fa-check"></i> Added');
    setTimeout(() => { btn.removeClass('added').html('<i class="fas fa-plus"></i> Add'); }, 1000);
    refreshTable();
});

/* Qty controls */
$(document).on('click', '.increaseQty', function() {
    cart[$(this).data('index')].qty++;
    refreshTable();
});
$(document).on('click', '.decreaseQty', function() {
    let idx = $(this).data('index');
    if (cart[idx].qty > 1) { cart[idx].qty--; refreshTable(); }
});
$(document).on('click', '.removeItem', function() {
    cart.splice($(this).data('index'), 1);
    refreshTable();
});

/* Search */
$('#searchBox').on('input', function() {
    let val = $(this).val().toLowerCase();
    $('.food-card-wrapper').each(function() {
        $(this).toggle($(this).data('name').includes(val));
    });
});

/* Filter */
$('.filter-btn').click(function() {
    $('.filter-btn').removeClass('active');
    $(this).addClass('active');
    let type = $(this).data('type');
    $('.food-card-wrapper').each(function() {
        $(this).toggle(type === '' || $(this).data('type') === type);
    });
});

/* Place Order */
$('#placeOrderBtn').click(function() {
    if (cart.length === 0) { alert('Please add items to your order'); return; }
    let name  = $('#customer_name').val().trim();
    let phone = $('#phone').val().trim();
    if (!name)  { alert('Please enter your name'); $('#customer_name').focus(); return; }
    if (!phone) { alert('Please enter your phone number'); $('#phone').focus(); return; }

    let orderItems = cart.map(item => ({
        id: item.id, name: item.name, price: item.price, qty: item.qty, item_discount: item.discount
    }));

    $(this).html('<i class="fas fa-spinner fa-spin"></i> Processing…').prop('disabled', true);

    $.post("{{ route('temp.order.store') }}", {
        _token:          "{{ csrf_token() }}",
        customer_name:   name,
        customer_phone:  phone,
        table_id:        $('#table_id').val(),
        restaurant_id:   $('#restaurant_id').val(),
        order_items:     orderItems
    }, function(res) {
        if (res.status) {
            window.location.href = res.redirect;
        } else {
            alert('Something went wrong. Please try again.');
            $('#placeOrderBtn').html('<i class="fas fa-paper-plane"></i> Confirm Order').prop('disabled', false);
        }
    }).fail(function() {
        alert('Network error. Please check your connection and try again.');
        $('#placeOrderBtn').html('<i class="fas fa-paper-plane"></i> Confirm Order').prop('disabled', false);
    });
});

$(document).ready(function() { updateEmptyState(); });
</script>
</body>
</html>