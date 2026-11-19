@extends('layouts.app')

@section('title')
<title>Customer Directory - {{ $restaurant->name ?? 'Restaurant' }}</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root {
        --cust-primary: #ff6a00;
        --cust-primary-dark: #e65c00;
        --cust-dark: #0f172a;
        --cust-card-bg: #ffffff;
        --cust-border: #e2e8f0;
        --cust-text: #1e293b;
        --cust-muted: #64748b;
        --cust-success: #10b981;
        --cust-whatsapp: #25D366;
        --cust-whatsapp-dark: #128C7E;
        --cust-call: #2563eb;
    }

    body {
        background-color: #f8fafc !important;
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: var(--cust-text);
    }

    .cust-wrap {
        padding: 10px 0 30px 0;
    }

    /* Page Header */
    .cust-header-card {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: 18px;
        padding: 28px 32px;
        margin-bottom: 24px;
        color: white;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.2);
        position: relative;
        overflow: hidden;
    }
    .cust-header-card::after {
        content: '';
        position: absolute;
        top: -40px;
        right: -40px;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, rgba(255, 106, 0, 0.25) 0%, rgba(255, 106, 0, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .cust-header-title {
        font-family: 'Outfit', sans-serif;
        font-size: 1.65rem;
        font-weight: 700;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #ffffff !important;
    }
    .cust-header-subtitle {
        color: #ffffff !important;
        font-size: 0.92rem;
        margin-bottom: 0;
        opacity: 0.92;
    }

    /* KPI Summary Cards */
    .cust-kpi-card {
        background: #ffffff;
        border: 1px solid var(--cust-border);
        border-radius: 14px;
        padding: 18px 20px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .cust-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.06);
    }
    .cust-kpi-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }
    .cust-kpi-icon.blue { background: #eff6ff; color: #2563eb; }
    .cust-kpi-icon.green { background: #ecfdf5; color: #059669; }
    .cust-kpi-icon.amber { background: #fffbeb; color: #d97706; }
    .cust-kpi-icon.purple { background: #f5f3ff; color: #7c3aed; }

    .cust-kpi-val {
        font-family: 'Outfit', sans-serif;
        font-size: 1.45rem;
        font-weight: 800;
        line-height: 1.2;
        color: var(--cust-dark);
    }
    .cust-kpi-label {
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--cust-muted);
        margin-bottom: 2px;
    }

    /* Filter & Search Bar */
    .cust-filter-card {
        background: #ffffff;
        border: 1px solid var(--cust-border);
        border-radius: 14px;
        padding: 16px 20px;
        margin-bottom: 22px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.02);
    }
    .cust-search-input {
        border-radius: 10px;
        border: 1px solid var(--cust-border);
        padding: 10px 14px 10px 40px;
        font-size: 0.92rem;
    }
    .cust-search-input:focus {
        border-color: var(--cust-primary);
        box-shadow: 0 0 0 3px rgba(255, 106, 0, 0.15);
    }
    .cust-search-wrap {
        position: relative;
    }
    .cust-search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--cust-muted);
        pointer-events: none;
    }

    /* Table Styles */
    .cust-table-card {
        background: #ffffff;
        border: 1px solid var(--cust-border);
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        overflow: hidden;
    }
    .cust-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 0;
    }
    .cust-table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.76rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 14px 18px;
        border-bottom: 1px solid var(--cust-border);
        white-space: nowrap;
    }
    .cust-table tbody td {
        padding: 14px 18px;
        vertical-align: middle;
        font-size: 0.9rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .cust-table tbody tr:hover td {
        background-color: #fafbfc;
    }
    .cust-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Avatar Initial */
    .cust-avatar {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: white;
        font-weight: 700;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        text-transform: uppercase;
    }
    .cust-name {
        font-weight: 700;
        color: var(--cust-dark);
        font-size: 0.94rem;
        line-height: 1.25;
    }
    .cust-phone-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f1f5f9;
        color: #334155;
        font-weight: 700;
        font-family: 'Outfit', sans-serif;
        font-size: 0.92rem;
        padding: 4px 10px;
        border-radius: 8px;
        letter-spacing: 0.03em;
    }

    /* Action Buttons */
    .cust-actions-group {
        display: flex;
        align-items: center;
        gap: 8px;
        justify-content: center;
    }
    .cust-btn-call {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        font-size: 0.82rem;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .cust-btn-call:hover {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);
    }
    .cust-btn-whatsapp {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
        font-size: 0.82rem;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .cust-btn-whatsapp:hover {
        background: #25D366;
        color: #ffffff;
        border-color: #25D366;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(37, 211, 102, 0.3);
    }

    /* Badges */
    .badge-orders {
        background: #f1f5f9;
        color: #475569;
        font-weight: 700;
        font-size: 0.78rem;
        padding: 4px 10px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
    }
    .badge-repeat {
        background: #ecfdf5;
        color: #059669;
        font-weight: 700;
        font-size: 0.72rem;
        padding: 2px 7px;
        border-radius: 4px;
        border: 1px solid #a7f3d0;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    /* Buttons */
    .btn-export-excel {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #10b981;
        color: white;
        font-weight: 600;
        font-size: 0.88rem;
        padding: 9px 18px;
        border-radius: 10px;
        border: none;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
    }
    .btn-export-excel:hover {
        background: #059669;
        color: white;
        transform: translateY(-1px);
    }
    .btn-filter-apply {
        background: var(--cust-primary);
        color: white;
        font-weight: 600;
        border-radius: 10px;
        padding: 9px 18px;
        border: none;
        transition: all 0.2s ease;
    }
    .btn-filter-apply:hover {
        background: var(--cust-primary-dark);
        color: white;
    }
</style>
@endsection

@section('body')
@include('includes.sidebar')

<div class="pc-container">
    <div class="pc-content">
        <div class="cust-wrap">

            {{-- Flash Messages --}}
            @include('includes.message')

            {{-- Page Header --}}
            <div class="cust-header-card">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div>
                        <h2 class="cust-header-title text-white">
                            <i class="fas fa-users text-warning"></i> Customer Directory
                        </h2>
                        <p class="cust-header-subtitle text-white">
                            Unique customers who have provided a name and mobile number during checkout.
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('restaurant.customers.export', request()->all()) }}" class="btn-export-excel">
                            <i class="fas fa-file-excel"></i> Export Customer List
                        </a>
                    </div>
                </div>
            </div>

            {{-- Metric Summary Cards --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="cust-kpi-card">
                        <div class="cust-kpi-icon blue">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <div class="cust-kpi-label">Unique Customers</div>
                            <div class="cust-kpi-val">{{ number_format($totalCustomersCount) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="cust-kpi-card">
                        <div class="cust-kpi-icon green">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div>
                            <div class="cust-kpi-label">Repeat Customers</div>
                            <div class="cust-kpi-val">{{ number_format($repeatCustomersCount) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="cust-kpi-card">
                        <div class="cust-kpi-icon amber">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <div>
                            <div class="cust-kpi-label">Orders Placed</div>
                            <div class="cust-kpi-val">{{ number_format($totalOrdersCount) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="cust-kpi-card">
                        <div class="cust-kpi-icon purple">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div>
                            <div class="cust-kpi-label">Customer Revenue</div>
                            <div class="cust-kpi-val">₹{{ number_format($totalRevenue, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filter & Search Card --}}
            <div class="cust-filter-card">
                <form method="GET" action="{{ route('restaurant.customers.index') }}" class="row g-3 align-items-center">
                    <div class="col-12 col-md-6 col-lg-5">
                        <div class="cust-search-wrap">
                            <i class="fas fa-search cust-search-icon"></i>
                            <input type="text" 
                                   name="keyword" 
                                   class="form-control cust-search-input" 
                                   placeholder="Search by customer name or mobile number..." 
                                   value="{{ $keyword }}">
                        </div>
                    </div>

                    <div class="col-6 col-md-3 col-lg-3">
                        <select name="sort" class="form-select cust-search-input">
                            <option value="latest" {{ $sortBy === 'latest' ? 'selected' : '' }}>Sort: Latest Visited</option>
                            <option value="orders" {{ $sortBy === 'orders' ? 'selected' : '' }}>Sort: Most Orders</option>
                            <option value="spent" {{ $sortBy === 'spent' ? 'selected' : '' }}>Sort: Highest Spent</option>
                            <option value="name" {{ $sortBy === 'name' ? 'selected' : '' }}>Sort: Name (A-Z)</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-3 col-lg-4 d-flex gap-2">
                        <button type="submit" class="btn btn-filter-apply">
                            <i class="fas fa-filter me-1"></i> Apply
                        </button>
                        @if(!empty($keyword) || $sortBy !== 'latest')
                            <a href="{{ route('restaurant.customers.index') }}" class="btn btn-outline-secondary" style="border-radius: 10px;">
                                <i class="fas fa-undo me-1"></i> Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Customers Data Table Card --}}
            <div class="cust-table-card">
                <div class="table-responsive">
                    <table class="cust-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th>Customer Name</th>
                                <th>Mobile Number</th>
                                <th class="text-center">Total Orders</th>
                                <th class="text-end">Total Spent</th>
                                <th>Last Visited</th>
                                <th class="text-center" style="width: 220px;">Quick Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $index => $c)
                            <tr>
                                <td class="text-center text-muted fw-bold">
                                    {{ ($customers->currentPage() - 1) * $customers->perPage() + $index + 1 }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="cust-avatar">
                                            {{ strtoupper(substr($c->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="cust-name">{{ $c->name }}</div>
                                            <small class="text-muted">Unique ID: #{{ $c->clean_phone }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="cust-phone-badge">
                                        <i class="fas fa-mobile-alt text-secondary"></i> {{ $c->phone }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge-orders">
                                        {{ $c->total_orders }} {{ $c->total_orders > 1 ? 'Orders' : 'Order' }}
                                    </span>
                                    @if($c->total_orders > 1)
                                        <span class="badge-repeat ms-1">Repeat</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold" style="font-family: 'Outfit', sans-serif; color: #0f172a;">
                                    ₹{{ number_format($c->total_spent, 2) }}
                                </td>
                                <td>
                                    @if($c->last_order_date)
                                        <div class="fw-semibold text-dark small">{{ $c->last_order_date->format('d M Y') }}</div>
                                        <small class="text-muted">{{ $c->last_order_date->format('h:i A') }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="cust-actions-group">
                                        {{-- Call Button --}}
                                        <a href="tel:{{ $c->phone }}" 
                                           class="cust-btn-call" 
                                           title="Call {{ $c->name }} ({{ $c->phone }})">
                                            <i class="fas fa-phone-alt"></i> Call
                                        </a>

                                        {{-- WhatsApp Button --}}
                                        <a href="https://api.whatsapp.com/send?phone={{ $c->wa_phone }}" 
                                           target="_blank" 
                                           rel="noopener noreferrer" 
                                           class="cust-btn-whatsapp" 
                                           title="Chat with {{ $c->name }} on WhatsApp">
                                            <i class="fab fa-whatsapp"></i> WhatsApp
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <div class="py-4">
                                        <i class="fas fa-users-slash fa-3x text-secondary opacity-50 mb-3"></i>
                                        <h5 class="fw-bold text-dark mb-1">No Customers Found</h5>
                                        <p class="small text-muted mb-0">
                                            @if(!empty($keyword))
                                                No customers match your search criteria "<strong>{{ $keyword }}</strong>".
                                            @else
                                                No billed orders with a customer name and mobile number were found yet.
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links --}}
                @if($customers->hasPages())
                <div class="p-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="small text-muted">
                        Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} customers
                    </div>
                    <div>
                        {{ $customers->links() }}
                    </div>
                </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
