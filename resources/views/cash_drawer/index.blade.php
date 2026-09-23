<!DOCTYPE html>
<html lang="en">
<head>
  <title>Cash Drawer Management | {{ auth()->user()->restaurant->name ?? 'Restaurant POS' }}</title>
  @include('includes.style')
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .stat-card {
      border-radius: 14px;
      padding: 22px;
      color: #fff;
      position: relative;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0,0,0,0.06);
      transition: transform 0.25s ease, box-shadow 0.25s ease;
      height: 100%;
    }
    .stat-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }
    .stat-card .stat-icon {
      position: absolute;
      right: 18px;
      bottom: 12px;
      font-size: 3.2rem;
      opacity: 0.18;
    }
    .stat-card .stat-title {
      font-size: 0.82rem;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      font-weight: 600;
      opacity: 0.9;
      margin-bottom: 6px;
    }
    .stat-card .stat-value {
      font-size: 1.85rem;
      font-weight: 800;
      line-height: 1.2;
    }
    .stat-card .stat-subtitle {
      font-size: 0.78rem;
      opacity: 0.85;
      margin-top: 6px;
    }

    .bg-gradient-drawer {
      background: linear-gradient(135deg, #0d9488 0%, #059669 100%);
    }
    .bg-gradient-opening {
      background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    }
    .bg-gradient-in {
      background: linear-gradient(135deg, #10b981 0%, #047857 100%);
    }
    .bg-gradient-out {
      background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .card-filter {
      background: #ffffff;
      border-radius: 12px;
      border: 1px solid #e2e8f0;
      padding: 18px 22px;
      margin-bottom: 24px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.03);
    }
    .quick-date-btn {
      padding: 4px 12px;
      font-size: 0.8rem;
      border-radius: 20px;
      font-weight: 600;
      border: 1px solid #cbd5e1;
      background: #f8fafc;
      color: #475569;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    .quick-date-btn:hover, .quick-date-btn.active {
      background: #ff6a00;
      color: #fff;
      border-color: #ff6a00;
    }

    .ledger-table thead th {
      background-color: #f1f5f9;
      color: #334155;
      font-weight: 700;
      font-size: 0.85rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border-top: none;
      padding: 14px 16px;
    }
    .ledger-table tbody td {
      padding: 14px 16px;
      vertical-align: middle;
      font-size: 0.92rem;
    }
    .amount-credit {
      color: #059669;
      font-weight: 700;
    }
    .amount-debit {
      color: #dc2626;
      font-weight: 700;
    }
    .amount-balance {
      color: #0f172a;
      font-weight: 800;
    }

    .badge-type {
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 0.76rem;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      letter-spacing: 0.3px;
    }

    .btn-action-cashin {
      background: linear-gradient(135deg, #10b981, #059669);
      color: #fff;
      font-weight: 600;
      border: none;
      border-radius: 8px;
      padding: 9px 18px;
      transition: all 0.2s;
    }
    .btn-action-cashin:hover {
      background: linear-gradient(135deg, #059669, #047857);
      color: #fff;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(16,185,129,0.35);
    }

    .btn-action-cashout {
      background: linear-gradient(135deg, #f59e0b, #d97706);
      color: #fff;
      font-weight: 600;
      border: none;
      border-radius: 8px;
      padding: 9px 18px;
      transition: all 0.2s;
    }
    .btn-action-cashout:hover {
      background: linear-gradient(135deg, #d97706, #b45309);
      color: #fff;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(245,158,11,0.35);
    }

    .empty-drawer-banner {
      background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
      border-left: 6px solid #f59e0b;
      border-radius: 12px;
      padding: 24px 28px;
      margin-bottom: 25px;
      color: #92400e;
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

      <!-- Breadcrumb & Header -->
      <div class="page-header mb-4">
        <div class="page-block">
          <div class="row align-items-center">
            <div class="col-md-6">
              <div class="page-header-title">
                <h4 class="m-b-10 font-weight-bold"><i class="fas fa-cash-register text-primary me-2"></i>Cash Drawer Management</h4>
              </div>
              <ul class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item" aria-current="page">Cash Drawer</li>
              </ul>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
              <div class="d-flex flex-wrap justify-content-md-end gap-2">
                @if(!$ledgerData['has_opening_cash'])
                  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#openingCashModal">
                    <i class="fas fa-key me-1"></i> Set Initial Opening Cash
                  </button>
                @else
                  <button type="button" class="btn btn-action-cashin" data-bs-toggle="modal" data-bs-target="#cashInModal">
                    <i class="fas fa-plus-circle me-1"></i> Add Cash (In)
                  </button>
                  <button type="button" class="btn btn-action-cashout" data-bs-toggle="modal" data-bs-target="#cashOutModal">
                    <i class="fas fa-minus-circle me-1"></i> Spend Cash (Out)
                  </button>
                  <a href="{{ route('cash.drawer.export', request()->query()) }}" class="btn btn-outline-success">
                    <i class="fas fa-file-excel me-1"></i> Export Excel
                  </a>
                  <button type="button" class="btn btn-outline-dark" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Print
                  </button>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Alerts -->
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

      @if(!$ledgerData['has_opening_cash'])
        <div class="empty-drawer-banner d-flex align-items-center justify-content-between flex-wrap gap-3">
          <div class="d-flex align-items-center gap-3">
            <div style="font-size: 2.5rem;"><i class="fas fa-coins text-warning"></i></div>
            <div>
              <h5 class="fw-bold mb-1" style="color: #92400e;">Initial Opening Cash Required</h5>
              <p class="mb-0 text-muted" style="color: #78350f !important;">
                Please set up your starting cash balance in the drawer. You only need to do this once. All subsequent additions, orders, supplier payouts, and cash spendings will automatically calculate from this starting point.
              </p>
            </div>
          </div>
          <div>
            <button type="button" class="btn btn-warning text-dark fw-bold px-4 py-2" data-bs-toggle="modal" data-bs-target="#openingCashModal">
              <i class="fas fa-arrow-right me-1"></i> Enter Opening Cash
            </button>
          </div>
        </div>
      @endif

      <!-- Stat Cards -->
      <div class="row g-3 mb-4">
        <!-- Live Drawer Balance -->
        <div class="col-xl-3 col-md-6">
          <div class="stat-card bg-gradient-drawer">
            <div class="stat-title">Cash in Drawer (Live)</div>
            <div class="stat-value">₹{{ number_format($ledgerData['live_balance'], 2) }}</div>
            <div class="stat-subtitle">Current Realtime Net Balance</div>
            <i class="fas fa-cash-register stat-icon"></i>
          </div>
        </div>

        <!-- Initial Opening Cash -->
        <div class="col-xl-3 col-md-6">
          <div class="stat-card bg-gradient-opening">
            <div class="stat-title">Initial Opening Cash</div>
            <div class="stat-value">
              ₹{{ number_format($ledgerData['opening_record']->amount ?? 0, 2) }}
            </div>
            <div class="stat-subtitle">
              {{ $ledgerData['opening_record'] ? 'Set on ' . \Carbon\Carbon::parse($ledgerData['opening_record']->entry_date)->format('d M Y') : 'Not Configured Yet' }}
            </div>
            <i class="fas fa-door-open stat-icon"></i>
          </div>
        </div>

        <!-- Period Cash In -->
        <div class="col-xl-3 col-md-6">
          <div class="stat-card bg-gradient-in">
            <div class="stat-title">Period Cash In (Credit)</div>
            <div class="stat-value">₹{{ number_format($ledgerData['total_period_credit'], 2) }}</div>
            <div class="stat-subtitle">Orders & Cash Additions</div>
            <i class="fas fa-arrow-circle-down stat-icon"></i>
          </div>
        </div>

        <!-- Period Cash Out -->
        <div class="col-xl-3 col-md-6">
          <div class="stat-card bg-gradient-out">
            <div class="stat-title">Period Cash Out (Debit)</div>
            <div class="stat-value">₹{{ number_format($ledgerData['total_period_debit'], 2) }}</div>
            <div class="stat-subtitle">Supplier & Expense Payouts</div>
            <i class="fas fa-arrow-circle-up stat-icon"></i>
          </div>
        </div>
      </div>

      <!-- Filter Card -->
      <div class="card-filter">
        <form id="filterForm" method="GET" action="{{ route('cash.drawer.index') }}" class="row g-3 align-items-end">
          <div class="col-md-3 col-sm-6">
            <label class="form-label fw-bold text-muted small mb-1">Start Date</label>
            <input type="date" name="start_date" id="startDate" class="form-control" value="{{ $startDate }}" required>
          </div>
          <div class="col-md-3 col-sm-6">
            <label class="form-label fw-bold text-muted small mb-1">End Date</label>
            <input type="date" name="end_date" id="endDate" class="form-control" value="{{ $endDate }}" required>
          </div>
          <div class="col-md-3 col-sm-6">
            <label class="form-label fw-bold text-muted small mb-1">Transaction Type</label>
            <select name="type" class="form-select">
              @foreach($types as $k => $v)
                <option value="{{ $k }}" {{ $filterType === $k ? 'selected' : '' }}>{{ $v }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3 col-sm-6">
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary flex-grow-1">
                <i class="fas fa-filter me-1"></i> Filter
              </button>
              <a href="{{ route('cash.drawer.index') }}" class="btn btn-light border" title="Reset Filters">
                <i class="fas fa-undo"></i>
              </a>
            </div>
          </div>
        </form>

        <div class="d-flex flex-wrap align-items-center gap-2 mt-3 pt-2 border-top">
          <span class="text-muted small fw-bold me-1">Quick Select:</span>
          <button type="button" class="quick-date-btn" onclick="setQuickDate('today')">Today</button>
          <button type="button" class="quick-date-btn" onclick="setQuickDate('yesterday')">Yesterday</button>
          <button type="button" class="quick-date-btn" onclick="setQuickDate('this_week')">This Week</button>
          <button type="button" class="quick-date-btn" onclick="setQuickDate('this_month')">This Month</button>
          <button type="button" class="quick-date-btn" onclick="setQuickDate('last_month')">Last Month</button>
        </div>
      </div>

      <!-- Ledger Table Card -->
      <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
          <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-list-alt text-primary me-2"></i>Cash Drawer Ledger</h5>
            <small class="text-muted">
              Showing records from <strong>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</strong> to <strong>{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</strong>
              (Opening Balance: <strong>₹{{ number_format($ledgerData['opening_balance_period'], 2) }}</strong>)
            </small>
          </div>
          <div>
            <span class="badge bg-light text-dark border px-3 py-2 fs-6">
              Closing Period Balance: <strong class="text-primary ms-1">₹{{ number_format($ledgerData['closing_balance_period'], 2) }}</strong>
            </span>
          </div>
        </div>

        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 ledger-table" id="cashLedgerTable">
              <thead>
                <tr>
                  <th style="width: 50px;">#</th>
                  <th>Date & Time</th>
                  <th>Type</th>
                  <th>Remarks / Description</th>
                  <th class="text-end">Debit (₹ Out)</th>
                  <th class="text-end">Credit (₹ In)</th>
                  <th>Recorded By</th>
                  <th class="text-center" style="width: 80px;">Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($ledgerData['transactions'] as $index => $txn)
                  @php
                    $badge = $txn->getTypeBadge();
                    $isDebit = $txn->entry_type === \App\Models\CashDrawerTransaction::ENTRY_DEBIT;
                    $isCredit = $txn->entry_type === \App\Models\CashDrawerTransaction::ENTRY_CREDIT;
                  @endphp
                  <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                      <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($txn->entry_date)->format('d M Y') }}</div>
                      <small class="text-muted">{{ $txn->entry_time ? \Carbon\Carbon::parse($txn->entry_time)->format('h:i A') : $txn->created_at->format('h:i A') }}</small>
                    </td>
                    <td>
                      <span class="badge-type {{ $badge['class'] }}">
                        <i class="{{ $badge['icon'] }}"></i> {{ $badge['label'] }}
                      </span>
                    </td>
                    <td>
                      <div class="fw-semibold text-dark">{{ $txn->remarks ?: '-' }}</div>
                      @if($txn->transaction_type === \App\Models\CashDrawerTransaction::TYPE_ORDER_PAYMENT && $txn->reference_id)
                        <small class="text-primary"><i class="fas fa-link me-1"></i>Order Payment Ref #{{ $txn->reference_id }}</small>
                      @elseif($txn->transaction_type === \App\Models\CashDrawerTransaction::TYPE_SUPPLIER_PAYMENT && $txn->reference_id)
                        <small class="text-danger"><i class="fas fa-link me-1"></i>Supplier Deposit Ref #{{ $txn->reference_id }}</small>
                      @elseif($txn->transaction_type === \App\Models\CashDrawerTransaction::TYPE_EXPENSE_PAYMENT && $txn->reference_id)
                        <small class="text-warning"><i class="fas fa-link me-1"></i>Expense Ref #{{ $txn->reference_id }}</small>
                      @endif
                    </td>
                    <td class="text-end amount-debit">
                      @if($isDebit)
                        - ₹{{ number_format($txn->amount, 2) }}
                      @else
                        -
                      @endif
                    </td>
                    <td class="text-end amount-credit">
                      @if($isCredit)
                        + ₹{{ number_format($txn->amount, 2) }}
                      @else
                        -
                      @endif
                    </td>
                    <td>
                      <span class="badge bg-light text-secondary border">
                        <i class="fas fa-user-circle me-1"></i>{{ $txn->user->name ?? 'System' }}
                      </span>
                    </td>
                    <td class="text-center">
                      @php
                        $isToday = \Carbon\Carbon::parse($txn->entry_date)->isToday();
                        $isManual = in_array($txn->transaction_type, [\App\Models\CashDrawerTransaction::TYPE_CASH_IN, \App\Models\CashDrawerTransaction::TYPE_CASH_OUT]);
                      @endphp
                      @if($isManual && $isToday)
                        <form method="POST" action="{{ route('cash.drawer.destroy', $txn->id) }}" onsubmit="return confirm('Are you sure you want to delete this cash transaction?');" class="d-inline">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Entry">
                            <i class="fas fa-trash-alt"></i>
                          </button>
                        </form>
                      @elseif($isManual && !$isToday)
                        <span class="badge bg-light text-muted border small" title="Past date transactions cannot be deleted">
                          <i class="fas fa-lock me-1"></i>Locked
                        </span>
                      @else
                        <span class="text-muted small" title="Auto-synced from orders or suppliers"><i class="fas fa-lock text-secondary"></i></span>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                      <div class="mb-3"><i class="fas fa-receipt fa-3x text-secondary opacity-50"></i></div>
                      <h6 class="fw-bold">No Cash Drawer Transactions Found</h6>
                      <p class="small mb-0">Transactions will appear here as you add cash, make sales, or payout expenses.</p>
                    </td>
                  </tr>
                @endforelse
              </tbody>
              <tfoot class="table-light">
                <tr class="fw-bold" style="background-color: #f8fafc; border-top: 2px solid #cbd5e1;">
                  <th colspan="4" class="text-end text-uppercase py-3" style="font-size: 0.88rem; letter-spacing: 0.5px;">
                    <i class="fas fa-calculator text-primary me-1"></i> Total (Period Sum):
                  </th>
                  <th class="text-end text-danger fs-6 py-3">
                    - ₹{{ number_format($ledgerData['total_period_debit'], 2) }}
                  </th>
                  <th class="text-end text-success fs-6 py-3">
                    + ₹{{ number_format($ledgerData['total_period_credit'], 2) }}
                  </th>
                  <th colspan="2"></th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Initial Opening Cash Modal -->
  <div class="modal fade" id="openingCashModal" tabindex="-1" aria-labelledby="openingCashModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg">
        <form method="POST" action="{{ route('cash.drawer.opening') }}">
          @csrf
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title text-white" id="openingCashModalLabel">
              <i class="fas fa-key me-2"></i> Set Initial Opening Cash
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <div class="alert alert-info py-2 small mb-3">
              <i class="fas fa-info-circle me-1"></i> This is configured <strong>only once</strong> for your restaurant to initialize the live cash drawer.
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Opening Cash Amount (₹) <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text fw-bold">₹</span>
                <input type="number" step="0.01" min="0" name="amount" class="form-control form-control-lg fw-bold" placeholder="0.00" required>
              </div>
              <small class="text-muted">Enter the physical cash balance available in the drawer right now.</small>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Date <span class="text-danger">*</span></label>
              <input type="date" name="entry_date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Remarks / Description</label>
              <textarea name="remarks" class="form-control" rows="2" placeholder="Initial Opening Cash Setup">Initial Opening Cash</textarea>
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary fw-bold px-4">
              <i class="fas fa-check-circle me-1"></i> Save Opening Cash
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Add Cash (In / Increment) Modal -->
  <div class="modal fade" id="cashInModal" tabindex="-1" aria-labelledby="cashInModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg">
        <form method="POST" action="{{ route('cash.drawer.cashin') }}">
          @csrf
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title text-white" id="cashInModalLabel">
              <i class="fas fa-plus-circle me-2"></i> Add Cash to Drawer (Cash In)
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <div class="mb-3">
              <label class="form-label fw-bold">Amount to Add (₹) <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text fw-bold bg-light-success text-success">₹</span>
                <input type="number" step="0.01" min="0.01" name="amount" class="form-control form-control-lg fw-bold text-success" placeholder="0.00" required>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Date <span class="text-danger">*</span></label>
              <input type="date" name="entry_date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Remarks / Reason <span class="text-danger">*</span></label>
              <textarea name="remarks" class="form-control" rows="2" placeholder="e.g., Added cash change float, Owner cash deposit, etc." required></textarea>
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success fw-bold px-4">
              <i class="fas fa-arrow-down me-1"></i> Add Cash
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Spend Cash (Out / Decrement) Modal -->
  <div class="modal fade" id="cashOutModal" tabindex="-1" aria-labelledby="cashOutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg">
        <form method="POST" action="{{ route('cash.drawer.cashout') }}">
          @csrf
          <div class="modal-header bg-warning text-dark">
            <h5 class="modal-title fw-bold" id="cashOutModalLabel">
              <i class="fas fa-minus-circle me-2"></i> Spend Cash from Drawer (Cash Out)
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <div class="alert alert-light border d-flex justify-content-between align-items-center py-2 mb-3">
              <span class="small text-muted">Current Live Balance:</span>
              <strong class="text-dark fs-6">₹{{ number_format($ledgerData['live_balance'], 2) }}</strong>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Amount to Spend / Withdraw (₹) <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text fw-bold bg-light-warning text-warning">₹</span>
                <input type="number" step="0.01" min="0.01" name="amount" class="form-control form-control-lg fw-bold text-danger" placeholder="0.00" required>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Date <span class="text-danger">*</span></label>
              <input type="date" name="entry_date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Remarks / Purpose <span class="text-danger">*</span></label>
              <textarea name="remarks" class="form-control" rows="2" placeholder="e.g., Tea & snacks, Cleaning supplies, Cash drop to bank, etc." required></textarea>
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-warning fw-bold px-4 text-dark">
              <i class="fas fa-arrow-up me-1"></i> Record Spend
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  @include('includes.script')
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script>
    $(document).ready(function() {
      // Auto open opening cash modal if not configured
      @if(!$ledgerData['has_opening_cash'])
        setTimeout(function() {
          var openingModal = new bootstrap.Modal(document.getElementById('openingCashModal'));
          openingModal.show();
        }, 500);
      @endif

      // Initialize DataTable if rows exist
      if ($('#cashLedgerTable tbody tr td').length > 1) {
        $('#cashLedgerTable').DataTable({
          paging: true,
          pageLength: 25,
          ordering: false, // Maintain computed running balance sequence
          info: true,
          searching: true,
          language: {
            search: "_INPUT_",
            searchPlaceholder: "Search ledger..."
          }
        });
      }
    });

    function setQuickDate(period) {
      const today = new Date();
      let start = new Date();
      let end = new Date();

      if (period === 'today') {
        start = today;
        end = today;
      } else if (period === 'yesterday') {
        start.setDate(today.getDate() - 1);
        end.setDate(today.getDate() - 1);
      } else if (period === 'this_week') {
        const day = today.getDay();
        const diff = today.getDate() - day + (day === 0 ? -6 : 1);
        start = new Date(today.setDate(diff));
        end = new Date();
      } else if (period === 'this_month') {
        start = new Date(today.getFullYear(), today.getMonth(), 1);
        end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
      } else if (period === 'last_month') {
        start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
        end = new Date(today.getFullYear(), today.getMonth(), 0);
      }

      function formatDate(d) {
        let month = '' + (d.getMonth() + 1);
        let day = '' + d.getDate();
        let year = d.getFullYear();
        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;
        return [year, month, day].join('-');
      }

      $('#startDate').val(formatDate(start));
      $('#endDate').val(formatDate(end));
      $('#filterForm').submit();
    }
  </script>
</body>
</html>
