<!DOCTYPE html>
<html lang="en">
<head>
  <title>Manage Tables</title>
  @include('includes.style')
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>
<body>
@include('includes.sidebar')

<div class="pc-container">
  <div class="pc-content">
    <div class="page-header">
      <h5 class="m-b-10">Manage Tables</h5>
    </div>

    <div class="card">
      @include('includes.message')
      <div class="card-header">
        @if(
                  isset($plan_details)
                  && isset($plan_details->total_number_of_table)
                  && count($tables ?? []) < $plan_details->total_number_of_table
              )
        <button class="btn btn-primary" data-toggle="modal" data-target="#addTableModal" style="float:right;">
          <i class="fa fa-plus"></i> Add Table
        </button>
        @endif
      </div>

      <div class="card-body">
        <div class="table-responsive">
          <table id="tableManage" class="table table-striped table-bordered nowrap">
            <thead>
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Description</th>
                <th>Status</th>
                 <th>QR Code</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($tables as $key => $table)
              <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $table->name }}</td>
                <td>{{ $table->description }}</td>
                <td>
                  @if($table->qr_code)
                      <img src="{{ asset('public/qrcodes/'.$table->qr_code) }}" width="80">
                      <br>
                      <a href="{{ asset('public/qrcodes/'.$table->qr_code) }}" download class="btn btn-sm btn-primary mt-2">
                          Download QR
                      </a>
                  @else
                      <span class="text-danger">No QR</span>
                  @endif
              </td>
                <td>
                  <a href="{{ route('table.manage.status', $table->id) }}"
                     onclick="return confirm('Are you sure you want to change status?')"
                     class="btn btn-sm {{ $table->status == 'A' ? 'btn-success' : 'btn-warning' }}">
                     {{ $table->status == 'A' ? 'Active' : 'Inactive' }}
                  </a>
                </td>
                <td>
                  <button class="btn btn-success editBtn"
                          data-id="{{ $table->id }}"
                          data-name="{{ $table->name }}"
                          data-description="{{ $table->description }}">
                    <i class="fa fa-edit"></i>
                  </button>
                  <a href="{{ route('table.manage.delete', $table->id) }}"
                     onclick="return confirm('Are you sure to delete ?')"
                     class="btn btn-danger"><i class="fa fa-trash"></i></a>
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
<div class="modal fade" id="addTableModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="{{ route('table.manage.insert') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Add Table</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body row">
          <div class="col-md-12 mt-2">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="col-md-12 mt-2">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editTableModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="{{ route('table.manage.update') }}" method="POST">
        @csrf
        <input type="hidden" name="id" id="edit_id">
        <div class="modal-header">
          <h5 class="modal-title">Edit Table</h5>
          <button type="button" class="close" data-dismiss="modal" onclick="location.reload()">&times;</button>
        </div>

        <div class="modal-body row">
          <div class="col-md-12 mt-2">
            <label>Name</label>
            <input type="text" name="name" id="edit_name" class="form-control" required>
          </div>
          <div class="col-md-12 mt-2">
            <label>Description</label>
            <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Update</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="location.reload()">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
@include('includes.script')

<script>
$(document).ready(function() {
  $('#tableManage').DataTable();

  $('.editBtn').on('click', function() {
    $('#edit_id').val($(this).data('id'));
    $('#edit_name').val($(this).data('name'));
    $('#edit_description').val($(this).data('description'));
    $('#editTableModal').modal('show');
  });
});
</script>

</body>
</html>
