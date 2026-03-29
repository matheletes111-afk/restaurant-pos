@extends('layouts.app')

@section('title')
<title>Admin || Manage Role</title>
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
              <h5 class="m-b-10">Manage Role</h5>
            </div>
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item" aria-current="page">Role</li>
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
              <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">+ Add Role</button>
            </div>

            <div class="dt-responsive table-responsive">
              <table id="roleTable" class="table table-striped table-bordered nowrap">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($data as $value)
                  <tr>
                    <td>{{ @$value->title }}</td>
                    @php
                      $string = @$value->description;
                      $strippedString = strip_tags($string);
                      $shortenedString = \Illuminate\Support\Str::limit($strippedString, 100);
                    @endphp
                    <td>{{ $shortenedString }}</td>
                    <td>
                      <a href="javascript:void(0)"
                         class="btn btn-primary btn-sm editBtn"
                         data-id="{{ $value->id }}"
                         data-title="{{ $value->title }}"
                         data-description="{{ $value->description }}">
                         <i class="fas fa-edit"></i>
                      </a>
                      <a onclick="return confirm('Are you sure want to delete this?')" 
                         href="{{ route('manage.operations.role.management.delete', $value->id) }}" 
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

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <form action="{{ route('manage.operations.role.management.insert') }}" method="POST">
            @csrf
            <div class="modal-header">
              <h5 class="modal-title">Add Role</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
              <div class="mb-3">
                <label>Role Name</label>
                <input type="text" class="form-control" name="title" required>
              </div>
              <div class="mb-3">
                <label>Description</label>
                <textarea class="form-control" name="description" rows="3"></textarea>
              </div>
            </div>

            <div class="modal-footer">
              <button type="submit" class="btn btn-success">Save</button>
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
          <form action="{{ route('manage.operations.role.management.update') }}" method="POST">
            @csrf
            <input type="hidden" name="id" id="edit_id">

            <div class="modal-header">
              <h5 class="modal-title">Edit Role</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
              <div class="mb-3">
                <label>Role Name</label>
                <input type="text" class="form-control" id="edit_title" name="title" required>
              </div>
              <div class="mb-3">
                <label>Description</label>
                <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
              </div>
            </div>

            <div class="modal-footer">
              <button type="submit" class="btn btn-success">Update</button>
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
  $('#roleTable').DataTable();

  $(".editBtn").click(function(){
      $("#edit_id").val($(this).data("id"));
      $("#edit_title").val($(this).data("title"));
      $("#edit_description").val($(this).data("description"));
      $("#editModal").modal("show");
  });
});
</script>
@endsection
