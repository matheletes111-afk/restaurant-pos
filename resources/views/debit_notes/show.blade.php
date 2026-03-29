<!DOCTYPE html>
<html lang="en">
<head>
    <title>Debit Note Details</title>
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
                            <h5 class="m-b-10">Debit Note Details</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('debit-notes.index') }}">Debit Notes</a></li>
                            <li class="breadcrumb-item" aria-current="page">Details</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Debit Note: {{ $debitNote->debit_note_no }}</h5>
                        <div class="float-end">
                            <a href="{{ route('debit-notes.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <!-- Debit Note Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="40%">Debit Note No:</th>
                                        <td>{{ $debitNote->debit_note_no }}</td>
                                    </tr>
                                    <tr>
                                        <th>Date:</th>
                                        <td>{{ $debitNote->debit_date->format('d-m-Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Supplier:</th>
                                        <td>{{ $debitNote->supplier->supplier_name ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="40%">Created By:</th>
                                        <td>{{ $debitNote->user->name ?? 'System' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Created At:</th>
                                        <td>{{ $debitNote->created_at->format('d-m-Y h:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Remarks:</th>
                                        <td>{{ $debitNote->remarks ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="row">
                            <div class="col-md-12">
                                <h5>Returned Items</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Product</th>
                                                <th>Unit</th>
                                                <th>Quantity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($debitNote->items as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $item->product->product_name ?? '-' }}</td>
                                                <td>{{ $item->unit->name ?? '-' }}</td>
                                                <td>{{ $item->quantity }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" class="text-right">Total Items:</th>
                                                <th>{{ $debitNote->items->count() }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <a href="{{ route('debit-notes.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Back to List
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@include('includes.script')

</body>
</html>