@extends('layouts.app')

@section('title')
<title>Admin || Manage Admin Users</title>
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
              <h5 class="m-b-10">Manage Admin Users</h5>
            </div>
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item" aria-current="page">Admin Users</li>
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
              <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">+ Add Admin User</button>
            </div>

            <div class="dt-responsive table-responsive">
              <table id="usersTable" class="table table-striped table-bordered nowrap">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Assigned Permissions</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($users as $value)
                  @php
                    $permissionsList = [];
                    $perms = $value->permissions ?? [];
                    if (in_array('restaurant_master', $perms)) $permissionsList[] = 'Restaurant Master';
                    if (in_array('plan_master', $perms)) $permissionsList[] = 'Plan Master';
                    if (in_array('payment_history', $perms)) $permissionsList[] = 'Payment History';
                    if (in_array('admin_crm', $perms)) $permissionsList[] = 'Admin CRM';
                    if (in_array('customer_support', $perms)) $permissionsList[] = 'Customer Support';
                    if (in_array('admin_user_management', $perms)) $permissionsList[] = 'Admin Users';
                  @endphp
                  <tr>
                    <td>{{ $value->name }}</td>
                    <td>{{ $value->email }}</td>
                    <td>{{ $value->phone }}</td>
                    <td>
                      @if(count($permissionsList) > 0)
                        @foreach($permissionsList as $p)
                          <span class="badge bg-info text-dark">{{ $p }}</span>
                        @endforeach
                      @else
                        <span class="badge bg-secondary">No Permissions</span>
                      @endif
                    </td>
                    <td>
                      <a href="javascript:void(0)"
                         class="btn btn-primary btn-sm editBtn"
                         data-id="{{ $value->id }}"
                         data-name="{{ $value->name }}"
                         data-email="{{ $value->email }}"
                         data-phone="{{ $value->phone }}"
                         data-permissions="{{ json_encode($perms) }}">
                         <i class="fas fa-edit"></i>
                      </a>
                      <a href="{{ route('admin.users.delete', $value->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Delete this admin user?')">
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
          <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="modal-header">
              <h5 class="modal-title">Add Admin User</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body row g-3">
              <div class="col-md-6">
                <label>Name *</label>
                <input type="text" class="form-control" name="name" required>
              </div>
              <div class="col-md-6">
                <label>Email *</label>
                <input type="email" class="form-control" name="email" required>
              </div>
              <div class="col-md-6">
                <label>Phone *</label>
                <input type="text" class="form-control" name="phone" required>
              </div>
              <div class="col-md-6">
                <label>Password *</label>
                <input type="password" name="password" class="form-control" required minlength="6">
              </div>
              
              <div class="col-md-12">
                <label class="form-label d-block"><strong>Assign Permission Menu</strong></label>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="permissions[]" value="restaurant_master" id="perm_restaurant_master">
                      <label class="form-check-label" for="perm_restaurant_master">Restaurant Master</label>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="permissions[]" value="plan_master" id="perm_plan_master">
                      <label class="form-check-label" for="perm_plan_master">Plan Master</label>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="permissions[]" value="payment_history" id="perm_payment_history">
                      <label class="form-check-label" for="perm_payment_history">Payment History</label>
                    </div>
                  </div>
                  <div class="col-md-4 mt-2">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="permissions[]" value="admin_crm" id="perm_admin_crm">
                      <label class="form-check-label" for="perm_admin_crm">Admin CRM</label>
                    </div>
                  </div>
                  <div class="col-md-4 mt-2">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="permissions[]" value="customer_support" id="perm_customer_support">
                      <label class="form-check-label" for="perm_customer_support">Customer Support</label>
                    </div>
                  </div>
                  <div class="col-md-4 mt-2">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="permissions[]" value="admin_user_management" id="perm_admin_user_management">
                      <label class="form-check-label" for="perm_admin_user_management">Admin Users</label>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <button type="submit" class="btn btn-success">Save Admin User</button>
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
          <form action="{{ route('admin.users.update') }}" method="POST">
            @csrf
            <input type="hidden" name="id" id="edit_id">

            <div class="modal-header">
              <h5 class="modal-title">Edit Admin User</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body row g-3">
              <div class="col-md-6">
                <label>Name *</label>
                <input type="text" class="form-control" id="edit_name" name="name" required>
              </div>
              <div class="col-md-6">
                <label>Email *</label>
                <input type="email" class="form-control" id="edit_email" name="email" required>
              </div>
              <div class="col-md-6">
                <label>Phone *</label>
                <input type="text" class="form-control" id="edit_phone" name="phone" required>
              </div>
              <div class="col-md-6">
                <label>Password (leave blank to keep current)</label>
                <input type="password" name="password" class="form-control" minlength="6">
              </div>

              <div class="col-md-12">
                <label class="form-label d-block"><strong>Assign Permission Menu</strong></label>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-check">
                      <input class="form-check-input edit-perm-checkbox" type="checkbox" name="permissions[]" value="restaurant_master" id="edit_perm_restaurant_master">
                      <label class="form-check-label" for="edit_perm_restaurant_master">Restaurant Master</label>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-check">
                      <input class="form-check-input edit-perm-checkbox" type="checkbox" name="permissions[]" value="plan_master" id="edit_perm_plan_master">
                      <label class="form-check-label" for="edit_perm_plan_master">Plan Master</label>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-check">
                      <input class="form-check-input edit-perm-checkbox" type="checkbox" name="permissions[]" value="payment_history" id="edit_perm_payment_history">
                      <label class="form-check-label" for="edit_perm_payment_history">Payment History</label>
                    </div>
                  </div>
                  <div class="col-md-4 mt-2">
                    <div class="form-check">
                      <input class="form-check-input edit-perm-checkbox" type="checkbox" name="permissions[]" value="admin_crm" id="edit_perm_admin_crm">
                      <label class="form-check-label" for="edit_perm_admin_crm">Admin CRM</label>
                    </div>
                  </div>
                  <div class="col-md-4 mt-2">
                    <div class="form-check">
                      <input class="form-check-input edit-perm-checkbox" type="checkbox" name="permissions[]" value="customer_support" id="edit_perm_customer_support">
                      <label class="form-check-label" for="edit_perm_customer_support">Customer Support</label>
                    </div>
                  </div>
                  <div class="col-md-4 mt-2">
                    <div class="form-check">
                      <input class="form-check-input edit-perm-checkbox" type="checkbox" name="permissions[]" value="admin_user_management" id="edit_perm_admin_user_management">
                      <label class="form-check-label" for="edit_perm_admin_user_management">Admin Users</label>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <button type="submit" class="btn btn-success">Update Admin User</button>
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
  $('#usersTable').DataTable();

  $(".editBtn").click(function(){
    $("#edit_id").val($(this).data("id"));
    $("#edit_name").val($(this).data("name"));
    $("#edit_email").val($(this).data("email"));
    $("#edit_phone").val($(this).data("phone"));
    
    // Reset checkboxes
    $(".edit-perm-checkbox").prop("checked", false);
    
    // Check assigned permissions
    var perms = $(this).data("permissions");
    if(Array.isArray(perms)) {
      perms.forEach(function(perm){
        $("#edit_perm_" + perm).prop("checked", true);
      });
    }
    
    $("#editModal").modal("show");
  });
});
</script>
@endsection
