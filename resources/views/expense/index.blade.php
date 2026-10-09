<!DOCTYPE html>
<html lang="en">
<head>
  <title>Expense Management | {{ auth()->user()->restaurant->name ?? 'Restaurant POS' }}</title>
  @include('includes.style')
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .summary-card {
      background: white;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.05);
      border-top: 4px solid #ff6a00;
      transition: transform 0.25s ease;
      height: 100%;
    }
    .summary-card:hover {
      transform: translateY(-4px);
    }
    .summary-value {
      font-size: 1.75rem;
      font-weight: 800;
      margin-bottom: 4px;
    }
    .summary-label {
      color: #64748b;
      font-size: 0.82rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-weight: 600;
    }

    .card-filter {
      background: #ffffff;
      border-radius: 12px;
      border: 1px solid #e2e8f0;
      padding: 18px 22px;
      margin-bottom: 24px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.03);
    }

    .payment-badge {
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 0.78rem;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }
    .badge-cash { background: #10b98120; color: #10b981; border: 1px solid #10b98140; }
    .badge-upi { background: #3b82f620; color: #3b82f6; border: 1px solid #3b82f640; }
    .badge-card { background: #8b5cf620; color: #8b5cf6; border: 1px solid #8b5cf640; }
    .badge-bank_transfer { background: #f59e0b20; color: #f59e0b; border: 1px solid #f59e0b40; }
    .badge-cheque { background: #06b6d420; color: #0891b2; border: 1px solid #06b6d440; }
    .badge-other { background: #64748b20; color: #64748b; border: 1px solid #64748b40; }

    .amount-cell {
      font-weight: 700;
      color: #dc2626;
      font-size: 1rem;
    }

    .toast-popup {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 9999;
      min-width: 300px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
      border-radius: 8px;
      padding: 14px 18px;
      color: white;
      animation: slideIn 0.3s ease;
    }
    .toast-popup.success { background: #10b981; }
    .toast-popup.error { background: #ef4444; }

    @keyframes slideIn {
      from { transform: translateX(100%); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
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
                <h4 class="m-b-10 font-weight-bold"><i class="fas fa-wallet text-primary me-2"></i>Expense Management</h4>
              </div>
              <ul class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item" aria-current="page">Expense Management</li>
              </ul>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
              <div class="d-flex flex-wrap justify-content-md-end gap-2">
                <button class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                  <i class="fas fa-plus-circle me-1"></i> Add Expense
                </button>
                <a href="{{ route('expense.export', request()->query()) }}" class="btn btn-outline-success">
                  <i class="fas fa-file-excel me-1"></i> Export CSV
                </a>
                <button type="button" class="btn btn-outline-dark" onclick="window.print()">
                  <i class="fas fa-print me-1"></i> Print
                </button>
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

      <!-- Summary Stats -->
      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <div class="summary-card" style="border-top-color: #ef4444;">
            <div class="summary-label">Total Expenses</div>
            <div class="summary-value text-danger">₹{{ number_format($summary['total_expenses'], 2) }}</div>
            <small class="text-muted"><i class="fas fa-calendar-alt me-1"></i>{{ $summary['date_range'] }}</small>
          </div>
        </div>
        <div class="col-md-4">
          <div class="summary-card" style="border-top-color: #f59e0b;">
            <div class="summary-label">Average Expense</div>
            <div class="summary-value text-warning">₹{{ number_format($summary['average_expense'], 2) }}</div>
            <small class="text-muted"><i class="fas fa-chart-line me-1"></i>Per expense record</small>
          </div>
        </div>
        <div class="col-md-4">
          <div class="summary-card" style="border-top-color: #3b82f6;">
            <div class="summary-label">Total Records</div>
            <div class="summary-value text-primary">{{ $summary['total_count'] }}</div>
            <small class="text-muted"><i class="fas fa-list me-1"></i>Logged in period</small>
          </div>
        </div>
      </div>

      <!-- Filter Card -->
      <div class="card-filter">
        <form method="GET" action="{{ route('expense.index') }}" class="row g-3 align-items-end">
          <div class="col-md-3 col-sm-6">
            <label class="form-label fw-bold text-muted small mb-1">From Date</label>
            <input type="date" name="from_date" value="{{ request('from_date') ?? $fromDate->format('Y-m-d') }}" class="form-control" required>
          </div>
          <div class="col-md-3 col-sm-6">
            <label class="form-label fw-bold text-muted small mb-1">To Date</label>
            <input type="date" name="to_date" value="{{ request('to_date') ?? $toDate->format('Y-m-d') }}" class="form-control" required>
          </div>
          <div class="col-md-4 col-sm-8">
            <label class="form-label fw-bold text-muted small mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by remarks or description...">
          </div>
          <div class="col-md-2 col-sm-4">
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary flex-grow-1">
                <i class="fas fa-filter me-1"></i> Filter
              </button>
              <a href="{{ route('expense.index') }}" class="btn btn-light border" title="Reset">
                <i class="fas fa-undo"></i>
              </a>
            </div>
          </div>
        </form>
      </div>

      <!-- Expenses Table Card -->
      <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
          <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-receipt text-primary me-2"></i>Expense Log</h5>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table id="expensesTable" class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th style="width: 50px;">#</th>
                  <th>Date</th>
                  <th>Amount (₹)</th>
                  <th>Payment Mode</th>
                  <th>Remarks / Description</th>
                  <th>Added By</th>
                  <th class="text-center" style="width: 100px;">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($expenses as $key => $expense)
                  @php
                    $modeKey = strtoupper(str_replace(' ', '_', $expense->payment_method ?? 'OTHER'));
                  @endphp
                  <tr>
                    <td>{{ ($expenses->currentPage() - 1) * $expenses->perPage() + $key + 1 }}</td>
                    <td>
                      <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</div>
                      <small class="text-muted">{{ $expense->created_at ? $expense->created_at->format('h:i A') : '' }}</small>
                    </td>
                    <td class="amount-cell">₹{{ number_format($expense->amount, 2) }}</td>
                    <td>
                      <span class="payment-badge badge-{{ strtolower($modeKey) }}">
                        @if($modeKey === 'CASH')
                          <i class="fas fa-money-bill-wave"></i>
                        @elseif($modeKey === 'UPI')
                          <i class="fas fa-mobile-alt"></i>
                        @elseif($modeKey === 'CARD')
                          <i class="fas fa-credit-card"></i>
                        @elseif($modeKey === 'BANK_TRANSFER')
                          <i class="fas fa-university"></i>
                        @elseif($modeKey === 'CHEQUE')
                          <i class="fas fa-money-check"></i>
                        @else
                          <i class="fas fa-receipt"></i>
                        @endif
                        {{ $paymentMethods[$modeKey] ?? $expense->payment_method ?? 'Cash' }}
                      </span>
                    </td>
                    <td>
                      <div class="fw-semibold text-dark">{{ $expense->description ?: $expense->title ?: '-' }}</div>
                    </td>
                    <td>
                      <span class="badge bg-light text-secondary border">
                        <i class="fas fa-user-circle me-1"></i>{{ $expense->user->name ?? 'System' }}
                      </span>
                    </td>
                    <td class="text-center">
                      <div class="d-inline-flex gap-1">
                        <button type="button" class="btn btn-sm btn-outline-primary edit-expense-btn" 
                                data-id="{{ $expense->id }}"
                                data-date="{{ \Carbon\Carbon::parse($expense->expense_date)->format('Y-m-d') }}"
                                data-amount="{{ $expense->amount }}"
                                data-method="{{ $modeKey }}"
                                data-remarks="{{ $expense->description ?: $expense->title }}"
                                title="Edit Expense">
                          <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger delete-expense-btn"
                                data-id="{{ $expense->id }}"
                                data-amount="{{ $expense->amount }}"
                                title="Delete Expense">
                          <i class="fas fa-trash-alt"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                      <div class="mb-3"><i class="fas fa-wallet fa-3x text-secondary opacity-50"></i></div>
                      <h6 class="fw-bold">No Expenses Recorded</h6>
                      <p class="small mb-0">Add expenses to track your spending and automatic cash drawer deductions.</p>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          @if($expenses->hasPages())
            <div class="p-3 border-top d-flex justify-content-end">
              {{ $expenses->appends(request()->query())->links() }}
            </div>
          @endif
        </div>
      </div>

    </div>
  </div>

  <!-- Add Expense Modal -->
  <div class="modal fade" id="addExpenseModal" tabindex="-1" aria-labelledby="addExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg">
        <form id="addExpenseForm">
          @csrf
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title text-white" id="addExpenseModalLabel">
              <i class="fas fa-plus-circle me-2"></i> Add New Expense
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <div class="mb-3">
              <label class="form-label fw-bold">Date <span class="text-danger">*</span></label>
              <input type="date" name="expense_date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Amount (₹) <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text fw-bold">₹</span>
                <input type="number" name="amount" class="form-control form-control-lg fw-bold text-danger" step="0.01" min="0.01" placeholder="0.00" required>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Payment Mode <span class="text-danger">*</span></label>
              <select name="payment_method" id="addPaymentMode" class="form-select" required>
                @foreach($paymentMethods as $key => $label)
                  <option value="{{ $key }}" {{ $key === 'CASH' ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
              </select>
              <div id="cashNoticeAdd" class="alert alert-warning py-2 px-3 small mt-2 mb-0">
                <i class="fas fa-info-circle me-1"></i> <strong>Cash selected:</strong> This expense will automatically debit from the <strong>Cash Drawer</strong>.
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Remarks / Description <span class="text-danger">*</span></label>
              <textarea name="remarks" class="form-control" rows="3" placeholder="e.g., Daily vegetables, Tea & snacks, Cleaning supplies, Repair work..." required></textarea>
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary fw-bold px-4" id="saveExpenseBtn">
              <i class="fas fa-save me-1"></i> Save Expense
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Expense Modal -->
  <div class="modal fade" id="editExpenseModal" tabindex="-1" aria-labelledby="editExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg">
        <form id="editExpenseForm">
          @csrf
          @method('PUT')
          <input type="hidden" name="id" id="edit_expense_id">
          <div class="modal-header bg-dark text-white">
            <h5 class="modal-title text-white" id="editExpenseModalLabel">
              <i class="fas fa-edit me-2"></i> Edit Expense
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body p-4">
            <div class="mb-3">
              <label class="form-label fw-bold">Date <span class="text-danger">*</span></label>
              <input type="date" name="expense_date" id="edit_expense_date" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Amount (₹) <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text fw-bold">₹</span>
                <input type="number" name="amount" id="edit_amount" class="form-control form-control-lg fw-bold text-danger" step="0.01" min="0.01" required>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Payment Mode <span class="text-danger">*</span></label>
              <select name="payment_method" id="edit_payment_method" class="form-select" required>
                @foreach($paymentMethods as $key => $label)
                  <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
              </select>
              <div id="cashNoticeEdit" class="alert alert-warning py-2 px-3 small mt-2 mb-0">
                <i class="fas fa-info-circle me-1"></i> <strong>Cash selected:</strong> Cash drawer sync will be updated automatically.
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Remarks / Description <span class="text-danger">*</span></label>
              <textarea name="remarks" id="edit_remarks" class="form-control" rows="3" required></textarea>
            </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary fw-bold px-4" id="updateExpenseBtn">
              <i class="fas fa-save me-1"></i> Update Expense
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
    function showToast(message, isError = false) {
      const toast = $(`<div class="toast-popup ${isError ? 'error' : 'success'}">${message}</div>`);
      $('body').append(toast);
      setTimeout(() => {
        toast.fadeOut(300, function() { $(this).remove(); });
      }, 3500);
    }

    function hideModal(modalId) {
      const modalEl = document.getElementById(modalId);
      if (modalEl) {
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
          const inst = bootstrap.Modal.getInstance(modalEl);
          if (inst) inst.hide();
        }
        if (typeof $ !== 'undefined') {
          $(`#${modalId}`).modal('hide');
        }
      }
      $('.modal-backdrop').remove();
      $('body').removeClass('modal-open').css('overflow', '');
    }

    $(document).ready(function() {
      // Toggle cash notice based on dropdown
      $('#addPaymentMode').on('change', function() {
        if ($(this).val() === 'CASH') {
          $('#cashNoticeAdd').slideDown(200);
        } else {
          $('#cashNoticeAdd').slideUp(200);
        }
      });

      $('#edit_payment_method').on('change', function() {
        if ($(this).val() === 'CASH') {
          $('#cashNoticeEdit').slideDown(200);
        } else {
          $('#cashNoticeEdit').slideUp(200);
        }
      });

      // Submit Add Expense Form
      $('#addExpenseForm').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#saveExpenseBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

        $.ajax({
          url: "{{ route('expense.store') }}",
          type: "POST",
          data: $(this).serialize(),
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}'
          },
          success: function(response) {
            btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Expense');
            if (response.success) {
              hideModal('addExpenseModal');
              showToast(response.message, false);
              setTimeout(() => { location.reload(); }, 700);
            } else {
              showToast(response.message || 'Failed to add expense', true);
            }
          },
          error: function(xhr) {
            btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Expense');
            let errorMsg = 'Error saving expense.';
            if (xhr.responseJSON) {
              if (xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
              } else if (xhr.responseJSON.errors) {
                const errs = Object.values(xhr.responseJSON.errors).flat();
                errorMsg = errs.join('<br>');
              }
            }
            showToast(errorMsg, true);
          }
        });
      });

      // Open Edit Expense Modal
      $(document).on('click', '.edit-expense-btn', function() {
        const id = $(this).data('id');
        const date = $(this).data('date');
        const amount = $(this).data('amount');
        const method = $(this).data('method');
        const remarks = $(this).data('remarks');

        $('#edit_expense_id').val(id);
        $('#edit_expense_date').val(date);
        $('#edit_amount').val(amount);
        $('#edit_payment_method').val(method);
        $('#edit_remarks').val(remarks);

        if (method === 'CASH') {
          $('#cashNoticeEdit').show();
        } else {
          $('#cashNoticeEdit').hide();
        }

        const editModalEl = document.getElementById('editExpenseModal');
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
          const editModal = bootstrap.Modal.getOrCreateInstance(editModalEl);
          editModal.show();
        } else {
          $('#editExpenseModal').modal('show');
        }
      });

      // Submit Edit Expense Form
      $('#editExpenseForm').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit_expense_id').val();
        const btn = $('#updateExpenseBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Updating...');

        const updateUrl = "{{ route('expense.index') }}/" + id;

        $.ajax({
          url: updateUrl,
          type: "POST",
          data: $(this).serialize(),
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}'
          },
          success: function(response) {
            btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Update Expense');
            if (response.success) {
              hideModal('editExpenseModal');
              showToast(response.message, false);
              setTimeout(() => { location.reload(); }, 700);
            } else {
              showToast(response.message || 'Failed to update expense', true);
            }
          },
          error: function(xhr) {
            btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Update Expense');
            let errorMsg = 'Error updating expense.';
            if (xhr.responseJSON) {
              if (xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
              } else if (xhr.responseJSON.errors) {
                const errs = Object.values(xhr.responseJSON.errors).flat();
                errorMsg = errs.join('<br>');
              }
            }
            showToast(errorMsg, true);
          }
        });
      });

      // Delete Expense
      $(document).on('click', '.delete-expense-btn', function() {
        const id = $(this).data('id');
        const amount = $(this).data('amount');

        if (!confirm(`Are you sure you want to delete this expense of ₹${parseFloat(amount).toFixed(2)}?`)) {
          return;
        }

        const deleteUrl = "{{ route('expense.index') }}/" + id;

        $.ajax({
          url: deleteUrl,
          type: "POST",
          data: {
            _method: 'DELETE',
            _token: "{{ csrf_token() }}"
          },
          headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
          },
          success: function(response) {
            if (response.success) {
              showToast(response.message, false);
              setTimeout(() => { location.reload(); }, 700);
            } else {
              showToast(response.message || 'Failed to delete expense', true);
            }
          },
          error: function(xhr) {
            let errorMsg = 'Error deleting expense.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
              errorMsg = xhr.responseJSON.message;
            }
            showToast(errorMsg, true);
          }
        });
      });
    });
  </script>
</body>
</html>