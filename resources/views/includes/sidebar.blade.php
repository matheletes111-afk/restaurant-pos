<style type="text/css">
  /* =========================================================
     PREMIUM SIDEBAR THEME & ACTIVE MENU BACKGROUND
     ========================================================= */
  .pc-sidebar {
    background: #0f172a !important;
    background: linear-gradient(180deg, #0f172a 0%, #111827 50%, #090d16 100%) !important;
    border-right: 1px solid rgba(255, 255, 255, 0.07) !important;
    box-shadow: 4px 0 25px rgba(0, 0, 0, 0.18) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
  }
  .pc-sidebar .navbar-wrapper {
    background: transparent !important;
  }
  
  /* Brand Header Container */
  .pc-sidebar .m-header {
    background: rgba(255, 255, 255, 0.03) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.07) !important;
    padding: 14px 18px !important;
    height: auto !important;
    min-height: 70px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
  }
  .pc-sidebar .m-header .b-brand {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 100% !important;
  }
  .pc-sidebar .m-header img {
    max-height: 42px !important;
    width: auto !important;
    object-fit: contain !important;
    background: #ffffff;
    padding: 6px 14px;
    border-radius: 10px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
  }

  /* Section Captions */
  .pc-sidebar .pc-caption {
    padding: 16px 16px 6px 16px !important;
  }
  .pc-sidebar .pc-caption label {
    color: #64748b !important;
    font-size: 11px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.8px !important;
  }

  /* Navigation Items & Links */
  .pc-sidebar .pc-navbar {
    padding: 12px 10px 40px 10px !important;
  }
  .pc-sidebar .pc-item {
    margin-bottom: 3px !important;
    position: relative !important;
  }
  .pc-sidebar .pc-link {
    color: #94a3b8 !important;
    padding: 10px 14px !important;
    border-radius: 10px !important;
    font-size: 13.5px !important;
    font-weight: 500 !important;
    display: flex !important;
    align-items: center !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    border: 1px solid transparent !important;
    text-decoration: none !important;
    position: relative !important;
  }
  .pc-sidebar .pc-link:hover {
    color: #ffffff !important;
    background: rgba(255, 255, 255, 0.08) !important;
    transform: translateX(3px) !important;
  }
  .pc-sidebar .pc-link:hover .pc-micon {
    color: #ff8c42 !important;
    transform: scale(1.1) !important;
  }
  .pc-sidebar .pc-micon {
    color: #94a3b8 !important;
    margin-right: 12px !important;
    width: 22px !important;
    text-align: center !important;
    font-size: 15.5px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    transition: all 0.2s ease !important;
  }
  .pc-sidebar .pc-micon i, .pc-sidebar .pc-micon svg {
    color: inherit !important;
    font-size: 15.5px !important;
  }
  .pc-sidebar .pc-mtext {
    flex: 1 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    letter-spacing: 0.2px !important;
  }
  .pc-sidebar .pc-arrow {
    color: #64748b !important;
    font-size: 12px !important;
    transition: transform 0.22s ease !important;
  }

  /* =========================================================
     ACTIVE MENU HIGHLIGHT (GLOWING BRAND ORANGE PILL)
     ========================================================= */
  .active_class,
  .pc-sidebar .pc-link.active_class,
  .pc-sidebar .pc-navbar > .pc-item.active > .pc-link:not(.pc-hasmenu > .pc-link) {
    background: linear-gradient(135deg, #ff6a00 0%, #ff8533 100%) !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    box-shadow: 0 4px 18px rgba(255, 106, 0, 0.45) !important;
    border-radius: 10px !important;
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    transform: none !important;
  }
  .active_class .pc-micon,
  .active_class .pc-micon i,
  .active_class .pc-mtext,
  .pc-sidebar .pc-link.active_class .pc-micon,
  .pc-sidebar .pc-link.active_class .pc-micon i,
  .pc-sidebar .pc-link.active_class .pc-mtext {
    color: #ffffff !important;
  }
  .pc-sidebar .pc-link.active_class::before,
  .active_class::before {
    display: none !important;
  }

  /* Active Parent Dropdown Menu Trigger */
  .pc-sidebar .pc-item.pc-hasmenu.pc-trigger > .pc-link,
  .pc-sidebar .pc-link.active-parent {
    background: rgba(255, 106, 0, 0.14) !important;
    color: #ff8c42 !important;
    border: 1px solid rgba(255, 106, 0, 0.28) !important;
    border-radius: 10px !important;
  }
  .pc-sidebar .pc-item.pc-hasmenu.pc-trigger > .pc-link .pc-micon,
  .pc-sidebar .pc-item.pc-hasmenu.pc-trigger > .pc-link .pc-arrow,
  .pc-sidebar .pc-link.active-parent .pc-micon,
  .pc-sidebar .pc-link.active-parent .pc-arrow {
    color: #ff8c42 !important;
  }

  /* Submenu Dropdown Container */
  .pc-sidebar .pc-submenu {
    background: rgba(0, 0, 0, 0.32) !important;
    border-radius: 10px !important;
    margin: 4px 4px 8px 4px !important;
    padding: 6px 4px !important;
    border: 1px solid rgba(255, 255, 255, 0.05) !important;
    list-style: none !important;
  }
  .pc-sidebar .pc-submenu .pc-item {
    margin-bottom: 2px !important;
  }
  .pc-sidebar .pc-submenu .pc-link {
    padding: 8px 12px 8px 20px !important;
    font-size: 13px !important;
    color: #94a3b8 !important;
    border-radius: 8px !important;
  }
  .pc-sidebar .pc-submenu .pc-link:hover {
    background: rgba(255, 255, 255, 0.08) !important;
    color: #ffffff !important;
    transform: translateX(2px) !important;
  }
  .pc-sidebar .pc-submenu .pc-link.active_class {
    background: linear-gradient(135deg, #ff6a00 0%, #ff8533 100%) !important;
    color: #ffffff !important;
    box-shadow: 0 3px 12px rgba(255, 106, 0, 0.35) !important;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
  }

  /* Disabled State */
  .sidebar-disabled-item {
    pointer-events: none !important;
    opacity: 0.45 !important;
    filter: grayscale(80%) blur(0.6px) !important;
    user-select: none !important;
  }

  /* Custom Sleek Scrollbar */
  .pc-sidebar .navbar-content::-webkit-scrollbar {
    width: 4px;
  }
  .pc-sidebar .navbar-content::-webkit-scrollbar-track {
    background: transparent;
  }
  .pc-sidebar .navbar-content::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.15);
    border-radius: 10px;
  }
  .pc-sidebar .navbar-content::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
  }
