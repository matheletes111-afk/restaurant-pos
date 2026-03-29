@extends('layouts.app')

@section('title')
<title>Admin || Manage Staff</title>
@endsection

@section('style')
@include('includes.style')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
@endsection

@section('body')
@include('includes.sidebar')
<div class="pc-container">
  <div class="pc-content">

    <!-- Breadcrumb -->
    <div class="page-header">
      <div class="page-block">
        <div class="row align-items-center">
          <div class="col-md-12">
            <div class="page-header-title">
              <h5 class="m-b-10">Manage Staff</h5>
            </div>
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item" aria-current="page">Staff</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <!-- End Breadcrumb -->

    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          @include('includes.message')

          <div class="card-body">
            <div class="d-flex justify-content-end mb-3">
              <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">+ Add Staff</button>
            </div>

            <div class="dt-responsive table-responsive">
              <table id="staffTable" class="table table-striped table-bordered nowrap">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Role</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($data as $value)
                  <tr>
                    <td>{{ $value->name }}</td>
                    <td>{{ $value->email }}</td>
                    <td>{{ $value->phone }}</td>
                    <td>
                      @if($value->status=="Active")
                        <span class="badge bg-success">Active</span>
                      @else
                        <span class="badge bg-danger">Inactive</span>
                      @endif
                    </td>
                    <td>{{ $value->role_name->title ?? '--' }}</td>
                    <td>
                      <a href="javascript:void(0)"
                         class="btn btn-primary btn-sm editBtn"
                         data-id="{{ $value->id }}"
                         data-name="{{ $value->name }}"
                         data-email="{{ $value->email }}"
                         data-phone="{{ $value->phone }}"
                         data-status="{{ $value->status }}"
                         data-role="{{ $value->role_id }}"
                         data-address="{{ $value->address }}">
                         <i class="fas fa-edit"></i>
                      </a>
                         <a href="{{route('manage.operations.role.management.delete',$value->id)}}" class="btn btn-danger btn-sm" onclick="return confirm('Delete this staff?')">
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

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form action="{{ route('manage.operations.staff.management.insert') }}" method="POST">
            @csrf
            <div class="modal-header">
              <h5 class="modal-title">Add Staff</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body row g-3">
              <div class="col-md-6">
                <label>Name</label>
                <input type="text" class="form-control" name="name" required>
              </div>
              <div class="col-md-6">
                <label>Email</label>
                <input type="email" class="form-control" name="email" required>
              </div>
              <div class="col-md-6">
                <label>Phone</label>
                <input type="text" class="form-control" name="phone" required>
              </div>
              <div class="col-md-6">
                <label>Role</label>
                <select class="form-control" name="role_id" required>
                  <option value="">Select Role</option>
                  @foreach($role as $r)
                  <option value="{{ $r->id }}">{{ $r->title }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-12">
                <label>Address</label>
                <textarea class="form-control" name="address" rows="2"></textarea>
              </div>
              <div class="col-md-6">
                <label>Status</label>
                <select class="form-control" name="status">
                  <option value="Active">Active</option>
                  <option value="Inactive">Inactive</option>
                </select>
              </div>

              <div class="col-md-6">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
              </div>
            </div>

            <div class="modal-footer">
              <button type="submit" class="btn btn-success">Save Staff</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form action="{{ route('manage.operations.staff.management.update') }}" method="POST">
            @csrf
            <input type="hidden" name="id" id="edit_id">

            <div class="modal-header">
              <h5 class="modal-title">Edit Staff</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body row g-3">
              <div class="col-md-6">
                <label>Name</label>
                <input type="text" class="form-control" id="edit_name" name="name" required>
              </div>
              <div class="col-md-6">
                <label>Email</label>
                <input type="email" class="form-control" id="edit_email" name="email" required>
              </div>
              <div class="col-md-6">
                <label>Phone</label>
                <input type="text" class="form-control" id="edit_phone" name="phone" required>
              </div>
              <div class="col-md-6">
                <label>Role</label>
                <select class="form-control" id="edit_role" name="role_id" required>
                  @foreach($role as $r)
                  <option value="{{ $r->id }}">{{ $r->title }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-12">
                <label>Address</label>
                <textarea class="form-control" id="edit_address" name="address" rows="2"></textarea>
              </div>
              <div class="col-md-6">
                <label>Status</label>
                <select class="form-control" id="edit_status" name="status">
                  <option value="Active">Active</option>
                  <option value="Inactive">Inactive</option>
                </select>
              </div>
              
            </div>

            <div class="modal-footer">
              <button type="submit" class="btn btn-success">Update Staff</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
@include('includes.script')

<script>
$(document).ready(function () {
  $('#staffTable').DataTable({
    "order": [[3, "desc"]]
  });

  $(".editBtn").click(function(){
    $("#edit_id").val($(this).data("id"));
    $("#edit_name").val($(this).data("name"));
    $("#edit_email").val($(this).data("email"));
    $("#edit_phone").val($(this).data("phone"));
    $("#edit_status").val($(this).data("status"));
    $("#edit_role").val($(this).data("role"));
    $("#edit_address").val($(this).data("address"));
    $("#editModal").modal("show");
  });
});
</script>
@endsection
