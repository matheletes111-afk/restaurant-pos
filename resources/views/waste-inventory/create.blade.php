<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Waste Inventory - Restaurant POS</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('admin_template/css/inventory-modules.css') }}">
    
    <style>
        body {
            background-color: #f4f6fb;
            font-family: 'Plus Jakarta Sans', sans-serif;
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
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 1.3rem;
            margin-bottom: 5px;
        }
        .breadcrumb-item a {
            color: #ff5e14;
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
        .form-group label, .form-label {
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
            height: auto !important;
        }
        .form-control:focus, select:focus {
            background-color: #ffffff !important;
            border-color: #ff5e14 !important;
            box-shadow: 0 0 0 3px rgba(255, 94, 20, 0.12) !important;
            outline: none !important;
        }

        /* Select2 */
        .select2-container--default .select2-selection--single {
            background-color: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            height: 44px !important;
            padding: 6px 12px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 30px !important;
            color: #1e293b !important;
        }

        /* Type selector pills */
        .type-selector-pills {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
        }
        .type-pill {
            flex: 1;
            padding: 16px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #f8fafc;
        }
        .type-pill:hover {
            border-color: #cbd5e1;
            background: #ffffff;
        }
        .type-pill.active {
            border-color: #ff5e14;
            background: #fff7ed;
            color: #ea580c;
            box-shadow: 0 0 0 1px #ff5e14;
        }
        .type-pill input[type="radio"] {
            display: none;
        }
        .type-pill-title {
            font-weight: 800;
            font-size: 1.05rem;
            display: block;
            margin-bottom: 4px;
        }
        .type-pill-sub {
            font-size: 0.76rem;
            color: #64748b;
        }
        .type-pill.active .type-pill-sub {
            color: #c2410c;
        }

        .btn-theme {
            background: linear-gradient(135deg, #ff5e14 0%, #ff8c42 100%) !important;
            border: none !important;
            border-radius: 30px !important;
            padding: 12px 32px !important;
            font-weight: 700 !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(255, 94, 20, 0.25) !important;
            transition: all 0.25s ease !important;
        }
        .btn-theme:hover {
            background: linear-gradient(135deg, #ea580c 0%, #ff5e14 100%) !important;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(255, 94, 20, 0.35) !important;
            color: white !important;
        }

        .stock-info-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 20px;
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
                            <h5 class="m-b-10">Record Waste Inventory</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('waste-inventory.index') }}">Waste Inventory</a></li>
                            <li class="breadcrumb-item" aria-current="page">Add Waste Entry</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-9 col-md-12">
                <div class="card">
                    @include('includes.message')

                    <form action="{{ route('waste-inventory.store') }}" method="POST" id="wasteCreateForm">
                        @csrf

                        <div class="card-body">
                            <!-- Ref No & Date -->
                            <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom flex-wrap gap-2">
                                <div>
                                    <h6 class="m-0 fw-bold text-dark">Waste Log Reference: <span class="text-primary">{{ $nextWasteNo }}</span></h6>
                                    <small class="text-muted">Recorded quantities will be directly deducted from the restaurant's live inventory.</small>
                                </div>
                                <div>
                                    <span class="badge bg-light text-dark border px-3 py-2">
                                        <i class="far fa-calendar-alt me-1"></i> Today: {{ date('d M Y') }}
                                    </span>
                                </div>
                            </div>

                            <!-- 1. Item Type Selector -->
                            <label class="form-label">Step 1: Select Item Type *</label>
                            <div class="type-selector-pills">
                                <label class="type-pill active" id="pillFood">
                                    <input type="radio" name="item_type" value="FOOD" checked onchange="switchItemType('FOOD')">
                                    <span class="type-pill-title">🍽️ Food Item</span>
                                    <span class="type-pill-sub">Kitchen, groceries, ingredients, raw materials</span>
                                </label>
                                @if(!empty($isBarEnabled))
                                <label class="type-pill" id="pillBar">
                                    <input type="radio" name="item_type" value="BAR" onchange="switchItemType('BAR')">
                                    <span class="type-pill-title">🍾 Bar Item</span>
                                    <span class="type-pill-sub">Liquor, wine, beer, spirits (Auto Unit: ML)</span>
                                </label>
                                @endif
                            </div>

                            <!-- 2. Product & Unit & Stock -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-8">
                                    <label class="form-label">Step 2: Select Product *</label>
                                    <select name="product_id" id="productSelect" class="form-control select2" required style="width: 100%;">
                                        <option value="">-- Choose Food Product --</option>
                                        @foreach($foodProducts as $product)
                                            <option value="{{ $product->id }}" 
                                                    data-type="FOOD"
                                                    data-unit="{{ $product->unit ? $product->unit->name : 'N/A' }}">
                                                {{ $product->product_name }} ({{ $product->unit ? $product->unit->name : 'N/A' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Unit</label>
                                    <input type="text" id="unitDisplay" class="form-control" placeholder="Select product" readonly>
                                </div>
                            </div>

                            <!-- Current Stock Alert Box -->
                            <div class="stock-info-box d-flex justify-content-between align-items-center" id="stockBox">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-boxes text-secondary fs-5"></i>
                                    <div>
                                        <div class="fw-bold text-dark small">Current Available Stock</div>
                                        <div id="stockValText" class="text-muted small">Please select a product above</div>
                                    </div>
                                </div>
                                <div id="stockBadgeContainer">
                                    <span class="badge bg-light text-secondary border px-3 py-2">--</span>
                                </div>
                            </div>

                            <!-- 3. Quantity & Date -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Step 3: Waste Quantity *</label>
                                    <input type="number" name="waste_qty" id="wasteQty" class="form-control" step="0.01" min="0.01" placeholder="Enter quantity to deduct" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Step 4: Waste Date *</label>
                                    <input type="date" name="waste_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>

                            <!-- 4. Reason -->
                            <div class="mb-4">
                                <label class="form-label">Step 5: Reason / Remarks</label>
                                <textarea name="reason" id="reasonText" class="form-control" rows="3" placeholder="Enter reason for wastage (e.g. Expired, spoiled, accidental spillage, broken bottle, burnt/overcooked, quality rejection)..."></textarea>
                                
                                <div class="mt-2 d-flex flex-wrap gap-2">
                                    <span class="badge bg-light text-secondary border cursor-pointer" onclick="fillReason('Expired / Past shelf life')">Expired</span>
                                    <span class="badge bg-light text-secondary border cursor-pointer" onclick="fillReason('Spoiled / Quality issue')">Spoiled</span>
                                    <span class="badge bg-light text-secondary border cursor-pointer" onclick="fillReason('Spillage / Accidental loss')">Spillage</span>
                                    <span class="badge bg-light text-secondary border cursor-pointer" onclick="fillReason('Broken bottle / Damaged pack')">Broken Bottle</span>
                                    <span class="badge bg-light text-secondary border cursor-pointer" onclick="fillReason('Kitchen preparation error')">Kitchen Error</span>
                                    <span class="badge bg-light text-secondary border cursor-pointer" onclick="fillReason('Customer tasting / Demo waste')">Tasting / Demo</span>
                                </div>
                            </div>

                        </div>

                        <div class="card-footer bg-light px-4 py-3 d-flex justify-content-between align-items-center">
                            <a href="{{ route('waste-inventory.index') }}" class="btn btn-secondary px-4" style="border-radius: 30px;">
                                <i class="fas fa-arrow-left me-1"></i> Back to List
                            </a>
                            <button type="submit" class="btn btn-theme">
                                <i class="fas fa-minus-circle me-1"></i> Deduct & Save Waste
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    const foodProductsList = @json($foodProducts);
    const barProductsList = @json($barProducts);
</script>

<!-- JS Dependencies -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@include('includes.script')

<script>
    $(document).ready(function() {
        // Initialize Select2
        $('#productSelect').select2({
            placeholder: '-- Select Product --',
            allowClear: true
        });

        $('#productSelect').on('change', function() {
            const productId = $(this).val();
            fetchStock(productId);
        });
    });

    function switchItemType(type) {
        if (type === 'FOOD') {
            $('#pillFood').addClass('active');
            $('#pillBar').removeClass('active');
            renderDropdown(foodProductsList, 'FOOD');
        } else {
            $('#pillBar').addClass('active');
            $('#pillFood').removeClass('active');
            renderDropdown(barProductsList, 'BAR');
        }
    }

    function renderDropdown(products, type) {
        const select = $('#productSelect');
        select.empty();
        select.append(new Option(`-- Select ${type === 'BAR' ? 'Bar Item' : 'Food Product'} --`, ''));

        products.forEach(p => {
            const unitName = type === 'BAR' ? 'ML' : (p.unit ? p.unit.name : 'N/A');
            const label = type === 'BAR' 
                ? `🍾 ${p.product_name} (${unitName})` 
                : `${p.product_name} (${unitName})`;
            const opt = new Option(label, p.id, false, false);
            $(opt).attr('data-unit', unitName);
            select.append(opt);
        });

        select.val('').trigger('change');
        $('#unitDisplay').val('');
        $('#stockValText').text('Please select a product above');
        $('#stockBadgeContainer').html('<span class="badge bg-light text-secondary border px-3 py-2">--</span>');
    }

    function fetchStock(productId) {
        if (!productId) {
            $('#unitDisplay').val('');
            $('#stockValText').text('Please select a product above');
            $('#stockBadgeContainer').html('<span class="badge bg-light text-secondary border px-3 py-2">--</span>');
            return;
        }

        $.ajax({
            url: '{{ route("waste-inventory.get-product", "") }}/' + productId,
            type: 'GET',
            success: function(res) {
                if (res.success) {
                    $('#unitDisplay').val(res.unit_name);
                    const stock = parseFloat(res.current_stock) || 0;

                    let infoText = `${stock.toFixed(2)} ${res.unit_name}`;
                    if (res.is_bar && res.bottle_count !== null) {
                        infoText += ` (≈ ${res.bottle_count} Bottles @ ${res.bottle_size_ml}ml)`;
                    }

                    $('#stockValText').html(`<strong>${infoText}</strong>`);

                    if (stock <= 0) {
                        $('#stockBadgeContainer').html(`<span class="badge bg-danger text-white px-3 py-2">Out of Stock (0)</span>`);
                    } else {
                        $('#stockBadgeContainer').html(`<span class="badge bg-success text-white px-3 py-2">${stock.toFixed(2)} ${res.unit_name}</span>`);
                    }
                }
            }
        });
    }

    function fillReason(txt) {
        $('#reasonText').val(txt);
    }
</script>

</body>
</html>

