<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Suppliers</title>
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
                            <h5 class="m-b-10">Manage Suppliers</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item" aria-current="page">Manage Suppliers</li>
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
                        <a href="#" class="btn btn-primary" style="float: right;" data-toggle="modal" data-target="#addModal">
                            <i class="fa fa-plus"></i> Add Supplier
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="dt-responsive table-responsive">
                            <table id="supplierTable" class="table table-striped table-bordered nowrap">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Supplier Name</th>
                                        <th>Shop Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Address</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($suppliers as $key => $supplier)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $supplier->supplier_name }}</td>
                                        <td>{{ $supplier->shop_name ?? 'N/A' }}</td>
                                        <td>{{ $supplier->phone }}</td>
                                        <td>{{ $supplier->email ?? 'N/A' }}</td>
                                        <td>{{ Str::limit($supplier->address, 30) }}</td>
                                        <td>
                                            <button class="btn btn-success edit-btn"
                                                    data-id="{{ $supplier->id }}"
                                                    data-name="{{ $supplier->supplier_name }}"
                                                    data-shop="{{ $supplier->shop_name }}"
                                                    data-phone="{{ $supplier->phone }}"
                                                    data-email="{{ $supplier->email }}"
                                                    data-address="{{ $supplier->address }}"
                                                    data-outstanding="{{ $supplier->opening_outstanding }}">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                                <a href="{{ route('suppliers.ledger', $supplier->id) }}" class="btn btn-warning btn-sm" title="View Ledger">
                                                    <i class="fa fa-book"></i> Ledger
                                                </a>
                                            <a href="{{ route('suppliers.delete', $supplier->id) }}"
                                               class="btn btn-danger"
                                               onclick="return confirm('Are you sure you want to delete this supplier?')">
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

<!-- Add Supplier Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('suppliers.store') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add Supplier</h5>
                    <button type="button" onclick="location.reload()" class="close" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Supplier Name *</label>
                        <input type="text" name="supplier_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Shop Name</label>
                        <input type="text" name="shop_name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Phone Number *</label>
                        <input type="text" name="phone" class="form-control" required maxlength="20">
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Opening Outstanding (₹)</label>
                        <input type="number" name="opening_outstanding" class="form-control" step="0.01" min="0" value="0">
                        <small class="text-muted">Leave as 0 if no outstanding amount</small>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" onclick="location.reload()" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Supplier Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('suppliers.update') }}">
            @csrf
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Supplier</h5>
                    <button type="button" class="close" onclick="location.reload()" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Supplier Name *</label>
                        <input type="text" name="supplier_name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Shop Name</label>
                        <input type="text" name="shop_name" id="edit_shop" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Phone Number *</label>
                        <input type="text" name="phone" id="edit_phone" class="form-control" required maxlength="20">
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" id="edit_email" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Opening Outstanding (₹)</label>
                        <input type="number" name="opening_outstanding" id="edit_outstanding" class="form-control" step="0.01" min="0">
                        <small class="text-muted">Note: Changing this will reset current outstanding</small>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" id="edit_address" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" onclick="location.reload()" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
@include('includes.script')

<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#supplierTable').DataTable();

        // Edit Button
        $('.edit-btn').on('click', function() {
            let id = $(this).data('id');
            let name = $(this).data('name');
            let shop = $(this).data('shop');
            let phone = $(this).data('phone');
            let email = $(this).data('email');
            let address = $(this).data('address');
            let outstanding = $(this).data('outstanding');

            $('#edit_id').val(id);
            $('#edit_name').val(name);
            $('#edit_shop').val(shop);
            $('#edit_phone').val(phone);
            $('#edit_email').val(email);
            $('#edit_address').val(address);
            $('#edit_outstanding').val(outstanding);
            
            $('#editModal').modal('show');
        });
        
        // Format phone number input
        $('input[name="phone"]').on('input', function() {
            var value = $(this).val().replace(/\D/g, '');
            $(this).val(value);
        });
        
        // Format outstanding amount input
        $('input[name="opening_outstanding"]').on('input', function() {
            var value = $(this).val();
            if (value < 0) {
                $(this).val(0);
            }
        });
    });
</script>

</body>
</html>