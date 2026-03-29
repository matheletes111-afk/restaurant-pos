<!DOCTYPE html>
<html lang="en">
<head>
  <title>Admin - Manage Product</title>
  @include('includes.style')
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <style>
.custom-file-input:focus ~ .custom-file-label {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
.custom-file-label::after {
    content: "Browse";
}
.table th {
    font-size: 0.85rem;
    font-weight: 600;
}
</style>
</head>

<body data-pc-theme="light">
  <div class="loader-bg">
    <div class="loader-track"><div class="loader-fill"></div></div>
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
                <h5 class="m-b-10">Manage Product of {{ @$details->name }}</h5>
              </div>
              <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('manage.category') }}">Category</a></li>
                <li class="breadcrumb-item" aria-current="page">Product</li>
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
            <div class="row">
              <div class="col-md-6">
                <h5>Product List</h5>
              </div>
              <div class="col-md-6 text-right">
                <!-- Bulk Upload Button -->
                {{-- <button type="button" class="btn btn-info mr-2" data-toggle="modal" data-target="#bulkUploadModal">
                  <i class="fa fa-upload"></i> Bulk Upload
                </button> --}}
                
                <!-- Download Template Button -->
                {{-- <a href="{{ route('manage.subcategory.category.template', $id) }}" class="btn btn-secondary mr-2">
                  <i class="fa fa-download"></i> Download Template
                </a> --}}
                
                <!-- Add Product Button -->
                @if(
                  isset($plan_details)
                  && isset($plan_details->total_number_of_dishes)
                  && count($data ?? []) < $plan_details->total_number_of_dishes
                )
                <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#addProductModal">
                  <i class="fa fa-plus"></i> Add Product
                </a>
                @endif
              </div>
            </div>
          </div>

            <div class="card-body">
              <div class="dt-responsive table-responsive">
                <table id="productTable" class="table table-striped table-bordered nowrap">
                  <thead>
                    <tr>
                      <th>Image</th>
                      <th>Product Name</th>
                      <th>Price (₹)</th>
                      <th>GST (%)</th>
                      <th>Food Type</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach(@$data as $value)
                    <tr>
                      <td>
                        @if($value->image)
                          <img src="{{ URL::to('storage/app/public/category') }}/{{ @$value->image }}" alt="Category" width="60" height="60" style="object-fit: cover; border-radius: 6px;">
                        @else
                          <span class="text-muted">No Image</span>
                        @endif
                      </td>
                      <td>{{ @$value->name }}</td>
                      
                      <td>{{ number_format($value->price, 2) }}</td>
                      <td>{{ $value->gst_rate }}</td>
                      <td>
                        <button class="btn btn-sm {{ $value->food_type == 'VEG' ? 'btn-success' : 'btn-danger' }}" disabled>
  {{ $value->food_type }}
</button>

                      </td>
                      <td>
                        <a href="{{ route('manage.subcategory.category.status', $value->id) }}"
                           onclick="return confirm('Are you sure you want to change the status?')"
                           class="btn btn-sm {{ $value->status == 'A' ? 'btn-success' : 'btn-warning' }}">
                           {{ $value->status == 'A' ? 'Active' : 'Inactive' }}
                        </a>
                      </td>
                      <td>
                        <!-- Edit -->
                        <button class="btn btn-success edit-btn"
                                data-id="{{ $value->id }}"
                                data-name="{{ $value->name }}"
                                data-price="{{ $value->price }}"
                                data-gst="{{ $value->gst_rate }}"
                                data-type="{{ $value->food_type }}">
                          <i class="fa fa-edit"></i>
                        </button>

                        <!-- Delete -->
                        <a href="{{ route('manage.subcategory.category.delete', $value->id) }}"
                           class="btn btn-danger"
                           onclick="return confirm('Are you sure want to delete this product?')">
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

  <!-- Add Product Modal -->
  <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form method="POST" action="{{ route('manage.subcategory.category.insert') }}" enctype="multipart/form-data">

        @csrf
        <input type="hidden" name="category_id" value="{{ @$id }}">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Product</h5>
            <button type="button" class="close" onclick="location.reload()" data-dismiss="modal" aria-label="Close">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Product Name</label>
              <input type="text" name="name" class="form-control" required>
            </div>

            <div class="form-group">
              <label>Product Price (₹)</label>
              <input type="number" step="0.01" name="price" class="form-control" required>
            </div>

            <div class="form-group">
              <label>GST Rate (%)</label>
              <input type="number" step="0.01" name="gst_rate" class="form-control" placeholder="e.g. 5, 12, 18" required>
            </div>

            <div class="form-group">
              <label>Food Type</label>
              <select name="food_type" class="form-control" required>
                <option value="VEG">VEG</option>
                <option value="NON-VEG">NON-VEG</option>
              </select>
            </div>

            <div class="form-group">
              <label>Product Image</label>
              <input type="file" name="image" class="form-control" accept="image/*">
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

  <!-- Edit Product Modal -->
  <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form method="POST" action="{{ route('manage.subcategory.category.update') }}" enctype="multipart/form-data">

        @csrf
        <input type="hidden" name="id" id="edit_id">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Product</h5>
            <button type="button" class="close" onclick="location.reload()" data-dismiss="modal" aria-label="Close">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Product Name</label>
              <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>

            <div class="form-group">
              <label>Product Price (₹)</label>
              <input type="number" step="0.01" name="price" id="edit_price" class="form-control" required>
            </div>

            <div class="form-group">
              <label>GST Rate (%)</label>
              <input type="number" step="0.01" name="gst_rate" id="edit_gst" class="form-control" required>
            </div>

            <div class="form-group">
              <label>Food Type</label>
              <select name="food_type" id="edit_food_type" class="form-control" required>
                <option value="VEG">VEG</option>
                <option value="NON-VEG">NON-VEG</option>
              </select>
            </div>

            <div class="form-group">
              <label>Product Image</label>
              <input type="file" name="image" class="form-control" accept="image/*">
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

  <!-- Add Bulk Upload Modal after the Edit Modal -->
