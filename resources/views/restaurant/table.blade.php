<!DOCTYPE html>
<html lang="en">
<head>
  <title>Manage Tables</title>
  @include('includes.style')
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <style>
    body {
      background-color: #f8fafc;
      font-family: 'Public Sans', sans-serif;
    }

    .page-header {
      background: white;
      padding: 24px;
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(15, 23, 42, 0.02);
      margin-bottom: 25px;
      border: 1px solid rgba(0, 0, 0, 0.03);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .page-header h5 {
      color: #0f172a;
      font-weight: 800;
      font-size: 1.3rem;
      margin-bottom: 0;
      display: flex;
      align-items: center;
    }

    .card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 8px 30px rgba(15, 23, 42, 0.03);
      overflow: hidden;
      background: white;
      border: 1px solid rgba(0, 0, 0, 0.03);
    }

    .card-header {
      background: #f8fafc;
      border-bottom: 1px solid rgba(0, 0, 0, 0.05);
      padding: 18px 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .btn-add-table {
      background: linear-gradient(135deg, #009d1a 0%, #00bc20 100%) !important;
      color: white !important;
      border: none !important;
      border-radius: 30px !important;
      padding: 10px 24px !important;
      font-weight: 700 !important;
      font-size: 0.85rem !important;
      box-shadow: 0 4px 12px rgba(0, 157, 26, 0.15) !important;
      transition: all 0.3s ease !important;
    }

    .btn-add-table:hover {
      background: linear-gradient(135deg, #00bc20 0%, #009d1a 100%) !important;
      transform: translateY(-1px) !important;
      box-shadow: 0 6px 18px rgba(0, 157, 26, 0.25) !important;
    }

    /* Table styles */
    .table-responsive {
      padding: 10px;
    }

    #tableManage {
      border-collapse: collapse !important;
      width: 100% !important;
    }

    #tableManage thead th {
      background: #0f172a;
      color: white;
      font-weight: 700;
      font-size: 0.8rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      padding: 14px 18px;
      border: none;
    }

    #tableManage tbody td {
      padding: 16px 18px;
      vertical-align: middle;
      border-bottom: 1px solid #e2e8f0;
      font-size: 0.9rem;
      color: #334155;
    }

    #tableManage tbody tr:hover {
      background-color: #f8fafc;
    }

    .qr-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
    }

    .qr-img {
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 6px;
      background: #ffffff;
      box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }

    .btn-download-qr {
      background: #0f172a;
      color: white;
      font-weight: 700;
      font-size: 0.75rem;
      border-radius: 30px;
      padding: 6px 16px;
      border: none;
      transition: all 0.2s ease;
      text-decoration: none;
    }

    .btn-download-qr:hover {
      background: #009d1a;
      color: white;
      box-shadow: 0 4px 10px rgba(0, 157, 26, 0.2);
    }

    .status-pill {
      font-weight: 700;
      font-size: 0.8rem;
      padding: 6px 16px;
      border-radius: 30px;
      display: inline-block;
      text-decoration: none;
      transition: all 0.2s ease;
    }

    .status-pill-active {
      background: #d1fae5;
      color: #065f46;
      border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .status-pill-active:hover {
      background: #a7f3d0;
      color: #047857;
    }

    .status-pill-inactive {
      background: #fee2e2;
      color: #991b1b;
      border: 1px solid rgba(239, 68, 68, 0.2);
    }

    .status-pill-inactive:hover {
      background: #fecaca;
      color: #b91c1c;
    }

    /* Actions */
    .btn-edit-action {
      background: rgba(0, 157, 26, 0.1);
      color: #009d1a;
      border: 1px solid rgba(0, 157, 26, 0.2);
      border-radius: 8px;
      width: 34px;
      height: 34px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s ease;
      cursor: pointer;
    }

    .btn-edit-action:hover {
      background: #009d1a;
      color: white;
      transform: translateY(-1px);
    }

    .btn-delete-action {
      background: rgba(239, 68, 68, 0.1);
      color: #ef4444;
      border: 1px solid rgba(239, 68, 68, 0.2);
      border-radius: 8px;
      width: 34px;
      height: 34px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s ease;
      text-decoration: none;
    }

    .btn-delete-action:hover {
      background: #ef4444;
      color: white;
      transform: translateY(-1px);
    }
  </style>
</head>
<body>
@include('includes.sidebar')

<div class="pc-container">
  <div class="pc-content">
    
    <!-- Header -->
    <div class="page-header">
      <h5><i class="fas fa-table text-success me-2"></i> Manage Tables</h5>
      @if(
          isset($plan_details)
          && isset($plan_details->total_number_of_table)
          && count($tables ?? []) < $plan_details->total_number_of_table
      )
        <button class="btn btn-add-table" data-toggle="modal" data-target="#addTableModal">
          <i class="fa fa-plus-circle me-1"></i> Add Table
        </button>
      @endif
    </div>

    @include('includes.message')

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table id="tableManage" class="table table-striped table-bordered nowrap">
            <thead>
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Description</th>
                <th>QR Code</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($tables as $key => $table)
              <tr>
                <td>{{ $key + 1 }}</td>
                <td class="font-weight-bold text-dark">{{ $table->name }}</td>
                <td>{{ $table->description ?? '-' }}</td>
                <td>
                  <div class="qr-container">
                    @if($table->qr_code)
                      <img src="{{ asset('qrcodes/'.$table->qr_code) }}" class="qr-img" width="80" alt="Table QR">
                      <a href="{{ asset('qrcodes/'.$table->qr_code) }}" download class="btn-download-qr">
                        <i class="fas fa-download me-1"></i> Download
                      </a>
                    @else
                      <span class="badge bg-light-danger text-danger"><i class="fas fa-times-circle"></i> No QR Code</span>
                    @endif
                  </div>
                </td>
                <td>
                  <a href="{{ route('table.manage.status', $table->id) }}"
                     onclick="return confirm('Are you sure you want to change status?')"
                     class="status-pill {{ $table->status == 'A' ? 'status-pill-active' : 'status-pill-inactive' }}">
                     <i class="fas {{ $table->status == 'A' ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                     {{ $table->status == 'A' ? 'Active' : 'Inactive' }}
                  </a>
                </td>
                <td>
                  <div class="d-flex gap-2">
                    <button class="btn-edit-action editBtn"
                            data-id="{{ $table->id }}"
                            data-name="{{ $table->name }}"
                            data-description="{{ $table->description }}">
                      <i class="fa fa-edit"></i>
                    </button>
                    <a href="{{ route('table.manage.delete', $table->id) }}"
                       onclick="return confirm('Are you sure you want to delete this table?')"
                       class="btn-delete-action"><i class="fa fa-trash"></i></a>
                  </div>
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
          <h5 class="modal-title"><i class="fas fa-plus text-success me-2"></i> Add Table</h5>
          <button type="button" class="btn-close-custom" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body row g-3">
          <div class="col-md-12">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required placeholder="e.g. Table 5">
          </div>
          <div class="col-md-12">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Enter table seating info or details"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-success">Save Table</button>
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
          <h5 class="modal-title"><i class="fas fa-edit text-success me-2"></i> Edit Table</h5>
          <button type="button" class="btn-close-custom" data-dismiss="modal" onclick="location.reload()">&times;</button>
        </div>

        <div class="modal-body row g-3">
          <div class="col-md-12">
            <label>Name</label>
            <input type="text" name="name" id="edit_name" class="form-control" required>
          </div>
          <div class="col-md-12">
            <label>Description</label>
            <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="location.reload()">Close</button>
          <button type="submit" class="btn btn-success">Update Table</button>
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
