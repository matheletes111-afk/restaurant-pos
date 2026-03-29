<!DOCTYPE html>
<html lang="en">
<head>
    <title>Supplier Ledger - {{ $supplier->supplier_name }}</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <style>
        .summary-card {
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .opening-balance { background-color: #e3f2fd; border-left: 4px solid #2196f3; }
        .total-purchases { background-color: #fff3e0; border-left: 4px solid #ff9800; }
        .total-deposits { background-color: #e8f5e9; border-left: 4px solid #4caf50; }
        .closing-balance { background-color: #fce4ec; border-left: 4px solid #e91e63; }
        .amount-badge {
            font-size: 1.2em;
            font-weight: bold;
        }
        .positive { color: #28a745; }
        .negative { color: #dc3545; }
        .neutral { color: #6c757d; }
        .payment-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .payment-cash { background-color: #28a745; color: white; }
        .payment-upi { background-color: #6610f2; color: white; }
        .payment-bank { background-color: #17a2b8; color: white; }
        .payment-cheque { background-color: #ffc107; color: black; }
        .payment-other { background-color: #6c757d; color: white; }
        .date-filter {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .section-title {
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: #495057;
        }
    </style>
</head>

<body data-pc-theme="light">
<div class="loader-bg">
    <div class="loader-track">
        <div class="loader-fill"></div>
    </div>
</div>

@include('includes.sidebar')

<div class="pc-container">
    <div class="pc-content">

        <!-- Breadcrumb -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Supplier Ledger - {{ $supplier->supplier_name }}</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Suppliers</a></li>
                            <li class="breadcrumb-item" aria-current="page">Supplier Ledger</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- Breadcrumb end -->

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="mb-0">
                                    <i class="fa fa-book"></i> Ledger for {{ $supplier->supplier_name }}
                                    @if($supplier->shop_name)
                                        <small class="text-muted">({{ $supplier->shop_name }})</small>
                                    @endif
                                </h5>
                                <p class="text-muted mb-0 mt-1">
                                    <i class="fa fa-phone"></i> {{ $supplier->phone }} 
                                    @if($supplier->email)
                                        | <i class="fa fa-envelope"></i> {{ $supplier->email }}
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Back to Suppliers
                                </a>
                                <a href="{{ route('purchases.create') }}?supplier_id={{ $supplier->id }}" 
                                   class="btn btn-success">
                                    <i class="fa fa-plus"></i> New Purchase
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Date Filter -->
                        <div class="date-filter">
                            <form method="GET" action="{{ route('suppliers.ledger', $supplier->id) }}" class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>From Date</label>
                                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>To Date</label>
                                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-filter"></i> Filter
                                            </button>
                                            <a href="{{ route('suppliers.ledger', $supplier->id) }}" class="btn btn-secondary">
                                                <i class="fa fa-refresh"></i> Reset
                                            </a>
                                            <button type="button" onclick="window.print()" class="btn btn-info">
                                                <i class="fa fa-print"></i> Print
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card summary-card opening-balance">
                                    <div class="card-body">
                                        <h6 class="text-muted">Opening Outstanding</h6>
                                        <h3 class="amount-badge {{ $openingBalance > 0 ? 'negative' : 'neutral' }}">
                                            ₹{{ number_format($supplier->opening_outstanding, 2) }}
                                        </h3>
                                        <small class="text-muted">As on {{ date('d-m-Y', strtotime($startDate . ' -1 day')) }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card summary-card total-purchases">
                                    <div class="card-body">
                                        <h6 class="text-muted">Total Purchases</h6>
                                        <h3 class="amount-badge negative">
                                            ₹{{ number_format($totalPurchases, 2) }}
                                        </h3>
                                        <small class="text-muted">{{ $purchases->count() }} purchases in period</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card summary-card total-deposits">
                                    <div class="card-body">
                                        <h6 class="text-muted">Total Deposits</h6>
                                        <h3 class="amount-badge positive">
                                            ₹{{ number_format($totalDeposits, 2) }}
                                        </h3>
                                        <small class="text-muted">{{ $deposits->count() }} deposits in period</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card summary-card closing-balance">
                                    <div class="card-body">
                                        <h6 class="text-muted">Closing Balance</h6>
                                        <h3 class="amount-badge {{ $closingBalance > 0 ? 'negative' : ($closingBalance < 0 ? 'positive' : 'neutral') }}">
                                            ₹{{ number_format($closingBalance, 2) }}
                                        </h3>
                                        <small class="text-muted">As on {{ date('d-m-Y', strtotime($endDate)) }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Add Deposit Form -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0"><i class="fa fa-plus-circle"></i> Add New Deposit</h6>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('suppliers.deposit.store') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">
                                            
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Deposit Date *</label>
                                                        <input type="date" name="deposit_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Amount (₹) *</label>
                                                        <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Payment Mode *</label>
                                                        <select name="payment_mode" class="form-control" required>
                                                            <option value="">Select Mode</option>
                                                            @foreach($paymentModes as $key => $mode)
                                                                <option value="{{ $key }}">{{ $mode }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Transaction No.</label>
                                                        <input type="text" name="transaction_no" class="form-control" placeholder="Optional">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-9">
                                                    <div class="form-group">
                                                        <label>Remarks</label>
                                                        <textarea name="remarks" class="form-control" rows="1" placeholder="Optional remarks"></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>&nbsp;</label>
                                                        <button type="submit" class="btn btn-success btn-block">
                                                            <i class="fa fa-save"></i> Add Deposit
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Purchases Section -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="section-title mb-0">
                                            <i class="fa fa-shopping-cart"></i> Purchases 
                                            <span class="badge badge-secondary">{{ $purchases->count() }}</span>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Invoice No</th>
                                                        <th class="text-right">Amount (₹)</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($purchases as $purchase)
                                                    <tr>
                                                        <td>{{ date('d-m-Y', strtotime($purchase->purchase_date)) }}</td>
                                                        <td>{{ $purchase->invoice_no }}</td>
                                                        <td class="text-right font-weight-bold">
                                                            ₹{{ number_format($purchase->total_amount, 2) }}
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('purchases.show', $purchase->id) }}" 
                                                               class="btn btn-sm btn-info" title="View">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                    @if($purchases->isEmpty())
                                                    <tr>
                                                        <td colspan="4" class="text-center text-muted">
                                                            No purchases found in this period
                                                        </td>
                                                    </tr>
                                                    @endif
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="2" class="text-right"><strong>Total:</strong></td>
                                                        <td class="text-right font-weight-bold">
                                                            ₹{{ number_format($totalPurchases, 2) }}
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Deposits Section -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="section-title mb-0">
                                            <i class="fa fa-money"></i> Deposits 
                                            <span class="badge badge-secondary">{{ $deposits->count() }}</span>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Amount (₹)</th>
                                                        <th>Mode</th>
                                                        <th>Remarks</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($deposits as $deposit)
                                                        @php
                                                            $paymentClass = 'payment-' . strtolower($deposit->payment_mode);
                                                        @endphp
                                                    <tr>
                                                        <td>{{ date('d-m-Y', strtotime($deposit->deposit_date)) }}</td>
                                                        <td class="text-right font-weight-bold text-success">
                                                            ₹{{ number_format($deposit->amount, 2) }}
                                                        </td>
                                                        <td>
                                                            <span class="payment-badge {{ $paymentClass }}">
                                                                {{ \App\Models\SupplierDeposit::PAYMENT_MODES[$deposit->payment_mode] }}
                                                            </span>
                                                            @if($deposit->transaction_no)
                                                                <br><small class="text-muted">{{ $deposit->transaction_no }}</small>
                                                            @endif
                                                        </td>
                                                        <td>{{ $deposit->remarks ?? '-' }}</td>
                                                        <td>
                                                            <a href="{{ route('suppliers.deposit.delete', $deposit->id) }}" 
                                                               class="btn btn-sm btn-danger"
                                                               onclick="return confirm('Are you sure you want to delete this deposit?')"
                                                               title="Delete">
                                                                <i class="fa fa-trash"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                    @if($deposits->isEmpty())
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted">
                                                            No deposits found in this period
                                                        </td>
                                                    </tr>
                                                    @endif
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="1" class="text-right"><strong>Total:</strong></td>
                                                        <td class="text-right font-weight-bold text-success">
                                                            ₹{{ number_format($totalDeposits, 2) }}
                                                        </td>
                                                        <td colspan="3"></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ledger Summary -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0"><i class="fa fa-calculator"></i> Ledger Summary</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th>Opening Outstanding</th>
                                                        <td class="text-right">₹{{ number_format($supplier->opening_outstanding, 2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Total Purchases (All Time)</th>
                                                        <td class="text-right">₹{{ number_format($supplier->getTotalPurchasesAttribute(), 2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Total Deposits (All Time)</th>
                                                        <td class="text-right text-success">₹{{ number_format($supplier->getTotalDepositsAttribute(), 2) }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-4">
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th>Current Outstanding</th>
                                                        <td class="text-right font-weight-bold {{ $supplier->current_outstanding > 0 ? 'text-danger' : 'text-success' }}">
                                                            ₹{{ number_format($supplier->current_outstanding, 2) }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Last Purchase Date</th>
                                                        <td class="text-right">
                                                            {{ $supplier->last_purchase_date ? date('d-m-Y', strtotime($supplier->last_purchase_date)) : 'Never' }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Last Deposit Date</th>
                                                        <td class="text-right">
                                                            {{ $supplier->last_deposit_date ? date('d-m-Y', strtotime($supplier->last_deposit_date)) : 'Never' }}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-4">
                                                <table class="table table-bordered">
                                                    <tr class="table-active">
                                                        <th>Opening Balance (Period)</th>
                                                        <td class="text-right">₹{{ number_format($openingBalance, 2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>+ Total Purchases (Period)</th>
                                                        <td class="text-right">₹{{ number_format($totalPurchases, 2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>- Total Deposits (Period)</th>
                                                        <td class="text-right text-success">₹{{ number_format($totalDeposits, 2) }}</td>
                                                    </tr>
                                                    <tr class="table-primary">
                                                        <th>Closing Balance (Period)</th>
                                                        <td class="text-right font-weight-bold {{ $closingBalance > 0 ? 'text-danger' : 'text-success' }}">
                                                            ₹{{ number_format($closingBalance, 2) }}
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <small class="text-muted">
                                    <i class="fa fa-info-circle"></i> 
                                    Ledger period: {{ date('d M Y', strtotime($startDate)) }} to {{ date('d M Y', strtotime($endDate)) }}
                                    | Generated on: {{ date('d-m-Y H:i:s') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
@include('includes.script')

<script>
    $(document).ready(function() {
        // Auto-focus amount field
        $('input[name="amount"]').focus();
        
        // Validate deposit amount
        $('form').submit(function(e) {
            const amount = parseFloat($('input[name="amount"]').val()) || 0;
            if (amount <= 0) {
                alert('Please enter a valid amount greater than 0');
                e.preventDefault();
                return false;
            }
        });
    });
    
    // Keyboard shortcut for adding deposit (Ctrl + D)
    $(document).keydown(function(e) {
        if (e.ctrlKey && e.keyCode === 68) { // Ctrl + D
            e.preventDefault();
            $('input[name="amount"]').focus();
        }
    });
</script>

<style>
    @media print {
        .card-footer, .breadcrumb, .page-header, .loader-bg, 
        .date-filter, .btn, .action-column, .section-title .badge {
            display: none !important;
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        .card-header {
            background-color: #fff !important;
            color: #000 !important;
            border-bottom: 2px solid #000 !important;
        }
        
        .table-bordered th, .table-bordered td {
            border: 1px solid #000 !important;
        }
        
        .summary-card {
            border: 1px solid #000 !important;
            margin-bottom: 10px !important;
        }
        
        h5, h6, h3 {
            color: #000 !important;
        }
        
        .text-success { color: #000 !important; font-weight: bold; }
        .text-danger { color: #000 !important; font-weight: bold; }
        .text-muted { color: #666 !important; }
    }
</style>

</body>
</html>