<div class="modal fade" id="bulkUploadModal" tabindex="-1" aria-labelledby="bulkUploadModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('manage.subcategory.category.bulk.upload') }}" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="category_id" value="{{ @$id }}">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Bulk Upload Products</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="alert alert-info">
            <h6><i class="fa fa-info-circle"></i> Instructions:</h6>
            <ol class="mb-0 pl-3">
              <li>Download the template file first</li>
              <li>Fill in your product data</li>
              <li>Upload the completed Excel file</li>
              <li>File must be in .xlsx, .xls, or .csv format</li>
            </ol>
          </div>
          
          <div class="form-group">
            <label>Upload Excel/CSV File</label>
            <div class="custom-file">
              <input type="file" class="custom-file-input" id="bulk_file" name="bulk_file" accept=".xlsx,.xls,.csv" required>
              <label class="custom-file-label" for="bulk_file">Choose file</label>
            </div>
            <small class="form-text text-muted">
              Max file size: 2MB. Supported formats: .xlsx, .xls, .csv
            </small>
          </div>
          
          <div class="mt-3">
            <h6>Sample Format:</h6>
            <div class="table-responsive">
              <table class="table table-bordered table-sm">
                <thead class="thead-light">
                  <tr>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>GST Rate (%)</th>
                    <th>Food Type</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Paneer Butter Masala</td>
                    <td>250</td>
                    <td>18</td>
                    <td>VEG</td>
                  </tr>
                  <tr>
                    <td>Chicken Biryani</td>
                    <td>320</td>
                    <td>12</td>
                    <td>NON-VEG</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          
        </div>
        <div class="modal-footer">
          <a href="{{ route('manage.subcategory.category.template', $id) }}" class="btn btn-info mr-auto">
            <i class="fa fa-download"></i> Download Template
          </a>
          <button type="submit" class="btn btn-primary">
            <i class="fa fa-upload"></i> Upload
          </button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
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
      $('#productTable').DataTable();

      // Edit Button Click
      $('.edit-btn').on('click', function() {
        $('#edit_id').val($(this).data('id'));
        $('#edit_name').val($(this).data('name'));
        $('#edit_price').val($(this).data('price'));
        $('#edit_gst').val($(this).data('gst'));
        $('#edit_food_type').val($(this).data('type'));

        $('#editProductModal').modal('show');
      });
    });
  </script>


  <script>
$(document).ready(function() {
    $('#productTable').DataTable();
    
    // Edit Button Click
    $('.edit-btn').on('click', function() {
        $('#edit_id').val($(this).data('id'));
        $('#edit_name').val($(this).data('name'));
        $('#edit_price').val($(this).data('price'));
        $('#edit_gst').val($(this).data('gst'));
        $('#edit_food_type').val($(this).data('type'));
        $('#editProductModal').modal('show');
    });
    
    // Show file name in custom file input
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).siblings('.custom-file-label').addClass("selected").html(fileName);
    });
    
    // Clear file input when modal is hidden
    $('#bulkUploadModal').on('hidden.bs.modal', function() {
        $('.custom-file-input').val('');
        $('.custom-file-label').removeClass("selected").html('Choose file');
    });
});
</script>
</body>
</html>
