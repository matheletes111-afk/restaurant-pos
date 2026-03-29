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
    <style>
        .stock-out { background-color: #f8d7da !important; }
        .stock-low { background-color: #fff3cd !important; }
        .stock-medium { background-color: #d1ecf1 !important; }
        .stock-good { background-color: #d4edda !important; }
        .stock-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .refresh-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }
        .refresh-btn:hover {
            background-color: #218838;
        }
        .refresh-btn i {
            margin-right: 5px;
        }
        .stock-quantity {
            font-weight: bold;
            font-size: 1.1em;
        }
        .zero-stock {
            color: #dc3545;
        }
        .low-stock {
            color: #ffc107;
        }
        .good-stock {
            color: #28a745;
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
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="mb-0">Current Stock Levels</h5>
                                <p class="text-muted mb-0 mt-1">Showing real-time inventory data</p>
                            </div>
                            <div class="col-md-6 text-right">
                                <button onclick="refreshInventory()" class="refresh-btn">
                                    <i class="fa fa-refresh"></i> Refresh
                                </button>
                                
                                <!-- Search Form -->
                                <form method="GET" action="{{ route('inventory.live') }}" class="d-inline">
                                    <div class="input-group" style="width: 250px; display: inline-flex;">
                                        <input type="text" name="search" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="submit">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Stock Filters -->
                                    <div class="btn-group ml-2" role="group">
                                        <a href="{{ route('inventory.live') }}" 
                                           class="btn btn-sm {{ !request('low_stock') && !request('out_of_stock') ? 'btn-primary' : 'btn-outline-primary' }}">
                                            All
                                        </a>
                                        <a href="{{ route('inventory.live') }}?low_stock=1" 
                                           class="btn btn-sm {{ request('low_stock') ? 'btn-warning' : 'btn-outline-warning' }}">
                                            Low Stock (≤10)
                                        </a>
                                        <a href="{{ route('inventory.live') }}?out_of_stock=1" 
                                           class="btn btn-sm {{ request('out_of_stock') ? 'btn-danger' : 'btn-outline-danger' }}">
                                            Out of Stock
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card border-primary">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <i class="fa fa-cubes fa-2x text-primary"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="mb-0">{{ $totalProducts }}</h5>
                                                <p class="text-muted mb-0">Total Items</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-success">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <i class="fa fa-check-circle fa-2x text-success"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="mb-0">{{ $totalProducts - $lowStockItems - $outOfStockItems }}</h5>
                                                <p class="text-muted mb-0">In Stock</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-warning">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <i class="fa fa-exclamation-triangle fa-2x text-warning"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="mb-0">{{ $lowStockItems }}</h5>
                                                <p class="text-muted mb-0">Low Stock (≤10)</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-danger">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <i class="fa fa-times-circle fa-2x text-danger"></i>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="mb-0">{{ $outOfStockItems }}</h5>
                                                <p class="text-muted mb-0">Out of Stock</p>
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
                                                $statusClass = 'stock-out';
                                                $statusText = 'Out of Stock';
                                                $statusBadge = '<span class="stock-badge" style="background-color: #dc3545; color: white;">Out of Stock</span>';
                                                $quantityClass = 'zero-stock';
                                            } elseif ($currentStock <= 10) {
                                                $statusClass = 'stock-low';
                                                $statusText = 'Low Stock';
                                                $statusBadge = '<span class="stock-badge" style="background-color: #ffc107; color: black;">Low</span>';
                                                $quantityClass = 'low-stock';
                                            } elseif ($currentStock <= 50) {
                                                $statusClass = 'stock-medium';
                                                $statusText = 'Medium Stock';
                                                $statusBadge = '<span class="stock-badge" style="background-color: #17a2b8; color: white;">Medium</span>';
                                                $quantityClass = 'good-stock';
                                            } else {
                                                $statusClass = 'stock-good';
                                                $statusText = 'Good Stock';
                                                $statusBadge = '<span class="stock-badge" style="background-color: #28a745; color: white;">Good</span>';
                                                $quantityClass = 'good-stock';
                                            }
                                        @endphp
                                        <tr class="{{ $statusClass }}">
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                <strong>{{ $product->product_name ?? 'N/A' }}</strong>
                                                @if($currentStock <= 0)
                                                    <br><small class="text-danger"><i class="fa fa-exclamation-circle"></i> Needs immediate restocking</small>
                                                @elseif($currentStock <= 10)
                                                    <br><small class="text-warning"><i class="fa fa-exclamation-triangle"></i> Running low</small>
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
                                                {{ $lastUpdated }}
                                                @if($inventory->created_by)
                                                    <br><small class="text-muted">By: {{ $inventory->created_by }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($product)
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="{{ route('purchases.create') }}?product_id={{ $product->id }}" 
                                                           class="btn btn-success" 
                                                           title="Add Purchase">
                                                            <i class="fa fa-plus"></i>
                                                        </a>
                                                        <a href="{{ route('stock-outs.create') }}?product_id={{ $product->id }}" 
                                                           class="btn btn-danger" 
                                                           title="Stock Out">
                                                            <i class="fa fa-minus"></i>
                                                        </a>
                                                        @if($currentStock <= 10)
                                                            <a href="{{ route('purchases.create') }}?product_id={{ $product->id }}&quantity={{ max(50, $currentStock * 2) }}" 
                                                               class="btn btn-warning" 
                                                               title="Quick Restock">
                                                                <i class="fa fa-bolt"></i>
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
    $(document).ready(function() {
        // Initialize DataTable
        $('#inventoryTable').DataTable({
            "order": [[4, "asc"]], // Sort by current stock ascending (lowest first)
            "pageLength": 25,
            "dom": 'Bfrtip',
            "buttons": [
                {
                    extend: 'excel',
                    text: '<i class="fa fa-file-excel-o"></i> Export Excel',
                    className: 'btn btn-success',
                    title: 'Live_Inventory_Report_' + new Date().toISOString().split('T')[0],
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6]
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Print',
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
    
    // function refreshInventory() {
    //     // Show loading indicator
    //     const refreshBtn = $('.refresh-btn');
    //     const originalHtml = refreshBtn.html();
    //     refreshBtn.html('<i class="fa fa-spinner fa-spin"></i> Refreshing...');
    //     refreshBtn.prop('disabled', true);
        
    //     // Reload after showing loading state
    //     setTimeout(function() {
    //         location.reload();
    //     }, 500);
    // }
    
    // Keyboard shortcut for refresh (Ctrl + R)
    $(document).keydown(function(e) {
        if (e.ctrlKey && e.keyCode === 82) {
            e.preventDefault();
            refreshInventory();
        }
    });
    
    // Auto-refresh notification
    // $(document).ready(function() {
    //     setTimeout(function() {
    //         $('<div class="alert alert-light alert-dismissible fade show" role="alert" style="position: fixed; bottom: 20px; right: 20px; z-index: 1000; max-width: 300px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">' +
    //           '<small><i class="fa fa-info-circle text-primary"></i> Auto-refresh every 2 minutes</small><br>' +
    //           '<small class="text-muted">Press Ctrl+R to refresh manually</small>' +
    //           '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
    //           '<span aria-hidden="true">&times;</span>' +
    //           '</button>' +
    //           '</div>').appendTo('body');
    //     }, 2000);
    // });
</script>

</body>
</html>