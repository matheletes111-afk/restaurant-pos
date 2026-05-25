
  <style type="text/css">
   .active_class{
    background: #009d1a !important;
    color: white !important;
   }
 </style>
<nav class="pc-sidebar">
  <div class="navbar-wrapper">
    <div class="m-header">
      <a href="#" class="b-brand text-primary">
        
        <img src="{{asset('logo.png')}}" class="img-fluid logo-lg mt-3" alt="logo">
      </a>
    </div>
    <div class="navbar-content">
      <ul class="pc-navbar mt-3">
        

        

        @if(auth()->user()->role!="RES")
        <li class="pc-item">
          <a href="{{route('manage.restaurant')}}" class="pc-link @if(Request::segment(2)=="manage-restaurant") active_class @endif">
            <span class="pc-micon"><i class="fas fa-utensils"></i></span>
            <span class="pc-mtext">Restaurant Master</span>
          </a>
        </li>

        <li class="pc-item">
          <a href="{{route('plans.index')}}" class="pc-link @if(Request::segment(2)=="plans") active_class @endif">
            <span class="pc-micon"><i class="fas fa-layer-group"></i></span>
            <span class="pc-mtext">Plan Master</span>
          </a>
        </li>

         <li class="pc-item">
          <a href="{{route('admin.payment.history')}}" class="pc-link @if(Request::segment(2)=="payment-history") active_class @endif">
            <span class="pc-micon">
              <i class="fas fa-receipt"></i>
            </span>
            <span class="pc-mtext">Payment History</span>
          </a>
        </li>
        @endif



@php
  $active = DB::table('subscriptions')->where('user_id',auth()->user()->restaurant_id)->where('status','active')->first();
  $plan_details = DB::table('plans')->where('id',@$active->plan_id)->first();
@endphp
@if($active!="")


<li class="pc-item">
          <a href="{{route('dashboard')}}" class="pc-link @if(Request::segment(1)=="dashboard") active_class @endif">
            <span class="pc-micon"><i class="fas fa-sitemap"></i></span>
            <span class="pc-mtext">Dashboard</span>
          </a>
        </li>

@if(auth()->user()->role_type=="Manager"  || auth()->user()->role_type=="ADMIN" )
<li class="pc-item">
  <a href="{{route('manage.category')}}" class="pc-link @if(Request::segment(2)=="manage-menu-category") active_class @endif">
    <span class="pc-micon"><i class="fas fa-concierge-bell"></i></span>
    <span class="pc-mtext">Menu Master</span>
  </a>
</li>

<li class="pc-item">
  <a href="{{route('menu.availability')}}" class="pc-link @if(Request::segment(2)=="menu-availability") active_class @endif">
    <span class="pc-micon"><i class="fas fa-clipboard-list"></i></span>
    <span class="pc-mtext">Menu Avilability</span>
  </a>
</li>


<li class="pc-item">
  <a href="{{route('table.manage')}}" class="pc-link @if(Request::segment(2)=="table-manage") active_class @endif">
    <span class="pc-micon"><i class="fas fa-chair"></i></span>
    <span class="pc-mtext">Table Master</span>
  </a>
</li>


@endif

@if(auth()->user()->role_type=="Manager"  || auth()->user()->role_type=="ADMIN" || auth()->user()->role_type=="Cashier" || auth()->user()->role_type=="Waiter")
<li class="pc-item">
  <a href="{{route('order.management.dashboard')}}" class="pc-link @if(Request::segment(2)=="order-management-dashboard") active_class @endif">
    <span class="pc-micon"><i class="fas fa-receipt"></i></span>
    <span class="pc-mtext">Order Master</span>
  </a>
</li>
@endif





@if(auth()->user()->role_type=="Manager" || auth()->user()->role_type=="ADMIN" || auth()->user()->role_type=="Kitchen Staff")



<li class="pc-item">
  <a href="{{route('manage.kitchen-panel')}}" class="pc-link @if(Request::segment(2)=="kitchen-panel") active_class @endif">
    <span class="pc-micon"><i class="fas fa-users"></i></span>
    <span class="pc-mtext">Kitchen Order</span>
  </a>
</li>

<li class="pc-item">
  <a href="{{ route('temp.orders') }}" 
     class="pc-link @if(Request::segment(2) == 'pending-temp-orders') active_class @endif">
    <span class="pc-micon">
      <i class="fas fa-clock"></i>
    </span>
    <span class="pc-mtext">Pending Order</span>
  </a>
</li>

<li class="pc-item">
  <a href="{{ route('ask-ai') }}" 
     class="pc-link @if(Request::segment(2) == 'ask-ai') active_class @endif">
    <span class="pc-micon">
      <i class="fas fa-robot"></i>
    </span>
    <span class="pc-mtext">Restro AI</span>
  </a>
</li>

<li class="pc-item">
  <a href="{{ route('admin.subscriptions.index') }}" 
     class="pc-link @if(Request::segment(2) == 'subscriptions') active_class @endif">
    <span class="pc-micon">
      <i class="fas fa-credit-card"></i>
    </span>
    <span class="pc-mtext">Billing & Subscription</span>
  </a>
</li>

@endif

@if(auth()->user()->role=="RES" && auth()->user()->role_type=="ADMIN")
<li class="pc-item">
  <a href="{{route('restaurant.staff.index')}}" class="pc-link @if(Request::segment(2)=="restaurant-staff") active_class @endif">
    <span class="pc-micon"><i class="fas fa-users"></i></span>
    <span class="pc-mtext">Staff</span>
  </a>
