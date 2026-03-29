<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Purchase</title>
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
                            <h5 class="m-b-10">View Purchase</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}">Purchases</a></li>
                            <li class="breadcrumb-item" aria-current="page">View Purchase</li>
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
                        <a href="{{ route('purchases.index') }}" class="btn btn-secondary" style="float: right;">
                            <i class="fa fa-arrow-left"></i> Back to List
                        </a>
                    </div>

                    <div class="card-body">
                        <!-- Purchase Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="40%">Invoice Number:</th>
                                        <td>{{ $purchase->invoice_no }}</td>
                                    </tr>
                                    <tr>
                                        <th>Purchase Date:</th>
                                        <td>{{ date('d-m-Y', strtotime($purchase->purchase_date)) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Supplier:</th>
                                        <td>{{ $purchase->supplier->supplier_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Supplier Phone:</th>
                                        <td>{{ $purchase->supplier->phone }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="40%">Total Items:</th>
                                        <td>{{ $purchase->total_items }}</td>
                                    </tr>
                                    <tr>
                                        <th>Bill Amount:</th>
                                        <td class="text-right font-weight-bold">₹{{ number_format($purchase->bill_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Total Amount:</th>
                                        <td class="text-right font-weight-bold">₹{{ number_format($purchase->total_amount, 2) }}</td>
                                    </tr>
                                   
                                </table>
                            </div>
                        </div>

                        <!-- Bill Attachment -->
                        @if($purchase->bill_attachment)
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Bill Attachment:</label>
                                    <br>
                                    @php
                                        $fileExtension = pathinfo($purchase->bill_attachment, PATHINFO_EXTENSION);
                                        $isImage = in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif']);
                                    @endphp
                                    
                                    @if($isImage)
                                        <img src="{{ URL::to('storage/app/public/') }}/{{ $purchase->bill_attachment }}" 
                                             alt="Bill Attachment" 
                                             class="img-thumbnail" 
                                             style="max-width: 300px; max-height: 300px; margin-bottom: 10px;">
                                        <br>
                                    @endif
                                    
                                    <a href="{{ URL::to('storage/app/public/') }}/{{ $purchase->bill_attachment }}" 
                                       target="_blank" 
                                       class="btn btn-outline-primary">
                                        <i class="fa fa-download"></i> Download Bill
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Remarks -->
                        @if($purchase->remarks)
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Remarks:</label>
                                    <div class="alert alert-light" style="background-color: #f8f9fa;">
                                        {{ $purchase->remarks }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Purchase Items -->
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Purchase Items ({{ $purchase->total_items }} items)</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Product</th>
                                                <th>Unit</th>
                                                <th class="text-right">Quantity</th>
                                                <th class="text-right">Price (₹)</th>
                                               
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $totalQuantity = 0;
                                                $totalPrice = 0;
                                            @endphp
                                            @foreach($purchase->items as $index => $item)
                                                @php
                                                    $itemTotal = $item->quantity * $item->price;
                                                    $totalQuantity += $item->quantity;
                                                    $totalPrice += $itemTotal;
                                                @endphp
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $item->product->product_name }}</td>
                                                    <td>{{ $item->product->unit ? $item->product->unit->name : 'N/A' }}</td>
                                                    <td class="text-right">{{ number_format($item->quantity, 2) }}</td>
                                                    <td class="text-right">{{ number_format($item->price, 2) }}</td>
                                                    
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Summary Card -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0 text-white"><i class="fa fa-info-circle"></i> Purchase Summary</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="text-center">
                                                    <h6 class="text-muted">Items Purchased</h6>
                                                    <h3 class="text-primary">{{ $purchase->total_items }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="text-center">
                                                    <h6 class="text-muted">Total Quantity</h6>
                                                    <h3 class="text-success">{{ number_format($totalQuantity, 2) }}</h3>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="text-center">
                                                    <h6 class="text-muted">Bill Amount</h6>
                                                    <h3 class="text-danger">₹{{ number_format($purchase->bill_amount, 2) }}</h3>
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
                                        <strong><i class="fa fa-user"></i> Created By:</strong> {{ $purchase->user->name ?? 'N/A' }} 
                                        | <strong><i class="fa fa-calendar"></i> Created At:</strong> {{ $purchase->created_at->format('d-m-Y H:i:s') }}
                                        @if($purchase->updated_at != $purchase->created_at)
                                            | <strong><i class="fa fa-edit"></i> Last Updated:</strong> {{ $purchase->updated_at->format('d-m-Y H:i:s') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <a href="{{ route('purchases.edit', $purchase->id) }}" class="btn btn-success">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <a href="{{ route('purchases.delete', $purchase->id) }}" 
                                   class="btn btn-danger"
                                   onclick="return confirm('Are you sure you want to delete this purchase? This will also reverse inventory quantities.')">
                                    <i class="fa fa-trash"></i> Delete
                                </a>
                                <a href="javascript:window.print()" class="btn btn-info">
                                    <i class="fa fa-print"></i> Print
                                </a>
                                <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
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