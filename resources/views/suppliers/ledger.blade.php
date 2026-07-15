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
    body {
      background-color: #f8fafc;
      font-family: 'Public Sans', 'Segoe UI', sans-serif;
      color: #1e293b;
    }

    .page-header {
      background: white;
      padding: 24px;
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(15, 23, 42, 0.02);
      margin-bottom: 25px;
      border: 1px solid rgba(0, 0, 0, 0.03);
    }

    .page-header h5 {
      color: #0f172a;
      font-weight: 800;
      font-size: 1.4rem;
      margin: 0;
    }

    .breadcrumb {
      background: transparent;
      padding: 0;
      margin: 10px 0 0 0;
    }

    .breadcrumb-item a {
      color: #009d1a;
      text-decoration: none;
      font-weight: 600;
    }

    .supplier-profile-card {
      background: white;
      border-radius: 16px;
      padding: 24px;
      box-shadow: 0 4px 20px rgba(15, 23, 42, 0.02);
      border: 1px solid rgba(0, 0, 0, 0.03);
      margin-bottom: 25px;
    }

    /* Analytical Summary Cards */
    .summary-card {
      border: none !important;
      border-radius: 16px !important;
      box-shadow: 0 4px 15px rgba(15, 23, 42, 0.02) !important;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      background: #ffffff;
      border: 1px solid rgba(0, 0, 0, 0.03) !important;
      height: 100%;
    }

    .summary-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      bottom: 0;
      width: 4px;
    }

    .opening-balance::before { background-color: #3b82f6; }
    .total-purchases::before { background-color: #f59e0b; }
    .total-deposits::before { background-color: #10b981; }
    .closing-balance::before { background-color: #ef4444; }

    .summary-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(15, 23, 42, 0.05) !important;
    }

    .summary-card h6 {
      font-size: 0.75rem;
      font-weight: 700;
      color: #64748b;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin-bottom: 12px;
    }

    .amount-badge {
      font-size: 1.45rem;
      font-weight: 800;
      color: #0f172a;
      margin-bottom: 6px;
    }

    .amount-badge.positive { color: #10b981; }
    .amount-badge.negative { color: #ef4444; }
    .amount-badge.neutral { color: #64748b; }

    .summary-card small {
      font-size: 0.75rem;
      color: #94a3b8;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 4px;
    }

    /* Date Filter Card */
    .date-filter {
      background: white;
      padding: 24px;
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(15, 23, 42, 0.02);
      border: 1px solid rgba(0, 0, 0, 0.03);
      margin-bottom: 25px;
    }

    .date-filter label {
      font-weight: 700;
      color: #475569;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin-bottom: 8px;
      display: inline-block;
    }

    .date-filter .form-control {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      padding: 10px 14px;
      font-size: 0.9rem;
      color: #0f172a;
      transition: all 0.2s ease;
    }

    .date-filter .form-control:focus {
      background: white;
      border-color: #009d1a;
      box-shadow: 0 0 0 3px rgba(0, 157, 26, 0.1);
      outline: none;
    }

    .btn-action-primary {
      background: linear-gradient(135deg, #009d1a 0%, #00bc20 100%) !important;
      color: white !important;
      border: none !important;
      border-radius: 30px !important;
      padding: 10px 24px !important;
      font-weight: 700 !important;
      box-shadow: 0 4px 12px rgba(0, 157, 26, 0.15) !important;
      transition: all 0.3s ease !important;
    }

    .btn-action-primary:hover {
      background: linear-gradient(135deg, #00bc20 0%, #009d1a 100%) !important;
      transform: translateY(-1px) !important;
      box-shadow: 0 6px 18px rgba(0, 157, 26, 0.25) !important;
    }

    .btn-action-secondary {
      background: #e2e8f0 !important;
      color: #475569 !important;
      border: none !important;
      border-radius: 30px !important;
      padding: 10px 20px !important;
      font-weight: 600 !important;
      transition: all 0.25s ease !important;
    }

    .btn-action-secondary:hover {
      background: #cbd5e1 !important;
      color: #1e293b !important;
    }

    .btn-action-info {
      background: #0f172a !important;
      color: white !important;
      border: none !important;
      border-radius: 30px !important;
      padding: 10px 20px !important;
      font-weight: 600 !important;
      transition: all 0.25s ease !important;
    }

    .btn-action-info:hover {
      background: #1e293b !important;
      color: white !important;
    }

    /* Section Forms */
    .deposit-card {
      background: white;
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(15, 23, 42, 0.02);
      border: 1px solid rgba(0, 0, 0, 0.03);
      margin-bottom: 25px;
      overflow: hidden;
    }

    .deposit-card-header {
      background: #f8fafc;
      border-bottom: 1px solid rgba(0, 0, 0, 0.05);
      padding: 16px 24px;
    }

    .deposit-card-header h6 {
      font-weight: 800;
      color: #0f172a;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .deposit-card label {
      font-weight: 700;
      color: #475569;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin-bottom: 8px;
      display: inline-block;
    }

    .deposit-card .form-control {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      padding: 10px 14px;
      font-size: 0.9rem;
      color: #0f172a;
      transition: all 0.2s ease;
    }

    .deposit-card .form-control:focus {
      background: white;
      border-color: #009d1a;
      box-shadow: 0 0 0 3px rgba(0, 157, 26, 0.1);
      outline: none;
    }

    /* Table Styles */
    .section-card {
      background: white;
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(15, 23, 42, 0.02);
      border: 1px solid rgba(0, 0, 0, 0.03);
      margin-bottom: 25px;
      overflow: hidden;
    }

    .section-card-header {
      background: #f8fafc;
      border-bottom: 1px solid rgba(0, 0, 0, 0.05);
      padding: 16px 24px;
    }

    .section-title-custom {
      font-weight: 800;
      color: #0f172a;
      margin: 0;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .table-responsive {
      padding: 8px;
    }

    .table-custom {
      width: 100% !important;
      border-collapse: collapse !important;
    }

    .table-custom thead th {
      background: #0f172a;
      color: white;
      font-weight: 700;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      padding: 12px 14px;
      border: none;
    }

    .table-custom tbody td {
      padding: 12px 14px;
      vertical-align: middle;
      border-bottom: 1px solid #e2e8f0;
      font-size: 0.85rem;
      color: #334155;
    }

    .table-custom tbody tr:hover {
      background-color: #f8fafc;
    }

    .table-custom tfoot td {
      padding: 12px 14px;
      vertical-align: middle;
      border-top: 1px solid #cbd5e1;
      font-size: 0.9rem;
      color: #0f172a;
    }

    /* Badges */
    .payment-badge {
      padding: 4px 12px;
      border-radius: 30px;
      font-size: 0.75rem;
      font-weight: 700;
      display: inline-block;
    }

    .payment-cash { background-color: #d1fae5; color: #065f46; border: 1px solid rgba(16, 185, 129, 0.2); }
    .payment-upi { background-color: #f3e8ff; color: #6b21a8; border: 1px solid rgba(147, 51, 234, 0.2); }
    .payment-bank { background-color: #e0f2fe; color: #075985; border: 1px solid rgba(14, 165, 233, 0.2); }
    .payment-cheque { background-color: #fef3c7; color: #92400e; border: 1px solid rgba(245, 158, 11, 0.2); }
    .payment-other { background-color: #f1f5f9; color: #475569; border: 1px solid rgba(100, 116, 139, 0.2); }

    .btn-view-action {
      background: rgba(59, 130, 246, 0.1);
      color: #3b82f6;
      border: 1px solid rgba(59, 130, 246, 0.2);
      border-radius: 6px;
      width: 30px;
      height: 30px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s ease;
      text-decoration: none;
    }

    .btn-view-action:hover {
      background: #3b82f6;
      color: white;
    }

    .btn-delete-action {
      background: rgba(239, 68, 68, 0.1);
      color: #ef4444;
      border: 1px solid rgba(239, 68, 68, 0.2);
      border-radius: 6px;
      width: 30px;
      height: 30px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s ease;
      text-decoration: none;
      cursor: pointer;
    }

    .btn-delete-action:hover {
      background: #ef4444;
      color: white;
    }

    /* Print styling overrides */
    @media print {
      .card-footer, .breadcrumb, .page-header, .loader-bg, 
      .date-filter, .btn, .action-column, .section-title-custom span, .btn-action-primary, .btn-action-secondary, .btn-action-info {
        display: none !important;
      }
      .pc-container {
        top: 0 !important;
        margin-left: 0 !important;
      }
      .pc-content {
        padding: 0 !important;
      }
      .summary-card {
        border: 1px solid #cbd5e1 !important;
        box-shadow: none !important;
        margin-bottom: 10px !important;
      }
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

    <!-- Breadcrumb Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
      <div>
        <h5><i class="fas fa-book text-success me-2"></i> Supplier Ledger</h5>
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Suppliers</a></li>
          <li class="breadcrumb-item active" aria-current="page">Ledger</li>
        </ul>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('suppliers.index') }}" class="btn btn-action-secondary">
          <i class="fa fa-arrow-left me-1"></i> Back to Suppliers
        </a>
        <a href="{{ route('purchases.create') }}?supplier_id={{ $supplier->id }}" class="btn btn-action-primary">
          <i class="fa fa-plus-circle me-1"></i> New Purchase
        </a>
      </div>
    </div>

    <!-- Supplier Identity Profile Card -->
    <div class="supplier-profile-card">
      <div class="row align-items-center">
        <div class="col-md-8">
          <h4 class="font-weight-bold mb-1 text-dark">
            {{ $supplier->supplier_name }}
            @if($supplier->shop_name)
              <span style="font-size: 0.95rem; color: #64748b; font-weight: 600;">({{ $supplier->shop_name }})</span>
            @endif
          </h4>
          <p class="text-secondary mb-0 font-weight-bold" style="font-size: 0.85rem;">
            <i class="fa fa-phone me-1 text-success"></i> {{ $supplier->phone }} 
            @if($supplier->email)
              | <i class="fa fa-envelope me-1 text-success"></i> {{ $supplier->email }}
            @endif
          </p>
        </div>
      </div>
    </div>

    <!-- Date Range Filter Card -->
    <div class="date-filter shadow-sm">
      <form method="GET" action="{{ route('suppliers.ledger', $supplier->id) }}" class="row align-items-end g-3">
        <div class="col-md-3">
          <label><i class="fa fa-calendar me-1 text-success"></i> From Date</label>
          <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" required>
        </div>
        <div class="col-md-3">
          <label><i class="fa fa-calendar me-1 text-success"></i> To Date</label>
          <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" required>
        </div>
        <div class="col-md-6 d-flex gap-2 mt-3">
          <button type="submit" class="btn btn-action-primary">
            <i class="fa fa-filter"></i> Filter
          </button>
          <a href="{{ route('suppliers.ledger', $supplier->id) }}" class="btn btn-action-secondary">
            <i class="fa fa-refresh"></i> Reset
          </a>
          <button type="button" onclick="window.print()" class="btn btn-action-info ms-auto">
            <i class="fa fa-print"></i> Print Ledger
          </button>
        </div>
      </form>
    </div>

    <!-- Analytical Summary Metrics -->
    <div class="row g-3 mb-4">
      <div class="col-md-3 col-sm-6">
        <div class="card summary-card opening-balance">
          <div class="card-body">
            <h6>Opening Outstanding</h6>
            <h3 class="amount-badge {{ $openingBalance > 0 ? 'negative' : 'neutral' }}">
              ₹{{ number_format($supplier->opening_outstanding, 2) }}
            </h3>
            <small><i class="fa fa-clock-o"></i> As of {{ date('d-m-Y', strtotime($startDate . ' -1 day')) }}</small>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="card summary-card total-purchases">
          <div class="card-body">
            <h6>Total Purchases</h6>
            <h3 class="amount-badge negative">
              ₹{{ number_format($totalPurchases, 2) }}
            </h3>
            <small><i class="fa fa-shopping-cart"></i> {{ $purchases->count() }} purchases in period</small>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="card summary-card total-deposits">
          <div class="card-body">
            <h6>Total Deposits</h6>
            <h3 class="amount-badge positive">
              ₹{{ number_format($totalDeposits, 2) }}
            </h3>
            <small><i class="fa fa-money"></i> {{ $deposits->count() }} deposits in period</small>
          </div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="card summary-card closing-balance">
          <div class="card-body">
            <h6>Closing Balance</h6>
            <h3 class="amount-badge {{ $closingBalance > 0 ? 'negative' : ($closingBalance < 0 ? 'positive' : 'neutral') }}">
              ₹{{ number_format($closingBalance, 2) }}
            </h3>
            <small><i class="fa fa-calculator"></i> As of {{ date('d-m-Y', strtotime($endDate)) }}</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Deposit Form Card -->
    <div class="deposit-card shadow-sm">
      <div class="deposit-card-header">
        <h6><i class="fa fa-plus-circle text-success"></i> Add New Deposit</h6>
      </div>
      <div class="card-body">
        <form action="{{ route('suppliers.deposit.store') }}" method="POST">
          @csrf
          <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">
          
          <div class="row g-3">
            <div class="col-md-3">
              <label>Deposit Date *</label>
              <input type="date" name="deposit_date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-3">
              <label>Amount (₹) *</label>
              <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required placeholder="0.00">
            </div>
            <div class="col-md-3">
              <label>Payment Mode *</label>
              <select name="payment_mode" class="form-control" required>
                <option value="">Select Mode</option>
                @foreach($paymentModes as $key => $mode)
                  <option value="{{ $key }}">{{ $mode }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label>Transaction No.</label>
              <input type="text" name="transaction_no" class="form-control" placeholder="Optional code">
            </div>
          </div>
          
          <div class="row g-3 mt-2">
            <div class="col-md-9">
              <label>Remarks</label>
              <input type="text" name="remarks" class="form-control" placeholder="Optional notes">
            </div>
            <div class="col-md-3 d-flex align-items-end">
              <button type="submit" class="btn btn-action-primary w-100" style="height: 42px;">
                <i class="fa fa-save"></i> Save Deposit
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Split Purchases & Deposits Section -->
    <div class="row g-3">
      
      <!-- Purchases Table Card -->
      <div class="col-md-6">
        <div class="section-card shadow-sm">
          <div class="section-card-header">
            <h5 class="section-title-custom">
              <span><i class="fa fa-shopping-cart text-success me-1"></i> Purchases</span>
              <span class="badge bg-dark rounded-pill">{{ $purchases->count() }}</span>
            </h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table-custom">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Invoice No</th>
                    <th class="text-right">Amount</th>
                    <th class="action-column">Action</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($purchases as $purchase)
                  <tr>
                    <td>{{ date('d-m-Y', strtotime($purchase->purchase_date)) }}</td>
                    <td class="font-weight-bold text-dark">{{ $purchase->invoice_no }}</td>
                    <td class="text-right font-weight-bold">
                      ₹{{ number_format($purchase->total_amount, 2) }}
                    </td>
                    <td class="action-column">
                      <a href="{{ route('purchases.show', $purchase->id) }}" class="btn-view-action" title="View details">
                        <i class="fa fa-eye"></i>
                      </a>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="4" class="text-center text-muted py-4">
                      No purchases recorded in this period.
                    </td>
                  </tr>
                  @endforelse
                </tbody>
                @if(!$purchases->isEmpty())
                <tfoot>
                  <tr>
                    <td colspan="2" class="text-right font-weight-bold">Total:</td>
                    <td class="text-right font-weight-bold text-dark">
                      ₹{{ number_format($totalPurchases, 2) }}
                    </td>
                    <td></td>
                  </tr>
                </tfoot>
                @endif
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Deposits Table Card -->
      <div class="col-md-6">
        <div class="section-card shadow-sm">
          <div class="section-card-header">
            <h5 class="section-title-custom">
              <span><i class="fa fa-money text-success me-1"></i> Deposits</span>
              <span class="badge bg-dark rounded-pill">{{ $deposits->count() }}</span>
            </h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table-custom">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Payment Mode</th>
                    <th>Remarks</th>
                    <th class="action-column">Action</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($deposits as $deposit)
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
                        <br><small class="text-muted" style="font-size: 0.7rem;">Txn: {{ $deposit->transaction_no }}</small>
                      @endif
                    </td>
                    <td>{{ $deposit->remarks ?? '-' }}</td>
                    <td class="action-column">
                      <a href="{{ route('suppliers.deposit.delete', $deposit->id) }}" 
                         class="btn-delete-action"
                         onclick="return confirm('Are you sure you want to delete this deposit?')"
                         title="Delete deposit">
                        <i class="fa fa-trash"></i>
                      </a>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                      No deposits recorded in this period.
                    </td>
                  </tr>
                  @endforelse
                </tbody>
                @if(!$deposits->isEmpty())
                <tfoot>
                  <tr>
                    <td class="text-right font-weight-bold">Total:</td>
                    <td class="text-right font-weight-bold text-success">
                      ₹{{ number_format($totalDeposits, 2) }}
                    </td>
                    <td colspan="3"></td>
                  </tr>
                </tfoot>
                @endif
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Overall Ledger Summary Cards section -->
    <div class="section-card shadow-sm mt-4">
      <div class="section-card-header bg-dark text-white" style="background: #0f172a !important;">
        <h6 class="mb-0 text-white"><i class="fa fa-calculator me-1"></i> Full Ledger Summary & Statistics</h6>
      </div>
      <div class="card-body p-4">
        <div class="row g-4">
          <div class="col-md-4">
            <h6 class="font-weight-bold text-secondary mb-3 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">All-Time Historical Summary</h6>
            <table class="table table-bordered mb-0">
              <tr>
                <th class="bg-light font-weight-bold" style="font-size: 0.85rem;">Opening Outstanding</th>
                <td class="text-right font-weight-bold">₹{{ number_format($supplier->opening_outstanding, 2) }}</td>
              </tr>
              <tr>
                <th class="bg-light" style="font-size: 0.85rem;">Total Purchases (All Time)</th>
                <td class="text-right">₹{{ number_format($supplier->getTotalPurchasesAttribute(), 2) }}</td>
              </tr>
              <tr>
                <th class="bg-light" style="font-size: 0.85rem;">Total Deposits (All Time)</th>
                <td class="text-right text-success font-weight-bold">₹{{ number_format($supplier->getTotalDepositsAttribute(), 2) }}</td>
              </tr>
            </table>
          </div>
          
          <div class="col-md-4">
            <h6 class="font-weight-bold text-secondary mb-3 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Current Balance & Info</h6>
            <table class="table table-bordered mb-0">
              <tr>
                <th class="bg-light font-weight-bold" style="font-size: 0.85rem;">Current Outstanding</th>
                <td class="text-right font-weight-bold {{ $supplier->current_outstanding > 0 ? 'text-danger' : 'text-success' }}">
                  ₹{{ number_format($supplier->current_outstanding, 2) }}
                </td>
              </tr>
              <tr>
                <th class="bg-light" style="font-size: 0.85rem;">Last Purchase Date</th>
                <td class="text-right">
                  {{ $supplier->last_purchase_date ? date('d-m-Y', strtotime($supplier->last_purchase_date)) : 'Never' }}
                </td>
              </tr>
              <tr>
                <th class="bg-light" style="font-size: 0.85rem;">Last Deposit Date</th>
                <td class="text-right">
                  {{ $supplier->last_deposit_date ? date('d-m-Y', strtotime($supplier->last_deposit_date)) : 'Never' }}
                </td>
              </tr>
            </table>
          </div>
          
          <div class="col-md-4">
            <h6 class="font-weight-bold text-secondary mb-3 text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Ledger Calculations (Selected Period)</h6>
            <table class="table table-bordered mb-0">
              <tr class="table-active">
                <th style="font-size: 0.85rem;">Opening Balance</th>
                <td class="text-right">₹{{ number_format($openingBalance, 2) }}</td>
              </tr>
              <tr>
                <th style="font-size: 0.85rem;">+ Total Purchases</th>
                <td class="text-right">₹{{ number_format($totalPurchases, 2) }}</td>
              </tr>
              <tr>
                <th style="font-size: 0.85rem;">- Total Deposits</th>
                <td class="text-right text-success">₹{{ number_format($totalDeposits, 2) }}</td>
              </tr>
              <tr class="table-primary" style="background: rgba(0, 157, 26, 0.05);">
                <th class="font-weight-bold text-dark" style="font-size: 0.85rem;">Closing Balance</th>
                <td class="text-right font-weight-bold {{ $closingBalance > 0 ? 'text-danger' : 'text-success' }}">
                  ₹{{ number_format($closingBalance, 2) }}
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>
      
      <div class="card-footer bg-light" style="padding: 12px 24px;">
        <div class="row align-items-center">
          <div class="col-md-12 text-md-end text-center">
            <small class="text-muted font-weight-bold" style="font-size: 0.75rem;">
              <i class="fa fa-info-circle me-1 text-success"></i> 
              Ledger period: {{ date('d M Y', strtotime($startDate)) }} to {{ date('d M Y', strtotime($endDate)) }}
              | Generated on: {{ date('d-m-Y H:i:s') }}
            </small>
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

    // Keyboard shortcut for adding deposit (Ctrl + D)
    $(document).keydown(function(e) {
      if (e.ctrlKey && e.keyCode === 68) { // Ctrl + D
        e.preventDefault();
        $('input[name="amount"]').focus();
      }
    });
  });
</script>

</body>
</html>