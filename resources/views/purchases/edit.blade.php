<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Purchase</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Public Sans', sans-serif;
        }
        .page-header {
            background: white;
            padding: 24px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.02);
            margin-bottom: 25px;
            border: 1px solid rgba(0, 0, 0, 0.03);
        }
        .page-header h5 {
            color: #0f172a;
            font-weight: 800;
            font-size: 1.3rem;
            margin-bottom: 5px;
        }
        .breadcrumb-item a {
            color: #009d1a;
            text-decoration: none;
            font-weight: 600;
        }
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(15, 23, 42, 0.03);
            background: white;
            border: 1px solid rgba(0, 0, 0, 0.03);
            overflow: hidden;
        }
        .card-body {
            padding: 30px !important;
        }
        .form-group label {
            font-weight: 700;
            color: #475569;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
            display: inline-block;
        }
        .form-control, select {
            background-color: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 10px 14px !important;
            font-size: 0.9rem !important;
            color: #1e293b !important;
            transition: all 0.2s ease !important;
            height: auto !important;
        }
        .form-control:focus, select:focus {
            background-color: #ffffff !important;
            border-color: #009d1a !important;
            box-shadow: 0 0 0 3px rgba(0, 157, 26, 0.1) !important;
            outline: none !important;
        }
        
        /* Select2 Premium Styles */
        .select2-container--default .select2-selection--single {
            background-color: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            height: 42px !important;
            padding: 6px 12px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px !important;
            color: #1e293b !important;
        }
        .select2-container--default .select2-selection--single:focus,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #009d1a !important;
            background-color: #ffffff !important;
        }
        
        /* Items Table */
        #itemsTable {
            border-collapse: collapse !important;
            width: 100% !important;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            margin-top: 15px;
        }
        #itemsTable thead th {
            background: #0f172a;
            color: white;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 14px 16px;
            border: none;
        }
        #itemsTable tbody td {
            padding: 14px 16px;
            vertical-align: middle;
            border-bottom: 1px solid #e2e8f0;
        }
        
        /* Action buttons */
        .btn-success {
            background: linear-gradient(135deg, #009d1a 0%, #00bc20 100%) !important;
            border: none !important;
            border-radius: 30px !important;
            padding: 10px 24px !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 12px rgba(0, 157, 26, 0.15) !important;
            transition: all 0.3s ease !important;
        }
        .btn-success:hover {
            background: linear-gradient(135deg, #00bc20 0%, #009d1a 100%) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 6px 18px rgba(0, 157, 26, 0.25) !important;
        }
        .btn-primary {
            background: linear-gradient(135deg, #009d1a 0%, #00bc20 100%) !important;
            border: none !important;
            border-radius: 30px !important;
            padding: 12px 30px !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 12px rgba(0, 157, 26, 0.15) !important;
            transition: all 0.3s ease !important;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #00bc20 0%, #009d1a 100%) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 6px 18px rgba(0, 157, 26, 0.25) !important;
        }
        .btn-secondary {
            background-color: #e2e8f0 !important;
            color: #475569 !important;
            border: none !important;
            border-radius: 30px !important;
            padding: 12px 24px !important;
            font-weight: 600 !important;
            transition: all 0.25s ease !important;
        }
        .btn-secondary:hover {
            background-color: #cbd5e1 !important;
            color: #1e293b !important;
        }
        .btn-danger {
            background: rgba(239, 68, 68, 0.1) !important;
            color: #ef4444 !important;
            border: 1px solid rgba(239, 68, 68, 0.2) !important;
            border-radius: 8px !important;
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 !important;
            transition: all 0.2s ease !important;
        }
        .btn-danger:hover {
            background: #ef4444 !important;
            color: white !important;
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2) !important;
        }
        
        .card-footer {
            background: #f8fafc;
            border-top: 1px solid rgba(0, 0, 0, 0.05) !important;
            padding: 20px 30px !important;
        }
    </style>
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
                            <h5 class="m-b-10">Edit Purchase</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}">Purchases</a></li>
                            <li class="breadcrumb-item" aria-current="page">Edit Purchase</li>
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
                    
                    <form action="{{ route('purchases.update') }}" method="POST" enctype="multipart/form-data" id="purchaseForm">
                        @csrf
                        <input type="hidden" name="id" value="{{ $purchase->id }}">
                        
                        <div class="card-body">
                            <!-- Basic Information -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Invoice Number *</label>
                                        <input type="text" name="invoice_no" class="form-control" value="{{ $purchase->invoice_no }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Purchase Date *</label>
                                        <input type="date" name="purchase_date" class="form-control" value="{{ $purchase->purchase_date->format('Y-m-d') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Supplier *</label>
                                        <select name="supplier_id" class="form-control select2" required>
                                            <option value="">Select Supplier</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" {{ $purchase->supplier_id == $supplier->id ? 'selected' : '' }}>
                                                    {{ $supplier->supplier_name }} ({{ $supplier->phone }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Bill Amount (₹) *</label>
                                        <input type="number" name="bill_amount" class="form-control" step="0.01" min="0" value="{{ $purchase->bill_amount }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Remarks</label>
                                        <textarea name="remarks" class="form-control" rows="2">{{ $purchase->remarks }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Bill Attachment</label>
                                        <input type="file" name="bill_attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                                        @if($purchase->bill_attachment)
                                            <small class="text-success">
                                                <i class="fa fa-paperclip"></i> 
                                                <a href="{{ URL::to('storage/app/public/') }}/{{ @$purchase->bill_attachment }}" target="_blank">View Current Attachment</a>
                                            </small>
                                        @endif
                                        <small class="text-muted">Max file size: 2MB (JPG, PNG, PDF)</small>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- Purchase Items -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <h5>Purchase Items</h5>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="itemsTable">
                                    <thead>
                                        <tr>
                                            <th width="40%">Product *</th>
                                            <th width="20%">Unit</th>
                                            <th width="20%">Quantity *</th>
                                            <th width="15%">Price (₹) *</th>
                                            <th width="5%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsBody">
                                        @foreach($purchase->items as $index => $item)
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
                                                <small class="text-muted current-stock"></small>
                                                <input type="hidden" class="form-control unit-id" name="items[{{ $index }}][unit_id]" value="{{ $item->unit_id }}">
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $index }}][quantity]" class="form-control quantity" step="0.01" min="0.01" value="{{ $item->quantity }}" required>
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $index }}][price]" class="form-control price" step="0.01" min="0" value="{{ $item->price }}" required>
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
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> Update Purchase
                                    </button>
                                    <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@include('includes.script')

