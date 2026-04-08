<!DOCTYPE html>
<html lang="en">
<head>
  <title>Admin - Manage Category | Card View</title>
  @include('includes.style')
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">

  <style>
    /* Card specific styles - preserves existing template structure */
    .category-card {
      background: #ffffff;
      border-radius: 16px;
      overflow: hidden;
      transition: all 0.3s ease;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      margin-bottom: 24px;
      border: 1px solid #e9ecef;
    }

    .category-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
    }

    .category-card-img {
      height: 200px;
      overflow: hidden;
      background: #f8f9fa;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
    }

    .category-card-img img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .category-card:hover .category-card-img img {
      transform: scale(1.05);
    }

    .category-card-img .no-image {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      font-size: 3rem;
    }

    .category-card-body {
      padding: 1.25rem;
    }

    .category-card-title {
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
      color: #2c3e50;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .category-card-slug {
      font-size: 0.75rem;
      color: #6c757d;
      margin-bottom: 1rem;
      font-family: monospace;
      background: #f8f9fa;
      padding: 4px 8px;
      border-radius: 6px;
      display: inline-block;
    }

    .category-card-actions {
      display: flex;
      gap: 8px;
      margin-top: 1rem;
      border-top: 1px solid #e9ecef;
      padding-top: 1rem;
    }

    .category-card-actions .btn {
      flex: 1;
      padding: 6px 12px;
      font-size: 0.8rem;
      border-radius: 8px;
    }

    /* Grid layout - exactly col-md-3 style */
    .category-grid {
      display: flex;
      flex-wrap: wrap;
      margin: 0 -12px;
    }

    .category-grid-item {
      flex: 0 0 25%;
      max-width: 25%;
      padding: 0 12px;
    }

    @media (max-width: 992px) {
      .category-grid-item {
        flex: 0 0 33.333%;
        max-width: 33.333%;
      }
    }

    @media (max-width: 768px) {
      .category-grid-item {
        flex: 0 0 50%;
        max-width: 50%;
      }
    }

    @media (max-width: 576px) {
      .category-grid-item {
        flex: 0 0 100%;
        max-width: 100%;
      }
    }

    /* Keep table hidden, show cards instead */
    .table-responsive table {
      display: none;
    }
  </style>
</head>

<body data-pc-theme="light">
  <div class="loader-bg">
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>

  @include('includes.sidebar')

  <div class="pc-container">
    <div class="pc-content">

      <!-- Breadcrumb -->
      {{-- <div class="page-header">
        <div class="page-block">
          <div class="row align-items-center">
            <div class="col-md-12">
              <div class="page-header-title">
                <h5 class="m-b-10">Manage Category</h5>
              </div>
              <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item" aria-current="page">Manage Category</li>
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
              @if(
                  isset($plan_details)
                  && isset($plan_details->category_number)
                  && count($data ?? []) < $plan_details->category_number
              )
              <a href="#" class="btn btn-primary" style="float: right;" data-toggle="modal" data-target="#addCategoryModal">
                <i class="fa fa-plus"></i> Add Category
              </a>
              @endif
            </div>

            <div class="card-body">
              <!-- Category Cards Grid - col-md-3 style -->
              <div class="category-grid">
                @forelse(@$data as $value)
                <div class="category-grid-item">
                  <div class="category-card">
                    <div class="category-card-img">
                      @if($value->image)
                        <img src="{{ URL::to('storage/app/public/category') }}/{{ @$value->image }}" alt="{{ @$value->name }}">
                      @else
                        <div class="no-image">
                          <i class="fa fa-image"></i>
                        </div>
                      @endif
                    </div>
                    <div class="category-card-body">
                      <h6 class="category-card-title" title="{{ @$value->name }}">
                        {{ Str::limit(@$value->name, 30) }}
                      </h6>
                      <div class="category-card-slug">
                        <i class="fa fa-link"></i> {{ @$value->slug }}
                      </div>
                      <div class="category-card-actions">
                        <!-- Subcategory -->
                        <a href="{{ route('manage.subcategory.category', @$value->id) }}" class="btn btn-dark btn-sm" title="View Subcategories">
                          <i class="fa fa-list"></i>
                        </a>

                        <!-- Edit -->
                        <button class="btn btn-success btn-sm edit-btn"
                                data-id="{{ $value->id }}"
                                data-name="{{ $value->name }}"
                                data-image="{{ $value->image }}">
                          <i class="fa fa-edit"></i>
                        </button>

                        <!-- Delete -->
                        <a href="{{ route('manage.category.delete', @$value->id) }}"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Are you sure want to delete this category?')">
                          <i class="fa fa-trash"></i>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                  <i class="fa fa-folder-open fa-4x text-muted mb-3"></i>
                  <p class="text-muted">No categories found. Click "Add Category" to create one.</p>
                </div>
                @endforelse
              </div>

              <!-- Hidden table for structure compatibility (not displayed) -->
              <div class="dt-responsive table-responsive" style="display: none;">
                <table id="categoryTable" class="table table-striped table-bordered nowrap">
                  <thead>
                    <tr>
                      <th>Category Name</th>
                      <th>Category Image</th>
                      <th>Slug</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach(@$data as $value)
                    <tr>
                      <td>{{ @$value->name }}</td>
                      <td>
                        @if($value->image)
                          <img src="{{ URL::to('storage/app/public/category') }}/{{ @$value->image }}" alt="Category" width="60" height="60">
                        @else
                          <span>No Image</span>
                        @endif
                      </td>
                      <td>{{ @$value->slug }}</td>
                      <td>
                        <a href="{{ route('manage.subcategory.category', @$value->id) }}" class="btn btn-dark btn-sm">Subcategory</a>
                        <button class="btn btn-success btn-sm edit-btn" data-id="{{ $value->id }}" data-name="{{ $value->name }}" data-image="{{ $value->image }}">Edit</button>
                        <a href="{{ route('manage.category.delete', @$value->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
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

  <!-- Add Category Modal -->
  <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form method="POST" action="{{ route('manage.category.insert') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addCategoryModalLabel">Add Category</h5>
            <button type="button" onclick="location.reload()" class="close" data-dismiss="modal" aria-label="Close">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Category Name</label>
              <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Category Image</label>
              <input type="file" name="image" class="form-control">
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

  <!-- Edit Category Modal -->
  <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form method="POST" action="{{ route('manage.category.update') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" id="edit_id">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Category</h5>
            <button type="button" class="close" onclick="location.reload()" data-dismiss="modal" aria-label="Close">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Category Name</label>
              <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>
            <div class="form-group">
              <label>Category Image</label>
              <input type="file" name="image" class="form-control">
              <img id="edit_image_preview" src="" class="mt-2" width="70" height="70" style="object-fit: cover; border-radius: 6px;">
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

  <!-- JS -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
  @include('includes.script')

  <script>
    $(document).ready(function() {
      // Edit Button functionality
      $('.edit-btn').on('click', function() {
        let id = $(this).data('id');
        let name = $(this).data('name');
        let image = $(this).data('image');

        $('#edit_id').val(id);
        $('#edit_name').val(name);
        if (image) {
          $('#edit_image_preview').attr('src', '{{ URL::to("storage/app/public/category") }}/' + image).show();
        } else {
          $('#edit_image_preview').hide();
        }
        $('#editCategoryModal').modal('show');
      });
    });
  </script>

</body>
</html>