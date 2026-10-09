@extends('layouts.app')

@section('title')
<title>Supplier Ledger - {{ $supplier->supplier_name }} | Admin</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('admin_template/css/inventory-modules.css') }}">
<style>
    /* Add New Deposit Premium Card */
    .deposit-action-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid var(--inv-border);
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
        margin-bottom: 26px;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .deposit-card-top {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #ffffff;
        padding: 20px 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .deposit-top-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .deposit-header-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: var(--inv-primary-gradient);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        box-shadow: 0 4px 14px rgba(255, 94, 20, 0.35);
        flex-shrink: 0;
    }

    .deposit-card-title {
        font-family: 'Outfit', sans-serif;
        font-size: 1.25rem;
        font-weight: 800;
        margin: 0;
        color: #ffffff;
        letter-spacing: -0.01em;
    }

    .deposit-card-sub {
        font-size: 0.8rem;
        color: #94a3b8;
        margin: 0;
    }

    .deposit-form-body {
        padding: 26px 28px;
        background: #ffffff;
    }

    .deposit-label {
        font-size: 0.74rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--inv-slate);
        margin-bottom: 6px;
        display: block;
    }

    .deposit-input {
        width: 100%;
        background: #f8fafc;
        border: 1px solid var(--inv-border);
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 0.9rem;
        color: var(--inv-slate);
        outline: none;
        transition: all 0.2s ease;
    }

    .deposit-input:focus {
        background: #ffffff;
        border-color: var(--inv-primary);
        box-shadow: 0 0 0 3px rgba(255, 94, 20, 0.12);
    }

    .deposit-amount-input {
        font-family: 'Outfit', sans-serif;
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--inv-success-text);
        background: #f0fdf4;
        border-color: #bbf7d0;
    }

    .deposit-amount-input:focus {
        border-color: #10b981;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
    }

    .btn-deposit-save {
        background: var(--inv-primary-gradient);
        color: #ffffff;
        border: none;
        padding: 11px 24px;
        border-radius: 30px;
        font-weight: 800;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 16px rgba(255, 94, 20, 0.28);
        transition: all 0.25s ease;
        cursor: pointer;
        width: 100%;
        height: 44px;
    }

    .btn-deposit-save:hover {
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 8px 22px rgba(255, 94, 20, 0.38);
    }

    /* Ledger Tables & Badges */
    .payment-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.74rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }
    .payment-cash { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
    .payment-upi { background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; }
    .payment-bank_transfer { background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; }
    .payment-cheque { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
    .payment-other { background: #f1f5f9; color: var(--inv-muted); border: 1px solid var(--inv-border); }

    .preset-chip {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 20px;
        background: #f1f5f9;
        color: var(--inv-slate);
        border: 1px solid var(--inv-border);
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        cursor: pointer;
    }
    .preset-chip:hover {
        background: var(--inv-primary-light);
        border-color: var(--inv-primary);
        color: var(--inv-primary);
    }

    @media print {
        .inv-header-deck, .inv-filter-card, .deposit-action-card, .inv-actions, .pc-sidebar, .pc-header {
            display: none !important;
        }
        body, .pc-container, .pc-content, .inv-page-wrap {
            background: #ffffff !important;
            padding: 0 !important;
            margin: 0 !important;
        }
    }
</style>
@endsection

@section('body')
@include('includes.sidebar')

<div class="pc-container">
<div class="pc-content">

    <div class="inv-page-wrap">
        {{-- Flash / Error Messages --}}
        @include('includes.message')

        {{-- 1. Header Deck --}}
        <div class="inv-header-deck">
            <div class="inv-header-left">
                <div class="inv-header-icon icon-suppliers">
                    <i class="fas fa-book"></i>
                </div>
                <div class="inv-header-title-meta">
                    <span class="inv-header-eyebrow">Supplier Financial Statement</span>
                    <h1 class="inv-header-title">
                        {{ $supplier->supplier_name }}
                        @if($supplier->shop_name)
                            <small class="text-muted fs-6 fw-normal">({{ $supplier->shop_name }})</small>
                        @endif
                    </h1>
                    <p class="inv-header-sub">
                        <i class="fas fa-phone me-1 text-primary"></i> {{ $supplier->phone }}
                        @if($supplier->email)
                            | <i class="fas fa-envelope me-1 text-primary"></i> {{ $supplier->email }}
                        @endif
                        @if($supplier->gstin)
                            | <strong>GSTIN:</strong> {{ $supplier->gstin }}
                        @endif
                    </p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary rounded-pill px-3 fw-bold" style="font-size: 0.86rem;">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
                <a href="{{ route('purchases.create') }}?supplier_id={{ $supplier->id }}" class="btn-inv-primary">
                    <i class="fas fa-cart-plus"></i> New Purchase
                </a>
                <button type="button" onclick="window.print()" class="btn btn-light rounded-pill border px-3 fw-bold" style="font-size: 0.86rem;">
                    <i class="fas fa-print me-1 text-primary"></i> Print
                </button>
            </div>
        </div>

        {{-- 2. Date Range Filter Card --}}
        <div class="inv-filter-card">
            <form method="GET" action="{{ route('suppliers.ledger', $supplier->id) }}" id="ledgerFilterForm" class="row g-3 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label class="inv-filter-label"><i class="far fa-calendar-alt me-1 text-primary"></i> From Date</label>
                    <input type="date" name="start_date" id="filterStartDate" class="inv-filter-control" value="{{ $startDate }}" required>
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="inv-filter-label"><i class="far fa-calendar-alt me-1 text-primary"></i> To Date</label>
                    <input type="date" name="end_date" id="filterEndDate" class="inv-filter-control" value="{{ $endDate }}" required>
                </div>
                <div class="col-md-6 col-sm-12 d-flex gap-2 justify-content-end">
                    <button type="submit" class="btn-inv-primary px-4">
                        <i class="fas fa-filter me-1"></i> Filter Ledger
                    </button>
                    <a href="{{ route('suppliers.ledger', $supplier->id) }}" class="btn btn-outline-secondary rounded-pill px-3 fw-bold" style="font-size: 0.86rem;" title="Reset to Current Month">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
                
                {{-- Quick Presets --}}
                <div class="col-12 d-flex align-items-center gap-1 flex-wrap pt-2 border-top">
                    <span class="text-muted small fw-bold me-1">Quick Presets:</span>
                    <button type="button" class="preset-chip" onclick="setLedgerPreset('current_month')">
                        <i class="fas fa-calendar-day"></i> Current Month
                    </button>
                    <button type="button" class="preset-chip" onclick="setLedgerPreset('last_30_days')">
                        <i class="fas fa-calendar-week"></i> Last 30 Days
                    </button>
                    <button type="button" class="preset-chip" onclick="setLedgerPreset('this_year')">
                        <i class="fas fa-calendar"></i> This Year
                    </button>
                    <a href="{{ route('suppliers.ledger', [$supplier->id, 'start_date' => '2000-01-01', 'end_date' => date('Y-m-d')]) }}" class="preset-chip">
                        <i class="fas fa-infinity"></i> All Time
                    </a>
                </div>
            </form>
        </div>

        {{-- 3. Analytical Summary Cards --}}
        <div class="inv-stats-grid">
            <div class="inv-stat-card stat-info">
                <div class="inv-stat-info">
                    <span class="inv-stat-label">Opening Outstanding</span>
                    <span class="inv-stat-val">₹{{ number_format($openingBalance, 2) }}</span>
                    <small class="text-muted" style="font-size: 0.72rem;">As of {{ date('d M Y', strtotime($startDate . ' -1 day')) }}</small>
                </div>
                <div class="inv-stat-icon">
                    <i class="fas fa-clock-rotate-left"></i>
                </div>
            </div>

            <div class="inv-stat-card stat-primary">
                <div class="inv-stat-info">
                    <span class="inv-stat-label">Total Purchases (Period)</span>
                    <span class="inv-stat-val text-primary">₹{{ number_format($totalPurchases, 2) }}</span>
                    <small class="text-muted" style="font-size: 0.72rem;">{{ $purchases->count() }} Invoices in range</small>
                </div>
                <div class="inv-stat-icon">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>

            <div class="inv-stat-card stat-success">
                <div class="inv-stat-info">
                    <span class="inv-stat-label">Total Deposits Paid</span>
                    <span class="inv-stat-val text-success">₹{{ number_format($totalDeposits, 2) }}</span>
                    <small class="text-muted" style="font-size: 0.72rem;">{{ $deposits->count() }} Payments settled</small>
                </div>
                <div class="inv-stat-icon">
                    <i class="fas fa-hand-holding-dollar"></i>
                </div>
            </div>

            <div class="inv-stat-card {{ $closingBalance > 0 ? 'stat-danger' : 'stat-success' }}">
                <div class="inv-stat-info">
                    <span class="inv-stat-label">Net Closing Balance</span>
                    <span class="inv-stat-val {{ $closingBalance > 0 ? 'text-danger' : 'text-success' }}">
                        ₹{{ number_format($closingBalance, 2) }}
                    </span>
                    <small class="text-muted" style="font-size: 0.72rem;">{{ $closingBalance > 0 ? 'Payable to vendor' : 'Clear / Advance' }}</small>
                </div>
                <div class="inv-stat-icon">
                    <i class="fas fa-scale-balanced"></i>
                </div>
            </div>
        </div>

        {{-- 4. ★ PROMINENT ADD NEW DEPOSIT / PAYMENT SECTION ★ --}}
        <div class="deposit-action-card">
            <div class="deposit-card-top">
                <div class="deposit-top-left">
                    <div class="deposit-header-icon">
                        <i class="fas fa-hand-holding-dollar"></i>
                    </div>
                    <div>
                        <h3 class="deposit-card-title">Record New Deposit / Payment</h3>
                        <p class="deposit-card-sub">Record settlement payment or advance deposit paid to {{ $supplier->supplier_name }}</p>
                    </div>
                </div>
                <span class="badge bg-white text-dark fw-bold px-3 py-2 border rounded-pill" style="font-size: 0.78rem;">
                    <i class="fas fa-shield-alt text-success me-1"></i> Auto-updates Vendor Balance
                </span>
            </div>

            <div class="deposit-form-body">
                <form action="{{ route('suppliers.deposit.store') }}" method="POST" id="addDepositForm">
                    @csrf
                    <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">

                    <div class="row g-3">
                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <label class="deposit-label"><i class="far fa-calendar-check me-1 text-primary"></i> Deposit Date <span class="text-danger">*</span></label>
                            <input type="date" name="deposit_date" class="deposit-input" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <label class="deposit-label"><i class="fas fa-indian-rupee-sign me-1 text-success"></i> Amount to Deposit (₹) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="deposit-input deposit-amount-input" step="0.01" min="0.01" placeholder="0.00" required>
                        </div>

                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <label class="deposit-label"><i class="fas fa-credit-card me-1 text-secondary"></i> Payment Mode <span class="text-danger">*</span></label>
                            <select name="payment_mode" class="deposit-input" required>
                                <option value="">-- Select Mode --</option>
                                @foreach($paymentModes as $key => $mode)
                                    <option value="{{ $key }}">{{ $mode }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6 col-sm-6">
                            <label class="deposit-label"><i class="fas fa-receipt me-1 text-muted"></i> Transaction / Ref No.</label>
                            <input type="text" name="transaction_no" class="deposit-input font-monospace" placeholder="e.g. UTR / Cheque / TXN-9988">
                        </div>

                        <div class="col-lg-9 col-md-8 col-sm-12">
                            <label class="deposit-label"><i class="fas fa-pen me-1 text-muted"></i> Remarks / Payment Notes</label>
                            <input type="text" name="remarks" class="deposit-input" placeholder="Optional cashier or settlement remarks">
                        </div>

                        <div class="col-lg-3 col-md-4 col-sm-12 d-flex align-items-end">
                            <button type="submit" class="btn-deposit-save" id="btnSaveDeposit">
                                <i class="fas fa-check-circle"></i> Save Deposit Payment
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- 5. Split Ledger Tables: Purchases vs Deposits --}}
        <div class="row g-4">
            {{-- Purchases Table Card --}}
            <div class="col-lg-6">
                <div class="inv-card h-100">
                    <div class="d-flex justify-content-between align-items-center p-3 border-bottom bg-light">
                        <h5 class="m-0 fw-bold text-dark font-outfit fs-6">
                            <i class="fas fa-file-invoice text-primary me-2"></i> Inward Purchases
                        </h5>
                        <span class="badge bg-primary rounded-pill px-3">{{ $purchases->count() }} Invoices</span>
                    </div>
                    <div class="table-responsive">
                        <table class="inv-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Invoice No</th>
                                    <th class="text-end">Amount (₹)</th>
                                    <th class="text-center" width="60">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchases as $purchase)
                                <tr>
                                    <td class="text-muted small font-monospace">
                                        {{ date('d M Y', strtotime($purchase->purchase_date)) }}
                                    </td>
                                    <td>
                                        <strong class="text-dark">{{ $purchase->invoice_no }}</strong>
                                        <small class="text-muted d-block">{{ $purchase->total_items }} items inward</small>
                                    </td>
                                    <td class="text-end fw-extrabold text-dark" style="font-family: 'Outfit', sans-serif;">
                                        ₹{{ number_format($purchase->total_amount, 2) }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('purchases.show', $purchase->id) }}" class="inv-btn-action btn-view" title="View Purchase Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="fas fa-box-open fa-2x mb-2 d-block opacity-50"></i>
                                        No purchases recorded in this period.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(!$purchases->isEmpty())
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-end">Total Purchases:</th>
                                    <th class="text-end text-primary fw-extrabold fs-6" style="font-family: 'Outfit', sans-serif;">
                                        ₹{{ number_format($totalPurchases, 2) }}
                                    </th>
                                    <th></th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            {{-- Deposits Table Card --}}
            <div class="col-lg-6">
                <div class="inv-card h-100">
                    <div class="d-flex justify-content-between align-items-center p-3 border-bottom bg-light">
                        <h5 class="m-0 fw-bold text-dark font-outfit fs-6">
                            <i class="fas fa-hand-holding-dollar text-success me-2"></i> Deposits & Payments
                        </h5>
                        <span class="badge bg-success rounded-pill px-3">{{ $deposits->count() }} Deposits</span>
                    </div>
                    <div class="table-responsive">
                        <table class="inv-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-end">Amount (₹)</th>
                                    <th>Payment Mode</th>
                                    <th>Remarks</th>
                                    <th class="text-center" width="60">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deposits as $deposit)
                                @php
                                    $paymentClass = 'payment-' . strtolower(str_replace(' ', '_', $deposit->payment_mode));
                                @endphp
                                <tr>
                                    <td class="text-muted small font-monospace">
                                        {{ date('d M Y', strtotime($deposit->deposit_date)) }}
                                    </td>
                                    <td class="text-end fw-extrabold text-success" style="font-family: 'Outfit', sans-serif;">
                                        ₹{{ number_format($deposit->amount, 2) }}
                                    </td>
                                    <td>
                                        <span class="payment-badge {{ $paymentClass }}">
                                            {{ \App\Models\SupplierDeposit::PAYMENT_MODES[$deposit->payment_mode] ?? $deposit->payment_mode }}
                                        </span>
                                        @if($deposit->transaction_no)
                                            <small class="text-muted d-block font-monospace" style="font-size: 0.7rem;">Ref: {{ $deposit->transaction_no }}</small>
                                        @endif
                                    </td>
                                    <td class="text-muted small">{{ $deposit->remarks ?: '-' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('suppliers.deposit.delete', $deposit->id) }}" 
                                           class="inv-btn-action btn-del"
                                           onclick="return confirm('Are you sure you want to delete this deposit record of ₹{{ $deposit->amount }}?')"
                                           title="Delete deposit">
                                            <i class="fas fa-trash-can"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-receipt fa-2x mb-2 d-block opacity-50"></i>
                                        No deposit payments recorded in this period.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(!$deposits->isEmpty())
                            <tfoot>
                                <tr>
                                    <th>Total Deposits:</th>
                                    <th class="text-end text-success fw-extrabold fs-6" style="font-family: 'Outfit', sans-serif;">
                                        ₹{{ number_format($totalDeposits, 2) }}
                                    </th>
                                    <th colspan="3"></th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- 6. Historical Summary & Final Ledger Statistics --}}
        <div class="inv-card mt-4">
            <div class="p-3 border-bottom bg-dark text-white d-flex align-items-center gap-2">
                <i class="fas fa-calculator text-warning"></i>
                <h6 class="m-0 text-white font-outfit fw-bold">Full Ledger Summary & Balance Calculations</h6>
            </div>
            <div class="p-4">
                <div class="row g-4">
                    <div class="col-md-4">
                        <span class="inv-filter-label text-muted">All-Time Historical Summary</span>
                        <table class="table table-bordered mb-0 rounded overflow-hidden">
                            <tr>
                                <th class="bg-light fw-bold text-secondary" style="font-size: 0.85rem;">Opening Outstanding</th>
                                <td class="text-end fw-bold">₹{{ number_format($supplier->opening_outstanding, 2) }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light text-secondary" style="font-size: 0.85rem;">All-Time Purchases</th>
                                <td class="text-end">₹{{ number_format($supplier->getTotalPurchasesAttribute(), 2) }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light text-secondary" style="font-size: 0.85rem;">All-Time Deposits</th>
                                <td class="text-end text-success fw-bold">₹{{ number_format($supplier->getTotalDepositsAttribute(), 2) }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-4">
                        <span class="inv-filter-label text-muted">Current Vendor Profile Status</span>
                        <table class="table table-bordered mb-0 rounded overflow-hidden">
                            <tr>
                                <th class="bg-light fw-bold text-secondary" style="font-size: 0.85rem;">Current Outstanding</th>
                                <td class="text-end fw-bold {{ $supplier->current_outstanding > 0 ? 'text-danger' : 'text-success' }}">
                                    ₹{{ number_format($supplier->current_outstanding, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light text-secondary" style="font-size: 0.85rem;">Last Purchase Date</th>
                                <td class="text-end">
                                    {{ $supplier->last_purchase_date ? date('d M Y', strtotime($supplier->last_purchase_date)) : 'Never' }}
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light text-secondary" style="font-size: 0.85rem;">Last Deposit Date</th>
                                <td class="text-end">
                                    {{ $supplier->last_deposit_date ? date('d M Y', strtotime($supplier->last_deposit_date)) : 'Never' }}
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-4">
                        <span class="inv-filter-label text-muted">Ledger Computation (Selected Period)</span>
                        <table class="table table-bordered mb-0 rounded overflow-hidden">
                            <tr>
                                <th class="bg-light text-secondary" style="font-size: 0.85rem;">Opening Balance</th>
                                <td class="text-end">₹{{ number_format($openingBalance, 2) }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light text-secondary" style="font-size: 0.85rem;">+ Total Purchases</th>
                                <td class="text-end">₹{{ number_format($totalPurchases, 2) }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light text-secondary" style="font-size: 0.85rem;">- Total Deposits</th>
                                <td class="text-end text-success">₹{{ number_format($totalDeposits, 2) }}</td>
                            </tr>
                            <tr class="table-active">
                                <th class="fw-bold text-dark font-outfit" style="font-size: 0.88rem;">Closing Period Balance</th>
                                <td class="text-end fw-extrabold font-outfit {{ $closingBalance > 0 ? 'text-danger' : 'text-success' }}" style="font-size: 1.05rem;">
                                    ₹{{ number_format($closingBalance, 2) }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="p-3 bg-light border-top text-end">
                <small class="text-muted fw-bold" style="font-size: 0.74rem;">
                    <i class="fas fa-info-circle me-1 text-primary"></i> 
                    Ledger period: {{ date('d M Y', strtotime($startDate)) }} to {{ date('d M Y', strtotime($endDate)) }}
                    | Statement generated on: {{ date('d M Y, h:i A') }}
                </small>
            </div>
        </div>

    </div>

</div>
</div>

@endsection

@section('script')
@include('includes.script')
<script>
    function setLedgerPreset(type) {
        const now = new Date();
        const startInput = document.getElementById('filterStartDate');
        const endInput = document.getElementById('filterEndDate');
        const form = document.getElementById('ledgerFilterForm');

        function formatDate(d) {
            let month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();
            if (month.length < 2) month = '0' + month;
            if (day.length < 2) day = '0' + day;
            return [year, month, day].join('-');
        }

        if (type === 'current_month') {
            const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
            const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
            startInput.value = formatDate(firstDay);
            endInput.value = formatDate(lastDay);
        } else if (type === 'last_30_days') {
            const past30 = new Date();
            past30.setDate(now.getDate() - 30);
            startInput.value = formatDate(past30);
            endInput.value = formatDate(now);
        } else if (type === 'this_year') {
            const firstDayYear = new Date(now.getFullYear(), 0, 1);
            const lastDayYear = new Date(now.getFullYear(), 11, 31);
            startInput.value = formatDate(firstDayYear);
            endInput.value = formatDate(lastDayYear);
        }

        if (form) form.submit();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const depositForm = document.getElementById('addDepositForm');
        if (depositForm) {
            depositForm.addEventListener('submit', function(e) {
                const amountInput = depositForm.querySelector('input[name="amount"]');
                const amount = parseFloat(amountInput.value) || 0;
                if (amount <= 0) {
                    alert('Please enter a valid deposit amount greater than 0');
                    e.preventDefault();
                    amountInput.focus();
                    return false;
                }
            });
        }
    });
</script>
@endsection