</style>

<nav class="pc-sidebar">
  <div class="navbar-wrapper">
    <div class="m-header">
      <a href="#" class="b-brand text-primary">
        <img src="{{asset('logo.png')}}" class="img-fluid logo-lg" alt="logo">
      </a>
    </div>
    <div class="navbar-content">
      <ul class="pc-navbar">

        @if(auth()->user()->role != "RES")
        {{-- ==================== ADMIN ROUTES ==================== --}}
        @php
          $saUser = auth()->user();
          $saPerms = $saUser->permissions ?? [];
        @endphp

        <li class="pc-item">
          <a href="{{route('admin.dashboard')}}" class="pc-link @if(Request::is('admin/dashboard*') || request()->routeIs('admin.dashboard')) active_class @endif">
            <span class="pc-micon"><i class="fas fa-sitemap"></i></span>
            <span class="pc-mtext">Dashboard</span>
          </a>
        </li>

        @if($saUser->id == 1 || in_array('restaurant_master', $saPerms))
        <li class="pc-item">
          <a href="{{route('manage.restaurant')}}" class="pc-link @if(Request::is('*manage-restaurant*')) active_class @endif">
            <span class="pc-micon"><i class="fas fa-utensils"></i></span>
            <span class="pc-mtext">Restaurant Master</span>
          </a>
        </li>
        @endif

        @if($saUser->id == 1 || in_array('plan_master', $saPerms))
        <li class="pc-item">
          <a href="{{route('plans.index')}}" class="pc-link @if(Request::is('*plans*') && !Request::is('*manage-restaurant/plans*')) active_class @endif">
            <span class="pc-micon"><i class="fas fa-layer-group"></i></span>
            <span class="pc-mtext">Plan Master</span>
          </a>
        </li>
        @endif

        @if($saUser->id == 1 || in_array('payment_history', $saPerms))
        <li class="pc-item">
          <a href="{{route('admin.payment.history')}}" class="pc-link @if(Request::is('*payment-history*')) active_class @endif">
            <span class="pc-micon"><i class="fas fa-receipt"></i></span>
            <span class="pc-mtext">Payment History</span>
          </a>
        </li>
        @endif

        @if($saUser->id == 1 || in_array('admin_crm', $saPerms))
        <li class="pc-item">
          <a href="{{route('admin.crm.index')}}" class="pc-link @if(Request::is('*crm*')) active_class @endif">
            <span class="pc-micon"><i class="fas fa-address-book"></i></span>
            <span class="pc-mtext">Admin CRM</span>
          </a>
        </li>
        @endif

        @if($saUser->id == 1 || in_array('marketing_notifications', $saPerms))
        <li class="pc-item">
          <a href="{{route('admin.marketing.index')}}" class="pc-link @if(Request::is('*marketing*')) active_class @endif">
            <span class="pc-micon"><i class="fas fa-paper-plane"></i></span>
            <span class="pc-mtext">Send Notification</span>
          </a>
        </li>
        @endif

        @if($saUser->id == 1 || in_array('customer_support', $saPerms))
        <li class="pc-item">
          <a href="{{ route('admin.support.tickets') }}" class="pc-link @if(Request::is('*admin-support*')) active_class @endif">
            <span class="pc-micon"><i class="fas fa-headset"></i></span>
            <span class="pc-mtext">Customer Support</span>
          </a>
        </li>
        @endif

        @if($saUser->id == 1 || in_array('admin_user_management', $saPerms))
        <li class="pc-item">
          <a href="{{route('admin.users.index')}}" class="pc-link @if(Request::is('admin/users*') || Request::is('users*')) active_class @endif">
            <span class="pc-micon"><i class="fas fa-users-cog"></i></span>
            <span class="pc-mtext">Admin Users</span>
          </a>
        </li>
        @endif
        {{-- ==================== END ADMIN ROUTES ==================== --}}
        @endif


        @php
          $subRestId = method_exists(auth()->user(), 'getSubscriptionRestaurantId') ? auth()->user()->getSubscriptionRestaurantId() : auth()->user()->restaurant_id;
          $active = DB::table('subscriptions')
            ->where('user_id', $subRestId)
            ->where(function($query) {
                $query->where('status', 'active')
                      ->orWhere(function($q) {
                          $q->where('status', 'completed')
                            ->whereDate('end_date', '>=', now());
                      });
            })
            ->first();
          $plan_details = DB::table('plans')->where('id',@$active->plan_id)->first();
          $disabledClass = $active == "" ? 'sidebar-disabled-item' : '';
        @endphp

        @if(auth()->user()->role == "RES" || $active != "")
        {{-- ==================== RESTAURANT ROUTES ==================== --}}

        <li class="pc-item {{ $disabledClass }}">
          <a href="{{route('dashboard')}}" class="pc-link @if(request()->routeIs('dashboard') || (Request::is('dashboard*') && !Request::is('admin/dashboard*'))) active_class @endif">
            <span class="pc-micon"><i class="fas fa-sitemap"></i></span>
            <span class="pc-mtext">Dashboard</span>
          </a>
        </li>

        @if(auth()->user()->hasPermission('menu_master'))
        <li class="pc-item {{ $disabledClass }}">
          <a href="{{route('manage.category')}}" class="pc-link @if(Request::is('*manage-menu-category*') || Request::is('*manage-category*') || Request::is('*manage-food-items*')) active_class @endif">
            <span class="pc-micon"><i class="fas fa-concierge-bell"></i></span>
            <span class="pc-mtext">Menu Master</span>
          </a>
        </li>
        @endif

        @if(auth()->user()->hasPermission('menu_availability'))
        <li class="pc-item {{ $disabledClass }}">
          <a href="{{route('menu.availability')}}" class="pc-link @if(Request::is('*menu-availability*') || Request::is('*menu-discount*')) active_class @endif">
            <span class="pc-micon"><i class="fas fa-clipboard-list"></i></span>
            <span class="pc-mtext">Menu Availability</span>
          </a>
        </li>
        @endif

        @if(auth()->user()->hasPermission('table_master'))
        <li class="pc-item {{ $disabledClass }}">
          <a href="{{route('table.manage')}}" class="pc-link @if(Request::is('*table-manage*') || Request::is('*table*')) active_class @endif">
            <span class="pc-micon"><i class="fas fa-chair"></i></span>
            <span class="pc-mtext">Table Master</span>
          </a>
        </li>
        @endif

        @if(auth()->user()->hasPermission('order_master'))
        <li class="pc-item {{ $disabledClass }}">
          <a href="{{route('order.management.dashboard')}}" class="pc-link @if(Request::is('*order-management*') || Request::is('*order-management-dashboard*') || Request::is('admin/order*') || Request::is('order/*')) active_class @endif">
            <span class="pc-micon"><i class="fas fa-receipt"></i></span>
            <span class="pc-mtext">Order Master</span>
          </a>
        </li>
        @endif

        @if(auth()->user()->hasPermission('kitchen_order'))
        <li class="pc-item {{ $disabledClass }}">
          <a href="{{route('manage.kitchen-panel')}}" class="pc-link @if(Request::is('*kitchen*')) active_class @endif">
            <span class="pc-micon"><i class="fas fa-utensils"></i></span>
            <span class="pc-mtext">Kitchen Order</span>
          </a>
        </li>
        @endif

        @if(auth()->user()->hasPermission('pending_order'))
        <li class="pc-item {{ $disabledClass }}">
          <a href="{{ route('temp.orders') }}" class="pc-link @if(Request::is('*pending-temp-orders*') || Request::is('*temp-orders*') || Request::is('*temp-order*')) active_class @endif">
            <span class="pc-micon"><i class="fas fa-clock"></i></span>
            <span class="pc-mtext">Pending Order</span>
          </a>
        </li>
        @endif

        @if(auth()->user()->hasPermission('billing_subscription'))
        <li class="pc-item">
          <a href="{{ route('admin.subscriptions.index') }}" class="pc-link @if(Request::is('*subscriptions*') || Request::is('*plans/*/subscribe*')) active_class @endif">
            <span class="pc-micon"><i class="fas fa-credit-card"></i></span>
            <span class="pc-mtext">Billing & Subscription</span>
          </a>
        </li>
        @endif

        @if(auth()->user()->hasPermission('customer_support'))
        <li class="pc-item">
          <a href="{{ route('restaurant.support.tickets') }}" class="pc-link @if(Request::is('*restaurant-support*')) active_class @endif">
            <span class="pc-micon"><i class="fas fa-headset"></i></span>
            <span class="pc-mtext">Customer Support</span>
          </a>
        </li>
        @endif

        @if(auth()->user()->hasPermission('staff'))
        <li class="pc-item {{ $disabledClass }}">
          <a href="{{route('restaurant.staff.index')}}" class="pc-link @if(Request::is('*restaurant-staff*') || Request::is('*manage-staff*')) active_class @endif">
            <span class="pc-micon"><i class="fas fa-users"></i></span>
            <span class="pc-mtext">Staff</span>
          </a>
        </li>
        @endif

        @if(auth()->user()->hasPermission('cash_drawer'))
        <li class="pc-item {{ $disabledClass }}">
          <a href="{{ route('cash.drawer.index') }}" class="pc-link @if(Request::is('*cash-drawer*')) active_class @endif">
            <span class="pc-micon"><i class="fas fa-cash-register"></i></span>
            <span class="pc-mtext">Cash Drawer</span>
          </a>
        </li>
        @endif

        @if(auth()->user()->hasPermission('expense_management'))
        <li class="pc-item {{ $disabledClass }}">
          <a href="{{ route('expense.index') }}" class="pc-link @if(Request::is('*expense*')) active_class @endif">
            <span class="pc-micon"><i class="fas fa-wallet"></i></span>
            <span class="pc-mtext">Expense Management</span>
          </a>
        </li>
        @endif

        @if(auth()->user()->isOwner())
        <li class="pc-item {{ $disabledClass }}">
          <a href="{{ route('restaurant.outlets.index') }}" class="pc-link @if(Request::is('*outlets*')) active_class @endif">
            <span class="pc-micon"><i class="fas fa-store-alt"></i></span>
            <span class="pc-mtext">Outlets / Branches</span>
          </a>
        </li>
        @endif

        {{-- Inventory Setting Dropdown --}}
        @if(auth()->user()->hasPermission('inventory_setting'))
        @if(@$plan_details->inventory_checkbox=="Y" || $active == "")
        @php
          $isInvActive = Request::is('*manage-units*') || Request::is('*units*') || Request::is('*products*') || Request::is('*product*') || (Request::is('*supplier*') && !Request::is('*debit-note*')) || Request::is('*purchase*') || Request::is('*stock-out*') || Request::is('*debit-note*');
        @endphp
        <li class="pc-item pc-hasmenu {{ $isInvActive ? 'pc-trigger active' : '' }} {{ $disabledClass }}">
          <a href="#!" class="pc-link {{ $isInvActive ? 'active-parent' : '' }}">
            <span class="pc-micon">
              <i class="fas fa-boxes"></i>
            </span>
            <span class="pc-mtext">Inventory Setting</span>
            <span class="pc-arrow">
              <i data-feather="chevron-right"></i>
            </span>
          </a>

          <ul class="pc-submenu" style="{{ $isInvActive ? 'display: block;' : '' }}">
            <!-- Manage Units -->
            <li class="pc-item">
              <a href="{{ route('manage.units') }}" class="pc-link @if(Request::is('*manage-units*') || Request::is('*units*')) active_class @endif">
                <span class="pc-micon"><i class="fas fa-ruler-combined"></i></span>
                <span class="pc-mtext">Manage Units</span>
              </a>
            </li>

            <!-- Manage Products -->
            <li class="pc-item">
              <a href="{{ route('products.manage') }}" class="pc-link @if(Request::is('*products*') || Request::is('*product*')) active_class @endif">
                <span class="pc-micon"><i class="fas fa-th-list"></i></span>
                <span class="pc-mtext">Manage Products</span>
              </a>
            </li>

            <!-- Manage Suppliers -->
            <li class="pc-item">
              <a href="{{ route('suppliers.index') }}" class="pc-link @if((Request::is('*supplier*') || Request::is('*suppliers*')) && !Request::is('*debit-note*')) active_class @endif">
                <span class="pc-micon"><i class="fas fa-truck"></i></span>
                <span class="pc-mtext">Manage Suppliers</span>
              </a>
            </li>

            <!-- Manage Purchases -->
            <li class="pc-item">
              <a href="{{ route('purchases.index') }}" class="pc-link @if(Request::is('*purchases*') || Request::is('*purchase*')) active_class @endif">
                <span class="pc-micon"><i class="fas fa-file-invoice-dollar"></i></span>
                <span class="pc-mtext">Manage Purchases</span>
              </a>
            </li>

            <!-- Manage Stockout -->
            <li class="pc-item">
              <a href="{{ route('stock-outs.index') }}" class="pc-link @if(Request::is('*stock-outs*') || Request::is('*stock-out*')) active_class @endif">
                <span class="pc-micon"><i class="fas fa-box-open"></i></span>
                <span class="pc-mtext">Manage Stockout</span>
              </a>
            </li>

            <!-- Supplier Debit Note -->
            <li class="pc-item">
              <a href="{{ route('debit-notes.index') }}" class="pc-link @if(Request::is('*debit-notes*') || Request::is('*debit-note*')) active_class @endif">
                <span class="pc-micon"><i class="fas fa-file-invoice"></i></span>
                <span class="pc-mtext">Supplier Debit Note</span>
              </a>
            </li>
          </ul>
        </li>
        @endif
        @endif

        {{-- Reports Dropdown --}}
        @if(auth()->user()->hasPermission('reports'))
        @php
          $isReportsActive = Request::is('*report*') || Request::is('*item-gst-summary*') || Request::is('*inventory/live*');
        @endphp
        <li class="pc-item pc-hasmenu {{ $isReportsActive ? 'pc-trigger active' : '' }} {{ $disabledClass }}">
          <a href="#!" class="pc-link {{ $isReportsActive ? 'active-parent' : '' }}">
            <span class="pc-micon">
              <i class="ti ti-report-analytics"></i>
            </span>
            <span class="pc-mtext">Reports</span>
            <span class="pc-arrow">
              <i data-feather="chevron-right"></i>
            </span>
          </a>

          <ul class="pc-submenu" style="{{ $isReportsActive ? 'display: block;' : '' }}">
            <li class="pc-item">
              <a href="{{ route('order.report.management') }}" class="pc-link @if(Request::is('*report-order-management*')) active_class @endif">
                <span class="pc-micon"><i class="ti ti-file-text"></i></span>
                <span class="pc-mtext">Order Report</span>
              </a>
            </li>

            <li class="pc-item">
              <a href="{{ route('report.item.gst.summary') }}" class="pc-link @if(Request::is('*item-gst-summary*')) active_class @endif">
                <span class="pc-micon"><i class="fas fa-file-invoice-dollar"></i></span>
                <span class="pc-mtext">Item-Wise GST</span>
              </a>
            </li>

            <li class="pc-item">
              <a href="{{ route('order.report.top.analysis') }}" class="pc-link @if(Request::is('*report-top-analysis*')) active_class @endif">
                <span class="pc-micon"><i class="ti ti-trophy"></i></span>
                <span class="pc-mtext">Top Cust. / Dish</span>
              </a>
            </li>

            <li class="pc-item">
              <a href="{{ route('order.report.analysis') }}" class="pc-link @if(Request::is('*report-order-analysis*')) active_class @endif">
                <span class="pc-micon"><i class="ti ti-chart-line"></i></span>
                <span class="pc-mtext">Order Analysis</span>
              </a>
            </li>

            <!-- Live Stocks -->
            @if(@$plan_details->inventory_checkbox=="Y" || $active == "")
            <li class="pc-item">
              <a href="{{ route('inventory.live') }}" class="pc-link @if(Request::is('*inventory/live*')) active_class @endif">
                <span class="pc-micon"><i class="fas fa-warehouse"></i></span>
                <span class="pc-mtext">Live Stocks</span>
              </a>
            </li>
            @endif
          </ul>
        </li>
        @endif

        {{-- ==================== END RESTAURANT ROUTES ==================== --}}
        @endif

        <li class="pc-item mt-2">
          <a href="{{route('logout')}}" class="pc-link text-danger">
            <span class="pc-micon"><i class="fas fa-sign-out-alt text-danger"></i></span>
            <span class="pc-mtext text-danger fw-semibold">Logout</span>
          </a>
        </li>
        
      </ul>
      
    </div>
  </div>
