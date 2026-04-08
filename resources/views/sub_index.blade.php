<!DOCTYPE html>
<html lang="en">
<head>
  <title>Admin - Manage Product | Card View</title>
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
    
    /* Product Card Styles - Modern Restaurant Style */
    .product-card {
        background: #ffffff;
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        margin-bottom: 24px;
        border: 1px solid rgba(0, 0, 0, 0.05);
        position: relative;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
    }
    
    /* Food Type Badge - Top Right Corner */
    .food-type-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        z-index: 10;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: bold;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    
    .food-type-badge.veg {
        background: #4caf50;
        color: white;
    }
    
    .food-type-badge.non-veg {
        background: #f44336;
        color: white;
    }
    
    .product-card-img {
        height: 200px;
        overflow: hidden;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    
    .product-card-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    
    .product-card:hover .product-card-img img {
        transform: scale(1.05);
    }
    
    .product-card-img .no-image {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-size: 2.5rem;
    }
    
    .product-card-img .no-image i {
        font-size: 3rem;
        margin-bottom: 8px;
    }
    
    .product-card-img .no-image span {
        font-size: 0.75rem;
        opacity: 0.9;
    }
    
    .product-card-body {
        padding: 1rem 1rem 1.25rem;
    }
    
    .product-card-title {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #2c3e50;
        line-height: 1.4;
        min-height: 25px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .product-price-section {
        margin: 12px 0;
    }
    
    .product-price {
        font-size: 1.4rem;
        font-weight: 700;
        color: #2c7a4d;
        display: inline-block;
    }
    
    .product-price small {
        font-size: 0.75rem;
        font-weight: 400;
        color: #6c757d;
    }
    
    .product-gst {
        font-size: 0.7rem;
        color: #6c757d;
        display: inline-block;
        margin-left: 8px;
    }
    
    .product-status {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    
    .product-status.active {
        background: #d4edda;
        color: #155724;
    }
    
    .product-status.inactive {
        background: #fff3cd;
        color: #856404;
    }
    
    .product-card-actions {
        display: flex;
        gap: 8px;
        margin-top: 1rem;
        padding-top: 0.75rem;
        border-top: 1px solid #e9ecef;
    }
    
    .product-card-actions .btn {
        flex: 1;
        padding: 6px 12px;
        font-size: 0.75rem;
        border-radius: 10px;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .product-card-actions .btn i {
        margin-right: 4px;
    }
    
    /* Grid Layout - Responsive Cards */
    .product-grid {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -12px;
    }
    
    .product-grid-item {
        flex: 0 0 25%;
        max-width: 25%;
        padding: 0 12px;
    }
    
    /* Status Button in Card Footer */
    .status-btn {
        font-size: 0.7rem;
        padding: 4px 10px;
        border-radius: 20px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    /* Hide the original table */
    .dt-responsive table {
        display: none;
    }
    
    /* Section Header */
    .section-header {
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e9ecef;
    }
    
    .section-header h6 {
        font-weight: 600;
        color: #495057;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #f8f9fa;
        border-radius: 20px;
    }
    
    .empty-state i {
        font-size: 4rem;
        color: #adb5bd;
        margin-bottom: 1rem;
    }
    
    .empty-state p {
        color: #6c757d;
        font-size: 1rem;
    }
    
    @media (max-width: 1200px) {
        .product-grid-item {
            flex: 0 0 33.333%;
            max-width: 33.333%;
        }
    }
    
    @media (max-width: 992px) {
        .product-grid-item {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }
    
    @media (max-width: 576px) {
        .product-grid-item {
            flex: 0 0 100%;
            max-width: 100%;
        }
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
{{--       <div class="page-header">
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
      </div> --}}
      <!-- Breadcrumb end -->

      <div class="row">
        <div class="col-sm-12">
          <div class="card">
            @include('includes.message')

            <div class="card-header">
              <div class="row">
                <div class="col-md-6">
                  <h5>Manage Product of {{ @$details->name }}</h5>
                </div>
                <div class="col-md-6 text-right">
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
              <!-- Product Cards Grid - Modern Restaurant Style -->
              <div class="product-grid">
                @forelse(@$data as $value)
                <div class="product-grid-item">
                  <div class="product-card">
                    <!-- Food Type Badge (Top Right) -->
                    <div class="food-type-badge {{ $value->food_type == 'VEG' ? 'veg' : 'non-veg' }}">
                      @if($value->food_type == 'VEG')
                        <i class="fa fa-leaf"></i>
                      @else
                        <i class="fa fa-utensils"></i>
                      @endif
                    </div>
                    
                    <!-- Product Image -->
                    <div class="product-card-img">
                      @if($value->image)
                        <img src="{{ URL::to('storage/app/public/category') }}/{{ @$value->image }}" alt="{{ @$value->name }}">
                      @else
                        <div class="no-image">
                          <i class="fa fa-hamburger"></i>
                          <span>No Image</span>
                        </div>
                      @endif
                    </div>
                    
                    <!-- Product Details -->
                    <div class="product-card-body">
                      <h6 class="product-card-title" title="{{ @$value->name }}">
                        {{ Str::limit(@$value->name, 35) }}
                      </h6>
                      
                      <div class="product-price-section">
                        <span class="product-price">
                          ₹{{ number_format($value->price, 2) }}
                        </span>
                        <span class="product-gst">
                          <i class="fa fa-percent"></i> GST {{ $value->gst_rate }}%
                        </span>
                      </div>
                      
                      <!-- Status Badge -->
                      <div class="mb-2">
                        <span class="product-status {{ $value->status == 'A' ? 'active' : 'inactive' }}">
                          <i class="fa {{ $value->status == 'A' ? 'fa-check-circle' : 'fa-clock-o' }}"></i>
                          {{ $value->status == 'A' ? 'Active' : 'Inactive' }}
                        </span>
                      </div>
                      
                      <!-- Action Buttons -->
                      <div class="product-card-actions">
                        <!-- Edit Button -->
                        <button class="btn btn-outline-success edit-btn"
                                data-id="{{ $value->id }}"
                                data-name="{{ $value->name }}"
                                data-price="{{ $value->price }}"
                                data-gst="{{ $value->gst_rate }}"
                                data-type="{{ $value->food_type }}">
                         <i class="fa fa-edit"></i>
                        </button>
                        
                        <!-- Status Toggle -->
                        <a href="{{ route('manage.subcategory.category.status', $value->id) }}"
                           onclick="return confirm('Are you sure you want to change the status?')"
                           class="btn btn-outline-secondary">
                          <i class="fa {{ $value->status == 'A' ? 'fa-ban' : 'fa-check' }}"></i>
                          
                        </a>
                        
                        <!-- Delete Button -->
                        <a href="{{ route('manage.subcategory.category.delete', $value->id) }}"
                           class="btn btn-outline-danger"
                           onclick="return confirm('Are you sure want to delete this product?')">
                          <i class="fa fa-trash"></i>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
                @empty
                <div class="col-12">
                  <div class="empty-state">
                    <i class="fa fa-utensils"></i>
                    <p>No products found. Click "Add Product" to create your first menu item.</p>
                  </div>
                </div>
                @endforelse
              </div>
              
              <!-- Hidden Table for DataTable Compatibility (kept for functionality but not displayed) -->
              <div class="dt-responsive table-responsive" style="display: none;">
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
                          <img src="{{ URL::to('storage/app/public/category') }}/{{ @$value->image }}" alt="Category" width="60" height="60">
                        @else
                          <span>No Image</span>
                        @endif
                      </td>
                      <td>{{ @$value->name }}</td>
                      <td>{{ number_format($value->price, 2) }}</td>
                      <td>{{ $value->gst_rate }}</td>
                      <td>{{ $value->food_type }}</td>
                      <td>{{ $value->status == 'A' ? 'Active' : 'Inactive' }}</td>
                      <td>
                        <button class="btn btn-success edit-btn" data-id="{{ $value->id }}" data-name="{{ $value->name }}" data-price="{{ $value->price }}" data-gst="{{ $value->gst_rate }}" data-type="{{ $value->food_type }}">Edit</button>
                        <a href="{{ route('manage.subcategory.category.delete', $value->id) }}" class="btn btn-danger">Delete</a>
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

  <!-- Bulk Upload Modal -->
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
      // Initialize DataTable (hidden but kept for functionality)
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