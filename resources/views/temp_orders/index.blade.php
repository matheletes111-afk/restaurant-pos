<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin - Pending Orders</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>

<body data-pc-theme="light">

@include('includes.sidebar')

<div class="pc-container">
<div class="pc-content">

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Pending Customer Orders</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item" aria-current="page">Pending Orders</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <div class="row">
        <div class="col-sm-12">

            <div class="card">
                <div class="card-header">
                    <h5>Customer Pending Orders</h5>
                </div>

                <div class="card-body">
                    <div class="dt-responsive table-responsive">
                        <table id="ordersTable" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Phone</th>
                                    <th>Table No</th>
                                    <th>Total Items</th>
                                    <th>Total Amount</th>
                                    <th>GST</th>
                                    <th>Grand Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($orders as $o)
                                <tr>
                                    <td>{{ $o->id }}</td>
                                    <td>{{ @$o->customer_name }}</td>
                                    <td>{{ @$o->customer_phone }}</td>
                                    <td>{{ @$o->table_details->name }}</td>
                                    <td>{{ @$o->items->count() }}</td>
                                    <td>₹{{ number_format($o->total_amount,2) }}</td>
                                    <td>₹{{ number_format($o->gst_amount,2) }}</td>
                                    <td><b>₹{{ number_format($o->grand_total,2) }}</b></td>

                                    <td>
                                        <a href="{{ route('temp.orders.view',$o->id) }}" 
                                           class="btn btn-primary btn-sm">
                                           <i class="fa fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>

</div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    $('#ordersTable').DataTable({
        order: [[0, 'desc']] // Order ID column DESC
    });
});

</script>

@include('includes.script')
</body>
</html>
