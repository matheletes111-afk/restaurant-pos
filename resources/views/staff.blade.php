@extends('layouts.app')

@section('title')
<title>Admin || Manage Restaurant Staff</title>
@endsection

@section('style')
@include('includes.style')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
@endsection

@section('body')
@include('includes.sidebar')

<div class="pc-container">
<div class="pc-content">

    <div class="page-header">
        <div class="page-block">
            <h5 class="m-b-10">Manage Restaurant Staff</h5>
        </div>
    </div>

    <div class="card">
        @include('includes.message')

        <div class="card-body">
            <div class="text-end mb-3">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">+ Add Staff</button>
            </div>
            <div class="table-responsive">
            <table id="staffTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Name</th><th>Email</th><th>Phone</th>
                        <th>Role</th><th>Status</th><th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($data as $value)
                    <tr>
                        <td>{{ $value->name }}</td>
                        <td>{{ $value->email }}</td>
                        <td>{{ $value->phone }}</td>
                        <td>{{ $value->role_type }}</td>

                        <td>
                          @if($value->status=="A")
                            <a  href="{{route('restaurant.staff.status',@$value->id)}}" class="badge bg-success">Active</a>
                          @else
                            <a href="{{route('restaurant.staff.status',@$value->id)}}"  class="badge bg-danger">Inactive</a>
                          @endif
                        </td>

                        <td>
                            <button class="btn btn-primary btn-sm editBtn"
                                data-id="{{ $value->id }}"
                                data-name="{{ $value->name }}"
                                data-email="{{ $value->email }}"
                                data-phone="{{ $value->phone }}"
                                data-role="{{ $value->role_type }}"
                                data-address="{{ $value->address }}"
                                data-pincode="{{ $value->pincode }}"
                                data-status="{{ $value->status }}">
                                <i class="fas fa-edit"></i>
                            </button>

                            <a href="{{ route('restaurant.staff.delete',$value->id) }}"
                               onclick="return confirm('Delete this staff?')"
                               class="btn btn-danger btn-sm">
                               <i class="fas fa-trash"></i>
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

{{-- ADD MODAL --}}
<div class="modal fade" id="addModal">
<div class="modal-dialog modal-lg">
<div class="modal-content">

<form action="{{ route('restaurant.staff.insert') }}" method="POST">
@csrf

<div class="modal-header">
    <h5>Add Staff</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body row g-3">

    <div class="col-md-6">
        <label>Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="col-md-6">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>

    <div class="col-md-6">
        <label>Phone</label>
        <input type="text" name="phone" class="form-control" required>
    </div>

    <div class="col-md-6">
        <label>Role Type</label>
        <select name="role_type" class="form-control" required>
            <option value="">Select Role</option>
            <option value="Manager">Manager</option>
            <option value="Cashier">Cashier</option>
            <option value="Waiter">Waiter</option>
            <option value="Kitchen Staff">Kitchen Staff</option>
        </select>
    </div>

    <div class="col-md-12">
        <label>Address</label>
        <textarea class="form-control" name="address"></textarea>
    </div>

    <div class="col-md-6">
        <label>Pincode</label>
        <input type="text" name="pincode" class="form-control">
    </div>

    <div class="col-md-6">
        <label>Status</label>
        <select name="status" class="form-control">
            <option value="A">Active</option>
            <option value="I">Inactive</option>
        </select>
    </div>

    <div class="col-md-6">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>

</div>

<div class="modal-footer">
    <button class="btn btn-success">Save</button>
</div>

</form>
</div>
</div>
</div>


{{-- EDIT MODAL --}}
<div class="modal fade" id="editModal">
<div class="modal-dialog modal-lg">
<div class="modal-content">

<form action="{{ route('restaurant.staff.update') }}" method="POST">
@csrf

<input type="hidden" name="id" id="edit_id">

<div class="modal-header">
    <h5>Edit Staff</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body row g-3">

    <div class="col-md-6">
        <label>Name</label>
        <input type="text" class="form-control" id="edit_name" name="name">
    </div>

    <div class="col-md-6">
        <label>Email</label>
        <input type="email" class="form-control" id="edit_email" name="email">
    </div>

    <div class="col-md-6">
        <label>Phone</label>
        <input type="text" class="form-control" id="edit_phone" name="phone">
    </div>

    <div class="col-md-6">
        <label>Role Type</label>
        <select class="form-control" id="edit_role" name="role_type">
            <option value="Manager">Manager</option>
            <option value="Cashier">Cashier</option>
            <option value="Waiter">Waiter</option>
            <option value="Kitchen Staff">Kitchen Staff</option>
        </select>
    </div>

    <div class="col-md-12">
        <label>Address</label>
        <textarea class="form-control" id="edit_address" name="address"></textarea>
    </div>

    <div class="col-md-6">
        <label>Pincode</label>
        <input type="text" class="form-control" id="edit_pincode" name="pincode">
    </div>

    <div class="col-md-6">
        <label>Status</label>
        <select class="form-control" id="edit_status" name="status">
            <option value="A">Active</option>
            <option value="I">Inactive</option>
        </select>
    </div>

</div>

<div class="modal-footer">
    <button class="btn btn-success">Update</button>
</div>

</form>
</div>
</div>
</div>

@include('includes.script')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap 5 JS (Required for Modal) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $('#staffTable').DataTable();

        $(".editBtn").click(function(){
            $("#edit_id").val($(this).data("id"));
            $("#edit_name").val($(this).data("name"));
            $("#edit_email").val($(this).data("email"));
            $("#edit_phone").val($(this).data("phone"));
            $("#edit_role").val($(this).data("role"));
            $("#edit_address").val($(this).data("address"));
            $("#edit_pincode").val($(this).data("pincode"));
            $("#edit_status").val($(this).data("status"));
            
            var modal = new bootstrap.Modal(document.getElementById('editModal'));
            modal.show();
        });
    });
</script>


@endsection