</nav>
<!-- [ Sidebar Menu ] end -->


 <!-- [ Header Topbar ] start -->
<header class="pc-header">
  <div class="header-wrapper"> <!-- [Mobile Media Block] start -->
<div class="me-auto pc-mob-drp">
  <ul class="list-unstyled">
    <!-- ======= Menu collapse Icon ===== -->
    <li class="pc-h-item pc-sidebar-collapse">
      <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
        <i class="ti ti-menu-2"></i>
      </a>
    </li>
    <li class="pc-h-item pc-sidebar-popup">
      <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
        <i class="ti ti-menu-2"></i>
      </a>
    </li>
    <li class="dropdown pc-h-item d-inline-flex d-md-none">
      <a
        class="pc-head-link dropdown-toggle arrow-none m-0"
        data-bs-toggle="dropdown"
        href="#"
        role="button"
        aria-haspopup="false"
        aria-expanded="false"
      >
        <i class="ti ti-search"></i>
      </a>
      <div class="dropdown-menu pc-h-dropdown drp-search">
        <form class="px-3">
          <div class="form-group mb-0 d-flex align-items-center">
            <i data-feather="search"></i>
            <input type="search" class="form-control border-0 shadow-none" placeholder="Search here. . .">
          </div>
        </form>
      </div>
    </li>
    <li class="pc-h-item d-none d-md-inline-flex">
      <form class="header-search">
        <i data-feather="search" class="icon-search"></i>
        <input type="search" class="form-control" placeholder="Search here. . .">
      </form>
    </li>
  </ul>
