@extends('layouts.app')

@section('title')
<title>Performance Master Report - Customers - {{ $restaurant->name }}</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endsection

@section('body')
@include('includes.sidebar')

<div class="pc-container">
<div class="pc-content">

    <div class="mr-wrap">
        {{-- Flash Messages --}}
        @include('includes.message')

        {{-- Master Header & Nav --}}
        @include('franchise.performance.report.nav')

        {{-- 1. KEYWORD SEARCH & ACTIONS FILTER CARD --}}
        <div class="mr-filter-card">
            <form method="GET" action="{{ route('franchise.performance.report.customers', ['restaurantId' => $restaurant->id]) }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-6 col-lg-5">
                    <label class="mr-form-label"><i class="fas fa-search text-primary me-1"></i> Search Customers</label>
                    <input type="text" name="keyword" class="form-control mr-input" 
                           placeholder="Search by customer name, mobile number, or email..." 
                           value="{{ $keyword }}">
                </div>

                <div class="col-12 col-md-6 col-lg-7 d-flex gap-2 flex-wrap justify-content-md-end">
                    <button type="submit" class="mr-btn mr-btn-primary">
                        <i class="fas fa-filter"></i> Search
                    </button>
                    @if(!empty($keyword))
                        <a href="{{ route('franchise.performance.report.customers', ['restaurantId' => $restaurant->id]) }}" class="mr-btn mr-btn-outline">
                            <i class="fas fa-undo"></i> Reset
                        </a>
                    @endif
                    <a href="{{ route('franchise.performance.report.customers.export', array_merge(['restaurantId' => $restaurant->id], request()->except('restaurantId'))) }}" class="mr-btn mr-btn-success">
                        <i class="fas fa-file-excel"></i> Download Excel
                    </a>
                </div>
            </form>
        </div>

        {{-- 2. KPI METRICS CARDS --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="mr-kpi-card" style="border-left: 4px solid var(--mr-primary);">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">Unique Customers</span>
                        <div class="mr-kpi-icon" style="background: var(--mr-primary-light); color: var(--mr-primary);">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value">{{ number_format($totalCustomersCount) }}</div>
                    <div class="mr-kpi-footer">
                        <i class="fas fa-shield-alt text-success"></i> Deduplicated by phone
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="mr-kpi-card" style="border-left: 4px solid var(--mr-success);">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">With Mobile No.</span>
                        <div class="mr-kpi-icon" style="background: var(--mr-success-light); color: var(--mr-success);">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value">{{ number_format($customersWithPhone) }}</div>
                    <div class="mr-kpi-footer">
                        <span class="text-success font-weight-bold">{{ $totalCustomersCount > 0 ? round(($customersWithPhone / $totalCustomersCount) * 100, 1) : 0 }}%</span> of customer base
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="mr-kpi-card" style="border-left: 4px solid var(--mr-info);">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">With Email</span>
                        <div class="mr-kpi-icon" style="background: var(--mr-info-light); color: var(--mr-info);">
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value">{{ number_format($customersWithEmail) }}</div>
                    <div class="mr-kpi-footer">
                        <span class="text-info font-weight-bold">{{ $totalCustomersCount > 0 ? round(($customersWithEmail / $totalCustomersCount) * 100, 1) : 0 }}%</span> email reached
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="mr-kpi-card" style="border-left: 4px solid var(--mr-accent);">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">Repeat Customers</span>
                        <div class="mr-kpi-icon" style="background: #fff3e6; color: var(--mr-accent);">
                            <i class="fas fa-sync-alt"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value">{{ number_format($repeatCustomersCount) }}</div>
                    <div class="mr-kpi-footer">
                        <span class="text-warning font-weight-bold">{{ $totalCustomersCount > 0 ? round(($repeatCustomersCount / $totalCustomersCount) * 100, 1) : 0 }}%</span> retention rate
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. CUSTOMER DATA TABLE CARD --}}
        <div class="mr-card">
            <div class="mr-card-header">
                <div>
                    <h3 class="mr-card-title"><i class="fas fa-address-book text-primary me-2"></i>Customer Directory</h3>
                    <div class="mr-card-subtitle">
                        Showing all registered customers who ordered from {{ $restaurant->name }}. Walk-in orders excluded, common phone numbers deduplicated.
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary rounded-pill px-3 py-2" style="font-size: 0.85rem;">
                        {{ $customers->count() }} Customers Listed
                    </span>
                </div>
            </div>

            <div class="mr-card-body p-0">
                <div class="table-responsive">
                    <table class="table mr-table table-hover align-middle mb-0" id="performanceCustomersTable">
                        <thead>
                            <tr>
                                <th style="width: 60px;" class="text-center">#</th>
                                <th>Customer Name</th>
                                <th>Mobile Number</th>
                                <th>Email Address</th>
                                <th class="text-center">Total Orders</th>
                                <th class="text-end">Total Spent</th>
                                <th>Last Order Date</th>
                                <th class="text-center">Customer Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $idx => $customer)
                                <tr>
                                    <td class="text-center font-weight-bold text-muted">{{ $idx + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white font-weight-bold" 
                                                 style="width: 36px; height: 36px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); font-size: 14px; flex-shrink: 0;">
                                                {{ strtoupper(substr($customer->name ?: 'C', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-weight-bold text-dark">{{ $customer->name ?: 'Not Found' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if(!empty($customer->phone))
                                            <a href="tel:{{ $customer->phone }}" class="text-decoration-none font-weight-bold text-primary">
                                                <i class="fas fa-phone-alt me-1 text-muted" style="font-size: 11px;"></i>{{ $customer->phone }}
                                            </a>
                                        @else
                                            <span class="badge bg-light text-muted border px-2 py-1">Not Found</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($customer->email))
                                            <a href="mailto:{{ $customer->email }}" class="text-decoration-none text-secondary">
                                                <i class="fas fa-envelope me-1 text-muted" style="font-size: 11px;"></i>{{ $customer->email }}
                                            </a>
                                        @else
                                            <span class="badge bg-light text-muted border px-2 py-1">Not Found</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill" style="background: #eef2ff; color: #4f46e5; font-size: 12px; font-weight: 700; padding: 6px 12px;">
                                            {{ $customer->total_orders }} {{ Str::plural('Order', $customer->total_orders) }}
                                        </span>
                                    </td>
                                    <td class="text-end font-weight-bold text-dark">
                                        ₹{{ number_format($customer->total_spent, 2) }}
                                    </td>
                                    <td>
                                        @if($customer->last_order_date)
                                            <span class="text-dark font-weight-500">{{ $customer->last_order_date->format('d M Y') }}</span>
                                            <span class="text-muted small d-block">{{ $customer->last_order_date->format('h:i A') }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($customer->total_orders > 1)
                                            <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-1">
                                                <i class="fas fa-crown me-1"></i> Repeat
                                            </span>
                                        @else
                                            <span class="badge rounded-pill bg-secondary-subtle text-secondary border px-3 py-1">
                                                New
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="py-4">
                                            <div class="mb-3">
                                                <i class="fas fa-user-slash text-muted" style="font-size: 3rem;"></i>
                                            </div>
                                            <h5 class="text-muted font-weight-bold">No Customers Found</h5>
                                            <p class="text-muted small mb-0">
                                                @if(!empty($keyword))
                                                    No customers matched the search term "<strong>{{ $keyword }}</strong>".
                                                @else
                                                    No orders with named customer details found for this restaurant.
                                                @endif
                                            </p>
                                        </div>
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
</div>

@endsection

@section('script')
@include('includes.script')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    if ($('#performanceCustomersTable tbody tr td').length > 1) {
        $('#performanceCustomersTable').DataTable({
            paging: true,
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            searching: false,
            ordering: true,
            info: true,
            autoWidth: false,
            language: {
                emptyTable: "No customers found",
                zeroRecords: "No matching customers found"
            }
        });
    }
});

function exportActiveReportToExcel() {
    window.location.href = "{{ route('franchise.performance.report.customers.export', array_merge(['restaurantId' => $restaurant->id], request()->except('restaurantId'))) }}";
}
</script>
@endsection