</li>
@endif

@if(@$plan_details->inventory_checkbox=="Y")
<li class="pc-item pc-hasmenu">
  <a href="#!" class="pc-link">
    <span class="pc-micon">
      <i class="ti ti-menu"></i>
    </span>
    <span class="pc-mtext">Inventory Setting</span>
    <span class="pc-arrow">
      <i data-feather="chevron-right"></i>
    </span>
  </a>

  <ul class="pc-submenu">

    <!-- Manage Units -->
    <li class="pc-item">
      <a href="{{ route('manage.units') }}" class="pc-link">
        <span class="pc-micon">
          <i class="fas fa-ruler-combined"></i>
        </span>
        <span class="pc-mtext">Manage Units</span>
      </a>
    </li>

<li class="pc-item">
    <a href="{{ route('products.manage') }}" class="pc-link">
        <span class="pc-micon">
            <i class="fas fa-th-list"></i> <!-- or fa-th-large -->
        </span>
        <span class="pc-mtext">Manage Products</span>
    </a>
</li>

    <!-- Manage Suppliers -->
    <li class="pc-item">
      <a href="{{ route('suppliers.index') }}" class="pc-link">
        <span class="pc-micon">
          <i class="fas fa-truck"></i>
        </span>
        <span class="pc-mtext">Manage Suppliers</span>
      </a>
    </li>

    <!-- Manage Purchases -->
    <li class="pc-item">
      <a href="{{ route('purchases.index') }}" class="pc-link">
        <span class="pc-micon">
          <i class="fas fa-file-invoice-dollar"></i>
        </span>
        <span class="pc-mtext">Manage Purchases</span>
      </a>
    </li>

    <!-- Manage Stockout -->
    <li class="pc-item">
      <a href="{{ route('stock-outs.index') }}" class="pc-link">
        <span class="pc-micon">
          <i class="fas fa-box-open"></i>
        </span>
        <span class="pc-mtext">Manage Stockout</span>
      </a>
    </li>

      <li class="pc-item">
  <a href="{{ route('debit-notes.index') }}" class="pc-link">
    <span class="pc-micon">
      <i class="fas fa-file-invoice-dollar"></i>
    </span>
    <span class="pc-mtext">Supplier Debit Note</span>
  </a>
</li>
</ul>
</li>
@endif

<li class="pc-item pc-hasmenu">
  <a href="#!" class="pc-link">
    <span class="pc-micon">
      <i class="ti ti-report-analytics"></i>
    </span>
    <span class="pc-mtext">Reports</span>
    <span class="pc-arrow">
      <i data-feather="chevron-right"></i>
    </span>
  </a>

  <ul class="pc-submenu">

    <li class="pc-item">
      <a href="{{ route('order.report.management') }}" class="pc-link">
        <span class="pc-micon">
          <i class="ti ti-file-text"></i>
        </span>
        <span class="pc-mtext">Order Report</span>
      </a>
    </li>

    <li class="pc-item">
    <a href="{{ route('report.item.gst.summary') }}" class="pc-link">
      <span class="pc-micon">
        <i class="fas fa-file-invoice-dollar"></i>
      </span>
      <span class="pc-mtext">Item-Wise (GST)</span>
    </a>
  </li>

    <li class="pc-item">
      <a href="{{ route('order.report.top.analysis') }}" class="pc-link">
        <span class="pc-micon">
          <i class="ti ti-trophy"></i>
        </span>
        <span class="pc-mtext">Top Cust. / Dish</span>
      </a>
    </li>

    <li class="pc-item">
      <a href="{{ route('order.report.analysis') }}" class="pc-link">
        <span class="pc-micon">
          <i class="ti ti-chart-line"></i>
        </span>
        <span class="pc-mtext">Order Analysis</span>
      </a>
    </li>

    <!-- Live Stocks -->
    @if(@$plan_details->inventory_checkbox=="Y")
    <li class="pc-item">
      <a href="{{ route('inventory.live') }}" class="pc-link">
        <span class="pc-micon">
          <i class="fas fa-warehouse"></i>
        </span>
        <span class="pc-mtext">Live Stocks</span>
      </a>
    </li>
    @endif

  </ul>
</li>

@endif
















<li class="pc-item">
        <a href="{{route('logout')}}" class="pc-link">
          <span class="pc-micon"><i class="fa fa-sign-out-alt fa-3x"></i></span>
          <span class="pc-mtext">Logout</span>
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
      <a
        class="pc-head-link dropdown-toggle arrow-none me-0"
        data-bs-toggle="dropdown"
        href="#"
        role="button"
        aria-haspopup="false"
        aria-expanded="false"
      >
        <i class="ti ti-mail"></i>
      </a>
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
    <li class="dropdown pc-h-item header-user-profile">
      <a
        class="pc-head-link dropdown-toggle arrow-none me-0"
        data-bs-toggle="dropdown"
        href="#"
        role="button"
        aria-haspopup="false"
        data-bs-auto-close="outside"
        aria-expanded="false"
      >
        
        <span>Welcome , {{auth()->user()->name}}</span>
      </a>
      <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
        <div class="dropdown-header">
          <div class="d-flex mb-1">
            
            <div class="flex-grow-1 ms-3">
              <a href="{{route('restaurant.profile.index')}}">
              <h6 class="mb-1">{{auth()->user()->restaurant->name}}</h6>
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