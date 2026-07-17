<!DOCTYPE html>
<html lang="en">
<head>
    <title>Live Inventory</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8fafc !important;
        }
        .card {
            border: none !important;
            border-radius: 16px !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03) !important;
            background: white !important;
            margin-bottom: 30px !important;
            overflow: hidden;
        }
        .refresh-btn {
            background: linear-gradient(135deg, #ff6a00 0%, #ff8c42 100%) !important;
            color: white !important;
            border: none !important;
            border-radius: 30px !important;
            padding: 8px 20px !important;
            font-weight: 600 !important;
            font-size: 0.85rem !important;
            box-shadow: 0 4px 12px rgba(255, 106, 0, 0.15) !important;
            transition: all 0.3s ease !important;
            cursor: pointer;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }
        .refresh-btn:hover {
            background: linear-gradient(135deg, #ff8c42 0%, #ff6a00 100%) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 6px 18px rgba(255, 106, 0, 0.25) !important;
            color: white !important;
        }
        .refresh-btn:disabled {
            background: #cbd5e1 !important;
            box-shadow: none !important;
            cursor: not-allowed !important;
            color: #94a3b8 !important;
        }
        
        /* Modern search field */
        .search-input-group {
            position: relative;
        }
        .search-field {
            padding-left: 36px !important;
            border-radius: 30px !important;
            border: 1px solid #e2e8f0 !important;
            font-size: 0.85rem !important;
            height: 38px !important;
            width: 200px !important;
            transition: all 0.2s ease !important;
            background-color: white !important;
        }
        .search-field:focus {
            border-color: #ff6a00 !important;
            box-shadow: 0 0 0 3px rgba(255, 106, 0, 0.15) !important;
        }
        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 0.85rem;
            z-index: 10;
        }

        /* Filter Tab Group */
        .filter-buttons-group {
            display: inline-flex;
            background: #f1f5f9;
            padding: 4px;
            border-radius: 30px;
        }
        .filter-tab-btn {
            padding: 6px 16px;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #64748b;
            text-decoration: none !important;
            transition: all 0.2s ease;
        }
        .filter-tab-btn:hover {
            color: #0f172a;
        }
        .filter-tab-btn.active {
            background: white;
            color: #ff6a00;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }
        .filter-tab-btn.warning-tab.active {
            color: #d97706;
        }
        .filter-tab-btn.danger-tab.active {
            color: #dc2626;
        }

        /* Premium Summary Cards */
        .summary-card {
            border: 1px solid #f1f5f9 !important;
            border-radius: 14px !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.01) !important;
            transition: all 0.25s ease !important;
        }
        .summary-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.03) !important;
        }
        .summary-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Table Styles */
        #inventoryTable {
            border-collapse: separate !important;
            border-spacing: 0 !important;
            width: 100% !important;
        }
        #inventoryTable th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-weight: 600 !important;
            font-size: 0.8rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            border-bottom: 2px solid #e2e8f0 !important;
            padding: 12px 16px !important;
        }
        #inventoryTable td {
            padding: 14px 16px !important;
            vertical-align: middle !important;
            border-bottom: 1px solid #f1f5f9 !important;
            font-size: 0.85rem !important;
            color: #334155 !important;
        }
        
        /* Stock Rows Colors */
        .stock-out-row {
            background-color: rgba(239, 68, 68, 0.02) !important;
        }
        .stock-low-row {
            background-color: rgba(245, 158, 11, 0.02) !important;
        }
        
        /* Stock badges */
        .stock-badge-pill {
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
        .badge-pill-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }
        .badge-pill-warning {
            background-color: rgba(245, 158, 11, 0.1);
            color: #d97706;
        }
        .badge-pill-danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }
        .badge-pill-info {
            background-color: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }
        
        .btn-action-custom {
            width: 32px;
            height: 32px;
            border-radius: 8px !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 !important;
            transition: all 0.2s ease;
            border: none !important;
            text-decoration: none !important;
        }
        .btn-action-custom:hover {
            transform: translateY(-1px);
        }
        .stock-quantity {
            font-weight: 700;
            font-size: 0.95rem;
        }
        .zero-stock {
            color: #ef4444;
        }
        .low-stock {
            color: #d97706;
        }
        .good-stock {
            color: #10b981;
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
                            <h5 class="m-b-10">Live Inventory</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item" aria-current="page">Live Inventory</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- Breadcrumb end -->

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header border-0 bg-transparent py-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div>
                                <h4 class="mb-1 text-slate-800 fw-bold">Current Stock Levels</h4>
                                <p class="text-muted mb-0">Real-time tracking of restaurant stock, supplies, and raw materials.</p>
                            </div>
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <button onclick="refreshInventory()" class="refresh-btn">
                                    <i class="fa-solid fa-arrows-rotate"></i> Refresh
                                </button>
                                
                                <form method="GET" action="{{ route('inventory.live') }}" class="d-flex align-items-center gap-2 m-0">
                                    <div class="search-input-group">
                                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                                        <input type="text" name="search" class="form-control search-field" placeholder="Search stock..." value="{{ request('search') }}">
                                    </div>
                                    
                                    <div class="filter-buttons-group">
                                        <a href="{{ route('inventory.live') }}" 
                                           class="filter-tab-btn {{ !request('low_stock') && !request('out_of_stock') ? 'active' : '' }}">
                                            All
                                        </a>
                                        <a href="{{ route('inventory.live') }}?low_stock=1" 
                                           class="filter-tab-btn warning-tab {{ request('low_stock') ? 'active' : '' }}">
                                            Low Stock
                                        </a>
                                        <a href="{{ route('inventory.live') }}?out_of_stock=1" 
                                           class="filter-tab-btn danger-tab {{ request('out_of_stock') ? 'active' : '' }}">
                                            Out of Stock
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Summary Cards -->
                        <div class="row mb-4 g-3">
                            <div class="col-md-3">
                                <div class="card summary-card mb-0">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="summary-icon-box text-primary" style="background-color: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                                                <i class="fa-solid fa-cubes fa-lg"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h4 class="mb-0 fw-bold">{{ $totalProducts }}</h4>
                                                <p class="text-muted mb-0 small">Total Products</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card summary-card mb-0">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="summary-icon-box text-success" style="background-color: rgba(16, 185, 129, 0.1); color: #10b981;">
                                                <i class="fa-solid fa-circle-check fa-lg"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h4 class="mb-0 fw-bold">{{ $totalProducts - $lowStockItems - $outOfStockItems }}</h4>
                                                <p class="text-muted mb-0 small">Good Stock</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card summary-card mb-0">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="summary-icon-box text-warning" style="background-color: rgba(245, 158, 11, 0.1); color: #d97706;">
                                                <i class="fa-solid fa-triangle-exclamation fa-lg"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h4 class="mb-0 fw-bold">{{ $lowStockItems }}</h4>
                                                <p class="text-muted mb-0 small">Low Stock (≤10)</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card summary-card mb-0">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="summary-icon-box text-danger" style="background-color: rgba(239, 68, 68, 0.1); color: #ef4444;">
                                                <i class="fa-solid fa-circle-xmark fa-lg"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h4 class="mb-0 fw-bold">{{ $outOfStockItems }}</h4>
                                                <p class="text-muted mb-0 small">Out of Stock</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Inventory Table -->
                        <div class="dt-responsive table-responsive">
                            <table id="inventoryTable" class="table table-striped table-bordered nowrap">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product Name</th>
                                        <th>Unit</th>
                                        <th>Opening Qty</th>
                                        <th>Current Stock</th>
                                        <th>Stock Status</th>
                                        <th>Last Updated</th>
                                        <th>Quick Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inventories as $key => $inventory)
                                        @php
                                            $product = $inventory->product;
                                            $currentStock = $inventory->total_qty;
                                            $openingStock = $inventory->opening_qty;
                                            $lastUpdated = $inventory->updated_at->format('d-m-Y H:i');
                                            
                                            // Determine stock status
                                            if ($currentStock <= 0) {
                                                $statusClass = 'stock-out-row';
                                                $statusBadge = '<span class="stock-badge-pill badge-pill-danger"><i class="fa-solid fa-circle-xmark me-1"></i> Out of Stock</span>';
                                                $quantityClass = 'zero-stock';
                                            } elseif ($currentStock <= 10) {
                                                $statusClass = 'stock-low-row';
                                                $statusBadge = '<span class="stock-badge-pill badge-pill-warning"><i class="fa-solid fa-triangle-exclamation me-1"></i> Low Stock</span>';
                                                $quantityClass = 'low-stock';
                                            } elseif ($currentStock <= 50) {
                                                $statusClass = '';
                                                $statusBadge = '<span class="stock-badge-pill badge-pill-info"><i class="fa-solid fa-circle-info me-1"></i> Medium Stock</span>';
                                                $quantityClass = 'good-stock';
                                            } else {
                                                $statusClass = '';
                                                $statusBadge = '<span class="stock-badge-pill badge-pill-success"><i class="fa-solid fa-circle-check me-1"></i> Good Stock</span>';
                                                $quantityClass = 'good-stock';
                                            }
                                        @endphp
                                        <tr class="{{ $statusClass }}">
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                <strong class="text-slate-800">{{ $product->product_name ?? 'N/A' }}</strong>
                                                @if($currentStock <= 0)
                                                    <br><small class="text-danger"><i class="fa-solid fa-circle-exclamation me-1"></i> Needs immediate restocking</small>
                                                @elseif($currentStock <= 10)
                                                    <br><small class="text-warning"><i class="fa-solid fa-triangle-exclamation me-1"></i> Running low</small>
                                                @endif
                                            </td>
                                            <td>{{ $product->unit ? $product->unit->name : 'N/A' }}</td>
                                            <td class="text-right">{{ number_format($openingStock, 2) }}</td>
                                            <td class="text-right">
                                                <span class="stock-quantity {{ $quantityClass }}">
                                                    {{ number_format($currentStock, 2) }}
                                                </span>
                                            </td>
                                            <td>{!! $statusBadge !!}</td>
                                            <td>
                                                <div>{{ $lastUpdated }}</div>
                                                @if($inventory->created_by)
                                                    <small class="text-muted">By: {{ $inventory->created_by }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($product)
                                                    <div class="d-flex align-items-center gap-1">
                                                        <a href="{{ route('purchases.create') }}?product_id={{ $product->id }}" 
                                                           class="btn-action-custom text-success" 
                                                           style="background-color: rgba(16, 185, 129, 0.1); color: #10b981;"
                                                           title="Add Purchase">
                                                            <i class="fa-solid fa-plus"></i>
                                                        </a>
                                                        <a href="{{ route('stock-outs.create') }}?product_id={{ $product->id }}" 
                                                           class="btn-action-custom text-danger" 
                                                           style="background-color: rgba(239, 68, 68, 0.1); color: #ef4444;"
                                                           title="Stock Out">
                                                            <i class="fa-solid fa-minus"></i>
                                                        </a>
                                                        @if($currentStock <= 10)
                                                            <a href="{{ route('purchases.create') }}?product_id={{ $product->id }}&quantity={{ max(50, $currentStock * 2) }}" 
                                                               class="btn-action-custom text-warning" 
                                                               style="background-color: rgba(245, 158, 11, 0.1); color: #d97706;"
                                                               title="Quick Restock">
                                                                <i class="fa-solid fa-bolt"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>

                   {{--  <div class="card-footer">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info mb-0">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <span class="badge badge-danger">Out of Stock</span> = 0 units
                                        </div>
                                        <div class="col-md-3">
                                            <span class="badge badge-warning">Low Stock</span> = 1-10 units
                                        </div>
                                        <div class="col-md-3">
                                            <span class="badge badge-info">Medium Stock</span> = 11-50 units
                                        </div>
                                        <div class="col-md-3">
                                            <span class="badge badge-success">Good Stock</span> = > 50 units
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>

    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
@include('includes.script')

<script>
    function refreshInventory() {
        const refreshBtn = $('.refresh-btn');
        refreshBtn.html('<i class="fa-solid fa-arrows-rotate fa-spin"></i> Refreshing...');
        refreshBtn.prop('disabled', true);
        
        setTimeout(function() {
            location.reload();
        }, 500);
    }

    $(document).ready(function() {
        // Initialize DataTable
        $('#inventoryTable').DataTable({
            "order": [[4, "asc"]], // Sort by current stock ascending (lowest first)
            "pageLength": 25,
            "dom": 'Bfrtip',
            "buttons": [
                {
                    extend: 'excel',
                    text: '<i class="fa-solid fa-file-excel"></i> Export Excel',
                    className: 'btn btn-success',
                    title: 'Live_Inventory_Report_' + new Date().toISOString().split('T')[0],
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fa-solid fa-print"></i> Print',
                    className: 'btn btn-info',
                    title: 'Live Inventory Report - ' + new Date().toLocaleDateString(),
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    },
                    customize: function (win) {
                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', '10pt');
                        $(win.document.body)
                            .css('font-size', '10pt')
                            .prepend('<h3>Live Inventory Report</h3><p>Generated on: ' + new Date().toLocaleString() + '</p>');
                    }
                }
            ],
            "language": {
                "emptyTable": "No inventory data available",
                "search": "Search products:",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                },
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ items"
            }
        });
        
        // Auto-refresh every 2 minutes (120000 ms)
        setInterval(refreshInventory, 120000);
    });
    
    // Keyboard shortcut for refresh (Ctrl + R)
    $(document).keydown(function(e) {
        if (e.ctrlKey && e.keyCode === 82) {
            e.preventDefault();
            refreshInventory();
        }
    });
</script>

</body>
</html>