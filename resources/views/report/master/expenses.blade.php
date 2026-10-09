@extends('layouts.app')

@section('title')
<title>Master Report - Expenses Report</title>
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
        @include('report.master.nav')

        {{-- 1. FILTERS --}}
        <div class="mr-filter-card">
            <form method="GET" action="{{ route('admin.reports.master.expenses') }}" class="row g-3 align-items-end">
                @if($context['selectedOutletId'])
                    <input type="hidden" name="outlet_id" value="{{ $context['selectedOutletId'] }}">
                @endif

                <div class="col-12 col-sm-6 col-md-3">
                    <label class="mr-form-label"><i class="fas fa-calendar-alt text-primary me-1"></i> From Date</label>
                    <input type="date" name="from_date" class="form-control mr-input" value="{{ $fromDate->format('Y-m-d') }}">
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label class="mr-form-label"><i class="fas fa-calendar-alt text-primary me-1"></i> To Date</label>
                    <input type="date" name="to_date" class="form-control mr-input" value="{{ $toDate->format('Y-m-d') }}">
                </div>

                <div class="col-12 col-sm-6 col-md-2">
                    <label class="mr-form-label">Payment Mode</label>
                    <select name="payment_method" class="form-select mr-input">
                        <option value="">All Payment Modes</option>
                        <option value="CASH" {{ request('payment_method') == 'CASH' ? 'selected' : '' }}>Cash</option>
                        <option value="UPI" {{ request('payment_method') == 'UPI' ? 'selected' : '' }}>UPI</option>
                        <option value="CARD" {{ request('payment_method') == 'CARD' ? 'selected' : '' }}>Card</option>
                        <option value="BANK_TRANSFER" {{ request('payment_method') == 'BANK_TRANSFER' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="CHEQUE" {{ request('payment_method') == 'CHEQUE' ? 'selected' : '' }}>Cheque</option>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-2">
                    <label class="mr-form-label">Search</label>
                    <input type="text" name="search" class="form-control mr-input" placeholder="Title / Description..." value="{{ request('search') }}">
                </div>

                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="mr-btn mr-btn-primary flex-grow-1 justify-content-center">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.reports.master.expenses', $context['selectedOutletId'] ? ['outlet_id' => $context['selectedOutletId']] : []) }}" class="mr-btn mr-btn-outline" title="Reset Filters">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>
            </form>
        </div>

        {{-- 2. SUMMARY METRICS BAR --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-4">
                <div class="mr-kpi-card">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">Total Expense Amount</span>
                        <div class="mr-kpi-icon" style="background: #fef2f2; color: #dc2626;">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value text-danger">₹{{ number_format($totalExpensesAmount, 2) }}</div>
                    <div class="mr-kpi-footer">
                        <span>Total outflow in period</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-4">
                <div class="mr-kpi-card">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">Total Expense Vouchers</span>
                        <div class="mr-kpi-icon" style="background: #eef2ff; color: #4f46e5;">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                    </div>
                    <div class="mr-kpi-value">{{ number_format($totalExpensesCount) }}</div>
                    <div class="mr-kpi-footer">
                        <span>Recorded operational entries</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
                <div class="mr-kpi-card">
                    <div class="mr-kpi-header">
                        <span class="mr-kpi-label">Payment Mode Breakdown</span>
                        <div class="mr-kpi-icon" style="background: #ecfeff; color: #0891b2;">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-1">
                        @forelse($paymentBreakdown as $pb)
                            <span class="badge bg-light text-dark border">
                                {{ strtoupper($pb->payment_method ?: 'OTHER') }}: <strong>₹{{ number_format($pb->total, 2) }}</strong>
                            </span>
                        @empty
                            <span class="text-muted small">No payment data</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. EXPENSES DATA TABLE --}}
        <div class="mr-table-card">
            <div class="mr-table-header">
                <h5 class="mr-table-title">
                    <i class="fas fa-wallet text-primary"></i> Operating Expenses Ledger
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="mr-btn mr-btn-success" onclick="exportActiveReportToExcel()">
                        <i class="fas fa-file-excel"></i> Download Excel
                    </button>
                </div>
            </div>
            <div class="table-responsive p-3">
                <table class="table mr-table align-middle" id="masterExpensesTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Expense Title</th>
                            <th>Expense Date</th>
                            <th>Amount (₹)</th>
                            <th>Payment Mode</th>
                            <th>Recorded By</th>
                            <th>Description / Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $index => $expense)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="font-weight-bold text-dark">{{ $expense->title }}</div>
                                </td>
                                <td>
                                    {{ $expense->expense_date ? $expense->expense_date->format('d M Y') : $expense->created_at->format('d M Y') }}
                                </td>
                                <td class="font-weight-bold text-danger">
                                    ₹{{ number_format($expense->amount, 2) }}
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary">
                                        {{ strtoupper($expense->payment_method ?: 'CASH') }}
                                    </span>
                                </td>
                                <td>
                                    {{ $expense->user ? $expense->user->name : 'System Admin' }}
                                </td>
                                <td>
                                    <small class="text-muted">{{ $expense->description ?: '-' }}</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-wallet fa-3x mb-3 text-slate-300"></i>
                                    <h5>No Expenses Found</h5>
                                    <p class="mb-0">No expense records match the specified date range and filters.</p>
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
    if ($('#masterExpensesTable tbody tr').length > 1 || !$('#masterExpensesTable tbody td[colspan]').length) {
        $('#masterExpensesTable').DataTable({
            order: [[2, 'desc']],
            pageLength: 25,
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search expenses..."
            }
        });
    }
});

function exportActiveReportToExcel() {
    exportTableToExcel('masterExpensesTable', 'Master_Expenses_Report_{{ $fromDate->format("Ymd") }}_to_{{ $toDate->format("Ymd") }}');
}
</script>
@endsection
