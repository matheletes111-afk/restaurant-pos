<!DOCTYPE html>
<html lang="en">
<head>
    <title>Invoice #{{ $order->id }}</title>

    @include('includes.style')

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        .invoice-iframe {
            width: 100%;
            height: calc(100vh - 220px);
            border: none;
        }
    </style>
</head>

<body data-pc-theme="light">

@include('includes.sidebar')

<div class="pc-container">
    <div class="pc-content">

        <!-- Page Header (same as product page) -->
        <div class="page-header d-flex justify-content-between align-items-center">
            <h5 class="m-b-10">Invoice #{{ $order->id }}</h5>

            <div>
                <button onclick="printInvoice()" class="btn btn-success">
                    <i class="fa fa-print"></i> Print
                </button>

                <a href="{{route('order.management.dashboard')}}" class="btn btn-secondary">
                    <i class="fa fa-times"></i> Close
                </a>
            </div>
        </div>

        <!-- Card Layout -->
        <div class="card">
            <div class="card-body p-0">
                <iframe
                    class="invoice-iframe"
                    src="{{ route('order.receipt.pdf', $order->id) }}">
                </iframe>
            </div>
        </div>

    </div>
</div>

<!-- Scripts (same stack as product page) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function printInvoice() {
    document.querySelector('.invoice-iframe').contentWindow.print();
}
</script>

</body>
</html>
