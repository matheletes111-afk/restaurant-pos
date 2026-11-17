@extends('layouts.app')

@section('title')
<title>Customer List - {{ $restaurant ? $restaurant->name : 'Restaurant' }}</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
.cl-wrap {
    font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
    color: #1e293b;
}

.cl-header-deck {
    background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 60%, #311042 100%);
    border-radius: 20px;
    padding: 26px 30px;
    color: #ffffff;
    box-shadow: 0 12px 32px rgba(15, 23, 42, 0.25);
    margin-bottom: 22px;
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.cl-header-deck::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -15%;
    width: 320px;
    height: 320px;
    background: radial-gradient(circle, rgba(255, 106, 0, 0.25) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}

.cl-kpi-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 20px 22px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
    transition: all 0.25s ease;
    height: 100%;
}

.cl-kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
    border-color: #cbd5e1;
}

.cl-kpi-label {
    font-size: 0.74rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #64748b;
}

.cl-kpi-icon {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.cl-kpi-value {
    font-size: 1.65rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.2;
    margin-top: 6px;
}

.cl-filter-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.03);
    padding: 18px 24px;
    margin-bottom: 22px;
}

.cl-table-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    overflow: hidden;
}

.cl-table thead th {
    background: #f8fafc;
    color: #475569;
    font-weight: 700;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    padding: 14px 18px;
    border-bottom: 1px solid #e2e8f0;
    white-space: nowrap;
}

.cl-table tbody td {
    padding: 14px 18px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    font-size: 0.88rem;
    color: #334155;
}

.cl-table tbody tr:hover td {
    background: #f8fafc;
}

