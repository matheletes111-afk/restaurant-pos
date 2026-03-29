<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Stock Out</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
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
                            <h5 class="m-b-10">View Stock Out</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('stock-outs.index') }}">Stock Outs</a></li>
                            <li class="breadcrumb-item" aria-current="page">View Stock Out</li>
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
                        <a href="{{ route('stock-outs.index') }}" class="btn btn-secondary" style="float: right;">
                            <i class="fa fa-arrow-left"></i> Back to List
                        </a>
                    </div>

                    <div class="card-body">
                        <!-- Stock Out Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="40%">Stock Out Number:</th>
                                        <td>{{ $stockOut->stockout_no }}</td>
                                    </tr>
                                    <tr>
                                        <th>Stock Out Date:</th>
                                        <td>{{ date('d-m-Y', strtotime($stockOut->stockout_date)) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Created By:</th>
                                        <td>{{ $stockOut->user->name ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="40%">Total Items:</th>
                                        <td>{{ $stockOut->total_items }}</td>
                                    </tr>
                                    <tr>
                                        <th>Total Quantity:</th>
                                        <td class="text-right font-weight-bold">{{ number_format($stockOut->total_quantity, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            @if($stockOut->status == 'COMPLETED')
                                                <span class="badge badge-success">Completed</span>
                                            @elseif($stockOut->status == 'PENDING')
                                                <span class="badge badge-warning">Pending</span>
                                            @else
                                                <span class="badge badge-danger">Cancelled</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Remarks -->
                        @if($stockOut->remarks)
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Remarks:</label>
                                    <div class="alert alert-light" style="background-color: #f8f9fa;">
                                        {{ $stockOut->remarks }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Stock Out Items -->
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Stock Out Items ({{ $stockOut->total_items }} items)</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Product</th>
                                                <th>Unit</th>
                                                <th class="text-right">Quantity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $totalQuantity = 0;
                                            @endphp
                                            @foreach($stockOut->items as $index => $item)
                                                @php
                                                    $totalQuantity += $item->quantity;
                                                @endphp
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $item->product->product_name }}</td>
                                                    <td>{{ $item->product->unit ? $item->product->unit->unit_name : 'N/A' }}</td>
                                                    <td class="text-right font-weight-bold">{{ number_format($item->quantity, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                                <td class="text-right font-weight-bold" style="font-size: 1.1em;">
                                                    {{ number_format($totalQuantity, 2) }}
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Summary Card -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card border-danger">
                                    <div class="card-header bg-danger text-white">
                                        <h6 class="mb-0"><i class="fa fa-arrow-down"></i> Stock Out Summary</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="text-center">
                                                    <h6 class="text-muted">Items Stocked Out</h6>
                                                    <h3 class="text-danger">{{ $stockOut->total_items }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="text-center">
                                                    <h6 class="text-muted">Total Quantity Reduced</h6>
                                                    <h3 class="text-danger">{{ number_format($stockOut->total_quantity, 2) }}</h3>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Created Information -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <p class="mb-0">
                                        <strong><i class="fa fa-user"></i> Created By:</strong> {{ $stockOut->user->name ?? 'N/A' }} 
                                        | <strong><i class="fa fa-calendar"></i> Created At:</strong> {{ $stockOut->created_at->format('d-m-Y H:i:s') }}
                                        @if($stockOut->updated_at != $stockOut->created_at)
                                            | <strong><i class="fa fa-edit"></i> Last Updated:</strong> {{ $stockOut->updated_at->format('d-m-Y H:i:s') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <a href="{{ route('stock-outs.edit', $stockOut->id) }}" class="btn btn-success">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <a href="{{ route('stock-outs.delete', $stockOut->id) }}" 
                                   class="btn btn-danger"
                                   onclick="return confirm('Are you sure you want to delete this stock out? This will add back quantities to inventory.')">
                                    <i class="fa fa-trash"></i> Delete
                                </a>
                                <a href="javascript:window.print()" class="btn btn-info">
                                    <i class="fa fa-print"></i> Print
                                </a>
                                <a href="{{ route('stock-outs.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-times"></i> Close
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
@include('includes.script')

<style>
    @media print {
        .card-footer, .breadcrumb, .page-header, .loader-bg {
            display: none !important;
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        .card-header {
            background-color: #fff !important;
            color: #000 !important;
            border-bottom: 2px solid #000 !important;
        }
        
        .table-bordered th, .table-bordered td {
            border: 1px solid #000 !important;
        }
        
        .alert-info {
            background-color: #f8f9fa !important;
            border: 1px solid #ddd !important;
        }
        
        .btn {
            display: none !important;
        }
    }
</style>

</body>
</html>