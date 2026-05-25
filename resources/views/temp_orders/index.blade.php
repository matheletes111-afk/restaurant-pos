<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin - Pending Orders</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        .order-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s;
            border-left: 4px solid;
        }
        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.12);
        }
        .order-status-new {
            border-left-color: #f59e0b;
        }
        .badge-new {
            background: #fef3c7;
            color: #92400e;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .gst-bill-badge {
            background: #8b5cf6;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.65rem;
            font-weight: 500;
        }
        .non-gst-bill-badge {
            background: #64748b;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.65rem;
            font-weight: 500;
        }
    </style>
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
                    <h5>Customer Pending Orders <span class="badge badge-warning ml-2">{{ $orders->count() }} Pending</span></h5>
                </div>

                <div class="card-body">
                    <div class="dt-responsive table-responsive">
                        <table id="ordersTable" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Phone</th>
                                    <th>Table No</th>
                                    <th>Order Type</th>
                                    <th>Total Items</th>
                                    <th>Subtotal</th>
                                    <th>Discount</th>
                                    <th>Taxable</th>
                                    <th>GST</th>
                                    <th>Grand Total</th>
                                    <th>Bill Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($orders as $o)
                                @php
                                    $totalItems = $o->items->count();
                                    $isGstBill = ($o->is_gst_bill ?? 'NO') == 'YES';
                                @endphp
                                <tr>
                                    <td>{{ $o->id }}</div>
                                    <td><span class="fw-bold text-primary">{{ $o->order_id ?? 'N/A' }}</span></div>
                                    <td>{{ $o->customer_name ?? 'Guest' }}</div>
                                    <td>{{ $o->customer_phone ?? '-' }}</div>
                                    <td>{{ $o->table_details->name ?? 'Takeaway' }}</div>
                                    <td>
                                        @if($o->order_type == 'DINE_IN')
                                            <span class="btn btn-info">Dine In</span>
                                        @else
                                            <span class="btn btn-success">Takeaway</span>
                                        @endif
                                    </div>
                                    <td>{{ $totalItems }}</div>
                                    <td>₹{{ number_format($o->total_amount ?? 0, 2) }}</div>
                                    <td><span class="text-danger">- ₹{{ number_format($o->discount ?? 0, 2) }}</span></div>
                                    <td>₹{{ number_format($o->taxable_amount ?? 0, 2) }}</div>
                                    <td>
                                        @if($isGstBill)
                                            ₹{{ number_format($o->gst_amount ?? 0, 2) }}
                                            <br><small class="text-muted">{{ $o->restaurant_gst_percentage ?? 0 }}%</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                    <td><b>₹{{ number_format($o->grand_total ?? 0, 2) }}</b></div>
                                    <td>
                                        @if($isGstBill)
                                            <span class="gst-bill-badge">GST Bill</span>
                                        @else
                                            <span class="non-gst-bill-badge">Non-GST</span>
                                        @endif
                                    </div>
                                    <td>
                                        <a href="{{ route('temp.orders.view', $o->id) }}" 
                                           class="btn btn-primary btn-sm" title="View Order">
                                           <i class="fa fa-eye"></i> View
                                        </a>
                                        
                                    </div>
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
        order: [[0, 'desc']],
        responsive: true,
        pageLength: 25,
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries"
        },
        columnDefs: [
            { targets: [0,1], visible: true },
            { targets: [6,7,8,9,10,11], className: 'text-end' }
        ]
    });
});
</script>

<style>
    .table td {
        vertical-align: middle;
    }
    .btn-sm {
        margin: 0 2px;
    }
    .badge {
        padding: 5px 10px;
        border-radius: 20px;
    }
</style>

@include('includes.script')
</body>
</html>