<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Stock Outs</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
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
                            <h5 class="m-b-10">Manage Stock Outs</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item" aria-current="page">Manage Stock Outs</li>
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
                    <div class="card-header">
                        <a href="{{ route('stock-outs.create') }}" class="btn btn-primary" style="float: right;">
                            <i class="fa fa-plus"></i> Add Stock Out
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="dt-responsive table-responsive">
                            <table id="stockOutTable" class="table table-striped table-bordered nowrap">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Stock Out No</th>
                                        <th>Date</th>
                                        <th>Items</th>
                                        <th>Total Quantity</th>
                                        <th>Created By</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stockOuts as $key => $stockOut)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $stockOut->stockout_no }}</td>
                                        <td>{{ date('d-m-Y', strtotime($stockOut->stockout_date)) }}</td>
                                        <td class="text-center">{{ $stockOut->total_items }}</td>
                                        <td class="text-right">{{ number_format($stockOut->total_quantity, 2) }}</td>
                                        <td>{{ $stockOut->user->name ?? 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('stock-outs.show', $stockOut->id) }}" class="btn btn-info btn-sm" title="View">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('stock-outs.edit', $stockOut->id) }}" class="btn btn-success btn-sm" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="{{ route('stock-outs.delete', $stockOut->id) }}" 
                                               class="btn btn-danger btn-sm" 
                                               title="Delete"
                                               onclick="return confirm('Are you sure you want to delete this stock out?')">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
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

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
@include('includes.script')

<script>
    $(document).ready(function() {
        $('#stockOutTable').DataTable({
            "order": [[0, "desc"]],
            "pageLength": 25
        });
    });
</script>

</body>
</html>