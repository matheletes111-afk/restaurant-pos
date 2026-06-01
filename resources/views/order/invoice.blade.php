<!DOCTYPE html>
<html lang="en">
<head>
    <title>Invoice #{{ $order->id }} | {{ $order->customer_name }}</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        /* Keep all your existing styles */
        .invoice-container {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }
        
        .invoice-header {
            background: linear-gradient(135deg, #1e293b, #334155);
            color: white;
            padding: 30px;
        }
        
        .invoice-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .invoice-subtitle {
            color: rgba(255,255,255,0.7);
        }
        
        .order-info {
            background: #f8fafc;
            padding: 20px 30px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
        }
        
        .info-value {
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
            margin-top: 4px;
        }
        
        .payment-summary {
            background: #f0fdf4;
            border-left: 4px solid #10b981;
            padding: 20px;
            margin: 20px 30px;
            border-radius: 12px;
        }
        
        .payment-table {
            padding: 0 30px 20px;
        }
        
        .btn-add-payment {
            background: linear-gradient(135deg, #FF6A00, #FF8C42);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
        }
        
        .btn-add-payment:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255,106,0,0.3);
        }
        
        .btn-print {
            background: #475569;
            color: white;
        }
        
        .btn-back {
            background: #64748b;
            color: white;
        }
        
        .payment-method-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .method-cash { background: #10b98120; color: #10b981; }
        .method-upi { background: #3b82f620; color: #3b82f6; }
        .method-card { background: #8b5cf620; color: #8b5cf6; }
        .method-bank { background: #f59e0b20; color: #f59e0b; }
        .method-other { background: #64748b20; color: #64748b; }
        
        .total-paid {
            font-size: 1.8rem;
            font-weight: 800;
            color: #10b981;
        }
        
        .balance-due {
            font-size: 1.8rem;
            font-weight: 800;
            color: #ef4444;
        }
        
        .status-paid { background: #10b981; color: white; }
        .status-partial { background: #f59e0b; color: white; }
        .status-pending { background: #ef4444; color: white; }
        
        .hidden-iframe {
            position: absolute;
            width: 0;
            height: 0;
            border: 0;
            visibility: hidden;
        }
        
        .toast-success {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            z-index: 9999;
            animation: slideIn 0.3s ease;
        }
        
        .toast-error {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #ef4444;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            z-index: 9999;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1050;
            overflow-x: hidden;
            overflow-y: auto;
        }
        
        .modal.show {
            display: block;
        }
        
        .modal-dialog {
            position: relative;
            width: auto;
            margin: 1.75rem auto;
            max-width: 500px;
        }
        
        .modal-content {
            position: relative;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        
        .modal-header {
            padding: 16px 20px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .modal-footer {
            padding: 16px 20px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        .close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0;
            margin: 0;
        }
    </style>
</head>

<body>
@include('includes.sidebar')

<div class="pc-container">
    <div class="pc-content">
        
        <!-- Hidden Iframe for PDF -->
        
        
        <!-- Action Buttons -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="mb-0">Invoice #{{ $order->order_id ?? $order->id }}</h5>
                <small class="text-muted">{{ $order->created_at->format('d M Y, h:i A') }}</small>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-add-payment" id="showAddPaymentModal">
                    <i class="fas fa-plus"></i> Add Payment
                </button>
                <button class="btn btn-print" id="printInvoiceBtn">
                    <i class="fas fa-print"></i> Print Invoice
                </button>
                <a href="{{ route('order.management.dashboard') }}" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <!-- Invoice Container -->
        <div class="invoice-container" id="invoiceContent">
            <div class="invoice-header">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-subtitle">Order #{{ $order->order_id ?? $order->id }}</div>
            </div>
            
            <div class="order-info">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Customer Name</span>
                        <span class="info-value">{{ $order->customer_name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Customer Phone</span>
                        <span class="info-value">{{ $order->customer_phone ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Order Type</span>
                        <span class="info-value">{{ $order->order_type == 'DINE_IN' ? 'Dine In' : 'Takeaway' }}</span>
                    </div>
                    @if($order->table)
                    <div class="info-item">
                        <span class="info-label">Table</span>
                        <span class="info-value">{{ $order->table->name }}</span>
                    </div>
                    @endif
                    <div class="info-item">
                        <span class="info-label">Order Date</span>
                        <span class="info-value">{{ $order->created_at->format('d M Y, h:i A') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Bill Type</span>
                        <span class="info-value">
                            @if($order->is_gst_bill == 'YES')
                                <span class="badge badge-success">GST Bill ({{ $order->restaurant_gst_percentage ?? 0 }}%)</span>
                            @else
                                <span class="badge badge-secondary">Non-GST Bill</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="payment-summary">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="text-muted small">Total Bill Amount</div>
                            <div class="total-paid">₹{{ number_format($order->grand_total, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="text-muted small">Total Paid</div>
                            <div class="total-paid">₹<span id="totalPaidAmount">{{ number_format($totalPaid, 2) }}</span></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="text-muted small">Balance Due</div>
                            <div class="balance-due">₹<span id="balanceDueAmount">{{ number_format($balanceDue, 2) }}</span></div>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-3" id="paymentStatusBadge">
                    @if($balanceDue <= 0)
                        <span class="badge status-paid px-3 py-2">FULLY PAID</span>
                    @elseif($totalPaid > 0)
                        <span class="badge status-partial px-3 py-2">PARTIALLY PAID</span>
                    @else
                        <span class="badge status-pending px-3 py-2">PENDING</span>
                    @endif
                </div>
            </div>
            
            <div class="payment-table">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0"><i class="fas fa-history"></i> Payment History</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" id="paymentTable">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Amount (₹)</th>
                                <th>Payment Method</th>
                                <th>Transaction No</th>
                                <th>Remarks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="paymentTableBody">
                            @foreach($payments as $index => $payment)
                            <tr>
                                <td>{{ $index + 1 }}</div>
                                <td>{{ $payment->payment_date->format('d M Y, h:i A') }}</div>
                                <td><strong>₹{{ number_format($payment->amount, 2) }}</strong></div>
                                <td>
                                    <span class="payment-method-badge method-{{ strtolower(str_replace(' ', '_', $payment->payment_method)) }}">
                                        {{ $payment->payment_method }}
                                    </span>
                                 </div>
                                <td>{{ $payment->transaction_no ?? '-' }}</div>
                                <td>{{ $payment->remarks ?? '-' }}</div>
                                <td>
                                    <button class="btn btn-sm btn-danger delete-payment" 
                                            data-id="{{ $payment->id }}"
                                            data-amount="{{ $payment->amount }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                 </div>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="thead-light">
                            <tr>
                                <th colspan="2" class="text-end">Total:</th>
                                <th><strong id="totalPaidFooter">₹{{ number_format($totalPaid, 2) }}</strong></th>
                                <th colspan="4"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Payment Modal -->
<div id="addPaymentModal" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Add Payment</h5>
                <button type="button" class="close" id="closeModalBtn">&times;</button>
            </div>
            <form id="addPaymentForm">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Amount <span class="text-danger">*</span></label>
                        <input type="number" name="amount" id="paymentAmount" class="form-control" step="0.01" 
                               max="{{ $balanceDue }}" required placeholder="Enter amount">
                        <small class="text-muted">Balance Due: ₹{{ number_format($balanceDue, 2) }}</small>
                    </div>
                    <div class="form-group">
                        <label>Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" id="paymentMethod" class="form-control" required>
                            <option value="">-- Select --</option>
                            <option value="CASH">Cash</option>
                            <option value="UPI">UPI</option>
                            <option value="CARD">Card</option>
                            <option value="BANK_TRANSFER">Bank Transfer</option>
                            <option value="OTHER">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Transaction No (Optional)</label>
                        <input type="text" name="transaction_no" id="transactionNo" class="form-control" placeholder="e.g., UTR number, TXN ID">
                    </div>
                    <div class="form-group">
                        <label>Remarks</label>
                        <textarea name="remarks" id="remarks" class="form-control" rows="2" placeholder="Any notes about this payment"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelModalBtn">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitPaymentBtn">Add Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
@include('includes.script')
<script>
function showToast(message, isError = false) {
    let toastClass = isError ? 'toast-error' : 'toast-success';
    let toast = $(`<div class="${toastClass}">${message}</div>`);
    $('body').append(toast);
    setTimeout(() => {
        toast.fadeOut(300, function() { $(this).remove(); });
    }, 3000);
}

function openModal() {
    $('#addPaymentModal').addClass('show');
    $('body').css('overflow', 'hidden');
}

function closeModal() {
    $('#addPaymentModal').removeClass('show');
    $('body').css('overflow', 'auto');
    $('#addPaymentForm')[0].reset();
    $('#paymentMethod').val('');
}

function getMethodClass(method) {
    // Fix: map correctly to your CSS classes
    const map = {
        'CASH': 'cash',
        'UPI': 'upi',
        'CARD': 'card',
        'BANK_TRANSFER': 'bank',
        'OTHER': 'other'
    };
    return map[method] || 'other';
}

function refreshPaymentsTable() {
    $.ajax({
        url: "{{ route('order.get.payments', $order->id) }}",
        type: "GET",
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // ✅ Destroy DataTable BEFORE touching the DOM
                if ($.fn.DataTable.isDataTable('#paymentTable')) {
                    $('#paymentTable').DataTable().destroy();
                }

                let tbody = $('#paymentTableBody');
                tbody.empty();

                if (response.payments.length === 0) {
                    tbody.append(`
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-credit-card fa-2x text-muted mb-2 d-block"></i>
                                No payments recorded yet
                            </td>
                        </tr>
                    `);
                } else {
                    $.each(response.payments, function(index, payment) {
                        let methodClass = getMethodClass(payment.payment_method); // ✅ Fixed mapping
                        let paymentDate = new Date(payment.payment_date);
                        let dateStr = paymentDate.toLocaleString('en-IN', {
                            day: '2-digit', month: 'short', year: 'numeric',
                            hour: '2-digit', minute: '2-digit', hour12: true
                        });

                        tbody.append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${dateStr}</td>
                                <td><strong>₹${parseFloat(payment.amount).toFixed(2)}</strong></td>
                                <td>
                                    <span class="payment-method-badge method-${methodClass}">
                                        ${payment.payment_method}
                                    </span>
                                </td>
                                <td>${payment.transaction_no || '-'}</td>
                                <td>${payment.remarks || '-'}</td>
                                <td>
                                    <button class="btn btn-sm btn-danger delete-payment"
                                            data-id="${payment.id}"
                                            data-amount="${payment.amount}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `);
                    });
                }

                // ✅ Update summary amounts
                $('#totalPaidFooter').text(`₹${parseFloat(response.total_paid).toFixed(2)}`);
                $('#totalPaidAmount').text(parseFloat(response.total_paid).toFixed(2));
                $('#balanceDueAmount').text(parseFloat(response.balance_due).toFixed(2));

                // ✅ Update status badge
                let statusHtml = '';
                if (response.balance_due <= 0) {
                    statusHtml = '<span class="badge status-paid px-3 py-2">FULLY PAID</span>';
                } else if (response.total_paid > 0) {
                    statusHtml = '<span class="badge status-partial px-3 py-2">PARTIALLY PAID</span>';
                } else {
                    statusHtml = '<span class="badge status-pending px-3 py-2">PENDING</span>';
                }
                $('#paymentStatusBadge').html(statusHtml);

                // ✅ Update modal balance hint
                $('#paymentAmount').attr('max', response.balance_due);
                $('#paymentAmount').closest('.form-group').find('.text-muted')
                    .text(`Balance Due: ₹${parseFloat(response.balance_due).toFixed(2)}`);

                // ✅ Reinit DataTable AFTER DOM is fully updated
                $('#paymentTable').DataTable({
                    order: [[0, 'desc']],
                    responsive: true,
                    paging: false,
                    searching: false,
                    info: false,
                    destroy: true
                });
            }
        },
        error: function(xhr) {
            console.error('Error refreshing payments:', xhr);
            showToast('Error refreshing payments', true);
        }
    });
}

$(document).ready(function() {
    // Initialize DataTable
    $('#paymentTable').DataTable({
        order: [[0, 'desc']],
        responsive: true,
        paging: false,
        searching: false,
        info: false,
        destroy: true
    });

    $('#showAddPaymentModal').click(function() { openModal(); });
    $('#closeModalBtn, #cancelModalBtn').click(function() { closeModal(); });

    $('#addPaymentModal').click(function(e) {
        if (e.target === this) { closeModal(); }
    });

    // Add Payment
    $('#addPaymentForm').on('submit', function(e) {
        e.preventDefault();

        let amount = $('#paymentAmount').val();
        let paymentMethod = $('#paymentMethod').val();

        if (!amount || amount <= 0) { showToast('Please enter a valid amount', true); return; }
        if (!paymentMethod) { showToast('Please select payment method', true); return; }

        let submitBtn = $('#submitPaymentBtn');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

        $.ajax({
            url: "{{ route('order.add.payment', $order->id) }}",
            type: "POST",
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    closeModal();
                    refreshPaymentsTable(); // ✅ Refresh after add
                    showToast(response.message);
                } else {
                    showToast(response.message, true);
                }
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'Error adding payment', true);
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('Add Payment');
            }
        });
    });

    // Delete Payment
    $(document).on('click', '.delete-payment', function() {
        let paymentId = $(this).data('id');
        let amount = $(this).data('amount');

        if (confirm(`Delete payment of ₹${amount}? This action cannot be undone.`)) {
            let deleteBtn = $(this);
            deleteBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                url: "{{ route('order.delete.payment', '') }}/" + paymentId,
                type: "DELETE",
                data: { _token: "{{ csrf_token() }}" },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        refreshPaymentsTable(); // ✅ Refresh after delete
                        showToast(response.message);
                    } else {
                        showToast(response.message, true);
                    }
                },
                error: function() {
                    showToast('Error deleting payment', true);
                    deleteBtn.prop('disabled', false).html('<i class="fas fa-trash"></i>');
                }
            });
        }
    });

// Print Invoice
$('#printInvoiceBtn').click(function() {
    let btn = $(this);
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');

    let pdfUrl = "{{ route('order.receipt.pdf', $order->id) }}";
    
    // ✅ Remove old iframe and create a fresh one each time
    $('#pdfFrame').remove();
    
    let iframe = $('<iframe>', {
        id: 'pdfFrame',
        src: pdfUrl,
        class: 'hidden-iframe'
    }).appendTo('body');

    // ✅ Wait for iframe to fully load, then print
    iframe[0].onload = function() {
        btn.prop('disabled', false).html('<i class="fas fa-print"></i> Print Invoice');
        try {
            iframe[0].contentWindow.focus();
            iframe[0].contentWindow.print();
        } catch (e) {
            // Fallback: open in new tab if iframe print fails
            window.open(pdfUrl, '_blank');
        }
    };

    // ✅ Fallback timeout in case onload doesn't fire
    setTimeout(function() {
        btn.prop('disabled', false).html('<i class="fas fa-print"></i> Print Invoice');
    }, 5000);
});
});
</script>
</body>
</html>