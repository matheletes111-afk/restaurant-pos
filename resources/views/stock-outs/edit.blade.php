<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Stock Out</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
                            <h5 class="m-b-10">Edit Stock Out</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('stock-outs.index') }}">Stock Outs</a></li>
                            <li class="breadcrumb-item" aria-current="page">Edit Stock Out</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- Breadcrumb end -->

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    @include('includes.message')
                    
                    @if(session('stock_errors'))
                        <div class="alert alert-danger">
                            <h6><i class="fa fa-exclamation-triangle"></i> Stock Errors:</h6>
                            <ul class="mb-0">
                                @foreach(session('stock_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form action="{{ route('stock-outs.update') }}" method="POST" id="stockOutForm">
                        @csrf
                        <input type="hidden" name="id" value="{{ $stockOut->id }}">
                        
                        <div class="card-body">
                            <!-- Basic Information -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Stock Out Number *</label>
                                        <input type="text" name="stockout_no" class="form-control" value="{{ $stockOut->stockout_no }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Stock Out Date *</label>
                                        <input type="date" name="stockout_date" class="form-control" value="{{ $stockOut->stockout_date->format('Y-m-d') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Remarks</label>
                                        <textarea name="remarks" class="form-control" rows="1">{{ $stockOut->remarks }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- Stock Out Items -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <h5>Stock Out Items</h5>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="itemsTable">
                                    <thead>
                                        <tr>
                                            <th width="40%">Product *</th>
                                            <th width="20%">Unit</th>
                                            <th width="20%">Quantity *</th>
                                            <th width="5%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsBody">
                                        @foreach($stockOut->items as $index => $item)
                                        <tr id="row_{{ $index }}">
                                            <td>
                                                <select name="items[{{ $index }}][product_id]" class="form-control product-select" required onchange="getProductDetails({{ $index }}, this.value)">
                                                    <option value="">Select Product</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" 
                                                                data-unit="{{ $product->unit ? $product->unit->name : 'N/A' }}"
                                                                {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                            {{ $product->product_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control unit-display" value="{{ $item->product->unit ? $item->product->unit->name : 'N/A' }}" readonly>
                                                <input type="hidden" class="form-control unit-id" name="items[{{ $index }}][unit_id]" value="{{ $item->unit_id }}">
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $index }}][quantity]" class="form-control quantity" step="0.01" min="0.01" value="{{ $item->quantity }}" required onchange="checkStock({{ $index }})">
                                            </td>
                                            
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="removeRow({{ $index }})" {{ $index == 0 ? 'disabled' : '' }}>
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button type="button" class="btn btn-success" onclick="addRow()">
                                        <i class="fa fa-plus"></i> Add Item
                                    </button>
                                </div>
                            </div>

                            <!-- Total Summary -->
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <strong>Total Items:</strong> <span id="totalItems">{{ count($stockOut->items) }}</span>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Total Quantity:</strong> <span id="totalQuantity">{{ number_format($stockOut->total_quantity, 2) }}</span>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Status:</strong> <span id="stockStatus" class="text-success">Ready</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> Update Stock Out
                                    </button>
                                    <a href="{{ route('stock-outs.index') }}" class="btn btn-secondary">
                                        <i class="fa fa-times"></i> Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
@include('includes.script')

<script>
    let rowCount = {{ count($stockOut->items) - 1 }};
    
    $(document).ready(function() {
        // Get stock info for existing products
        for (let i = 0; i <= rowCount; i++) {
            const productId = $(`#row_${i} select`).val();
            if (productId) {
                getProductDetails(i, productId);
            }
        }
        updateTotals();
    });
    
    // ... (same JavaScript functions as create.blade.php)
    // Copy all functions from create.blade.php
</script>

</body>
</html>