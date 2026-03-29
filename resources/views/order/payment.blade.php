<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order Payment</title>
  @include('includes.style')
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body { font-family: 'Poppins', sans-serif; background: #f6f8fa; }
    .card { border-radius: 14px; padding: 20px; margin-top: 20px; }
    .save-btn { background: linear-gradient(135deg, #28a745, #58d26b); color: #fff; border: none; font-weight: 600; }
    .save-btn:hover { opacity: 0.9; }
  </style>
</head>
<body>
@include('includes.sidebar')

<div class="pc-container">
  <div class="pc-content">
    <div class="card shadow-sm">
        <h4><i class="fas fa-credit-card mr-2"></i> Payment for Order #{{ $order->id }}</h4>
        <p><strong>Customer:</strong> {{ $order->customer_name }}</p>
        @if($table)
            <p><strong>Table:</strong> {{ $table->name }}</p>
        @endif
        <p><strong>Grand Total:</strong> ₹{{ number_format($order->grand_total, 2) }}</p>

        <form action="{{ route('order.payment.submit', $order->id) }}" method="POST" class="mt-3">
            @csrf
            <div class="form-group mb-3">
                <label for="payment_method">Payment Method</label>
                <select name="payment_method" id="payment_method" class="form-control" required>
                    <option value="">--Select--</option>
                    <option value="CASH">Cash</option>
                    <option value="CARD">Card</option>
                    <option value="UPI">UPI</option>
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="remarks">Remarks</label>
                <input type="text" name="remarks" id="remarks" class="form-control">
            </div>

            <div class="form-group mb-3">
                <label for="payment_status">Payment Status</label>
                <select name="payment_status" id="payment_status" class="form-control" required>
                    <option value="PENDING">PENDING</option>
                    <option value="PAID">PAID</option>
                </select>
            </div>

            <button type="submit" class="btn save-btn"><i class="fa fa-check"></i> Submit Payment</button>
        </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
