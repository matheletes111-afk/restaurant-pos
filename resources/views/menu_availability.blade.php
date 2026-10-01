<!DOCTYPE html>
<html lang="en">
<head>
  <title>Menu Availability &amp; Live Discounts | Bill&amp;Bite POS</title>
  @include('includes.style')
  <!-- Font Awesome 6.5.1 (Standard Rendering) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <!-- Google Fonts: Outfit & Public Sans -->
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <!-- Dedicated Admin Stylesheet -->
  <link rel="stylesheet" href="{{ asset('admin_template/css/menu-availability.css') }}">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body data-pc-theme="light">
  <div class="loader-bg">
    <div class="loader-track"><div class="loader-fill"></div></div>
  </div>

  @include('includes.sidebar')

  <div class="pc-container">
    <div class="pc-content">

      <!-- ===================================================
           PAGE HEADER & LIVE SYNC STATUS
           =================================================== -->
      <div class="availability-header-card">
        <div class="availability-header-left">
          <div class="availability-header-icon">
            <i class="fa-solid fa-toggle-on"></i>
          </div>
          <div class="availability-header-meta">
            <span class="availability-eyebrow-tag">Live Menu Management</span>
            <h1 class="availability-main-title">Menu Availability &amp; Discounts</h1>
            <p class="availability-sub-text">Control real-time item visibility on POS / QR menus and apply instant promotional discounts.</p>
          </div>
        </div>

        <div class="availability-header-right-badge">
          <span class="pulse-dot"></span>
          <span>Live POS Sync Active</span>
        </div>
      </div>

      <!-- ===================================================
           KPI LIVE METRICS DECK
           =================================================== -->
      <div class="stats-kpi-grid">
        <!-- Total Items -->
        <div class="kpi-stat-card kpi-card-total">
          <div class="kpi-icon-avatar">
            <i class="fa-solid fa-utensils"></i>
          </div>
          <div class="kpi-stat-info">
            <div class="kpi-stat-number" id="totalCount">0</div>
            <div class="kpi-stat-label">Total Menu Items</div>
          </div>
        </div>

        <!-- Available Items -->
        <div class="kpi-stat-card kpi-card-active">
          <div class="kpi-icon-avatar">
            <i class="fa-solid fa-circle-check"></i>
          </div>
          <div class="kpi-stat-info">
            <div class="kpi-stat-number" id="activeCount">0</div>
            <div class="kpi-stat-label">Available (In Stock)</div>
          </div>
        </div>

        <!-- Unavailable Items -->
        <div class="kpi-stat-card kpi-card-inactive">
          <div class="kpi-icon-avatar">
            <i class="fa-solid fa-circle-xmark"></i>
          </div>
          <div class="kpi-stat-info">
            <div class="kpi-stat-number" id="inactiveCount">0</div>
            <div class="kpi-stat-label">Unavailable (86'd)</div>
          </div>
        </div>

        <!-- Veg Items -->
        <div class="kpi-stat-card kpi-card-veg">
          <div class="kpi-icon-avatar">
            <i class="fa-solid fa-leaf"></i>
          </div>
          <div class="kpi-stat-info">
            <div class="kpi-stat-number" id="vegCount">0</div>
            <div class="kpi-stat-label">Vegetarian Dishes</div>
          </div>
        </div>
      </div>

      <!-- ===================================================
           SEARCH & SWIPEABLE FILTER PILLS TOOLBAR
           =================================================== -->
      <div class="availability-toolbar-card">
        <div class="availability-search-wrap">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" id="searchInput" class="availability-search-input" placeholder="Search by dish name or category...">
        </div>

        <div class="availability-filter-pills">
          <button type="button" class="filter-pill-btn active" data-filter="all">
            <i class="fa-solid fa-layer-group me-1"></i> All Items
          </button>
          <button type="button" class="filter-pill-btn" data-filter="active">
            <i class="fa-solid fa-circle-check text-success me-1"></i> Available
          </button>
          <button type="button" class="filter-pill-btn" data-filter="inactive">
            <i class="fa-solid fa-circle-pause text-danger me-1"></i> Unavailable
          </button>
          <button type="button" class="filter-pill-btn" data-filter="veg">
            <i class="fa-solid fa-leaf text-success me-1"></i> Veg Only
          </button>
          <button type="button" class="filter-pill-btn" data-filter="nonveg">
            <i class="fa-solid fa-drumstick-bite text-danger me-1"></i> Non-Veg
          </button>
          <button type="button" class="filter-pill-btn" data-filter="discounted">
            <i class="fa-solid fa-tag text-warning me-1"></i> On Discount
          </button>
        </div>
      </div>

      <!-- ===================================================
           FOOD ITEMS AVAILABILITY GRID
           =================================================== -->
      <div class="availability-product-grid" id="productGrid">
        @forelse($data as $product)
        <div class="availability-product-card product-grid-item"
             data-name="{{ strtolower($product->name) }}"
             data-category="{{ strtolower($product->category->name ?? '') }}"
             data-status="{{ $product->status }}"
             data-foodtype="{{ $product->food_type }}"
             data-discount="{{ $product->discount_percentage ?? 0 }}"
             data-price="{{ $product->price }}">

          <!-- Media Thumbnail -->
          <div class="avail-media-wrap">
            <!-- Category Tag Badge -->
            <span class="category-top-badge" title="{{ $product->category->name ?? 'Uncategorized' }}">
              <i class="fa-solid fa-folder-open"></i>
              <span>{{ $product->category->name ?? 'General' }}</span>
            </span>

            <!-- FSSAI Veg / Non-Veg Indicator -->
            <div class="fssai-type-badge {{ $product->food_type == 'VEG' ? 'veg' : 'non-veg' }}" title="{{ $product->food_type == 'VEG' ? 'Pure Veg' : 'Non-Veg' }}">
              @if($product->food_type == 'VEG')
                <i class="fa-solid fa-leaf"></i>
              @else
                <i class="fa-solid fa-drumstick-bite"></i>
              @endif
            </div>

            @if($product->image)
              <img src="{{ URL::to('storage/category') }}/{{ $product->image }}" alt="{{ $product->name }}" class="avail-media-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
              <div class="avail-media-placeholder" style="display: none;">
                <div class="placeholder-dish-initial">{{ strtoupper(substr($product->name, 0, 1)) }}</div>
              </div>
            @else
              <div class="avail-media-placeholder">
                <div class="placeholder-dish-initial">{{ strtoupper(substr($product->name, 0, 1)) }}</div>
              </div>
            @endif
            <div class="avail-media-overlay"></div>
          </div>

          <!-- Card Content Body -->
          <div class="avail-card-body">
            <!-- Dish Title -->
            <h3 class="avail-dish-title" title="{{ $product->name }}">
              {{ $product->name }}
            </h3>

            <!-- Price & Real-Time Discount Section -->
            <div class="avail-price-box price-section">
              @php
                $discountPercent = (float)($product->discount_percentage ?? 0);
                $originalPrice = (float)$product->price;
                $discountedPrice = $originalPrice - ($originalPrice * $discountPercent / 100);
              @endphp

              <div class="avail-price-left">
                @if($discountPercent > 0)
                  <span class="avail-original-price">₹{{ number_format($originalPrice, 2) }}</span>
                  <span class="avail-final-price">₹{{ number_format($discountedPrice, 2) }}</span>
                @else
                  <span class="avail-final-price">₹{{ number_format($originalPrice, 2) }}</span>
                @endif
              </div>

              @if($discountPercent > 0)
                <span class="avail-discount-badge">{{ $discountPercent }}% OFF</span>
              @endif
            </div>

            <!-- Discount Editor Field -->
            <div class="avail-discount-control">
              <div class="avail-discount-label">
                <span><i class="fa-solid fa-percent text-warning"></i> Set Promo Discount</span>
                <small class="text-muted">Live sync</small>
              </div>
              <div class="avail-discount-input-group">
                <input type="number"
                       class="avail-discount-input discount-input"
                       id="discount_{{ $product->id }}"
                       data-id="{{ $product->id }}"
                       value="{{ $product->discount_percentage ?? 0 }}"
                       min="0"
                       max="100"
                       step="1"
                       placeholder="0">
                <span class="avail-discount-addon">%</span>
              </div>
            </div>

            <!-- Card Actions Footer / Toggle -->
            <div class="avail-card-footer">
              <span class="avail-status-chip {{ $product->status == 'A' ? 'active' : 'inactive' }}" id="statusText_{{ $product->id }}">
                <i class="fa-solid {{ $product->status == 'A' ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
                <span>{{ $product->status == 'A' ? 'Available' : 'Unavailable' }}</span>
              </span>

              <label class="avail-toggle-switch" title="Toggle Item Availability">
                <input type="checkbox"
                       class="status-toggle"
                       data-id="{{ $product->id }}"
                       {{ $product->status == 'A' ? 'checked' : '' }}>
                <span class="avail-toggle-slider"></span>
              </label>
            </div>

          </div><!-- /.avail-card-body -->

        </div><!-- /.availability-product-card -->
        @empty
        <!-- Empty State -->
        <div class="pos-empty-container">
          <div class="pos-empty-icon-circle">
            <i class="fa-solid fa-utensils"></i>
          </div>
          <h3 class="pos-empty-title">No Menu Dishes Found</h3>
          <p class="pos-empty-desc">Add food items from Category Management to monitor stock availability and configure real-time promotional discounts.</p>
        </div>
        @endforelse
      </div>

    </div><!-- /.pc-content -->
  </div><!-- /.pc-container -->

  <!-- ===================================================
       LOADING OVERLAY & TOAST NOTIFICATION
       =================================================== -->
  <div class="pos-loading-backdrop" id="loadingOverlay">
    <div class="pos-spinner-ring"></div>
  </div>

  <div class="pos-toast-box" id="toastNotification">
    <i class="fa-solid fa-circle-check pos-toast-icon"></i>
    <span class="pos-toast-text" id="toastMessage">Status updated successfully!</span>
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  @include('includes.script')

  <script>
    $(document).ready(function() {
      // Setup CSRF Token for AJAX
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      // Update KPI Counter Stats
      function updateStats() {
        let total = $('.product-grid-item').length;
        let active = $('.product-grid-item[data-status="A"]').length;
        let inactive = $('.product-grid-item[data-status="I"]').length;
        let veg = $('.product-grid-item[data-foodtype="VEG"]').length;

        $('#totalCount').text(total);
        $('#activeCount').text(active);
        $('#inactiveCount').text(inactive);
        $('#vegCount').text(veg);
      }

      // Show Floating POS Toast
      let toastTimer;
      function showToast(message, isError = false) {
        let toast = $('#toastNotification');
        toast.find('#toastMessage').text(message);

        if (isError) {
          toast.addClass('error');
          toast.find('i').attr('class', 'fa-solid fa-circle-exclamation pos-toast-icon');
        } else {
          toast.removeClass('error');
          toast.find('i').attr('class', 'fa-solid fa-circle-check pos-toast-icon');
        }

        toast.addClass('show');
        if (toastTimer) clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
          toast.removeClass('show');
        }, 3000);
      }

      // Update Discount UI Display
      function updateDiscountDisplay(card, discountPercent, originalPrice) {
        let priceSection = card.find('.price-section');
        let discount = parseFloat(discountPercent) || 0;
        let discountedPrice = originalPrice - (originalPrice * discount / 100);

        if (discount > 0) {
          let html = `
            <div class="avail-price-left">
              <span class="avail-original-price">₹${originalPrice.toFixed(2)}</span>
              <span class="avail-final-price">₹${discountedPrice.toFixed(2)}</span>
            </div>
            <span class="avail-discount-badge">${discount}% OFF</span>
          `;
          priceSection.html(html);
        } else {
          let html = `
            <div class="avail-price-left">
              <span class="avail-final-price">₹${originalPrice.toFixed(2)}</span>
            </div>
          `;
          priceSection.html(html);
        }

        card.attr('data-discount', discount);
        card.data('discount', discount);
      }

      // Debounced Live Discount Input Handler
      let discountTimeout;
      $('.discount-input').on('input', function() {
        let input = $(this);
        let id = input.data('id');
        let newDiscount = parseFloat(input.val());
        let card = input.closest('.product-grid-item');
        let originalPrice = parseFloat(card.data('price'));

        if (isNaN(newDiscount)) newDiscount = 0;
        if (newDiscount < 0) newDiscount = 0;
        if (newDiscount > 100) newDiscount = 100;

        if (input.val() != newDiscount) {
          input.val(newDiscount);
        }

        input.addClass('loading');
        if (discountTimeout) clearTimeout(discountTimeout);

        discountTimeout = setTimeout(function() {
          $.ajax({
            url: '{{ route("menu.discount.update") }}',
            type: 'POST',
            data: {
              id: id,
              discount_percentage: newDiscount
            },
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                updateDiscountDisplay(card, newDiscount, originalPrice);
                input.addClass('discount-updated-pulse');
                setTimeout(() => input.removeClass('discount-updated-pulse'), 500);
                showToast(response.message);
              } else {
                let oldDiscount = card.data('discount');
                input.val(oldDiscount);
                showToast(response.message || 'Failed to update discount', true);
              }
            },
            error: function(xhr) {
              let oldDiscount = card.data('discount');
              input.val(oldDiscount);
              let errorMsg = xhr.responseJSON?.message || 'Network error. Please try again.';
              showToast(errorMsg, true);
            },
            complete: function() {
              input.removeClass('loading');
            }
          });
        }, 500);
      });

      // Discount Blur Handler
      $('.discount-input').on('blur', function() {
        let input = $(this);
        let id = input.data('id');
        let newDiscount = parseFloat(input.val());

        if (isNaN(newDiscount)) newDiscount = 0;
        if (newDiscount < 0) newDiscount = 0;
        if (newDiscount > 100) newDiscount = 100;

        if (input.val() != newDiscount) {
          input.val(newDiscount);
        }

        let card = input.closest('.product-grid-item');
        let originalPrice = parseFloat(card.data('price'));
        let oldDiscount = parseFloat(card.data('discount'));

        if (newDiscount !== oldDiscount) {
          if (discountTimeout) clearTimeout(discountTimeout);
          input.addClass('loading');

          $.ajax({
            url: '{{ route("menu.discount.update") }}',
            type: 'POST',
            data: {
              id: id,
              discount_percentage: newDiscount
            },
            dataType: 'json',
            success: function(response) {
              if (response.success) {
                updateDiscountDisplay(card, newDiscount, originalPrice);
                showToast(response.message);
              } else {
                input.val(oldDiscount);
                showToast(response.message || 'Failed to update discount', true);
              }
            },
            error: function(xhr) {
              input.val(oldDiscount);
              showToast('Network error. Please try again.', true);
            },
            complete: function() {
              input.removeClass('loading');
            }
          });
        }
      });

      // Toggle Availability via AJAX
      $('.status-toggle').on('change', function() {
        let toggle = $(this);
        let id = toggle.data('id');
        let isChecked = toggle.is(':checked');
        let card = toggle.closest('.product-grid-item');
        let statusBadge = $('#statusText_' + id);

        $('#loadingOverlay').addClass('active');
        toggle.prop('disabled', true);

        $.ajax({
          url: '{{ route("menu.availability.toggle") }}',
          type: 'POST',
          data: { id: id },
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              card.attr('data-status', response.status);
              card.data('status', response.status);

              if (response.status === 'A') {
                statusBadge.html('<i class="fa-solid fa-circle-check"></i> <span>Available</span>');
                statusBadge.removeClass('inactive').addClass('active');
              } else {
                statusBadge.html('<i class="fa-solid fa-circle-xmark"></i> <span>Unavailable</span>');
                statusBadge.removeClass('active').addClass('inactive');
              }

              updateStats();
              showToast(response.message);

              let activeFilter = $('.filter-pill-btn.active').data('filter');
              applyFilter(activeFilter);
            } else {
              toggle.prop('checked', !isChecked);
              showToast(response.message || 'Something went wrong', true);
            }
          },
          error: function(xhr) {
            toggle.prop('checked', !isChecked);
            let errorMsg = xhr.responseJSON?.message || 'Network error. Please try again.';
            showToast(errorMsg, true);
          },
          complete: function() {
            $('#loadingOverlay').removeClass('active');
            toggle.prop('disabled', false);
          }
        });
      });

      // Real-Time Search & Filter Implementation
      function applyFilter(filter) {
        let searchTerm = $('#searchInput').val().toLowerCase().trim();

        $('.product-grid-item').each(function() {
          let item = $(this);
          let name = item.data('name') || '';
          let category = item.data('category') || '';
          let status = item.attr('data-status') || item.data('status');
          let foodType = item.data('foodtype');
          let discount = parseFloat(item.attr('data-discount') || item.data('discount') || 0);

          let matchesSearch = (name.includes(searchTerm) || category.includes(searchTerm));
          let matchesFilter = true;

          switch(filter) {
            case 'active':
              matchesFilter = (status === 'A');
              break;
            case 'inactive':
              matchesFilter = (status === 'I');
              break;
            case 'veg':
              matchesFilter = (foodType === 'VEG');
              break;
            case 'nonveg':
              matchesFilter = (foodType === 'NON-VEG');
              break;
            case 'discounted':
              matchesFilter = (discount > 0);
              break;
            default:
              matchesFilter = true;
          }

          if (matchesSearch && matchesFilter) {
            item.show();
          } else {
            item.hide();
          }
        });

        let visibleCount = $('.product-grid-item:visible').length;
        if (visibleCount === 0) {
          if ($('#productGrid .empty-search-message').length === 0) {
            $('#productGrid').append(`
              <div class="pos-empty-container empty-search-message">
                <div class="pos-empty-icon-circle">
                  <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <h3 class="pos-empty-title">No Matching Dishes Found</h3>
                <p class="pos-empty-desc">Try searching for a different keyword or resetting your filter pills.</p>
              </div>
            `);
          }
        } else {
          $('.empty-search-message').remove();
        }
      }

      $('#searchInput').on('keyup', function() {
        let activeFilter = $('.filter-pill-btn.active').data('filter');
        applyFilter(activeFilter);
      });

      $('.filter-pill-btn').on('click', function() {
        $('.filter-pill-btn').removeClass('active');
        $(this).addClass('active');
        let filter = $(this).data('filter');
        applyFilter(filter);
      });

      updateStats();
    });
  </script>
</body>
</html>