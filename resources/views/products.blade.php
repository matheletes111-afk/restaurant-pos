<!DOCTYPE html>
<html lang="en">
<head>
  <title>Manage Products</title>
  @include('includes.style')
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
</head>

<body data-pc-theme="light">
@include('includes.sidebar')

<div class="pc-container">
  <div class="pc-content">

    <div class="page-header">
      <h5 class="m-b-10">Manage Products</h5>
      <div class="float-right">
        {{-- <a href="{{ route('products.import.view') }}" class="btn btn-info mr-2">
          <i class="fa fa-upload"></i> Import Excel
        </a>
        <a href="{{ route('products.download-sample') }}" class="btn btn-secondary mr-2">
          <i class="fa fa-download"></i> Download Sample
        </a> --}}
        <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#addModal">
          <i class="fa fa-plus"></i> Add Product
        </a>
      </div>
    </div>

    @include('includes.message')
    
    @if(session('import_errors'))
    <div class="alert alert-danger">
        <h6>Import Errors:</h6>
        <ul class="mb-0">
            @foreach(session('import_errors') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table id="productTable" class="table table-striped table-bordered nowrap">
            <thead>
              <tr>
                <th>#</th>
                <th>Product Name</th>
                <th>Unit</th>
                
                
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($products as $key => $product)
              <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $product->product_name }}</td>
                <td>{{ $product->unit ? $product->unit->name : 'N/A' }}</td>
                
                
                <td>
                  <button class="btn btn-sm btn-info editBtn"
                          data-id="{{ $product->id }}"
                          data-name="{{ $product->product_name }}"
                          data-unit="{{ $product->unit_id }}"
                          data-qty="{{ $product->opening_qty }}">
                    <i class="fa fa-edit"></i>
                  </button>

                  <a href="{{ route('products.delete', $product->id) }}"
                     onclick="return confirm('Are you sure you want to delete this product?')"
                     class="btn btn-sm btn-danger">
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

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog">
    <form action="{{ route('products.store') }}" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Product</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Product Name *</label>
            <input type="text" name="product_name" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Unit</label>
            <select name="unit_id" class="form-control">
              <option value="">Select Unit</option>
              @foreach($units as $unit)
              <option value="{{ $unit->id }}">{{ $unit->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>Opening Quantity</label>
            <input type="number" name="opening_qty" class="form-control" step="0.01" min="0" value="0">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <form action="{{ route('products.update') }}" method="POST">
      @csrf
      <input type="hidden" name="id" id="edit_id">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Product</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Product Name *</label>
            <input type="text" id="edit_name" name="product_name" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Unit</label>
            <select name="unit_id" id="edit_unit" class="form-control">
              <option value="">Select Unit</option>
              @foreach($units as $unit)
              <option value="{{ $unit->id }}">{{ $unit->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>Opening Quantity</label>
            <input type="number" name="opening_qty" id="edit_qty" class="form-control" step="0.01" min="0">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Update</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Scripts -->
@include('includes.script')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

<script>
  $(document).ready(function() {
    $('#productTable').DataTable({
      dom: 'Bfrtip',
      buttons: [
        {
          extend: 'excel',
          text: '<i class="fa fa-file-excel-o"></i> Export Excel',
          className: 'btn btn-success',
          title: 'Products_List_' + new Date().toISOString().split('T')[0]
        },
        {
          extend: 'pdf',
          text: '<i class="fa fa-file-pdf-o"></i> Export PDF',
          className: 'btn btn-danger'
        }
      ]
    });

    // Edit modal open
    $('.editBtn').on('click', function() {
      $('#edit_id').val($(this).data('id'));
      $('#edit_name').val($(this).data('name'));
      $('#edit_unit').val($(this).data('unit'));
      $('#edit_qty').val($(this).data('qty'));
      $('#editModal').modal('show');
    });
  });
</script>
</body>
</html>