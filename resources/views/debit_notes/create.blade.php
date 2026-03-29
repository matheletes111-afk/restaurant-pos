<!DOCTYPE html>
<html lang="en">
<head>
    <title>Create Debit Note</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .stock-info {
            font-size: 12px;
            margin-top: 5px;
        }
        .current-stock {
            color: #dc3545;
            font-weight: bold;
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
                            <h5 class="m-b-10">Create Debit Note</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('debit-notes.index') }}">Debit Notes</a></li>
                            <li class="breadcrumb-item" aria-current="page">Create</li>
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
                    
                    <form action="{{ route('debit-notes.store') }}" method="POST" id="debitNoteForm">
                        @csrf
                        
                        <div class="card-body">
                            <!-- Basic Information -->
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Debit Note No *</label>
                                        <input type="text" name="debit_note_no" class="form-control" value="{{ $debitNoteNo }}" readonly required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Debit Date *</label>
                                        <input type="date" name="debit_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Supplier *</label>
                                        <select name="supplier_id" class="form-control select2" required>
                                            <option value="">Select Supplier</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Remarks</label>
                                        <input type="text" name="remarks" class="form-control" placeholder="Optional remarks">
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- Debit Note Items -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <h5>Return Items (Stock will be reduced)</h5>
                                    <p class="text-danger"><small>Note: Adding items here will reduce stock from your inventory</small></p>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered" id="itemsTable">
                                    <thead>
                                        <tr>
                                            <th width="50%">Product *</th>
                                            <th width="20%">Unit</th>
                                            <th width="25%">Return Quantity *</th>
                                            <th width="5%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsBody">
                                        <!-- Initial empty row -->
                                        <tr id="row_0">
                                            <td>
                                                <select name="items[0][product_id]" class="form-control product-select" required onchange="getProductDetails(0, this.value)">
                                                    <option value="">Select Product</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" data-unit="{{ $product->unit ? $product->unit->name : 'N/A' }}">
                                                            {{ $product->product_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="stock-info">
                                                    <small>Available Stock: <span class="current-stock" id="stock_0">-</span></small>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control unit-display" id="unit_0" readonly>
                                                <input type="hidden" class="form-control unit-id" name="items[0][unit_id]" id="unit_id_0">
                                            </td>
                                            <td>
                                                <input type="number" name="items[0][quantity]" class="form-control quantity" id="quantity_0" step="0.001" min="0.001" required onchange="validateQuantity(0)">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(0)" disabled>
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
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
                                        <i class="fa fa-save"></i> Save Debit Note
                                    </button>
                                    <a href="{{ route('debit-notes.index') }}" class="btn btn-secondary">
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
    let rowCount = 0;
    let selectedProducts = []; // Track selected products to prevent duplicates
    
    $(document).ready(function() {
        // Initialize Select2 for all selects including the initial one
        $('.select2').select2();
        $('.product-select').select2();
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
                    <div class="stock-info">
                        <small>Available Stock: <span class="current-stock" id="stock_${rowCount}">-</span></small>
                    </div>
                </td>
                <td>
                    <input type="text" class="form-control unit-display" id="unit_${rowCount}" readonly>
                    <input type="hidden" class="form-control unit-id" name="items[${rowCount}][unit_id]" id="unit_id_${rowCount}">
                </td>
                <td>
                    <input type="number" name="items[${rowCount}][quantity]" class="form-control quantity" id="quantity_${rowCount}" step="0.001" min="0.001" required onchange="validateQuantity(${rowCount})">
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
        
        // Initialize select2 for new row
        $(`#row_${rowCount} .product-select`).select2();
    }
    
    function removeRow(rowId) {
        if (rowId === 0 && $('#itemsBody tr').length === 1) {
            alert('At least one item is required');
            return;
        }
        
        // Remove product from selected list
        const productId = $(`#row_${rowId} .product-select`).val();
        if (productId) {
            selectedProducts = selectedProducts.filter(id => id != productId);
        }
        
        // Destroy Select2 before removing the row
        $(`#row_${rowId} .product-select`).select2('destroy');
        
        $(`#row_${rowId}`).remove();
    }
    
    function getProductDetails(rowId, productId) {
        if (!productId) {
            $(`#unit_${rowId}`).val('');
            $(`#unit_id_${rowId}`).val('');
            $(`#stock_${rowId}`).text('-');
            return;
        }
        
        // Check for duplicate product
        if (selectedProducts.includes(productId)) {
            alert('This product is already added. Please select a different product.');
            $(`#row_${rowId} .product-select`).val('').trigger('change');
            return;
        }
        
        // Add to selected products
        selectedProducts.push(productId);
        
        const selectedOption = $(`#row_${rowId} select option:selected`);
        const unitName = selectedOption.data('unit');
        $(`#unit_${rowId}`).val(unitName);
        
        // Get product details via AJAX
        $.ajax({
            url: '{{ route("debit-notes.get-product", "") }}/' + productId,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    $(`#unit_id_${rowId}`).val(response.product.unit_id);
                    
                    // Get current stock
                    $.ajax({
                        url: '{{ route("debit-notes.check-stock", "") }}/' + productId,
                        type: 'GET',
                        success: function(stockResponse) {
                            $(`#stock_${rowId}`).text(stockResponse.stock);
                        }
                    });
                }
            }
        });
    }
    
    function validateQuantity(rowId) {
        const quantity = parseFloat($(`#quantity_${rowId}`).val());
        const stock = parseFloat($(`#stock_${rowId}`).text());
        
        if (!isNaN(stock) && quantity > stock) {
            alert('Return quantity cannot exceed available stock!');
            $(`#quantity_${rowId}`).val('');
            $(`#quantity_${rowId}`).focus();
        }
    }
    
    // Form validation
    $('#debitNoteForm').submit(function(e) {
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
        
        // Validate supplier selected
        const supplierId = $('select[name="supplier_id"]').val();
        if (!supplierId) {
            errorMessage += 'Please select a supplier.\n';
            isValid = false;
        }
        
        // Validate quantities don't exceed stock
        $('.quantity').each(function() {
            const rowId = $(this).attr('id').replace('quantity_', '');
            const quantity = parseFloat($(this).val());
            const stock = parseFloat($(`#stock_${rowId}`).text());
            
            if (!isNaN(stock) && quantity > stock) {
                errorMessage += `Quantity exceeds available stock for row ${parseInt(rowId) + 1}\n`;
                isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Validation Error:\n' + errorMessage);
        }
    });
</script>

</body>
</html>