.cl-badge-not-found {
    background: #f1f5f9;
    color: #94a3b8;
    border: 1px dashed #cbd5e1;
    font-weight: 600;
    font-size: 0.74rem;
    padding: 4px 10px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.cl-avatar-circle {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.88rem;
    flex-shrink: 0;
}
</style>
@endsection

@section('body')
@include('includes.sidebar')

<div class="pc-container">
<div class="pc-content">

    <div class="cl-wrap">
        {{-- Flash Messages --}}
        @include('includes.message')

        {{-- Header Deck --}}
        <div class="cl-header-deck">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 position-relative" style="z-index: 2;">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-warning text-dark font-weight-bold px-3 py-1">RESTAURANT DIRECTORY</span>
                        <span class="text-white-50 small"><i class="fas fa-shield-alt text-success me-1"></i> Deduplicated by Phone</span>
                    </div>
                    <h1 class="h3 font-weight-bold text-white mb-1">Customer List</h1>
                    <p class="text-white-50 mb-0">
                        Customer contact records retrieved directly from orders. Mobile numbers are grouped so each customer phone appears only once.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    {{-- Excel Download Option --}}
                    <a href="{{ route('restaurant.customers.export', request()->all()) }}" class="btn btn-success fw-bold px-3 py-2 shadow-sm">
                        <i class="fas fa-file-excel me-1"></i> Download Excel
                    </a>
                </div>
            </div>
        </div>

        {{-- Top KPI Cards --}}
        <div class="row g-3 mb-4">
            {{-- Total Unique Customers --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="cl-kpi-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="cl-kpi-label">Unique Customers</span>
                        <div class="cl-kpi-icon" style="background: #eef2ff; color: #4f46e5;">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="cl-kpi-value text-dark">{{ number_format($totalCustomersCount) }}</div>
                    <div class="small text-muted mt-1">
                        <i class="fas fa-id-card text-primary me-1"></i> Excludes walk-in guests
                    </div>
                </div>
            </div>

            {{-- Customers with Phone --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="cl-kpi-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="cl-kpi-label">Common Mobile Numbers</span>
                        <div class="cl-kpi-icon" style="background: #ecfdf5; color: #059669;">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                    </div>
                    <div class="cl-kpi-value text-success">{{ number_format($customersWithPhone) }}</div>
                    <div class="small text-muted mt-1">
                        <i class="fas fa-check-circle text-success me-1"></i> Unique phone numbers
                    </div>
                </div>
            </div>

            {{-- Customers with Email --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="cl-kpi-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="cl-kpi-label">Emails on Record</span>
                        <div class="cl-kpi-icon" style="background: #ecfeff; color: #0891b2;">
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>
                    <div class="cl-kpi-value text-info">{{ number_format($customersWithEmail) }}</div>
                    <div class="small text-muted mt-1">
                        <i class="fas fa-at text-info me-1"></i> Available email contacts
                    </div>
                </div>
            </div>

            {{-- Repeat / Loyal Customers --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="cl-kpi-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="cl-kpi-label">Repeat Customers</span>
                        <div class="cl-kpi-icon" style="background: #fdf4ff; color: #c026d3;">
                            <i class="fas fa-heart"></i>
                        </div>
                    </div>
                    <div class="cl-kpi-value text-primary">{{ number_format($repeatCustomersCount) }}</div>
                    <div class="small text-muted mt-1">
                        <i class="fas fa-redo text-primary me-1"></i> Placed more than 1 order
                    </div>
                </div>
            </div>
        </div>

        {{-- Keyword Search & Filter Card --}}
        <div class="cl-filter-card">
            <form method="GET" action="{{ route('restaurant.customers.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-8 col-lg-9">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-1"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="keyword" class="form-control bg-light border-1" placeholder="Keyword search by customer name, mobile number, or email address..." value="{{ $keyword ?? '' }}">
                    </div>
                </div>
                <div class="col-6 col-md-2 col-lg-2">
                    <button type="submit" class="btn btn-primary w-100 fw-bold">
                        <i class="fas fa-filter me-1"></i> Search
                    </button>
                </div>
                <div class="col-6 col-md-2 col-lg-1">
                    <a href="{{ route('restaurant.customers.index') }}" class="btn btn-outline-secondary w-100" title="Reset Search">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>
            </form>
            @if(!empty($keyword))
                <div class="mt-2 text-muted small">
                    Showing search results matching keyword: <strong class="text-dark">"{{ $keyword }}"</strong>
                    ({{ $customers->count() }} records found)
                    <a href="{{ route('restaurant.customers.index') }}" class="text-danger ms-2"><i class="fas fa-times-circle"></i> Clear filter</a>
                </div>
            @endif
        </div>

        {{-- Customers Table Card --}}
        <div class="cl-table-card">
            <div class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="fas fa-address-book text-primary me-2"></i> All Customer Contacts ({{ $customers->count() }})
                </h5>
                <div class="text-muted small">
                    <i class="fas fa-info-circle text-info me-1"></i> Same phone number is merged and will not repeat
                </div>
            </div>

            <div class="table-responsive p-3">
                <table class="table cl-table align-middle" id="customerListTable">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Customer Name</th>
                            <th>Mobile Number</th>
                            <th>Email</th>
                            <th class="text-center">Total Orders</th>
                            <th class="text-end">Total Spent (₹)</th>
                            <th>Last Order Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $index => $c)
                            @php
                                $firstLetter = strtoupper(substr(trim($c->name ?: 'C'), 0, 1));
                            @endphp
                            <tr>
                                <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="cl-avatar-circle">
                                            {{ $firstLetter }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $c->name ?: 'Not Found' }}</div>
                                            @if($c->total_orders > 1)
                                                <small class="badge bg-warning-subtle text-dark border border-warning-subtle">
                                                    <i class="fas fa-crown text-warning me-1"></i> Repeat Customer
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if(!empty($c->phone))
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-6 font-monospace">
                                                <i class="fas fa-phone-alt me-1 text-primary"></i> {{ $c->phone }}
                                            </span>
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $c->phone) }}" target="_blank" class="btn btn-sm btn-outline-success p-1" title="Chat on WhatsApp" style="line-height: 1; border-radius: 6px;">
                                                <i class="fab fa-whatsapp"></i>
                                            </a>
                                        </div>
                                    @else
                                        <span class="cl-badge-not-found">
                                            <i class="fas fa-phone-slash"></i> Not Found
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($c->email))
                                        <a href="mailto:{{ $c->email }}" class="text-decoration-none text-dark fw-semibold">
                                            <i class="fas fa-envelope text-info me-1"></i> {{ $c->email }}
                                        </a>
                                    @else
                                        <span class="cl-badge-not-found">
                                            <i class="fas fa-envelope-open"></i> Not Found
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-dark px-2 py-1 fs-6">
                                        {{ $c->total_orders }}
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-success font-monospace fs-6">
                                    ₹{{ number_format($c->total_spent, 2) }}
                                </td>
                                <td>
                                    @if($c->last_order_date)
                                        <div>{{ $c->last_order_date->format('d M Y') }}</div>
                                        <small class="text-muted">{{ $c->last_order_date->format('h:i A') }}</small>
                                    @else
                                        <span class="cl-badge-not-found">Not Found</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-users-slash fa-3x mb-3 text-secondary"></i>
                                    <h5>No Customers Found</h5>
                                    <p class="mb-0">
                                        @if(!empty($keyword))
                                            No customers matched your search keyword "<strong>{{ $keyword }}</strong>".
                                        @else
                                            No orders with customer names have been recorded yet.
                                        @endif
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
</div>

@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@include('includes.script')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    if ($('#customerListTable tbody tr').length > 1 || !$('#customerListTable tbody td[colspan]').length) {
        $('#customerListTable').DataTable({
            order: [[0, 'asc']],
            pageLength: 25,
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Instant table search..."
            }
        });
    }
});
</script>
@endsection