</div>
<!-- [Mobile Media Block end] -->
<div class="ms-auto">
  <ul class="list-unstyled">
    <li class="dropdown pc-h-item">
      {{-- <a
        class="pc-head-link dropdown-toggle arrow-none me-0"
        data-bs-toggle="dropdown"
        href="#"
        role="button"
        aria-haspopup="false"
        aria-expanded="false"
      >
        <i class="ti ti-mail"></i>
      </a> --}}
      <div class="dropdown-menu dropdown-notification dropdown-menu-end pc-h-dropdown">
        <div class="dropdown-header d-flex align-items-center justify-content-between">
          <h5 class="m-0">Message</h5>
          <a href="#!" class="pc-head-link bg-transparent"><i class="ti ti-x text-danger"></i></a>
        </div>
        <div class="dropdown-divider"></div>
        <div class="dropdown-header px-0 text-wrap header-notification-scroll position-relative" style="max-height: calc(100vh - 215px)">
          <div class="list-group list-group-flush w-100">
            <a class="list-group-item list-group-item-action">
              <div class="d-flex">
                <div class="flex-shrink-0">
                  <img src="../assets/images/user/avatar-2.jpg" alt="user-image" class="user-avtar">
                </div>
                <div class="flex-grow-1 ms-1">
                  <span class="float-end text-muted">3:00 AM</span>
                  <p class="text-body mb-1">It's <b>Cristina danny's</b> birthday today.</p>
                  <span class="text-muted">2 min ago</span>
                </div>
              </div>
            </a>
            <a class="list-group-item list-group-item-action">
              <div class="d-flex">
                <div class="flex-shrink-0">
                  <img src="../assets/images/user/avatar-1.jpg" alt="user-image" class="user-avtar">
                </div>
                <div class="flex-grow-1 ms-1">
                  <span class="float-end text-muted">6:00 PM</span>
                  <p class="text-body mb-1"><b>Aida Burg</b> commented your post.</p>
                  <span class="text-muted">5 August</span>
                </div>
              </div>
            </a>
            <a class="list-group-item list-group-item-action">
              <div class="d-flex">
                <div class="flex-shrink-0">
                  <img src="../assets/images/user/avatar-3.jpg" alt="user-image" class="user-avtar">
                </div>
                <div class="flex-grow-1 ms-1">
                  <span class="float-end text-muted">2:45 PM</span>
                  <p class="text-body mb-1"><b>There was a failure to your setup.</b></p>
                  <span class="text-muted">7 hours ago</span>
                </div>
              </div>
            </a>
            <a class="list-group-item list-group-item-action">
              <div class="d-flex">
                <div class="flex-shrink-0">
                  <img src="../assets/images/user/avatar-4.jpg" alt="user-image" class="user-avtar">
                </div>
                <div class="flex-grow-1 ms-1">
                  <span class="float-end text-muted">9:10 PM</span>
                  <p class="text-body mb-1"><b>Cristina Danny </b> invited to join <b> Meeting.</b></p>
                  <span class="text-muted">Daily scrum meeting time</span>
                </div>
              </div>
            </a>
          </div>
        </div>
        <div class="dropdown-divider"></div>
        <div class="text-center py-2">
          <a href="#!" class="link-primary">View all</a>
        </div>
      </div>
    </li>
    @if(auth()->user()->role === 'RES' && auth()->user()->isOwner())
    @php
      $availableOutlets = auth()->user()->getAvailableOutlets();
      $currRest = \App\Models\RestaurantMaster::find(auth()->user()->restaurant_id);
    @endphp
    @if($availableOutlets->count() > 1 || auth()->user()->hasMultiOutletAccess())
    <li class="dropdown pc-h-item me-2 d-none d-sm-inline-flex">
      <a class="btn btn-outline-warning btn-sm dropdown-toggle d-flex align-items-center gap-2" 
         href="#" 
         data-bs-toggle="dropdown" 
         style="border-radius: 20px; font-weight: 600; padding: 6px 14px; background: rgba(255, 106, 0, 0.08); border-color: #ff6a00; color: #e65100;">
        <i class="fas fa-store-alt text-primary"></i>
        <span class="text-truncate" style="max-width: 160px;">{{ $currRest ? $currRest->name : 'Select Outlet' }}</span>
      </a>
      <div class="dropdown-menu dropdown-menu-end shadow border-0 p-2" style="min-width: 250px; border-radius: 12px;">
        <div class="dropdown-header text-muted text-uppercase fw-bold pb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">
          <i class="fas fa-exchange-alt me-1"></i> Switch Working Outlet
        </div>
        @foreach($availableOutlets as $outItem)
          <a class="dropdown-item d-flex align-items-center justify-content-between py-2 rounded mb-1 {{ $outItem->id == auth()->user()->restaurant_id ? 'bg-light-primary text-primary fw-bold' : '' }}" 
             href="{{ route('restaurant.outlets.switch', $outItem->id) }}">
            <div class="d-flex align-items-center text-truncate">
              <i class="fas fa-{{ $outItem->isMainRestaurant() ? 'crown text-warning' : 'store text-secondary' }} me-2"></i>
              <span class="text-truncate">{{ $outItem->name }}</span>
            </div>
            @if($outItem->id == auth()->user()->restaurant_id)
              <i class="fas fa-check-circle text-primary ms-2"></i>
            @endif
          </a>
        @endforeach
        <div class="dropdown-divider my-1"></div>
        <a class="dropdown-item text-primary fw-semibold py-2 text-center" href="{{ route('restaurant.outlets.index') }}">
          <i class="fas fa-cog me-1"></i> Manage All Outlets
        </a>
      </div>
    </li>
    @endif
    @endif

    <li class="dropdown pc-h-item header-user-profile">
      @if(auth()->user()->role=="RES")
      <a
        class="pc-head-link dropdown-toggle arrow-none me-0"
        data-bs-toggle="dropdown"
        href="#"
        role="button"
        aria-haspopup="false"
        data-bs-auto-close="outside"
        aria-expanded="false"
      >
      @endif
        
        <span>Welcome , {{auth()->user()->name}}</span>
      </a>
      <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
        <div class="dropdown-header">
          <div class="d-flex mb-1">
            
            <div class="flex-grow-1 ms-3">
              <a href="{{route('restaurant.profile.index')}}">
              <h6 class="mb-1">@if(auth()->user()->role=="RES"){{auth()->user()->restaurant->name}} @endif</h6>
              <span style="color:orange;"><i class="fas fa-user me-2"></i>View Profile</span>
            </a>
            
            </div>
            <a href="{{route('logout')}}" class="pc-head-link bg-transparent"><i class="ti ti-power text-danger"></i></a>
          </div>
        </div>
        
      </div>
    </li>
  </ul>
</div>
 </div>
</header>