<!DOCTYPE html>
<html lang="en">
<head>
  <title>Admin - Manage Units</title>
  @include('includes.style')
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>

<body data-pc-theme="light">

@include('includes.sidebar')

<div class="pc-container">
  <div class="pc-content">

    <div class="page-header">
      <div class="page-block">
        <h5 class="m-b-10">Manage Units</h5>
      </div>
    </div>

    <div class="row">
      <div class="col-sm-12">
        <div class="card">

          @include('includes.message')

          <div class="card-header">
            <a href="#" class="btn btn-primary" style="float:right;" data-toggle="modal" data-target="#addUnitModal">
              <i class="fa fa-plus"></i> Add Unit
            </a>
          </div>

          <div class="card-body">
            <table id="unitsTable" class="table table-striped table-bordered">
              <thead>
                <tr>
                  {{-- <th>ID</th> --}}
                  <th>Unit Name</th>
                  <th>Action</th>
                </tr>
              </thead>

              <tbody>
                @foreach($data as $value)
                <tr>
                  {{-- <td>{{ $value->id }}</td> --}}
                  <td>{{ $value->name }}</td>
                  <td>
                    <button class="btn btn-success btn-sm edit-btn"
                      data-id="{{ $value->id }}"
                      data-name="{{ $value->name }}">
                      <i class="fa fa-edit"></i>
                    </button>

                    <a href="{{ route('manage.units.delete', $value->id) }}"
                      class="btn btn-sm btn-danger"
                      onclick="return confirm('Delete this unit?')">
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

<!-- Add Modal -->
<div class="modal fade" id="addUnitModal">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('manage.units.insert') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5>Add Unit</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
          <div class="form-group">
            <label>Unit Name</label>
            <input type="text" name="name" class="form-control" required placeholder="e.g., Kilogram, Liter">
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editUnitModal">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('manage.units.update') }}">
      @csrf
      <input type="hidden" name="id" id="edit_id">

      <div class="modal-content">
        <div class="modal-header">
          <h5>Edit Unit</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
          <div class="form-group">
            <label>Unit Name</label>
            <input type="text" name="name" id="edit_name" class="form-control" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
@include('includes.script')
<script>
  $(document).ready(function() {
    $('#unitsTable').DataTable({
      "order": [[0, "desc"]]
    });

    $(document).on('click', '.edit-btn', function () {
      $('#edit_id').val($(this).data('id'));
      $('#edit_name').val($(this).data('name'));
      $('#editUnitModal').modal('show');
    });
  });
</script>

</body>
</html>