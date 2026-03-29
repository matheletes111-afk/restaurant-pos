<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin - View Temp Order</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
</head>

<body data-pc-theme="light">

@include('includes.sidebar')

<div class="pc-container">
<div class="pc-content">

    <!-- Breadcrumb -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Order Details</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('temp.orders') }}">Pending Orders</a></li>
                        <li class="breadcrumb-item">Order #{{ $order->id }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Info Card -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0 text-white">Customer & Order Information</h5>
        </div>

        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>Name:</strong> {{ $order->customer_name }}
                </div>
                <div class="col-md-4">
                    <strong>Phone:</strong> {{ $order->customer_phone }}
                </div>
                <div class="col-md-4">
                    <strong>Table No:</strong> {{ $order->table_details->name }}
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-4"><strong>Subtotal:</strong> ₹{{ number_format($order->total_amount,2) }}</div>
                <div class="col-md-4"><strong>GST:</strong> ₹{{ number_format($order->gst_amount,2) }}</div>
                <div class="col-md-4"><strong>Grand Total:</strong> <b>₹{{ number_format($order->grand_total,2) }}</b></div>
            </div>

        </div>
    </div>

    <!-- Order Items -->
    <div class="card mt-3">
        <div class="card-header">
            <h5>Order Items</h5>
        </div>
        @include('includes.message')
        <div class="card-body table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Food Item</th>
                        <th>Qty</th>
                        <th>Rate</th>
                        <th>GST</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($order->items as $key => $i)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ @$i->menuItem->name }}</td>
                        <td>{{ $i->quantity }}</td>
                        <td>₹{{ number_format($i->price,2) }}</td>
                        <td>{{ number_format($i->gst_rate,2) }}%</td>
                        <td>₹{{ number_format($i->total_amount,2) }}</td>

                        <td>
                            <a href="{{route('temp.orders.view.delete.item',@$i->id)}}"
                               onclick="return confirm('Delete this item?')"
                               class="btn btn-danger btn-sm">
                               <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>

    <!-- Approve Order -->
    <div class="text-center mt-4">
        @if($table->table_status == 'AVAILABLE')
            <a href="{{route('admin.temporder.approve',@$order->id)}}"
                class="btn btn-success btn-lg">
                <i class="fa fa-check-circle"></i> Approve Order & Move to Main Order
            </a>
        @else
            <div class="alert alert-warning">
                <b>Table is not available! </b>
            </div>
        @endif
    </div>

</div>
</div>

@include('includes.script')
</body>
</html>
