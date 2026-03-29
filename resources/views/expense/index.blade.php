<!DOCTYPE html>
<html lang="en">
<head>
  <title>Admin - Expense Management</title>
  @include('includes.style')
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .filter-card {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 25px;
    }
    .summary-card {
      background: white;
      border-radius: 12px;
      padding: 15px;
      margin-bottom: 15px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      text-align: center;
      border-top: 4px solid;
      transition: transform 0.3s ease;
    }
    .summary-card:hover {
      transform: translateY(-5px);
    }
    .summary-value {
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 5px;
    }
    .summary-label {
      color: #64748b;
      font-size: 0.85rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .table-hover tbody tr:hover {
      background-color: #f8fafc;
    }
    .payment-badge {
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 0.85rem;
      font-weight: 500;
    }
    .badge-cash { background: #10b98120; color: #10b981; border: 1px solid #10b98130; }
    .badge-upi { background: #3b82f620; color: #3b82f6; border: 1px solid #3b82f630; }
    .badge-card { background: #8b5cf620; color: #8b5cf6; border: 1px solid #8b5cf630; }
    .badge-bank { background: #f59e0b20; color: #f59e0b; border: 1px solid #f59e0b30; }
    .badge-online { background: #ef444420; color: #ef4444; border: 1px solid #ef444430; }
    .action-buttons {
      display: flex;
      gap: 5px;
    }
    .amount-cell {
      font-weight: 600;
      color: #ef4444;
    }
    .empty-state {
      text-align: center;
      padding: 40px 20px;
      color: #64748b;
    }
    .empty-state i {
      font-size: 3rem;
      opacity: 0.5;
      margin-bottom: 15px;
    }
    .hidden {
      display: none !important;
    }
    #toastNotification {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 9999;
      min-width: 300px;
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
                <h5 class="m-b-10">Expense Management</h5>
              </div>
              <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item" aria-current="page">Expense Management</li>
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
              <h5>Expense Management</h5>
              <div class="float-end">
                <button id="exportExcel" class="btn btn-success btn-sm">
                  <i class="bi bi-file-excel"></i> Export Excel
                </button>
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addExpenseModal">
                  <i class="bi bi-plus-circle"></i> Add Expense
                </button>
              </div>
            </div>

            <div class="card-body">
              <!-- Filter Form -->
              <!-- Filter Form -->
<div class="filter-card">
  <form method="GET" action="{{ route('expense.index') }}" class="row g-3 align-items-end">
    <div class="col-md-3">
      <label class="form-label text-white">From Date</label>
      <input type="date" name="from_date" value="{{ request('from_date') ?? \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}" class="form-control">
    </div>
    <div class="col-md-3">
      <label class="form-label text-white">To Date</label>
      <input type="date" name="to_date" value="{{ request('to_date') ?? \Carbon\Carbon::now()->format('Y-m-d') }}" class="form-control">
    </div>
    <div class="col-md-3">
      <label class="form-label text-white">Search</label>
      <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by title or description...">
    </div>
    <div class="col-md-3">
      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-light w-100">
          <i class="bi bi-funnel"></i> Filter
        </button>
        <button type="button" id="resetFilter" class="btn btn-outline-light w-100">
          <i class="bi bi-arrow-clockwise"></i> Reset
        </button>
      </div>
    </div>
  </form>
</div>

              <!-- Summary Stats -->
              <div class="row mb-4">
                <div class="col-md-4">
                  <div class="summary-card" style="border-top-color: #ef4444;">
                    <div class="summary-value text-danger">₹{{ number_format($summary['total_expenses'], 2) }}</div>
                    <div class="summary-label">Total Expenses</div>
                    <div class="mt-2">
                      <small class="text-muted">{{ $summary['total_count'] }} Records</small>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="summary-card" style="border-top-color: #f59e0b;">
                    <div class="summary-value text-warning">₹{{ number_format($summary['average_expense'], 2) }}</div>
                    <div class="summary-label">Average Expense</div>
                    <div class="mt-2">
                      <small class="text-muted">Per record</small>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="summary-card" style="border-top-color: #3b82f6;">
                    <div class="summary-value text-primary">{{ $summary['total_count'] }}</div>
                    <div class="summary-label">Total Records</div>
                    <div class="mt-2">
                      <small class="text-muted">{{ $summary['date_range'] }}</small>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Expenses Table -->
              <div class="table-responsive">
                <table id="expensesTable" class="table table-hover table-striped">
                  <thead class="thead-light">
                    <tr>
                      <th>#</th>
                      <th>Title</th>
                      <th>Amount</th>
                      <th>Description</th>
                      <th>Date</th>
                      <th>Payment Method</th>
                      <th>Created By</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($expenses as $key => $expense)
                      <tr data-expense-id="{{ $expense->id }}">
                        <td>{{ ($expenses->currentPage() - 1) * $expenses->perPage() + $key + 1 }}</td>
                        <td>
                          <strong>{{ $expense->title }}</strong>
                        </td>
                        <td class="amount-cell">₹{{ number_format($expense->amount, 2) }}</td>
                        <td>
                          @if($expense->description)
                            <div class="text-truncate" style="max-width: 250px;" title="{{ $expense->description }}">
                              {{ $expense->description }}
                            </div>
                          @else
                            <span class="text-muted">No description</span>
                          @endif
                        </td>
                        <td>
                          {{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}
                        </td>
                        <td>
                          @if($expense->payment_method)
                            @php
                              $methodClass = 'badge-cash';
                              if (str_contains(strtolower($expense->payment_method), 'upi')) $methodClass = 'badge-upi';
                              elseif (str_contains(strtolower($expense->payment_method), 'card')) $methodClass = 'badge-card';
                              elseif (str_contains(strtolower($expense->payment_method), 'bank')) $methodClass = 'badge-bank';
                              elseif (str_contains(strtolower($expense->payment_method), 'online')) $methodClass = 'badge-online';
                            @endphp
                            <span class="payment-badge {{ $methodClass }}">
                              {{ $expense->payment_method }}
                            </span>
                          @else
                            <span class="text-muted">Not specified</span>
                          @endif
                        </td>
                        <td>
                          {{ $expense->user->name ?? 'System' }}
                          <br>
                          <small class="text-muted">{{ $expense->created_at->format('d M, h:i A') }}</small>
                        </td>
                        <td>
                          <div class="action-buttons">
                            <button class="btn btn-sm btn-outline-primary edit-expense" 
                                    data-id="{{ $expense->id }}"
                                    title="Edit">
                              <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-expense" 
                                    data-id="{{ $expense->id }}"
                                    title="Delete">
                              <i class="bi bi-trash"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                    @empty
                      
                    @endforelse
                  </tbody>
                </table>
              </div>

              <!-- Pagination -->
              @if($expenses->hasPages())
                <div class="mt-4">
                  {{ $expenses->appends(request()->query())->links() }}
                </div>
              @endif

              <!-- Export Summary -->
              <div class="row mt-4">
                <div class="col-md-12">
                  <div class="alert alert-info">
                    <div class="row">
                      <div class="col-md-4">
                        <strong>Date Range:</strong><br>
                        {{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} to {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}
                      </div>
                      <div class="col-md-4">
                        <strong>Showing:</strong><br>
                        {{ $expenses->firstItem() ?? 0 }}-{{ $expenses->lastItem() ?? 0 }} of {{ $expenses->total() }} records
                      </div>
                      <div class="col-md-4 text-end">
                        <button id="exportSummary" class="btn btn-outline-success">
                          <i class="bi bi-download"></i> Export Summary
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Add Expense Modal -->
  <div class="modal fade" id="addExpenseModal" tabindex="-1" aria-labelledby="addExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add New Expense</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span>&times;</span>
          </button>
        </div>
        <form id="addExpenseForm">
          @csrf
          <div class="modal-body">
            <div class="form-group">
              <label>Title *</label>
              <input type="text" name="title" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Amount *</label>
              <input type="number" name="amount" class="form-control" step="0.01" min="0" required>
            </div>
            <div class="form-group">
              <label>Description</label>
              <textarea name="description" class="form-control" rows="3" placeholder="Optional description..."></textarea>
            </div>
            <div class="form-group">
              <label>Expense Date *</label>
              <input type="date" name="expense_date" class="form-control" 
                     value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
            </div>
            <div class="form-group">
              <label>Payment Method</label>
              <select name="payment_method" class="form-control">
                <option value="">-- Select Method --</option>
                @foreach($paymentMethods as $method)
                  <option value="{{ $method }}">{{ $method }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-save"></i> Save Expense
            </button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Expense Modal -->
  <div class="modal fade" id="editExpenseModal" tabindex="-1" aria-labelledby="editExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Expense</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span>&times;</span>
          </button>
        </div>
        <form id="editExpenseForm">
          @csrf
          @method('PUT')
          <input type="hidden" name="id" id="edit_expense_id">
          <div class="modal-body">
            <div class="form-group">
              <label>Title *</label>
              <input type="text" name="title" id="edit_title" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Amount *</label>
              <input type="number" name="amount" id="edit_amount" class="form-control" step="0.01" min="0" required>
            </div>
            <div class="form-group">
              <label>Description</label>
              <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
              <label>Expense Date *</label>
              <input type="date" name="expense_date" id="edit_expense_date" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Payment Method</label>
              <select name="payment_method" id="edit_payment_method" class="form-control">
                <option value="">-- Select Method --</option>
                @foreach($paymentMethods as $method)
                  <option value="{{ $method }}">{{ $method }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-save"></i> Update Expense
            </button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Toast Notification -->
  <div id="toastNotification" class="hidden"></div>

  <!-- JS Libraries -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
  @include('includes.script')

  <script>
$(document).ready(function() {
  // Initialize DataTable
  const table = $('#expensesTable').DataTable({
    "paging": false,
    "searching": false,
    "ordering": true,
    "info": false,
    "dom": 'Bfrtip',
    "buttons": [
      {
        extend: 'excel',
        text: '<i class="bi bi-file-excel"></i> Export Excel',
        className: 'btn btn-success btn-sm',
        title: 'Expense Management Report - {{ \Carbon\Carbon::now()->format("d M Y") }}',
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6]
        }
      },
      {
        extend: 'print',
        text: '<i class="bi bi-printer"></i> Print',
        className: 'btn btn-primary btn-sm',
        title: '<h3>Expense Management Report</h3>' + 
               '<p>Date Range: {{ \Carbon\Carbon::parse($fromDate)->format("d M Y") }} to {{ \Carbon\Carbon::parse($toDate)->format("d M Y") }}</p>',
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6]
        }
      }
    ]
  });

  // Toast notification function
  function showToast(message, type = 'success') {
    const toast = $('#toastNotification');
    toast.removeClass().addClass('alert alert-dismissible fade show');
    toast.addClass(type === 'success' ? 'alert-success' : 'alert-danger');
    toast.html(`
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `);
    toast.removeClass('hidden');
    
    setTimeout(() => {
      toast.addClass('hidden');
    }, 3000);
  }

  // Add expense form submission
  $('#addExpenseForm').submit(function(e) {
    e.preventDefault();
    
    const formData = $(this).serialize();
    const submitBtn = $(this).find('button[type="submit"]');
    
    submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Saving...');
    
    $.ajax({
      url: "{{ route('expense.store') }}",
      method: 'POST',
      data: formData,
      success: function(response) {
        if (response.success) {
          showToast('Expense added successfully!', 'success');
          $('#addExpenseModal').modal('hide');
          $('#addExpenseForm')[0].reset();
          setTimeout(() => location.reload(), 1000);
        }
      },
      error: function(xhr) {
        showToast(xhr.responseJSON?.message || 'Error adding expense', 'error');
      },
      complete: function() {
        submitBtn.prop('disabled', false).html('<i class="bi bi-save"></i> Save Expense');
      }
    });
  });

  // Edit expense - open modal
  $(document).on('click', '.edit-expense', function() {
    const expenseId = $(this).data('id');
    
    $.ajax({
      url: "{{ route('expense.show', ':id') }}".replace(':id', expenseId),
      method: 'GET',
      success: function(response) {
        if (response.success) {

          const expense = response.expense;
          console.log(expense);
          $('#edit_expense_id').val(expense.id);
          $('#edit_title').val(expense.title);
          $('#edit_amount').val(expense.amount);
          $('#edit_description').val(expense.description);
          $('#edit_expense_date').val(expense.expense_date);
          $('#edit_payment_method').val(expense.payment_method);
          $('#editExpenseModal').modal('show');
        }
      },
      error: function() {
        showToast('Error loading expense details', 'error');
      }
    });
  });

  // Edit expense form submission
  $('#editExpenseForm').submit(function(e) {
    e.preventDefault();
    
    const expenseId = $('#edit_expense_id').val();
    const formData = $(this).serialize();
    const submitBtn = $(this).find('button[type="submit"]');
    
    submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Updating...');
    
    $.ajax({
      url: "{{ route('expense.update', ':id') }}".replace(':id', expenseId),
      method: 'PUT',
      data: formData,
      success: function(response) {
        if (response.success) {
          showToast('Expense updated successfully!', 'success');
          $('#editExpenseModal').modal('hide');
          setTimeout(() => location.reload(), 1000);
        }
      },
      error: function(xhr) {
        showToast(xhr.responseJSON?.message || 'Error updating expense', 'error');
      },
      complete: function() {
        submitBtn.prop('disabled', false).html('<i class="bi bi-save"></i> Update Expense');
      }
    });
  });

  // Delete expense
  $(document).on('click', '.delete-expense', function() {
    if (!confirm('Are you sure you want to delete this expense? This action cannot be undone.')) {
      return;
    }
    
    const expenseId = $(this).data('id');
    const deleteBtn = $(this);
    
    deleteBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i>');
    
    $.ajax({
      url: "{{ route('expense.destroy', ':id') }}".replace(':id', expenseId),
      method: 'DELETE',
      success: function(response) {
        if (response.success) {
          showToast('Expense deleted successfully!', 'success');
          setTimeout(() => location.reload(), 1000);
        }
      },
      error: function(xhr) {
        showToast(xhr.responseJSON?.message || 'Error deleting expense', 'error');
        deleteBtn.prop('disabled', false).html('<i class="bi bi-trash"></i>');
      }
    });
  });

  // Export Excel button
  $('#exportExcel').click(function() {
    table.button('.buttons-excel').trigger();
  });

  // Print button
  $('#printReport').click(function() {
    table.button('.buttons-print').trigger();
  });

  // Export summary
  $('#exportSummary').click(function() {
    $.ajax({
      url: "{{ route('expense.export') }}",
      method: 'GET',
      data: {
        from_date: "{{ request('from_date') ?? \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}",
        to_date: "{{ request('to_date') ?? \Carbon\Carbon::now()->format('Y-m-d') }}"
      },
      success: function(data) {
        // Convert to CSV
        let csvContent = "data:text/csv;charset=utf-8,";
        data.forEach(function(rowArray) {
          let row = rowArray.join(",");
          csvContent += row + "\r\n";
        });
        
        // Download file
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "expenses_summary_{{ \Carbon\Carbon::now()->format('Y-m-d') }}.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        showToast('Summary exported successfully!', 'success');
      },
      error: function() {
        showToast('Error exporting summary', 'error');
      }
    });
  });
});  

// Reset filter button
$('#resetFilter').click(function() {
  // Reset the form
  $('.filter-card form')[0].reset();
  
  // Set default dates
  const today = new Date().toISOString().split('T')[0];
  const firstDayOfMonth = new Date();
  firstDayOfMonth.setDate(1);
  const firstDayStr = firstDayOfMonth.toISOString().split('T')[0];
  
  $('input[name="from_date"]').val(firstDayStr);
  $('input[name="to_date"]').val(today);
  $('input[name="search"]').val('');
  
  // Submit the form to reload with defaults
  $('.filter-card form').submit();
});

// Optional: Set default values on page load
$(document).ready(function() {
  // If no date is set in URL, set defaults
  if (!window.location.search.includes('from_date')) {
    const today = new Date().toISOString().split('T')[0];
    const firstDayOfMonth = new Date();
    firstDayOfMonth.setDate(1);
    const firstDayStr = firstDayOfMonth.toISOString().split('T')[0];
    
    $('input[name="from_date"]').val(firstDayStr);
    $('input[name="to_date"]').val(today);
  }
});


</script>


</body>
</html>