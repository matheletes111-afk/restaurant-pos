@extends('layouts.app')

@section('title')
<title>Admin - Order Management Report</title>
@endsection

@section('style')
@include('includes.style')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="{{ asset('admin_template/css/report-analytics.css') }}">
<style>
  .dataTables_wrapper {
    padding: 16px 20px;
  }
  .dataTables_filter input {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 6px 14px;
    outline: none;
    font-size: 0.85rem;
  }
  .dataTables_filter input:focus {
    border-color: var(--rpt-primary);
    background: #ffffff;
  }
  .dt-buttons .dt-button {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 20px !important;
    padding: 6px 14px !important;
    font-size: 0.8rem !important;
    font-weight: 700 !important;
    color: var(--rpt-slate) !important;
    margin-right: 6px !important;
    transition: all 0.2s ease !important;
  }
  .dt-buttons .dt-button:hover {
    background: #f8fafc !important;
    border-color: var(--rpt-primary) !important;
    color: var(--rpt-primary) !important;
  }
</style>
@endsection

@section('body')
@include('includes.sidebar')

<div class="pc-container">
<div class="pc-content">

    <div class="rpt-page-wrap">
        {{-- Flash / Error Messages --}}
        @include('includes.message')

        {{-- 1. Header Deck --}}
        <div class="rpt-header-deck">
            <div class="rpt-header-left">
                <div class="rpt-header-icon icon-orders">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div class="rpt-header-title-meta">
                    <span class="rpt-header-eyebrow">Financial & Sales Audit</span>
                    <h1 class="rpt-header-title">Order Management Report</h1>
                    <p class="rpt-header-sub">Comprehensive order ledger, revenue analytics, tax breakdown, and payment auditing</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small fw-bold">Period: {{ $fromDate->format('d M Y') }} - {{ $toDate->format('d M Y') }}</span>
            </div>
        </div>

        {{-- 2. Filter Form --}}
        <div class="rpt-filter-card">
            <form method="GET" action="{{ route('order.report.management') }}" class="row g-3 align-items-end">
                <div class="col-md-3 col-sm-6">
                    <label class="rpt-filter-label">From Date</label>
                    <input type="date" name="from_date" value="{{ $fromDate->format('Y-m-d') }}" class="rpt-filter-control">
                </div>
                <div class="col-md-3 col-sm-6">
                    <label class="rpt-filter-label">To Date</label>
                    <input type="date" name="to_date" value="{{ $toDate->format('Y-m-d') }}" class="rpt-filter-control">
                </div>
                <div class="col-md-2 col-sm-6">
                    <label class="rpt-filter-label">Order Type</label>
                    <select name="order_type" class="rpt-filter-control">
                        <option value="all" {{ ($orderType == 'all' || !$orderType) ? 'selected' : '' }}>All Types</option>
                        @foreach($orderTypes as $type)
                            <option value="{{ $type }}" {{ $orderType == $type ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', strtolower($type))) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-sm-6">
                    <label class="rpt-filter-label">Payment Status</label>
                    <select name="payment_status" class="rpt-filter-control">
                        <option value="all" {{ ($paymentStatus == 'all' || !$paymentStatus) ? 'selected' : '' }}>All Status</option>
                        @foreach($paymentStatuses as $status)
                            <option value="{{ $status }}" {{ $paymentStatus == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-sm-12">
                    <button type="submit" class="btn-rpt-primary w-100 justify-content-center">
                        <i class="fas fa-filter"></i> Apply Filter
                    </button>
                </div>
            </form>
        </div>

        {{-- 3. Summary Stats Deck --}}
        <div class="rpt-stats-grid">
            <div class="rpt-stat-card stat-primary">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Total Revenue</span>
                    <span class="rpt-stat-val">₹{{ number_format($summary['total_revenue'], 2) }}</span>
                    <small class="text-muted mt-1">{{ $summary['total_orders'] }} Total Orders</small>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-success">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Amount Collected</span>
                    <span class="rpt-stat-val text-success">₹{{ number_format($summary['total_collected'], 2) }}</span>
                    <small class="text-muted mt-1">{{ $summary['paid_count'] }} Paid Orders</small>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-danger">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Pending Balance</span>
                    <span class="rpt-stat-val text-danger">₹{{ number_format($summary['pending_amount'], 2) }}</span>
                    <small class="text-muted mt-1">{{ $summary['pending_count'] }} Pending Orders</small>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-purple">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">GST Invoices</span>
                    <span class="rpt-stat-val text-purple">{{ $summary['gst_bills_count'] ?? 0 }}</span>
                    <small class="text-muted mt-1">₹{{ number_format($summary['total_gst_amount'] ?? 0, 2) }} Tax Collected</small>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>
        </div>

        <div class="rpt-stats-grid">
            <div class="rpt-stat-card stat-info">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Dine-in Orders</span>
                    <span class="rpt-stat-val text-info">{{ $summary['dine_in_count'] }}</span>
                    <small class="text-muted mt-1">Table Service</small>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-chair"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-indigo">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Takeaway Orders</span>
                    <span class="rpt-stat-val" style="color: var(--rpt-indigo); font-family: 'Outfit', sans-serif;">{{ $summary['takeaway_count'] }}</span>
                    <small class="text-muted mt-1">Parcel / Delivery</small>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-warning">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Item Discounts</span>
                    <span class="rpt-stat-val text-warning">₹{{ number_format($summary['total_item_discount'] ?? 0, 2) }}</span>
                    <small class="text-muted mt-1">Menu Dish Discounts</small>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-percentage"></i>
                </div>
            </div>

            <div class="rpt-stat-card stat-dark">
                <div class="rpt-stat-info">
                    <span class="rpt-stat-label">Order Discounts</span>
                    <span class="rpt-stat-val text-danger">₹{{ number_format($summary['total_order_discount'] ?? 0, 2) }}</span>
                    <small class="text-muted mt-1">Overall Bill Discounts</small>
                </div>
                <div class="rpt-stat-icon">
                    <i class="fas fa-tags"></i>
                </div>
            </div>
        </div>

        {{-- 4. Orders Datatable Card --}}
        <div class="rpt-card">
            <div class="rpt-card-header">
                <h5 class="rpt-card-title">
                    <i class="fas fa-list-alt text-primary"></i> Detailed Orders Audit
                </h5>
                <span class="text-muted small fw-bold">Displaying {{ $orders->count() }} records</span>
            </div>

            <div class="table-responsive">
                <table id="ordersTable" class="rpt-table table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th class="text-center">Actions</th>
                            <th>Order UID</th>
                            <th>Customer Info</th>
                            <th>Service Type</th>
                            <th class="text-end">Subtotal</th>
                            <th class="text-end">Discounts</th>
                            <th class="text-end">Taxable</th>
                            <th class="text-end">GST</th>
                            <th class="text-end">Grand Total</th>
                            <th class="text-end">Paid</th>
                            <th class="text-end">Due Balance</th>
                            <th class="text-center">Bill Type</th>
                            <th class="text-center">Status</th>
                            <th>Timestamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $key => $order)
                        @php
                            $balance = round($order->grand_total, 2) - ($order->amount_paid ?? 0);
                            $isFullyPaid = $balance <= 0;
                            $isGstBill = ($order->is_gst_bill ?? 'NO') == 'YES';
                            $gstPercentage = $order->restaurant_gst_percentage ?? 0;
                            
                            $totalItemDiscount = 0;
                            if($order->orderItems) {
                                foreach ($order->orderItems as $item) {
                                    $itemDiscountAmount = ($item->price * $item->quantity) - $item->taxable_amount;
                                    $totalItemDiscount += $itemDiscountAmount;
                                }
                            }
                            $orderDiscountAmount = $order->discount ?? 0;
                            $totalDiscount = $totalItemDiscount + $orderDiscountAmount;
                        @endphp
                        <tr>
                            <td class="text-center text-muted fw-bold">
                                {{ method_exists($orders, 'currentPage') ? ($orders->currentPage() - 1) * $orders->perPage() + $key + 1 : $key + 1 }}
                            </td>
                            <td>
                                <div class="rpt-actions justify-content-center">
                                    <a href="{{ route('order.invoice', $order->id) }}" 
                                       class="rpt-btn-action btn-print" 
                                       title="View / Print Tax Invoice"
                                       target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <a href="{{ route('order.report.order.details', $order->id) }}" 
                                       class="rpt-btn-action btn-view" 
                                       title="View Order Breakdown">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" 
                                            class="rpt-btn-action btn-del btn-delete-order" 
                                            data-order-id="{{ $order->id }}"
                                            data-order-uid="{{ $order->order_id }}"
                                            title="Delete Order (Requires OTP)">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold" style="font-family: 'Outfit', sans-serif; color: var(--rpt-primary);">
                                    #{{ $order->order_id }}
                                </span>
                            </td>
                            <td>
                                <div>
                                    <strong class="text-dark">{{ $order->customer_name ?? 'Walk-in Guest' }}</strong>
                                    @if($order->customer_phone)
                                    <div class="text-muted small">
                                        <i class="fas fa-phone-alt me-1 text-secondary"></i> {{ $order->customer_phone }}
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($order->order_type == 'DINE_IN')
                                    <span class="rpt-badge badge-dinein">
                                        <i class="fas fa-chair"></i> Dine-in
                                    </span>
                                    @if($order->table)
                                        <div class="text-muted small mt-1">{{ $order->table->name }}</div>
                                    @endif
                                @else
                                    <span class="rpt-badge badge-takeaway">
                                        <i class="fas fa-shopping-bag"></i> Takeaway
                                    </span>
                                @endif
                            </td>
                            <td class="text-end fw-semibold">₹{{ number_format($order->total_amount ?? 0, 2) }}</td>
                            <td class="text-end text-danger fw-semibold">- ₹{{ number_format($totalDiscount, 2) }}</td>
                            <td class="text-end fw-semibold">₹{{ number_format($order->taxable_amount ?? 0, 2) }}</td>
                            <td class="text-end">
                                @if($isGstBill)
                                    <span class="fw-bold text-dark">₹{{ number_format($order->gst_amount ?? 0, 2) }}</span>
                                    <div class="text-muted small">({{ $gstPercentage }}%)</div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <strong style="font-family: 'Outfit', sans-serif; font-size: 1rem; color: var(--rpt-dark);">
                                    ₹{{ number_format($order->grand_total, 2) }}
                                </strong>
                            </td>
                            <td class="text-end text-success fw-bold">
                                ₹{{ number_format($order->amount_paid ?? 0, 2) }}
                            </td>
                            <td class="text-end">
                                @if($isFullyPaid)
                                    <span class="text-success fw-bold">₹0.00</span>
                                @else
                                    <span class="text-danger fw-bold">₹{{ number_format($balance, 2) }}</span>
                                    <div class="text-danger small"><i class="fas fa-exclamation-circle"></i> Due</div>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($isGstBill)
                                    <span class="rpt-badge badge-gst">
                                        <i class="fas fa-file-invoice"></i> GST
                                    </span>
                                @else
                                    <span class="rpt-badge badge-nongst">Non-GST</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($order->payment_status == 'PAID')
                                    <span class="rpt-badge badge-paid">
                                        <i class="fas fa-check-circle"></i> Paid
                                    </span>
                                @elseif($order->payment_status == 'PENDING')
                                    <span class="rpt-badge badge-pending">
                                        <i class="fas fa-clock"></i> Pending
                                    </span>
                                @else
                                    <span class="badge bg-secondary">{{ $order->payment_status }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-dark fw-semibold small">{{ $order->created_at->format('d M Y') }}</div>
                                <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="15" class="text-center py-5 text-muted">
                                <i class="fas fa-file-invoice fa-3x mb-3 text-secondary opacity-50"></i>
                                <h6 class="fw-bold">No Orders Found</h6>
                                <p class="small text-muted mb-0">No transaction records match the selected date or filter criteria.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($orders, 'links'))
            <div class="p-3 border-top">
                {{ $orders->links() }}
            </div>
            @endif
        </div>

    </div>

</div>
</div>

{{-- Delete Order Modal (OTP verification) --}}
<div class="modal fade" id="deleteOrderModal" tabindex="-1" aria-labelledby="deleteOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rpt-modal-content" style="border-radius: var(--rpt-radius-lg); border: none; box-shadow: 0 20px 40px rgba(15, 23, 42, 0.15);">
            <div class="modal-header p-4 border-bottom">
                <h5 class="modal-title fw-bold text-danger d-flex align-items-center gap-2" id="deleteOrderModalLabel" style="font-family: 'Outfit', sans-serif;">
                    <i class="fas fa-exclamation-triangle"></i> Delete Order
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                {{-- Step 1: Reason / Remarks --}}
                <div id="deleteStepRemarks">
                    <div class="alert alert-warning border-0 p-3 mb-3 d-flex align-items-start gap-2" style="border-radius: 12px; font-size: 0.88rem;">
                        <i class="fas fa-info-circle text-warning mt-1"></i>
                        <div>
                            Are you sure you want to delete order <strong id="deleteOrderUIDDisplay"></strong>? This will soft-delete the transaction and require OTP confirmation.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="rpt-filter-label">Reason / Remarks <span class="text-danger">*</span></label>
                        <textarea class="rpt-filter-control" id="deleteRemarks" rows="3" placeholder="Enter reason for deletion..." required></textarea>
                        <div class="text-danger small mt-1 fw-bold" id="deleteRemarksFeedback" style="display: none;">Reason is required.</div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger rounded-pill px-4 fw-bold" id="btnSendDeleteOTP">
                            Send Verification OTP
                        </button>
                    </div>
                </div>

                {{-- Step 2: OTP Verification --}}
                <div id="deleteStepOTP" style="display: none;">
                    <div class="alert alert-info border-0 p-3 mb-3 d-flex align-items-start gap-2" style="border-radius: 12px; font-size: 0.88rem;">
                        <i class="fas fa-envelope text-info mt-1"></i>
                        <div>
                            A 6-digit verification OTP has been sent to the Restaurant Administrator's registered email address.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="rpt-filter-label text-center d-block">Enter Verification OTP <span class="text-danger">*</span></label>
                        <input type="text" class="rpt-filter-control text-center fw-bold fs-3" id="deleteOTP" maxlength="6" placeholder="000000" style="letter-spacing: 6px;">
                        <div class="text-danger small text-center mt-1 fw-bold" id="deleteOTPFeedback" style="display: none;">Invalid OTP entered.</div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-light rounded-pill px-3" id="btnBackToRemarks">Back</button>
                        <button type="button" class="btn btn-success rounded-pill px-4 fw-bold" id="btnVerifyAndDelete">
                            Verify &amp; Delete Order
                        </button>
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
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    let currentDeleteOrderId = null;

    // Handle delete button click
    $(document).on('click', '.btn-delete-order', function(e) {
        e.preventDefault();
        currentDeleteOrderId = $(this).data('order-id');
        let orderUID = $(this).data('order-uid');

        $('#deleteOrderUIDDisplay').text(orderUID);
        $('#deleteRemarks').val('');
        $('#deleteOTP').val('');
        $('#deleteRemarksFeedback').hide();
        $('#deleteOTPFeedback').hide();
        
        $('#deleteStepRemarks').show();
        $('#deleteStepOTP').hide();

        const deleteModal = new bootstrap.Modal(document.getElementById('deleteOrderModal'));
        deleteModal.show();
    });

    // Send OTP
    $('#btnSendDeleteOTP').on('click', function() {
        let remarks = $('#deleteRemarks').val().trim();
        if (!remarks) {
            $('#deleteRemarksFeedback').text('Reason is required.').show();
            return;
        }
        $('#deleteRemarksFeedback').hide();

        let btn = $(this);
        btn.prop('disabled', true).text('Sending OTP...');

        $.ajax({
            url: "{{ route('order.report.management.send-otp') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                order_id: currentDeleteOrderId,
                remarks: remarks
            },
            success: function(response) {
                btn.prop('disabled', false).text('Send Verification OTP');
                if (response.success) {
                    $('#deleteStepRemarks').hide();
                    $('#deleteStepOTP').show();
                } else {
                    alert(response.message || 'An error occurred.');
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).text('Send Verification OTP');
                let msg = 'An error occurred while sending OTP.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                alert(msg);
            }
        });
    });

    // Back to remarks
    $('#btnBackToRemarks').on('click', function() {
        $('#deleteStepOTP').hide();
        $('#deleteStepRemarks').show();
    });

    // Verify OTP and Soft Delete
    $('#btnVerifyAndDelete').on('click', function() {
        let otp = $('#deleteOTP').val().trim();
        if (!otp || otp.length !== 6) {
            $('#deleteOTPFeedback').text('Please enter a 6-digit OTP.').show();
            return;
        }
        $('#deleteOTPFeedback').hide();

        let btn = $(this);
        btn.prop('disabled', true).text('Verifying...');

        $.ajax({
            url: "{{ route('order.report.management.verify-delete') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                order_id: currentDeleteOrderId,
                otp: otp
            },
            success: function(response) {
                btn.prop('disabled', false).text('Verify & Delete Order');
                if (response.success) {
                    const modalEl = document.getElementById('deleteOrderModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) modalInstance.hide();
                    alert('Order deleted successfully.');
                    window.location.reload();
                } else {
                    $('#deleteOTPFeedback').text(response.message || 'Verification failed.').show();
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).text('Verify & Delete Order');
                let msg = 'Verification failed.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                $('#deleteOTPFeedback').text(msg).show();
            }
        });
    });
});
</script>
@endsection