@extends('layouts.app')

@section('title')
<title>Admin || Dashboard</title>
@endsection

@section('style')
@include('includes.style')
<style>
    .welcome-card-custom {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: white;
        border-radius: 20px;
        padding: 45px 40px;
        box-shadow: 0 12px 35px rgba(15, 23, 42, 0.08);
        position: relative;
        overflow: hidden;
        margin-bottom: 35px;
    }
    .welcome-card-custom::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(255, 106, 0, 0.15), transparent 70%);
        border-radius: 50%;
        z-index: 1;
    }
    .welcome-title {
        font-size: 2.3rem;
        font-weight: 850;
        margin-bottom: 12px;
        z-index: 2;
        position: relative;
        letter-spacing: -0.5px;
        color: #ffffff;
    }
    .welcome-subtitle {
        font-size: 1.1rem;
        z-index: 2;
        position: relative;
        font-weight: 400;
        color: #ffffff;
        opacity: 0.9;
    }
    .quote-card-custom {
        border: none;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        overflow: hidden;
        transition: transform 0.3s ease;
        margin-top: 10px;
    }
    .quote-card-custom:hover {
        transform: translateY(-2px);
    }
    .quote-icon {
        font-size: 2.8rem;
        color: #ff6a00;
        opacity: 0.08;
        position: absolute;
        top: 25px;
        right: 25px;
        z-index: 1;
    }
    .quote-text {
        font-size: 1.3rem;
        font-style: italic;
        color: #334155;
        font-weight: 600;
        line-height: 1.65;
        margin: 0;
        z-index: 2;
        position: relative;
    }
    
    /* Shortcut Card styling */
    .shortcut-card {
        border: none;
        border-radius: 18px;
        background: #fff;
        padding: 24px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        border: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 20px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
    }
    .shortcut-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.06) !important;
        border-color: rgba(255, 106, 0, 0.25) !important;
    }
    .shortcut-icon {
        width: 54px;
        height: 54px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        flex-shrink: 0;
        transition: transform 0.3s ease;
    }
    .shortcut-card:hover .shortcut-icon {
        transform: scale(1.08);
    }
    .shortcut-title {
        margin: 0 0 4px 0;
        font-weight: 700;
        color: #1e293b;
        font-size: 1.05rem;
    }
    .shortcut-desc {
        color: #64748b;
        font-size: 0.85rem;
        margin: 0;
    }
</style>
@endsection

@section('body')
@include('includes.sidebar')
<div class="pc-container">
    <div class="pc-content">

        <!-- Breadcrumb -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Admin Dashboard</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item" aria-current="page">Dashboard</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Breadcrumb -->

        @php
          $saUser = auth()->user();
          $saPerms = $saUser->permissions ?? [];
        @endphp

        <div class="row">
            <div class="col-md-12">
                <div class="welcome-card-custom">
                    <h1 class="welcome-title">Hi {{ $saUser->name }}, welcome back!</h1>
                    <p class="welcome-subtitle">We are glad to have you back. Let's make today productive and successful.</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 col-md-12">
                <div class="card quote-card-custom">
                    <div style="height: 6px; background: linear-gradient(90deg, #ff6a00, #ff8c42);"></div>
                    <div class="card-body" style="padding: 35px; position: relative;">
                        <i class="fas fa-quote-right quote-icon"></i>
                        <div style="position: relative; z-index: 2;">
                            <span style="color: #64748b; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1.2px; display: block; margin-bottom: 12px;">Daily Inspiration</span>
                            <blockquote style="margin: 0;">
                                <p class="quote-text">"{{ $quote }}"</p>
                            </blockquote>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-md-12 mb-3">
                <h4 style="font-weight: 800; color: #0f172a; font-size: 1.3rem; letter-spacing: -0.3px;">Quick Access Menu</h4>
            </div>
            
            @if($saUser->id == 1 || in_array('restaurant_master', $saPerms))
            <div class="col-xl-4 col-md-6 col-sm-6 mb-4">
                <a href="{{route('manage.restaurant')}}" style="text-decoration: none;">
                    <div class="shortcut-card">
                        <div class="shortcut-icon" style="background: #fff0e6; color: #ff6a00;">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <div>
                            <h5 class="shortcut-title">Restaurant Master</h5>
                            <p class="shortcut-desc">Manage restaurants & custom plans</p>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if($saUser->id == 1 || in_array('plan_master', $saPerms))
            <div class="col-xl-4 col-md-6 col-sm-6 mb-4">
                <a href="{{route('plans.index')}}" style="text-decoration: none;">
                    <div class="shortcut-card">
                        <div class="shortcut-icon" style="background: #e0f2fe; color: #0284c7;">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div>
                            <h5 class="shortcut-title">Plan Master</h5>
                            <p class="shortcut-desc">Configure subscription plans & details</p>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if($saUser->id == 1 || in_array('payment_history', $saPerms))
            <div class="col-xl-4 col-md-6 col-sm-6 mb-4">
                <a href="{{route('admin.payment.history')}}" style="text-decoration: none;">
                    <div class="shortcut-card">
                        <div class="shortcut-icon" style="background: #ecfdf5; color: #059669;">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <div>
                            <h5 class="shortcut-title">Payment History</h5>
                            <p class="shortcut-desc">View payments & invoicing audits</p>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if($saUser->id == 1 || in_array('admin_crm', $saPerms))
            <div class="col-xl-4 col-md-6 col-sm-6 mb-4">
                <a href="{{route('admin.crm.index')}}" style="text-decoration: none;">
                    <div class="shortcut-card">
                        <div class="shortcut-icon" style="background: #faf5ff; color: #7c3aed;">
                            <i class="fas fa-address-book"></i>
                        </div>
                        <div>
                            <h5 class="shortcut-title">Admin CRM</h5>
                            <p class="shortcut-desc">Track sales leads & interaction tasks</p>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if($saUser->id == 1 || in_array('customer_support', $saPerms))
            <div class="col-xl-4 col-md-6 col-sm-6 mb-4">
                <a href="{{route('admin.support.tickets')}}" style="text-decoration: none;">
                    <div class="shortcut-card">
                        <div class="shortcut-icon" style="background: #fff1f2; color: #e11d48;">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div>
                            <h5 class="shortcut-title">Customer Support</h5>
                            <p class="shortcut-desc">Resolve user support requests & tickets</p>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if($saUser->id == 1 || in_array('admin_user_management', $saPerms))
            <div class="col-xl-4 col-md-6 col-sm-6 mb-4">
                <a href="{{route('admin.users.index')}}" style="text-decoration: none;">
                    <div class="shortcut-card">
                        <div class="shortcut-icon" style="background: #f0fdf4; color: #16a34a;">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <div>
                            <h5 class="shortcut-title">Admin Users</h5>
                            <p class="shortcut-desc">Configure sub-admins & permissions</p>
                        </div>
                    </div>
                </a>
            </div>
            @endif
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
@include('includes.script')
@endsection