<script>
    let rowCount = {{ count($purchase->items) - 1 }};
    
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2();
        
        // Get stock info for existing products
        for (let i = 0; i <= rowCount; i++) {
            const productId = $(`#row_${i} select`).val();
            if (productId) {
                getProductDetails(i, productId);
            }
        }
    });
    
    function addRow() {
        rowCount++;
        const newRow = `
            <tr id="row_${rowCount}">
                <td>
                    <select name="items[${rowCount}][product_id]" class="form-control product-select" required onchange="getProductDetails(${rowCount}, this.value)">
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-unit="{{ $product->unit ? $product->unit->name : 'N/A' }}">
                                {{ $product->product_name }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control unit-display" readonly>
                    <small class="text-muted current-stock"></small>
                    <input type="hidden" class="form-control unit-id" name="items[${rowCount}][unit_id]">
                </td>
                <td>
                    <input type="number" name="items[${rowCount}][quantity]" class="form-control quantity" step="0.01" min="0.01" required>
                </td>
                <td>
                    <input type="number" name="items[${rowCount}][price]" class="form-control price" step="0.01" min="0" required>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(${rowCount})">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        
        $('#itemsBody').append(newRow);
        $('#row_0 .btn-danger').prop('disabled', false);
    }
    
    function removeRow(rowId) {
        if (rowId === 0 && $('#itemsBody tr').length === 1) {
            // Don't remove the first row if it's the only one
            alert('At least one item is required');
            return;
        }
        
        $(`#row_${rowId}`).remove();
    }
    
    function getProductDetails(rowId, productId) {
        if (!productId) {
            $(`#row_${rowId} .unit-display`).val('');
            $(`#row_${rowId} .unit-id`).val('');
            $(`#row_${rowId} .current-stock`).text('');
            return;
        }
        
        const selectedOption = $(`#row_${rowId} select option:selected`);
        const unitName = selectedOption.data('unit');
        $(`#row_${rowId} .unit-display`).val(unitName);
        
        // Get product details via AJAX
        $.ajax({
            url: '{{ route("purchases.get-product", "") }}/' + productId,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    $(`#row_${rowId} .unit-id`).val(response.product.unit_id);
                    
                    // Get current stock
                    $.ajax({
                        url: '{{ route("purchases.check-stock", "") }}/' + productId,
                        type: 'GET',
                        success: function(stockResponse) {
                            $(`#row_${rowId} .current-stock`).text('Stock: ' + stockResponse.stock);
                        }
                    });
                }
            }
        });
    }
    
    // Form validation
    $('#purchaseForm').submit(function(e) {
        let isValid = true;
        let errorMessage = '';
        
        // Check if at least one item has quantity > 0
        let hasValidItem = false;
        $('.quantity').each(function() {
            if (parseFloat($(this).val()) > 0) {
                hasValidItem = true;
            }
        });
        
        if (!hasValidItem) {
            errorMessage += 'Please add at least one item with quantity greater than 0.\n';
            isValid = false;
        }
        
        // Check for duplicate products
        const selectedProducts = [];
        $('.product-select').each(function() {
            const productId = $(this).val();
            if (productId && selectedProducts.includes(productId)) {
                errorMessage += 'Duplicate products found. Please remove duplicate items.\n';
                isValid = false;
                return false;
            }
            if (productId) {
                selectedProducts.push(productId);
            }
        });
        
        // Check bill amount
        const billAmount = parseFloat($('input[name="bill_amount"]').val()) || 0;
        if (billAmount <= 0) {
            errorMessage += 'Bill amount must be greater than 0.\n';
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            alert(errorMessage);
        }
    });
</script>

</body>
</html>